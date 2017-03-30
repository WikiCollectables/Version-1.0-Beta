<?php
if(is_object($this))
{
	if(trim($this->data['currency']) != "")
		$usercurrency = trim($this->data['currency']);
}

$sql = "select curr_country, curr_symbol
			from escrow_currencies ";
$res = $dbw->query($sql, __METHOD__);
?>
<script language=javascript>
function changetoUSOnly(frmCtrl)
{
	if(frmCtrl.options[frmCtrl.selectedIndex].value != "US$")
	{
		alert("Currently we are supporting only United States Dollars (US$)");
		var optioncount = frmCtrl.options.length;
		for(var i=0; i < optioncount; i++)
		{
			if(frmCtrl.options[i].value == "US$")
			{
				frmCtrl.options[i].selected = true;
				return false;
			}
		}
	}
}
</script>
<SELECT name=usercurrency onchange="changetoUSOnly(this)"> 
<?
while($line = $dbw->fetchObject( $res ))
{
	// Euro symbol can be shown from this line of code
	$curr_symbol_entity = mb_convert_encoding($line->curr_symbol,'HTML-ENTITIES',"ISO-8859-1");

	if(trim($line->curr_symbol) == trim($this->data['currency']))
		echo "<OPTION selected value='$curr_symbol_entity'>$line->curr_country ($curr_symbol_entity)</OPTION>\n";
	else
		echo "<OPTION value='$curr_symbol_entity'>$line->curr_country ($curr_symbol_entity)</OPTION>\n";
}
?>
</SELECT>
