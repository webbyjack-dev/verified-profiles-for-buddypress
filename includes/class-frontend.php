<?php
/**
 * Front-end badge injection for BuddyPress profile pages.
 *
 * Outputs the badge via a small jQuery snippet in wp_footer.
 * All visual styling is handled by assets/css/badge.css,
 * which is enqueued by BP_Verified_Assets on profile pages.
 *
 * @package BP_Verified_Badge
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BP_Verified_Frontend {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'maybe_inject_badge' ), 999 );
	}

	/**
	 * Inject the badge DOM manipulation script on verified user profile pages.
	 *
	 * The stylesheet is already enqueued by BP_Verified_Assets::enqueue_frontend()
	 * so no styles are output here.
	 *
	 * @return void
	 */
	public static function maybe_inject_badge() {
		if ( ! function_exists( 'bp_is_user' ) || ! bp_is_user() ) {
			return;
		}

		$user_id = bp_displayed_user_id();

		if ( ! $user_id || ! BP_Verified_Core::is_verified( $user_id ) ) {
			return;
		}

		$badge_html  = BP_Verified_Core::get_badge_html();
		$clean_badge = str_replace( array( "\r", "\n" ), '', $badge_html );
		?>
		<script type="text/javascript">
			( function( $ ) {
				$( document ).ready( function() {
					var badge  = '<?php echo $clean_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup, no user data ?>';
					var $target = $( '.user-nicename, #item-header-content h2, .item-header-name' );
					if ( $target.length ) {
						$target.first().append( ' ' + badge );
					}
				} );
			} )( jQuery );
		</script>
		<?php
	}
}
