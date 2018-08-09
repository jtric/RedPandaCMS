<?php
require_once( 'RedPanda.php' );

function getCatQuery( ) {
	if( isset($_GET['cat']) )
		return $_GET['cat'] == "all" ? NULL : $_GET['cat'];
	else
		return NULL;
}

function getTagQuery( ) {
	if( isset($_GET['tag']) )
		return $_GET['tag'];
	else
		return NULL;
}

$db = init( );

$cat = getCatQuery( );
$tag = getTagQuery( );

$images = search( $db, $cat, $tag );


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
				<? foreach( $images as $image ) echo "<a href=\"./?p=" . $image[ 'ID' ] . "\"><div class=\"thumbnail-container\"><img class=\"thumbnail\" src=\"" . $image[ 'URI' ] . "\" /></div><br />" ?>

			</center>
		</div>
		<br />
		<div class="banner">
			<span class="footer">&copy; 2017+ Student Project For CS4420-001</span>
		</div>
	</body>
</html>