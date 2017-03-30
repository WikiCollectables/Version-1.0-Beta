<?

if($_SERVER["HTTP_HOST"] == "localhost")
{
	$wgDBserver         = "localhost";
	$wgDBname           = "wiki19";
	$wgDBuser           = "wiki";
	$wgDBpassword       = "wiki";

	$wgEscrowLoginPath = "http://localhost/wiki19/escrow/login.php";

	$wgDBprefix         = "fir_";
}
else
{
	$wgDBserver         = "localhost";
	$wgDBname           = "database_name";
	$wgDBuser           = "database_username";
	$wgDBpassword       = "database_password";

	$wgEscrowLoginPath = "https://the-escrow-url.com/login.php";
	$wgDBprefix         = "coi_";
}

$wgCookieExpiration = 2592000;
$wgCookieDomain = '';
$wgCookiePath = '/';
$wgCookieSecure = 0;

if ( $wgDBprefix ) {
	$wgCookiePrefix = $wgDBname . '_' . $wgDBprefix;
} elseif ( $wgSharedDB ) {
	$wgCookiePrefix = $wgSharedDB;
} else {
	$wgCookiePrefix = $wgDBname;
}

?>
