<?php
/**
 * Plugin Name: RestaurantPress
 * Plugin URI: http://www.themegrill.com/plugins/restaurantpress/
 * Description: Allows you to create awesome restaurant menu for restaurant, bars, cafes in no time. Smartly :)
 * Version: 1.3.1
 * Author: ThemeGrill
 * Author URI: http://themegrill.com
 * Requires at least: 4.2
 * Tested up to: 4.4
 *
 * Text Domain: restaurantpress
 * Domain Path: /languages/
 *
 * @package  RestaurantPress
 * @category Core
 * @author   ThemeGrill
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RestaurantPress' ) ) :

/**
 * Main RestaurantPress Class.
 *
 * @class   RestaurantPress
 * @version 1.3.0
 */
final class RestaurantPress {

	/**
	 * RestaurantPress Version.
	 *
	 * @var string
	 */
	public $version = '1.3.1';

	/**
	 * The single instance of the class.
	 *
	 * @var RestaurantPress
	 */
	protected static $_instance = null;

	/**
	 * Main RestaurantPress Instance.
	 *
	 * Ensure only one instance of RestaurantPress is loaded or can be loaded.
	 *
	 * @static
	 * @see    RP()
	 * @return RestaurantPress - Main instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restaurantpress' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'restaurantpress' ), '1.0' );
	}

	/**
	 * RestaurantPress Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'restaurantpress_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'RP_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( 'RP_Shortcodes', 'init' ) );
		add_action( 'init', array( $this, 'wpdb_table_fix' ), 0 );
		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
	}

	/**
	 * Define RP Constants.
	 */
	private function define_constants() {
		$this->define( 'RP_PLUGIN_FILE', __FILE__ );
		$this->define( 'RP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'RP_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name
	 * @param string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Includes the required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( 'includes/functions-rp-core.php' );
		include_once( 'includes/functions-rp-widget.php' );
		include_once( 'includes/class-rp-autoloader.php' );
		include_once( 'includes/class-rp-install.php' );
		include_once( 'includes/class-rp-ajax.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-rp-admin.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		include_once( 'includes/class-rp-post-types.php' );         // Registers post types
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-rp-frontend-scripts.php' );   // Frontend Scripts
		include_once( 'includes/class-rp-shortcodes.php' );         // Shortcodes Class
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/restaurantpress/restaurantpress-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/restaurantpress-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'restaurantpress' );

		load_textdomain( 'restaurantpress', WP_LANG_DIR . '/restaurantpress/restaurantpress-' . $locale . '.mo' );
		load_plugin_textdomain( 'restaurantpress', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Ensure theme compatibility and setup image sizes.
	 */
	public function setup_environment() {
		$this->add_thumbnail_support();
		$this->add_image_sizes();
	}

	/**
	 * Ensure post thumbnail support is turned on.
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'food_menu', 'thumbnail' );
	}

	/**
	 * Add RP Image sizes to WP.
	 */
	private function add_image_sizes() {
		$food_grid	    = rp_get_image_size( 'food_grid' );
		$food_thumbnail = rp_get_image_size( 'food_thumbnail' );

		add_image_size( 'food_grid', $food_grid['width'], $food_grid['height'], $food_grid['crop'] );
		add_image_size( 'food_thumbnail', $food_thumbnail['width'], $food_thumbnail['height'], $food_thumbnail['crop'] );
	}

	/**
	 * RestaurantPress Term Meta API - set table name.
	 */
	public function wpdb_table_fix() {
		global $wpdb;

		if ( get_option( 'db_version' ) < 34370 ) {
			$wpdb->restaurantpress_termmeta = $wpdb->prefix . 'restaurantpress_termmeta';
			$wpdb->tables[]                 = 'restaurantpress_termmeta';
		}
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}
}

endif;

/**
 * Main instance of RestaurantPress.
 *
 * Returns the main instance of RP to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return RestaurantPress
 */
function RP() {
	return RestaurantPress::get_instance();
}

// Global for backwards compatibility.
$GLOBALS['restaurantpress'] = RP();
