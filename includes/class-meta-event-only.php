<?php
/* Category Layout purpose */
add_action( 'product_cat_add_form_fields', 'webd_sglayout_purpowebd_fields', 10 );
add_action ( 'product_cat_edit_form_fields', 'webd_sglayout_purpowebd_fields');

function webd_sglayout_purpowebd_fields( $tag ) {
	$t_id 					= isset($tag->term_id) ? $tag->term_id : '';
	$webd_cat_sglayout 			= get_option( "webd_cat_sglayout_$t_id")?get_option( "webd_cat_sglayout_$t_id"):'';
	?>
	<tr class="form-field" style="">
		<th scope="row" valign="top">
			<label for="webd_cat_sglayout"><?php esc_html_e('Layout Purpose','WEBDWooEVENT'); ?></label>
		</th>
		<td>
			<select name="webd_cat_sglayout" id="webd_cat_sglayout">
                <option value=""><?php esc_html_e('Default','WEBDWooEVENT'); ?></option>
                <option value="woo" <?php echo $webd_cat_sglayout=='woo'?'selected="selected"':'' ?>><?php esc_html_e('WooCommerce','WEBDWooEVENT'); ?></option>
                <option value="event" <?php echo $webd_cat_sglayout=='event'?'selected="selected"':'' ?>><?php esc_html_e('Events','WEBDWooEVENT'); ?></option>
            </select>
		</td>
	</tr>
	<?php
}
//save layout fields
add_action ( 'edited_product_cat', 'webd_save_extra_sglayout_fileds', 10, 2);
add_action( 'created_product_cat', 'webd_save_extra_sglayout_fileds', 10, 2 );
function webd_save_extra_sglayout_fileds( $term_id ) {
	if ( isset( $_POST[sanitize_key('webd_cat_sglayout')] ) ) {
		$webd_cat_sglayout = sanitize_text_field($_POST['webd_cat_sglayout']);
		update_option( "webd_cat_sglayout_$term_id", $webd_cat_sglayout );
	}
}
if(!function_exists('webd_event_cat_custom_layout')){
	function webd_event_cat_custom_layout($id){
		if($id==''){
			return;	
		}
		$args = array(
			'hide_empty'        => true, 
		);
		$webd_eventlayout ='';
		$terms = wp_get_post_terms($id, 'product_cat', $args);
		if(!empty($terms) && !is_wp_error( $terms )){
			foreach ( $terms as $term ) {
				$webd_eventlayout = get_option('webd_cat_sglayout_' . $term->term_id);
				if($webd_eventlayout!=''){
					break;
				}
			}
		}
		$webd_eventlayout = apply_filters( 'webd_cat_sglayout', $webd_eventlayout,$id );
		return $webd_eventlayout;
	}
}

class WEBD_WooEvent_ShowMeta {
	public function __construct()
    {
		add_action( 'woocommerce_single_product_summary', array( &$this,'webd_woocommerce_single_ev_meta') );
		add_action( 'woocommerce_after_single_product_summary', array( &$this,'webd_woocommerce_single_ev_schedu') );
		
		add_filter( 'woocommerce_product_description_heading',  array( &$this,'webd_wc_change_product_description_tab_title'), 10, 1 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this,'webd_woo_custom_cart_button_text'));  

		add_action('woocommerce_before_single_product',array( &$this,'webd_add_info_before_single'),11);

		add_action( 'woocommerce_email_before_order_table', array( &$this,'woocommerce_email_hook'));
		add_filter( 'woocommerce_cart_item_name', array( &$this,'webd_woocommerce_cart_hook'), 10, 3 );
		add_filter ('woocommerce_add_to_cart_redirect', array( &$this,'webd_woocommerce_redirect_to_checkout'));
		add_filter( 'woocommerce_add_to_cart_validation', array( &$this,'webd_validate_add_cart_item'), 10, 5 );
		//add_filter( 'woocommerce_loop_add_to_cart_link', array( &$this,'webd_change_product_link') );
		add_action( 'woocommerce_shop_loop_item_title', array( &$this,'webd_add_ev_meta' ),12);
		add_action( 'woocommerce_before_shop_loop_item',  array( &$this,'webd_woocommerce_shopitem_ev_meta'),99 );
    }
	// Stop event booking before X day event start
	function webd_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
	
		// do your validation, if not met switch $passed to false
		if ( webd_hide_booking_form()!='' ){
			$passed = false;
			$t_stopb = get_option('webd_text_stopb')!='' ? get_option('webd_text_stopb') : esc_html__('Tickets not available','WEBDWooEVENT');
			wc_add_notice( $t_stopb, 'error' );
		}
		return $passed;
	
	}
	//change text
	function webd_wc_change_product_description_tab_title( $heading ) {
		global $woocommerce, $post;
		$webd_layout_purpose = webd_global_default_spurpose();
		if($webd_layout_purpose!='event'){
			$heading = get_option('webd_text_details')!='' ? get_option('webd_text_details') : esc_html__('Details','WEBDWooEVENT');
		}else{
			$heading = get_option('webd_text_evdetails')!='' ? get_option('webd_text_evdetails') : esc_html__('Event Details','WEBDWooEVENT');
		}
		return $heading;
	}
	//remove button if event pass
	function webd_add_info_before_single(){
		global $woocommerce, $post;
		$time_now =  strtotime("now");
		$webd_enddate = webd_global_enddate() ;
		$webd_layout_purpose = webd_global_default_spurpose();
		echo webd_hide_booking_form();
		
		$webd_time_zone = get_post_meta($post->ID,'webd_time_zone',true);
		if($webd_time_zone!='' && $webd_time_zone!='def'){
			$webd_time_zone = $webd_time_zone * 60 * 60;
			$time_now = $webd_time_zone + $time_now;
		}
		$all_day = get_post_meta($post->ID,'webd_allday', true );
		if( $all_day == 1){
			$date_notime = date('Y-m-d',$webd_enddate);
			$webd_enddate = strtotime($date_notime) + 86399;
		}
		if($time_now > $webd_enddate && $webd_enddate!='' && $webd_layout_purpose=='event'){
			$evpasstrsl = get_option('webd_text_event_pass')!='' ? get_option('webd_text_event_pass') : esc_html__('This event has passed','WEBDWooEVENT');
			echo '
			<div class="alert alert-warning event-info"><i class="fa fa-exclamation-triangle"></i>'.$evpasstrsl.'</div>
			<style type="text/css">.woocommerce div.product form.cart, .woocommerce div.product p.cart{ display:none !important}</style>';
		}
	}// global $date
	
	function webd_woo_custom_cart_button_text($text) {
	 	$webd_text_join_ev = get_option('webd_text_join_ev');
		global $woocommerce, $post,$product;
		if( $product->is_type( 'external' ) ){
        	return $text;
        }
		$webd_main_purpose = webd_global_main_purpose();
		$webd_layout_purpose = webd_global_default_spurpose();
		if($webd_layout_purpose!='event'){
			return get_option('webd_text_add_to_cart')!='' ? get_option('webd_text_add_to_cart') : esc_html__( 'Add To Cart', 'WEBDWooEVENT' );
		}
		if($webd_text_join_ev!=''){
			return $webd_text_join_ev;
		}else{
			return esc_html__( 'Join this Event', 'WEBDWooEVENT' );
		}
	 
	}
	// change add to cart link
	function webd_ical_google_calendar() {
		global $woocommerce, $post;
		$webd_enddate = webd_global_enddate();
		$webd_startdate = webd_global_startdate();?>
        <div class="webd-icl-import">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn btn-primary"><a href="<?php echo home_url().'?ical_product='.$post->ID; ?>"><?php echo get_option('webd_text_ical')!='' ? get_option('webd_text_ical') : esc_html__('+ Ical Import','WEBDWooEVENT');?></a></div>
                    <div class="btn btn-primary"><a href="https://www.google.com/calendar/render?dates=<?php  echo gmdate("Ymd\THis", $webd_startdate);?>/<?php echo gmdate("Ymd\THis", $webd_enddate);?>&action=TEMPLATE&text=<?php echo get_the_title(get_the_ID());?>&location=<?php echo get_post_meta(get_the_ID(),'webd_adress', true );?>&details=<?php echo get_the_excerpt();?>"><?php echo get_option('webd_text_ggcal')!='' ? get_option('webd_text_ggcal') : esc_html__('+ Google calendar','WEBDWooEVENT');?></a></div>
                </div>
            </div>
        </div>
        <?php
	}
	// Single Custom meta 
	function webd_woocommerce_single_ev_meta() {
		global $woocommerce, $post;
		$webd_layout_purpose = webd_global_default_spurpose();
		if($webd_layout_purpose=='event'){
			wooevent_template_plugin('event-meta');
		}
			
	}
	function webd_woocommerce_single_ev_schedu() {
		global $woocommerce, $post;
		$webd_layout_purpose = webd_global_default_spurpose();
		
		if($webd_layout_purpose=='event'){
			wooevent_template_plugin('event-schedu');
		}
	}
	// Email hook
	function woocommerce_email_hook($order){
		$event_details = new WC_Order( $order->get_id() );
		global $event_items;
		$event_items = $event_details->get_items();
		wooevent_template_plugin('email-event-details');

	}
	// Cart hook
	function webd_woocommerce_cart_hook($_product_title,$cart_item, $cart_item_key){
		if(!is_cart() && !is_checkout()){ return $_product_title;}
		$webd_startdate = get_post_meta( $cart_item['product_id'], 'webd_startdate', true );
		$webd_enddate = get_post_meta( $cart_item['product_id'], 'webd_enddate', true );
		$all_day = get_post_meta($cart_item['product_id'],'webd_allday', true );
		$html = $_product_title;
		$stdatetrsl = get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') :  esc_html__('Start Date','WEBDWooEVENT');
			$edatetrsl = get_option('webd_text_edate')!='' ? get_option('webd_text_edate') : esc_html__('End Date','WEBDWooEVENT');
			$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
		if($all_day!='1' && $webd_startdate!=''){
			$html .='<span class="meta-stdate" style="display: block;">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate).'</span>';
			if($webd_enddate!=''){
				$html .='<span class="meta-eddate" style="display: block;">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate).'</span>';
			}
		}elseif($webd_startdate!=''){
			$html .='<span class="meta-stdate" style="display: block;">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).'</span>';
			if($webd_enddate!=''){
				$html .='<span class="meta-eddate" style="display: block;">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.$alltrsl.'</span>';
			}
		}
		return $html;

	}
	// redirect to checkout
	function webd_woocommerce_redirect_to_checkout($wc) {
		if(get_option('webd_enable_cart')=='off'){
			global $woocommerce;
			$checkout_url = wc_get_checkout_url();
			return $checkout_url;
		}
		return $wc;
	}
	// add event info to shop page
	function webd_add_ev_meta(){
		global $woocommerce, $post;
	  	$hml = '';
		$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true );
		$webd_enddate = get_post_meta( $post->ID, 'webd_enddate', true );
		global $product;	
		$type = $product->get_type();
		$price_html = $product->get_price();
		$price = '';
		if($type=='variable'){
			$price = webd_variable_price_html();
		}else{
			if ( $price_html = $product->get_price_html() ) :
				$price = $price_html;
			endif; 	
		}
		if($webd_startdate!=''){ 
			$hml = '
			<div class="shop-webd-more-meta">
				<span><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).'</span>';
				if(get_option('webd_dis_status') !='yes'){
				$hml .= '
					<span>
						<i class="fa fa-ticket"></i>
						'.woo_event_status( $post->ID, $webd_enddate).'
					</span>';
				}
				$hml .='
			</div>';
		}
		$ft_html = apply_filters( 'webd_shop_ev_meta', $hml, $webd_startdate, $price, $webd_enddate );
		echo $ft_html;
	}
	// Add meta to item of shop
	function webd_woocommerce_shopitem_ev_meta(){
		global $woocommerce, $post;
		$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true );
		if(!function_exists('webd_event_custom_color')){ return;}
		$webd_eventcolor = webd_event_custom_color($post->ID);
		if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
		$bgev_color = '';
		if($webd_eventcolor!=""){
			$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
		}
	
		if($webd_startdate!=''){ 
			echo '<div class="shop-webd-stdate" '.$bgev_color.'><span class="day">'.date_i18n('d', $webd_startdate).'</span>';
			echo '<span class="month">'.date_i18n('M', $webd_startdate).'</span></div>';
		}
	}

	// change add to cart link
	function webd_change_product_link( $link ) {
		global $product;
		$product_id = $product->get_id();
		$product_sku = $product->get_sku();
		$vialltrsl = get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__( 'View Details', 'WEBDWooEVENT' );
		$cure_time =  strtotime("now");
		$gmt_offset = get_option('gmt_offset');
		if($gmt_offset!=''){
			$cure_time = $cure_time + ($gmt_offset*3600);
		}
		$webd_startdate = get_post_meta( $product->get_id(), 'webd_startdate', true );
		if($webd_startdate==''){ return $link;}
		$webd_enddate = get_post_meta( $product->get_id(), 'webd_enddate', true );
		if( $cure_time > $webd_enddate ){
			$evlink = '<a href="'.get_permalink().'" class="button add_to_cart_button product_type_variable webd-has-passed">'.$vialltrsl.'</a>';
		}else{
			$evlink = '<a href="'.get_permalink().'" class="button add_to_cart_button product_type_variable">'.$vialltrsl.'</a>';
		}
		$ft_link = apply_filters( 'webd_shop_ev_link', $evlink, $webd_startdate, $webd_enddate );
		return $ft_link;
	}
}
$WEBD_WooEvent_ShowMeta = new WEBD_WooEvent_ShowMeta();