<?php
/**
 * Asset registration and enqueuing.
 *
 * Registers all CSS and JS for both the admin and frontend,
 * loading files from the /assets directory and never inline.
 *
 * @package BP_Verified_Badge
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BP_Verified_Assets {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'enqueue_frontend' ) );
	}

	/**
	 * Enqueue admin stylesheet and scripts on our settings page only.
	 *
	 * @param string $hook Current admin page hook suffix.
	 * @return void
	 */
	public static function enqueue_admin( $hook ) {
		if ( 'toplevel_page_bp-verified-badge' !== $hook ) {
			return;
		}

		// WordPress core colour picker (used by Pro appearance tab).
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Plugin admin styles.
		wp_enqueue_style(
			'bp-verified-admin',
			BP_VERIFIED_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			BP_VERIFIED_VERSION
		);

		// Plugin admin scripts.
		wp_enqueue_script(
			'bp-verified-admin',
			BP_VERIFIED_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BP_VERIFIED_VERSION,
			true
		);
	}

	/**
	 * Enqueue frontend stylesheet on BuddyPress user profile pages only.
	 *
	 * @return void
	 */
	public static function enqueue_frontend() {
		if ( ! function_exists( 'bp_is_user' ) || ! bp_is_user() ) {
			return;
		}

		wp_enqueue_style(
			'bp-verified-badge',
			BP_VERIFIED_PLUGIN_URL . 'assets/css/badge.css',
			array(),
			BP_VERIFIED_VERSION
		);
	}
}
