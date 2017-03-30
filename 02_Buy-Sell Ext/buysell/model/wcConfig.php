<?php

/* USE THIS VERSION FOR THE MAIN ACCOUNT
/*
class wcConfigExt {
	var $wikifolder;
	var $escrowfolder;
	function wcConfigExt() {
		global $_SERVER;
		if($_SERVER[HTTP_HOST] == "localhost") $this->wikifolder = "wiki19";
		else $this->wikifolder = "wiki";

		if($_SERVER[HTTP_HOST] == "localhost") $this->escrowfolder = "wikisafe.com";
		else $this->escrowfolder = "https://wikisafe.com";
	}
}
*/

/*USE THIS VERSION FOR THE DEVELOPMEN ACCOUNT*/
class wcConfigExt {
	var $wikifolder;
	var $escrowfolder;
	function wcConfigExt() {
		global $_SERVER;
		if($_SERVER[HTTP_HOST] == "localhost") $this->wikifolder = "wiki19";
		else $this->wikifolder = "wiki";

		if($_SERVER[HTTP_HOST] == "localhost") $this->escrowfolder = "escrowfolder.com";
		else $this->escrowfolder = "https://the-escrow-url.com";
	}
}

/**
 * Class wcConfig.php
 *
 * Contains all global configuration settings.
 *
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

class wcConfig {
			
	const DB_DRIVER =       'mysql';                 // database driver
	const DB_HOST =         'localhost';             // name of database server
	const DB_MEDIA_WIKI =   'database_name';   	// name of MediaWiki database
	const DB_WIKI_COINS =   'database_name';    	 // name of WikiCoins database
	const DB_USER =         'database_username';      // database user
	const DB_PASSWORD =     'database_password';      // database password

	const SUPER_DELETER_USERNAME = 'Test';

	const ADS_PER_PAGE =    20;                      // no. of ads displayed per page
	const ADS_DAYS_SHOWN =  30;                      // no. of days to display ads
	const IMAGE_MAX_SIZE =  307200;                  // max. file size of images
	const UPLOAD_TMP_DIR =  '/home/userdir/public_html/wikifolder.com/extensions/buysell/ad_images/temp';    // temp. directory for file uploads
	const IMAGE_UPLOAD_DIR= '/home/userdir/public_html/wikifolder.com/extensions/buysell/ad_images';
	const UPLOADED_BUTTON_URL = 'http://wikifolder.com/extensions/buysell/objects/UploadedBut.gif';
	const SOURCE_DIR = '/home/userdir/public_html/wikifolder.com/extensions/buysell/view';
	const IMAGE_URL =       'extensions/buysell/ad_images';                // URL to the above directory
	const THUMBNAIL_WIDTH = 115;                     // max. width of thumbnail images
	const THUMBNAIL_HEIGHT = 75;                     // max. height of thumbnail images

	/*
	const DB_DRIVER =       'mysql';                 // database driver
	const DB_HOST =         'localhost';             // name of database server
	const DB_MEDIA_WIKI =   'wikicomi_artdb';   // name of MediaWiki database
	const DB_WIKI_COINS =   'wikicomi_artdb';     // name of WikiCoins database
	const DB_USER =         'wikicomi_artuser';      // database user
	const DB_PASSWORD =     'yui789';              // database password
	
	const ADS_PER_PAGE =    20;                      // no. of ads displayed per page
	const ADS_DAYS_SHOWN =  30;                      // no. of days to display ads
	const IMAGE_MAX_SIZE =  307200;                  // max. file size of images
	const UPLOAD_TMP_DIR =  '/home/wikicomi/tmp';    // temp. directory for file uploads
	const IMAGE_UPLOAD_DIR= '/home/wikicomi/public_html/wikiartworks/images';
	const IMAGE_URL =       'images';                // URL to the above directory
	const THUMBNAIL_WIDTH = 115;                     // max. width of thumbnail images
	const THUMBNAIL_HEIGHT = 75;                     // max. height of thumbnail images
	*/


}

?>