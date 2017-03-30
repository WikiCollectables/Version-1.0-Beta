<?php
/**
 * Class Db
 *
 * The Db class represents a database. It provides a singleton pattern to create a PDO
 * instance and encapsulates basic data filtering and vendor-specific SQL abstraction.
 *
 *
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

require_once('extension/buysell/model/wcConfig.php');

class Db {
    
/**
 * Member variables
 *
 */
    static private $instance = null;            // stores PDO database connection

    private function __construct() {            // private constructor
    }

    private function __clone() {                // no clones
    }

/**
  * getInstance() - create/return a PDO database object
  * @param $dbName string optional name of the database
  * @return void
  *
  */
    static public function getInstance($dbName= wcConfig::DB_WIKI_COINS) {
        if (self::$instance == null) {
            $connectionString = wcConfig::DB_DRIVER . 
              ':host=' . wcConfig::DB_HOST .
              ';dbname=' . $dbName;
            try {
                self::$instance = new PDO($connectionString, wcConfig::DB_USER, wcConfig::DB_PASSWORD);
            } 
            catch (PDOException $e) {
                die('Fatal Error: cannot connect to WikiCoins database.');
            }
        }
        return self::$instance;
    }
    
/**
  * close() - close PDO connection
  * @return void
  *
  */
    static public function close() {
        self::$instance = null;
    }
    
/**
  * escape() - escapes field value used in queries and encloses them in quotes
  * @return string filtered and escaped value
  *
  */
    static public function escape($value) {
        if (is_null($value) || (strcasecmp($value, 'null') == 0))
            $value = 'NULL';
        elseif (!is_numeric($value))
        {
            if (get_magic_quotes_gpc())
                $value = stripslashes($value);
            $value = "'" . addslashes($value) . "'";
        }
        return $value;
    }
}
?>