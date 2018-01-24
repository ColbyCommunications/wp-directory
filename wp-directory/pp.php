<?php

if ( ! function_exists( 'pp' ) ) {
	function pp( $data, $die = 0 ) {
		echo '<pre>';

		print_r( $data );

		echo '</pre>';

		if ( $die ) {
			wp_die();
		}
	}
}