<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.niu.com/en/
 * @since      1.0.0
 *
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/includes
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
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/includes
 * @author     NIU <info@niu.com>
 */
class Niu_range_calculator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Niu_range_calculator_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'NIU_RANGE_CALCULATOR_VERSION' ) ) {
			$this->version = NIU_RANGE_CALCULATOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'niu_range_calculator';

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
	 * - Niu_range_calculator_Loader. Orchestrates the hooks of the plugin.
	 * - Niu_range_calculator_i18n. Defines internationalization functionality.
	 * - Niu_range_calculator_Admin. Defines all hooks for the admin area.
	 * - Niu_range_calculator_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-niu_range_calculator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-niu_range_calculator-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-niu_range_calculator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-niu_range_calculator-public.php';

		/**
     	* Custom Post Types
     	*/
    	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-niu_range_calculator-post_types.php';

		$this->loader = new Niu_range_calculator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Niu_range_calculator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Niu_range_calculator_i18n();

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

		$plugin_admin = new Niu_range_calculator_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$plugin_post_types = new Niu_range_calculator_Post_Types();

		/**
		 * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
		 * so hooking into init from the activation hook won't do anything.
		 * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
		 * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
		 * loader on the init hook, and also re-register it within the activation function and
		 * call flush_rewrite_rules() to add the CPT rewrite rules.
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
		 */
		$this->loader->add_action( 'init', $plugin_post_types, 'create_custom_post_type', 999 );

		if( function_exists('acf_add_options_page') ) {
			acf_add_options_page(array(
				'page_title' 	=> 'Range Calculator Settings',
				'menu_title'	=> 'Range Calculator Settings',
				'menu_slug' 	=> 'range_calculator_settings',
				'capability'	=> 'edit_posts',
				'parent_slug'    => 'edit.php?post_type=scooters',
				'redirect'		=> false
			));
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Niu_range_calculator_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Register shortcode via loader
		 *
		 * Use: [short-code-name args]
		 *
		 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/262
		 */
		$this->loader->add_shortcode( "range-calculator-form", $plugin_public, "range_calculator_form", $priority = 10, $accepted_args = 2 );
		//This actions for showing scooter image
		$this->loader->add_action('wp_ajax_scooter_modal_image_ajax', $plugin_public, 'scooter_modal_image_ajax');
		$this->loader->add_action('wp_ajax_nopriv_scooter_modal_image_ajax', $plugin_public, 'scooter_modal_image_ajax');
		
		//THis actions for showing range result
		//This actions for showing scooter image
		$this->loader->add_action('wp_ajax_range_calc_function', $plugin_public, 'range_calc_function');
		$this->loader->add_action('wp_ajax_nopriv_range_calc_function', $plugin_public, 'range_calc_function');

		//This Filter added for modify the filter to show correct results
		$this->loader->add_filter('posts_where', $plugin_public, 'niu_scooters_where');
		
		// This actions for change scooter modal based on category
		$this->loader->add_action( 'wp_ajax_nopriv_scooter_modal', $plugin_public, 'scooter_modal' );
		$this->loader->add_action( 'wp_ajax_scooter_modal', $plugin_public, 'scooter_modal' );

		//This actions for change state based on country
		$this->loader->add_action('wp_ajax_state_populate_based_on_country', $plugin_public, 'state_populate_based_on_country');
		$this->loader->add_action('wp_ajax_nopriv_state_populate_based_on_country', $plugin_public, 'state_populate_based_on_country');

		//This actions for change temp based on switch
		$this->loader->add_action('wp_ajax_temp_change_switch', $plugin_public, 'temp_change_switch');
		$this->loader->add_action('wp_ajax_nopriv_temp_change_switch', $plugin_public, 'temp_change_switch');
		
		//This actions for change Weight based on switch
		$this->loader->add_action('wp_ajax_weight_change_switch', $plugin_public, 'weight_change_switch');
		$this->loader->add_action('wp_ajax_nopriv_weight_change_switch', $plugin_public, 'weight_change_switch');
	
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
	 * @return    Niu_range_calculator_Loader    Orchestrates the hooks of the plugin.
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
