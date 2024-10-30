<?php
if ( isset( $_GET['pcpl-follow-nonce'] ) ) { // Input var okay.

	// Verify the nonce before proceeding.
	if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['pcpl-follow-nonce'] ) ), 'pcpl-follow-nonce' ) ) { // Input var okay.

		// To Do: Update data here

		echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Success! Data saved successfully.', 'pcpl-follow' ) . '</p></div>';

	} else {
		echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Error: Invalid nonce, data not saved, please try again!', 'pcpl-follow' ) . '</p></div>';
	}
}
?>

<div class="wrap">
	<h2><?php echo __( 'Lets Users Follow you on social media', 'pcpl-follow' ); ?></h2>

	<div id="uac-message"></div>

	<h2 class="nav-tab-wrapper uac-tabs">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url( 'options-general.php?page=' . self::$plugin_slug ); ?>"><?php echo __( 'General Settings', 'pcpl-follow' ); ?></a>
		<a class="nav-tab" href="<?php echo admin_url( 'options-general.php?page=' . self::$plugin_slug ); ?>"><?php echo __( 'Enable/Disable', 'pcpl-follow' ); ?></a>
		<a class="nav-tab" href="<?php echo admin_url( 'options-general.php?page=' . self::$plugin_slug ); ?>"><?php echo __( 'About', 'pcpl-follow' ); ?></a>
	</h2>

	<input type="hidden" name="uac_current_tab" id="uac_current_tab" value="1">

	<section class="uac-section">
		<form method="post" action="admin-post.php" enctype="multipart/form-data">
			<input type="hidden" id="pcpl-follow-nonce" name="pcpl-follow-nonce" value="<?php echo esc_attr( wp_create_nonce( 'pcpl-follow-nonce' ) ); ?>" />
			<input type="hidden" name="action" name="action" value="save_pcpl_follow_settings">

			<p>
				<b><?php echo __( 'Configure the content below as you wish to show on article page. Replace the social media icons with desired one and link to your relevant page.', 'pcpl-follow' ); ?></b>
			</p>

			<?php
			$settings = array(
				'textarea_rows' => 20,
				'textarea_name' => 'follow_config',
				'teeny'         => true,
				'media_buttons' => true,
			);

			$default_data = '<!-- wp:columns -->
<div class="wp-block-columns">

<!-- wp:column {"style":{"color":{"gradient":"linear-gradient(135deg,rgb(255,203,112) 21%,rgb(199,81,192) 54%,rgb(65,88,208) 100%)"},"spacing":{"padding":{"top":"0px","right":"10px","bottom":"0px","left":"15px"}}}} -->
<div class="wp-block-column has-background" style="background: linear-gradient(135deg,#ffcb70 21%,#c751c0 54%,#4158d0 100%); padding: 0px 10px 0px 10px;">

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1"}},"textColor":"background","fontSize":"medium"} -->
<p class="has-background-color has-text-color has-medium-font-size" style="line-height: 1; padding: 15px;">If you like this article then please follow us on <img class="wp-image-150" style="width: 30px; vertical-align: middle;" src="http://two.wordpress.test/wp-content/uploads/2022/05/pcpl-twitter2-1.webp" alt="" /> <img class="wp-image-151" style="width: 30px; vertical-align: middle;" src="http://two.wordpress.test/wp-content/uploads/2022/05/pcpl_facebook.png" alt="" /></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->';

			$desc = ! empty( $pcpl_follow_config ) ? $pcpl_follow_config : $default_data;

			wp_editor( wp_kses_post( stripslashes( html_entity_decode( $desc ) ) ), 'follow_config', $settings );
			?>

			<p>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
			</p>
		</form>

	</section>

	<section class="uac-section">
		<form method="post" action="admin-post.php" enctype="multipart/form-data">
			<input type="hidden" id="pcpl-follow-nonce" name="pcpl-follow-nonce" value="<?php echo esc_attr( wp_create_nonce( 'pcpl-follow-nonce' ) ); ?>" />
			<input type="hidden" name="action" name="action" value="save_pcpl_follow_enable_disable_settings">

			<p>
				<b><?php echo __( 'Enable/Disable the plugin', 'pcpl-follow' ); ?></b>
			</p>
			<p>
				<label>Enable :</label>
				<input type="checkbox" name="pcpl_follow_enabled" value="1" <?php echo ( "1" === sanitize_text_field( $pcpl_follow_enabled ) ? 'checked' : '' ); ?> >
			</p>

			<p>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
			</p>
		</form>

	</section>

	<section class="uac-section">
		<div class="uac-about">

			<p><b><?php echo __( 'Follow Us on Social Media', 'pcpl-follow' ); ?></b></p>

			<p><?php echo __( 'Version: 1.0.1', 'pcpl-follow' ); ?></p>

			<p><a href="http://siwanpress.com/" target="_blank"><?php echo __( 'Author\'s Website', 'pcpl-follow' ); ?></a></p>

			<p><?php echo __( 'If you have any feedback please tell us. We love to improve our service.', 'pcpl-follow' ); ?></p>

			<p><a href="http://siwanpress.com/provide-feedback/" target="_blank"><?php echo __( 'Provide Feedback', 'pcpl-follow' ); ?></a></p>
		</div>

	</section>

</div>
