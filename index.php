<?php
require_once( 'RedPanda.php' );

function getImageNumber( ) {
	if( isset($_GET['p']) && is_numeric($_GET['p']) )
		return $_GET['p'];
	else
		return -1;
}

$db = init( );
$imNum = getImageNumber( );
$imageData = $imNum == -1 ? getLatestImage( $db ) : getSpecificImage( $db, $imNum );

$imageNum = getImageID( $imageData );
$imageURL = getImageURL( $imageData );
$imageCount = getNumberOfImages( $db );
$caption = getCaption( $imageData );
$hover = getHover( $imageData );
$time = getViewTime( $imageData );

$category = getCategory( $db, $imageData );
$tags = getTags( $db, $imageNum );

$assoc = search( $db, $category, $tags[mt_rand(0, sizeof($tags)-1)], true, $imageNum );

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="icon" type="image/png" href="./favicon.png" />
		<title>[Red Panda] Image Of The Day</title>
		<link rel="stylesheet" type="text/css" href="./site.css">
		
	</head>
	<body>
		<div class="banner">
			<a href="./"><h3>[ Project RedPanda ] &nbsp; Image Of The Day</h3></a>
			
			<div class="search">
				<form style="display:inline" action="./Search" method="get">
				<? echo getCategoryList( $db, false ) ?>
				<input class="tag-search-box" type="text" name="tag" />
				<input class="search-button" type="submit" value="" alt="search" />
				</form>
			</div>
			
			<a href="./Upload"><span class="admin">Admin</span></a>
		</div>
		<br />
		<div id="content">
			<center>
				<br /><br />
				<div class="iotd-container">
					<? if( $hover == NULL ) { ?>
					<img class="iotd" src="<? echo $imageURL ?>" />
					<? } else { ?>
					<img class="iotd" src="<? echo $imageURL ?>" title="<? echo $hover ?>" />
					<? } ?>
				</div>
				<br /><br />
				<div class="info">
				<? echo date( "F jS", $time ) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Category: <? echo "<a href=\"./Search?cat=" . $category . "\">" . $category . "</a>" ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Tags: <? foreach( $tags as $tag ) echo "<a href=\"./Search?tag=" . $tag . "\">" . $tag . "</a>&nbsp;" ?>
				</div>
				<br/ >
				<div class="caption">
					<? echo $caption ?>
				</div>
				<br /><br />
				
				<div class="go-container">
					<? if( $imageNum-1 < 1 ) { ?><span class="no-go left">previous</span><? } else { ?> <a href="./?p=<? echo $imageNum-1 ?> "><span class="go left">previous</span></a> <? } ?>
					<? if( $imageNum+1 > $imageCount ) { ?><span class="no-go right">next</span><? } else { ?> <a href="./?p=<? echo $imageNum+1 ?> "><span class="go right">next</span></a> <? } ?>
				</div>
				<br /><br /><br />
				
				<? if( sizeof($assoc) > 0 ) { ?>
				<div class="info">
				You may also like <br />
				<? foreach( $assoc as $a ) { if( $a[ 'ID' ] != $imageNum ) echo "<a href=\"./?p=" . $a[ 'ID' ] . "\"><div style=\"display:inline-block; padding: 0px 5px 0px 5px\" class=\"thumbnail-container\"><img class=\"thumbnail\" src=\"" . $a[ 'URI' ] . "\" /></div></a>"; } ?>
				</div>
				<br /><br /><br />
				<? } ?>
			</center>
		</div>
		<br />
		<div class="banner">
			<span class="footer">&copy; 2017+ Student Project For CS4420-001</span>
		</div>
	</body>
</html>