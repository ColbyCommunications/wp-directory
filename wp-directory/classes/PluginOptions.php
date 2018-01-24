<?php
/**
 * Creates an options page with Carbon fields.
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory;

use Carbon_Fields\{Container, Field};
use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Sets up an options page using Carbon Fields.
 */
class PluginOptions {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'create_container' ] );
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'add_plugin_options' ] );
	}

	/**
	 * Creates the options page.
	 */
	public function create_container() {
		$this->container = Container::make( 'theme_options', 'Directory Import Options' )
			->set_page_parent( 'plugins.php' );
	}

	/**
	 * Adds the plugin options.
	 */
	public function add_plugin_options() {
		$this->container->add_fields(
			[
				Field::make( 'text', 'directory_import__departments_json_url', 'Departments JSON URL.' )
					->set_help_text( 'The URL from which to query a JSON array of departments.' ),

				Field::make( 'text', 'directory_import__department_json_url', 'Individual department JSON URL.' )
					->set_help_text( 'The URL from which to query members of an individual department. (The query replaces "{DEPT}" with a passed-in department code).' ),

				Field::make( 'text', 'directory_import__person_json_url', 'Individual person JSON URL.' )
					->set_help_text( 'The URL from which to query members of an individual person. (The query replaces "{LOGIN}" with a passed-in login name.' ),

				Field::make( 'text', 'directory_import__department_codes', 'Departments to import.' )
					->set_help_text( 'A comma-separated list of department codes to import to this site (e.g., "COMMS, ADMS").' ),
			]
		);
	}
}
