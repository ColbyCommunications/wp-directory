<?php

namespace ColbyComms\WpDirectory;

use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

class WpDirectoryPlugin {
	/**
	 * Adds hooks.
	 */
	public function __construct() {
		WP::add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts_and_styles' ] );
	}

	/**
	 * Enqueues plugin assets.
	 *
	 * @return void
	 */
	public static function enqueue_scripts_and_styles() : void {
		$dist = self::get_dist_directory();
		$min = defined( 'PROD' ) && PROD ? '.min' : '';
		/**
		 * Filters whether to enqueue this plugin's script.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( 'colbycomms__wp_directory__enqueue_script', true ) === true ) {
			wp_enqueue_script(
				TEXT_DOMAIN,
				"{$dist}wp-directory$min.js",
				[],
				VERSION,
				true
			);
		}
		/**
		 * Filters whether to enqueue this plugin's stylesheet.
		 *
		 * @param bool Yes or no.
		 */
		if ( apply_filters( 'colbycomms__wp_directory__enqueue_style', true ) === true ) {
			wp_enqueue_style(
				TEXT_DOMAIN,
				"{$dist}wp-directory$min.css",
				[],
				VERSION
			);
		}
	}


	/**
	 * Gets the plugin's dist/ directory URL, whether this package is installed as a plugin
	 * or in a theme via composer. If the package is in neither of those places and the filter
	 * is not used, this whole thing will fail.
	 *
	 * @return string The URL.
	 */
	public static function get_dist_directory() : string {
		/**
		 * Filters the URL location of the /dist directory.
		 *
		 * @param string The URL.
		 */
		$dist = apply_filters( 'colbycomms__whos_coming__dist', '' );
		if ( ! empty( $dist ) ) {
			return $dist;
		}

		if ( file_exists( dirname( __DIR__, 4 ) . '/plugins' ) ) {
			return plugin_dir_url( dirname( __DIR__, 2 ) . '/index.php' ) . '/dist/';
		}

		return get_template_directory_uri() . '/vendor/colbycomms/whos-coming/dist/';
	}
}
