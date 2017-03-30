
<?php
function wfSpecialArticleswiththeMostAds() {
global $wgOut, $wgDBprefix;
$ads = array();
$dbr =& wfGetDB( DB_SLAVE);  //  art_page 
$res = $dbr->query( "select * from ".$wgDBprefix."page where  page_namespace = 0" );
$con = '

<table width="100%" >
			<tr align= "left" ><th width = "25%">Articles with most Ads</th><th width = "25%" align= "center">Total Ads</th><th width = "25%" align= "center">Buyer Ads</th><th width = "25%" align= "center"> Seller Ads</th></tr>';
$j = 0;
while ( $row = $dbr->fetchObject( $res ) ) {
	$page_title = $row->page_title;//page_title
	$page_id = $row->page_id;//page_id
	$a[$j]['page_title'] = $page_title;
	$a[$j]['page_id'] = $page_id;
	$j++;
	
}
$j = 0;
//echo "<pre>"; print_r($a); echo "</pre>";
$number_of_ads = count($a);

require_once('extensions/buysell/model/wcConfig.php');
$cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));

for ($i = 0; $i < $number_of_ads; $i++ ){
   $id_of_page = $a[$i]['page_id'];
	$title_of_page =  $a[$i]['page_title'];	
	$buyer_count_sql = "select count(*) as buyer_number from ".$wgDBprefix."ads
							where page_id = $id_of_page and ad_type = 'B' 
							";
	$buyer_count_sql = "select count(a.ad_id) as buyer_number from ".$wgDBprefix."ads a
							where a.page_id = $id_of_page and a.ad_type = 'B' 
								/*and a.post_date >='$cutOffDate'*/
								and a.ad_id NOT IN (SELECT ad_id 
														FROM escrow_escrow 
														WHERE escrow_prefix = '$wgDBprefix' 
																AND buyer_id = a.user_id
																AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
													)
							";
	//echo "<br>sql1 = $buyer_count_sql";
	$res_buyer_count = $dbr->query( $buyer_count_sql);
	$row = $dbr->fetchObject( $res_buyer_count );
	$buyer_count = $row->buyer_number;
	$seller_count_sql = "select count(*) as seller_number from ".$wgDBprefix."ads
							where page_id = $id_of_page and ad_type = 'S' 
								";
	$seller_count_sql = "select count(a.ad_id) as seller_number from ".$wgDBprefix."ads a
							where a.page_id = $id_of_page and a.ad_type = 'S' 
								/*and a.post_date >='$cutOffDate'*/
								and a.ad_id NOT IN (SELECT ad_id 
														FROM escrow_escrow 
														WHERE escrow_prefix = '$wgDBprefix' 
																AND seller_id = a.user_id
																AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
													)
							";
	//echo "<br>sql1 = $seller_count_sql ";
	$res_seller_count = $dbr->query($seller_count_sql );
	$row = $dbr->fetchObject( $res_seller_count );
	$seller_count = $row->seller_number;
	$total_count = $buyer_count + $seller_count;
	//echo "<br><br>seller count = $seller_count buyer count = $buyer_count total_count = $total_count";
	if ( $total_count > 0 ) {
		$title = str_replace("_", " ", $title_of_page);
		$ads[$j]['link'] = "/index.php?title=$title_of_page";
		$ads[$j]['page_title'] = $title;
		$ads[$j]['totla_ads'] = $total_count;
		$ads[$j]['buyer_count'] = $buyer_count;
		$ads[$j]['seller_count'] = $seller_count;
	//	$ads[$j]['link'] = str_replace();
		$j++;
	}
	if ( $j > 49 )
		break;
}
$temp = msort($ads, "totla_ads", false );
$ads = $temp;
$list_ad_count = count($ads);
$replace_string = '<tr align="left"><td width = "25%" >&nbsp;&nbsp;&nbsp;&nbsp;{$count}.&nbsp;<a href="{$link}">{$page_title}</a></td><td width = "25%" align= "center" >&nbsp;&nbsp;&nbsp;&nbsp;{$total_ads}</td><td  width = "25%" align= "center">&nbsp;&nbsp;&nbsp;&nbsp;{$buyer_count}</td><td width = "25%" align= "center">&nbsp;&nbsp;&nbsp;&nbsp;{$seller_count}</td></tr>';
//echo $list_ad_count;
$table_content = '';
for ($j = 0; $j < $list_ad_count; $j++){
	//echo "j = $j";
	$temp = $replace_string;
	$temp = str_replace('{$count}', $j + 1, $temp );
	$temp = str_replace('{$page_title}',$ads[$j]['page_title'], $temp );
	$temp = str_replace('{$link}', $ads[$j]['link'], $temp );
	$temp = str_replace('{$total_ads}', "&nbsp;".$ads[$j]['totla_ads'] ,  $temp );
	$temp = str_replace('{$buyer_count}',  "&nbsp;". $ads[$j]['buyer_count'], $temp );
	$temp = str_replace('{$seller_count}', "&nbsp;".$ads[$j]['seller_count'] , $temp );
	$table_content .= $temp;
}
$con = $con.$table_content;
$con .= "</table>";
//$dbr =& wfGetDB( DB_SLAVE, 'watchlist' );

$wgOut->addHTML($con);
}

function msort($array, $id="total_ads", $sort_ascending=true) {
        $temp_array = array();
        while(count($array)>0) {
            $lowest_id = 0;
            $index=0;
            foreach ($array as $item) {
                if (isset($item[$id])) {
                                    if ($array[$lowest_id][$id]) {
                    if ($item[$id]<$array[$lowest_id][$id]) {
                        $lowest_id = $index;
                    }
                    }
                                }
                $index++;
            }
            $temp_array[] = $array[$lowest_id];
            $array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
        }
                if ($sort_ascending) {
            return $temp_array;
                } else {
                    return array_reverse($temp_array);
                }
}






?> 