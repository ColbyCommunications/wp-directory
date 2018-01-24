<?php
/**
 * Creates a shortcode for showing a staff directory listing.
 *
 * @package colbycomms/wp-directory
 */

namespace ColbyComms\WpDirectory\Shortcodes;

use ColbyComms\WpDirectory\Utils\WpFunctions as WP;

/**
 * Shortcode [person]
 */
class PersonShortcode {
	/**
	 * The shortcode tag to be added.
	 *
	 * @var string
	 */
	public static $shortcode_tag = 'person';

	/**
	 * Default shortcode attributes.
	 *
	 * @var string
	 */
	public static $shortcode_defaults = [
		'leader' => false,
		'name' => '',
		'title' => '',
		'phone' => '',
		'bio' => '',
		'email' => '',
		'photo' => '',
		'box' => '',
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
	 * The shortcode callback.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string HTML output.
	 */
	public static function render_shortcode( $atts = [], $content = '' ) : string {
		$atts = WP::shortcode_atts( self::$shortcode_defaults, $atts );

		ob_start();
		?>
<section class="department__person person">
	<?php if ( $atts['photo'] ) : ?>
	<aside class="person__photo-container">
		<img src="<?php echo WP::esc_attr( $atts['photo'] ); ?>"
			alt="<?php echo WP::esc_attr( $atts['name'] ); ?>" />
	</aside>
	<?php endif; ?>
	<div class="person__inner">
		<header class="person__header">
			<h1 class="person__name">
				<?php echo WP::wp_kses_post( $atts['name'] ); ?>
			</h1>
			<h2 class="person__title">
				<?php echo WP::wp_kses_post( $atts['title'] ); ?>
			</h2>
		</header>
		<?php if ( $content ) : ?>
		<section class="person__body">
			<?php echo WP::wp_kses_post( $content ); ?>
		</section>
		<?php endif; ?>
		<footer class="person__info">
			<?php if ( $atts['email'] ) : ?>
			<div class="person__email">
				<a data-mailto="<?php echo WP::esc_attr( $atts['email'] ); ?>"></a>
			</div>
			<?php endif; ?>
			<?php if ( $atts['phone'] ) : ?>
			<div class="person__phone">
				207-859-<?php echo $atts['phone']; ?>
			</div>
			<?php endif; ?>
			<?php if ( $atts['box'] ) : ?>
			<div class="person__box">
				Box <?php echo $atts['box']; ?>
			</div>
			<?php endif; ?>
		</footer>
	</div>
</section>
		<?php

		return ob_get_clean();
	}
}
