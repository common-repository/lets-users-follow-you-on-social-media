<?php
/**
 * Plugin that adds a message at the end of each article asking users to follow us on Twitter
 *
 * @package   pcpl-follow
 * @author    Dhananjay Singh <dhananjay22a@gmail.com>
 * @license   GPL v2 or later
 *
 * Plugin Name:  Lets Users Follow you on social media
 * Description:  Plugin to adds a message at the end of each article asking users to follow us on Twitter
 * Version:      1.0.0
 * Author:       Dhananjay Singh
 * Text Domain:  pcpl-follow
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class Pcpl_Follow {

	static $plugin_name = 'Follow Us On Social Media';
	static $plugin_slug = 'pcpl-follow';

	public function __construct() {
		if ( is_admin() ) {
			// Activation and Deactivation hooks
			register_activation_hook( __FILE__, [ $this, 'plugin_activation' ] );
			register_deactivation_hook( __FILE__, [ $this, 'plugin_deactivation' ] );
			add_action( 'admin_init', [ $this, 'do_activation_redirect' ] );
			add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts_and_styles' ] );
			add_action( 'admin_notices', [ $this, 'notice_welcome' ] );

			add_action( 'admin_post_save_pcpl_follow_settings', array( $this, 'save_pcpl_follow_settings' ) );
			add_action( 'admin_post_save_pcpl_follow_enable_disable_settings', array( $this, 'save_pcpl_follow_enable_disable_settings' ) );
		}

		// Hook our function to WordPress the_content filter
		add_filter( 'the_content', [ $this, 'pcpl_follow_us' ] );
	}

	/**
	 * Activate the plugin
	 */
	public function plugin_activation() : void {
		set_transient( 'pcpl_activation_redirect_transient', true, 30 );
	}

	/**
	 * Deactivate the plugin
	 */
	public function plugin_deactivation() : void {
		update_option( 'pcpl_welcome', 1 );
	}

	public function do_activation_redirect() {
		// Bail if no activation redirect
		if ( ! get_transient( 'pcpl_activation_redirect_transient' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( 'pcpl_activation_redirect_transient' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Redirect to plugin page
		wp_safe_redirect( add_query_arg( array( 'page' => self::$plugin_slug ), admin_url( 'admin.php' ) ) );

	}

	/**
	 * Enqueue CSS for ou plugin in admin area.
	 */
	public function enqueue_admin_scripts_and_styles(){
		wp_enqueue_style('pcpl_admin_style', plugin_dir_url(__FILE__) . '/assets/css/admin-styles.css');
		wp_enqueue_script( 'pcpl_admin_script', plugin_dir_url(__FILE__) . '/assets/js/admin-scripts.js', array(), '1.0.2', true );
	}

	/**
	 * Add menu item in the admin area.
	 */
	public function create_admin_menu() {
		// add_submenu_page( 'options-general.php', self::$plugin_name, self::$plugin_name, 'manage_options', self::$plugin_slug, [ $this, 'admin_panel' ] );
		// Add the menu for bottom popup items
		$page_title = self::$plugin_name;
		$menu_title = self::$plugin_name;
		$capability = 'edit_others_posts';
		$slug       = 'pcpl-follow';
		$callback   = array( $this, 'admin_panel' );
		$icon       = 'dashicons-admin-plugins';
		$position   = 100;

		add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
	}

	public function admin_panel(){

		if ( ! current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'Sorry, you are not allowed to access this page.', 'pcpl-follow' ) . '</p>';
			return;
		}

		$pcpl_follow_config  = get_option( 'pcpl_follow_config' );
		$pcpl_follow_enabled = get_option( 'pcpl_follow_enabled' );

		// Display the plugin page
		include_once( __DIR__ . '/templates/admin-panel.php' );
	}

	/**
	* Function to save pcpl follow configs
	*/
	public function save_pcpl_follow_settings() {

		// Verify the nonce before proceeding.
		if ( empty( $_POST[ self::$plugin_slug . '-nonce' ] ) ) { // Input var okay.
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::$plugin_slug . '-nonce' ] ) ), self::$plugin_slug . '-nonce' ) ) { // Input var okay.
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) ) { // Input var okay.
			return;
		}

		// $pcpl_follow_config = ! empty( $_POST['follow_config'] ) ? wpautop( $_POST['follow_config'] ) : ''; //Input var okay. // @codingStandardsIgnoreLine
		$pcpl_follow_config = ! empty( $_POST['follow_config'] ) ? sanitize_text_field( htmlentities( $_POST['follow_config'] ) ) : ''; //Input var okay. // @codingStandardsIgnoreLine

		update_option( 'pcpl_follow_config', $pcpl_follow_config, false );

		$url = wp_nonce_url( admin_url( 'admin.php?page=pcpl-follow' ), self::$plugin_slug . '-nonce', self::$plugin_slug . '-nonce' );
		$url = str_replace( '&amp;', '&', $url );
		wp_safe_redirect( $url );
		exit;

	}

	/**
	* Function to save pcpl follow plugin enable/disable status
	*/
	public function save_pcpl_follow_enable_disable_settings() {

		// Verify the nonce before proceeding.
		if ( empty( $_POST[ self::$plugin_slug . '-nonce' ] ) ) { // Input var okay.
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::$plugin_slug . '-nonce' ] ) ), self::$plugin_slug . '-nonce' ) ) { // Input var okay.
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) ) { // Input var okay.
			return;
		}

		$pcpl_follow_enabled = ! empty( $_POST['pcpl_follow_enabled'] ) ? intval( $_POST['pcpl_follow_enabled'] ) : 0; //Input var okay. // @codingStandardsIgnoreLine

		update_option( 'pcpl_follow_enabled', $pcpl_follow_enabled, false );

		$url = wp_nonce_url( admin_url( 'admin.php?page=' . self::$plugin_slug ), self::$plugin_slug . '-nonce', self::$plugin_slug . '-nonce' );
		$url = str_replace( '&amp;', '&', $url );
		wp_safe_redirect( $url );
		exit;

	}

	public function pcpl_follow_us( $content ) {

		$pcpl_follow_enabled = sanitize_text_field( get_option( 'pcpl_follow_enabled' ) );

		if ( $pcpl_follow_enabled ) {
			// Only do this when a single post is displayed
			if ( is_single() ) {

				$pcpl_follow_config = get_option( 'pcpl_follow_config' );

				// Message you want to display after the post
				// Add URLs to your own Twitter and Facebook profiles
				$content .= wp_kses_post( stripslashes( html_entity_decode( $pcpl_follow_config ) ) );
			}
		}

		// Return the content
		return $content;
	}

	/**
	 * Display welcome messages
	 */
	public function notice_welcome() {
		global $pagenow;

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && self::$plugin_slug === $_GET['page'] ) {
			if ( ! get_option( 'pcpl_welcome' ) ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo __( 'Thank you for installing PCPL Follow Us on Social Media Plugin.', 'pcpl-follow' ) ?></p>
				</div>
				<?php
				update_option( 'pcpl_welcome', 1 );
			}
		}
	}

}

new Pcpl_Follow();
