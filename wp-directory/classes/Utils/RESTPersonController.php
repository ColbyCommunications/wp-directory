<?php

namespace ColbyComms\WpDirectory\Utils;

class RESTPersonController extends \WP_REST_Posts_Controller {
	public function __construct( $post_type ) {
		parent::__construct( $post_type );
		$this->meta = new RESTPersonMetaFields( $post_type );
	}
}
