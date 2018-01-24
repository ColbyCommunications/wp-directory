<?php
/**
 * A shortcode for showing members of a department or a set of departments.
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory\Shortcodes;

use Carbon_Fields\Helper\Helper as Carbon;
use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Shortcode [department].
 */
class DepartmentShortcode {
	/**
	 * The shortcode tag to be added.
	 *
	 * @var string
	 */
	public static $shortcode_tag = 'department';

	/**
	 * Default shortcode attributes.
	 *
	 * @var string
	 */
	public static $shortcode_defaults = [
		'name' => '',
	];

	/**
	 * Hooks.
	 */
	public function __construct() {
		WP::add_action( 'init', [ __CLASS__, 'add_shortcode' ] );
	}

	/**
	 * Hooks the shortcode tag to its callback.
	 */
	public static function add_shortcode() {
		WP::add_shortcode( self::$shortcode_tag, [ __CLASS__, 'render_shortcode' ] );
	}

	/**
	 * Gets term objects from a comma-separated list of term names.
	 *
	 * @param $string $atts_name The string.
	 * @return array An array of term objects.
	 */
	public static function get_terms( string $atts_name ) : array {
		$term_names = array_map( 'trim', explode( ',', $atts_name ) );

		$terms = array_map(
			function( $term_name ) {
				return WP::get_term_by( 'name', $term_name, 'department' );
			},
			$term_names
		);

		return array_filter( $terms ?: [] );
	}

	/**
	 * Creates a \WP_Query instance for the shortcode.
	 *
	 * @param array $terms Terms to include in the tax query.
	 * @return \WP_Query A \WP_Query instance.
	 */
	public static function get_people_query( array $terms = [] ) : \WP_Query {
		return new \WP_Query(
			[
				'post_type' => 'person',
				'posts_per_page' => -1,
				'order' => 'asc',
				'orderby' => 'meta_value',
				'meta_key' => 'lastname',
				'tax_query' => [
					[
						'taxonomy' => 'department',
						'terms' => array_map(
							function( $term ) {
								return $term->term_id;
							},
							$terms
						),
					],
				],
			]
		);
	}

	/**
	 * Creates an array of leaders set as term meta through the admin UI.
	 *
	 * @param array $terms An array of term objects.
	 * @return array An array of post objects.
	 */
	public static function get_department_leaders( array $terms ) : array {
		return array_reduce(
			$terms,
			function( $leaders, $term ) {
				return array_merge(
					$leaders,
					array_map(
						function( $person ) {
							$post = WP::get_post( $person['id'] );
							$post->leader = true;
							return $post;
						},
						Carbon::get_term_meta( $term->term_id, 'department__department_leaders' ) ?: []
					)
				);
			},
			[]
		);
	}

	/**
	 * Makes an array of post IDs from an array of \WP_Post objects.
	 *
	 * @param array $posts WP_Post objects.
	 * @return array Post IDs.
	 */
	public static function get_ids_from_posts( array $posts = [] ) : array {
		return array_map(
			function( $post ) {
				return $post->ID;
			},
			$posts
		);
	}

	/**
	 * Filters leaders out of WP_Query results so they can be re-added at the top.
	 *
	 * @param array $posts WP_Query->posts.
	 * @param array $department_leader_ids The ids of the leader WP_Post objects.
	 * @return array The filtered array.
	 */
	public static function remove_leaders_from_query_results(
		array $posts = [],
		array $department_leader_ids = []
		) : array {
		return array_filter(
			$posts,
			function( $post ) use ( $department_leader_ids ) {
				return ! in_array( $post->ID, $department_leader_ids, true );
			}
		);
	}

	/**
	 * Gets people in the departments being shown.
	 *
	 * @param array $terms The terms to query.
	 * @param array $department_leaders Department leader post objects.
	 * @return array The resulting posts.
	 */
	public static function get_people( array $terms, array $department_leaders = [] ) : array {
		$query = self::get_people_query( $terms );

		$department_leader_ids = self::get_ids_from_posts( $department_leaders );
		$posts = self::remove_leaders_from_query_results( $query->posts, $department_leader_ids );

		return array_merge( $department_leaders, $posts );
	}

	/**
	 * Builds a name string from meta fields.
	 *
	 * @param array $meta_fields The meta fields array from the directory database.
	 * @return string The resulting name string.
	 */
	public static function get_name_from_meta_fields( array $meta_fields ) : string {
		if ( empty( $meta_fields['altname'] ) ) {
			$name = $meta_fields['firstname'];

			if ( strlen( $meta_fields['middlename'] ) ) {
				$name .= " {$meta_fields['middlename']}";
			}

			$name .= " {$meta_fields['lastname']}";

			if ( strlen( $meta_fields['suffixname'] ) ) {
				$name .= ", {$meta_fields['suffixname']}";
			}

			if ( strlen( $meta_fields['nickname'] )
					&& $meta_fields['nickname'] !== $meta_fields['firstname'] ) {
				$name .= " ({$meta_fields['nickname']} {$meta_fields['lastname']})";
			}
		} else {
			$name_parts = explode( ',', $meta_fields['altname'] );

			if ( count( $name_parts ) === 2 ) {
				$name = "{$name_parts[1]} {$name_parts[0]}";
			} else {
				$name = $meta_fields['altname'];
			}
		}

		return $name;
	}

	/**
	 * Pulls the relevant data fields out of the WP_Post object and its meta.
	 *
	 * @param \WP_Post $post A WP_Post instance.
	 * @return array The data.
	 */
	public static function get_data_from_post( \WP_Post $post ) : array {
		$data = [];

		$meta_fields = array_map(
			function( $field ) {
				return trim( $field[0] );
			},
			get_post_meta( $post->ID )
		);

		$data['leader'] = $post->leader ?? false;
		$data['name'] = self::get_name_from_meta_fields( $meta_fields );
		$data['title'] = $meta_fields['title'];
		$data['phone'] = $meta_fields['phone'];
		$data['bio'] = $meta_fields['profbio'] ?? '';
		$data['email'] = $meta_fields['email'];
		$data['photo'] = $meta_fields['fullphoto'];
		$data['box'] = $meta_fields['box'];

		return $data;
	}

	/**
	 * Renders the HTML for an individual person.
	 *
	 * @param array $person An array of data to use in the template.
	 * @return string The HTML output.
	 */
	public static function render_person( array $person ) : string {
		return PersonShortcode::render_shortcode( $person, $person['bio'] );
	}

	/**
	 * Renders a department.
	 *
	 * @param array $people An array of data arrays.
	 * @return string HTML output.
	 */
	public static function render_people( array $people ) : string {
		ob_start();
		?>
<article class="department">
		<?php
		echo implode(
			'',
			array_map( [ __CLASS__, 'render_person' ], $people )
		);
		?>
</article>
		<?php

		return ob_get_clean();
	}

	/**
	 * The shortcode callback.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string HTML output or an empty string.
	 */
	public static function render_shortcode( array $atts = [], string $content = '' ) : string {
		$atts = WP::shortcode_atts( self::$shortcode_defaults, $atts );

		if ( empty( $atts['name'] ) ) {
			return '';
		}

		$terms = self::get_terms( $atts['name'] );

		if ( empty( $terms ) ) {
			return '';
		}

		$department_leaders = self::get_department_leaders( $terms );
		$people = array_map(
			[ __CLASS__, 'get_data_from_post' ],
			self::get_people( $terms, $department_leaders )
		);

		return self::render_people( $people );
	}
}
