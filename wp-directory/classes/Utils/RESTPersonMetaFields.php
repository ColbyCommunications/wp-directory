<?php

namespace ColbyComms\WpDirectory\Utils;

class RESTPersonMetaFields extends \WP_REST_Post_Meta_Fields {
	/**
	 * Retrieves the object meta type.
	 *
	 * @since 4.7.0
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return $this->post_type;
	}

	/**
	 * Retrieves the meta field value.
	 *
	 * @since 4.7.0
	 *
	 * @param int             $object_id Object ID to fetch meta for.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @return WP_Error|object Object containing the meta values by name, otherwise WP_Error object.
	 */
	public function get_value( $object_id, $request ) {
		$fields   = $this->get_registered_fields();
		$response = array();

		foreach ( $fields as $meta_key => $args ) {
			$response[ $args['name'] ] = get_post_meta( $object_id, $meta_key, true );
		}

		return $response;
	}
}
