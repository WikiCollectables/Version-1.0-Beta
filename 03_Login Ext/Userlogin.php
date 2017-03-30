<?php
/**
 * @addtogroup Templates
 */
if( !defined( 'MEDIAWIKI' ) ) die( -1 );

/** */
require_once( 'includes/SkinTemplate.php' );

/**
 * HTML template for Special:Userlogin form
 * @addtogroup Templates
 */
class UserloginTemplate extends QuickTemplate {
	function execute() {
		if( $this->data['message'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>

<div id="userloginForm">
<form name="userlogin" method="post" action="<?php $this->text('action') ?>">
	<h2><?php $this->msg('login') ?></h2>
	<p id="userloginlink"><?php $this->html('link') ?></p>
	<div id="userloginprompt"><?php  $this->msgWiki('loginprompt') ?></div>
	<?php if( @$this->haveData( 'languages' ) ) { ?><div id="languagelinks"><p><?php $this->html( 'languages' ); ?></p></div><?php } ?>
	<table>
		<tr>
			<td align='right'><label for='wpName1'><?php $this->msg('yourname') ?>:</label></td>
			<td align='left'>
				<input type='text' class='loginText' name="wpName" id="wpName1"
					tabindex="1"
					value="<?php $this->text('name') ?>" size='20' />
			</td>
		</tr>
		<tr>
			<td align='right'><label for='wpPassword1'><?php $this->msg('yourpassword') ?>:</label></td>
			<td align='left'>
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword1"
					tabindex="2"
					value="<?php $this->text('password') ?>" size='20' />
			</td>
		</tr>
	<?php if( $this->data['usedomain'] ) {
		$doms = "";
		foreach( $this->data['domainnames'] as $dom ) {
			$doms .= "<option>" . htmlspecialchars( $dom ) . "</option>";
		}
	?>
		<tr>
			<td align='right'><?php $this->msg( 'yourdomainname' ) ?>:</td>
			<td align='left'>
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>"
					tabindex="3">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td></td>
			<td align='left'>
				<input type='checkbox' name="wpRemember"
					tabindex="4"
					value="1" id="wpRemember"
					<?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>
					/> <label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td align='left' style="white-space:nowrap">
				<input type='submit' name="wpLoginattempt" id="wpLoginattempt" tabindex="5" value="<?php $this->msg('login') ?>" />&nbsp;<?php if( $this->data['useemail'] && $this->data['canreset']) { ?><input type='submit' name="wpMailmypassword" id="wpMailmypassword"
					tabindex="6"
									value="<?php $this->msg('mailmypassword') ?>" />
				<?php } ?>
			</td>
		</tr>
	</table>
<?php if( @$this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>
</form>
</div>
<div id="loginend"><?php $this->msgWiki( 'loginend' ); ?></div>
<?php

	}
}

/**
 * @addtogroup Templates
 */
class UsercreateTemplate extends QuickTemplate {
	function execute() {
// Enhanced User Login Extension Code Starts Here -->
		$dbw =& wfGetDB( DB_MASTER );

		if (trim($_REQUEST[escrowid]) != "") {
			$sql = "select s.user_id sid, b.user_id bid, s.user_registration sregdate, b.user_registration bregdate 
						from escrow_escrow e, user s, user b 
						where e.id='" . trim($_REQUEST[escrowid]) . "' and e.seller_id=s.user_id and e.buyer_id=b.user_id";
			$res = $dbw->query($sql, __METHOD__);
			$line = $dbw->fetchObject( $res ) ;

			if (!$line->sregdate) {
				$user_id=$line->sid;
			}
				
			else if (!$line->bregdate) {
				$user_id=$line->bid;
			}

			$sql = "select * from user where user_id='".$user_id."'";
			$res = $dbw->query($sql, __METHOD__);
			$line = $dbw->fetchObject( $res ) ;

			$user_name=$line->user_name;
			$userbusiness=$line->userbusiness;
			$user_email=$line->user_email;
			$user_real_name=$line->user_real_name;
			$userlastname=$line->userlastname;
			$useraddress1=$line->useraddress1;
			$useraddress2=$line->useraddress2;
			$usercity=$line->usercity;
			$userstate=$line->userstate;
			$userzip=$line->userzip;
			$usercountry=$line->usercountry;
			$usercurrency=$line->usercurrency;

			$userbusiness_ship=$line->userbusiness_ship;
			$useraddress1_ship=$line->useraddress1_ship;
			$usercity_ship=$line->usercity_ship;
			$userstate_ship=$line->userstate_ship;
			$userzip_ship=$line->userzip_ship;
			$usercountry_ship=$line->usercountry_ship;

			$paymentaccepttype=$line->paymentaccepttype;
			$terms=1;

			$append_actionurl = "&escrowid=" . trim($_REQUEST[escrowid]);
		}
		else {
			if ("" != trim($this->return_text('name'))) $user_name = trim($this->return_text('name'));
			if ("" != trim($this->return_text('business'))) $userbusiness = trim($this->return_text('business'));
			if ("" != trim($this->return_text('email'))) $user_email = trim($this->return_text('email'));
			if ("" != trim($this->return_text('realname'))) $user_real_name = trim($this->return_text('realname'));
			if ("" != trim($this->return_text('lastname'))) $userlastname = trim($this->return_text('lastname'));
			if ("" != trim($this->return_text('address1'))) $useraddress1 = trim($this->return_text('address1'));
			if ("" != trim($this->return_text('address2'))) $useraddress2 = trim($this->return_text('address2'));
			if ("" != trim($this->return_text('city'))) $usercity = trim($this->return_text('city'));
			if ("" != trim($this->return_text('state'))) $userstate = trim($this->return_text('state'));
			if ("" != trim($this->return_text('zip'))) $userzip = trim($this->return_text('zip'));
			if ("" != trim($this->return_text('country'))) $usercountry = trim($this->return_text('country'));
			if ("" != trim($this->return_text('currency'))) $usercurrency = trim($this->return_text('usercurrency'));

			if ("" != trim($this->return_text('business_ship'))) $userbusiness_ship = trim($this->return_text('business_ship'));
			if ("" != trim($this->return_text('address1_ship'))) $useraddress1_ship = trim($this->return_text('address1_ship'));
			if ("" != trim($this->return_text('city_ship'))) $usercity_ship = trim($this->return_text('city_ship'));
			if ("" != trim($this->return_text('state_ship'))) $userstate_ship = trim($this->return_text('state_ship'));
			if ("" != trim($this->return_text('zip_ship'))) $userzip_ship = trim($this->return_text('zip_ship'));
			if ("" != trim($this->return_text('country_ship'))) $usercountry_ship = trim($this->return_text('country_ship'));
			
			if ("" != trim($this->return_text('paymentaccepttype'))) $paymentaccepttype = trim($this->return_text('paymentaccepttype'));
			if ("" != trim($this->return_text('terms'))) $terms = trim($this->return_text('terms'));
		}
// Enhanced User Login Extension Code Ends Here -->
		if( $this->data['message'] ) {
?>
	<div class="<?php $this->text('messagetype') ?>box">
		<?php if ( $this->data['messagetype'] == 'error' ) { ?>
			<h2><?php $this->msg('loginerror') ?>:</h2>
		<?php } ?>
		<?php $this->html('message') ?>
	</div>
	<div class="visualClear"></div>
<?php } ?>
<!--Enhanced User Login Extension Code Starts Here -->
<div /*id="userlogin"*/>
<!--Enhanced User Login Extension Code Ends Here -->
<form name="userlogin2" id="userlogin2" method="post" action="<?php $this->text('action') ?>">
<!--Enhanced User Login Extension Code Starts Here -->
	<span style="visibility:hidden"><?php $this->msg('createaccount') ?>
	<span id="userloginlink"><?php $this->html('link') ?></span></span>
<!--Enhanced User Login Extension Code Ends Here -->
	<?php $this->html('header'); /* pre-table point for form plugins... */ ?>
	<?php if( @$this->haveData( 'languages' ) ) { ?><div id="languagelinks"><p><?php $this->html( 'languages' ); ?></p></div><?php } ?>
	
<!--Enhanced User Login Extension Code Starts Here -->
	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR><br>- <b>Important:</b> Once your account is created, we will send you a confirmation e-mail. <b>If you do not receive an e-mail from us</b>, please <A href="http://www.wikicoins.com/WikiCoins:Spam">read our article on spam</a>.<br>- WikiCoins is currently available only in the United States.  Coming soon in other currencies.<br>- Required fields are marked with an asterisk (*)<br>- Use your account on any <A href="http://www.wikicollectables.com">WikiCollectables</a> wiki and <A href="https://wikisafe.com">WikiSafe</a>.<br><br>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Create a Login</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
	<TABLE border=0 cellPadding=3 cellSpacing=1>
	  <TBODY>

	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN class=verdana10><FONT 
		  color=#3f3f3f><label for='wpName2'><?php $this->msg('yourname') ?>:</label><BR></FONT></SPAN></SPAN>
		  <INPUT maxLength=15 name="wpName" id="wpName2" value="<?=$user_name?>" ></TD>
		
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT 
		  color=#3f3f3f><label for='wpPassword2'><?php $this->msg('yourpassword') ?>:</label><BR></FONT></SPAN></SPAN><INPUT type=password maxLength=15 name="wpPassword" id="wpPassword2" value="<?php $this->text('password') ?>" onblur="this.form.wpRetype.value=this.value">
		  <input type='hidden' class='loginPassword' name="wpRetype" id="wpRetype" value="<?php $this->text('retype') ?>"/>
		  </TD></TR></TBODY></TABLE><SPAN class=verd10><BR><BR></SPAN>
	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 
		  width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Billing Address</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
	<TABLE border=0 cellPadding=3 cellSpacing=1>
	  <TBODY>

	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT 
		  color=#3f3f3f><!-- <label for='wpRealName'><?php $this->msg('yourrealname') ?></label> -->*First Name:<BR></FONT></SPAN></SPAN><INPUT maxLength=50 
		  name="wpRealName" id="wpRealName" value="<?=$user_real_name?>"></TD>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Last 
		  Name:</FONT></SPAN><BR><SPAN class=verdana10><FONT 
		  color=#8c8c8c><INPUT maxLength=50 name=userlastname value="<?=$userlastname?>"> 
		  </FONT></SPAN></SPAN></TD></TR>
<!-- Email Field Brought Down --> 
	  <TR>
		  <TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT 
		  color=#3f3f3f><label for='wpEmail'><?php $this->msg('youremail') ?></label><BR></FONT></SPAN></SPAN><INPUT 
		  maxLength=255 name="wpEmail" id="wpEmail" value="<?=$user_email?>"></TD></TR>
<!-- Email Field Brought Down -->

<!-- Bug # 44: Checkbox for Shipping Address to be same as Billing Address -->
<script language=javascript>
function changevalue(obj)
{
	if(document.userlogin2.chkAddress.checked == true)
	{
		if(obj.name != "usercountry")
		{
			eval("document.userlogin2." + obj.name + "_ship.value = obj.value;")
		}
		else if(obj.name == "usercountry")
		{
			eval("document.userlogin2." + obj.name + "_ship.selectedIndex = obj.selectedIndex;")
		}
	}
}
</script>
<!-- Bug # 44: Checkbox for Shipping Address to be same as Billing Address -->

	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>Business 
		  Name:</FONT></SPAN><BR><SPAN class=verdana10><FONT 
		  color=#8c8c8c><INPUT maxLength=50 name=userbusiness size=35 value="<?=$userbusiness?>" onkeyup="changevalue(this)" onblur="changevalue(this)"> 
		  </FONT></SPAN></SPAN></TD></TR>
	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Street Address, Apt# or Suite# (No PO Boxes):
		  </FONT></SPAN><BR><SPAN class=verdana10><FONT 
		  color=#8c8c8c><INPUT maxLength=50 name=useraddress1 
		  size=35 value="<?=$useraddress1?>" onkeyup="changevalue(this)" onblur="changevalue(this)"><? //<!-- <BR><INPUT maxLength=50 name=useraddress2 size=35 value="$useraddress2"> -->?>
		  </FONT></SPAN></SPAN></TD></TR>

	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*City:<BR><INPUT 
		  maxLength=50 name=usercity value="<?=$usercity?>" onkeyup="changevalue(this)" onblur="changevalue(this)"> </FONT></SPAN></SPAN></TD>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*State/Province:<BR><INPUT 
		  maxLength=50 name=userstate value="<?=$userstate?>" onkeyup="changevalue(this)" onblur="changevalue(this)"> </FONT></SPAN></SPAN></TD></TR>
	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Postal 
		  Code:<BR></FONT></SPAN></SPAN><INPUT maxLength=15 name=userzip 
		  size=13 value="<?=$userzip?>" onkeyup="changevalue(this)" onblur="changevalue(this)"></TD>
		<TD bgColor=white></TD></TR>
	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Country: 
		  </FONT></SPAN></SPAN><BR>
		  
		  <?include("extensions/usrlogin/countries.php");?>
			
		</TD>
	  </TR>

	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Currency: 
		  </FONT></SPAN></SPAN><BR>
		  
		  <?include("extensions/usrlogin/currencies.php");?>
			
			</TD></TR>			

	</TBODY></TABLE><SPAN 
	class=verd10><BR><BR></SPAN>

<!-- Shipping Address fields are added -->
	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 
		  width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Shipping Address</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
	<TABLE border=0 cellPadding=3 cellSpacing=1>
	  <TBODY>

<!-- Bug # 44: Checkbox for Shipping Address to be same as Billing Address -->
<script language=javascript>
function chkAdd()
{	
	document.userlogin2.userbusiness_ship.value = document.userlogin2.userbusiness.value;
	document.userlogin2.useraddress1_ship.value = document.userlogin2.useraddress1.value;
	document.userlogin2.usercity_ship.value = document.userlogin2.usercity.value;
	document.userlogin2.userstate_ship.value = document.userlogin2.userstate.value;
	document.userlogin2.userzip_ship.value = document.userlogin2.userzip.value;
	document.userlogin2.usercountry_ship.selectedIndex = document.userlogin2.usercountry.selectedIndex;
}
</script>

	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f><INPUT type=checkbox name=chkAddress value="" onclick="chkAdd()"> Check this box if your Shipping Address is the same as your Billing Address.
		  </FONT></SPAN></SPAN></TD></TR>
	  
<script language=javascript>
function ResetCheckBox()
{
	document.userlogin2.chkAddress.checked = false;
}
</script>
<!-- Bug # 44: Checkbox for Shipping Address to be same as Billing Address -->

	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>Business 
		  Name:</FONT></SPAN><BR><SPAN class=verdana10><FONT 
		  color=#8c8c8c><INPUT maxLength=50 name=userbusiness_ship size=35 value="<?=$userbusiness_ship?>" onkeyup="ResetCheckBox()" onmousedown="ResetCheckBox()"> 
		  </FONT></SPAN></SPAN></TD></TR>
	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Street Address, Apt# or Suite# (No PO Boxes):
		  </FONT></SPAN><BR><SPAN class=verdana10><FONT 
		  color=#8c8c8c><INPUT maxLength=50 name=useraddress1_ship 
		  size=35 value="<?=$useraddress1_ship?>" onkeyup="ResetCheckBox()" onmousedown="ResetCheckBox()"><? //<!-- <BR><INPUT maxLength=50 name=useraddress2_ship size=35 value="$useraddress2_ship"> -->?> 
		  </FONT></SPAN></SPAN></TD></TR>

	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*City:<BR><INPUT 
		  maxLength=50 name=usercity_ship value="<?=$usercity_ship?>" onkeyup="ResetCheckBox()" onmousedown="ResetCheckBox()"> </FONT></SPAN></SPAN></TD>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*State/Province:<BR><INPUT 
		  maxLength=50 name=userstate_ship value="<?=$userstate_ship?>" onkeyup="ResetCheckBox()" onmousedown="ResetCheckBox()"> </FONT></SPAN></SPAN></TD></TR>
	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Postal 
		  Code:<BR></FONT></SPAN></SPAN><INPUT maxLength=15 name=userzip_ship 
		  size=13 value="<?=$userzip_ship?>" onkeyup="ResetCheckBox()" onmousedown="ResetCheckBox()"></TD>
		<TD bgColor=white></TD></TR>
	  <TR>
		<TD bgColor=white colSpan=2><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*Country: 
		  </FONT></SPAN></SPAN><BR>
		  
		  <?
		  // shipping address uses the same countries.php
		  $ship_address = true;
		  include("extensions/usrlogin/countries.php");?>
			
			</TD></TR>
	</TBODY></TABLE><SPAN 
	class=verd10><BR><BR></SPAN>

<!-- Shipping Address fields are added -->

	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 
		  width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Payment Preference</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10><SPAN 
		  class=verdana10><FONT color=#3f3f3f>*How would you like to 
		  receive your payments? (You can change this 
		  later.)</FONT></SPAN></SPAN></TD></TR>
	  <TR>
		<TD bgColor=white><SELECT name=paymentaccepttype size=1> <?php $this->text('state') ?>
			<OPTION value=mail <?if ($this->text('paymentaccepttype')=="mail") echo "selected"?>>Regular Mail</OPTION> <OPTION 
			value=paypal <?if ($this->text('paymentaccepttype')=="paypal") echo "selected"?>>PayPal</OPTION> <OPTION 
			value=overnight <?if ($this->text('paymentaccepttype')=="overnight") echo "selected"?>>Overnight Mail (U.S. Only)</OPTION> <OPTION 
			value=air <?if ($this->text('paymentaccepttype')=="air") echo "selected"?>>Air Mail 
	  (International)</OPTION></SELECT></TD></TR></TBODY></TABLE><SPAN 
	class=verd10><BR><BR></SPAN>

	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 
		  width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Terms of 
				Service</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>

            <TABLE border=0 cellPadding=3 cellSpacing=1>
              <TBODY>
              <TR>
                <TD bgColor=white vAlign=top><INPUT name=terms type=checkbox 
                  value=1 <?if ($terms!="") echo "checked"?>></TD>
                <TD bgColor=white><SPAN class=verd10>*By checking this box, I indicate that I have read and agree to the WikiCoins <A href="http://www.wikicoins.com/index.php?title=WikiCoins:General_disclaimer" target="_new">Disclaimer</A>, WikiSafe <A href="http://www.wikisafe.com/terms.php" target="_new">Usage Agreement</A> and <A href="http://www.wikisafe.com/privacy.php" target="_new">Privacy Policy</A>.</SPAN></TD> </TR></TBODY></TABLE><SPAN 
            class=verd10><BR><BR></SPAN>

	<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
	  <TBODY>
	  <TR>
		<TD bgColor=white><SPAN class=verd10>
		  <TABLE bgColor=#4591b4 border=0 cellPadding=2 cellSpacing=0 
		  width=150>
			<TBODY>
			<TR>
			  <TD align=middle bgColor=#AAAAAA><SPAN 
				class=verd10><FONT color=white><B>Security Code</B></FONT></SPAN></TD></TR></TBODY></TABLE></SPAN>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD bgColor=white></TD></TR></TBODY></TABLE>
		  <TABLE border=0 cellPadding=0 cellSpacing=0 height=1 
		  width="100%">
			<TBODY>
			<TR>
			  <TD 
	bgColor=#4591b4></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE>

            <TABLE border=0 cellPadding=3 cellSpacing=1>
              <TBODY>
              <TR>
                <TD bgColor=white><SPAN class=verd10>*To help protect against automated account creation, please enter the numbers that appear below in the box.</SPAN></TD> </TR></TBODY></TABLE>
				<?
				$rand = rand(10000, 99999); // generate 5 digit random number
				$_SESSION["captcha_hash"] = md5($rand); // create the hash for the random number and put it in the session
				?>
				<img src=extensions/usrlogin/captcha.php?rand=<?=$rand?>><INPUT name=captcha_code type=text size=5 value=>

				<SPAN class=verd10><BR><BR></SPAN>
			
			<TABLE border=0 cellPadding=3 cellSpacing=1 width="100%">
              <TBODY>
              <TR>
                <TD vAlign=top>
				<input type="Hidden" name="register" value="1">
				<input type="Hidden" name="escrowid" value="<?=$_REQUEST[escrowid]?>">
				<input /*src="escrow/img/updatemyaccount.gif"*/ type=submit name="wpCreateaccount" id="wpCreateaccount"
					value="<?php $this->msg('createaccount') ?>" />
				<?php if( $this->data['createemail'] ) { ?>
				<input type='submit' name="wpCreateaccountMail" id="wpCreateaccountMail"
					value="<?php $this->msg('createaccountmail') ?>" />
				<?php } ?>
				</TD></TR></TBODY></TABLE><SPAN class=verd10><INPUT name=doregister type=hidden value=1><BR></SPAN></TD> </TR></TBODY></TABLE>

<?php
// Enhanced User Login Extension Code Ends Here -->
// Enhanced User Login Extension Code DELETION Starts Here -->
/*	
?>	
	<table>
		<tr>
			<td align='right'><label for='wpName2'><?php $this->msg('yourname') ?>:</label></td>
			<td align='left'>
				<input type='text' class='loginText' name="wpName" id="wpName2"
					tabindex="1"
					value="<?php $this->text('name') ?>" size='20' />
			</td>
		</tr>
		<tr>
			<td align='right'><label for='wpPassword2'><?php $this->msg('yourpassword') ?>:</label></td>
			<td align='left'>
				<input type='password' class='loginPassword' name="wpPassword" id="wpPassword2"
					tabindex="2"
					value="<?php $this->text('password') ?>" size='20' />
			</td>
		</tr>
	<?php if( $this->data['usedomain'] ) {
		$doms = "";
		foreach( $this->data['domainnames'] as $dom ) {
			$doms .= "<option>" . htmlspecialchars( $dom ) . "</option>";
		}
	?>
		<tr>
			<td align='right'><?php $this->msg( 'yourdomainname' ) ?>:</td>
			<td align='left'>
				<select name="wpDomain" value="<?php $this->text( 'domain' ) ?>"
					tabindex="3">
					<?php echo $doms ?>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td align='right'><label for='wpRetype'><?php $this->msg('yourpasswordagain') ?>:</label></td>
			<td align='left'>
				<input type='password' class='loginPassword' name="wpRetype" id="wpRetype"
					tabindex="4"
					value="<?php $this->text('retype') ?>"
					size='20' />
			</td>
		</tr>
		<tr>
			<?php if( $this->data['useemail'] ) { ?>
				<td align='right'><label for='wpEmail'><?php $this->msg('youremail') ?></label></td>
				<td align='left'>
					<input type='text' class='loginText' name="wpEmail" id="wpEmail"
						tabindex="5"
						value="<?php $this->text('email') ?>" size='20' />
				</td>
			<?php } ?>
			<?php if( $this->data['userealname'] ) { ?>
				</tr>
				<tr>
					<td align='right'><label for='wpRealName'><?php $this->msg('yourrealname') ?></label></td>
					<td align='left'>
						<input type='text' class='loginText' name="wpRealName" id="wpRealName"
							tabindex="6"
							value="<?php $this->text('realname') ?>" size='20' />
					</td>
			<?php } ?>
		</tr>
		<tr>
			<td></td>
			<td align='left'>
				<input type='checkbox' name="wpRemember"
					tabindex="7"
					value="1" id="wpRemember"
					<?php if( $this->data['remember'] ) { ?>checked="checked"<?php } ?>
					/> <label for="wpRemember"><?php $this->msg('remembermypassword') ?></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td align='left'>
				<input type='submit' name="wpCreateaccount" id="wpCreateaccount"
					tabindex="8"
					value="<?php $this->msg('createaccount') ?>" />
				<?php if( $this->data['createemail'] ) { ?>
				<input type='submit' name="wpCreateaccountMail" id="wpCreateaccountMail"
					tabindex="9"
					value="<?php $this->msg('createaccountmail') ?>" />
				<?php } ?>
			</td>
		</tr>
	</table>
	<?php

		if ($this->data['userealname'] || $this->data['useemail']) {
			echo '<div id="login-sectiontip">';
			if ( $this->data['useemail'] ) {
				echo '<div>';
				$this->msgHtml('prefs-help-email');
				echo '</div>';
			}
			if ( $this->data['userealname'] ) {
				echo '<div>';
				$this->msgHtml('prefs-help-realname');
				echo '</div>';
			}
			echo '</div>';
		}
	*/// Enhanced User Login Extension Code DELETION Ends Here -->
	?>
<?php if( @$this->haveData( 'uselang' ) ) { ?><input type="hidden" name="uselang" value="<?php $this->text( 'uselang' ); ?>" /><?php } ?>
</form>
</div>
<div id="signupend"><?php $this->msgWiki( 'signupend' ); ?></div>
<?php

	}
}

?>
