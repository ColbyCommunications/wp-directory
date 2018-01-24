<?php
/**
 * PostTypes.php
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory;

use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Registers post types.
 */
class PostTypes {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		WP::add_action( 'init', [ __CLASS__, 'register_person' ] );
		WP::add_action( 'init', [ __CLASS__, 'register_department_taxonomy' ] );
		WP::add_action( 'init', [ __CLASS__, 'register_meta_fields' ] );
	}

	public static function register_department_taxonomy() {
		WP::register_taxonomy(
			'department',
			'person',
			[
				'labels' => [
					'name' => 'Departments',
					'singular_name' => 'Department',
				],
				'public' => true,
				'publicly_queryable' => false,
				'hierarchical' => true,
				'show_in_rest' => true,
				'taxonomies' => [ 'person' ],
			]
		);

		WP::register_taxonomy_for_object_type( 'department', 'person' );
	}

	public static function register_person() {
		WP::register_post_type(
			'person',
			[
				'labels' => [
					'name' => 'People',
					'singular_name' => 'Person',
				],
				'public' => true,
				'publicly_queryable' => false,
				'supports' => [ 'title', 'custom-fields' ],
				'show_in_rest' => true,
				'rest_controller_class' => 'ColbyComms\\WpDirectory\\RESTPersonController',
			]
		);
	}

	public static function register_meta_fields() {
		$fields = [
			'altname',
			'box',
			'dept',
			'deptfax',
			'depttel',
			'dirurl',
			'email',
			'firstname',
			'fullname',
			'fullphoto',
			'id',
			'lastname',
			'login',
			'middlename',
			'mydept',
			'nickname',
			'officehours',
			'phone',
			'photo',
			'pos',
			'room',
			'showme',
			'suffixname',
			'title',
		];

		for ( $i = 1; $i <= 9; $i++ ) {
			$fields[] = "expertise$i";
		}

		foreach ( $fields as $field ) {
			register_meta(
				'person',
				$field,
				[
					'type' => 'string',
					'single' => true,
					'show_in_rest' => true,
				]
			);
		}
	}
}
