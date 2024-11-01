;(function($){
	$(document).ready(function() {
		$(document).on('click', 'a.clear-ev', function(e){
			if(!confirm($(this).data('confirm'))){
				e.stopImmediatePropagation();
				e.preventDefault();
			}
		});
		if(jQuery("#webd_allday input").prop('checked')){
			jQuery('#webd_startdate .exc_mb_timepicker').css('display', 'none');
			jQuery('#webd_enddate .exc_mb_timepicker').css('display', 'none');
		}
		jQuery("#webd_allday input").click(function(){
			if(jQuery('#webd_allday input').prop('checked')){			
				jQuery('#webd_startdate .exc_mb_timepicker').css('display', 'none');
				jQuery('#webd_enddate .exc_mb_timepicker').css('display', 'none');
			}else{
				jQuery('#webd_startdate .exc_mb_timepicker').css('display', 'inline');
				jQuery('#webd_enddate .exc_mb_timepicker').css('display', 'inline');
			}
		});
		jQuery('#webd_startdate #webd_startdate-exc_mb-field-0-date').change(function() {
			jQuery('#webd_enddate #webd_enddate-exc_mb-field-0-date').val(this.value);
		});
		
		jQuery(document).on('change', '#webd_ct_stdate .field-item .exc_mb_datepicker:first-child', function() {
			fieldItem = jQuery(this).closest('.webd_ct_allday' );
			jQuery('#webd_ct_edate_end .field-item .exc_mb_datepicker:first-child', fieldItem).val(this.value);
		});
		jQuery(document).on('click','#webd_ct_allday input',function(){
			fieldItem = jQuery(this).closest('.webd_ct_allday' );
			if(jQuery(this).prop('checked')){
				jQuery('.field-item .exc_mb_timepicker:last-child', fieldItem).css('display', 'none');
			}else{
				jQuery('.field-item .exc_mb_timepicker:last-child', fieldItem).css('display', 'inline');
			}
		});
		if(jQuery('#webd_ct_allday').length){
			jQuery('#webd_ct_allday input').each(function(){
				if(jQuery(this).prop('checked')){
					fieldItem = jQuery(this).closest('.webd_ct_allday' );
					jQuery('.field-item .exc_mb_timepicker:last-child', fieldItem).css('display', 'none');
				}
			});
		}
		
		
		var webd_layout_purpowebd_obj  = jQuery('.postbox-container #webd_layout_purpose select');
		var webd_layout_purpose = jQuery('.postbox-container #webd_layout_purpose select').val();
		var webd_event_settings = jQuery('#event-settings.postbox');
		var webd_location_settings = jQuery('#location-settings.postbox');
		
		var webd_custom_field = jQuery('.post-type-product .postbox-container #custom-field.postbox');
		var webd_sponsor = jQuery('.post-type-product .postbox-container #sponsors-of-event.postbox');
		var webd_layout_set = jQuery('.post-type-product .postbox-container #layout-settings.postbox');

		var webd_label = jQuery('.post-type-product .postbox-container #custom-label.postbox');
		if(typeof(webd_layout_purpose)!='undefined'){
			if(webd_layout_purpose == 'event'){
				webd_event_settings.show();
				webd_event_settings.addClass('active-c');
				
				webd_location_settings.show();
				webd_location_settings.addClass('active-c');
				
				webd_custom_field.show();
				webd_custom_field.addClass('active-c');
				
				webd_sponsor.show();
				webd_sponsor.addClass('active-c');
				
				webd_layout_set.show();
				webd_layout_set.addClass();

				webd_label.show();
				webd_label.addClass('active-c');
			}else if(webd_layout_purpose == 'woo'){
				webd_event_settings.hide();
				webd_event_settings.removeClass('active-c');
				
				webd_location_settings.hide();
				webd_location_settings.removeClass('active-c');
				
				webd_custom_field.hide();
				webd_custom_field.removeClass('active-c');
				
				webd_sponsor.hide();
				webd_sponsor.removeClass('active-c');
				
				webd_layout_set.hide();
				webd_layout_set.removeClass('active-c');

				webd_label.hide();
				webd_label.removeClass('active-c');
			}
			webd_layout_purpowebd_obj.change(function(event) {
				if(jQuery(this).val() == 'event'){
					webd_event_settings.show(200);
					webd_event_settings.addClass('active-c');
					
					webd_location_settings.show(200);
					webd_location_settings.addClass('active-c');
					
					webd_custom_field.show();
					webd_custom_field.addClass('active-c');
					
					webd_sponsor.show();
					webd_sponsor.addClass('active-c');
					
					webd_layout_set.show();
					webd_layout_set.addClass('active-c');

					webd_label.show();
					webd_label.addClass('active-c');
				}else if(jQuery(this).val() == 'woo'){
					webd_event_settings.hide(200);
					webd_event_settings.removeClass('active-c');
					
					webd_location_settings.hide(200);
					webd_location_settings.removeClass('active-c');
					
					webd_custom_field.hide();
					webd_custom_field.removeClass('active-c');
					
					webd_sponsor.hide();
					webd_sponsor.removeClass('active-c');
					
					webd_layout_set.hide();
					webd_layout_set.removeClass('active-c');

					webd_label.hide();
					webd_label.removeClass('active-c');
				}else if(jQuery(this).val() == 'def'){
					webd_event_settings.css("display","");
					webd_location_settings.css("display","");
					
					webd_custom_field.css("display","");
					webd_sponsor.css("display","");
					webd_layout_set.css("display","");

					webd_label.css("display","");
					
					webd_event_settings.removeClass('active-c');
					webd_location_settings.removeClass('active-c');
					webd_custom_field.removeClass('active-c');
					webd_sponsor.removeClass('active-c');
					webd_layout_set.removeClass('active-c');

					webd_label.removeClass('active-c');
					
				}
			});
		}
		/*--recurrence select--*/
		var webd_recurrence = jQuery('.postbox-container #webd_recurrence select').val();
		var webd_frequency = jQuery('.post-type-product .postbox-container .webd_frequency');
		var webd_ctdate = jQuery('.post-type-product .postbox-container .webd_ctdate');
		var webd_frequency_sl = jQuery('.post-type-product #webd_frequency select').val();
		
		var webd_every_x = jQuery('.post-type-product .postbox-container #webd_every_x');
		var webd_weekday = jQuery('.post-type-product .postbox-container #webd_weekday');
		var webd_monthday = jQuery('.post-type-product .postbox-container #webd_monthday');
		var webd_mweekday = jQuery('.post-type-product .postbox-container #webd_mweekday');
		
		var webd_monthday_sl = jQuery('.post-type-product #webd_monthday select').val();
		if(typeof(webd_recurrence)!='undefined'){
			if(webd_recurrence=='custom'){
				webd_frequency.show(200);
				webd_ctdate.show(200);
				if(webd_frequency_sl=='ct_date'){ 
					webd_ctdate.show(200);
					webd_every_x.hide(200);
					webd_weekday.hide(200);
					webd_monthday.hide(200);
					webd_mweekday.hide(200);
				}else{ 
					webd_ctdate.hide(200);
					if(webd_frequency_sl=='daily'){
						webd_every_x.show(200);
						webd_weekday.hide(200);
						webd_monthday.hide(200);
						webd_mweekday.hide(200);
					}else if(webd_frequency_sl=='week'){
						webd_every_x.show(200);
						webd_weekday.show(200);
						webd_monthday.hide(200);
						webd_mweekday.hide(200);
					}else if(webd_frequency_sl=='month'){
						webd_weekday.hide(200);
						webd_monthday.show(200);
						if(webd_monthday_sl=='first' || webd_monthday_sl=='second' || webd_monthday_sl=='third' || webd_monthday_sl=='fourth' || webd_monthday_sl=='fifth' || webd_monthday_sl=='last'){
							webd_mweekday.show(200);
						}else{ webd_mweekday.hide(200);}
					}
				}
			}else {
				webd_frequency.hide(200);
				webd_ctdate.hide(200);
			}
		}
		jQuery('.postbox-container #webd_recurrence select').change(function(event) {
			if(jQuery(this).val() == 'custom'){
				webd_frequency.show(200);
				webd_ctdate.show(200);
				if(webd_frequency_sl=='ct_date'){ 
					webd_ctdate.show(200);
					webd_every_x.hide(200);
					webd_weekday.hide(200);
					webd_monthday.hide(200);
					webd_weekday.hide(200);
				}else{ 
					webd_ctdate.hide(200);
					if(webd_frequency_sl=='daily'){
						webd_every_x.show(200);
						webd_weekday.hide(200);
						webd_monthday.hide(200);
						webd_mweekday.hide(200);
					}else if(webd_frequency_sl=='week'){
						webd_every_x.show(200);
						webd_weekday.show(200);
						webd_monthday.hide(200);
						webd_mweekday.hide(200);
					}else if(webd_frequency_sl=='month'){
						webd_weekday.hide(200);
						webd_monthday.show(200);
						if(webd_monthday_sl=='first' || webd_monthday_sl=='second' || webd_monthday_sl=='third' || webd_monthday_sl=='fourth' || webd_monthday_sl=='fifth' || webd_monthday_sl=='last'){
							webd_mweekday.show(200);
						}else{ webd_mweekday.hide(200);}
					}
				}
			}else {
				webd_frequency.hide(200);
				webd_ctdate.hide(200);
			}
		});
		jQuery('.post-type-product #webd_frequency select').change(function(event) {
			var webd_frequency_ch = jQuery(this).val();
			if(webd_frequency_ch=='ct_date'){ 
				webd_ctdate.show(200);
				webd_every_x.hide(200);
				webd_weekday.hide(200);
				webd_monthday.hide(200);
				webd_mweekday.hide(200);
			}else{ 
				webd_ctdate.hide(200);
				webd_every_x.show(200);
				if(webd_frequency_ch=='daily'){
					webd_weekday.hide(200);
					webd_monthday.hide(200);
					webd_mweekday.hide(200);
				}else if(webd_frequency_ch=='week'){
					webd_weekday.show(200);
					webd_monthday.hide(200);
					webd_mweekday.hide(200);
				}else if(webd_frequency_ch=='month'){
					webd_weekday.hide(200);
					webd_monthday.show(200);
					if(webd_monthday_sl=='first' || webd_monthday_sl=='second' || webd_monthday_sl=='third' || webd_monthday_sl=='fourth' || webd_monthday_sl=='fifth' || webd_monthday_sl=='last'){
						webd_mweekday.show(200);
					}else{ webd_mweekday.hide(200);}
				}
			}
		});
		jQuery('.post-type-product #webd_monthday select').change(function(event) {
			var webd_monthday_ch = jQuery(this).val();
			if(webd_monthday_ch=='first' || webd_monthday_ch=='second' || webd_monthday_ch=='third' || webd_monthday_ch=='fourth' || webd_monthday_ch=='fifth' || webd_monthday_ch=='last'){
				webd_mweekday.show(200);
			}else{ webd_mweekday.hide(200);}
		});
		/*- Default venue -*/
		jQuery('.postbox-container #webd_default_venue #webd_default_venue-exc_mb-field-0').change(function() {
			$('#location-settings').addClass('loading');
			if(!$('#location-settings .wpex-loading').length){
				$('#location-settings').prepend('<div class="wpex-loading"><div class="wpex-spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div></div>');
			}
			var valu = jQuery(this).val();
           	var param = {
	   			action: 'webd_add_venue',
				value: valu
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: woo_events.ajaxurl,
	   			dataType: 'json',
	   			data: (param),
	   			success: function(data){
					if(data != '0')
					{
						$('#location-settings #webd_adress .field-item > input').val(data.webd_adress);
						$('#location-settings #webd_latitude_longitude .field-item > input').val(data.webd_latitude_longitude);
						$('#location-settings #webd_phone .field-item > input').val(data.webd_phone);
						$('#location-settings #webd_email .field-item > input').val(data.webd_email);
						$('#location-settings #webd_website .field-item > input').val(data.webd_website);
					}
					$('#location-settings').removeClass('loading');
	   				return true;
	   			}	
	   		});
		});
		jQuery("#bulk-update-venue").on('click', function() {
			var $this = jQuery(this);
			$this.addClass('loading');
			var $id = $this.data('id');
			var param = {
	   			action: 'webd_update_events_venue',
				id: $id
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: woo_events.ajaxurl,
	   			dataType: 'json',
	   			data: (param),
	   			success: function(data){
					if(data != '0')
					{
						
					}
					$this.removeClass('loading');
	   				return true;
	   			}	
	   		});
		});
	
	});
}(jQuery));

function initialize() {
	var input = document.getElementById('webd_adress-exc_mb-field-0');
	if(input!=null){
		var autocomplete = new google.maps.places.Autocomplete(input);
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
			if(place.geometry.location.lat()!='' && place.geometry.location.lng()!=''){
				document.getElementById('webd_latitude_longitude-exc_mb-field-0').value = place.geometry.location.lat()+', '+place.geometry.location.lng();
			}

        });
	}
}
if (typeof google !== 'undefined' && google.maps.event.addDomListener) {
	google.maps.event.addDomListener(window, 'load', initialize);
};