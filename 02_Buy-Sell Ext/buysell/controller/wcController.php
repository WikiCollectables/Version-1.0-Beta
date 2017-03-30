<?php

include_once('extensions/buysell/model/wcAd.php');
include_once('extensions/buysell/controller/wcImageProcessor.php');

/**
 * Class wcController
 *
 * Provides controller logic for buy/sell page.
 * 
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

class wcController {
	var $my_wikiPageID;
	var $my_wikiUserID;
	public function wcController($my_wikiPageID, $my_wikiUserID)
	{
		$this->my_wikiPageID = $my_wikiPageID;
		$this->my_wikiUserID = $my_wikiUserID;
	}
/**
  * getAction() - get current user action, e.g. delete, add, etc. from REQUEST data array.
  * If no action was chosen by the user, an empty string is returned.
  * @return string user action
  *
  */
    public function getAction() {
        if (isset($_REQUEST['buysellaction']))
            $buysellaction = trim($_REQUEST['buysellaction']);
        elseif (isset($_REQUEST['Action']))
            $buysellaction = trim($_REQUEST['Action']);
        else
            $buysellaction = '';
        return $buysellaction;
    }
    
/**
  * getCurrentPageNo() - get current ad page number from session data array
  * @return integer page number, default is 1
  *
  */
    public function getCurrentPageNo() {
        if (isset($_SESSION['currentPageNo']))
          $currentPageNo = $_SESSION['currentPageNo'];
        else
          $currentPageNo = 1;
        return $currentPageNo;
    }
    
/**
  * getNoOfPages() - get number of internal ad pages
  * @return integer number of pages, default is 1
  *
  */
    public function getNoOfPages() {
        if (isset($_SESSION['noOfPages']))
            $noOfPages = $_SESSION['noOfPages'];
        else
            $noOfPages = 1;
        return $noOfPages;
    }
    
/**
  * getWikiUSerID() - get wiki user ID of the user who browses the wiki pages
  * @return integer  wiki user ID or null if there is no user
  *
  */
    public function getWikiUserID() {
        /*if (isset($_SESSION['wikiUserID']))
            $wikiUserID = $_SESSION['wikiUserID'];*/
        if (isset($this->my_wikiUserID))
            $wikiUserID = $this->my_wikiUserID;
        else
            $wikiUserID = null;
        return $wikiUserID;
    }
    
/**
  * getWikiPageID() - returns page ID of the wiki page currently viewed
  * @return integer - wiki page ID or null if the wiki page is undefined
  *
  */
    public function getWikiPageID() {
		/*if (isset($_SESSION['wikiPageID']))
            $wikiPageID = $_SESSION['wikiPageID'];*/
        if (isset($this->my_wikiPageID))
            $wikiPageID = $this->my_wikiPageID;
        else 
            $wikiPageID = null;
        return $wikiPageID;
    }
    
/**
  * getPreviousWikiPageID() - returns page ID of the wiki page previously viewed
  * @return integer - wiki page ID or -1 if there was no previous page
  *
  */
    public function getPreviousWikiPageID() {
        if (isset($_SESSION['previousWikiPageID']))
            $previousWikiPageID = $_SESSION['previousWikiPageID'];
        else 
            $previousWikiPageID = -1;
        return $previousWikiPageID;
    }
    
/**
  * toNumber() - convert a mixed string such as "$33.50" or "EUR 4.-" to a numeric string
  * such as "33.00", or "4"
  * @param $strInput (string) - input string containing numeric and non-numeric characters
  * @return string - numeric string
  *
  */
    private function toNumber($strInput) {
        $strOutput = '';
        $isFraction = false;
        for ($i = 0; $i < strlen($strInput); $i++) {
            switch ($strInput[$i]) {
                case '-' :
                    if ($i == 0)
                        $strOutput .= '-';
                break;
                case '.':
                    if (!$isFraction) {
                        $strOutput .= '.';
                        $isFraction = true;
                    }
                break;
                case '0':
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                    $strOutput .= $strInput[$i];
                break;
            }
        }
        return $strOutput;
    }
    
/**
  * postNewAd() - execute a "post ad" action.
  * 
  * Reads and filters user input from _REQUEST variables, creates a new ad record, inserts
  * filtered data into database and processes uploaded images.
  * @param $adType (string) must be either 'S' or 'B'
  * @return void
  *
  */
    private function postNewAd($adType) {
        if (is_null($this->getWikiUserID()) || is_null($this->getWikiPageID()))
          return;
        $ad = new wcAd();
        if ($adType == 'B') {
            $ad->ad_text = trim($_REQUEST['buyerAdText']);
            $ad->ad_amount = $this->toNumber($_REQUEST['buyerAmount']);
        }
        elseif ($adType == 'S') {
            $ad->ad_text = trim($_REQUEST['sellerAdText']);
            $ad->ad_amount = $this->toNumber($_REQUEST['sellerAmount']);
            $ad->weight_lbs = $this->toNumber($_REQUEST['weightLbs']);
            $ad->weight_ozs = $this->toNumber($_REQUEST['weightOzs']);
        }
        else
          return;
        $ad->user_id = $this->getWikiUserID();
        $ad->page_id = $this->getwikiPageID();
        $ad->ad_type = $adType;
        $ad->post_date = date('Y-m-d H:i:s');
        if ($ad->createRecord()) {
            $imageProcessor = new wcImageProcessor();
            // added AdId for SpecialGalleryofNewAdFiles.php to get new and open (non deleted) ads
			$imageProcessor->setAdId( $ad->getAdId() );
            $imageProcessor->setUser( $this->getWikiUserID() );
			$imageProcessor->setPageId($this->getwikiPageID() );
            $imageProcessor->processUploads($ad->getImageBaseName());
        }
    }
    
/**
  * deleteAd() - execute a delete action and remove an ad from database
  * @return void
  *
  */
    private function deleteAd() {
        if (($adID = $this->toNumber($_REQUEST['adID'])) > 0) {
            $ad = new wcAd();
            if ($ad->deleteRecord($adID)) {
                $imageProcessor = new wcImageProcessor();
                $imageProcessor->deleteImages($adID);
            }
        }
    }
    
/**
  * readRequest() - read and filter user data from GET/POST/COOKIE/SESSION data buffers.
  * This method stores status data input from the user in the session data array.
  * @return void
  *
  */
    private function readRequest() {
        // new wiki user id set? --> store in session data array
        if (isset($_POST['wikiUserID']))
          $_SESSION['wikiUserID'] = (int) $_POST['wikiUserID'];
        // new wiki page id set? --> store in session data array
        if (isset($_POST['wikiPageID'])) 
          $_SESSION['wikiPageID'] = (int) $_POST['wikiPageID'];
        // new page selected --> store in session data array
        if (isset($_GET['gotoPage'])) {
            $page = (int) $_GET['gotoPage'];
            if ($page >= 1 && $page <= $this->getNoOfPages())
                $_SESSION['currentPageNo'] = $page;
        }
    }
    
/**
  * paginate() - recalculate no. of pages, reset current page to 1.
  * Performs internal pagination and stores values in session data array.
  * @param $forcePaginate boolean (optional) If true, internal pagination is 
  * recalculated, even if the wiki page id did not change. This is necessary in
  * case ads were added or deleted.
  * @return void
  *
  */
    private function paginate($forcePaginate=false) {
        if ($forcePaginate || ($this->getWikiPageID() !== $this->getPreviousWikiPageID())) {
            $ad = new wcAd();
            $noOfAds = $ad->getNoOfAds($this->getWikiPageID());
            if ($noOfAds > wcConfig::ADS_PER_PAGE) {
                $noOfPages = (int) ($noOfAds / wcConfig::ADS_PER_PAGE);
                $noOfPages += ($noOfAds % wcConfig::ADS_PER_PAGE) > 0? 1 : 0;
            }
            else $noOfPages = 1;
            $_SESSION['noOfPages'] = $noOfPages;
            if ($forcePaginate) {
                if ($_SESSION['currentPageNo'] > $noOfPages)
                $_SESSION['currentPageNo'] = $noOfPages;
            }
            else
                $_SESSION['currentPageNo'] = 1;
        }
    }
    
/**
  * showIllegalRequest() - shows a HTTP BAD REQUEST Error 400 page if a manipulated 
  * URL is encountered
  * @return void
  *
  */
    private function showIllegalRequest() {
        header("HTTP/1.1 400 Bad Request");
        exit(1);
    }
    
/**
  * run() - central dispatcher that interprets and executes user actions
  * @return void
  *
  */
    public function run() {
        //session_start();
        ini_set('upload_tmp_dir', wcConfig::UPLOAD_TMP_DIR);
        $forcePaginate = false;
        $this->readRequest();
        $buysellaction = $this->getAction();
        
		if (!empty($buysellaction)) {
            switch($buysellaction) {
                case 'postBuyerAd':
                    $this->postNewAd('B');
                    $forcePaginate = true;
                break;
                case 'postSellerAd':
                    $this->postNewAd('S');
                    $forcePaginate = true;
                break;
                case 'deleteAd':
                    $this->deleteAd();
                    $forcePaginate = true;
                break;
                default:
                    //$this->showIllegalRequest();
                break;
            }
        }
        $this->paginate($forcePaginate);
    }
    
/**
  * render() - renders the response page.
  * This method must be invoked after run() in order to display the page properly.
  * @return void
  *
  */
    public function render() {
        $buysell_content = "";
		include('extensions/buysell/view/wcBuySellPage.php');
        $_SESSION['previousWikiPageID'] = $_SESSION['wikiPageID'];
		return $buysell_content;
  }
}

?>