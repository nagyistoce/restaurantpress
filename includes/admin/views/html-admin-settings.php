<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap restaurantpress">
	<form method="<?php echo esc_attr( apply_filters( 'restaurantpress_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-restaurantpress-settings" id="icon-restaurantpress"><br /></div>
		<h1><?php printf( __( '%s Settings', 'restaurantpress' ), esc_html( $tabs[ $current_tab ] ) ); ?></h1>
		<h2 class="nav-tab-wrapper rp-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=rp-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}

				do_action( 'restaurantpress_settings_tabs' );
			?>
		</h2>
		<?php
			do_action( 'restaurantpress_sections_' . $current_tab );

			self::show_messages();

			do_action( 'restaurantpress_settings_' . $current_tab );
			do_action( 'restaurantpress_settings_tabs_' . $current_tab ); // @deprecated hook
		?>
		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save Changes', 'restaurantpress' ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab" />
			<?php wp_nonce_field( 'restaurantpress-settings' ); ?>
		</p>
	</form>
</div>
