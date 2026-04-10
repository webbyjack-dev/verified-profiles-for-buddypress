/**
 * Admin JavaScript for Verified Badge for BuddyPress.
 *
 * @package BP_Verified_Badge
 * @since   2.8.0
 */

( function( $ ) {
	'use strict';

	$( document ).ready( function() {

		// Initialise the WordPress colour picker on any colour inputs.
		// The picker is used by the Pro appearance settings tab.
		if ( $.fn.wpColorPicker ) {
			$( '.wp-color-picker' ).wpColorPicker();
		}

	} );

} )( jQuery );
