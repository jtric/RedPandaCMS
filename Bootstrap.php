<?php
require_once( './Constants.php' );

function createTable( mysqli $db, $query ) {
	if( $sql = $db->prepare( $query ) ) {
		$sql->execute( );
		
		$result = $sql->get_result( );
		return !is_null( $result );
	}
	return false;
}

function createUser( mysqli $db, $user, $pass ) {
	// Perhaps arguably pointless to store usernames in a hashed state but... eh, why not?
	$userhash = hash( "sha256", $user );
	$salt = bin2hex( openssl_random_pseudo_bytes( 8, $cstrong ) );
	// 50-50 security feature and precluding old/crappy hardware
	if( $cstrong == false )
		return false;
	
	$passhash = hash( "sha256", ($salt . $pass) );
	
	$query = "INSERT INTO " . USERS . "(user, salt, pass) VALUES( ?, ?, ? );";
	
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "sss", $userhash, $salt, $passhash );
		return $sql->execute( );
	}
	return false;
}

function addDefaultCategories( mysqli $db ) {
	$query = '
		INSERT INTO ' . CATEGORIES . '(categoryID, categoryName) VALUES( 1, "animals" );
		INSERT INTO ' . CATEGORIES . '(categoryID, categoryName) VALUES( 2, "cartoons" );
		INSERT INTO ' . CATEGORIES . '(categoryID, categoryName) VALUES( 3, "nature" );
		INSERT INTO ' . CATEGORIES . '(categoryID, categoryName) VALUES( 4, "space" );
	';
	
	return $db->multi_query( $query );
}

function bootstrap( ) {

	$db = new mysqli( 
		DB_SERVER,
		DB_USER,
		DB_PASSWORD,
		DB_DATABASE
	);
	if( mysqli_connect_errno( ) ) die( "Installation Initialization Failed!" );
	
	$userTable = 
		"CREATE TABLE " . USERS . " (
		user VARCHAR(255) NOT NULL PRIMARY KEY COLLATE utf8_unicode_ci,
		salt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci,
		pass VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci
		);"
	;

	$imagesTable = 
		"CREATE TABLE " . IMAGES . " (
		ID MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
		URI VARCHAR(255),
		caption TEXT,
		hover VARCHAR(255),
		categoryID MEDIUMINT UNSIGNED,
		dtUploaded BIGINT UNSIGNED,
		dtPublic BIGINT UNSIGNED,
		FOREIGN KEY (categoryID) REFERENCES " . CATEGORIES . "(categoryID)
		);"
	;
	
	$categoriesTable = 
		"CREATE TABLE " . CATEGORIES . " (
		categoryID MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY,
		categoryName VARCHAR(255)
		);"
	;
	
	$tagsTable = 
		"CREATE TABLE " . TAGS . " (
		ID MEDIUMINT UNSIGNED,
		tagName VARCHAR(255),
		FOREIGN KEY (ID) REFERENCES " . IMAGES . "(ID)
		);"
	;
	
	$assocTable = 
		"CREATE TABLE " . ASSOC . " (
		ID MEDIUMINT UNSIGNED,
		assocID MEDIUMINT UNSIGNED,
		FOREIGN KEY (ID) REFERENCES " . IMAGES . "(ID),
		FOREIGN KEY (assocID) REFERENCES " . IMAGES . "(ID)
		);"
	;

	
	createTable( $db, $userTable ) or die( "Error creating users table: " . $db->error );
	createUser( $db, DEFAULT_ADMIN_NAME, DEFAULT_ADMIN_PASS ) or die( "Error creating admin account: " . $db->error );
	
	createTable( $db, $categoriesTable ) or die( "Error creating categories table: " . $db->error );
	
	createTable( $db, $imagesTable ) or die( "Error creating images table: " . $db->error );
	createTable( $db, $tagsTable ) or die( "Error creating tags table: " . $db->error );
	createTable( $db, $assocTable ) or die( "Error creating associations table: " . $db->error );
	
	addDefaultCategories( $db ) or die( "Error creating default categories: " . $db->error );
		
	return "Tables successfully created!";
	
	$db->close( );
}

echo bootstrap( );

?>