<?php
if(is_object($this))
{
	if(trim($this->data['country']) != "")
		$usercountry = trim($this->data['country']);
}
/*
if($countryType == "seller")
	$countryName = "sellercountry";
elseif($countryType == "buyer")
	$countryName = "buyercountry";
else
	$countryName = "usercountry";*/

// shipping address uses the same countries.php
if($ship_address == true)
{
	$countryName = "usercountry_ship";
	$usercountry = $usercountry_ship;
	$addedJS = "ResetCheckBox();";
}
else
{
	$countryName = "usercountry";
	$usercountry = $usercountry;
	$addedJS = "changevalue(this);";
}
?>
<script language=javascript>
function changetoUSCountryOnly(frmCtrl)
{
	if(frmCtrl.options[frmCtrl.selectedIndex].value != "United States")
	{
		alert("Currently we are supporting only United States");
		var optioncount = frmCtrl.options.length;
		for(var i=0; i < optioncount; i++)
		{
			if(frmCtrl.options[i].value == "United States")
			{
				frmCtrl.options[i].selected = true;
				return false;
			}	
		}
	}
}
</script>
<SELECT name=<?=$countryName?> onchange="if(changetoUSCountryOnly(this)){<?=$addedJS?>}"> <OPTION 
                    selected value="United States" <?if ($usercountry=="United States") echo "selected";?>>United States</OPTION> 
					<OPTION value="Brazil"<?if ($usercountry=="Brazil") echo "selected";?>>Brazil</OPTION> 
					<OPTION value="Canada"<?if ($usercountry=="Canada") echo "selected";?>>Canada</OPTION> 
					<OPTION value="China"<?if ($usercountry=="China") echo "selected";?>>China</OPTION> 
					<OPTION value="Denmark"<?if ($usercountry=="Denmark") echo "selected";?>>Denmark</OPTION>  
                    <OPTION value="France"<?if ($usercountry=="France") echo "selected";?>>France</OPTION> 
					<OPTION value="Germany"<?if ($usercountry=="Germany") echo "selected";?>>Germany</OPTION> 
					<OPTION value="Greece"<?if ($usercountry=="Greece") echo "selected";?>>Greece</OPTION>					<OPTION value="Hong Kong"<?if ($usercountry=="Hong Kong") echo "selected";?>>Hong Kong</OPTION> 					<OPTION value="India"<?if ($usercountry=="India") echo "selected";?>>India</OPTION>                     
					<OPTION value="Ireland"<?if ($usercountry=="Ireland") echo "selected";?>>Ireland</OPTION> 
					<OPTION value="Israel"<?if ($usercountry=="Israel") echo "selected";?>>Israel</OPTION> 
					<OPTION value="Italy"<?if ($usercountry=="Italy") echo "selected";?>>Italy</OPTION>  
					<OPTION value="Japan"<?if ($usercountry=="Japan") echo "selected";?>>Japan</OPTION>                     
					<OPTION value="Korea, Dem People's Rep"<?if ($usercountry=="Korea, Dem People's Rep") echo "selected";?>>Korea, Dem People's Rep</OPTION> 					<OPTION value="Luxembourg"<?if ($usercountry=="Luxembourg") echo "selected";?>>Luxembourg</OPTION> 
					<OPTION value="Macau"<?if ($usercountry=="Macau") echo "selected";?>>Macau</OPTION> 					<OPTION value="Mexico"<?if ($usercountry=="Mexico") echo "selected";?>>Mexico</OPTION> 					<OPTION value="Netherlands"<?if ($usercountry=="Netherlands") echo "selected";?>>Netherlands</OPTION> 
                    <OPTION value="New Zealand"<?if ($usercountry=="New Zealand") echo "selected";?>>New Zealand</OPTION> 
					<OPTION value="Norway"<?if ($usercountry=="Norway") echo "selected";?>>Norway</OPTION>					<OPTION value="Portugal"<?if ($usercountry=="Portugal") echo "selected";?>>Portugal</OPTION> 					<OPTION value="South Africa"<?if ($usercountry=="South Africa") echo "selected";?>>South Africa</OPTION> 					<OPTION value="Spain"<?if ($usercountry=="Spain") echo "selected";?>>Spain</OPTION> 
					<OPTION value="Sweden"<?if ($usercountry=="Sweden") echo "selected";?>>Sweden</OPTION> 
					<OPTION value="Switzerland"<?if ($usercountry=="Switzerland") echo "selected";?>>Switzerland</OPTION> 					<OPTION value="Taiwan"<?if ($usercountry=="Taiwan") echo "selected";?>>Taiwan</OPTION>					<OPTION value="Thailand"<?if ($usercountry=="Thailand") echo "selected";?>>Thailand</OPTION> 					<OPTION value="United Arab Emirates"<?if ($usercountry=="United Arab Emirates") echo "selected";?>>United Arab Emirates</OPTION> 
					<OPTION value="United Kingdom"<?if ($usercountry=="United Kingdom") echo "selected";?>>United Kingdom</OPTION> 
					<OPTION value="United States"<?if ($usercountry=="United States") echo "selected";?>>United States</OPTION> 					<OPTION value="Virgin Isles (British)"<?if ($usercountry=="Virgin Isles (British)") echo "selected";?>>Virgin Isles (British)</OPTION> 
					<OPTION value="Virgin Isles (U.S.)"<?if ($usercountry=="Virgin Isles (U.S.)") echo "selected";?>>Virgin Isles (U.S.)</OPTION> 				</SELECT>