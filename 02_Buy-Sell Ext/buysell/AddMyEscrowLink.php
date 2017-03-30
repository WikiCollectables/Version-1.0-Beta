<?
global $wgEscrowRedirPage;

$href = $wgEscrowRedirPage;
//$href = self::makeSpecialUrl( 'Escrows' );
$personal_urls['escrows'] = array(
	'text' => wfMsg( 'myescrows' ),
	'href' => $wgEscrowRedirPage,
	'active' => ( $href == $pageurl )
);
?>
