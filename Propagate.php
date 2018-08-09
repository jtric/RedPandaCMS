<?php
require_once( './Constants.php' );

function propagate( ) {

	$db = new mysqli( 
		DB_SERVER,
		DB_USER,
		DB_PASSWORD,
		DB_DATABASE
	);
	if( mysqli_connect_errno( ) ) die( "Propagation Failed!" );
	
	$query = (
		"INSERT INTO `T_RP_IMAGES` (`ID`, `URI`, `caption`, `hover`, `categoryID`, `dtUploaded`, `dtPublic`) VALUES
		(1, './img/zao-fox-village-japan-29.jpg', 'A fox in  Zao Village, Japan.', 'PET THE FOX', 1, 1511939320, 1511939320),
		(2, './img/1MHBKZN.jpg', 'A red panda.', '', 1, 1511949363, 1511940480),
		(3, './img/bunny-cute-0.jpg', 'It''s a cute bunny! Pet the bunny!', '', 1, 1511977132, 1511969700),
		(4, './img/7I7BfWj.png', 'Political satire regarding perpetual tensions between the US and the Democratic People''s Republic of Korea (aka North Korea).', '', 2, 1512854527, 1512846000),
		(5, './img/e2d.jpg', 'How most students probably feel about college.', 'Silly birds can''t do math!', 2, 1512930943, 1512923400),
		(6, './img/23334591.jpg', 'Many people would consider this the truth, wouldn''t they? Or perhaps enough of the xenophobic ones would rather pretend the rest of the world doesn''t exist.', '', 2, 1512931287, 1512924000),
		(7, './img/2SK3WiO.jpg', 'Superman is clearly the worst hero, even ignoring the plentiful history of his silver age self having every power under the sun (pun intended).', 'Superman? More like Stuporman!', 2, 1512931490, 1512924120),
		(8, './img/xQqDHEe.jpg', 'Professor and TA''s reaction to the project code they''ll be looking at.', 'You coded WHAT using WHAT?!', 2, 1512931796, 1512924300),
		(9, './img/ralph_helping.jpg', 'That one group member who doesn''t contribute anything but gets their name on the project anyway.', '', 2, 1512932697, 1512925200),
		(10, './img/5jebHw3.png', 'When you get carried away 3D printing Huskies.', '', 1, 1512933662, 1512926400),
		(11, './img/11qIDoy.jpg', 'Never had a dog who''d let people dress it up before....', '', 1, 1512934102, 1512926700),
		(12, './img/8c5Y0n6.png', 'Sounds about right....', '', 2, 1512940213, 1512932400);
		INSERT INTO `T_RP_TAGS` (`ID`, `tagName`) VALUES
		(4, 'political'),
		(4, 'usa'),
		(4, 'north korea'),
		(5, 'bird'),
		(8, 'edgar allan poe'),
		(6, 'yu-gi-oh'),
		(6, 'usa'),
		(10, 'huskies'),
		(10, 'puppies'),
		(11, 'shiba inu'),
		(11, 'dog'),
		(12, 'news'),
		(12, 'bbc');
	");
	
	$db->multi_query( $query );
	$db->close( );
	
	return "propagation successful";
}

echo propagate( );

?>