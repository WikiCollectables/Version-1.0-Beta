<?php

require_once('extensions/buysell/model/wcConfig.php');

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

class Db {
    
/**
 * Member variables
 *
 */
    static private $instance = null;            // stores MySQL connection

    private function __construct() {            // private constructor
    }

    private function __clone() {                // no clones
    }

/**
  * getInstance() - create/return a MySQL database link
  * @param $dbName string optional name of the database
  * @return void
  *
  */
    static public function getInstance($dbName=wcConfig::DB_WIKI_COINS) {
        if (is_null(self::$instance)) {
            $db = mysql_connect(wcConfig::DB_HOST, wcConfig::DB_USER, wcConfig::DB_PASSWORD);
            if (!$db)
                die('Fatal Error: cannot connect to database server.');
            else {
                if (!mysql_select_db($dbName))
                    die('Fatal Error: cannot connect to database.');
                self::$instance = $db;
            }
        }
        return self::$instance;
    }
    
/**
  * close() - close MySQL database link
  * @return void
  *
  */
    static public function close() {
        mysql_close(self::$instance);
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