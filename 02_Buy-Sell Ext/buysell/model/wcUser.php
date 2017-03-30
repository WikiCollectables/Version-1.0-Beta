<?php

require_once('extensions/buysell/model/DbRecord.php');

/**
 * Class wcUser
 *
 * The wcUser object represents an advertiser. Provides an OOP wrapper for the user table.
 *
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

class wcUser extends DbRecord {

    private $dbFields = array (
        'user_id' => '0',
        'rating' => '0'
    );
    private $errorMsg = '';
    
    
/**
  * __construct() - create wcUser object
  * reads record from database if "userID" is specified,
  * creates blank record otherwise
  * @param $userID integer (optional) primary key of the record to be read
  *
  */
    public function __construct($userID=-1) {
        parent::__construct(
            'user',
            array (
                'user_id'     => '0',
                'rating'     => '0',
            ),
            array ('user_id'),
            array ('user_id')
        );
        if($userID > 0)
            $this->readRecord($userID);
    }
    
/**
  * readRecord() - read a new record from the database
  * @param $newUserID integer (optional) primary key of the record to be read
  * @return boolean true if read operation was successful
  *
  */
    public function readRecord($newUserID=-1) {
        $retValue = false;
        if ($newUserID > 0) {
            $oldUserID = $this->user_id;
            $this->ad_id = $newUserID;
            if (!($retValue = parent::readRecord()))
              $this->user_id = $oldUserID;
        }
        else
            $retValue = parent::readRecord();
        return $retValue;
    }
    
}
?>