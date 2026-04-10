<?php
/**
 * Admin menu, settings registration, and settings page rendering.
 *
 * @package BP_Verified_Badge
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BP_Verified_Admin {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register the top-level admin menu page.
	 */
	public static function register_menu() {
		add_menu_page(
			esc_html__( 'Verified Badge', 'bp-verified-badge' ),
			esc_html__( 'Verified Badge', 'bp-verified-badge' ),
			'manage_options',
			'bp-verified-badge',
			array( __CLASS__, 'render_page' ),
			'dashicons-yes-alt',
			75
		);
	}

	/**
	 * Register plugin settings.
	 */
	public static function register_settings() {
		register_setting( 'bp_verified_badge_options', 'bp_verified_badge_color', 'sanitize_hex_color' );
		register_setting( 'bp_verified_badge_options', 'bp_verified_badge_icon',  'esc_url_raw' );
	}

	/**
	 * Output the full settings page shell.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';

		self::process_actions();

		?>
		<div class="wrap bp-v-wrap">
			<div class="bp-v-header">
				<h1><?php esc_html_e( 'Verified Badge for BuddyPress', 'bp-verified-badge' ); ?></h1>
				<a href="https://store.webbyjack.com/verify-for-buddypress-pro"
				   target="_blank"
				   rel="noopener noreferrer"
				   class="button button-primary bp-v-upgrade-btn">
					<?php esc_html_e( 'Upgrade to Pro', 'bp-verified-badge' ); ?>
				</a>
			</div>

			<h2 class="nav-tab-wrapper bp-v-nav">
				<?php self::render_tab_nav( $active_tab ); ?>
			</h2>

			<div class="bp-v-card">
				<?php
				switch ( $active_tab ) {
					case 'manage':
						self::render_tab_manage();
						break;
					case 'list':
						self::render_tab_list();
						break;
					case 'automations':
						self::render_tab_automations();
						break;
					default:
						self::render_tab_settings();
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle verify / unverify actions.
	 */
	private static function process_actions() {
		// POST: verify user.
		if ( isset( $_POST['bp_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bp_verify_nonce'] ) ), 'bp_verify_action' ) ) {

			if ( ! empty( $_POST['bp_verify_user_id'] ) ) {
				BP_Verified_Core::verify_user( intval( $_POST['bp_verify_user_id'] ) );
				self::admin_notice( esc_html__( 'User verified.', 'bp-verified-badge' ), 'success' );
			}

			if ( ! empty( $_POST['bp_verify_username'] ) ) {
				$user = get_user_by( 'login', sanitize_user( wp_unslash( $_POST['bp_verify_username'] ) ) );
				if ( $user ) {
					BP_Verified_Core::verify_user( $user->ID );
					
					self::admin_notice(
						sprintf(
							/* translators: %s: Username */
							esc_html__( 'User %s verified!', 'bp-verified-badge' ),
							esc_html( $user->user_login )
						),
						'success'
					);
				} else {
					self::admin_notice( esc_html__( 'User not found.', 'bp-verified-badge' ), 'error' );
				}
			}
		}

		// GET: unverify user.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['unverify'], $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'unverify_user_' . (int) $_GET['unverify'] ) ) {
				BP_Verified_Core::unverify_user( (int) $_GET['unverify'] );
				self::admin_notice( esc_html__( 'Verification removed.', 'bp-verified-badge' ), 'warning' );
			}
		}
	}

	/**
	 * Render an admin notice with late escaping.
	 */
	private static function admin_notice( $message, $type = 'success' ) {
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			esc_attr( $type ),
			wp_kses_post( $message )
		);
	}

	private static function render_tab_nav( $active_tab ) {
		$tabs = array(
			'settings'    => esc_html__( 'Settings', 'bp-verified-badge' ),
			'manage'      => esc_html__( 'Manual Verification', 'bp-verified-badge' ),
			'list'        => esc_html__( 'Verified Users', 'bp-verified-badge' ),
			'automations' => esc_html__( 'Automations', 'bp-verified-badge' ),
		);

		foreach ( $tabs as $slug => $label ) {
			$url   = admin_url( 'admin.php?page=bp-verified-badge&tab=' . $slug );
			$class = ( $active_tab === $slug ) ? 'nav-tab nav-tab-active' : 'nav-tab';

			if ( 'automations' === $slug ) {
				printf(
					'<a href="%s" class="%s">%s <span class="bp-v-pro-badge">%s</span></a>',
					esc_url( $url ),
					esc_attr( $class ),
					esc_html( $label ),
					esc_html__( 'Pro', 'bp-verified-badge' )
				);
			} else {
				printf(
					'<a href="%s" class="%s">%s</a>',
					esc_url( $url ),
					esc_attr( $class ),
					esc_html( $label )
				);
			}
		}
	}

	private static function render_tab_settings() {
		?>
		<div class="bp-v-upsell-box">
			<div>
				<p class="bp-v-upsell-title"><?php esc_html_e( 'Personalise your community', 'bp-verified-badge' ); ?></p>
				<p class="bp-v-upsell-sub"><?php esc_html_e( 'Upgrade to change badge colours and upload custom icons.', 'bp-verified-badge' ); ?></p>
			</div>
			<a href="https://store.webbyjack.com/verify-for-buddypress-pro" target="_blank" rel="noopener noreferrer" class="button"><?php esc_html_e( 'Go Pro', 'bp-verified-badge' ); ?></a>
		</div>
		<div class="bp-v-pro-locked">
			<h3><?php esc_html_e( 'Appearance Settings', 'bp-verified-badge' ); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Badge Colour', 'bp-verified-badge' ); ?></th>
					<td><input type="text" class="wp-color-picker" value="#1DA1F2" disabled /></td>
				</tr>
			</table>
		</div>
		<?php
	}

	private static function render_tab_automations() {
		?>
		<div class="bp-v-upsell-box">
			<div>
				<p class="bp-v-upsell-title"><?php esc_html_e( 'Auto-Verify Members', 'bp-verified-badge' ); ?></p>
				<p class="bp-v-upsell-sub"><?php esc_html_e( 'Automatically verify users based on roles or activity.', 'bp-verified-badge' ); ?></p>
			</div>
			<a href="https://store.webbyjack.com/verify-for-buddypress-pro" target="_blank" rel="noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Unlock Automations', 'bp-verified-badge' ); ?></a>
		</div>
		<div class="bp-v-pro-locked">
			<h3><?php esc_html_e( 'Active Workflows', 'bp-verified-badge' ); ?></h3>
			<div class="bp-v-automation-row">
				<strong><?php esc_html_e( 'IF', 'bp-verified-badge' ); ?></strong>
				<span><?php esc_html_e( 'User Role is', 'bp-verified-badge' ); ?></span>
				<code>Editor</code>
				<strong><?php esc_html_e( 'THEN', 'bp-verified-badge' ); ?></strong>
				<span><?php esc_html_e( 'Verify User', 'bp-verified-badge' ); ?></span>
			</div>
			<button class="button button-large" disabled>+ <?php esc_html_e( 'Create New Automation', 'bp-verified-badge' ); ?></button>
		</div>
		<?php
	}

	private static function render_tab_manage() {
		?>
		<h3><?php esc_html_e( 'Manual Verification', 'bp-verified-badge' ); ?></h3>
		<div class="bp-v-verify-grid">
			<div class="bp-v-verify-box">
				<h4><?php esc_html_e( 'Verify by Username', 'bp-verified-badge' ); ?></h4>
				<form method="post">
					<?php wp_nonce_field( 'bp_verify_action', 'bp_verify_nonce' ); ?>
					<input type="text" name="bp_verify_username" placeholder="<?php esc_attr_e( 'username', 'bp-verified-badge' ); ?>" class="regular-text bp-v-full-width" required />
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Verify', 'bp-verified-badge' ); ?></button>
				</form>
			</div>
			<div class="bp-v-verify-box">
				<h4><?php esc_html_e( 'Verify by User ID', 'bp-verified-badge' ); ?></h4>
				<form method="post">
					<?php wp_nonce_field( 'bp_verify_action', 'bp_verify_nonce' ); ?>
					<input type="number" name="bp_verify_user_id" placeholder="1" class="regular-text bp-v-full-width" min="1" required />
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Verify', 'bp-verified-badge' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}

	private static function render_tab_list() {
		$users = BP_Verified_Core::get_verified_users();
		?>
		<h3><?php esc_html_e( 'Verified Members', 'bp-verified-badge' ); ?></h3>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'bp-verified-badge' ); ?></th>
					<th><?php esc_html_e( 'Username', 'bp-verified-badge' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'bp-verified-badge' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $users ) : ?>
					<?php foreach ( $users as $user ) : ?>
						<?php
						$nonce_url = wp_nonce_url(
							admin_url( 'admin.php?page=bp-verified-badge&tab=list&unverify=' . $user->ID ),
							'unverify_user_' . $user->ID
						);
						?>
						<tr>
							<td><?php echo esc_html( $user->display_name ); ?></td>
							<td>@<?php echo esc_html( $user->user_login ); ?></td>
							<td>
								<a href="<?php echo esc_url( $nonce_url ); ?>" class="bp-v-remove-link">
									<?php esc_html_e( 'Remove', 'bp-verified-badge' ); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3"><?php esc_html_e( 'No verified users found.', 'bp-verified-badge' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}
}