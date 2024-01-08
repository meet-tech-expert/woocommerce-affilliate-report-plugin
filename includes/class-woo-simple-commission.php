<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://compactimpact.com
 * @since      1.0.0
 *
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Simple_Commission
 * @subpackage Woo_Simple_Commission/includes
 * @author     Jade-eCommerce <support@j-e.com.hk>
 */
class Woo_Simple_Commission {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Simple_Commission_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_WOO_SIMPLE_COMM_VERSION' ) ) {
			$this->version = PLUGIN_WOO_SIMPLE_COMM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woo-simple-commission';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Simple_Commission_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Simple_Commission_i18n. Defines internationalization functionality.
	 * - Woo_Simple_Commission_Admin. Defines all hooks for the admin area.
	 * - Woo_Simple_Commission_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-simple-commission-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-simple-commission-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-simple-commission-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-simple-commission-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wsc-list-table.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wsc-payment-table.php';

		$this->loader = new Woo_Simple_Commission_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Simple_Commission_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_Simple_Commission_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woo_Simple_Commission_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wsc_admin_menu' );

		$this->loader->add_filter('set-screen-option', $plugin_admin, 'wsc_set_screen_option' ,10, 3);
		$this->loader->add_action( 'wp_ajax_payment_commission', $plugin_admin, 'wsc_payment_commission_action' );
		$this->loader->add_action( 'wp_ajax_edit_payment_mode', $plugin_admin, 'wsc_edit_payment_mode_action' );
		$this->loader->add_action( 'wp_ajax_payment_stats', $plugin_admin, 'wsc_payment_stats_action' );
		$this->loader->add_action( 'wp_ajax_affilliate_stats', $plugin_admin, 'wsc_affilliate_stats_action' );
		
		$this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_admin, 'wsc_woocommerce_order_status_cancelled' );
		// Add Variation Settings
       $this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_admin, 'wsc_variation_settings_fields', 10, 3 );
		// Save Variation Settings
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'wsc_save_variation_settings_fields', 10, 1 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Simple_Commission_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Simple_Commission_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
