<?
// This page cannot be accessed directly. It has to be accessed from session_from_wikisafe.php
// Cannot use LocalSettings.php file as this file is not a valid start point for mediawiki

include("BuySellConfig.php");

// connect to the database 
$db = mysql_connect($wgDBserver, $wgDBuser, $wgDBpassword);
mysql_select_db($wgDBname, $db);

// get request from wikisafe.com
if($_REQUEST["wsUserID"] != "" && $_REQUEST["wsUserName"] != "" && $_REQUEST["wsToken"] != "")
{
	$query = "SELECT user_id, user_name, user_token FROM user WHERE user_id = '" . $_REQUEST["wsUserID"] . "' AND user_name = '" . $_REQUEST["wsUserName"] . "' AND user_token = '" . $_REQUEST["wsToken"] . "' ";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if(count($row) > 0)
	{
		// make wiki session
		
		$exp = time() + $wgCookieExpiration;

		session_start();
		session_destroy();

		$_SESSION["wsUserID"] = $row["user_id"];
		setcookie( $wgCookiePrefix.'UserID', "", $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
		setcookie( $wgCookiePrefix.'UserID', $row["user_id"], $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );

		$_SESSION["wsUserName"] = $row["user_name"];
		setcookie( $wgCookiePrefix.'UserName', "", $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
		setcookie( $wgCookiePrefix.'UserName', $row["user_name"], $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );

		$_SESSION["wsToken"] = $row["user_token"];
		setcookie( $wgCookiePrefix.'Token', "", $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
		setcookie( $wgCookiePrefix.'Token', $row["user_token"], $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );

		//print_r($_SESSION);
		// redirect to wiki article page
		//echo $_REQUEST["page_title"];

		//print_r($_SESSION);
		//print_r($_COOKIE);
		//print_r($row);
		//exit;
		
		header("location: ../index.php?title=" . $_REQUEST["page_title"]);
		exit;
	}
	// else redirect to "" or homepage
	else
	{
		header("location: ../index.php");
		exit;
	}
}

// post username and password to $wgEscrowLoginPath
?>
