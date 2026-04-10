<?php
/**
 * Core verification helpers.
 *
 * Provides the canonical methods for verifying, unverifying, and
 * checking users, plus the SVG badge markup. Global function wrappers
 * are included at the bottom for backwards compatibility.
 *
 * @package BP_Verified_Badge
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BP_Verified_Core {

	/**
	 * User meta key that stores verification status.
	 *
	 * @var string
	 */
	const META_KEY = 'bp_verified';

	/**
	 * Mark a user as verified.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return void
	 */
	public static function verify_user( $user_id ) {
		update_user_meta( (int) $user_id, self::META_KEY, 1 );
	}

	/**
	 * Remove verification status from a user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return void
	 */
	public static function unverify_user( $user_id ) {
		delete_user_meta( (int) $user_id, self::META_KEY );
	}

	/**
	 * Check whether a user is verified.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	public static function is_verified( $user_id ) {
		return (int) get_user_meta( (int) $user_id, self::META_KEY, true ) === 1;
	}

	/**
	 * Return the inline SVG badge markup.
	 *
	 * @return string
	 */
	public static function get_badge_html() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1.2em" height="1.2em" class="bp-verified-svg" aria-label="' . esc_attr__( 'Verified', 'bp-verified-badge' ) . '" role="img"><title>' . esc_html__( 'Verified', 'bp-verified-badge' ) . '</title><path fill="#1DA1F2" d="M22.5 12.5c0-.62-.31-1.18-.8-1.54l.75-1.92c.2-.5-.07-1.07-.58-1.28l-2.01-.79c-.27-.47-.64-.88-1.07-1.2l-.46-2.12c-.11-.53-.63-.86-1.16-.76l-2.12.46c-.46-.35-.97-.62-1.5-.78l-.34-2.14c-.08-.54-.58-.92-1.12-.92h-1.18c-.54 0-1.04.38-1.12.92l-.34 2.14c-.53.16-1.04.43-1.5.78l-2.12-.46c-.53-.1-1.05.23-1.16.76l-.46 2.12c-.43.32-.8.73-1.07 1.2l-2.01.79c-.51.21-.78.78-.58 1.28l.75 1.92c-.49.36-.8.92-.8 1.54s.31 1.18.8 1.54l-.75 1.92c-.2.5.07 1.07.58 1.28l2.01.79c.27.47.64.88 1.07 1.2l.46 2.12c.11.53.63.86 1.16.76l2.12-.46c.46.35.97.62 1.5.78l.34 2.14c.08.54.58.92 1.12.92h1.18c.54 0 1.04-.38 1.12-.92l.34-2.14c.53-.16 1.04-.43 1.5-.78l2.12.46c.53.1 1.05-.23 1.16-.76l.46-2.12c.43-.32.8-.73 1.07-1.2l2.01-.79c.51-.21.78-.78.58-1.28l-.75-1.92c.49-.36.8-.92.8-1.54z"/><path fill="#ffffff" d="M10.25 16.5c-.2 0-.39-.08-.53-.22l-3.5-3.5c-.29-.29-.29-.77 0-1.06.29-.29.77-.29 1.06 0l2.97 2.97 6.47-6.47c.29-.29.77-.29 1.06 0 .29.29.29.77 0 1.06l-7 7c-.14.14-.33.22-.53.22z"/></svg>';
	}

	/**
	 * Retrieve all verified users.
	 *
	 * @return WP_User[]
	 */
	public static function get_verified_users() {
		return get_users( array(
			'meta_key'   => self::META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '1',            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		) );
	}
}

// ---------------------------------------------------------------------------
// Backwards-compatible global function wrappers.
// ---------------------------------------------------------------------------

if ( ! function_exists( 'bp_verify_user' ) ) {
	/**
	 * Mark a user as verified.
	 *
	 * @param int $user_id WordPress user ID.
	 */
	function bp_verify_user( $user_id ) {
		BP_Verified_Core::verify_user( $user_id );
	}
}

if ( ! function_exists( 'bp_unverify_user' ) ) {
	/**
	 * Remove verification from a user.
	 *
	 * @param int $user_id WordPress user ID.
	 */
	function bp_unverify_user( $user_id ) {
		BP_Verified_Core::unverify_user( $user_id );
	}
}

if ( ! function_exists( 'bp_is_user_verified' ) ) {
	/**
	 * Check whether a user is verified.
	 *
	 * @param int  $user_id WordPress user ID.
	 * @return bool
	 */
	function bp_is_user_verified( $user_id ) {
		return BP_Verified_Core::is_verified( $user_id );
	}
}

if ( ! function_exists( 'bp_get_verified_badge_html' ) ) {
	/**
	 * Return the SVG badge markup.
	 *
	 * @return string
	 */
	function bp_get_verified_badge_html() {
		return BP_Verified_Core::get_badge_html();
	}
}