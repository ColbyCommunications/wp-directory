<?php
/**
 * CXWeb.php
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory\Data;

use Carbon_Fields\Helper\Helper as Carbon;
use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Fetches data from cxweb services. URLs are entered through plugin options.
 */
class CXWeb implements DataFetcher {
	/**
	 * Gets an instance of this class, runs it, and returns the results.
	 *
	 * @param array $department_codes An array of department codes.
	 * @return array The departments.
	 */
	public static function fetch( array $department_codes = [] ) : array {
		$data_fetcher = new CXWeb( $department_codes );
		return $data_fetcher->get_departments();
	}

	/**
	 * Gets options.
	 *
	 * @param array $department_codes The passed-in department codes array.
	 */
	public function __construct( array $department_codes = [] ) {
		$this->department_codes = $department_codes
			?: explode( ',', Carbon::get_theme_option( 'directory_import__department_codes' ) );
		$this->fetch_url = Carbon::get_theme_option( 'directory_import__departments_json_url' );
		$this->fetch_department_url = Carbon::get_theme_option( 'directory_import__department_json_url' );
		$this->fetch_individual_url = Carbon::get_theme_option( 'directory_import__person_json_url' );
	}

	/**
	 * Only use departments that were passed in or set via the plugin options.
	 *
	 * @param array $data An array of departments potentially to query.
	 * @return array The filtered array.
	 */
	public function filter_departments( array $data = [] ) : array {
		return count( $this->department_codes )
			? array_filter(
				$data,
				function( $department ) {
					return in_array( $department['code'], $this->department_codes, true );
				}
			)
			: $data;
	}

	/**
	 * Get department data from the web service.
	 *
	 * @return array JSON-decoded data.
	 */
	public function get_departments() : array {
		$response = WP::wp_remote_get( $this->fetch_url );

		if ( WP::is_wp_error( $response ) ) {
			return [];
		}

		$data = WP::wp_remote_retrieve_body( $response );

		if ( WP::is_wp_error( $data ) ) {
			return [];
		}

		return $this->filter_departments( json_decode( $data, true ) ) ?: [];
	}

	/**
	 * Gets data on the individuals within a department.
	 *
	 * @param string $department_code A department code.
	 * @return array An array of people.
	 */
	public function get_individuals_in_department( string $department_code ) : array {
		$url = str_replace( '{DEPT}', $department_code, $this->fetch_department_url );
		$response = WP::wp_remote_get( $url );

		if ( WP::is_wp_error( $response ) ) {
			return [];
		}

		$data = WP::wp_remote_retrieve_body( $response );

		if ( WP::is_wp_error( $data ) ) {
			return [];
		}

		return json_decode( $data, true )['entries'] ?: [];
	}
}
