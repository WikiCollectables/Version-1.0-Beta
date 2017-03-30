<?php

require_once('extensions/buysell/model/DbRecord.php');
require_once('extensions/buysell/model/wcConfig.php');
require_once('extensions/buysell/model/Db.php');

/**
 * Class wcAd
 *
 * The wcAd object represents a buy/sell ad. Provides an OOP wrapper for the Ads table.
 *
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

class wcAd extends DbRecord {

    private $resultSet = null;             // stores ads query result set
/**
  * __construct() - create wcAd object
  * reads record from database if "adID" is specified, creates blank record otherwise
  * user_rating and user_name must be pulled from related tables
  * @param $adID integer (optional) primary key of the record to be read
  *
  */
    public function __construct($adID=null) {
		global $wgDBprefix;

        parent::__construct(
            $wgDBprefix.'ads',
            array (
                'ad_id'       => '0',
                'user_id'     => '0',
                'page_id'     => '0',
                'ad_text'     => '',
                'ad_type'     => '',
                'ad_amount'   => '0',
                'weight_lbs'  => 'null',
                'weight_ozs'  => 'null',
                'post_date'   => '',
                'user_rating' => '0',
                'user_name'   => '',
                'page_title'     => ''
            ),
            array ('ad_id'),
            array ('ad_id', 'user_rating', 'user_name')
        );
        if (!is_null($adID)) {
            $this->readRecord($adID);
        }
    }
    
/**
  * readRecord() - read an ad from the database
  * @param $newAdID integer (optional) primary key of the record to be read
  * existing record is re-read if $newAdID is omitted
  * @return boolean true if read operation was successful
  *
  */
    public function readRecord($newAdID=null) {
        $retValue = false;
        if (!is_null($newAdID)) {
            $oldAdID = $this->ad_id;
            $this->ad_id = $newAdID;
            if (!($retValue = parent::readRecord()))
              $this->ad_id = $oldAdID;
        }
        else
            $retValue = parent::readRecord();
        return $retValue;
    }

/**
 * createRecord() - insert a new ad record into the database.
 * Stores value of primary index (AUTO_INCREMENT) into ad_id data field on success.
 * @return boolean true if create was successful
 * 
 */
    public function createRecord() {
        if (($retValue = parent::createRecord()) !== false)
            $this->ad_id = $this->getLastInsertID();
        return $retValue;
    }
    
/**
  * deleteRecord() - delete an ad
  * @param $adID integer (optional) primary key of the record to be deleted
  * existing record is deleted if $adID is omitted
  * @return boolean true if delete was successful
  *
  */
    public function deleteRecord($adID=null) {
        $retValue = false;
        if (!is_null($adID)) {
            $oldAdID = $this->ad_id;
            $this->ad_id = $adID;
            if (!($retValue = parent::deleteRecord()))
              $this->ad_id = $oldAdID;
        }
        else
            $retValue = parent::deleteRecord();
        return $retValue;
    }

/**
 * getImageBaseName() - get base name for image files associated with ad record.
 * Returns the ad id as eight-digit numeric string.
 * @return string image file base name
 * 
 */
    public function getImageBaseName() {
        return sprintf('%08d', $this->ad_id);
    }
    
/**
  * retrieveAds() - retrieves ads for a certain page and stores query result in 
  * internal result set for the application to fetch
  * @param $pageID integer identifies wiki page to which ads belong
  * @param $adType string 'B' = buyer ad, 'S' = seller ad
  * @param $pageNo integer number of current ad page
  * @param $pages integer total number of ad pages
  * @return boolean true if read operation was successful
  *
  */
    public function retrieveAds($pageID, $userID, $adType, $pageNo, $pages) {
		global $wgDBprefix;
        $wikiDb = wcConfig::DB_MEDIA_WIKI;
        $wikiCoinsDb = wcConfig::DB_WIKI_COINS;
        $cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));
        /*$sql = "SELECT {$wikiCoinsDb}.ads.*, {$wikiCoinsDb}.user.user_rating, {$wikiDb}.user.user_name " .
          "FROM {$wikiCoinsDb}.ads LEFT JOIN {$wikiDb}.user ON {$wikiCoinsDb}.ads.user_id = {$wikiDb}.user.user_id " .
          "LEFT JOIN {$wikiCoinsDb}.user ON {$wikiCoinsDb}.ads.user_id = {$wikiCoinsDb}.user.user_id " .
          "WHERE {$wikiCoinsDb}.ads.page_id=" . Db::escape($pageID)  .
          " AND {$wikiCoinsDb}.ads.ad_type=" . Db::escape($adType)  .
          " AND {$wikiCoinsDb}.ads.post_date >='$cutOffDate'" .
          " ORDER BY {$wikiCoinsDb}.ads.post_date DESC";*/

		// check if buysell page is called from article section or user section
		if( trim($_REQUEST["page_id"]) != "" && trim($_REQUEST["user_id"]) == "")
		{
			$sql = "SELECT {$wikiDb}." . $wgDBprefix . "ads.*, {$wikiDb}.user.user_name, '' page_title " .
			  " FROM {$wikiDb}." . $wgDBprefix . "ads " .
			  " LEFT JOIN {$wikiDb}.user ON {$wikiDb}." . $wgDBprefix . "ads.user_id = {$wikiDb}.user.user_id " .
			  " WHERE {$wikiDb}." . $wgDBprefix . "ads.page_id=" . Db::escape($pageID)  .
			  " AND {$wikiDb}." . $wgDBprefix . "ads.ad_type=" . Db::escape($adType)  .
			  //" AND {$wikiDb}." . $wgDBprefix . "ads.post_date >='$cutOffDate'" .
			  " ORDER BY {$wikiDb}." . $wgDBprefix . "ads.post_date DESC";
		}
		else if( trim($_REQUEST["page_id"]) == "" && trim($_REQUEST["user_id"]) != "")
		{
			$sql = "SELECT {$wikiDb}." . $wgDBprefix . "ads.*, 0 as user_id, {$wikiDb}.user.user_name, {$wikiDb}.".$wgDBprefix."page.page_title " .
			  " FROM {$wikiDb}." . $wgDBprefix . "ads " .
			  " LEFT JOIN {$wikiDb}.user ON {$wikiDb}." . $wgDBprefix . "ads.user_id = {$wikiDb}.user.user_id " .
			  " LEFT JOIN {$wikiDb}.".$wgDBprefix."page ON {$wikiDb}." . $wgDBprefix . "ads.page_id = {$wikiDb}.".$wgDBprefix."page.page_id " .
			  " WHERE {$wikiDb}." . $wgDBprefix . "ads.user_id=" . Db::escape($userID)  .
			  " AND {$wikiDb}." . $wgDBprefix . "ads.ad_type=" . Db::escape($adType)  .
			  //" AND {$wikiDb}." . $wgDBprefix . "ads.post_date >='$cutOffDate'" .
			  " ORDER BY {$wikiDb}." . $wgDBprefix . "ads.post_date DESC";
		}
        if ($pages > 1) {
            $limitOffset = ($pageNo - 1) * wcConfig::ADS_PER_PAGE;
            $limitRows = wcConfig::ADS_PER_PAGE;
            $sql .= " LIMIT $limitOffset, $limitRows";
        }
		//echo $sql;
        $db = Db::getInstance();
        if (!($this->resultSet = mysql_query($sql, $db)))
            $this->resultSet = null;
        return (!is_null($this->resultSet));
    }
    
/**
  * nextRecord() - retrieves a single ad record from the query result set previously
  * created by retrieveAds() and populates member fields with the record values
  * @return boolean true if new record was fetched, false if there are no more records
  *
  */
    public function nextRecord() {
        $retValue = false;
        if (!is_null($this->resultSet)) {
            $row = mysql_fetch_assoc($this->resultSet);
            if ($row) {
                $retValue = true;
                $this->clear();
                $this->populate($row);
            }
        }
        return $retValue;
    }
    
/**
  * getNoOfAds() - return the number of existing ads for a given page.
  * Since there are different types of ads displayed in columns, getNoOfAds() returns 
  * the number of ads in the longest column, i.e. the number of ads of the ad type 
  * with most records.
  * @param $pageID integer identifies wiki page
  * @return integer number of ads
  *
  */
    public function getNoOfAds($pageID) {
		global $wgDBprefix;
        $retValue = 0;
        $cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));
        $sql = 'SELECT COUNT(ad_type) FROM ' . $wgDBprefix . 'ads' .
          " WHERE page_id = " . Db::escape($pageID) .
          " /*AND post_date >= '$cutOffDate'*/" .
          " GROUP BY ad_type";
        $db = Db::getInstance();
        if (($resultSet = mysql_query($sql, $db))) {
            while (($row = mysql_fetch_row($resultSet)))
                if ($row[0] > $retValue)
                    $retValue = $row[0];
        }
        return $retValue;
    }

	// written for # bug # 12 - A user cannot "Make a WikiSafe Offer" twice on the same ad.
	public function chkInitiate($ad_id, $seller_id, $buyer_id)
	{
		global $wgDBprefix;
        $retValue = 0;
		$sql = "select count(id) from escrow_escrow 
					where ad_id='" . $ad_id . "' 
							and escrow_prefix = '$wgDBprefix' 
							and seller_id = '$seller_id' 
							and buyer_id = '$buyer_id' ";
        $db = Db::getInstance();
        if (($resultSet = mysql_query($sql, $db))) {
			while (($row = mysql_fetch_row($resultSet)))
                if ($row[0] > $retValue)
                    $retValue = $row[0];
        }
        return $retValue;
	}

	// written for # bug # 17 - The User Rating entered by the users upon completion of an escrow at pages status07.php and status11.php
	public function getRating($user_id)
	{
		//global $wgDBprefix;
		// all wiki rating of this user

        $db = Db::getInstance();

		$sql = "SELECT buyer_rating
					FROM escrow_escrow
					WHERE buyer_id =  '$user_id'
					AND buyer_rating IS NOT NULL and buyer_rating != 0 ";
        if (($resultSet = mysql_query($sql, $db))) {
			$buyer_num = mysql_num_rows($resultSet);
			$b_rating = 0;
			while (($row = mysql_fetch_row($resultSet)))
                if ($row[0] > 0)
                    $b_rating += $row[0];
        }

		$sql = "SELECT seller_rating
					FROM escrow_escrow
					WHERE seller_id =  '$user_id'
					AND seller_rating IS NOT NULL and seller_rating != 0 ";
        if (($resultSet = mysql_query($sql, $db))) {
			$seller_num = mysql_num_rows($resultSet);
			$s_rating = 0;
			while (($row = mysql_fetch_row($resultSet)))
                if ($row[0] > 0)
                    $s_rating += $row[0];
        }

		if(($buyer_num + $seller_num) != 0)
			$avr_rating = ($b_rating + $s_rating)/($buyer_num + $seller_num);
		else
			$avr_rating = 0;
        return $avr_rating;
	}

	// written for # bug # 38 - Once a user selects "Accept Offer" on status02b.php and status02s.php, the ad should be removed.
	public function chkNotAcceptOffer($ad_id)
	{
		global $wgDBprefix;
		$sql = "select count(id)
					FROM escrow_escrow 
					WHERE escrow_prefix = '$wgDBprefix'
						AND ad_id = '$ad_id'
						AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') ";
        $db = Db::getInstance();
        if (($resultSet = mysql_query($sql, $db)))
		{
			$row = mysql_fetch_row($resultSet);
			if ($row[0] > 0)
				return false;
			else
				return true;
        }
        return true;
	}

	// added AdId for SpecialGalleryofNewAdFiles.php to get new and open (non deleted) ads
	public function getAdId()
	{
		return $this->ad_id;
	}
}
?>