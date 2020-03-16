<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.niu.com/en/
 * @since      1.0.0
 *
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/public
 * @author     NIU <info@niu.com>
 */
class Niu_range_calculator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Niu_range_calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Niu_range_calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/niu_range_calculator-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Niu_range_calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Niu_range_calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/niu_range_calculator-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'wp_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			/**
			 * Create nonce for security.
			 *
			 * @link https://codex.wordpress.org/Function_Reference/wp_create_nonce
			 */
			'_nonce' => wp_create_nonce( 'scooter-nonce' ),
	
		) );

	}

	public function range_calculator_form( $atts ) {

		$args = shortcode_atts(
			array(
				'form-title'   => '',
				'form-description' => '',
				'enable-mailchimp-form'	=> '', 			
			),
			$atts
		);
	
		$form_title = $args['form-title'] != ""  ? $args['form-title'] : 'Range Calculator';
		$form_description = $args['form-description'] != ""  ? $args['form-description'] : 'This is form description.';
		$enable_mailchimp_form = $args['enable-mailchimp-form'] != ""  ? $args['enable-mailchimp-form'] : 'false';

		ob_start();
		include_once( 'partials/' . $this->plugin_name . '-form-display.php' );
		$output = ob_get_clean();	
	
		return $output;
	}

	// AJAX Callback function for scooter Image
	public function scooter_modal_image_ajax() {

		//echo '<pre>'; print_r($_POST); echo '</pre>'; exit;

		/**
		 * Do not forget to check your nonce for security!
		 *
		 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
		 */
		if( ! wp_verify_nonce( $_POST['nonce'], 'scooter-nonce' ) ) {
			wp_send_json_error();
			die();
		}
		
		// The rest of the function that does actual work.

		$scooter_data = array();

		/**
		 * Code to handle POST request...
		 *
		 * See tutorial file:
		 * handling_POST_request.php
		 */

		// Eg.: get POST value
		$scooter_id = sanitize_text_field( $_POST["scooter_id"] );

		// Eg.: custom Loop for Custom Post Type
		$args = array(
			'post_type' => 'scooters',
			'post_status' => 'publish', 
			'post__in' => array($scooter_id), 			
		);
		$scooters = new WP_Query( $args );
		if ( $scooters->have_posts() ) :
		while( $scooters->have_posts() ): $scooters->the_post();
			$scooters_img_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', true );
			if(has_post_thumbnail()){
				$scooter_data['html'] = '<img src="'.$scooters_img_url[0].'" alt="'.get_the_title().'" />';
				$ret['type']    = 'success';
			}else{
				$scooter_data['html'] = '<img src="https://via.placeholder.com/600x600" alt="'.get_the_title().'" />';
				$ret['type']    = 'error';
			}
         
        $all_modes = get_field('scooter_speeds');
		$i = 1; 
			if($all_modes){ 
				
				$scooter_data['mode'] = '<div class="scooter_modes_wrapper">
					<label>Select Scooters Driving Mode:</label>
                    <div class="scooter-mode-sec">
					<div class="range">
					 <input id="scooter_modes_selected" type="range" min="1" max="'.sizeof($all_modes).'" steps="1" value="1">
					</div>
					<ul class="range-labels">';

					foreach($all_modes as $mode) {
							if($i == 1){
								$selected_classes = 'active selected';
							}else{
								$selected_classes = ' ';
							}    
						
						$scooter_data['mode'] .= '<li class="'.$selected_classes.'" data-temp="'.$mode['scooter_modes'].'">
						<div>'.$mode['scooter_modes'].'</div>
						<div>'.$mode['top_scooter_speed'].' km/h</div>
						</li>';
					  $i++;
					}
					
				 $scooter_data['mode'] .= '</ul></div></div>';
			}
		endwhile;
		else: 
			$scooter_data['html'] = 'No Image Found';
			$ret['type']    = 'error';
		endif;
		wp_reset_query();

		die( json_encode( $scooter_data ) );

	}


	// AJAX Callback function for scooter Image
	public function range_calc_function() {

		// echo '<pre>'; print_r($_POST); echo '</pre>';

		/**
		 * Do not forget to check your nonce for security!
		 *
		 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
		 */
		if( ! wp_verify_nonce( $_POST['scooter-nonce'], 'scooter-nonce' ) ) {
			wp_send_json_error();
			die();
		}
		
		// The rest of the function that does actual work.

		$img_html;

		/**
		 * Code to handle POST request...
		 *
		 * See tutorial file:
		 * handling_POST_request.php
		 */

		// Eg.: get POST value
		$scooter_id = sanitize_text_field( $_POST["scooters_modals"] );
		$driving_modes = sanitize_text_field( $_POST["driving_modes"] );
		$scooter_temps = sanitize_text_field( $_POST["scooter_temps"] );
		$scooter_drive_weight = sanitize_text_field( $_POST["driver_weight"] );
		$contries_calc_option = sanitize_text_field( $_POST["contries_calc_option"] );
		$state_calc_option = sanitize_text_field( $_POST["state_calc_option"] );

		// Eg.: custom Loop for Custom Post Type
		$args = array(
			'post_type' => 'scooters',
			'post_status' => 'publish', 
			'post__in' => array($scooter_id), 
			'suppress_filters' => false,
			'meta_query'	=> array(
				'relation'		=> 'OR',
				array(
					'key' => 'scooter_data_$_temperature',
					'value'    => $scooter_temps,
					'compare'    => '='
				),
				array(
					'key' => 'scooter_data_$_weight',
					'value'    => $scooter_drive_weight,
					'compare'    => '='
				),
			)			
		);
		$scooters = new WP_Query( $args );

		if ( $scooters->have_posts() ) {
		$result_flag = false;
		while( $scooters->have_posts() ): $scooters->the_post();
			$sc_options=  get_field('scooter_data');
			if($sc_options) {
				foreach($sc_options as $row)
				{   
					if($row['temperature'] == $scooter_temps &&  $row['weight'] == $scooter_drive_weight && in_array($driving_modes, $row['gears'])) {
						$kw_val= get_field('kw_value');
						$img_html = "<div>";
                        $img_html .= '<label class="switch">
                            <input class="switch-input range-dist-input" id="dist_switch" type="checkbox" />
                            <span class="switch-label" id="dist_switch_label" data-on="km" data-off="miles"></span> 
                            <span class="switch-handle"></span> 
                        </label>';
						$img_html .= "<h3>Real Range: ".$row['range']."km</h3>";

						$countrys = get_field('price_calculator_options','option'); 
						$result_flag = true;
						$currency;
                        $countryName;
						if( $countrys ){
                            $countryName = ucwords(str_replace('_', ' ', $contries_calc_option));
							foreach($countrys as $country):
								$country_name = $country['country'];
								
                            	
								if($country_name == $contries_calc_option) {
                                    
                                    $currency = $country['currency'];
									$country_per_kw_price;
                                   
									if($state_calc_option) {
										if($country['usa_states'] == $state_calc_option) {
											$country_per_kw_price = $country['country_unit_price_per_kw'];
                                            $countryName = ucwords(str_replace('_', ' ', $state_calc_option)).', '.ucwords(str_replace('_', ' ', $contries_calc_option));
										}
									} else {
										$country_per_kw_price = $country['country_unit_price_per_kw'];
									}
								}
							endforeach;
						}
						if($country_per_kw_price > 0) {
							$total_charge =(float) $kw_val * (float)$country_per_kw_price;
							$img_html .= "<h3>Based on the the average electricity price in ".$countryName." one full charge will only cost you ".$currency.' '. round($total_charge, 2) ."!</h3>";
						} else {
							$img_html .= "<h3>Country Price per KW not set in option</h3>";
						}
						
						$img_html .= "</div>";
					} else {
						if($result_flag !== true) {
							$result_flag = false;
						}
					}

				}
			}
		endwhile;
			if($result_flag == false) {
				$img_html = 'No Results Found';	
			}
	} else {
			$img_html = 'No Results Found';			
	}
		wp_reset_query();

		die( $img_html );

	}

	/* This is the function to alter query for range calculation query results */
	public function niu_scooters_where( $where ) {
	
		$where = str_replace("meta_key = 'scooter_data_$", "meta_key LIKE 'scooter_data_%", $where);
	
		return $where;
	}

	// AJAX Callback function for scooter Image
	public function scooter_modal() {
		$scooter_mode = $_POST['scooter_cat_id'];
		$args = array(
			'post_type' => 'scooters',
			'post_status' => 'publish', 
			'posts_per_page' => -1,
			'order' => 'DESC', 
			'tax_query' => array(
				array(
					'taxonomy' => 'scooter_modes',
					'field' => 'slug',
					'terms' => array( $scooter_mode ),            
				),
			),  
		);
		$scooters = get_posts( $args );
		echo json_encode( $scooters );
		exit;
	}
	// AJAX callback function for USA state
	public function state_populate_based_on_country() {
		//$state_for_frontend_form = get_field_object('field_5e4927b5fc545'); 
		$state_for_frontend_form = get_field_object('field_5e492f29f1241'); 
		  
		$states = $state_for_frontend_form['choices'];
		$state_data;   
		if( $states ){
			$state_data .= '<label>Select your State:</label><select name="state_calc_option">';
			$state_data .=	'<option>Select State</option>';
			foreach($states as $k => $v) {
				$state_data .= '<option value='.$k.'>'.$v.'</option>';
			}
			$state_data .= '</select>';
		}

		echo json_encode( $state_data );
		exit;
	}

	// AJAX callback function for temp change
	public function temp_change_switch(){
		$temperature_form_values = get_field_object('field_5e41800ea0996');   
		$temperature_choices = $temperature_form_values['choices'];
		$temperature_choices_html = '';		  
		$j = 1; 
		if( $temperature_choices ){ 	
					
		$temperature_choices_html .= '<ul class="temp-range-labels">';
				foreach($temperature_choices as $k => $v){
				if($j == 1){
					$selected_classes = "active selected";
				}else{
					$selected_classes = " ";
				} 
				if($_POST["outside_temp"] == "Fahrenheit"){
					$v_val = $v*9/5+32;
					$v_val_str = $v_val." °F";
				}else{
					$v_val = $v;
					$v_val_str = $v_val." °C";
				}
				$temperature_choices_html .= '<li class="'.$selected_classes.'" data-temp="'.$v.'">'.$v_val_str.'</li>';
				$j++; }
				$temperature_choices_html .= '</ul>';
		} 
		echo json_encode( $temperature_choices_html );			
		exit;
	}

	// AJAX callback function for Weight unit change
	public function weight_change_switch(){
		$scooter_weight_frontend_options = get_field_object('field_5e418017a0997');   
		$scooter_weight_choices = $scooter_weight_frontend_options['choices'];
		
		$weight_choices_html = '';	
		if( $scooter_weight_choices ){ 
		$l = 1;						
		$weight_choices_html .= '<ul class="weight-range-labels">';
			foreach($scooter_weight_choices as $k => $v){
				if($l == 1){
					$selected_classes = "active selected";
				}else{
					$selected_classes = " ";
				} 
				if($_POST["driver_wight_unit"] == "LBS"){
					$weight_val = round($k*2.2046);
					$weight_val_str = $weight_val." LBS";
				}else{
					$weight_val = $k;
					$weight_val_str = $weight_val." KG";
				}
				$weight_choices_html .= '<li class="'.$selected_classes.'" data-temp="'.$k.'">'.$weight_val_str.'</li>';
				$l++; 
			}
				$weight_choices_html .= '</ul>';
		} 
		echo json_encode( $weight_choices_html );			
		exit;
	}
}