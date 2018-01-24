<?php
/**
 * Import.php
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory\Data;

use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Requests data and puts it in the WP database.
 */
class Import {
	/**
	 * Adds hooks.
	 */
	public function __construct( array $departments_to_import = [], DataFetcher $data_fetcher ) {
		$this->departments_to_import = $departments_to_import;
		$this->data_fetcher = $data_fetcher;
		ini_set( 'max_execution_time', 300 );
		$this->run();
	}

	private function run() {
		$this->departments = $this->data_fetcher::fetch( $this->departments_to_import );
		$this->insert_data();
	}

	private function insert_department( $department ) {
		if ( ! $department['code'] || ! $department['division'] ) {
			return;
		}

		$parent = get_term_by( 'name', $department['division'], 'department' );

		if ( ! $parent ) {
			$parent = (object) wp_insert_term( $department['division'], 'department' );
			$parent = get_term( $parent->term_id, 'department' );
		}

		$term = get_term_by( 'name', $department['code'], 'department' );

		if ( ! $term ) {
			$term = (object) wp_insert_term( $department['code'], 'department', [ 'parent' => $parent->term_id ] );
		}
	}

	private function insert_person( $person ) {
		$query = new \WP_Query(
			[
				'post_type' => 'person',
				'meta_query' => [
					[
						'key' => 'id',
						'compare' => '=',
						'value' => $person['id'],
					],
				],
			]
		);

		$departments = [ get_term_by( 'name', $person['dept'], 'department' ) ];
		$departments[] = get_term_by( 'name', $person['dept2'], 'department' );
		$departments = array_filter( $departments );
		$departments = array_map(
			function( $term ) {
				return $term->term_id;
			},
			$departments
		);

		wp_insert_post(
			[
				'ID' => $query->posts ? $query->posts[0]->ID : 0,
				'post_title' => $person['fullname'],
				'meta_input' => $person,
				'post_type' => 'person',
				'post_status' => 'publish',
				'tax_input' => [
					'department' => $departments,
				],
			]
		);
	}

	private function insert_data() {
		foreach ( $this->departments as $department ) {
			$this->insert_department( $department );
		}

		$individuals = array_reduce(
			$this->departments,
			function ( $individuals, $department ) use ( &$max ) {
				return array_merge(
					$individuals,
					$this->data_fetcher->get_individuals_in_department( $department['code'] )
				);
			},
			[]
		);

		foreach ( $individuals as $person ) {
			$this->insert_person( $person );
		}
	}
}
