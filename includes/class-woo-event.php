<?php
class WEBD_WooEvent_ShowMeta {
	public function __construct()
    {
		add_action( 'woocommerce_single_product_summary', array( &$this,'webd_woocommerce_single_ev_meta') );
		add_action( 'woocommerce_after_single_product_summary', array( &$this,'webd_woocommerce_single_ev_schedu') );
		add_action( 'woocommerce_archive_description', array( &$this,'webd_woocommerce_shop_search_view_bar') );
		add_action( 'pre_get_posts', array( &$this,'webd_remove__shop_loop'),99 );
		add_filter('loop_shop_columns', array( &$this,'webd_loop_columns'));
		add_action( 'woocommerce_before_shop_loop_item', array( &$this,'webd_woocommerce_shopitem_ev_meta'),99 );
		//add_action( 'woocommerce_after_shop_loop_item', array( &$this,'webd_woocommerce_shopitem_ev_short_des') );
		add_action( 'woocommerce_shop_loop_item_title', array( &$this,'webd_woocommerce_shopitem_ev_more_meta') );
		//add_action( 'woocommerce_product_thumbnails', array( &$this,'webd_ical_google_calendar') );
		//add_action( 'woocommerce_after_main_content', array( &$this,'webd_woocommerce_shopitem_ev_share') );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( &$this,'webd_change_product_link') );
		add_filter( 'woocommerce_catalog_orderby', array( &$this,'webd_change_product_orderby'),10,11 );
		add_action( 'init', array( &$this,'remove_upsell') );
		add_action( 'widgets_init', array( &$this,'webd_widgets_init') );
 		add_filter( 'woocommerce_output_related_products_args', array( &$this,'webd_related_products_item'), 99 );
		add_filter( 'woocommerce_product_tabs', array( &$this,'webd_woo_remove_reviews_tab'), 98 );
		add_filter( 'woocommerce_product_description_heading',  array( &$this,'webd_wc_change_product_description_tab_title'), 10, 1 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this,'webd_woo_custom_cart_button_text'));  
		//add_filter( 'gettext', array( &$this,'webd_related_chage'), 20, 3 );
		add_action('woocommerce_before_single_product',array( &$this,'webd_add_info_before_single'),11);
		add_filter( 'woocommerce_product_tabs', array( &$this,'webd_woo_remove_product_tabs'), 98 );
		add_filter( 'woocommerce_single_product_image_html', array( &$this,'webd_woo_remove_product_image'), 98 );
		add_action( 'woocommerce_email_before_order_table', array( &$this,'woocommerce_email_hook'));
		add_filter( 'woocommerce_cart_item_name', array( &$this,'webd_woocommerce_cart_hook'), 10, 3 );
		add_filter ('woocommerce_add_to_cart_redirect', array( &$this,'webd_woocommerce_redirect_to_checkout'));
		add_action( 'woocommerce_after_main_content', array( &$this,'webd_woocommerce_next_previous_event'));
		add_filter('woocommerce_related_products_args',array( &$this,'webd_wc_remove_related_products'), 10); 
		add_filter( 'woocommerce_add_to_cart_validation', array( &$this,'webd_validate_add_cart_item'), 10, 5 );
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
	//remove product tabs if layout 2
	function webd_woo_remove_product_tabs( $tabs ) {
		global $woocommerce, $post;
		$webd_enable_review = get_option('webd_enable_review');
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose == 'woo' || $webd_enable_review == 'on'){
			if(wooevent_global_layout() =='layout-2' || wooevent_global_layout() =='layout-3'){
				unset( $tabs['description'] ); 
			}
		}else{
			if(wooevent_global_layout() =='layout-2' || wooevent_global_layout() =='layout-3'){
				unset( $tabs['description'] );      	// Remove the description tab
				unset( $tabs['reviews'] ); 			// Remove the reviews tab
				unset( $tabs['additional_information'] );  	// Remove the additional information tab
			}
		}
		return $tabs;
	
	}
	//remove review
	function webd_woo_remove_reviews_tab($tabs) {
		$webd_main_purpose = webd_global_main_purpose();
		$webd_enable_review = get_option('webd_enable_review');
		if($webd_main_purpose=='woo' || $webd_main_purpose=='custom' || $webd_enable_review == 'on' ){return $tabs;}
		unset($tabs['reviews']);
		return $tabs;
	}
	function webd_woo_remove_product_image( $image ) {
		if(wooevent_global_layout() =='layout-2' || wooevent_global_layout() =='layout-3'){
			$image ='';
		}
		return $image;
	
	}
	//remove button if event pass
	function webd_add_info_before_single(){
		global $woocommerce, $post;
		$time_now =  strtotime("now");
		$webd_enddate = webd_global_enddate() ;
		$webd_main_purpose = webd_global_main_purpose();
		$webd_enable_sginfo = get_option('webd_enable_sginfo');
		echo webd_hide_booking_form();
		$id_user = get_current_user_id();
		
		$webd_time_zone = get_post_meta($post->ID,'webd_time_zone',true);
		if($webd_time_zone!='' && $webd_time_zone!='def'){
			$webd_time_zone = $webd_time_zone * 60 * 60;
			$time_now = $webd_time_zone + $time_now;
		}
		//echo $time_now;
		$all_day = get_post_meta($post->ID,'webd_allday', true );
		if( $all_day == 1){
			$date_notime = date('Y-m-d',$webd_enddate);
			$webd_enddate = strtotime($date_notime) + 86399;
		}
		if($time_now > $webd_enddate && $webd_enddate!='' && $webd_main_purpose!='woo'){
			$evpasstrsl = get_option('webd_text_event_pass')!='' ? get_option('webd_text_event_pass') : esc_html__('This event has passed','WEBDWooEVENT');
			echo '
			<div class="alert alert-warning event-info"><i class="fa fa-exclamation-triangle"></i>'.$evpasstrsl.'</div>
			<style type="text/css">.woocommerce div.product form.cart, .woocommerce div.product p.cart{ display:none !important}</style>';
		}else if($webd_enable_sginfo=='on' && $id_user!=0 && wc_customer_bought_product('',$id_user, get_the_ID())){
			$evsg_info = get_option('webd_text_usersg')!='' ? get_option('webd_text_usersg') : esc_html__('You already signed up this event','WEBDWooEVENT');
			echo '<div class="alert alert-warning event-info"><i class="fa fa-exclamation-triangle"></i>'.$evsg_info.'</div>';
		}
		if(wooevent_global_layout() =='layout-2' || wooevent_global_layout() =='layout-3'){
			wooevent_template_plugin('layout-2');
		}
	}// global $date
	
	//Add to cart text
	function webd_related_chage( $translated_text, $text, $domain ) {
		$webd_main_purpose = get_option('webd_main_purpose');
		if($webd_main_purpose!='custom'){
			switch ( $translated_text ) {
				case 'Related Products' :
					$translated_text = get_option('webd_text_related')!='' ? get_option('webd_text_related') : esc_html__( 'Related Events', 'WEBDWooEVENT' );
					break;
			}
		}
		return $translated_text;
	}
	function webd_woo_custom_cart_button_text($text) {
	 	$webd_text_join_ev = get_option('webd_text_join_ev');
		global $woocommerce, $post, $product;
		if( $product->is_type( 'external' ) ){
        	return $text;
        }
		$webd_main_purpose = webd_global_main_purpose();
		$webd_layout_purpose = get_post_meta($post->ID,'webd_layout_purpose',true);
		if($webd_main_purpose=='custom' && $webd_layout_purpose=='woo' || $webd_main_purpose=='woo'){
			return get_option('webd_text_add_to_cart')!='' ? get_option('webd_text_add_to_cart') : esc_html__( 'Add To Cart', 'WEBDWooEVENT' );
		}
		if($webd_text_join_ev!=''){
			return $webd_text_join_ev;
		}else{
			return esc_html__( 'Join this Event', 'WEBDWooEVENT' );
		}
	 
	}
	//change text
	function webd_wc_change_product_description_tab_title( $heading ) {
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose=='custom'){
			$heading = get_option('webd_text_details')!='' ? get_option('webd_text_details') : esc_html__('Details','WEBDWooEVENT');
		}else if($webd_main_purpose!='woo'){
			$heading = get_option('webd_text_evdetails')!='' ? get_option('webd_text_evdetails') : esc_html__('Event Details','WEBDWooEVENT');
		}
		return $heading;
	}
	
	function webd_related_products_item( $args ) {
		$webd_related_count = get_option('webd_related_count');
		if(!is_numeric($webd_related_count) || $webd_related_count==''){
			$webd_related_count = 3;
		}
		$args['posts_per_page'] = $webd_related_count; // number related products
		$args['columns'] = 3; 
		return $args;
	}
	//
	//Register sidebars
	function webd_widgets_init() {
		if(get_option('webd_sidebar') !='hide'){
			register_sidebar( array(
				'name' => esc_html__('WooEvent','WEBDWooEVENT'),
				'id' => 'wooevent-sidebar',
				'description' => esc_html__('Sidebar for all pages of WEBDWooEvents.','WEBDWooEVENT'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '<div class="clear"></div></div></div></div>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3><div class="wooe-sidebar"><div class="wooe-wrapper">',
			) );
		}
	}
	// change orderby
	function remove_upsell() {
		//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	}
	// change orderby
	function webd_change_product_orderby( $order ) {
		global $product;
		$webd_listing_order = get_option('webd_listing_order');
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose=='woo' || $webd_main_purpose=='custom'){
			$webd_listing_order = 'def';
		}
		$upc = get_option('webd_text_upc')!='' ? get_option('webd_text_upc') : esc_html__('Upcoming Events','WEBDWooEVENT');
		$def = get_option('webd_text_defa')!='' ? get_option('webd_text_defa') : esc_html__('Default','WEBDWooEVENT');
		$ong = get_option('webd_text_ong')!='' ? get_option('webd_text_ong') : esc_html__('Ongoing Events','WEBDWooEVENT');
		$pas = get_option('webd_text_pas')!='' ? get_option('webd_text_pas') : esc_html__('Past Events','WEBDWooEVENT');
		if( $webd_listing_order == 'all' || $webd_listing_order == 'ontoup' || is_search()){
			$order = array(
				'' => $def,
				'upcoming' => $upc,
				'ongoing' => $ong,
				'past' => $pas,
				
			);
		}elseif( $webd_listing_order!= 'def'){
			$order = array(
				'upcoming' => $upc,
				'ongoing' => $ong,
				'past' => $pas,
				
			);
		}
		return $order;
	}
	// change add to cart link
	function webd_change_product_link( $link ) {
		global $product;
		$product_id = $product->get_id();
		$product_sku = $product->get_sku();
		$vialltrsl = get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__( 'View Details', 'WEBDWooEVENT' );
		$link = '<a href="'.get_permalink().'" rel="nofollow" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" data-quantity="1" class="button add_to_cart_button product_type_variable">'.$vialltrsl.'</a>';
		return $link;
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
	//List item per row
	function webd_loop_columns() {
		return 3; // 3 products per row
	}
	// Shop toolbar
	function webd_woocommerce_shop_search_view_bar() {
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose!='woo' && $webd_main_purpose!='custom'){
			$webd_firstday = get_option('webd_firstday');
			echo do_shortcode('[webd_calendar show_search="1" uwebd_shortcode="0" firstday="'.$webd_firstday.'"]');
		}
	}
	// Single Custom meta 
	function webd_woocommerce_single_ev_meta() {
		global $woocommerce, $post;
		if(wooevent_global_layout() !='layout-2' && wooevent_global_layout() !='layout-3'){
			wooevent_template_plugin('event-meta');
		}
	}
	function webd_woocommerce_single_ev_schedu() {
		global $woocommerce, $post;
		wooevent_template_plugin('event-schedu');
	}
	function webd_remove__shop_loop( $query ) {
		if(is_admin() || (!$query->is_main_query())){ return;}
		$qobj = get_queried_object();
      	if (empty($qobj)){ return;}
  		$time_now =  strtotime("now");
		$webd_shop_view = get_option('webd_shop_view');
		$webd_listing_order = get_option('webd_listing_order');
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose=='woo' || $webd_main_purpose=='custom'){
			
		}else{
		
			if ( ! is_admin() && (is_shop() || is_product_category() && $query->is_main_query() || is_product_tag()) && $query->is_main_query()) {
				if((!isset($_GET['orderby']) && !is_search() && $webd_listing_order != 'all' && $webd_listing_order != 'def' && $webd_listing_order != 'ontoup') || isset($_GET['orderby']) && sanitize_text_field($_GET['orderby'])=='upcoming' ){
					$meta_valu = array(
						array(
							'key'     => 'webd_startdate',
							'value'   => $time_now,
							'compare' => '>',
						),
					);
					$query->set('orderby', 'meta_value_num');
					$query->set('order', 'ASC');
					$query->set('meta_key', 'webd_startdate');
					$query->set('meta_query', $meta_valu);
				}elseif(!isset($_GET['orderby']) && $webd_listing_order =='ontoup' ){
					$meta_valu = array(
						'relation' => 'OR',
						array(
							'key'     => 'webd_startdate',
							'value'   => $time_now,
							'compare' => '>',
						),
						array(
							'key'     => 'webd_enddate',
							'value'   => $time_now,
							'compare' => '>',
						),
					);
					$query->set('orderby', 'meta_value_num');
					$query->set('order', 'ASC');
					$query->set('meta_key', 'webd_startdate');
					$query->set('meta_query', $meta_valu);
				}elseif(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby'])=='ongoing' ){
					$meta_valu = array(
						array(
							'key'     => 'webd_startdate',
							'value'   => $time_now,
							'compare' => '<=',
						),
						array(
							'key'     => 'webd_enddate',
							'value'   => $time_now,
							'compare' => '>',
						),
					);
					$query->set('orderby', 'meta_value_num');
					$query->set('order', 'ASC');
					$query->set('meta_key', 'webd_startdate');
					$query->set('meta_query', $meta_valu);
					return;
				}elseif(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby'])=='past' ){
					$meta_valu = array(
						array(
							'key'     => 'webd_enddate',
							'value'   => $time_now,
							'compare' => '<',
						),
					);
					$query->set('orderby', 'meta_value_num');
					$query->set('order', 'DESC');
					$query->set('meta_key', 'webd_startdate');
					$query->set('meta_query', $meta_valu);
				}
				
				//search map
				if(is_search() && is_shop() && webd_global_search_result_page()=='map'){
					$query->set('posts_per_page', -1);
				}
			}
		}
		
	}
	// Add meta to item of shop
	function webd_woocommerce_shopitem_ev_meta(){
		global $woocommerce, $post;
		$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true );
		
		$webd_eventcolor = webd_event_custom_color($post->ID);
		if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose=='woo'){
			$webd_startdate ='';
		}
		$bgev_color = '';
		if($webd_eventcolor!=""){
			$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
		}

		if($webd_startdate!=''){ 
			echo '<div class="shop-webd-stdate" '.$bgev_color.'><span class="day">'.date_i18n('d', $webd_startdate).'</span>';
			echo '<span class="month">'.date_i18n('M', $webd_startdate).'</span></div>';
		}
	}
	// Add more meta to item of shop
	function webd_woocommerce_shopitem_ev_more_meta(){
		global $woocommerce, $post;
		$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true );
		$webd_enddate = get_post_meta( $post->ID, 'webd_enddate', true );
		$webd_adress = get_post_meta( $post->ID, 'webd_adress', true ) ;
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
		$hml = '
		<div class="shop-webd-more-meta">';
			if($webd_startdate!=''){ 
				$hml .= '
				<span><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).'</span>';
			}
			$hml .= '
			<span><i class="fa fa-shopping-basket"></i>'.$price.'</span>';
			if(get_option('webd_dis_status') !='yes'){
				$hml .= '
				<span>
					<i class="fa fa-ticket"></i>
					'.woo_event_status( $post->ID, $webd_enddate).'
				</span>';
			}
			$hml .= '
		</div>';
		$ft_html = apply_filters( 'webd_shop_ev_meta', $hml, $webd_startdate, $price, $webd_enddate );
		echo $ft_html;
	}
	// Add short des to item of shop
	function webd_woocommerce_shopitem_ev_short_des(){
		global $woocommerce, $post;
		echo '
		<div class="shop-webd-short-des">
			'.apply_filters( 'woocommerce_short_description', $post->post_excerpt ).'
			<div class="cat-meta">'.ex_cat_info('on','product').'</div>
		</div>';
	}
	// Add Social share to item of shop
	function webd_woocommerce_shopitem_ev_share(){
		global $woocommerce, $post;
		echo '<div class="shop-webd-social-share">
			'.webd_social_share().'
		</div>';
	}
	// Email hook
	function woocommerce_email_hook($order){
		$event_details = $order;
		global $event_items;
		$event_items = $event_details->get_items();
		wooevent_template_plugin('email-event-details');

	}
	// Cart hook
	function webd_woocommerce_cart_hook($_product_title,$cart_item, $cart_item_key){
		$webd_startdate = get_post_meta( $cart_item['product_id'], 'webd_startdate', true );
		$webd_enddate = get_post_meta( $cart_item['product_id'], 'webd_enddate', true );
		$all_day = get_post_meta($cart_item['product_id'],'webd_allday', true );
		$html = '<h4>'.$_product_title.'</h4>';
		$stdatetrsl = get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') :  esc_html__('Start Date','WEBDWooEVENT');
			$edatetrsl = get_option('webd_text_edate')!='' ? get_option('webd_text_edate') : esc_html__('End Date','WEBDWooEVENT');
			$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
		if($all_day!='1' && $webd_startdate!=''){
			$html .='<span class="meta-stdate">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate).'</span>';
			if($webd_enddate!=''){
				$html .='<span class="meta-eddate">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate).'</span>';
			}
		}elseif($webd_startdate!=''){
			$html .='<span class="meta-stdate">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).'</span>';
			if($webd_enddate!=''){
				$html .='<span class="meta-eddate">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.$alltrsl.'</span>';
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
	// Next Previous link
	function webd_woocommerce_next_previous_event(){
		$webd_sevent_navi = get_option('webd_sevent_navi');
		if(is_singular('product') && $webd_sevent_navi!='off'){
			global $post;
			$args = array( 'post_type' => 'product', 'posts_per_page' => 1, 'orderby'=> 'meta_value_num', 'order' => 'ASC','meta_key' => 'webd_startdate', 'meta_value' => get_post_meta( $post->ID, 'webd_startdate', true ),  'meta_compare' => '>', );
			$post_nex = get_posts( $args );
			$next_l ='';
			foreach ( $post_nex as $post ) : setup_postdata( $post );
				$next_l = get_the_permalink();
			endforeach; 
			wp_reset_postdata();
			$args_pre = array( 'post_type' => 'product', 'posts_per_page' => 1, 'orderby'=> 'meta_value_num', 'order' => 'DESC','meta_key' => 'webd_startdate', 'meta_value' => get_post_meta( $post->ID, 'webd_startdate', true ),  'meta_compare' => '<', );
			$post_pre = get_posts( $args_pre );
			$previous_l = '';
			foreach ( $post_pre as $post ) : setup_postdata( $post );
				$previous_l = get_the_permalink();
			endforeach; 
			wp_reset_postdata();
			$webd_main_purpose = webd_global_main_purpose();
			$html ='<div class="webd-navigation">';
			if($previous_l!=''){
				if($webd_main_purpose=='woo' || $webd_main_purpose=='custom'){
					$pretrsl = get_option('webd_text_previous')!='' ? get_option('webd_text_previous') :  esc_html__('Previous','WEBDWooEVENT');
					$html .='<div class="previous-event"><a href="'.$previous_l.'" class="btn btn-primary"><i class="fa fa-angle-double-left"></i>'.$pretrsl.'</a></div>';
				}else{
					$preevtrsl = get_option('webd_text_previousev')!='' ? get_option('webd_text_previousev') : esc_html__('Previous Event','WEBDWooEVENT');
					$html .='<div class="previous-event"><a href="'.$previous_l.'" class="btn btn-primary"><i class="fa fa-angle-double-left"></i>'.$preevtrsl.'</a></div>';
				}
			}
			if($next_l!=''){
				if($webd_main_purpose=='woo' || $webd_main_purpose=='custom'){
					$nexttrsl = get_option('webd_text_next')!='' ? get_option('webd_text_next') : esc_html__('Next','WEBDWooEVENT');
					$html .='<div class="next-event"><a href="'.$next_l.'" class="btn btn-primary">'.$nexttrsl.'<i class="fa fa-angle-double-right"></i></a></div>';
				}else{
					$nextevtrsl = get_option('webd_text_nextev')!='' ? get_option('webd_text_nextev') : esc_html__('Next Event','WEBDWooEVENT');
					$html .='<div class="next-event"><a href="'.$next_l.'" class="btn btn-primary">'.$nextevtrsl.'<i class="fa fa-angle-double-right"></i></a></div>';
				}
			}
			$html .='</div><div class="clear"></div>';
			echo  $html;
		}
	}
	// remove related
	function webd_wc_remove_related_products( $args ) {
		$webd_srelated = get_option('webd_srelated');
		if($webd_srelated =='off'){
			return array();
		}else{
			return $args;
		}
	}
}
$WEBD_WooEvent_ShowMeta = new WEBD_WooEvent_ShowMeta();

//-----------------------------------------------------Price Calculation----------------------------------------------------


// Generating dynamically the product "regular price"
add_filter( 'woocommerce_product_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
add_filter( 'woocommerce_product_variation_get_regular_price', 'custom_dynamic_regular_price', 10, 2 );
function custom_dynamic_regular_price( $regular_price, $product ) {
	global $product;
	$product_id = $product->get_id();

	$totayDay  = date("l");
	if($totayDay=='Monday'){
		$price_reg_monday = get_post_meta($product_id,'cost-price-regular-monday',true);
		if($price_reg_monday!='' && $price_reg_monday>0){
			$regular_price = $price_reg_monday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Tuesday'){
		$price_reg_tuesday = get_post_meta($product_id,'cost-price-regular-tuesday',true);
		if($price_reg_tuesday!='' && $price_reg_tuesday>0){
			$regular_price = $price_reg_tuesday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Wednesday'){
		$price_reg_wednesday = get_post_meta($product_id,'cost-price-regular-wednesday',true);
		if($price_reg_wednesday!='' && $price_reg_wednesday>0){
			$regular_price = $price_reg_wednesday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Thursday'){
		$price_reg_thursday = get_post_meta($product_id,'cost-price-regular-thursday',true);
		if($price_reg_thursday!='' && $price_reg_thursday>0){
			$regular_price = $price_reg_thursday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Friday'){
		$price_reg_friday = get_post_meta($product_id,'cost-price-regular-friday',true);
		if($price_reg_friday!='' && $price_reg_friday>0){
			$regular_price = $price_reg_friday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Saturday'){
		$price_reg_saturday = get_post_meta($product_id,'cost-price-regular-saturday',true);
		if($price_reg_saturday!='' && $price_reg_saturday>0){
			$regular_price = $price_reg_saturday;
		}else{
			$regular_price = $regular_price;
		}
	}else if($totayDay=='Sunday'){
		$price_reg_sunday = get_post_meta($product_id,'cost-price-regular-sunday',true);
		if($price_reg_sunday!='' && $price_reg_sunday>0){
			$regular_price = $price_reg_sunday;
		}else{
			$regular_price = $regular_price;
		}
	}
    return $regular_price;
}


// Generating dynamically the product "sale price"
add_filter( 'woocommerce_product_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
add_filter( 'woocommerce_product_variation_get_sale_price', 'custom_dynamic_sale_price', 10, 2 );
function custom_dynamic_sale_price( $sale_price, $product ) {
    global $product;
	$product_id = $product->get_id();

	$totayDay  = date("l");
	if($totayDay=='Monday'){
		$price_sales_monday = get_post_meta($product_id,'cost-price-sale-monday',true);
		if($price_sales_monday!='' && $price_sales_monday>0){
			$sale_price = $price_sales_monday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Tuesday'){
		$price_sales_tuesday = get_post_meta($product_id,'cost-price-sale-tuesday',true);
		if($price_sales_tuesday!='' && $price_sales_tuesday>0){
			$sale_price = $price_sales_tuesday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Wednesday'){
		$price_sales_wednesday = get_post_meta($product_id,'cost-price-sale-wednesday',true);
		if($price_sales_wednesday!='' && $price_sales_wednesday>0){
			$sale_price = $price_sales_wednesday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Thursday'){
		$price_sales_thursday = get_post_meta($product_id,'cost-price-sale-thursday',true);
		if($price_sales_thursday!='' && $price_sales_thursday>0){
			$sale_price = $price_sales_thursday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Friday'){
		$price_sales_friday = get_post_meta($product_id,'cost-price-sale-friday',true);
		if($price_sales_friday!='' && $price_sales_friday>0){
			$sale_price = $price_sales_friday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Saturday'){
		$price_sales_saturday = get_post_meta($product_id,'cost-price-sale-saturday',true);
		if($price_sales_saturday!='' && $price_sales_saturday>0){
			$sale_price = $price_sales_saturday;
		}else{
			$sale_price = $sale_price;
		}
	}else if($totayDay=='Sunday'){
		$price_sales_sunday = get_post_meta($product_id,'cost-price-sale-sunday',true);
		if($price_sales_sunday!='' && $price_sales_sunday>0){
			$sale_price = $price_sales_sunday;
		}else{
			$sale_price = $sale_price;
		}
	}
    return $sale_price;
};

// Displayed formatted regular price + sale price
add_filter( 'woocommerce_get_price_html', 'custom_dynamic_sale_price_html', 20, 2 );
function custom_dynamic_sale_price_html( $price_html, $product ) {
    if( $product->is_type('variable') ) return $price_html;
    $price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display(  $product, array( 'price' => $product->get_sale_price() ) ) ) . $product->get_price_suffix();
    return $price_html;
}