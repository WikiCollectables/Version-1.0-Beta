<?

# This file is used in SpecialUserlogin.php as enhanced login form validation extension
class FormValidations
{
	function FormValidate($extFormValid)
	{
// Added form validations for Point #4 Bug #10 - 10.3
		if ( isset($extFormValid->mPassword) && trim($extFormValid->mPassword) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified password." );
			return false;
		}
		if ( isset($extFormValid->mRealName) && trim($extFormValid->mRealName) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified first name." );
			return false;
		}
		if ( isset($extFormValid->mLastname) && trim($extFormValid->mLastname) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified last name." );
			return false;
		}
		if ( isset($extFormValid->mEmail) && trim($extFormValid->mEmail) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified email address." );
			return false;
		}
		if ( isset($extFormValid->mAddress1) && trim($extFormValid->mAddress1) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified street address." );
			return false;
		}
		if ( isset($extFormValid->mCity) && trim($extFormValid->mCity) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified city name." );
			return false;
		}
		if ( isset($extFormValid->mState) && trim($extFormValid->mState) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified state/province name." );
			return false;
		}
		if ( isset($extFormValid->mZip) && trim($extFormValid->mZip) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified postal code." );
			return false;
		}

		if ( isset($extFormValid->mAddress1_ship) && trim($extFormValid->mAddress1_ship) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified shipping street address." );
			return false;
		}
		if ( isset($extFormValid->mCity_ship) && trim($extFormValid->mCity_ship) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified shipping city name." );
			return false;
		}
		if ( isset($extFormValid->mState_ship) && trim($extFormValid->mState_ship) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified shipping state/province name." );
			return false;
		}
		if ( isset($extFormValid->mZip_ship) && trim($extFormValid->mZip_ship) == "" ) {
			$extFormValid->mainLoginForm( "You have not specified shipping postal code." );
			return false;
		}
// Added form validations for Point #4 Bug #10 - 10.3

// Added validation for PO Box address for point #2 Bug #10.1
		$address = ereg_replace(" |\.", "", $extFormValid->mAddress1);
		if ( isset($extFormValid->mAddress1) && (eregi("POBox([0-9]{1,5})", $address) || eregi("Box([0-9]{1,5})", $address) ) ) {
			$extFormValid->mainLoginForm( "PO Box address not allowed" );
			return false;
		}
		$address = ereg_replace(" |\.", "", $extFormValid->mAddress1_ship);
		if ( isset($extFormValid->mAddress1_ship) && (eregi("POBox([0-9]{1,5})", $address) || eregi("Box([0-9]{1,5})", $address) ) ) {
			$extFormValid->mainLoginForm( "PO Box address not allowed in shipping address" );
			return false;
		}
// Added validation for PO Box address for point #2 Bug #10.1

// Added validation for captcha code for point #5 Bug #10

// Added captcha code validations for Point #5 Bug #10 - 10.4
		if( md5($extFormValid->mcaptcha_code) != $_SESSION["captcha_hash"] ) {
			$extFormValid->mainLoginForm( "Security Code entered is not correct." );
			return false;
		}
// Added validation for captcha code for point #5 Bug #10 - 10.4
		
		return true;
	}
}
?>