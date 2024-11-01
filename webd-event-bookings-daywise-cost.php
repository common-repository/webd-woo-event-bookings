<?php
/*
Plugin Name: WEBD Woo Event Bookings
Description: An eCommerce event booking toolkit that helps you sell anything with set cost and discount by day wise.
Version: 1.0.0
Author: christopher dadan            
Requires at least: 4.6
Tested up to: 5.7
Requires PHP: 5.6
License: GPLv2 or later
*/

define( 'WEBD_EVENT_BOOKINGS', plugin_dir_url( __FILE__ ) );
if(!function_exists('WEBD_EVENT_BOOKINGS_URL')){
	function WEBD_EVENT_BOOKINGS_URL(){
		return plugin_dir_path(__FILE__);
	}
}
if(!function_exists('WEBD_EVENT_BOOKINGS_EXISTS')){
	function WEBD_EVENT_BOOKINGS_EXISTS() {
		$class = 'notice notice-error';
		$message = esc_html__( 'WooCommerce is Required to WEBDWooEvents plugin work, please install or activate WooCommerce plugin', 'WEBDWooEVENT' );
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_action( 'admin_notices', 'WEBD_EVENT_BOOKINGS_EXISTS' );
		return;
	}
	function webd_mapapi_notice() {
		$webd_api_map = get_option('webd_api_map');
		if($webd_api_map == ''){?>
			<div class="notice notice-warning is-dismissible">
				<p><?php esc_html_e( 'Google Maps APIs now requires API key, To make event map work, You need to create API key here: https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key and add it into WEBDWooEvents > Map Settings > API Key', 'WEBDWooEVENT' ); ?></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'webd_mapapi_notice' );
}
class WEBDWooEvents{ 
	public $webd_template_url;
	public $webd_plugin_path;
	public function __construct()
    {

		$this->includes();
		if(is_admin()){
			$this->register_plugin_settings();
		}
		add_action('after_setup_theme', array(&$this, 'webd_calthumb_register'));
		add_action('admin_enqueue_scripts', array($this, 'webd_webdwooevents_admin_css'));
		add_action('wp_enqueue_scripts', array($this, 'webd_frontend_scripts'));
		add_filter('template_include', array( $this, 'webd_template_loader'),999);
		add_action('wp_enqueue_scripts', array($this, 'webd_frontend_style'),99);
		add_action('wp_head',array( $this, 'webd_custom_css'),100);
		add_action('wp_footer', array( $this,'webd_enqueue_customjs'),99);
    }

	function webd_custom_css(){
		echo '<style type="text/css">';
			require WEBD_EVENT_BOOKINGS_URL(). '/css/custom.css.php';
		echo '</style>';
	}

	function webd_template_loader($template){
		$find = array('single-webd-view.php');
		$file = '';			
		if(is_singular('event-webd-view')){
			$file = 'webd_view/single-webd-view.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;
			if ( $file ) {
				$template = locate_template( $find );
				
				if ( ! $template ) $template = $this->plugin_path() . '/templates/webd_view/single-webd-view.php';
			}
		}elseif(is_post_type_archive( 'event-webd-view' )){
			$file = 'webd_view/webd-view-listing.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;
			if ( $file ) {
				$template = locate_template( $find );
				
				if ( ! $template ) $template = $this->plugin_path() . '/templates/webd_view/webd-view-listing.php';
			}
		}
		$webd_main_purpose = get_option('webd_main_purpose');
		if($webd_main_purpose!='meta'){
			if(is_post_type_archive( 'product' ) || is_tax('product_cat') || is_tax('product_tag') ){
				$file = 'archive-product.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;
				
				if ( $file ) {
					$template = locate_template( $find );
					
					if ( ! $template ){
						$file = 'webd-event-bookings-daywiwebd-cost/archive-product.php';
						$find[] = $file;
						$find[] = $this->template_url . $file;
						$template = locate_template( $find );
						if ( ! $template ){
							$template = $this->plugin_path() . '/templates/archive-product.php';
						}
					}
				}
				
			}
			if(is_singular('product')){
				$file = 'single-product.php';
				$find[] = $file;
				$find[] = $this->template_url . $file;
				
				if ( $file ) {
					$template = locate_template( $find );
					
					if ( ! $template ){
						$file = 'webd-event-bookings-daywiwebd-cost/single-product.php';
						$find[] = $file;
						$find[] = $this->template_url . $file;
						$template = locate_template( $find );
						if ( ! $template ){
							$template = $this->plugin_path() . '/templates/single-product.php';
						}
					}
				}
			}
		}
		return $template;		
	}
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	function register_plugin_settings(){
		global $settings;
		$settings = new WEBD_WooEvent_Settings(__FILE__);
		return $settings;
	}
	//thumbnails register
	function webd_calthumb_register(){
		add_image_size('thumb_150x160',150,160, true);
		add_image_size('thumb_150x140',150,140, true);
		add_image_size('wethumb_300x300',300,300, true);
		add_image_size('wethumb_460x307',460,307, true);
		add_image_size('wethumb_85x85',85,85, true);
	}
	//inculde
	function includes(){
		if(is_admin()){
			require_once  WEBD_EVENT_BOOKINGS_URL().'includes/admin/class-plugin-settings.php';
			include_once WEBD_EVENT_BOOKINGS_URL().'includes/admin/functions.php';
			if(!function_exists('exc_mb_init')){
				if(!class_exists('EXC_MB_Meta_Box')){
					include_once WEBD_EVENT_BOOKINGS_URL().'includes/admin/Meta-Boxes/custom-meta-boxes.php';
				}
			}
			include WEBD_EVENT_BOOKINGS_URL(). 'includes/admin/class-event-meta.php';
		}
		if(get_option('webd_webd_view')!='yes'){
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/webd_view/class-webd-view-post-type.php';
		}
		if(get_option('webd_venue_off')!='yes'){
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/admin/class-venue-post-type.php';
		}
		// Reminder email class
		if(get_option('webd_email_reminder')!='off'){
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/class-email-reminder.php';
		}
		$webd_main_purpose = get_option('webd_main_purpose');
		if($webd_main_purpose!='meta'){
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/class-woo-event.php';
		}else{
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/class-meta-event-only.php';
		}
		if(get_option('webd_multi_attendees')=='yes'){
			require_once WEBD_EVENT_BOOKINGS_URL().'includes/class-checkout-hook.php';
		}
		include_once WEBD_EVENT_BOOKINGS_URL().'includes/submission.php';
		include_once WEBD_EVENT_BOOKINGS_URL().'includes/functions.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/map.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/count-down.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/calendar.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/event-table.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/event-grid.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/event-carousel.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/timeline.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/webd_view-sc.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/webd_view-sc-grid.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/event-search.php';
		include WEBD_EVENT_BOOKINGS_URL().'shortcode/venues-sc.php';
		//widget
		include WEBD_EVENT_BOOKINGS_URL().'widgets/events-search.php';
		include WEBD_EVENT_BOOKINGS_URL().'widgets/latest-events.php';
	}
	/*
	 * Load js and css
	 */
	function webd_webdwooevents_admin_css(){
		$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'jquery', 'woo_events', $js_params  );
		// CSS for button styling
		wp_enqueue_style("wooevent_admin_style", WEBD_EVENT_BOOKINGS . 'assets/css/style.css', array(), '3.5');
		$webd_api_map = get_option('webd_api_map');
		if($webd_api_map!=''){
		wp_enqueue_script( 'wooevent-auto-map', '//maps.googleapis.com/maps/api/js?key='.$webd_api_map.'&libraries=places');
		}
		wp_enqueue_script( 'wooevent-admin-js', WEBD_EVENT_BOOKINGS . 'assets/js/admin.js', array( 'jquery' ), '3.5'  );
	}
	function webd_frontend_scripts(){
		$webd_fontawesome = get_option('webd_fontawesome');
		if($webd_fontawesome!='on'){
			wp_enqueue_style('webd-font-awesome', WEBD_EVENT_BOOKINGS.'css/font-awesome/css/font-awesome.min.css');
		}
		$webd_boostrap_css = get_option('webd_boostrap_css');
		if($webd_boostrap_css!='on'){
			wp_enqueue_style('webd-bootstrap-min', WEBD_EVENT_BOOKINGS.'js/bootstrap/bootstrap.min.css',array(), '3.5');
		}
		$main_font_default='Source Sans Pro';
		$meta_font_default='Ubuntu';
		$g_fonts = array($main_font_default, $meta_font_default);
		$webd_fontfamily = get_option('webd_fontfamily');
		if($webd_fontfamily!=''){
			$webd_fontfamily = webd_get_google_font_name($webd_fontfamily);
			array_push($g_fonts, $webd_fontfamily);
		}
		$webd_hfont = get_option('webd_hfont');
		if($webd_hfont!=''){
			$webd_hfont = webd_get_google_font_name($webd_hfont);
			array_push($g_fonts, $webd_hfont);
		}
		$webd_metafont = get_option('webd_metafont');
		if($webd_metafont!=''){
			$webd_metafont = webd_get_google_font_name($webd_metafont);
			array_push($g_fonts, $webd_metafont);
		}
		$webd_googlefont_js = get_option('webd_googlefont_js');
		if($webd_googlefont_js!='on'){
			wp_enqueue_style( 'wooevent-google-fonts', webd_get_google_fonts_url($g_fonts), array(), '1.0.0' );
		}
		wp_enqueue_style('fullcalendar', WEBD_EVENT_BOOKINGS.'js/fullcalendar/fullcalendar.min.css');
		wp_enqueue_style('qtip-css', WEBD_EVENT_BOOKINGS.'js/fullcalendar/lib/qtip/jquery.qtip.min.css');
		if(get_option('webd_owl_js')!='on'){
			wp_enqueue_style( 'webd-owl-carousel', WEBD_EVENT_BOOKINGS .'js/owl-carousel/owl.carousel.css');
			wp_enqueue_script( 'webd-owl-carousel', WEBD_EVENT_BOOKINGS. 'js/owl-carousel/owl.carousel.min.js', array('jquery'), '2.0', true );
		}

		wp_enqueue_script( 'woo-event',plugins_url('/js/plugin-script.js', __FILE__) , array( 'jquery' ), '3.5.4', true  );
	}
	function webd_frontend_style(){
		$webd_main_purpose = get_option('webd_main_purpose');
		$webd_plugin_style = get_option('webd_plugin_style');
		if($webd_plugin_style!='off'){
			$webd_main_purpose = get_option('webd_main_purpose');
			if($webd_main_purpose!='meta'){
				wp_enqueue_style('woo-event-css', WEBD_EVENT_BOOKINGS.'css/style.css', array(), '3.5');
			}else{
				wp_enqueue_style('woo-event-css', WEBD_EVENT_BOOKINGS.'css/meta-style.css', array(), '3.5');
			}
		}
		if($webd_main_purpose=='woo' || $webd_main_purpose=='custom'){
			wp_enqueue_style('webd-woo-style', WEBD_EVENT_BOOKINGS.'css/woo-style.css');
		}
		wp_enqueue_style('webd-general', WEBD_EVENT_BOOKINGS.'css/general.css', array(), '3.5');
	}
	function webd_enqueue_customjs() {
		$webd_custom_code = get_option('webd_custom_code');
		if($webd_custom_code!=''){
			echo '<script>'.$webd_custom_code.'</script>';
		}
	}
}
$WEBDWooEvents = new WEBDWooEvents();