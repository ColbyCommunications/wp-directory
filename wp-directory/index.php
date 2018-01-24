<?php
/**
 * Main entry point.
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory;

use ColbyComms\WpDirectory\{WpDirectoryPlugin, PluginOptions, DepartmentFields, PostTypes};
use ColbyComms\WpDirectory\Data\{Import, CXWeb};
use ColbyComms\WpDirectory\Utils\WpFunctions as WP;
use ColbyComms\WpDirectory\Shortcodes\DepartmentShortcode;

require 'pp.php';

define( __NAMESPACE__ . '\\TEXT_DOMAIN', 'wp-directory' );
define( __NAMESPACE__ . '\\VERSION', '1.0.0' );
define( __NAMESPACE__ . '\\PROD', false );

register_shutdown_function(
	function() {
		print_r( error_get_last() );
	}
);

// Boot Carbon Fields.
WP::add_action( 'after_setup_theme', [ 'Carbon_Fields\\Carbon_Fields', 'boot' ] );

WP::add_action(
	'after_setup_theme', function() {
		new PluginOptions();
	}
);

WP::add_action(
	'wp_loaded', function() {
		if ( ! isset( $_GET['import_people'] ) ) {
			return;
		}

		/**
		 * Filters which departments to import to the current site.
		 *
		 * @param array An array of department codes, or an empty array to import all.
		 */
		$departments_to_import = apply_filters( 'directory_import__departments_to_import', [] );
		new Import( $departments_to_import, new CXWeb() );
	}
);

new WpDirectoryPlugin();
new PostTypes();
new DepartmentFields();
new DepartmentShortcode();
