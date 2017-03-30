<?php

//required
//session_start(); // start a session
//$rand = rand(10000, 99999); // generate 5 digit random number
//$_SESSION["captcha_hash"] = md5($rand); // create the hash for the random number and put it in the session
//$_SESSION["abc"] = "abc"; // create the hash for the random number and put it in the session

//ensure no caching by browser - START
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //A Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // no cache for HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false); // no cache for HTTP/1.1
header("Pragma: no-cache"); // no cache for HTTP/1.0
//ensure no caching by browser - END

//output the image
header('Content-type: image/jpeg'); // tell the browser that this is an image
//You can change the following, providing it still uses $rand
$image = imagecreate(55, 15); // create the image
$bgColor = imagecolorallocate ($image, 255, 255, 255); // use white as the background image
$textColor = imagecolorallocate ($image, 0, 0, 0); // the text color is black
//imagestring ($image, 5, 5, 0, $rand, $textColor); // write the random number
imagestring ($image, 5, 5, 0, $_REQUEST["rand"], $textColor); // write the random number
imagejpeg($image); // send the image to the browser
imagedestroy($image); // destroy the image to free up the memory

?>