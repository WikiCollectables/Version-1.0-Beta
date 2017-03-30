<?php
/**
 * buysell.php
 *
 * Bootstrap buy/sell page.
 * 
 * Application: wikicoins.com buy/sell feature
 *
 * @package    buysell.php
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 *
 */

include_once('extensions/buysell/controller/wcController.php');

$controller = new wcController($my_wikiPageID, $my_wikiUserID);
$controller->run();
$buysell_content = $controller->render();

?>