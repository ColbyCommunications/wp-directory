<?php

namespace ColbyComms\WpDirectory;

use Carbon_Fields\{Container, Field};
use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

class DepartmentFields {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'create_container' ] );
		WP::add_action( 'carbon_fields_register_fields', [ $this, 'add_fields' ] );
	}

	/**
	 * Creates the meta container.
	 */
	public function create_container() {
		$this->container = Container::make( 'term_meta', 'Department Fields' )
			->where( 'term_taxonomy', '=', 'department' );
	}

	/**
	 * Adds the fields.
	 */
	public function add_fields() {
		$this->container->add_fields(
			[
				Field::make( 'association', 'department__department_leaders', 'Department Leader(s)' )
					->set_types(
						[
							[
								'type' => 'post',
								'post_type' => 'person',
							],
						]
					),
			]
		);
	}
}
