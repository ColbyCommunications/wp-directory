<?php
/**
 * WpFunctions.php
 *
 * @package colbycomms/colby-wp-schedule
 */

namespace ColbyComms\WpDirectory\Utils;

/**
 * Wrapper for WP functions called in this plugin. Provides fallbacks for testing.
 */
class WpFunctions {
	/**
	 * Provides fallback for WP functions whose behavior can be swallowed in testing.
	 *
	 * @param string $func The function name.
	 * @param array  $args The function arguments.
	 * @return mixed The function output or void.
	 */
	public static function __callStatic( string $func, $args ) {
		if ( function_exists( $func ) ) {
			return call_user_func_array( $func, $args );
		} else {
			return $args[0];
		}
	}

	/**
	 * Prevents errors if esc_attr doesn't exist.
	 *
	 * @param string $content A string to escape.
	 * @return string The escaped string.
	 */
	public static function esc_attr( string $content = '' ) : string {
		return function_exists( 'esc_attr' ) ? esc_attr( $content ) : $content;
	}

	/**
	 * Prevents errors if apply_filters doesn't exist.
	 *
	 * @return mixed The filtered value.
	 */
	public static function apply_filters() {
		$args = func_get_args();

		return function_exists( 'apply_filters' )
			? call_user_func_array( 'apply_filters', $args )
			: $args[1];
	}

	/**
	 * Provides a fallback for the shortcode_atts function.
	 *
	 * @return array The modified shortcode atts.
	 */
	public static function shortcode_atts() {
		$args = func_get_args();

		if ( function_exists( 'shortcode_atts' ) ) {
			return call_user_func_array( 'shortcode_atts', $args );
		}

		return array_merge( $args[0], $args[1] );
	}
}
