<?
# This page should be in includes folder as cookies are made in includes folder and deleted from includes folder also.
//print_r($_COOKIE);

// cannot use LocalSettings.php file as this file is not a valid start point for media wiki
include("BuySellConfig.php");

// connect to the database 
$db = mysql_connect($wgDBserver, $wgDBuser, $wgDBpassword);
mysql_select_db($wgDBname, $db);

if(is_array($_COOKIE))
{
	foreach($_COOKIE as $key=>$value)
	{
		if (eregi("_UserID", $key))
		{
			$userid = $value;
		}
		if (eregi("_UserName", $key))
		{
			$username = $value;
		}
	}
}

$query = "SELECT user_password FROM user WHERE user_name = '" . $username . "' ";
$result = mysql_query($query);
$row = mysql_fetch_array($result);

// post username and password to $wgEscrowLoginPath
?>
<html>
<body>
<form action="<?=$wgEscrowLoginPath?>" name=frm method=post>
<input type=hidden name="username" value="<?=$username?>">
<input type=hidden name="mdpu" value="<?=$row["user_password"]?>">
</form>
<script language=javascript>
document.frm.submit();
</script>
<body>
</html>
