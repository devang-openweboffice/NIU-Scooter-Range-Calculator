(function( $ ) {
	'use strict';

	//AJAX Function for Scooter Image
	$('#scooters_modals_mode3').on('change', function() {
		var scooter_id =  this.value;
		var scooter_nonce = $('#scooter_nonce').val();
		$('.btx-page-load').removeClass("loaded");
		$.ajax({
			type : "post",
			dataType : "json",
			url : wp_ajax.ajax_url,
			data : {action: "scooter_modal_image_ajax", scooter_id: scooter_id, nonce: scooter_nonce},
			success: function(response) {
				$('.btx-page-load').addClass("loaded");
				$('.scooters_modals_img').html(response.html);
				$('.scooter_modes_sec').empty();
				$('.scooter_modes_sec').html(response.mode);
				commonSlider("range", "range-labels", $(".driving_modes"));		
				$(".scooter_modes_sec .range-labels li").first().click();
				getScooterCalc();
			},
			error: function( xhr, status, error ) {
                console.log( 'Status: ' + xhr.status );
                console.log( 'Error: ' + xhr.responseText );
            }
		 });
	});

	//AJAX Function for state populate
	$('#contries_calc_option').on('change', function() {
		var country_val =  this.value;
		$('.btx-page-load').removeClass("loaded");
		if(country_val == "united_states") {
			$.ajax({
				type : "post",
				dataType : "json",
				url : wp_ajax.ajax_url,
				data : {action: "state_populate_based_on_country", country_val: country_val},
				success: function(response) {
					$('#state_data_wrapper').html(response);
				},
				error: function( xhr, status, error ) {
					console.log( 'Status: ' + xhr.status );
					console.log( 'Error: ' + xhr.responseText );
				}
			 });   
		} else {
			$('#state_data_wrapper').empty();
			getScooterCalc();
		}
	});
	
	//AJAX Function for state onchange
	$(document).on('change','#state_data_wrapper select',function(){
		if(this.val !== "Select State") {
			getScooterCalc();
		}
	});

	// //AJAX Function for scooter range result
	// $( document ).on( 'submit', '#ranage-calculator', function( event ) {
	// 	event.preventDefault();

	// 	var form = document.getElementById('ranage-calculator');
	// 	var formData = new FormData( form );

    //     // From the wp_ajax_prefix_ajax_first hook
	// 	formData.append( 'action', 'range_calc_function' );
    //     $.ajax({
    //         cache: false,
    //         type: "POST",
    //         url: wp_ajax.ajax_url,
    //         data: formData,
    //         processData: false, // Required for file upload
    //         contentType: false, // Required for file upload
    //         success: function( response ){
	// 			$('.range-data-wrapper').html(response);
    //             setSwitchState($('.range-dist-input'), true);
    //             $('#range_calc_mailchimp_sucess').show();
    //         },
    //         error: function( xhr, status, error ) {
    //             console.log( 'Status: ' + xhr.status );
    //             console.log( 'Error: ' + xhr.responseText );
    //         }
    //     });
	// });

	/* function for final ajax call */
	function getScooterCalc() {
		var modal = document.getElementById("scooters_modals_mode3").value;
		var mode = document.getElementsByClassName("driving_modes")[0].value;
		var temp = document.getElementsByClassName("scooter_temps")[0].value;
		var weight = document.getElementsByClassName("driver_weight")[0].value;
		var nonce = document.getElementById("scooter_nonce").value;
		var country = document.getElementById("contries_calc_option").value;

		if ((modal == '' || mode == 'Select Scooters') || temp == '' || weight == '' || nonce == '' || (country == '' || country == "Select Country"))          {
			
		} else {
		// AJAX code to submit form.
			var form = document.getElementById('ranage-calculator');
			var formData = new FormData( form );

			// From the wp_ajax_prefix_ajax_first hook
			formData.append( 'action', 'range_calc_function' );
			$.ajax({
				cache: false,
				type: "POST",
				url: wp_ajax.ajax_url,
				data: formData,
				processData: false, // Required for file upload
				contentType: false, // Required for file upload
				success: function( response ){
                    $('.btx-page-load').addClass("loaded");
					$('.scooter-range-data-result').html(response);
					setSwitchState($('.range-dist-input'), true);
					$('#range_calc_mailchimp_sucess').show();
				},
				error: function( xhr, status, error ) {
					console.log( 'Status: ' + xhr.status );
					console.log( 'Error: ' + xhr.responseText );
				}
			});
		}
		return false;
	}

	commonSlider("range", "range-labels", $(".driving_modes"));
	commonSlider("temp_range", "temp-range-labels", $(".scooter_temps"));
	commonSlider("weight_range", "weight-range-labels", $(".driver_weight"));

	/* JS Function for Modes custom slider start here */
	function commonSlider(rangeInput, rangeLabels, inputElm) {
		var sheet = document.createElement("style"),
		prefs = ["webkit-slider-runnable-track", "moz-range-track", "ms-track"];

		document.body.appendChild(sheet);

		var getTrackStyle = function(el, rInput, rLabels, itemW) {
			var curVal = el.value,
				val = (curVal - 1) * itemW,
				style = "";

			// Set active label
			$(`.${rLabels}`).find("li").removeClass("active selected");
			var curLabel = $(`.${rLabels}`).find("li:nth-child(" + curVal + ")");
			curLabel.addClass("active selected");
			curLabel.prevAll().addClass("selected");

			// Change background gradient
			for (var i = 0; i < prefs.length; i++) {
				style +=
				`.${rInput} {background: linear-gradient(to right, #df001f 0%, #df001f ` +
				val +
				"%, #fff " +
				val +
				"%, #fff 100%)}";
				style +=
				`.${rInput} input::-` +
				prefs[i] +
				"{background: linear-gradient(to right, #df001f 0%, #df001f " +
				val +
				"%, #b2b2b2 " +
				val +
				"%, #b2b2b2 100%)}";
			}
			return style;
		};


		$(`.${rangeInput} input`).on("input", function() {
			var valData = $(`.${rangeLabels}`).find( "li" ).eq( this.value - 1 ).data('temp');
			inputElm.val(valData).trigger('input');
			sheet.textContent = getTrackStyle(this, rangeInput, rangeLabels, 100/($(`.${rangeLabels}`).find( "li" ).length - 1));
			getScooterCalc();
		});

		// Change input value on label click
		$(`.${rangeLabels}`).find("li").on("click", function() {
			var index = $(this).index();
			$(`.${rangeInput} input`).val(index + 1).trigger("input");
			inputElm.val($(this).data('temp')).trigger('input');
			getScooterCalc();
		});
	}

	/* JS Function for temprature custom slider end here  */

	/* JS Function for temprature switch button */
	$('#temp_switch').on('change', function() {
		var isChecked = $(this).is(':checked');
		var selectedData;
		var $switchLabel = $('#temp_switch_label');

		if(isChecked) {
		  selectedData = $switchLabel.attr('data-on');
		} else {
		  selectedData = $switchLabel.attr('data-off');
		}
		changeTemp(selectedData);
        /* modeskmtomiles(selectedData); */
		
	});
	  //AJAX Function to change temprature on switch button
	function changeTemp(selectedData) {
		$.ajax({
			type : "post",
			dataType : "json",
			url : wp_ajax.ajax_url,
			data : {action: "temp_change_switch", outside_temp : selectedData },
			success: function(response) {
                $(".temp-range-labels li").first().click();
				$('.temp-range-wrapper').html(response);
				commonSlider("temp_range", "temp-range-labels", $(".scooter_temps"));
                changeKmtoMilesOnChangSlider();
			},
			error: function( xhr, status, error ) {
				console.log( 'Status: ' + xhr.status );
				console.log( 'Error: ' + xhr.responseText );
			}
		});   
	}

	/* JS Function for Weight switch button */
	$('#weight_switch').on('change', function() {
		var isChecked = $(this).is(':checked');
		var selectedData;
		var $switchLabel = $('#weight_switch_label');
		//console.log('isChecked: ' + isChecked); 
		if(isChecked) {
		  selectedData = $switchLabel.attr('data-on');
		} else {
		  selectedData = $switchLabel.attr('data-off');
		}
		changeWeight(selectedData);
		/* modeskmtomiles(selectedData); */
		
	});
	  //AJAX Function to change Weight on switch button
	function changeWeight(selectedData) {
		$.ajax({
			type : "post",
			dataType : "json",
			url : wp_ajax.ajax_url,
			data : {action: "weight_change_switch", driver_wight_unit : selectedData },
			success: function(response) {
                $(".weight-range-labels li").first().click();
				$('.weight-range-wrapper').html(response);
				commonSlider("weight_range", "weight-range-labels", $(".driver_weight"));
                changeKmtoMilesOnChangSlider();
			},
			error: function( xhr, status, error ) {
				console.log( 'Status: ' + xhr.status );
				console.log( 'Error: ' + xhr.responseText );
			}
		});   
	}
	  
	// Params ($selector, boolean)
	function setSwitchState(el, flag) {
		el.attr('checked', flag);
	}
	
	// Usage
	setSwitchState($('.switch-input'), true);
    
    //  range change from km to miles
	$('.scooter-range-data-result').on('change', '#dist_switch', function() {
		var isChecked = $(this).is(':checked');
		var selectedData;
		var $switchLabel = $('#dist_switch_label');
		//console.log('isChecked: ' + isChecked); 

		if(isChecked) {
		  selectedData = $switchLabel.attr('data-on');
		} else {
		  selectedData = $switchLabel.attr('data-off');
		}

		var rangeLi = $('.scooter-range-data-result h3').first();
		var range = rangeLi.text().match(/\d+/);
		var totalRange;
		if(selectedData == "miles") {
			totalRange = `Real Range: ${Math.round(range*0.62137)}miles`;
		} else {
			totalRange = `Real Range: ${Math.round(range/0.62137)}km`;
		}
		rangeLi.empty();
		rangeLi.append(totalRange);
		modeskmtomiles(selectedData);
	});
    
    function changeKmtoMilesOnChangSlider() {
		var selectedData;
		var isChecked = $('#dist_switch').is(':checked');
		var $switchLabel = $('#dist_switch_label');
		if(isChecked) {
			selectedData = $switchLabel.attr('data-on');
		  } else {
			selectedData = $switchLabel.attr('data-off');
		  }
		
		if(selectedData == "miles") {
			modeskmtomiles('km');
		}
	}
    
    // function for replace mode km to miles and vice-versa
    function modeskmtomiles(selectedData) {
		$(".range-labels").find("li").each(function()
		{
		   var $li=$(this);           
		   var rangeLi = $li.find('div:last-child');
			var range = rangeLi.text().match(/\d+/);
			var totalRange;
			if(selectedData == "miles") {
				totalRange = `${Math.round(range*0.62137)} miles/h`;
			} else {
				totalRange = `${Math.round(range/0.62137)} km/h`;
			}
			rangeLi.empty();
			rangeLi.append(totalRange);	
		});
	}


})( jQuery );