<?php
/**
 * MonoBook nouveau
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @addtogroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/** */
require_once('includes/SkinTemplate.php');

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @addtogroup Skins
 */
class SkinMonoBook extends SkinTemplate {
	/** Using monobook. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'monobook';
		$this->stylename = 'monobook';
		$this->template  = 'MonoBookTemplate';
	}
}

/**
 * @todo document
 * @addtogroup Skins
 */
class MonoBookTemplate extends QuickTemplate {
	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */

	#### BuySell Extension Code starts Here ####
	var $PriceRange_UserRating = "";
	var $str_buysell = "";

	public function getRating($user_id)
	{
		// all wiki rating of this user

		$dbr =& wfGetDB( DB_SLAVE );

		$sql = "SELECT buyer_rating
					FROM escrow_escrow
					WHERE buyer_id =  '$user_id'
					AND buyer_rating IS NOT NULL and buyer_rating != 0 ";
		$res = $dbr->query($sql, __METHOD__);
		
		$buyer_num = mysql_num_rows($res);
		$b_rating = 0;
		while ( $line = $dbr->fetchObject( $res ) ) 
		{
			if ($line->buyer_rating > 0)
                $b_rating += $line->buyer_rating;
		}

		$sql = "SELECT seller_rating
					FROM escrow_escrow
					WHERE seller_id =  '$user_id'
					AND seller_rating IS NOT NULL and seller_rating != 0 ";
		$res = $dbr->query($sql, __METHOD__);
		
		$seller_num = mysql_num_rows($res);
		$s_rating = 0;
		while ( $line = $dbr->fetchObject( $res ) ) 
		{
			if ($line->seller_rating > 0)
                $s_rating += $line->seller_rating;
		}

		if(($buyer_num + $seller_num) != 0)
			$avr_rating = ($b_rating + $s_rating)/($buyer_num + $seller_num);
		else
			$avr_rating = 0;
        return $avr_rating;
	}

	function User_Title($rowObj)
	{
		global $wgDBprefix;
		$this->PriceRange_UserRating = "User Rating: " . $this->getRating($rowObj->user_id);

		$query_string = "?title=User:" . $rowObj->user_name . "&amp;action=buysell&amp;user_id=" . $rowObj->user_id;
		$href = "/index.php$query_string";

		$dbr =& wfGetDB( DB_SLAVE );
		$sql = "select count(a.ad_id) as total_number from ".$wgDBprefix."ads a
					where user_id = '" . $rowObj->user_id . "'
						/*and a.post_date >='$cutOffDate'*/
						and a.ad_id NOT IN (SELECT ad_id 
												FROM escrow_escrow 
												WHERE escrow_prefix = '$wgDBprefix' 
														AND buyer_id = a.user_id
														AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
											)
						and a.ad_id NOT IN (SELECT ad_id 
												FROM escrow_escrow 
												WHERE escrow_prefix = '$wgDBprefix' 
														AND seller_id = a.user_id
														AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
											) ";
		$res = $dbr->query($sql, __METHOD__);
		$line = $dbr->fetchObject( $res ) ;

		$this->str_buysell = "<a href='$href'>".$line->total_number." Buy/Sell</a>";

	}
	function Article_Title($rowObj)
	{
		global $wgDBprefix;
		$dbr =& wfGetDB( DB_SLAVE );

		if($rowObj->page_min_price != "" && $rowObj->page_max_price != "") 
			$this->PriceRange_UserRating = "$" . number_format($rowObj->page_min_price) . " - $" . number_format($rowObj->page_max_price);
		else if ($rowObj->page_min_price != "")
			$this->PriceRange_UserRating = "$" . number_format($rowObj->page_min_price);
		else if ($rowObj->page_max_price != "")
			$this->PriceRange_UserRating = "$" . number_format($rowObj->page_max_price);
		else
			$this->PriceRange_UserRating = "";

		$sql = "select count(a.ad_id) as total_number from ".$wgDBprefix."ads a
					where page_id = '" . $rowObj->page_id . "'
						/*and a.post_date >='$cutOffDate'*/
						and a.ad_id NOT IN (SELECT ad_id 
												FROM escrow_escrow 
												WHERE escrow_prefix = '$wgDBprefix' 
														AND buyer_id = a.user_id
														AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
											)
						and a.ad_id NOT IN (SELECT ad_id 
												FROM escrow_escrow 
												WHERE escrow_prefix = '$wgDBprefix' 
														AND seller_id = a.user_id
														AND status NOT IN ('none', 'Submit Offer', 'Counter Offer') 
											) ";
		$res = $dbr->query($sql, __METHOD__);
		$line = $dbr->fetchObject( $res ) ;

		$query_string = "?title=" . $rowObj->page_title . "&amp;action=buysell&amp;page_id=" . $rowObj->page_id;
		$href = "/index.php$query_string";
		$this->str_buysell = "<a href='$href'>".$line->total_number." Buy/Sell</a>";

	} // Article_Title($rowObj)

	function getRedirectedID_v($page_id)
	{
		global $wgDBprefix;
		$dbr =& wfGetDB( DB_SLAVE );
		$sql = "SELECT pl_from, pl_title from " . $wgDBprefix . "pagelinks where pl_from = '" . $page_id . "' ";
		$res = $dbr->query($sql, __METHOD__);
		$line = $dbr->fetchObject( $res ) ;
		if($line->pl_title != "") 
		{
			//$my_page = str_replace("_", " ", $line->pl_title);
			$sql = "SELECT page_id, page_title, page_is_redirect from " . $wgDBprefix . "page where page_title = '" . trim($line->pl_title) . "' ";
			$res = $dbr->query($sql, __METHOD__);
			$line = $dbr->fetchObject( $res ) ;
			if($line->page_is_redirect == 1) 
			{
				$my_page_id = $this->getRedirectedID_v($line->page_id);
			}
			else
			{
				$my_page_id = $line->page_id;
			}
		}
		return $my_page_id;
	} // getRedirectedID_v($page_id)


	function Page_Titles()
	{
		global $wgDBprefix; // Added for BuySell Extension

		$dbr =& wfGetDB( DB_SLAVE );
		global $title;

		$url = $_SERVER[PHP_SELF];
		$arr = explode("/", $url);

		# if index.php/articlename
		$sql = "SELECT page_id, page_title, page_min_price, page_max_price, page_is_redirect from ".$wgDBprefix."page where page_title = '".$arr[count($arr) - 1]."' ";
		$res = $dbr->query($sql, __METHOD__);
		$line = $dbr->fetchObject( $res ) ;
		if($line->page_id > 0) 
		{
			if($line->page_is_redirect == 1)
			{
				$my_page_id = $this->getRedirectedID_v($line->page_id);
				$sql = "SELECT page_id, page_title, page_min_price, page_max_price from ".$wgDBprefix."page where page_id = '$my_page_id' ";
				$res = $dbr->query($sql, __METHOD__);
				$line = $dbr->fetchObject( $res ) ;
			}
			$this->Article_Title($line);
			//echo "1";
		}
		else
		{
			$page_id = $_REQUEST[page_id];
			$user_id = $_REQUEST[user_id];

			$sql = "SELECT page_id, page_title, page_min_price, page_max_price, page_is_redirect from ".$wgDBprefix."page where page_id = '" . $page_id . "' ";
			$res = $dbr->query($sql, __METHOD__);
			$line = $dbr->fetchObject( $res ) ;

			if($line->page_id > 0) 
			{
				if($line->page_is_redirect == 1)
				{
					$my_page_id = $this->getRedirectedID_v($line->page_id);
					$sql = "SELECT page_id, page_title, page_min_price, page_max_price from ".$wgDBprefix."page where page_id = '$my_page_id' ";
					$res = $dbr->query($sql, __METHOD__);
					$line = $dbr->fetchObject( $res ) ;
				}
				$this->Article_Title($line);
				//echo "2";
			}
			elseif(trim($title))
			{
				$sql = "SELECT page_id, page_title, page_min_price, page_max_price, page_is_redirect from ".$wgDBprefix."page where page_title = '" . addslashes($title) . "' ";
				$res = $dbr->query($sql, __METHOD__);
				$line = $dbr->fetchObject( $res ) ;

				if($line->page_id > 0) 
				{
					if($line->page_is_redirect == 1)
					{
						$my_page_id = $this->getRedirectedID_v($line->page_id);
						$sql = "SELECT page_id, page_title, page_min_price, page_max_price from ".$wgDBprefix."page where page_id = '$my_page_id' ";
						$res = $dbr->query($sql, __METHOD__);
						$line = $dbr->fetchObject( $res ) ;
					}
					$this->Article_Title($line);
					//echo "3";
				}
				elseif(ereg("Talk:", trim($title)) )
				{
					# if index.php?title=Talk:Main_Page&action=buysell
					$arr1 = explode(":", trim($title));

					$sql = "SELECT page_id, page_title, page_min_price, page_max_price, page_is_redirect from ".$wgDBprefix."page where lower(page_title) = '" . strtolower($arr1[count($arr1) - 1]) . "' ";
					$res = $dbr->query($sql, __METHOD__);
					$line = $dbr->fetchObject( $res ) ;

					if($line->page_id > 0) 
					{
						if($line->page_is_redirect == 1)
						{
							$my_page_id = $this->getRedirectedID_v($line->page_id);
							$sql = "SELECT page_id, page_title, page_min_price, page_max_price from ".$wgDBprefix."page where page_id = '$my_page_id' ";
							$res = $dbr->query($sql, __METHOD__);
							$line = $dbr->fetchObject( $res ) ;
						}
						$this->Article_Title($line);	
						//echo "4";
					}

				}
			}

			# if index.php/User:Test
			$arr1 = explode(":", $arr[count($arr) - 1]);
			$sql = "SELECT user_id, user_name from user where lower(user_name) = '".strtolower($arr1[count($arr1) - 1])."' ";
			$res = $dbr->query($sql, __METHOD__);
			$line = $dbr->fetchObject( $res ) ;
			if($line->user_name != "")
			{
				$this->User_Title($line);
				//echo "5";
			}
			else
			{
				$sql = "SELECT user_id, user_name from user where user_id = '" . $user_id . "' ";
				$res = $dbr->query($sql, __METHOD__);
				$line = $dbr->fetchObject( $res ) ;
				if ($line->user_name != "")
				{
					$this->User_Title($line);
					//echo "6";
				}
				//
				elseif (eregi("User_talk:", $title) || eregi("User:", $title))
				{
					// User_talk:Test
					$titlearr = explode(":", $title);
					$sql = "SELECT user_id, user_name from user where lower(user_name) = '" . strtolower($titlearr[count($titlearr) - 1]) . "' ";
					$res = $dbr->query($sql, __METHOD__);
					$line = $dbr->fetchObject( $res ) ;
					if($line->user_id > 0) 
					{
						$this->User_Title($line);
						//echo "7";
					}
				}
			}
		}
	}
	#### BuySell Extension Code ends Here ####

	function execute() {
		global $wgUser;
		global $wgDBprefix; // Added for BuySell Extension
		$skin = $wgUser->getSkin();

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php 
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php $this->html('headlinks') ?>
		<title><?php $this->text('pagetitle') ?></title>
		<style type="text/css" media="screen,projection">/*<![CDATA[*/ @import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css?<?php echo $GLOBALS['wgStyleVersion'] ?>"; /*]]>*/</style>
		<link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/common/commonPrint.css?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
		<link rel="stylesheet" type="text/css" media="handheld" href="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/handheld.css?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
		<!--[if lt IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE50Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<!--[if IE 5.5000]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE55Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<!--[if IE 6]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE60Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<!--[if IE 7]><style type="text/css">@import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/IE70Fixes.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";</style><![endif]-->
		<!--[if lt IE 7]><script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/IEFixes.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"></script>
		<meta http-equiv="imagetoolbar" content="no" /><![endif]-->
		
		<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>
                
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
<?php	if($this->data['jsvarurl'  ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl'  ) ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss'   ]) { ?>
		<style type="text/css"><?php $this->html('pagecss'   ) ?></style>
<?php	}
		if($this->data['usercss'   ]) { ?>
		<style type="text/css"><?php $this->html('usercss'   ) ?></style>
<?php	}
		if($this->data['userjs'    ]) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
		<!-- Head Scripts -->
<?php $this->html('headscripts') ?>
	</head>
<body <?php if($this->data['body_ondblclick']) { ?>ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload'    ]) { ?>onload="<?php     $this->text('body_onload')     ?>"<?php } ?>
 class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">
	<div id="globalWrapper">
		<div id="column-content">
	<div id="content">
		<a name="top" id="top"></a>
		<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
<div class="siteNotice" align="center"><br>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-4807012931176366";
/* WikiCoins 728x90, created 4/3/09 */
google_ad_slot = "1582922220";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<br>
		<h1 class="firstHeading"><?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title') ?></h1>
<?
#### BuySell Extension Code starts Here ####
$this->Page_Titles();
?>
		<h1 class="firstHeading2"><?=$this->str_buysell?>&nbsp</h1>
		<div id="bodyContent">
			<h3 id="siteSub">From <a href="http://www.wikicollectables.com">Wiki&#173;Collectables</a>, Buy &#149; Sell &#149; Collect &#149; Wiki</h3>
			<h3 id="siteSub2">View the <a href="http://wikicoins.com/Special:ArticleswiththeMostAds">top articles</a>!</h3>
<?
#### BuySell Extension Code ends Here ####
?>
			<div id="contentSub"><?php $this->html('subtitle') ?></div>
			<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
			<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
			<?php if($this->data['showjumplinks']) { ?><?php } ?>
			<!-- start content -->
			<?php $this->html('bodytext') ?>
			<?php if($this->data['catlinks']) { ?><div id="catlinks"><?php       $this->html('catlinks') ?></div><?php } ?>
			<!-- end content -->
			<div class="visualClear"></div>
		</div>
	</div>
		</div>
		<div id="column-one">
	<div id="p-cactions" class="portlet">
		<h5><?php $this->msg('views') ?></h5>
		<div class="pBody">
			<ul>
<!-- BuySell Extension Code Starts Here -->
	<?php
				foreach($this->data['content_actions'] as $key => $tab) 
				{ 
					if($_REQUEST[action] == "buysell" && strtolower($tab['text']) != "article" && strtolower($tab['text']) != "buy/sell" && strtolower($tab['text']) != "discussion" && strtolower($tab['text']) != "user page")
					{
						// As per the requirement of task#2.1: if buy/sell is seleted then do not show tabs to the right of discussion. 
						// As pe
					}
					else
					{
					?>
						 <li id="ca-<?php echo Sanitizer::escapeId($key) ?>"<?php
						if($tab['class'] && !eregi("buysell", $_REQUEST[action])) 
						{ 
						?> class="<?php echo htmlspecialchars($tab['class']) ?>"<?php 
						} ?>><a href="<?php echo htmlspecialchars($tab['href']) ?>"><?php
						 echo htmlspecialchars($tab['text']) ?></a></li>
		<?php		
						if(strtolower($tab['text']) == "article" or strtolower($tab['text']) == "user page")
						{
							global $title;
							$my_page_id = "";

							$url = $_SERVER[PHP_SELF];
							$arr = explode("/", $url);

							$dbr =& wfGetDB( DB_SLAVE );
							# if index.php/articlename
							
							$sql = "SELECT page_id, page_title from ".$wgDBprefix."page where page_title = '".$arr[count($arr) - 1]."' ";
							$res = $dbr->query($sql, __METHOD__);
							$line = $dbr->fetchObject( $res ) ;
							if($line->page_id > 0) 
							{
								$query_string = "?title=" . $line->page_title . "&amp;action=buysell&amp;page_id=" . $line->page_id;
							}
							else
							{
								$page_id = $_REQUEST[page_id];
								$user_id = $_REQUEST[user_id];
								
								$sql = "SELECT page_id, page_title from ".$wgDBprefix."page where page_id = '" . $page_id . "' ";
								$res = $dbr->query($sql, __METHOD__);
								$line = $dbr->fetchObject( $res ) ;

								if($line->page_id > 0) 
								{
									$query_string = "?title=" . $line->page_title . "&amp;action=buysell&amp;page_id=" . $line->page_id;
								}
								elseif(trim($title))
								{
									
									$sql = "SELECT page_id, page_title from ".$wgDBprefix."page where page_title = '" . addslashes($title) . "' ";
									$res = $dbr->query($sql, __METHOD__);
									$line = $dbr->fetchObject( $res ) ;

									if($line->page_id > 0) 
									{
										$query_string = "?title=" . $line->page_title . "&amp;action=buysell&amp;page_id=" . $line->page_id;
									}
									elseif(ereg(":", trim($title)) )
									{
										# if index.php?title=Talk:Main_Page&action=buysell
										$arr1 = explode(":", trim($title));

										$sql = "SELECT page_id, page_title from ".$wgDBprefix."page where lower(page_title) = '" . strtolower($arr1[count($arr1) - 1]) . "' ";
										$res = $dbr->query($sql, __METHOD__);
										$line = $dbr->fetchObject( $res ) ;

										if($line->page_id > 0) 
										{
											$query_string = "?title=" . $line->page_title . "&amp;action=buysell&amp;page_id=" . $line->page_id;
										}

									}
								}

								# if index.php/User:Test
								$arr1 = explode(":", $arr[count($arr) - 1]);
								//echo $query_string;
								$sql = "SELECT user_id, user_name from user where lower(user_name) = '".strtolower($arr1[count($arr1) - 1])."' ";
								$res = $dbr->query($sql, __METHOD__);
								$line = $dbr->fetchObject( $res ) ;
								if($line->user_name != "")
								{
									$query_string = "?title=User:" . $line->user_name . "&amp;action=buysell&amp;user_id=" . $line->user_id;
								}
								else
								{
									$sql = "SELECT user_id, user_name from user where user_id = '" . $user_id . "' ";
									$res = $dbr->query($sql, __METHOD__);
									$line = $dbr->fetchObject( $res ) ;
									if ($line->user_name != "")
									{
										$query_string = "?title=User:" . $line->user_name . "&amp;action=buysell&amp;user_id=" . $line->user_id;
									}
									//
									elseif (eregi("User_talk:", $title) || eregi("User:", $title))
									{
										// User_talk:Test
										$titlearr = explode(":", $title);
										$sql = "SELECT user_id, user_name from user where lower(user_name) = '" . strtolower($titlearr[count($titlearr) - 1]) . "' ";
										$res = $dbr->query($sql, __METHOD__);
										$line = $dbr->fetchObject( $res ) ;
										if($line->user_id > 0) 
										{
											$query_string = "?title=User:" . $line->user_name . "&amp;action=buysell&amp;user_id=" . $line->user_id;
										}
									}
								}
							}
							//echo $sql . $query_string;
							$li_id = "buysell"; // echo htmlspecialchars($key)
						?><li id="ca-<?php echo $li_id?>"<?php
							# if index.php?title=BuySell
							$action = $_REQUEST[action];

							if(eregi("buysell", $action))
							{ 
								?> class="<?php echo htmlspecialchars($tab['class']) ?>"<?php
							}
							if(trim($query_string) != "")
							{
								$href = "/index.php$query_string";
							}
							else
							{
								$href = "javascript: alert('This article must have content before the buy/sell page is available.');";
							}
							?>><a href="<?=$href?>">Buy/Sell</a></li>
						 <?php			
						}
					}							

				} ?>  
<!-- BuySell Extension Code Ends Here -->

<!-- BuySell Extension Deletion Begins Here

	<?php			foreach($this->data['content_actions'] as $key => $tab) { ?>
				 <li id="ca-<?php echo Sanitizer::escapeId($key) ?>"<?php
					 	if($tab['class']) { ?> class="<?php echo htmlspecialchars($tab['class']) ?>"<?php }
					 ?>><a href="<?php echo htmlspecialchars($tab['href']) ?>"<?php echo $skin->tooltipAndAccesskey('ca-'.$key) ?>><?php
					 echo htmlspecialchars($tab['text']) ?></a></li>
	<?php			 } ?>
	
BuySell Extension Deletion Ends Here -->
	
			</ul>
		</div>
	</div>
	<div class="portlet" id="p-personal">
		<h5><?php $this->msg('personaltools') ?></h5>
		<div class="pBody">
			<ul>
<?php 			foreach($this->data['personal_urls'] as $key => $item) { ?>
				<li id="pt-<?php echo Sanitizer::escapeId($key) ?>"<?php
					if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
				echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskey('pt-'.$key) ?><?php
				if(!empty($item['class'])) { ?> class="<?php
				echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
				echo htmlspecialchars($item['text']) ?></a></li>
<?php			} ?>
			</ul>
		</div>
	</div>
	<div class="portlet" id="p-logo">
		<a style="background-image: url(<?php $this->text('logopath') ?>);" <?php
			?>href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"<?php
			echo $skin->tooltipAndAccesskey('n-mainpage') ?>></a>
	</div>
	<script type="<?php $this->text('jsmimetype') ?>"> if (window.isMSIE55) fixalpha(); </script>
	<?php foreach ($this->data['sidebar'] as $bar => $cont) { ?>
	<div class='portlet' id='p-<?php echo Sanitizer::escapeId($bar) ?>'<?php echo $skin->tooltip('p-'.$bar) ?>>
		<h5><?php $out = wfMsg( $bar ); if (wfEmptyMsg($bar, $out)) echo $bar; else echo $out; ?></h5>
		<div class='pBody'>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php
					if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $skin->tooltipAndAccesskey($val['id']) ?>><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php			} ?>
			</ul>
		</div>
	</div>
	<?php } ?>
	<div id="p-search" class="portlet">
		<h5><label for="searchInput"><?php $this->msg('search') ?></label></h5>
		<div id="searchBody" class="pBody">
			<form action="<?php $this->text('searchaction') ?>" id="searchform"><div>
				<input id="searchInput" name="search" type="text"<?php echo $skin->tooltipAndAccesskey('search');
					if( isset( $this->data['search'] ) ) {
						?> value="<?php $this->text('search') ?>"<?php } ?> />
				<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('searcharticle') ?>" />&nbsp;
				<input type='submit' name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>" />
			</div></form>
		</div>
	</div>
<!-- BuySell Extension Code Starts Here -->
	<?php
		// Added by Vikas for Task 2 - Component 1 (Buy/Sell Task)
		if (trim($_REQUEST[action]) != "buysell")
		{
	?>
<!-- BuySell Extension Code Ends Here -->
	<div class="portlet" id="p-tb">
		<h5><?php $this->msg('toolbox') ?></h5>
		<div class="pBody">
			<ul>
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo Sanitizer::escapeId($key) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"<?php echo $skin->tooltipAndAccesskey('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $skin->tooltipAndAccesskey('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
?>
			</ul>
		</div>
	</div>
<?php if($this->data['notspecialpage'] && $this->data['copyright']) {
global $wgUser;
if($wgUser->mName != "INSERT MEDIAWIKI USERNAME HERE") {?>
<!-- Insert Ad LEAVE THIS MESSAGE IN: PHP generation code taken from
http://wiki.edsimpson.co.uk/ under the Creative Commons
Attribution-ShareAlike 3.0 License -->
        <div class="portlet">
        <h5>sponsors</h5>
        <div class="pBody" align="right">
        <div class="advert" align="right">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-4807012931176366";
/* WikiCoins 125X125 01 */
google_ad_slot = "8516311313";
google_ad_width = 125;
google_ad_height = 125;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
        </div>
        <div class="advert" align="right">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-4807012931176366";
/* WikiCoins 125x125 Sponsor 02 */
google_ad_slot = "8881365957";
google_ad_width = 125;
google_ad_height = 125;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
        </div>
        </div></div>
<!-- End Ad -->
<?} } ?>
<?php
		} // Added for BuySell Extension
		if( $this->data['language_urls'] ) { ?>
	<div id="p-lang" class="portlet">
		<h5><?php $this->msg('otherlanguages') ?></h5>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
		</div>
	</div>
<?php	} ?>
		</div><!-- end of the left (by default at least) column -->
			<div class="visualClear"></div>
			<div id="footer">
<?php
		if($this->data['poweredbyico']) { ?>
				<div id="f-poweredbyico"><?php $this->html('poweredbyico') ?></div>
<?php 	}
		if($this->data['copyrightico']) { ?>
				<div id="f-copyrightico"><?php $this->html('copyrightico') ?></div>
<?php	}

		// Generate additional footer links
?>
			<ul id="f-list">
<?php
		$footerlinks = array(
			'lastmod', 'viewcount', 'numberofwatchingusers', 'credits', 'copyright',
			'privacy', 'about', 'disclaimer', 'tagline',
		);
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>				<li id="<?php echo$aLink?>"><?php $this->html($aLink) ?></li>
<?php 		}
		}
?>
			</ul>
		</div>

			<div id="footer2">
<a href="http://www.wikicoins.com/WikiCoins:Interwiki_Links"><b>Interwiki Links</b></a>: <a href="http://www.wikicoins.com/Main_Page">WikiCoins</a>  &#149; <a href="http://www.wikistamps.com/Main_Page">WikiStamps</a>  &#149; <a href="http://www.wikicomics.com/Main_Page">WikiComics</a>  &#149; <a href="http://www.wikitradingcards.com/Main_Page">WikiTradingcards</a>  &#149; <a href="http://www.wikifirsteditions.com/Main_Page">WikiFirstEditions</a>  &#149; <a href="http://www.wikibotanicals.com/Main_Page">WikiBotanicals</a>  &#149; <a href="http://www.wikitoys.com/Main_Page">WikiToys</a>  &#149; <a href="http://www.wikisportsmemorabilia.com/Main_Page">WikiSports</a>  &#149; <a href="http://www.wikimoviesmemorabilia.com/Main_Page">WikiMovies</a>  &#149; <a href="http://www.wikimusicmemorabilia.com/Main_Page">WikiMusic</a> &#149; <a href="http://www.wikipedia.org/wiki/Main_Page">Wikipedia</a>
		</div>

	<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
</div>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>

-->
<?php endif; ?>
<!-- Start Quantcast tag -->
<script type="text/javascript">
_qoptions={
qacct:"p-6axhJNRBIyWBo"
};
</script>
<script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
<noscript>
<img src="http://pixel.quantserve.com/pixel/p-6axhJNRBIyWBo.gif" style="display: none;" border="0" height="1" width="1" alt="Quantcast"/>
</noscript>
<!-- End Quantcast tag -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-4993850-2");
pageTracker._trackPageview();
} catch(err) {}</script>
</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method
} // end of class
?>
