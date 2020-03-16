<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.niu.com/en/
 * @since      1.0.0
 *
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/includes
 * @author     NIU <info@niu.com>
 */
class Niu_range_calculator_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		/**
		 * This only required if custom post type has rewrite!
		 */
		flush_rewrite_rules();

	}

}
