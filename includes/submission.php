<?php
//User Submit Event
if(!function_exists('getUserIP')){
	function getUserIP(){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		if(filter_var($client, FILTER_VALIDATE_IP)){
			$ip = $client;
		}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
			$ip = $forward;
		}else{
			$ip = $remote;
		}
		return $ip;
	}
}
if(!function_exists('webd_event_submit_image')){
	function webd_event_submit_image($submission,$cf_data,$key,$new_event,$set_thumb){
		$title_img = $cf_data[$key];
		$loc_img = $submission->uploaded_files();
		$loc_img = $loc_img[$key];
		$img = file_get_contents($loc_img);
		$upload_dir = wp_upload_dir(); 
		$upload = wp_upload_bits( $title_img, '', $img);
		$filename= $upload['file'];
		require_once(ABSPATH . 'wp-admin/includes/admin.php');
		$file_type = wp_check_filetype(basename($filename), null );
		$attachment = array(
		   'post_mime_type' => $file_type['type'],
		   'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		   'post_content' => '',
		   'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename, $new_event);
		$attach_url = get_attached_file( $attach_id );
		if($set_thumb == 1){
			set_post_thumbnail( $new_event, $attach_id );
		}else{
			$product = wc_get_product( $new_event );
			$images = $product->get_gallery_image_ids();
			if(is_array($images)){ 
				array_push($images,$attach_id);
				$images = implode(",",$images);
			}else{
				$images = $attach_id;
			}
			update_post_meta( $new_event, '_product_image_gallery', $images);
		}
		$attach_data =  wp_generate_attachment_metadata( $attach_id, $attach_url );
		wp_update_attachment_metadata( $attach_id,  $attach_data );

	}
}

function webd_usersubmit_hook_cf7($cf) {
	if(!class_exists('WPCF7_Submission')){
		return false;
	}
	$submission = WPCF7_Submission::get_instance();
	if($submission) {
		$cf_data = $submission->get_posted_data();
		if(isset($cf_data['webd-startdate']) && isset($cf_data['webd-enddate'])){
			$title = isset($cf_data['webd-event-title'])?$cf_data['webd-event-title']:'';
			$email = isset($cf_data['your-email'])?$cf_data['your-email']:'';
			$name = isset($cf_data['your-name'])?$cf_data['your-name']:'';
			$email_event = isset($cf_data['webd-event-mail'])?$cf_data['webd-event-mail']:'';
			$phone_event = isset($cf_data['webd-event-phone'])?$cf_data['webd-event-phone']:'';
			$url_event = isset($cf_data['webd-event-url'])?$cf_data['webd-event-url']:'';
			$tzone = isset($cf_data['webd-event-tzone'])?$cf_data['webd-event-tzone']:'';
			$location_event = isset($cf_data['webd-event-location'])?$cf_data['webd-event-location']:'';
			$content = isset($cf_data['webd-event-details'])?$cf_data['webd-event-details']:'';
			$shortdes = isset($cf_data['webd-event-shortdes'])?$cf_data['webd-event-shortdes']:'';
			
			$event_recurrence = isset($cf_data['webd-recurrence'])?$cf_data['webd-recurrence']:'';
			$event_recurrence_end = isset($cf_data['webd-recurr-enddate'])?$cf_data['webd-recurr-enddate']:'';
			$event_color = isset($cf_data['webd-event-color'])?$cf_data['webd-event-color']:'';
			if($event_color!=''){ $event_color = '#'.$event_color;}
			$event_price = isset($cf_data['webd-event-price'])?$cf_data['webd-event-price']:'0';
			$event_stock = isset($cf_data['webd-event-stock'])?$cf_data['webd-event-stock']:'';
			$event_tag = isset($cf_data['webd-event-tag'])?$cf_data['webd-event-tag']:'';
			$event_tag = explode(",",$event_tag);
			$event_cat = isset($cf_data['webd-event-cat'])?$cf_data['webd-event-cat']:'';
			$event_schedu = isset($cf_data['webd-event-schedule'])?$cf_data['webd-event-schedule']:'';
			if($name==''){ $name = $email;}
			$title = apply_filters( 'webd_event_submit_title', $title, $name);
			$event = array(
				'post_content'   => $content,
				'post_name' 	 => sanitize_title($title),
				'post_title'     => $title,
				'post_status'    => apply_filters( 'webd_event_submit_status', 'pending'),
				'post_type'      => 'product',
				'post_excerpt'   => $shortdes
			);
			$id_user = isset($cf_data['id_user'])?$cf_data['id_user']: '';
			if ( $id_user!='' ) {
				$event['post_author'] = $id_user;
			}
			if($new_event = wp_insert_post( $event, false )){
				
				$list_ids 			= get_user_meta($id_user, '_my_submit', true);
				if(!$list_ids || !is_array($list_ids)) $list_ids = array();
				$list_ids = array_merge($list_ids, array($new_event));
				update_user_meta($id_user, '_my_submit', $list_ids);
				if(get_option('webd_sm_datefm') == 'dd/mm/yyyy'){
					$cf_data['webd-startdate'] = str_replace('/', '-', $cf_data['webd-startdate']);
					$cf_data['webd-enddate'] = str_replace('/', '-', $cf_data['webd-enddate']);
				}				
				$cf_data['webd-starttime'] = str_replace(' ', '', $cf_data['webd-starttime']);
				$cf_data['webd-endtime'] = str_replace(' ', '', $cf_data['webd-endtime']);
				add_post_meta( $new_event, 'webd_startdate', strtotime($cf_data['webd-startdate'].' '.$cf_data['webd-starttime']) );
				add_post_meta( $new_event, 'webd_enddate', strtotime($cf_data['webd-enddate'].' '.$cf_data['webd-endtime']));
				add_post_meta( $new_event, '_visibility', 'visible' );
				if(is_numeric($event_price)){
					add_post_meta( $new_event, '_regular_price', $event_price);
					add_post_meta( $new_event, '_price', $event_price);
				}
				add_post_meta( $new_event, '_stock_status', 'instock');
				if(is_numeric($event_stock)){
					add_post_meta($new_event, '_stock', $event_stock);
					add_post_meta($new_event, '_manage_stock', 'yes');
				}
				if($event_recurrence_end!='' && $event_recurrence!=''){
					add_post_meta( $new_event, 'webd_recurrence_end', strtotime($event_recurrence_end) );
					add_post_meta( $new_event, 'webd_recurrence', $event_recurrence );
				}
				add_post_meta( $new_event, 'webd_eventcolor', $event_color);
				add_post_meta( $new_event, 'webd_email_submit', $email);
				if($email!=''){
					$subject = esc_html__('Thank you for submitting your event','WEBDWooEVENT');
					$message = esc_html__('Thank you for submitting your event. We will notify you if the event is approved.','WEBDWooEVENT');
					wp_mail( $email, $subject, $message );
				}
				add_post_meta( $new_event, 'webd_adress', $location_event);
				add_post_meta( $new_event, 'webd_phone', $phone_event);
				add_post_meta( $new_event, 'webd_email', $email_event);
				add_post_meta( $new_event, 'webd_website', $url_event);
				add_post_meta( $new_event, 'webd_time_zone', $tzone);
				wp_set_object_terms( $new_event, $event_cat, 'product_cat' );
				wp_set_object_terms( $new_event, $event_tag, 'product_tag' );
				if(isset($cf_data["webd-event-image"]) && $cf_data["webd-event-image"]!=''){
					webd_event_submit_image($submission,$cf_data,'webd-event-image',$new_event,1);
				}
				if( is_array($event_schedu)){
					foreach ($event_schedu as $schedu){
						add_post_meta( $new_event, 'webd_schedu', $schedu);
					}
				}
				do_action('webd_event_submit_meta_data',$submission,$cf_data,$new_event);
			}
		}else if(isset($cf_data['webd-sp-title'])){
			$title = isset($cf_data['webd-sp-title'])?$cf_data['webd-sp-title']:'';
			$email = isset($cf_data['your-email'])?$cf_data['your-email']:'';
			$name = isset($cf_data['your-name'])?$cf_data['your-name']:'';
			$pos_sp = isset($cf_data['webd-sp-pos'])?$cf_data['webd-sp-pos']:'';
			
			$dribbble = isset($cf_data['webd-sp-dribbble'])?$cf_data['webd-sp-dribbble']:'';
			$envelope = isset($cf_data['webd-sp-envelope'])?$cf_data['webd-sp-envelope']:'';
			$facebook = isset($cf_data['webd-sp-facebook'])?$cf_data['webd-sp-facebook']:'';
			$flickr = isset($cf_data['webd-sp-flickr'])?$cf_data['webd-sp-flickr']:'';
			$instagram = isset($cf_data['webd-sp-instagram'])?$cf_data['webd-sp-instagram']:'';
			$linkedin = isset($cf_data['webd-sp-linkedin'])?$cf_data['webd-sp-linkedin']:'';
			$pinterest = isset($cf_data['webd-sp-pinterest'])?$cf_data['webd-sp-pinterest']:'';
			$tumblr = isset($cf_data['webd-sp-tumblr'])?$cf_data['webd-sp-tumblr']:'';
			$twitter = isset($cf_data['webd-sp-twitter'])?$cf_data['webd-sp-twitter']:'';
			$youtube = isset($cf_data['webd-sp-youtube'])?$cf_data['webd-sp-youtube']:'';
			$github = isset($cf_data['webd-sp-github'])?$cf_data['webd-sp-github']:'';
			$content = isset($cf_data['webd-sp-details'])?$cf_data['webd-sp-details']:'';
			$webd_view = array(
				'post_content'   => $content,
				'post_name' 	   => sanitize_title($title),
				'post_title'     => $title,
				'post_status'    => apply_filters( 'webd_webd_view_submit_status', 'pending'),
				'post_type'      => 'event-webd-view'
			);
			$id_user = isset($cf_data['id_user'])?$cf_data['id_user']: '';
			if ( $id_user!='' ) {
				$webd_view['post_author'] = $id_user;
			}
			if($new_webd_view = wp_insert_post( $webd_view, false )){
				add_post_meta( $new_webd_view, 'dribbble', $dribbble);
				add_post_meta( $new_webd_view, 'envelope', $envelope);
				add_post_meta( $new_webd_view, 'facebook', $facebook);
				add_post_meta( $new_webd_view, 'instagram', $instagram);
				add_post_meta( $new_webd_view, 'linkedin', $linkedin);
				
				add_post_meta( $new_webd_view, 'pinterest', $pinterest);
				add_post_meta( $new_webd_view, 'tumblr', $tumblr);
				add_post_meta( $new_webd_view, 'twitter', $twitter);
				add_post_meta( $new_webd_view, 'youtube', $youtube);
				add_post_meta( $new_webd_view, 'github', $github);
				add_post_meta( $new_webd_view, 'webd_view_position', $pos_sp);
				if(!is_user_logged_in()){
					add_post_meta( $new_webd_view, 'ip_submit', getUserIP());
				}
				if(isset($cf_data["webd-sp-image"]) && $cf_data["webd-sp-image"]!=''){
					$title_img = $cf_data["webd-sp-image"];
					$loc_img = $submission->uploaded_files();
					$loc_img = $loc_img["webd-sp-image"];
					$img = file_get_contents($loc_img);
					$upload_dir = wp_upload_dir(); 
					$upload = wp_upload_bits( $title_img, '', $img);
					$filename= $upload['file'];
					require_once(ABSPATH . 'wp-admin/includes/admin.php');
					$file_type = wp_check_filetype(basename($filename), null );
					  $attachment = array(
					   'post_mime_type' => $file_type['type'],
					   'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
					   'post_content' => '',
					   'post_status' => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $filename, $new_webd_view);
					//$attach_url = wp_get_attachment_url( $attach_id );
					$attach_url = get_attached_file( $attach_id );
					set_post_thumbnail( $new_webd_view, $attach_id );
					$attach_data =  wp_generate_attachment_metadata( $attach_id, $attach_url );
					wp_update_attachment_metadata( $attach_id,  $attach_data );
				}
			}
		}
	}
}
// email notification
add_action( 'save_post', 'webd_notify_submit');
function webd_notify_submit( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || get_option('webd_sm_notify')!='1' ){
		return;
	}
	$email = get_post_meta($post_id,'webd_email_submit',true);
	if($email!='' && get_post_status($post_id)=='publish'){
		$subject = esc_html__('Your event submission has been approved','WEBDWooEVENT');
		$message = esc_html__('Your event has been approved. You can see it here','WEBDWooEVENT').' '.get_permalink($post_id);
		wp_mail( $email, $subject, $message );
		update_post_meta( $post_id, 'webd_email_submit', '');
	}
}
add_action('wp_trash_post','webd_trash_post_function');
function webd_trash_post_function($post_id){
	$email = get_post_meta($post_id,'webd_email_submit',true);
	if($email!='' && get_option('webd_sm_notify')=='1'){
		$subject = esc_html__('Your submission has been canceled','WEBDWooEVENT');
		$message = esc_html__('Sorry, but the event you submitted did not get approved. ','WEBDWooEVENT');
		wp_mail( $email, $subject, $message );
	}
}
add_action("wpcf7_before_send_mail", "webd_usersubmit_hook_cf7");
function webd_time_cf7_field($tag){
	/*$output = '
	<input type="text"  name="'.$tag['name'].'" id="'.$tag['name'].'" class="time submit-time" placeholder="'.esc_html__('H:i','WEBDWooEVENT').'">';
	return $output;*/
	
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) ) { return ''; }

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$class .= ' time submit-time ';

	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['placeholder'] = esc_html__('H:i','WEBDWooEVENT');
	if ( $tag->is_required() ) { $atts['aria-required'] = 'true'; }

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$atts['type'] = 'text';

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );

	ob_start();
	wp_enqueue_style('webd-jquery.timepicker', WEBD_EVENT_BOOKINGS.'js/jquery-timepicker/jquery.timepicker.css');
	wp_enqueue_script( 'webd-jquery.timepicker', WEBD_EVENT_BOOKINGS.'js/jquery-timepicker/jquery.timepicker.min.js', array( 'jquery' ) );
	$js_string = ob_get_contents();
	ob_end_clean();
	return $html.$js_string;
}
function webd_time_cf7_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_time','webd_time*'), 'webd_time_cf7_field', true);
	}
}
add_action( 'init', 'webd_time_cf7_shortcode' );
// submit date
function webd_date_cf7_field($tag){
	/*$output = '
	<input type="text"  name="'.$tag['name'].'" id="'.$tag['name'].'" class="date submit-date wpcf7-validates-as-required" aria-required="true" placeholder="">';
	return $output;
	*/
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) ) { return ''; }

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$class .= ' date submit-date ';

	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	if ( $tag->is_required() ) { $atts['aria-required'] = 'true'; }

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$atts['type'] = 'text';

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );
	$tfm = get_option('webd_sm_timefm')!='' ? get_option('webd_sm_timefm') : 'h:i A';
	$html = sprintf(
		'
		<input type="hidden" name="id_user" value="'.get_current_user_id().'">
		<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s <input type="hidden" class="wedate_format" value="'.get_option('webd_sm_datefm').'"/><input type="hidden" class="wetime_format" value="'.$tfm.'"/></span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );
	ob_start();
	wp_enqueue_style('webd-bootstrap-datepicker', WEBD_EVENT_BOOKINGS.'js/jquery-timepicker/lib/bootstrap-datepicker.css');
	wp_enqueue_script( 'webd-bootstrap-datepicker', WEBD_EVENT_BOOKINGS.'js/jquery-timepicker/lib/bootstrap-datepicker.js', array( 'jquery' ) );
	if(get_option('webd_jscolor_js')!='on'){
		wp_enqueue_script( 'webd-color-picker', WEBD_EVENT_BOOKINGS. 'js/jscolor.min.js', array('jquery'), '2.0', true );
	}
	$js_string = ob_get_contents();
	ob_end_clean();
	return $html.$js_string;
		
}
add_filter( 'wpcf7_validate_webd_date', 'wpcf7_webd_date_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_webd_date*', 'wpcf7_webd_date_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_webd_time', 'wpcf7_webd_date_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_webd_time*', 'wpcf7_webd_date_validation_filter', 10, 2 );

function wpcf7_webd_date_validation_filter( $result, $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	$name = $tag->name;
	$value = isset( $_POST[$name] )
		? trim( strtr( (string) $_POST[$name], "\n", " " ) )
		: '';

	if ( $tag->is_required() && '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}
	return $result;
}




function webd_date_cf7_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_date','webd_date*'), 'webd_date_cf7_field', true);
	}
}
add_action( 'init', 'webd_date_cf7_shortcode' );
// submit recurrence
function webd_recurr_cf7_field($tag){
	$output = '
	<select name="'.$tag['name'].'" id="'.$tag['name'].'" class="recurrence submit-recurrence">
		<option value="">'.esc_html__('None','WEBDWooEVENT').'</option>
		<option value="day">'.esc_html__('Every Day','WEBDWooEVENT').'</option>
		<option value="week">'.esc_html__('Every Week','WEBDWooEVENT').'</option>
		<option value="month">'.esc_html__('Every Month','WEBDWooEVENT').'</option>
	</select>';
	return $output;
}
function webd_recurr_cf7_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_recurrence','webd_recurrence*'), 'webd_recurr_cf7_field', true);
	}
}
add_action( 'init', 'webd_recurr_cf7_shortcode' );
// submit category
function webd_cat_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_category','webd_category*'), 'webd_cathtml', true);
	}
}
function webd_cathtml($tag){
	$class = '';
	$is_required = 0;
	if(class_exists('WPCF7_FormTag')){
		$tag = new WPCF7_FormTag( $tag );
		if ( $tag->is_required() ){
			$is_required = 1;
			$class .= ' required-cat';
		}
	}
	$cat_exclude = get_option('webd_sm_cat');
	$cat_include = get_option('webd_sm_cat_in');
	$cargs = array(
		'hide_empty'    => false, 
		'exclude'       => explode(",",$cat_exclude),
		'include'       => explode(",",$cat_include)
	); 
	$cats = get_terms( 'product_cat', $cargs );
	$output = '';
	if($cats){
		$output = '<div class="wpcf7-form-control-wrap event-cat"><div class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required'.$class.'">';
		foreach ($cats as $acat){
			$output .= '
			<label class="col-md-4 wpcf7-list-item">
				<input type="checkbox" name="webd-event-cat[]" value="'.$acat->slug.'" /> '.$acat->name.'
			</label>';
		}
		$output .= '</div>';
	}
	ob_start();
	if($is_required){
		?>
		<script>
		jQuery(document).ready(function(e) {
			jQuery('.webd-submit .wpcf7-submit').on('click', function (e) {
				var checked = 0;
				var namecat = '';
				jQuery.each(jQuery("input[name='webd-event-cat[]']:checked"), function() {
					checked = jQuery(this).val();
					if(namecat!=''){
						namecat = namecat+', '+jQuery(this).closest('label' ).text();
					}else{
						namecat = jQuery(this).closest('label' ).text();
					}
				});
				if(jQuery("form.wpcf7-form input[name='webd-event-cname']").length){
					jQuery(this).val(namecat);
				}
				if(checked == 0){
					if(jQuery('.cat-alert').length==0){
						jQuery('.wpcf7-form-control-wrap.event-cat').append('<span role="alert" class="wpcf7-not-valid-tip cat-alert"><?php echo wpcf7_get_message( 'invalid_required' ); ?></span>');
					}
					var windowHeight = $(window).height();
			        jQuery('html,body').animate({
			          scrollTop: jQuery(".event-cat").offset().top - windowHeight * .2},
			          'slow');
					e.preventDefault();
					setTimeout(function(){ 
						jQuery('.wpex-wpcf7-loader').removeClass('visible');
					}, 500);
					return false;
				}else{
					return true;
				}
			});
		});
		</script>
		<?php
	}
	?>
    <script>
	jQuery(document).ready(function(e) {
		jQuery("form.wpcf7-form").submit(function (e) {
			if(jQuery("form.wpcf7-form input[name='webd-event-cname']").length){
				var namecat = '';
				jQuery.each(jQuery("input[name='webd-event-cat[]']:checked"), function() {
					if(jQuery(this).closest('label' ).text()!=''){
						if(namecat!=''){
							namecat = namecat+', '+jQuery(this).closest('label' ).text();
						}else{
							namecat = jQuery(this).closest('label' ).text();
						}
					}
				});
				jQuery("form.wpcf7-form input[name='webd-event-cname']").val(namecat);
			}
			return true;
		});
	});
	</script>
    <?php
	$js_string = ob_get_contents();
	ob_end_clean();
	return $output.$js_string;
}
add_action( 'init', 'webd_cat_shortcode' );

function webd_webd_viewhtml($tag){
	$is_required = 0;
	if(class_exists('WPCF7_FormTag')){
		$tag = new WPCF7_FormTag( $tag );
		if ( $tag->is_required() ){
			$is_required = 1;
		}
	}
	$cat_exclude = get_option('webd_sm_cat');
	$args = array(
		'post_type' => 'event-webd-view',
		'posts_per_page' => -1,
		'post_status' => array( 'pending', 'publish'),
 		'meta_key' => $meta_key,
		'ignore_sticky_posts' => 1,
	);
	if(is_user_logged_in()){
		$args['author'] = get_current_user_id();
	}else{
		$args['meta_key'] = 'ip_submit';
		$args['meta_value'] = getUserIP();
		
	}
	$postlist = get_posts($args);
	$output = '<div class="wpcf7-form-control-wrap event-webd_view"><div class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required">';
	$url= isset($tag->options) ? $tag->options : '';
	if(isset($url[0]) ){
		$output .= '<p class="col-md-12"><a href="'.$url[0].'">'.esc_html__('Submit new webd_view','WEBDWooEVENT').'</a></p>';
	}
	if($postlist){
		foreach ( $postlist as $post ) {
			$output .= '
			<label class="col-md-4 wpcf7-list-item">
				<input type="checkbox" name="webd-event-webd_view[]" value="'.$post->ID.'" /> '.get_the_title( $post->ID ).'
			</label>';
		}
	}
	$output .= '</div>';
	ob_start();
	if($is_required){?>
    <script>
	jQuery(document).ready(function(e) {
		jQuery('.webd-submit .wpcf7-submit').on('click', function (e) {
			var checked = 0;
			jQuery.each(jQuery("input[name='webd-event-webd_view[]']:checked"), function() {
				checked = jQuery(this).val();
			});
			if(checked == 0){
				if(jQuery('.sp-alert').length==0){
					jQuery('.wpcf7-form-control-wrap.event-webd_view').append('<span role="alert" class="wpcf7-not-valid-tip sp-alert"><?php echo wpcf7_get_message( 'invalid_required' ); ?></span>');
				}
				return false;
			}else{
				return true;
			}
		});
	});
	</script>
	<?php
	}
	$js_string = ob_get_contents();
	ob_end_clean();
	return $output.$js_string;
}
function webd_webd_view_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_webd_view','webd_webd_view*'), 'webd_webd_viewhtml', true);
	}
}
add_action( 'init', 'webd_webd_view_shortcode' );
// submit schedule
function webd_scheduler_cf7_field($tag){
	$output = '
	<span name="'.$tag['name'].'" id="'.$tag['name'].'" class="submit-schedule">'.esc_html__('+ Add New','WEBDWooEVENT').'</span>';
	ob_start();
	?>
    <script>
	jQuery(document).ready(function(e) {
		jQuery("#<?php echo esc_attr($tag['name']);?>").on('click', function(e) {
			jQuery(this).before( '<input type="text" name="webd-event-schedule[]" value="" />' );
		});
	});
	</script>
    <?php
	$js_string = ob_get_contents();
	ob_end_clean();
	return $output.$js_string;
}
function webd_schedule_cf7_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_schedule','webd_schedule*'), 'webd_scheduler_cf7_field', true);
	}
}
add_action( 'init', 'webd_schedule_cf7_shortcode' );
// Submit Venue
function webd_venue_submit_html($tag){
	$is_required = 0;
	if(class_exists('WPCF7_FormTag')){
		$tag = new WPCF7_FormTag( $tag );
		if ( $tag->is_required() ){
			$is_required = 1;
		}
	}
	$args = array(
		'post_type' => 'webd_venue',
		'posts_per_page' => -1,
		'post_status' => array('publish'),
		'ignore_sticky_posts' => 1,
	);
	$postlist = get_posts($args);
	$output = '';
	if($postlist){
		$output .= '
		<span class="wpcf7-form-control-wrap webd-event-venue wpcf7-not-valid">
			<select name="webd-event-venue">
			<option value="" />'.esc_html__('None','WEBDWooEVENT').'</option>';
		foreach ( $postlist as $post ) {
			$output .= '<option value="'.$post->ID.'" /> '.get_the_title( $post->ID ).'</option>';
		}
		$output .= '
			</select>
		</span>';
	}
	$output .= '';
	ob_start();
	if($is_required){?>
    <script>
	jQuery(document).ready(function(e) {
		jQuery('.webd-submit .wpcf7-submit').on('click', function (e) {
			var value = 0;
			jQuery.each(jQuery("select[name='webd-event-venue'] option"), function() {
				if(jQuery(this).is(':selected')){
					value = jQuery(this).val() !='' ? jQuery(this).val() : 0;
				}
			});
			if(value == 0){
				if(!jQuery('.sp-alert').length){
					jQuery('.wpcf7-form-control-wrap.webd-event-venue').append('<span role="alert" class="wpcf7-not-valid-tip sp-alert"><?php echo wpcf7_get_message( 'invalid_required' ); ?></span>');
				}
				e.preventDefault();
				return false;
			}else{
				return true;
			}
		});
	});
	</script>
	<?php
	}
	$js_string = ob_get_contents();
	ob_end_clean();
	return $output.$js_string;
}
function webd_venuesm_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_venue','webd_venue*'), 'webd_venue_submit_html', true);
	}
}
add_action( 'init', 'webd_venuesm_shortcode' );

// user submit id
function webd_user_submit_html($tag){
	$output = '<input type="hidden" name="id_user" value="'.get_current_user_id().'">';
	
	return $output;
}
function webd_user_sm_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_user','webd_user*'), 'webd_user_submit_html', true);
	}
}
add_action( 'init', 'webd_user_sm_shortcode' );
// user submit Latitude end Longitude 
function webd_lat_log_cf7_field_html($tag){
	$tag = new WPCF7_FormTag( $tag );
	if ( empty( $tag->name ) ) { return ''; }
	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );
	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}
	$class .= ' submit-lat-log ';
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	if ( $tag->is_required() ) { $atts['aria-required'] = 'true'; }
	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
	$atts['type'] = 'text';
	$atts['name'] = $tag->name;
	$atts = wpcf7_format_atts( $atts );
	$html = sprintf(
		'
		<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );
	ob_start();
	$webd_api_map = get_option('webd_api_map');
	if($webd_api_map!='' && $webd_api_map!='off'){
		wp_enqueue_script( 'wooevent-auto-map', '//maps.googleapis.com/maps/api/js?key='.$webd_api_map.'&libraries=places');
	}?>
    <script>
	jQuery(document).ready(function() {
		function initialize() {
			var input = document.getElementsByName('webd-event-location')[0];
			if(input!=null){
				var autocomplete = new google.maps.places.Autocomplete(input);
				google.maps.event.addListener(autocomplete, 'place_changed', function () {
					var place = autocomplete.getPlace();
					if(place.geometry.location.lat()!='' && place.geometry.location.lng()!=''){
						document.getElementsByName('webd-event-latlog')[0].value = place.geometry.location.lat()+', '+place.geometry.location.lng();
					}
		
				});
			}
		}
		if (typeof google !== 'undefined' && google.maps.event.addDomListener) {
			google.maps.event.addDomListener(window, 'load', initialize);
		};
	});
	</script>
    <?php
	
	$js_string = ob_get_contents();
	ob_end_clean();
	return $html.$js_string;
		
}

function webd_lat_log_shortcode(){
	if(function_exists('wpcf7_add_form_tag')){
		wpcf7_add_form_tag(array('webd_latlog','webd_latlog*'), 'webd_lat_log_cf7_field_html', true);
	}
}
add_action( 'init', 'webd_lat_log_shortcode' );
