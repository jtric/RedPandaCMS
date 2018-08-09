<?php
session_start( );

require_once( 'RedPanda.php' );
$db = init( );

// If kill is set, we kill the session. The value is irrelevant. It's mostly just a flag.
// Kill takes priority so we only bother session checking if there's no kill flag
if( isset($_POST['kill']) )
	$_SESSION['admin'] = false;
	
// Otherwise, check if a password was thrown in and we're not already admin
else if( $_SESSION['admin'] == false && isset($_POST['user']) && isset($_POST['pass']) )
	$_SESSION['admin'] = is_valid_user( $db, $_POST['user'], $_POST['pass'] );
	
$admin = isset( $_SESSION['admin'] ) && ($_SESSION['admin'] == true);


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
		<div id="upload-form">
				<?php if( $admin ) { ?>
				
				<form action="./After.php" method="post" enctype="multipart/form-data">
					Select image to upload: <input class="button" type="file" accept=".jpg,.jpeg,.png,.gif", name="image" id ="image">
					<br /><br />
					Post Time: <input type="datetime-local" name="time"><br />
					<br />
					Caption:<br />
					<textarea rows="20" cols="100" name="caption"></textarea>
					<br />
					Hover Text: <input style="width:250px;" type="text" value="" name="hover" />
					
					<br /><br /><br />
					
					Select a category for this image. Optionally, specify keyword "tags" that define this picture further.<br />
					Separate tags with commas. There is no need to encapsulate with quotes.
					<br /><br />
					Category: <? echo getCategoryList( $db ) ?>
					&nbsp;&nbsp;&nbsp;
					Tags: <input type="text" value="" name="tags" />
					
					<input class="button" type="submit" value="Upload" name="submit">
				</form>
				<br /><br /><br />
				<form name='logout' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
					<input class="button" type='submit' value='Log Out' name='kill' />
				</form>
				
				<?php } else { ?>
				<br /><br />
				<center>
				<form name='login' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
					<label for='user'></label><input type='text' value="username" name='user' />
					<label for='pass'></label><input type='password' value="password" name='pass' />
					<input class="button" type='submit' value='Log In' />
				</form>
				<center>
				<br /><br />
				<?php } ?>
		</div>
		<br />
		<div class="banner">
			<span class="footer">&copy; 2017+ Student Project For CS4420-001</span>
		</div>
	</body>
</html>