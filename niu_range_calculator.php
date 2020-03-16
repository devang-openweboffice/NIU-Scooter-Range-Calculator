<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.niu.com/en/
 * @since             1.0.0
 * @package           Niu_range_calculator
 *
 * @wordpress-plugin
 * Plugin Name:       NIU Range Calculator
 * Plugin URI:        https://www.niu.com/en/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            NIU
 * Author URI:        https://www.niu.com/en/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       niu_range_calculator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if( !class_exists('acf') )
{
	$tp_acf_notice_msg = __( 'This plugin needs "Advanced Custom Fields Pro" to run. Please download and activate it', 'tp-notice-acf' );
	
	/*
	*	Admin notice
	*/
	add_action( 'admin_notices', 'tp_notice_missing_acf' );
	function tp_notice_missing_acf()
	{
		global $tp_acf_notice_msg;
		
		echo '<div class="notice notice-error notice-large"><div class="notice-title">'. $tp_acf_notice_msg .'</div></div>';
	}
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NIU_RANGE_CALCULATOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-niu_range_calculator-activator.php
 */
function activate_niu_range_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-niu_range_calculator-activator.php';
	Niu_range_calculator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-niu_range_calculator-deactivator.php
 */
function deactivate_niu_range_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-niu_range_calculator-deactivator.php';
	Niu_range_calculator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_niu_range_calculator' );
register_deactivation_hook( __FILE__, 'deactivate_niu_range_calculator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-niu_range_calculator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_niu_range_calculator() {

	$plugin = new Niu_range_calculator();
	$plugin->run();

}
run_niu_range_calculator();


/**
 * Use radio inputs instead of checkboxes for term checklists in specified taxonomies.
 *
 * @param   array   $args
 * @return  array
 */
function wpse_139269_term_radio_checklist( $args ) {
    if ( ! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'scooter_modes' /* <== Change to your required taxonomy */ ) {
        if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) { // Don't override 3rd party walkers.
            if ( ! class_exists( 'WPSE_139269_Walker_Category_Radio_Checklist' ) ) {
                /**
                 * Custom walker for switching checkbox inputs to radio.
                 *
                 * @see Walker_Category_Checklist
                 */
                class WPSE_139269_Walker_Category_Radio_Checklist extends Walker_Category_Checklist {
                    function walk( $elements, $max_depth, ...$args ) {
                        $output = parent::walk( $elements, $max_depth, ...$args );
                        $output = str_replace(
                            array( 'type="checkbox"', "type='checkbox'" ),
                            array( 'type="radio"', "type='radio'" ),
                            $output
                        );

                        return $output;
                    }
                }
            }

            $args['walker'] = new WPSE_139269_Walker_Category_Radio_Checklist;
        }
    }

    return $args;
}

add_filter( 'wp_terms_checklist_args', 'wpse_139269_term_radio_checklist' );