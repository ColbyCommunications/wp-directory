<?php
/**
 * Plugin Name: WP Directory
 * Description: Imports data from Colby's directory, saves it in the WordPress database as posts, makes it available from REST endpoints, and provides shortcodes. **This plugin does not add users to the WP user database.**
 * Author: John Watkins <john.watkins@colby.edu>
 * Version: 1.0.1
 * Text Domain: wp-directory
 *
 * @package colbycomms/wp-directory
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	include __DIR__ . '/vendor/autoload.php';
}

