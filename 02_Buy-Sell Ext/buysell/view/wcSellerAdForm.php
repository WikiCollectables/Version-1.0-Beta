<?php
/**
 * wcSellerAdForm.php
 *
 * Contains XHTML presentation code for the display of the seller's ad input form.
 * 
 * Application: wikicoins.com buy/sell feature
 *
 * @author     Thomas Knierim
 * @copyright  (c) 2006 wikicoins.com
 * @version    1.0
 * @see        wcBuySellPage.php
 *
 */

// $_SERVER['PHP_SELF']

/*

				<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" name=\"sellerAdForm\" enctype=\"multipart/form-data\" 
                   accept-charset=\"UTF-8\" style=\"margin: 0px; padding: 0px\">
*/
//Code added By Nasir starts here
global $wgDBprefix;
//Code added by Nasir ends here 


include_once('extensions/buysell/model/wcConfig.php');
$user_browser = browser_detection('browser');
//Code added By Nasir starts here

//if ( $this->getWikiUserID() != '' ) {
//	$con =& wfGetDB( DB_SLAVE);
//	$cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));
//	$qry = "SELECT count(*) as number  FROM ".$wgDBprefix."ads where user_id = " . $this->getWikiUserID() . " and  page_id = " . $this->getwikiPageID() . " and ad_type = 'S' and post_date >='$cutOffDate'";
//	$res = $con->query( $qry );
//	$row = $con->fetchObject( $res );	
//	$seller_num = $row->number;

//}

if ( $this->getWikiUserID() != '' ) 
{
	$con =& wfGetDB( DB_SLAVE);
	$cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));

	$qry = "SELECT ad_id FROM ".$wgDBprefix."ads
				where user_id = " . $this->getWikiUserID() . " and  
						page_id = " . $this->getwikiPageID() . " and 
						ad_type = 'S' /*and 
						post_date >='$cutOffDate'*/ ";

	unset($res);
	$res = $con->query( $qry );
	//$row = $con->fetchObject( $res );
	//$seller_num = $row->number;
	unset($ad_ids);
	while ( $row = $con->fetchObject( $res ) ) 
	{
		$ad_ids[] = $row->ad_id;
	}
	$seller_num = 0;
	if(is_array($ad_ids))
	{
		foreach($ad_ids as $val)
		{
			$qry = "SELECT id FROM escrow_escrow WHERE ad_id = $val and escrow_prefix = '$wgDBprefix' and 
					( 
						status NOT IN ('none', 'Submit Offer', 'Counter Offer')
					) ";
			$res = $con->query( $qry );
			$row = $con->fetchObject( $res );
			$id = $row->id;
			if($id != "")
			{ 
				// if escrow is accepted then ignore the count
			}
			else
			{
				$seller_num++;
			}
		}
	}
}

//Code added by Nasir ends here 
$buysell_content .= "

<html>
<head>
  <title></title>
  <meta name=\"GENERATOR\" content=\"Quanta Plus\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=\">
  <style type=\"text/css\">
td {
    empty-cells : show;
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 10px;
    font-weight: 400 ;
    color: #000000;
    margin:0px;
}
 </style>
</head>
<body>



            <tr>
              <td class=\"listcell\">";
				if($this->getWikiUserID())
					$buysell_content .= "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" name=\"sellerAdForm\" enctype=\"multipart/form-data\" accept-charset=\"UTF-8\" style=\"margin: 0px; padding: 0px\">";
				else
					$buysell_content .= "<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\" name=\"sellerAdForm\" enctype=\"multipart/form-data\" accept-charset=\"UTF-8\" style=\"margin: 0px; padding: 0px\" onsubmit=\"alert('To create an ad, first login or create an account'); return false;\">";

$buysell_content .= "<div id=\"sellerwidth\">
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"parentsell\">
<tr>
<td class=\"sltop\"><img src=\"extensions/buysell/objects/null.gif\" class=\"adjustsell\"></td>
<td width=\"100%\"><img src=\"extensions/buysell/objects/null.gif\" width=\"385\" height=\"1\" align=\"left\" border=\"0\"></td>
<td class=\"srtop\"><img src=\"extensions/buysell/objects/null.gif\" class=\"adjustsell\"></td>
</tr>
<tr>
<td width=\"100%\"  colspan='3'>
            <table id=\"sellerform\" cellpadding=\"0\" cellspacing=\"0\" border=0>
                    <tr>
                      <td class=\"ad_sell_td\" id=\"sellerform_left\" >
                        <input type=\"hidden\" id=\"sellerAction\" name=\"buysellaction\" value=\"postSellerAd\" />";
if($this->getWikiUserID())
$buysell_content .= "           <input type=\"submit\" id=\"postsellerad\" name=\"postSellerAd\" value=\"Post Seller Ad\"
                                onclick=\"return validateSellerAd(". $seller_num.")\" />";
else
$buysell_content .= "           <input type=\"submit\" id=\"postsellerad\" name=\"postSellerAd\" value=\"Post Seller Ad\"
                                onclick=\"alert('To create an ad, first login or create an account'); return false;\" />";
if($user_browser == 'mozilla') {
$buysell_content .= "    <div style=\"overflow: hidden\">
                          <textarea class=\"add_txtareaff\" id=\"selleradtext\" name=\"sellerAdText\"
                            onfocus=\"focusField(this.id)\"
                            onblur=\"blurField(this.id)\"
                            onkeyup=\"watchTextLength(this.id, 200)\"></textarea>
                        </div>
                      </td>";
}
else {

$buysell_content .= "    <div style=\"overflow: hidden\">
                          <textarea class=\"add_txtarea\" id=\"selleradtext\" name=\"sellerAdText\"
                            onfocus=\"focusField(this.id)\"
                            onblur=\"blurField(this.id)\"
                            onkeyup=\"watchTextLength(this.id, 200)\"></textarea>
                        </div>
                      </td>";


}
                     

// if($user_browser == "mozilla") {
$buysell_content .= "
 <td align=\"center\" id=\"sellerform_right1\">
<div>
<input type=\"text\" id=\"selleramount\" name=\"sellerAmount\" value=\"\"  class=\"sellerAmount\"                   onfocus=\"focusField(this.id)\" onblur=\"formatNumber(this.id); blurField(this.id)\" />";
if($user_browser == 'mozilla') {
 $buysell_content .= "<div id=\"shipweightlabel1\" >&nbsp;</div>";
}
else {
 $buysell_content .= "<div id=\"shipweightlabel\" >&nbsp;</div>";
}

 $buysell_content .=
 "<input type=\"text\" id=\"weightlbs\" name=\"weightLbs\" class=\"shipweight\" value=\"\"                            onfocus=\"focusField(this.id)\" onblur=\"formatNumber1(this.id); blurField(this.id)\" />
 <input type=\"text\" id=\"weightozs\" name=\"weightOzs\" class=\"shipweight\" value=\"\"                             onfocus=\"focusField(this.id)\" onblur=\"formatNumber1(this.id); blurField(this.id)\" />
</div>

                      </td> <td id=\"sellerform_right\">";
// }
// else {
// $buysell_content .=" <td width=\"30px\" height=\"70px\" valign=\"top\" align=\"center\" class=\"sellerform_right2\">
//                        <div align=\"center\">
//                           <input type=\"text\" id=\"selleramount1\" name=\"sellerAmount\" value=\"\"
//                             onfocus=\"focusField(this.id)\"
//                             onblur=\"formatNumber(this.id); blurField(this.id)\" /><br><br><span id=\"shipweightlabel\"><img src=\"extensions/buysell/objects/null.gif\" width=\"100\" height=\"15\" align=\"left\" border=\"0\"></span>
//                             <input type=\"text\" id=\"weightlbs\" name=\"weightLbs\" class=\"shipweight2\" value=\"\"
//                             onfocus=\"focusField(this.id)\"
//                             onblur=\"formatNumber(this.id); blurField(this.id)\" />
// 			  
//                           <input type=\"text\" id=\"weightozs\" name=\"weightOzs\" class=\"shipweight3\" value=\"\"
//                             onfocus=\"focusField(this.id)\"
//                             onblur=\"formatNumber(this.id); blurField(this.id)\" /></div>
//                         
//                       </td> <td class=\"ad_td\" id=\"sellerform_right3\">";
// }
$buysell_content .= "
                     
                            <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".wcConfig::IMAGE_MAX_SIZE."\" />
                          <input type=\"hidden\" name=\"img_clicked\" value=\"\"/>
                          <input type=\"hidden\" name=\"REQUEST_URI\" value=\"".$_SERVER['REQUEST_URI']."\"/>    


<a href=\"extensions/buysell/view/lightfileupload1.php?id=".$this->getWikiUserID()."&imgno=1\" class=\"add_upload\" rel=\"lyteframe\" rev=\"width: 370px; height: 70px; scrolling: no;\" title=\"UPLOAD IMAGE 1\">
                          <img class=\"add_img\" name=\"upload1\" id=\"href1\" border=\"0\" src=\"extensions/buysell/objects/UpImageBut01.gif\" width = \"110\" height=\"16\">
                           </a>
</div>

<a href=\"extensions/buysell/view/lightfileupload1.php?id=".$this->getWikiUserID()."&imgno=2\" class=\"add_upload\" rel=\"lyteframe\" rev=\"width: 370px; height: 70px; scrolling: no;\" title=\"UPLOAD IMAGE 2\">
                               <img class=\"add_img\" name=\"upload2\" id=\"href2\" border=\"0\" src=\"extensions/buysell/objects/UpImageBut02.gif\" width = \"110\" height=\"16\">
                           </a>



                          <a href=\"extensions/buysell/view/lightfileupload1.php?id=".$this->getWikiUserID()."&imgno=3\" class=\"add_upload\" rel=\"lyteframe\" rev=\"width: 370px; height: 70px; scrolling: no;\" title=\"UPLOAD IMAGE 3\">
                               <img  class=\"add_img\" name=\"upload3\" id=\"href3\" src=\"extensions/buysell/objects/UpImageBut03.gif\" width = \"110\" height=\"16\" >
                           </a>


                          <a href=\"extensions/buysell/view/lightfileupload1.php?id=".$this->getWikiUserID()."&imgno=4\" class=\"add_upload\" rel=\"lyteframe\" rev=\"width: 370px; height: 70px; scrolling: no;\" title=\"UPLOAD IMAGE 4\">
                               <img  class=\"add_img\" name=\"upload4\" id=\"href4\" border=\"0\" src=\"extensions/buysell/objects/UpImageBut04.gif\"  width = \"110\" height=\"16\" >
                           </a>

                           <a href=\"extensions/buysell/view/lightfileupload1.php?id=".$this->getWikiUserID()."&imgno=5\" class=\"add_upload\" rel=\"lyteframe\" rev=\"width: 370px; height: 70px; scrolling: no;\" title=\"UPLOAD IMAGE 5\">
                              <img  class=\"add_img0\" name=\"upload5\" id=\"href5\"	border=\"0\" src=\"extensions/buysell/objects/UpImageBut05.gif\" width = \"110\" height=\"16\">
                           </a>

                        </td>
                    </tr>
                  </table>





  </td>
</tr>
<tr>
<td class=\"slbot\"><img src=\"extensions/buysell/objects/null.gif\" class\"adjustsell1\"></td>
<td width=\"100%\"><img src=\"extensions/buysell/objects/null.gif\" width=\"385\" height=\"1\" align=\"left\" border=\"0\"></td>
<td class=\"srbot\"><img src=\"extensions/buysell/objects/null.gif\" class\"adjustsell1\"></td>
</tr>
</table>
		  </div>
			 <input type=hidden name=image1 value=\"\">
			 <input type=hidden name=image2 value=\"\"><input type=hidden name=image3 value=\"\"><input type=hidden name=image4 value=\"\"><input type=hidden name=image5 value=\"\">
                </form>
              </td>
            </tr>";


function browser_detection( $which_test ) {

	// initialize the variables
	$browser = '';
	$dom_browser = '';

	// set to lower case to avoid errors, check to see if http_user_agent is set
	$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

	// run through the main browser possibilities, assign them to the main $browser variable
	if (stristr($navigator_user_agent, "opera")) 
	{
		$browser = 'opera';
		$dom_browser = true;
	}

	elseif (stristr($navigator_user_agent, "msie 4")) 
	{
		$browser = 'msie4'; 
		$dom_browser = false;
	}

	elseif (stristr($navigator_user_agent, "msie")) 
	{
		$browser = 'msie'; 
		$dom_browser = true;
	}

	elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari"))) 
	{
		$browser = 'safari'; 
		$dom_browser = true;
	}

	elseif (stristr($navigator_user_agent, "gecko")) 
	{
		$browser = 'mozilla';
		$dom_browser = true;
	}
	
	elseif (stristr($navigator_user_agent, "mozilla/4")) 
	{
		$browser = 'ns4';
		$dom_browser = false;
	}
	
	else 
	{
		$dom_browser = false;
		$browser = false;
	}

	// return the test result you want
	if ( $which_test == 'browser' )
	{
		return $browser;
	}
	elseif ( $which_test == 'dom' )
	{
		return $dom_browser;
		//  note: $dom_browser is a boolean value, true/false, so you can just test if
		// it's true or not.
	}
}

		?>

