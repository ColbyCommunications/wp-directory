<?php
/**
 * DataFetcher.php
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory\Data;

// phpcs:disable Squiz.Commenting.FunctionComment.Missing

/**
 * Provides a structure for potentially importing data from other sources.
 */
interface DataFetcher {
	public static function fetch( array $department_codes = [] ) : array;
	public function __construct( array $department_codes = [] );
	public function get_departments() : array;
	public function filter_departments( array $data = [] ) : array;
	public function get_individuals_in_department( string $department_code ) : array;
}
