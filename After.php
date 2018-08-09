<?php
session_start( );

require_once( 'RedPanda.php' );
$db = init( );

// something something admin only
if( !isset($_SESSION['admin']) || $_SESSION['admin'] == false ) die( "What are you doing here?" );

$target_dir = "./img/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $output =  "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $output =  "File is not an image.";
        $uploadOk = 0;
    }
}

// Check if file already exists
if (file_exists($target_file)) {
    $output =  "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size (we'll ignore this for now)
/*if ($_FILES["image"]["size"] > 500000) {
    $output =  "Sorry, your file is too large.";
    $uploadOk = 0;
}*/

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $output =  "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $output =  "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $output = "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
    } else {
        $output =  "Sorry, there was an error uploading your file.";
    }
}
$time = strtotime($_POST["time"]);
if( !isset($_POST["caption"] ) )
	$_POST["caption"] = NULL;
if( !isset($_POST["hover"] ) )
	$_POST["hover"] = NULL;
$id = addNewImage( $db, $target_file, $_POST["caption"], $_POST["hover"], $_POST["category"], $time, $_POST["tags"] ); //or die( "Image upload failed: " $db->error );

//$output =  $db->error;
//$output =  $_SERVER['SERVER_NAME'] . "?p=" . $db->insert_id;

header( "Location: http://" . $_SERVER['SERVER_NAME'] . ($id > 0 ? "/?p=" . $id : "/") );
die( );
?>