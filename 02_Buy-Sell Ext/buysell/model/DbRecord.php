<?php

require_once('extensions/buysell/model/Db.php');

/**
 * Class DbRecord
 *
 * Represents a single database table/view/query record.
 * 
 * DbRecord is meant to be extended by concrete domain objects which are based on
 * database records. It contains an associative array dbFields which represents the
 * record values and field names. From this information, it can construct standard 
 * read/write/create/delete SQL queries, and provide these standard operations to 
 * the implementing class, which may be override them if necessary. DbRecord exposes
 * data fields through PHP's overloading mechanism, where fields appear as properties 
 * accessed by column name.
 *
 * All fields types are internally represented as strings.
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

abstract class DbRecord {

/**
 * Member variables
 *
 */
    protected $tblName = '';              // name of the table or view
    protected $dbFields = array();        // associative array: fieldName => value
    protected $dbPrimary = array();       // list of field names that form the primary index
    protected $dbAuto = array();          // list of automatic fields (no update/insert)
    private   $dbDefault = array();       // default values for all fields
    private   $errorMsg = '';             // contains last DB error message
    private   $lastInsertID = 0;          // stores value of AUTO_INCREMENT field for last INSERT
    
    
/**
  * __construct() - create dbRecord object
  * set data fields and primary indexes
  * @param $tblName string name of the table or view
  * @param $dbFields string[] array with table's column names
  * @param $dbIndex string[] array with table's primary index column names
  *
  */
    public function __construct($tblName, $dbFields, $dbPrimary, $dbAuto) {
        $this->tblName = $tblName;
        $this->dbFields = $dbFields;
        $this->dbDefault = $dbFields;
        $this->dbPrimary = $dbPrimary;
        $this->dbAuto = $dbAuto;
    }
    
/**
  * getErrorMsg() - returns last error message
  * @return string error message
  *
  */
    public function getErrorMsg() {
        return $this->errorMsg;
    }
    
/**
  * __get() - returns the value of a single column by means of automatic member accecss
  * @param $fieldName string column name of the field to be retrieved
  * @return string the data field's value
  * @throws Exception if $fieldName is not valid
  *
  */
    public function __get($fieldName) {
        if (array_key_exists($fieldName, $this->dbFields))
            return $this->dbFields[$fieldName];
        else
            throw new Exception("$fieldName is not a recognised field.");
    }

/**
  * __set() - sets the value of a single column by means of automatic member accecss
  * @param $fieldName string column name of the field to be set
  * @param $vaue string value of the data field
  * @return void
  * @throws Exception if $fieldName is not valid
  *
  */
    public function __set($fieldName, $value) {
        if (array_key_exists($fieldName, $this->dbFields))
            $this->dbFields[$fieldName] = $value;
        else
            throw new Exception("$fieldName is not a recognised field.");
    }

/**
  * __isset() - check whether $fieldName member exists
  * @param $fieldName string column name of the field to be checked
  * @return boolean true if $fieldName exists
  *
  */
    private function __isset($fieldName) {
        return array_key_exists($fieldName, $this->dbFields);
    }

/**
  * __unset() - unset a member field
  * always throws an exception on attempt to unset member
  * @param $fieldName string column name of the field to beunset
  * @return void
  * @throws Exception if $fieldName is not valid
  *
  */
    private function __unset($fieldName) {
        throw new Exception("$fieldName cannot be unset.");
    }
    
/**
  * getLastInsertID() - returns last insert ID.
  * The last insert ID is the value of the AUTO_INCREMENT field or primary index field of the
  * last record inserted with the createRecord() method. The value is 0 if no record was inserted.
  * @return integer last insert ID
  *
  */
    public function getLastInsertID() {
        return $this->lastInsertID;
    }
    
/**
  * readRecord() retrieve values of data field members from DB table
  * @return boolean true if read is successful
  *
  */
    public function readRecord() {
        $retValue = false;
        $db = Db::getInstance();
        $sql = 'SELECT * FROM ' . $this->tblName . ' ';
        $sql .= $this->genWhereClause();
        if (($resultSet = mysql_query($sql, $db))) {
            if (($row = mysql_fetch_assoc($resultSet))) {
                foreach($row as $fieldName => $value) {
                    if (array_key_exists($fieldName, $this->dbFields))
                        $this->dbFields[$fieldName] = $value;
                }
            }
            else {
                $retValue = false;
                $this->errorMsg = 'No such record found.';
            }
            mysql_free_result($resultSet);
        }
        else {
            $retValue = false;
            $this->errorMsg = mysql_error($db);
        }
        return $retValue;
    }
    
/**
  * writeRecord() - perform SQL update, save data field members
  * @return boolean true if update was successful
  *
  */
    public function writeRecord() {
        $db = Db::getInstance();
        $sql = $this->genUpdateSQL() . ' ' . $this->genWhereClause();
        if (!($retValue = mysql_query($sql, $db)))
            $this->errorMsg = mysql_error($db);
        return $retValue;
    }
    
/**
  * createRecord() - perform SQL insert, append record to DB table
  * @return boolean true if insert was successful
  *
  */
    public function createRecord() {
        $db = Db::getInstance();
        $sql = $this->genInsertSQL();
        if (!($retValue = mysql_query($sql, $db)))
            $this->errorMsg = mysql_error($db);
        else
            $this->lastInsertID = mysql_insert_id($db);
        return $retValue;
    }
    
/**
  * deleteRecord() - perform SQL delete, remove record from DB table
  * @return boolean true if delete was successful
  *
  */
    public function deleteRecord() {
        $db = Db::getInstance();
        $sql = 'DELETE FROM ' . $this->tblName . ' ' . $this->genWhereClause();
        if (!($retValue = mysql_query($sql, $db)))
            $this->errorMsg = mysql_error($db);
        return $retValue;
    }
    
/**
  * clear() - clear buffer, i.e. clear the values of the data fields
  * @return void
  *
  */
    public function clear() {
        $this->dbFields = $this->dbDefault;
        $this->errorMsg = '';
    }
    
/**
  * populate() - populate values of data fields with those of a given associative array
  * @param $row mixed[] contains data values indexed by field name
  * @return void
  *
  */
    public function populate($row) {
        foreach ($row as $fieldName => $value) {
            if (array_key_exists($fieldName, $this->dbFields))
                $this->dbFields[$fieldName] = $value;
        }
    }
    
/**
  * genWhereClause() - generates an SQL WHERE clause that identifies this record
  * @return string SQL fragment
  *
  */
    private function genWhereClause() {
        $columnCount = 0;
        $sql = 'WHERE ';
        foreach ($this->dbPrimary as $primaryIndexColumn) {
            if (++$columnCount > 1)
                $sql .= ' AND ';
            $sql .= $primaryIndexColumn;
            $sql .= '=';
            $sql .= Db::escape($this->dbFields[$primaryIndexColumn]);
        }
        return $sql;
    }

/**
  * genInsertSQL() - generates ANSI 92 SQL syntax for INSERT statement
  * @return string SQL fragment
  *
  */
    private function genInsertSQL() {
        $columnCount = 0;
        $sql = 'INSERT INTO ' . $this->tblName . ' (';
        foreach($this->dbFields as $columnName => $value) {
            if (!in_array($columnName, $this->dbAuto) && $columnName != "page_title") {
                if (++$columnCount > 1)
                    $sql .= ', ';
              $sql .= $columnName;
            }
        }
        $sql .= ') VALUES (';
        $columnCount = 0;
        foreach($this->dbFields as $columnName => $value) {
            if (!in_array($columnName, $this->dbAuto) && $columnName != "page_title") {
                if (++$columnCount > 1)
                    $sql .= ', ';
                $sql .= Db::escape($value);
            }
        }
        $sql .= ')';
		//echo $sql; exit;
        return $sql;
    }

/**
  * genUpdateSQL() - generates ANSI 92 SQL syntax for UPDATE statement
  * @return string SQL fragment
  *
  */
    private function genUpdateSQL() {
        $columnCount = 0;
        $sql = 'UPDATE ' . $this->tblName . ' SET ';
        foreach($this->dbFields as $columnName => $value) {
            if (!in_array($columnName, $this->dbAuto) && $columnName != "page_title") {
                if (++$columnCount > 1)
                    $sql .= ', ';
                $sql .= $columnName;
                $sql .= '=';
                $sql .= DB::escape($value);
            }
        }
        return $sql;
    }

/**
  * __toString() - returns a string representation of the objects
  * @return string SQL fragment
  *
  */
    public function __toString() {
        return var_export($this->dbFields, true);
    }

}
?>