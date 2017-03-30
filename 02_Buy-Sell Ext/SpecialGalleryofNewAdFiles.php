<?php
function wfSpecialGalleryofnewAdFiles( $par, $specialPage) {
//Code added by starting from here
global $wgUser, $wgOut, $wgLang, $wgRequest, $wgGroupPermissions,$wgDBprefix ;

//Code done by nasir ends here

$img_names = array();
$str_array = array();
$image_link = array();
$cur_image_link = array();
$missing = array();
$file_size = array();

	$wpIlMatch = $wgRequest->getText( 'wpIlMatch' );
	$dbr =& wfGetDB( DB_SLAVE );
	$sk = $wgUser->getSkin();
	$shownav = !$specialPage->including();
	$hidebots = $wgRequest->getBool('hidebots',1);

	$hidebotsql = '';
	if ($hidebots) {

		/** Make a list of group names which have the 'bot' flag
		    set.
		*/
		$botconds=array();
		foreach ($wgGroupPermissions as $groupname=>$perms) {
			if(array_key_exists('bot',$perms) && $perms['bot']) {
				$botconds[]="ug_group='$groupname'";
			}
		}

		/* If not bot groups, do not set $hidebotsql */
		if ($botconds) {
			$isbotmember=$dbr->makeList($botconds, LIST_OR);

			/** This join, in conjunction with WHERE ug_group
			    IS NULL, returns only those rows from IMAGE
		    	where the uploading user is not a member of
		    	a group which has the 'bot' permission set.
			*/
			$ug = $dbr->tableName('user_groups');
			$hidebotsql = " LEFT OUTER JOIN $ug ON img_user=ug_user AND ($isbotmember)";
		}
	}

	$image = $dbr->tableName('adimage');

	$sql="SELECT img_timestamp from $image";
	if ($hidebotsql) {
		$sql .= "$hidebotsql WHERE ug_group IS NULL";
	}
	$sql.=' ORDER BY img_timestamp DESC LIMIT 1';
	$res = $dbr->query($sql, 'wfSpecialNewImages');
	$row = $dbr->fetchRow($res);
	if($row!==false) {
		$ts=$row[0];
	} else {
		$ts=false;
	}
	$dbr->freeResult($res);
	$sql='';

	/** If we were clever, we'd use this to cache. */
	$latestTimestamp = wfTimestamp( TS_MW, $ts);

	/** Hardcode this for now. */
	$limit = 48;

	if ( $parval = intval( $par ) ) {
		if ( $parval <= $limit && $parval > 0 ) {
			$limit = $parval;
		}
	}

	$where = array();
	$searchpar = '';
	if ( $wpIlMatch != '' ) {
		$nt = Title::newFromUrl( $wpIlMatch );
		if($nt ) {
			$m = $dbr->strencode( strtolower( $nt->getDBkey() ) );
			$m = str_replace( '%', "\\%", $m );
			$m = str_replace( '_', "\\_", $m );
			$where[] = "LCASE(img_name) LIKE '%{$m}%'";
			$searchpar = '&wpIlMatch=' . urlencode( $wpIlMatch );
		}
	}

	$invertSort = false;
	if( $until = $wgRequest->getVal( 'until' ) ) {
		$where[] = 'img_timestamp < ' . $dbr->timestamp( $until );
	}
	if( $from = $wgRequest->getVal( 'from' ) ) {
		$where[] = 'img_timestamp >= ' . $dbr->timestamp( $from );
		$invertSort = true;
	}
	require_once('extensions/buysell/model/wcConfig.php');
	$cutOffDate = date('Y-m-d', time() - (wcConfig::ADS_DAYS_SHOWN * 86400));

	$sql='SELECT img_size, i.page_id, img_name, img_user, img_user_text,'.
	     "img_description,img_timestamp FROM ".$wgDBprefix."ads a, $image i
		 ";

	if($hidebotsql) {
		$sql .= $hidebotsql;
		$where[]='ug_group IS NULL';
	}
	$where[]=" a.ad_id = i.ad_id 
								/*AND a.post_date >='$cutOffDate'*/ AND 
								(
									( a.ad_type = 'B' AND a.ad_id NOT IN (SELECT ad_id 
																			FROM escrow_escrow 
																			WHERE escrow_prefix = '$wgDBprefix' 
																					AND buyer_id = a.user_id
																					AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
																		 )
									)
									OR
									( a.ad_type = 'S' AND a.ad_id NOT IN (SELECT ad_id 
																			FROM escrow_escrow 
																			WHERE escrow_prefix = '$wgDBprefix' 
																					AND seller_id = a.user_id
																					AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
																		 )
									)
								) ";

	if(count($where)) {
		$sql.=' WHERE '.$dbr->makeList($where, LIST_AND);
	}
	$sql.=' ORDER BY img_timestamp '. ( $invertSort ? '' : ' DESC' );
	$sql.=' LIMIT '.($limit+1);
	$res = $dbr->query($sql, 'wfSpecialNewImages');

	/**
	 * We have to flip things around to get the last N after a certain date
	 */
	$images = array();
	while ( $s = $dbr->fetchObject( $res ) ) {
		if( $invertSort ) {
			array_unshift( $images, $s );
		} else {
			array_push( $images, $s );
		}
	}
	$dbr->freeResult( $res );

	$gallery = new ImageGallery();
	$firstTimestamp = null;
	$lastTimestamp = null;
	$shownImages = 0;
	foreach( $images as $s ) {
		if( ++$shownImages > $limit ) {
			# One extra just to test for whether to show a page link;
			# don't actually show it.
			break;
		}

		$name = $s->img_name;
		$img_names[] = 'src="/extensions/buysell/ad_images/'.$name.'"';
		$str_array[] = 'src="/skins/common/images/icons/fileicon.png"';
		//echo "<br>name = " . $name;
		$buysell_sql = "select page_id, page_title from  ".$wgDBprefix."page  where page_title like '%$name%'";
		$buysell_res = $dbr->query($buysell_sql);
		$buysell_s = $dbr->fetchObject( $buysell_res );
		$image_link[] = '?title='.$s->img_user_text.'&action=buysell&page_id='.$s->page_id;
// 		echo  "page id = ". $buysell_s->page_id;
	   $cur_image_link[] = '/Image:'.$name;
		$file_size[] = $s->img_size . " bytes";
		$missing[] =  'File missing';
		$ut = $s->img_user_text;

		$nt = Title::newFromText( $name, NS_IMAGE );
	//	echo "<br<br<br<br><br<br<br<br<br><br<br<br><br<br<br<br><br<br<br<br><br<br<br<br><br<br<br<br>nt = " .$nt; 
		$img = new Image( $nt );

		$user_sql = "select user_name from user where user_id = " .$s->img_user  ;
		$user_res = $dbr->query($user_sql);
		$user_s = $dbr->fetchObject( $user_res );
// 		$img->setFileName($name);
//		ECHO "path= " . $img->imagePath;
//		echo "<br><br><br><br><br><br>url = "  .$img->getURL();
// 		if (! $img->exists())
//                     {
//                         echo "unfortunate";
//                         return false;
//                     }
		//echo "<pre>"; print_r($img_names); echo "</pre>";
		$ul = $sk->makeLinkObj( Title::makeTitle( NS_USER, $ut ), $ut );
		$gallery->setShowFilename(false);
		$user_text = str_replace("_", " ",$s->img_user_text);
		$buysell_link = '<a href="/index.php?title='.$s->img_user_text.'&action=buysell&page_id='.$s->page_id.'">'.$user_text.'</a>';
		$ul = "test";
		$gallery->add( $img, "<br/>$buysell_link<br />". "<a href ="."/index.php?title=User:".$user_s->user_name.">". $user_s->user_name."</a><br />\n<i>".$wgLang->timeanddate( $s->img_timestamp, true )."</i><br />\n" );
		

		$timestamp = wfTimestamp( TS_MW, $s->img_timestamp );
		if( empty( $firstTimestamp ) ) {
			$firstTimestamp = $timestamp;
		}
		$lastTimestamp = $timestamp;
	}

	$bydate = wfMsg( 'bydate' );
	$lt = $wgLang->formatNum( min( $shownImages, $limit ) );
	if ($shownav) {
		$text = wfMsgExt( 'imagelisttext', array('parse'), $lt, $bydate );
		$wgOut->addHTML( $text . "\n" );
	}

	$sub = wfMsg( 'ilsubmit' );
	$titleObj = SpecialPage::getTitleFor( 'GalleryofNewAdFiles' );
	$action = $titleObj->escapeLocalURL( $hidebots ? '' : 'hidebots=0' );
	if ($shownav) {
		$wgOut->addHTML( "<form id=\"imagesearch\" method=\"post\" action=\"" .
		  "{$action}\">" .
		  "<input type='text' size='20' name=\"wpIlMatch\" value=\"" .
		  htmlspecialchars( $wpIlMatch ) . "\" /> " .
		  "<input type='submit' name=\"wpIlSubmit\" value=\"{$sub}\" /></form>" );
	}

	/**
	 * Paging controls...
	 */

	# If we change bot visibility, this needs to be carried along.
	if(!$hidebots) {
		$botpar='&hidebots=0';
	} else {
		$botpar='';
	}
	$now = wfTimestampNow();
	$date = $wgLang->timeanddate( $now, true );
	$dateLink = $sk->makeKnownLinkObj( $titleObj, wfMsg( 'sp-newimages-showfrom', $date ), 'from='.$now.$botpar.$searchpar );

	$botLink = $sk->makeKnownLinkObj($titleObj, wfMsg( 'showhidebots', ($hidebots ? wfMsg('show') : wfMsg('hide'))),'hidebots='.($hidebots ? '0' : '1').$searchpar);

	$prevLink = wfMsg( 'prevn', $wgLang->formatNum( $limit ) );
	if( $firstTimestamp && $firstTimestamp != $latestTimestamp ) {
		$prevLink = $sk->makeKnownLinkObj( $titleObj, $prevLink, 'from=' . $firstTimestamp . $botpar . $searchpar );
	}
	//$prevLink =  str_replace ("Newimages", "GalleryofNewAdFiles", $prevLink );
	$nextLink = wfMsg( 'nextn', $wgLang->formatNum( $limit ) );
	if( $shownImages > $limit && $lastTimestamp ) {
		$nextLink = $sk->makeKnownLinkObj( $titleObj, $nextLink, 'until=' . $lastTimestamp.$botpar.$searchpar );
	}

	//$nextLink =  str_replace ("Newimages", "GalleryofNewAdFiles", $nextLink );
	//$prevnext = '<p>' . $botLink . ' '. wfMsg( 'viewprevnext', $prevLink, $nextLink, $dateLink ) .'</p>';
	$prevnext = '<p>' . wfMsg( 'viewprevnext', $prevLink, $nextLink, $dateLink ) .'</p>';

	if ($shownav)
		$wgOut->addHTML( $prevnext );

	if( count( $images ) ) {
		//$wgOut->addHTML( $gallery->getHTML($name) );
		$temp_str = $gallery->getHTML($name);
		//$val = str_replace ($str_array, $img_names,$temp_str );
		$val = str_replace_once($str_array, $img_names,$temp_str );
		$val = str_replace($cur_image_link,$image_link ,$val );
	 	$val = str_replace_once($missing, $file_size, $val );
		$wgOut->addHTML($val);
		if ($shownav)
			$wgOut->addHTML( $prevnext );
	} else {
		$wgOut->addWikiText( wfMsg( 'noimages' ) );
	}
}



function str_replace_once($search, $replace, $subject, &$offset = 0) {
    if (is_array($search)) {
        if (is_array($replace)) {
            foreach ($search as $x => $value) $subject = str_replace_once($value, $replace[$x], $subject, $offset);
        } else {
            foreach ($search as $value) $subject = str_replace_once($value, $replace, $subject, $offset);
        }
    } else {
        if (is_array($replace)) {
            foreach ($replace as $value) $subject = str_replace_once($search, $value, $subject, $offset);
        } else {
            $pos = strpos($subject, $search, $offset);
            if ($pos !== false) {
                $offset = $pos+strlen($search);
                $subject = substr($subject, 0, $pos) . $replace . substr($subject, $offset);
            }
        }
    }
   
    return $subject;
}

?> 