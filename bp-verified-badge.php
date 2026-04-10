<?php
/**
 * Plugin Name:       Verified Badge for BuddyPress
 * Plugin URI:        https://store.webbyjack.com/verify-for-buddypress-pro
 * Description:       Adds a verified badge to BuddyPress profiles. Modern tabbed UI with user management, username verification, and automations.
 * Version:           1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            WebbyJack
 * Author URI:        https://store.webbyjack.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bp-verified-badge
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'BP_VERIFIED_VERSION',     '2.8.0' );
define( 'BP_VERIFIED_PLUGIN_FILE', __FILE__ );
define( 'BP_VERIFIED_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'BP_VERIFIED_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );



// Autoload classes.
require_once BP_VERIFIED_PLUGIN_DIR . 'includes/class-core.php';
require_once BP_VERIFIED_PLUGIN_DIR . 'includes/class-assets.php';
require_once BP_VERIFIED_PLUGIN_DIR . 'includes/class-admin.php';
require_once BP_VERIFIED_PLUGIN_DIR . 'includes/class-frontend.php';

// Initialise.
BP_Verified_Assets::init();
BP_Verified_Admin::init();
BP_Verified_Frontend::init();
