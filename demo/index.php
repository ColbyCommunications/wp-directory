<?php

require dirname( __DIR__ ) . '/vendor/autoload.php';

use ColbyComms\WpDirectory\Shortcodes\DepartmentShortcode;

register_shutdown_function( function() {
	print_r( error_get_last() );
} );

$data = [];
$data['name'] = 'Jane Faculty';
$data['title'] = 'Associate Professor of Snow';
$data['phone'] = '5555';
$data['bio'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at porta augue. Mauris condimentum efficitur neque sit amet aliquam. Vivamus a consequat eros. Cras sed pellentesque elit. Aliquam tempor augue at ullamcorper tincidunt. Duis quis turpis mi. Phasellus sed odio eu ante rhoncus sagittis ut a nulla. Nam sit amet rhoncus nibh.';
$data['email'] = 'jane.faculty';
$data['photo'] = 'https://upload.wikimedia.org/wikipedia/commons/4/44/Abraham_Lincoln_head_on_shoulders_photo_portrait.jpg';

?><!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, user-scalable=0" />
<link rel="stylesheet" href="../dist/wp-directory.css" />
<title>
	WP Directory
</title>
<style>
main {
	font-family: sans-serif;
	max-width: 992px;
	margin: 0 auto;
	padding: 3rem 0;
}
</style>
<main>

<?php echo DepartmentShortcode::render_person( $data ); ?>

</main>
<script src="../dist/wp-directory.js"></script>