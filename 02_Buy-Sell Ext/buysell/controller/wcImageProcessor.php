<?php
session_start();
require_once('extensions/buysell/model/wcConfig.php');

/**
 * Class wcImageProcessor
 *
 * The wcImageProcessor object provides functionality for uploading, processing, and 
 * displaying image files.
 *
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */




class wcImageProcessor {

/**
 * Internal error message
 *
 * @var string $errorMsg;
 */
    private $errorMsg = '';
	 private $page_id = '';
	 private $user = '';
	 private $ad_id = 0;
  //  $dbr =& wfGetDB( DB_SLAVE); 
/**
 * getErrorMsg() - return error message(s) from current operation.
 * Contains multiple lines if several errors occurred; empty if no error occurred.
 * @return string
 * 
 */
    public function getErrorMsg() {
        return $this->errorMsg;
    }

/**
 * processUploads() - process image uploads.
 * Validates image uploads one by one: (1) check upload errors, (2) verify upload integrity,
 * (3) validate file type, (4) check file max size, (5) move file to image upload directory
 * and rename it, (6) create a thumbnail image of each uploaded file.
 * @param $fileBaseName string Contains the base name for the target file. The uploaded files
 * are stored in the upload directory as "basename.n.ext" where n = number and ext = file
 * extension (e.g. .jpg, .gif, or .png). Thumbnail image files are named: "basename.n.th.ext"
 * @return integer Number of successfully uploaded image files
 */
    public function processUploads($fileBaseName) {
   global  $wgDBprefix;
	$dbr =& wfGetDB( DB_SLAVE); 
	$image[0] = $_POST['image1'];
	$image[1] = $_POST['image2'];
	$image[2] = $_POST['image3'];
	$image[3] = $_POST['image4'];
	$image[4] = $_POST['image5'];
  	$uploadFileCount = 1;
        //echo $image1;
        //echo $fileBaseName;
        $source_folder = wcConfig::UPLOAD_TMP_DIR;
	$dest_folder = wcConfig::IMAGE_UPLOAD_DIR;
	foreach($image as $image1) {
	if($image1 != '') {

	if(file_exists($source_folder."/".$image1.".gif")) {
		$filetype = ".gif";
		$file_size = filesize($source_folder."/".$image1.$filetype);
	}
	if(file_exists($source_folder."/".$image1.".jpg")) {
		$filetype = ".jpg";
		$file_size = filesize($source_folder."/".$image1.$filetype);
	}
	if(file_exists($source_folder."/".$image1.".jpeg")) {
		$filetype = ".jpeg";
		$file_size = filesize($source_folder."/".$image1.$filetype);
	}
	if(file_exists($source_folder."/".$image1.".png")) {
		$filetype = ".png";
		$file_size = filesize($source_folder."/".$image1.$filetype);
	}
	if(copy($source_folder."/".$image1.$filetype, $dest_folder."/".$fileBaseName."-".$uploadFileCount.$filetype)) {
        	        unlink($source_folder."/".$image1.$filetype);
        }
	$fileName = $dest_folder ."/". $fileBaseName . '-' . $uploadFileCount;


	$sql_title = "select page_title from ".$wgDBprefix."page where page_id = ". $this->page_id;
	$res = $dbr->query($sql_title);
	$response_row= $dbr->fetchObject($res  ) ;
	$page_title =  $response_row->page_title;

	$insert_filename = $fileBaseName . '-' . ( $uploadFileCount  ) . $filetype  ;
	$query =  "insert into ".$wgDBprefix."adimage(ad_id, img_name,img_user,	img_user_text , page_id,img_size, img_timestamp ) values('".$this->ad_id."', '$insert_filename', '".$this->user."','".$page_title."','".$this->page_id."',$file_size,NOW() + 0)";
//	$res = $dbr->query( "select * from art_page" );
   $dbr->query($query);
	$uploadFileCount++;
	$this->createThumbnail($fileName . $filetype,$fileName . '-tn' . '.jpg',                       wcConfig::THUMBNAIL_WIDTH, wcConfig::THUMBNAIL_HEIGHT);
	}
	}	



}

/**
 * createThumbnail() - create a thumbnail from an image file.
 * Creates a thumbnail image in JPEG format for a JPEG, GIF, or PNG input file that fits
 * into the given dimensions.
 * @param 
 * @param $srcFileName string Path to source image file to be thumb-nailed.
 * @param $targetFileName string Path and file name of the thumbnail image.
 * @param $maxWidth integer Maximum width of the thumbnail image
 * @param $maxHeight integer Maximum height of the thumbnail image
 * @return boolean true if image is created successfully
 */
    public function createThumbnail($srcFileName, $targetFileName, $maxWidth, $maxHeight) {
        $fileType = strtolower(strrchr($srcFileName, '.'));
        $img = false;
        $retValue = false;
        // create image resource
        if ($fileType == '.jpg' || $fileType == '.jpeg')
            $img = @imagecreatefromjpeg($srcFileName);
        elseif ($fileType == '.gif')
            $img = @imagecreatefromgif($srcFileName);
        elseif ($fileType == '.png')
            $img = @imagecreatefrompng($srcFileName);
        if(!$img) 
            return $retValue;
        // calculate aspect ration
        $originalWidth = imagesx($img);
        $originalHeight = imagesy($img);
        if ($originalWidth > $originalHeight) {
            $thumbWidth = $maxWidth;
            $thumbHeight = $originalHeight * $maxWidth / $originalWidth;
            if ($thumbHeight > $maxHeight) {
                $thumbWidth = $originalWidth * $maxHeight / $originalHeight;
                $thumbHeight = $maxHeight;
            }
        } 
        else {
            $thumbWidth = $originalWidth * $maxHeight / $originalHeight;
            $thumbHeight = $maxHeight;
            if ($thumbWidth > $maxWidth) {
                $thumbWidth = $maxWidth;
                $thumbHeight = $originalHeight * $maxWidth / $originalWidth;
            }
        }
        // resample image, save file, release resources
        $thumbnail = @imagecreatetruecolor($thumbWidth, $thumbHeight);
        if ($thumbnail) {
            if (@imagecopyresampled($thumbnail, $img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, 
              $originalWidth, $originalHeight))
                $retValue = @imagejpeg($thumbnail, $targetFileName);
            @imagedestroy($thumbnail);
        }
        @imagedestroy($img);
        return $retValue;
    }
    
/**
 * deleteImages() - delete all images that belong to a particular ad.
 * Removes all images and thumbnail images belonging to the ad identified by $adID
 * from the server.
 * @param $adID integer record ID of the ad to be deleted.
 * @return boolean true if all images are successfully deleted.
 */
    function deleteImages($adID) {
		  global  $wgDBprefix;
	     $dbr =& wfGetDB( DB_SLAVE); 
        $retValue = true;
        $fileExtensions = array('.jpg', '.gif', '.png');
        $imageBaseName = wcConfig::IMAGE_UPLOAD_DIR . '/' . sprintf('%08d', $adID) . '-';
        for ($i = 1; $i <=5; $i++) {
            $imageFileName = $imageBaseName . $i;
            foreach ($fileExtensions as $ext) {
                if (file_exists($imageFileName . $ext)) { 
						  $temp = wcConfig::IMAGE_UPLOAD_DIR.'/';
						  $temp = str_replace ($temp , '', $imageFileName . $ext); 
						  $sql = "delete from ".$wgDBprefix."adimage where img_name = '$temp'";
						  $dbr->query( $sql ) ;
                    if(!@unlink($imageFileName . $ext))
                        $retValue = false;
					  }
            }
            $thumbnailFileName = $imageBaseName . $i . '-tn.jpg';
            if (file_exists($thumbnailFileName)) {
					 	  $temp = wcConfig::IMAGE_UPLOAD_DIR.'/';
						  $temp = str_replace ($temp , '', $thumbnailFileName); 
						  $sql = "delete from ".$wgDBprefix."adimage where img_name = '$temp'";
						  $dbr->query( $sql ) ;
                if (!@unlink($thumbnailFileName))
                    $retValue = false;
				}
        }
        return $retValue;
    }


	// added AdId for SpecialGalleryofNewAdFiles.php to get new and open (non deleted) ads
	public function setAdId($ad_id){

		$this->ad_id = $ad_id;

	 }
	

	 public function setUser($user){

		$this->user = $user;

	 }
	
	 public function setPageId($page_id){

		$this->page_id = $page_id;

	 }
	  

	
}
?>