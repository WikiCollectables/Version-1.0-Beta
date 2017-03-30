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

class SuperDeleterAd  {
		var $superDeleterId;

	   public function getSuperDeleterUserId() {
			$db = mysql_connect("localhost","database_username","database_password");
			if (!$db) {
    			die('Could not connect: ' . mysql_error());
			}
			$sql = "select user_id from user where user_name = '".wcConfig::SUPER_DELETER_USERNAME ."'";
			mysql_select_db('database_name',$db) or die('Could not select database.');
			$resultSet  = mysql_query($sql);
			if (!$resultSet){
				die('Invalid query: ' . mysql_error());
			}
			$row = mysql_fetch_assoc($resultSet);
			$this->superDeleterId = $row ['user_id'];
			
		}

}
?>