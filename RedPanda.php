<?php
/**
 *	"Red Panda"
 *	IotD Software Prototype
 *
 *	Please do not use, modify, redistribute, or display as an example of a bad project.
 *	When the semester has ended, this never existed ;-)
*/

require_once( 'Constants.php' );

function init( ) {
	$db = new mysqli( 
		DB_SERVER,
		DB_USER,
		DB_PASSWORD,
		DB_DATABASE
	);
	if( mysqli_connect_errno( ) )
		$db = NULL;
	return $db;
}

function is_valid_user( mysqli $db, $user, $pass ) {
	$userhash = hash( "sha256", $user );
	
	$query = "SELECT * FROM " . USERS . " WHERE user=?;";
	
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "s", $userhash );
		$sql->execute( );
		
		$result = $sql->get_result( );
		if( $result->num_rows <= 0 )
			return false;
		
		$credentials = $result->fetch_assoc( );
		if( $credentials['salt'] == NULL )
			return false;
			
		$passhash = hash( "sha256", ($credentials['salt'] . $pass) );
		
		return strcmp( $passhash, $credentials['pass'] ) == 0;
	}
	return false;
}

function getCategoryList( mysqli $db, $useID = true ) {
	if( $db == NULL ) return NULL;
	
	$query = (
		"SELECT * FROM " . CATEGORIES . " ORDER BY `categoryName` ASC" 
	);
	if( $sql = $db->prepare( $query ) ) {
		$sql->execute( );
		
		$results = $sql->get_result( );
		
		$dropdown = '<select name="' . ($useID ? "category" : "cat") . '">';
		if( !$useID ) {
			$dropdown .= "<option value=\"all\">all</option>";
		}
		while( ($cat = $results->fetch_assoc( )) != NULL ) {
			$dropdown .= '<option value="' . ($useID ? $cat['categoryID'] : $cat['categoryName']) . '">' . $cat['categoryName'] . '</option>';
		}
		$dropdown .= "</select>";
		
		return $dropdown;
	}
	return "";
}

function addNewImage( mysqli $db, $URI, $caption, $hover, $categoryID, $dtPublic, $tags ) {
	if( $db == NULL ) return 0;
	
	$currTime = time( );
	
	$query = (
		"INSERT INTO " . IMAGES . "(`URI`, `caption`, `hover`, `categoryID`, `dtUploaded`, `dtPublic`) VALUES (?, ?, ?, ?, ?, ?)"
	);
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "sssddd", $URI, $caption, $hover, $categoryID, $currTime, $dtPublic );
		if( $sql->execute( ) ) {
			$id = $db->insert_id;
			if( addTagsForImage( $db, $id, explode(",", $tags) ) )
				return $id;
			else
				return 0;
		}
		return 0;
	}
	return 0;

}

function addTagsForImage( mysqli $db, $id, array $tags ) {
	if( $db == NULL ) return NULL;
	
	$query = '';
	
	foreach( $tags as $tag) {
		if( $tag != NULL && $tag !== "" )
			$query.= "INSERT INTO " . TAGS . "(ID, tagName) VALUES( " . $id . ", '" . $tag . "' );";
	}
	
	return $db->multi_query( $query );
}

function getLatestImage( mysqli $db ) {
	if( $db == NULL ) return NULL;
	
	$currTime = time( );
	
	$query = (
		"SELECT * FROM " . IMAGES . " WHERE `dtPublic` <= ? ORDER BY `ID` DESC LIMIT 1" 
	);
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "d", $currTime );
		$sql->execute( );
		
		return $sql->get_result( )->fetch_assoc( );
	}
	return NULL;
}

function getSpecificImage( mysqli $db, $id ) {
	if( $db == NULL ) return NULL;
	
	$currTime = time( );
	
	$query = (
		"SELECT * FROM " . IMAGES . " WHERE `ID` = ? AND `dtPublic` <= ?"
	);
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "dd", $id, $currTime );
		$sql->execute( );
		$res = $sql->get_result( )->fetch_assoc( );
		return $res != NULL ? $res : getLatestImage( $db );
	}
	return getLatestImage( $db );
}

function search( mysqli $db, $category, $tag, $isRandom = false ) {
	if( $db == NULL ) return array( 5 );
	
	$currTime = time( );
	
	// Best case, search by both
	if( $category !== NULL && $tag !== NULL ) {
		$query = (
			"SELECT DISTINCT ID, URI FROM " . IMAGES . "
			 WHERE  `categoryID` in (SELECT categoryID FROM " . CATEGORIES ." WHERE `categoryName` LIKE ?)"
		);
		if( $isRandom ) {
			$query .= " OR `ID` in (SELECT ID FROM " . TAGS . " WHERE `tagName` LIKE ?) AND `dtPublic` <= ? ORDER BY RAND() LIMIT 3";
		}
		else {
			$query .= " AND `ID` in (SELECT ID FROM " . TAGS . " WHERE `tagName` LIKE ?) AND `dtPublic` <= ?";
		}
		if( $sql = $db->prepare( $query ) ) {
			$sql->bind_param( "ssd", $category, $tag, $currTime );
			$sql->execute( );
			$results = $sql->get_result( );
			
			
			$arr = array( );
			while( ($res = $results->fetch_assoc( )) != NULL ) {
				$arr[] = $res;
			}
			return $arr;
		}
		return array( );
	}
	// Tag-only search
	else if( $category == NULL && $tag !== NULL ) {
		$query = (
			"SELECT ID, URI FROM " . IMAGES . "
			 WHERE  `ID` IN (SELECT ID FROM " . TAGS . " WHERE `tagName` LIKE ?)
			 AND `dtPublic` <= ?"
		);
		if( $isRandom ) {
			$query .= "  ORDER BY RAND() LIMIT 3";
		}
		if( $sql = $db->prepare( $query ) ) {
			$sql->bind_param( "sd", $tag, $currTime );
			$sql->execute( );
			$results = $sql->get_result( );
			
			$arr = array( );
			while( ($res = $results->fetch_assoc( )) != NULL ) {
				$arr[] = $res;
			}
			return $arr;
		}
		return array( );	
	}
	// Category-only search
	else if( $category != NULL && $tag == NULL ) {
		$query = (
			"SELECT ID, URI FROM " . IMAGES . " WHERE  `categoryID` in (SELECT categoryID FROM " . CATEGORIES ." WHERE `categoryName` LIKE ?) AND `dtPublic` <= ?"
		);
		if( $isRandom ) {
			$query .= "  ORDER BY RAND() LIMIT 3";
		}
		if( $sql = $db->prepare( $query ) ) {
			$sql->bind_param( "sd", $category, $currTime );
			$sql->execute( );
			$results = $sql->get_result( );
			
			$arr = array( );
			while( ($res = $results->fetch_assoc( )) != NULL ) {
				$arr[] = $res;
			}
			return $arr;
		}
		return array( );
	}
	// A search with no constraints should return everything. In this case, however, NO SOUP FOR YOU, JERRY
	else {
		return array( );
	}

}

function getImageID( array $imageData ) {
	return $imageData == NULL ?
		0 :
		$imageData[ 'ID' ];
}

function getImageURL( array $imageData ) {
	return $imageData == NULL ?
		"" :
		$imageData[ 'URI' ];
}

function getCaption( array $imageData ) {
	return $imageData == NULL ?
		"" :
		$imageData[ 'caption' ];
}

function getHover( array $imageData ) {
	return $imageData == NULL ?
		NULL :
		$imageData[ 'hover' ];
}

function getViewTime( array $imageData ) {
	return $imageData == NULL ?
		time( ) :
		$imageData[ 'dtPublic' ];
}

function getCategory( mysqli $db, $imageData ) {
	if( $db == NULL || $imageData == NULL )
		return "";
	
	$cid = $imageData[ 'categoryID' ];
	
	$query = "SELECT categoryName FROM " . CATEGORIES . " WHERE `categoryID` = ?";
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "d", $cid );
		$sql->execute( );
		
		return $sql->get_result( )->fetch_assoc( )[ 'categoryName' ];
	}
	return "";

}

function getTags( mysqli $db, $imageID ) {
	if( $db == NULL )
		return array( );
	
	$cid = $imageData[ 'categoryID' ];
	
	$query = "SELECT tagName FROM " . TAGS . " WHERE `ID` = ?";
	if( $sql = $db->prepare( $query ) ) {
		$sql->bind_param( "d", $imageID );
		$sql->execute( );
		$results = $sql->get_result( );
		
		$tags = array( );
		while( ($tag = $results->fetch_assoc( )[ 'tagName' ]) != NULL ) {
			$tags[] = $tag;
		}
		return $tags;
	}
	return array( );
}

function getNumberOfImages( mysqli $db ) {
	if( $db == NULL )
		return 0;
		
	$query = "SELECT * FROM " . IMAGES;
	if( $sql = $db->prepare( $query ) ) {
		$sql->execute( );
		
		$result = $sql->get_result( );
		return $result->num_rows;
	}
	return 0;	
}

?>