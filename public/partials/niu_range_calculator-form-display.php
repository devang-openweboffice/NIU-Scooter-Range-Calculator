<?php

/**
 * Provide a Public area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.niu.com/en/
 * @since      1.0.0
 *
 * @package    Niu_range_calculator
 * @subpackage Niu_range_calculator/admin/partials
 */
?>
<?php 
$nonce = wp_create_nonce("scooter-nonce");
$args = array(
    'post_type' => 'scooters',
    'post_status' => 'publish', 
    'posts_per_page' => -1,
    'order' => 'DESC',
);
$scooters = new WP_Query( $args );
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<form id="ranage-calculator" class="range-calc-form" action="" method="post">
    <h2><?php echo $form_title; ?></h2>
    <p><?php echo $form_description; ?></p>
<div class="scooter-filter-opt">
<div class="scooter-modal-select">
    <!-- Scooter modals dropdown start here -->
    <?php 
    if ( $scooters->have_posts() ) {
    ?>
    <fieldset class="scooter_modal_dropdown">
        <label>SELECT SCOOTER MODEL:</label>
        <select id="scooters_modals_mode3" name="scooters_modals" required>
            <option value="">Select Scooters</option>
            <?php while ( $scooters->have_posts() ) {  $scooters->the_post(); ?>
            <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
            <?php } ?>
        </select>
        <div class="scooters_modals_img" id="scooters_modals_img"></div>
    </fieldset>
    <?php } wp_reset_postdata(); ?>  
    <!-- Scooter modals dropdown end here -->  

    
     <!-- Scooter Mode field start here  field_5e4435d2748bc-->
     <?php 
    $scooter_modes_form_values = get_field_object('field_5e4e0ebddea06');   
    $scooter_modes = $scooter_modes_form_values['choices'];   
    $i = 1; 
    if( $scooter_modes ){ ?>
        <fieldset class="scooter_modes scooter_modes_sec">
            <label>Select Scooters Driving Mode:</label>
                <div class="scooter-mode-sec">
            <div class="range">
                <input id="scooter_modes_selected" type="range" min="1" max="3" steps="1" value="1">
            </div>
            
            <ul class="range-labels">
                <?php foreach( $scooter_modes as $k => $v ) { 
                    if($i == 1){
                        $selected_classes = 'active selected';
                    }else{
                        $selected_classes = ' ';
                    }    
                ?>
                <li class="<?php echo $selected_classes; ?>" data-temp="<?php echo $v; ?>"><?php echo $v; ?></li>
                <?php $i++; } ?>          
            </ul></div>
        </fieldset>
    <?php } ?>
    <!-- Scooter Mode field end here -->  
</div>
<div class="scooter-option-filter">
        <div class="scooter-temp">
    <!-- Scooter Temperature change switch button field start here -->
    <label class="switch">
        <input class="switch-input" id="temp_switch" type="checkbox" />
        <span class="switch-label" id="temp_switch_label" data-on="Celcius" data-off="Fahrenheit"></span> 
        <span class="switch-handle"></span> 
    </label>
    <!-- Scooter Temperature field end here -->
    
    <!-- Scooter Temperature field start here  field_5e4435d2748bc-->
    <?php 
    $temperature_form_values = get_field_object('field_5e41800ea0996');   
    $temperature_choices = $temperature_form_values['choices'];   
    $j = 1; 
    if( $temperature_choices ){ ?>
    <fieldset class="scooter_modes">
        <label>Outside Temperature:</label>
        <div class="range-data-wrapper">
            <div class="temp_range">
                <input id="scooter_temps_selected" type="range" min="1" max="9" steps="1" value="1">
            </div>
            <div class="temp-range-wrapper">
                <ul class="temp-range-labels">
                 <?php foreach($temperature_choices as $k => $v){
                    if($j == 1){
                        $selected_classes = 'active selected';
                    }else{
                        $selected_classes = ' ';
                    }    
                    ?>
                        <li class="<?php echo $selected_classes; ?>" data-temp="<?php echo $v;?>"><?php echo $v.' °C'; ?></li>
                            <?php $j++; } ?>          
                </ul>
             </div>
        </div>
    </fieldset>
    <?php } ?>
    <!-- Scooter Temperature field end here -->  
        </div>

        <div class="scooter-weight">
    <!-- Scooter Weight change switch button field start here -->
    <label class="switch">
        <input class="switch-input" id="weight_switch" type="checkbox" />
        <span class="switch-label" id="weight_switch_label"  data-on="KG" data-off="LBS"></span> 
        <span class="switch-handle"></span> 
    </label>
    <!-- Scooter Weight field end here -->

   <!-- Scooter driver weight slider start here -->
    <?php 
    $scooter_weight_frontend_options = get_field_object('field_5e418017a0997');   
    $scooter_weight_choices = $scooter_weight_frontend_options['choices']; 
    
    if( $scooter_weight_choices ):     
    $l = 1;
    ?>
    <fieldset class="scooter_driving_weight">
        <label>Driver Weight:</label>
            <div class="range-data-wrapper">
        <div class="weight_range">
            <input id="weight_range_selected" type="range" min="1" max="<?php echo count($scooter_weight_choices); ?>" steps="1" value="1">
        </div>
        <div class="weight-range-wrapper">
            <ul class="weight-range-labels">
                <?php  foreach($scooter_weight_choices as $k => $v):            
                if($l == 1){
                    $selected_classes = 'active selected';
                }else{
                    $selected_classes = ' ';
                }             
                ?>            
                <li class="<?php echo $selected_classes; ?>" data-temp="<?php echo $v;?>"><?php echo $v.' KG'; ?></li>
                <?php $l++; endforeach; ?>
            </ul>
        </div>
                    </div>
    </fieldset>
    <?php endif; ?>
    <!-- Scooter driver weight slider end here -->  
</div>
        <div class="country-wrapper">
    <!-- Contry dropdown start here -->           
    <?php 
    $contries_for_frontend_form = get_field_object('field_5e441f0b7266c');   
    $contries = $contries_for_frontend_form['choices'];    
    if( $contries ){ ?>
    <fieldset class="contries_section">
        <label>Your Country:</label>
        <select name="contries_calc_option" id="contries_calc_option" required>
            <option>Select Country</option>
            <?php foreach($contries as $k => $v): ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
    </fieldset>
    <?php } ?>

    <fieldset id="state_data_wrapper">
    </fieldset>
        </div>
    </div>
</div>
    
    <!-- Contry dropdown end here -->       
    <input type="hidden" name="scooter-nonce" id="scooter_nonce" value="<?php echo $nonce; ?>" />
    <input  type="hidden" name="driving_modes" value="mode 1" class="driving_modes">
    <input  type="hidden" name="scooter_temps" value="-10" class="scooter_temps">
    <input  type="hidden" name="driver_weight" value="40" class="driver_weight">
</form>
<div class="scooter-range-data-result"></div>
<?php 
if($enable_mailchimp_form  == 'true'):
    if( have_rows('mailchimp_cta', 'option') ):
        while( have_rows('mailchimp_cta', 'option') ): the_row(); 
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $form_shortcode = get_sub_field('form_shortcode');
    ?>
    <section class="range-calc-mailchimp" id="range_calc_mailchimp_sucess" style="display:none;">
        <div class="range-calc-cta">
            <?php if($title): ?>
            <h2><?php echo $title; ?></h2>
            <?php endif; ?>
            <?php if($description): ?>
            <p><?php echo $description; ?></p>
            <?php endif; ?>
            <?php if($form_shortcode): ?>
            <div class="range-calc-form-wrapper">
                <?php echo do_shortcode( $form_shortcode ); ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endwhile;  endif; ?>
<?php endif; ?>