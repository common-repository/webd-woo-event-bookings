<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WEBD_WooEvent_Settings {
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;
	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = '';
		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );
		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}
	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}
	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_menu_page( esc_html__( 'Settings', 'WEBDWooEVENT' ) , esc_html__( 'WEBDWooEvents', 'WEBDWooEVENT' ) , 'manage_options' , 'webdwooevents' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}
	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {
		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    wp_enqueue_script( 'farbtastic' );
    // We're including the WP media scripts here because they're needed for the image upload field
    // If you're not including an image upload then you can leave this function call out
    wp_enqueue_media();
    wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    wp_enqueue_script( 'wpt-admin-js' );
	}
	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=webdwooevents">' . esc_html__( 'Settings', 'WEBDWooEVENT' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		$settings['general'] = array(
			'title'					=> esc_html__( 'Main', 'WEBDWooEVENT' ),
			'description'			=> esc_html__( '', 'WEBDWooEVENT' ),
			'fields'				=> array(
				array(
					'id' 			=> 'webd_main_purpose',
					'label'			=> esc_html__( 'Your Purpose', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( '1) Select "Events" to choose product as event in each product. 2) Select "Styling for WooCommerce" to use your theme style layout as WooCommerce. 3) Select "Custom" to use main layout as WooCommerce. 4) Select "Only use metadata" to use your theme style.', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'event'     => esc_html__( 'Events', 'WEBDWooEVENT' ),
						'woo'       => esc_html__( 'Styling for WooCommerce', 'WEBDWooEVENT' ),
						'custom'    => esc_html__( 'Custom', 'WEBDWooEVENT' ),
						'meta'      => esc_html__( 'Only use metadata', 'WEBDWooEVENT' )
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_main_color',
					'label'			=> esc_html__( 'Events Color', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Choose main color of WEBDWooEvents', 'WEBDWooEVENT' ),
					'type'			=> 'color',
					'placeholder'	=> '',
					'default'		=> '#2544ad'
				),
				array(
					'id' 			=> 'webd_fontfamily',
					'label'			=> esc_html__( 'Events Font Family', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter Google font-family name here. For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_fontsize',
					'label'			=> esc_html__( 'Events Font Size', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter size of font, Ex: 13px', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_hfont',
					'label'			=> esc_html__( 'Heading Font Family', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter Google font-family name here. For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> '',
				),
				array(
					'id' 			=> 'webd_hfontsize',
					'label'			=> esc_html__( 'Heading Font Size', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter size of font, Ex: 20px', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_metafont',
					'label'			=> esc_html__( 'Meta Font Family', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter Google font-family name here. For example, if you choose "Ubuntu" Google Font, enter Ubuntu', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> '',
				),
				array(
					'id' 			=> 'webd_matafontsize',
					'label'			=> esc_html__( 'Meta Font Size', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter size of font, Ex: 12px', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_sidebar',
					'label'			=> esc_html__( 'Sidebar', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select hide to use sidebar of theme', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'right' => esc_html__( 'Right', 'WEBDWooEVENT' ),
						'left' => esc_html__( 'Left', 'WEBDWooEVENT' ),
						'hide' => esc_html__( 'Hide', 'WEBDWooEVENT' )
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_calendar_lg',
					'label'			=> esc_html__( 'Calendar Language', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select language of Calendar', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'en' => esc_html__( 'en', 'WEBDWooEVENT' ),
						'af' => esc_html__( 'af', 'WEBDWooEVENT' ),
						'ar-dz' => esc_html__( 'ar-dz', 'WEBDWooEVENT' ),
						'ar-kw' => esc_html__( 'ar-kw', 'WEBDWooEVENT' ),
						'ar-ly' => esc_html__( 'ar-ly', 'WEBDWooEVENT' ),
						'ar-ma' => esc_html__( 'ar-ma', 'WEBDWooEVENT' ),
						'ar-sa' => esc_html__( 'ar-sa', 'WEBDWooEVENT' ),
						'ar-tn' => esc_html__( 'ar-tn', 'WEBDWooEVENT' ),
						'ar' => esc_html__( 'ar', 'WEBDWooEVENT' ),
						'bg' => esc_html__( 'bg', 'WEBDWooEVENT' ),
						'bs' => esc_html__( 'bs', 'WEBDWooEVENT' ),
						'ca' => esc_html__( 'ca', 'WEBDWooEVENT' ),
						'cs' => esc_html__( 'cs', 'WEBDWooEVENT' ),
						'da' => esc_html__( 'da', 'WEBDWooEVENT' ),
						'de-at' => esc_html__( 'de-at', 'WEBDWooEVENT' ),
						'de-ch' => esc_html__( 'de-ch', 'WEBDWooEVENT' ),
						'de' => esc_html__( 'de', 'WEBDWooEVENT' ),
						'el' => esc_html__( 'el', 'WEBDWooEVENT' ),
						'en-au' => esc_html__( 'en-au', 'WEBDWooEVENT' ),
						'en-ca' => esc_html__( 'en-ca', 'WEBDWooEVENT' ),
						'en-gb' => esc_html__( 'en-gb', 'WEBDWooEVENT' ),
						'en-ie' => esc_html__( 'en-ie', 'WEBDWooEVENT' ),
						'en-nz' => esc_html__( 'en-nz', 'WEBDWooEVENT' ),
						'es-do' => esc_html__( 'es-do', 'WEBDWooEVENT' ),
						'es-us' => esc_html__( 'es-us', 'WEBDWooEVENT' ),
						'es' => esc_html__( 'es', 'WEBDWooEVENT' ),
						'et' => esc_html__( 'et', 'WEBDWooEVENT' ),
						'eu' => esc_html__( 'eu', 'WEBDWooEVENT' ),
						'fa' => esc_html__( 'fa', 'WEBDWooEVENT' ),
						'fi' => esc_html__( 'fi', 'WEBDWooEVENT' ),
						'fr-ca' => esc_html__( 'fr-ca', 'WEBDWooEVENT' ),
						'fr-ch' => esc_html__( 'fr-ch', 'WEBDWooEVENT' ),
						'fr' => esc_html__( 'fr', 'WEBDWooEVENT' ),
						'gl' => esc_html__( 'gl', 'WEBDWooEVENT' ),
						'he' => esc_html__( 'he', 'WEBDWooEVENT' ),
						'hi' => esc_html__( 'hi', 'WEBDWooEVENT' ),
						'hr' => esc_html__( 'hr', 'WEBDWooEVENT' ),
						'hu' => esc_html__( 'hu', 'WEBDWooEVENT' ),
						'id' => esc_html__( 'id', 'WEBDWooEVENT' ),
						'is' => esc_html__( 'is', 'WEBDWooEVENT' ),
						'it' => esc_html__( 'it', 'WEBDWooEVENT' ),
						'ja' => esc_html__( 'ja', 'WEBDWooEVENT' ),
						'ka' => esc_html__( 'ka', 'WEBDWooEVENT' ),
						'kk' => esc_html__( 'kk', 'WEBDWooEVENT' ),
						'ko' => esc_html__( 'ko', 'WEBDWooEVENT' ),
						'lb' => esc_html__( 'lb', 'WEBDWooEVENT' ),
						'lt' => esc_html__( 'lt', 'WEBDWooEVENT' ),
						'lv' => esc_html__( 'lv', 'WEBDWooEVENT' ),
						'mk' => esc_html__( 'mk', 'WEBDWooEVENT' ),
						'ms-my' => esc_html__( 'ms-my', 'WEBDWooEVENT' ),
						'ms' => esc_html__( 'ms', 'WEBDWooEVENT' ),
						'nb' => esc_html__( 'nb', 'WEBDWooEVENT' ),
						'nl-be' => esc_html__( 'nl-be', 'WEBDWooEVENT' ),
						'nn' => esc_html__( 'nn', 'WEBDWooEVENT' ),
						'nl' => esc_html__( 'nl', 'WEBDWooEVENT' ),
						'pl' => esc_html__( 'pl', 'WEBDWooEVENT' ),
						'pt-br' => esc_html__( 'pt-br', 'WEBDWooEVENT' ),
						'pt' => esc_html__( 'pt', 'WEBDWooEVENT' ),
						'ro' => esc_html__( 'ro', 'WEBDWooEVENT' ),
						'ru' => esc_html__( 'ru', 'WEBDWooEVENT' ),
						'sk' => esc_html__( 'sk', 'WEBDWooEVENT' ),
						'sq' => esc_html__( 'sq', 'WEBDWooEVENT' ),
						'sl' => esc_html__( 'sl', 'WEBDWooEVENT' ),
						'sr-cyrl' => esc_html__( 'sr-cyrl', 'WEBDWooEVENT' ),
						'sr' => esc_html__( 'sr', 'WEBDWooEVENT' ),
						'sv' => esc_html__( 'sv', 'WEBDWooEVENT' ),
						'th' => esc_html__( 'th', 'WEBDWooEVENT' ),
						'tr' => esc_html__( 'tr', 'WEBDWooEVENT' ),
						'uk' => esc_html__( 'uk', 'WEBDWooEVENT' ),
						'vi' => esc_html__( 'vi', 'WEBDWooEVENT' ),
						'zh-cn' => esc_html__( 'zh-cn', 'WEBDWooEVENT' ),
						'zh-tw' => esc_html__( 'zh-tw', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_webd_view_slug',
					'label'			=> esc_html__( 'Speaker Slug' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Remember to save the permalink settings again in Settings > Permalinks', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__('', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_firstday',
					'label'			=> esc_html__( 'Calendar day of week begins', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'1' => esc_html__('Monday', 'WEBDWooEVENT'),'2' => esc_html__('Tuesday', 'WEBDWooEVENT'),'3' => esc_html__('Wednesday', 'WEBDWooEVENT'),'4' => esc_html__('Thursday', 'WEBDWooEVENT'),'5' => esc_html__('Friday', 'WEBDWooEVENT'),'6' => esc_html__('Saturday', 'WEBDWooEVENT'),'7' => esc_html__('Sunday', 'WEBDWooEVENT'),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_shop_view',
					'label'			=> esc_html__( 'Listing default view', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'month' => esc_html__( 'Calendar Month', 'WEBDWooEVENT' ),
						'list' => esc_html__( 'List', 'WEBDWooEVENT' ),
						'map' => esc_html__( 'Map', 'WEBDWooEVENT' ),
						'week' => esc_html__( 'Agenda Week', 'WEBDWooEVENT' ),
						'day' => esc_html__( 'Agenda Day', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_listing_order',
					'label'			=> esc_html__( 'Listing default order', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'upcoming' => esc_html__( 'Upcoming', 'WEBDWooEVENT' ),
						'ontoup' => esc_html__( 'Ongoing and Upcoming', 'WEBDWooEVENT' ),
						'all' => esc_html__( 'Default order', 'WEBDWooEVENT' ),
						'def' => esc_html__( 'Default order bar of Woocommerce', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_enable_cart',
					'label'			=> esc_html__( 'Enable redirect to Checkout page', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Redirect to the Checkout page after successful addition', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'on' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_auto_color',
					'label'			=> esc_html__( 'Auto change color when low stock', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'This feature allow user can see product has color Red when out of stock or Yellow when the seats are between the limit and 0 in listing page', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_cat_ctcolor',
					'label'			=> esc_html__( 'Enable custom color for category', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_stop_booking',
					'label'			=> esc_html__( 'Stop booking before event start', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'This feature allow you can block all booking before X day before event start', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_webd_view',
					'label'			=> esc_html__( 'Disable webd_view feature', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Disable webd_view feature if you dont use it', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_venue_off',
					'label'			=> esc_html__( 'Disable Default Venue feature', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Disable Default Venue if you dont use it', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_venue_slug',
					'label'			=> esc_html__( 'Venue Slug' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Remember to save the permalink settings again in Settings > Permalinks', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__('', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_multi_attendees',
					'label'			=> esc_html__( 'Enable multiple attendees info', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Show field for enter name & email for each ticket', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_attendees_required',
					'label'			=> esc_html__( 'Attendees info field required', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select yes to make Attendees info field is required', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_delete_passevent',
					'label'			=> esc_html__( 'Enable clear event has passed', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Allow delete event has passed before this time 1 day in admin listing event page in one click', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				
				array(
					'id' 			=> 'webd_show_timezone',
					'label'			=> esc_html__( 'Show timezone', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Show timezone in single event & Email order', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_schema',
					'label'			=> esc_html__( 'Enable google event schema', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Mark up your organized events so that users can discover events through Google Search results and other Google products like Google Maps.', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				
			)
		);
		$settings['map_settings'] = array(
			'title'					=> esc_html__( 'Map Settings', 'WEBDWooEVENT' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'webd_api_map',
					'label'			=> esc_html__( 'API Key' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Google Maps APIs now requires a key, you can check how to create api key here: https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_smap',
					'label'			=> esc_html__( 'Map Icon' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select icon default of Map', 'WEBDWooEVENT' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_zoom_map',
					'label'			=> esc_html__( 'Map Zoom' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter number, default: 1', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_map_style',
					'label'			=> esc_html__( 'Paste custom code style of map' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Choose custom code style of map here: https://snazzymaps.com/explore?sort=popular', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_map_lg',
					'label'			=> esc_html__( 'Map Language', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select language of Google Map', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'en' => esc_html__( 'English', 'WEBDWooEVENT' ),
						'ar' => esc_html__( 'Arabic', 'WEBDWooEVENT' ),
						'bg' => esc_html__( 'Bulgarian', 'WEBDWooEVENT' ),
						'bn' => esc_html__( 'Bengali', 'WEBDWooEVENT' ),
						'ca' => esc_html__( 'Catalan', 'WEBDWooEVENT' ),
						'cs' => esc_html__( 'Czech', 'WEBDWooEVENT' ),
						'ca' => esc_html__( 'ca', 'WEBDWooEVENT' ),
						'da' => esc_html__( 'Danish', 'WEBDWooEVENT' ),
						'de' => esc_html__( 'German', 'WEBDWooEVENT' ),
						'el' => esc_html__( 'Greek', 'WEBDWooEVENT' ),
						'en-AU' => esc_html__( 'English (Australian)', 'WEBDWooEVENT' ),
						'en-GB' => esc_html__( 'English (Great Britain)', 'WEBDWooEVENT' ),
						'es' => esc_html__( 'Spanish', 'WEBDWooEVENT' ),
						'fa' => esc_html__( 'Farsi', 'WEBDWooEVENT' ),
						'eu' => esc_html__( 'Basque', 'WEBDWooEVENT' ),
						'fi' => esc_html__( 'Finnish', 'WEBDWooEVENT' ),
						'fil' => esc_html__( 'Filipino', 'WEBDWooEVENT' ),
						'fr' => esc_html__( 'French', 'WEBDWooEVENT' ),
						'gl' => esc_html__( 'Galician', 'WEBDWooEVENT' ),
						'gu' => esc_html__( 'Gujarati', 'WEBDWooEVENT' ),
						'hi' => esc_html__( 'Hindi', 'WEBDWooEVENT' ),
						'hr' => esc_html__( 'Croatian', 'WEBDWooEVENT' ),
						'hu' => esc_html__( 'Hungarian', 'WEBDWooEVENT' ),
						'id' => esc_html__( 'Indonesian', 'WEBDWooEVENT' ),
						'it' => esc_html__( 'Italian', 'WEBDWooEVENT' ),
						'iw' => esc_html__( 'Hebrew', 'WEBDWooEVENT' ),
						'ja' => esc_html__( 'Japanese', 'WEBDWooEVENT' ),
						'kn' => esc_html__( 'Kannada', 'WEBDWooEVENT' ),
						'ko' => esc_html__( 'Korean', 'WEBDWooEVENT' ),
						'lt' => esc_html__( 'Lithuanian', 'WEBDWooEVENT' ),
						'lv' => esc_html__( 'Latvian', 'WEBDWooEVENT' ),
						'ml' => esc_html__( 'Malayalam', 'WEBDWooEVENT' ),
						'mr' => esc_html__( 'Marathi', 'WEBDWooEVENT' ),
						'nl' => esc_html__( 'Dutch', 'WEBDWooEVENT' ),
						'no' => esc_html__( 'Norwegian', 'WEBDWooEVENT' ),
						'pl' => esc_html__( 'Polish', 'WEBDWooEVENT' ),
						'pt' => esc_html__( 'Portuguese', 'WEBDWooEVENT' ),
						'pt-BR' => esc_html__( 'Portuguese (Brazil)', 'WEBDWooEVENT' ),
						'pt-PT' => esc_html__( 'Portuguese (Portugal)', 'WEBDWooEVENT' ),
						'ro' => esc_html__( 'Romanian', 'WEBDWooEVENT' ),
						'ru' => esc_html__( 'Russian', 'WEBDWooEVENT' ),
						'sk' => esc_html__( 'Slovak', 'WEBDWooEVENT' ),
						'sl' => esc_html__( 'Slovenian', 'WEBDWooEVENT' ),
						'sr' => esc_html__( 'Serbian', 'WEBDWooEVENT' ),
						'sv' => esc_html__( 'Swedish', 'WEBDWooEVENT' ),
						'ta' => esc_html__( 'Tamil', 'WEBDWooEVENT' ),
						'te' => esc_html__( 'Telugu', 'WEBDWooEVENT' ),
						'th' => esc_html__( 'Thai', 'WEBDWooEVENT' ),
						'tl' => esc_html__( 'Tagalog', 'WEBDWooEVENT' ),
						'tr' => esc_html__( 'Turkish', 'WEBDWooEVENT' ),
						'uk' => esc_html__( 'Ukrainian', 'WEBDWooEVENT' ),
						'vi' => esc_html__( 'Vietnamese', 'WEBDWooEVENT' ),
						'zh-CN' => esc_html__( 'Chinese (Simplified)', 'WEBDWooEVENT' ),
						'zh-TW' => esc_html__( 'Chinese (Traditional)', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
			)
		);
		$layout = array( 'layout-1' => esc_html__( 'Layout 1', 'WEBDWooEVENT' ), 'layout-2' => esc_html__( 'Layout 2', 'WEBDWooEVENT' ), 'layout-3' => esc_html__( 'Layout 3', 'WEBDWooEVENT' ));
		$layout = apply_filters( 'webd_change_def_layout_meta', $layout );
		$settings['single_event'] = array(
			'title'					=> esc_html__( 'Single Event', 'WEBDWooEVENT' ),
			'description'			=> esc_html__( '', 'WEBDWooEVENT' ),
			'fields'				=> array(
				array(
					'id' 			=> 'webd_slayout',
					'label'			=> esc_html__( 'Layout', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select default layout of single event', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> $layout,
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_ssocial',
					'label'			=> esc_html__( 'Show Social Share', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Show/hide Social Share section', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Hide', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_srelated',
					'label'			=> esc_html__( 'Show related', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Show/hide Related Event section', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Hide', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_related_count',
					'label'			=> esc_html__( 'Number of related' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter number, default 3', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_sevent_navi',
					'label'			=> esc_html__( 'Show Event Navigation', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Show/hide Event Navigation section', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Hide', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_single_map',
					'label'			=> esc_html__( 'Default Map Style', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_click_remove',
					'label'			=> esc_html__( 'Remove click', 'WEBDWooEVENT' ),
					'description'	=> esc_html__('Remove click event on qty button when your theme has already added this event', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_enable_review',
					'label'			=> esc_html__( 'Enable Review', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_enable_sginfo',
					'label'			=> esc_html__( 'User Signed up info', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enable show info if user has signed up event', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
			)
		);
		
		$webd_main_purpose = webd_global_main_purpose();
		if($webd_main_purpose=='meta'){
			unset ($settings['general']['fields'][8],$settings['general']['fields'][12],$settings['general']['fields'][13]);
			unset ($settings['search-page'],$settings['single_event']);
			$settings['single_event'] = array(
				'title'					=> esc_html__( 'Single Event', 'WEBDWooEVENT' ),
				'description'			=> esc_html__( '', 'WEBDWooEVENT' ),
				'fields'				=> array(
					array(
						'id' 			=> 'webd_slayout_purpose',
						'label'			=> esc_html__( 'Default Layout Purpose', 'WEBDWooEVENT' ),
						'description'	=> esc_html__( 'Select default layout of single event', 'WEBDWooEVENT' ),
						'type'			=> 'select',
						'options'		=> array( 
							'woo' => esc_html__( 'WooCommere', 'WEBDWooEVENT' ),
							'event' => esc_html__( 'Event', 'WEBDWooEVENT' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'webd_ssocial',
						'label'			=> esc_html__( 'Show Social Share', 'WEBDWooEVENT' ),
						'description'	=> esc_html__( 'Show/hide Social Share section', 'WEBDWooEVENT' ),
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'Show', 'WEBDWooEVENT' ),
							'off' => esc_html__( 'Hide', 'WEBDWooEVENT' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'webd_single_map',
						'label'			=> esc_html__( 'Default Map Style', 'WEBDWooEVENT' ),
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'No', 'WEBDWooEVENT' ),
							'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
						),
						'default'		=> ''
					),				
				
				)
			);
		}
		$settings['single_event']['fields'][]= array(
			'id' 			=> 'webd_dis_status',
			'label'			=> esc_html__( 'Disable tickets status info', 'WEBDWooEVENT' ),
			'description'	=> '',
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]= array(
			'id' 			=> 'webd_dis_hassold',
			'label'			=> esc_html__( 'Disable tickets has sold info', 'WEBDWooEVENT' ),
			'description'	=> '',
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]= array(
			'id' 			=> 'webd_date_picker',
			'label'			=> esc_html__( 'Admin date picker format', 'WEBDWooEVENT' ),
			'description'	=> '',
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'Default', 'WEBDWooEVENT' ),
				'dmy' => esc_html__( 'EU dd.mm.yyyy 24h', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]= array(
			'id' 			=> 'webd_sunsire_set',
			'label'			=> esc_html__( 'Enable sunset/sunrise feature', 'WEBDWooEVENT' ),
			'description'	=> '',
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]=  array(
			'id' 			=> 'webd_enable_recstock',
			'label'			=> esc_html__( 'Enable update recurring event stock number', 'WEBDWooEVENT' ),
			'description'	=> esc_html__( 'Select Yes if you want to auto update stock for each recurring event when select Edit all feature', 'WEBDWooEVENT' ),
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]=  array(
			'id' 			=> 'webd_enable_subtitle',
			'label'			=> esc_html__( 'Enable event subtitle', 'WEBDWooEVENT' ),
			'description'	=> esc_html__( 'Select Yes to enable this feature', 'WEBDWooEVENT' ),
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		$settings['single_event']['fields'][]=  array(
			'id' 			=> 'webd_enable_livetotal',
			'label'			=> esc_html__( 'Enable Live total', 'WEBDWooEVENT' ),
			'description'	=> esc_html__( 'Select Yes to enable this feature', 'WEBDWooEVENT' ),
			'type'			=> 'select',
			'options'		=> array( 
				'' => esc_html__( 'No', 'WEBDWooEVENT' ),
				'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
				),
			'default'		=> ''
		);
		
		$settings['search-page'] = array(
			'title'					=> esc_html__( 'Search Section', 'WEBDWooEVENT' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'webd_search_enable',
					'label'			=> esc_html__( 'Enable search bar in shop page', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Enable', 'WEBDWooEVENT' ),
						'disable' => esc_html__( 'Disable', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_search_ajax',
					'label'			=> esc_html__( 'Ajax search', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_search_result',
					'label'			=> esc_html__( 'Show search result in', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Not working with ajax search', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Listing', 'WEBDWooEVENT' ),
						'map' => esc_html__( 'Map', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_scat_include',
					'label'			=> esc_html__( 'Category include' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'List of cat ID (or slug), separated by a comma, leave blank to show all category in dropdown', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_stag_include',
					'label'			=> esc_html__( 'Tags include' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'List of Tag ID (or slug), separated by a comma, leave blank to show all Tags ', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_syear_include',
					'label'			=> esc_html__( 'Event Years filter include' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'List of year, separated by a comma, ex:2016,2017. Leave blank to show 5 year nearest', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_loca_include',
					'label'			=> esc_html__( 'Event location filter include' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'List of location, separated by a comma, ex:16,17. Leave blank to show all, enter hide to hide this field', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				
			)
		);
		$settings['email-reminder'] = array(
			'title'					=> esc_html__( 'Email Settings', 'WEBDWooEVENT' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'webd_email_reminder',
					'label'			=> esc_html__( 'Email reminder', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select off to disable this feature', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'On', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_emreminder_atte',
					'label'			=> esc_html__( 'Email reminder to all attendees', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select Yes to allow send email reminder to all attendees instead of send to only use who booked event ', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'yes' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_emreminder_single',
					'label'			=> esc_html__( 'Enable setting per event', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select On to enable Email reminder setting per each event edit page', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_delay',
					'label'			=> esc_html__( 'Time for sending', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Time for sending the email notification before Event start', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_email_timeformat',
					'label'			=> esc_html__( 'Type', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select type of time', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => '',
						'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
						'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
						'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
						'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
						'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
						'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_subject',
					'label'			=> esc_html__( 'Email Subject', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter subject for Email reminder', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_email_content',
					'label'			=> esc_html__( 'Content of Email', 'WEBDWooEVENT' ),
					'description'	=> 'You can use [eventitle] for event title and [eventdate] for date of event and [eventlink] for link of event and [customer_name] for name of customer',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_emdelay_2',
					'label'			=> esc_html__( 'Second Time sending the email reminder', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_emtimeformat_2',
					'label'			=> esc_html__( 'Type', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => '',
						'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
						'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
						'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
						'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
						'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
						'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_content_2',
					'label'			=> esc_html__( 'Content of Second Email', 'WEBDWooEVENT' ),
					'description'	=> 'You can use [eventitle] for event title and [eventdate] for date of event and [eventlink] for link of event and [customer_name] for name of customer',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_emdelay_3',
					'label'			=> esc_html__( 'Third Time sending the email reminder', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_emtimeformat_3',
					'label'			=> esc_html__( 'Type', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => '',
						'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
						'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
						'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
						'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
						'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
						'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_content_3',
					'label'			=> esc_html__( 'Content of Third Email', 'WEBDWooEVENT' ),
					'description'	=> 'You can use [eventitle] for event title and [eventdate] for date of event and [eventlink] for link of event and [customer_name] for name of customer',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_email_reminder_fb',
					'label'			=> esc_html__( 'Email reminder Feedback & Thank you', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'This feature allow you can send automatic email thank you or reminder review or feedback to user has bought event after X time event end.', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'On', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_reminder_fbsg',
					'label'			=> esc_html__( 'Enable setting per event', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select On to enable Email reminder Feedback & Thank you setting on each event edit page', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_fbdelay',
					'label'			=> esc_html__( 'Time for sending the email', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Time for sending the email feedback notification after Event end', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_email_fbtimefm',
					'label'			=> esc_html__( 'Type of time', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Select type of time', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => '',
						'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
						'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
						'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_email_fbsubject',
					'label'			=> esc_html__( 'Email Subject', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Enter subject for Email Feedback', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_email_fbcontent',
					'label'			=> esc_html__( 'Content of Email Feedback', 'WEBDWooEVENT' ),
					'description'	=> 'You can use [eventitle] for event title and [eventdate] for date of event and [eventlink] for link of event',
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				
			)
		);
		$settings['submit-event'] = array(
			'title'					=> esc_html__( 'Include/Exclude Events', 'WEBDWooEVENT' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'webd_sm_cat',
					'label'			=> esc_html__( 'Exclude Category checkbox', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter list id of category, ex: 23,66,1', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_sm_cat_in',
					'label'			=> esc_html__( 'Include Category checkbox', 'WEBDWooEVENT' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter list id of category, ex: 23,66,1', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_sm_datefm',
					'label'			=> esc_html__( 'Date Format', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Default: mm/dd/yyyy', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_sm_timefm',
					'label'			=> esc_html__( 'Time format', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Default: h:i A (enter: H:i to show 24 hour format)', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_sm_notify',
					'label'			=> esc_html__( 'Email Notification', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Send notification email to user when email is published', 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'0' => esc_html__( 'Off', 'WEBDWooEVENT' ),
						'1' => esc_html__( 'On', 'WEBDWooEVENT' ),
						),
					'default'		=> ''
				),
			)
		);
		$settings['js_css_settings'] = array(
			'title'					=> esc_html__( 'Custom Style', 'WEBDWooEVENT' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'webd_custom_css',
					'label'			=> esc_html__( 'Custom css' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add custom CSS code to the plugin without modifying files', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_custom_code',
					'label'			=> esc_html__( 'Custom js' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add custom js code to the plugin without modifying files', 'WEBDWooEVENT' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'webd_fontawesome',
					'label'			=> esc_html__( 'Turn off Font Awesome', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading plugin's Font Awesome. Check if your theme has already loaded this library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_boostrap_css',
					'label'			=> esc_html__( 'Turn off Bootstrap Css file', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading plugin's Bootstrap library. Check if your theme has already loaded this library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_googlemap_js',
					'label'			=> esc_html__( 'Turn off Google Map Api Js file', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading plugin's Google Map Api Js file. Check if your theme has already loaded this library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_googlefont_js',
					'label'			=> esc_html__( 'Turn off Google Font', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading Google Font", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_owl_js',
					'label'			=> esc_html__( 'Turn off Owl Carousel library', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading Owl Carousel library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_jscolor_js',
					'label'			=> esc_html__( 'Turn off Jscolor js', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading Jscolor js. Check if your theme has already loaded this library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_qtip_js',
					'label'			=> esc_html__( 'Turn off Qtip js', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Turn off loading Qtip js. Check if your theme has already loaded this library", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'WEBDWooEVENT' ),
						'on' => esc_html__( 'Yes', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'webd_plugin_style',
					'label'			=> esc_html__( 'Plugin Style', 'WEBDWooEVENT' ),
					'description'	=> esc_html__( "Select Off to disable load plugin style", 'WEBDWooEVENT' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Default', 'WEBDWooEVENT' ),
						//'basic' => esc_html__( 'Basic', 'WEBDWooEVENT' ),
						'off' => esc_html__( 'Off', 'WEBDWooEVENT' ),
					),
					'default'		=> ''
				),
			)
		);
		$settings['static-text'] = array(
			'title'					=> esc_html__( 'Static Text Changes', 'WEBDWooEVENT' ),
			'description'			=> esc_html__( '', 'WEBDWooEVENT' ),
			'fields'				=> array(
				array(
					'id' 			=> 'webd_text_webd_view',
					'label'			=> esc_html__( 'Speaker' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_join_ev',
					'label'			=> esc_html__( 'Join this Event' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_join_now',
					'label'			=> esc_html__( 'Join Now' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_related',
					'label'			=> esc_html__( 'Related Events' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_event_pass',
					'label'			=> esc_html__( 'This event has passed' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_add_to_cart',
					'label'			=> esc_html__( 'Add To Cart' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_details',
					'label'			=> esc_html__( 'Details' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evdetails',
					'label'			=> esc_html__( 'Event Details' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_viewdetails',
					'label'			=> esc_html__( 'View Details' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_ical',
					'label'			=> esc_html__( '+ Ical Import' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_ggcal',
					'label'			=> esc_html__( '+ Google calendar' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_from',
					'label'			=> esc_html__( 'From' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_stdate',
					'label'			=> esc_html__( 'Start Date' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_edate',
					'label'			=> esc_html__( 'End Date' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_allday',
					'label'			=> esc_html__( 'All day' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_previous',
					'label'			=> esc_html__( 'Previous' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_previousev',
					'label'			=> esc_html__( 'Previous Event' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_next',
					'label'			=> esc_html__( 'Next' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_nextev',
					'label'			=> esc_html__( 'Next Event' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_search',
					'label'			=> esc_html__( 'Search' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_all',
					'label'			=> esc_html__( 'All' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_viewas',
					'label'			=> esc_html__( 'View as' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_month',
					'label'			=> esc_html__( 'Month' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_week',
					'label'			=> esc_html__( 'Week' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_day',
					'label'			=> esc_html__( 'Day' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_map',
					'label'			=> esc_html__( 'Map' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_list',
					'label'			=> esc_html__( 'List' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_select',
					'label'			=> esc_html__( 'Select' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_unl_tic',
					'label'			=> esc_html__( 'Unlimited tickets' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_unl_pie',
					'label'			=> esc_html__( 'Unlimited pieces' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_qty_av',
					'label'			=> esc_html__( 'Qty Available' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_pie_av',
					'label'			=> esc_html__( 'Pieces Available' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_no_tk',
					'label'			=> esc_html__( 'There are no ticket available at this time.' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_no_pie',
					'label'			=> esc_html__( 'There are no pieces available at this time.' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_no_resu',
					'label'			=> esc_html__( 'Nothing matched your search terms. Please try again with some different keywords.' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_no_evf',
					'label'			=> esc_html__( 'No Events Found' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_hours',
					'label'			=> esc_html__( 'Hours' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_min',
					'label'			=> esc_html__( 'min' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_sec',
					'label'			=> esc_html__( 'sec' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_loadm',
					'label'			=> esc_html__( 'Load more' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_name',
					'label'			=> esc_html__( 'Name' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_price',
					'label'			=> esc_html__( 'Price' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_status',
					'label'			=> esc_html__( 'Status' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_loca',
					'label'			=> esc_html__( 'Location' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_date',
					'label'			=> esc_html__( 'Date' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_buytk',
					'label'			=> esc_html__( 'BUY TICKET -' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_start',
					'label'			=> esc_html__( 'Start' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_end',
					'label'			=> esc_html__( 'End' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_addres',
					'label'			=> esc_html__( 'Address' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_vmap',
					'label'			=> esc_html__( 'View Map' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_phone',
					'label'			=> esc_html__( 'Phone' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_email',
					'label'			=> esc_html__( 'Email' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_web',
					'label'			=> esc_html__( 'Website' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_hassold',
					'label'			=> esc_html__( 'Has sold' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_schedule',
					'label'			=> esc_html__( 'Schedule' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_webd_view',
					'label'			=> esc_html__( 'Speaker' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evname',
					'label'			=> esc_html__( 'Event Name' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evdate',
					'label'			=> esc_html__( 'Event Date' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evlocati',
					'label'			=> esc_html__( 'Event Location' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				// 2.2
				array(
					'id' 			=> 'webd_text_evcat',
					'label'			=> esc_html__( 'Category' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evtag',
					'label'			=> esc_html__( 'Tags' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evyears',
					'label'			=> esc_html__( 'Years' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_evfilter',
					'label'			=> esc_html__( 'Filter' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_usersg',
					'label'			=> esc_html__( 'You already signed up this event' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_webd_view_of',
					'label'			=> esc_html__( 'Speaker of Events' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_protect_ct',
					'label'			=> esc_html__( 'Please login to see' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_protect_ct',
					'label'			=> esc_html__( 'Please login to see' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_sl_op',
					'label'			=> esc_html__( 'Select options' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_spon',
					'label'			=> esc_html__( 'Sponsors' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_stopb',
					'label'			=> esc_html__( 'Tickets not available' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_pending',
					'label'			=> esc_html__( '[pending]' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_trash',
					'label'			=> esc_html__( '[trash]' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				
				
				array(
					'id' 			=> 'webd_text_upc',
					'label'			=> esc_html__( 'Upcoming Events' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_defa',
					'label'			=> esc_html__( 'Default' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_ong',
					'label'			=> esc_html__( 'Ongoing Events' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_pas',
					'label'			=> esc_html__( 'Past Events' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				
				array(
					'id' 			=> 'webd_text_name_',
					'label'			=> esc_html__( 'Name:' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				
				array(
					'id' 			=> 'webd_text_fname_',
					'label'			=> esc_html__( 'First Name:' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_lname_',
					'label'			=> esc_html__( 'Last Name:' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				
				array(
					'id' 			=> 'webd_text_email_',
					'label'			=> esc_html__( 'Email:' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_attende_',
					'label'			=> esc_html__( 'Attendees info' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_timezone_',
					'label'			=> esc_html__( 'Timezone' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_sunrise',
					'label'			=> esc_html__( 'Sunrise' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_sunset',
					'label'			=> esc_html__( 'Sunset' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_yal',
					'label'			=> esc_html__( 'List year' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),
				array(
					'id' 			=> 'webd_text_total',
					'label'			=> esc_html__( 'Total' , 'WEBDWooEVENT' ),
					'description'	=> esc_html__( 'Add your text to replace this static text', 'WEBDWooEVENT' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'WEBDWooEVENT' )
				),				
			)
		);
		$settings = apply_filters( 'webdwooevents_fields', $settings );
		return $settings;
	}
	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {
				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'webdwooevents' );
				foreach( $data['fields'] as $field ) {
					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}
					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'webdwooevents', $option_name, $validation );
					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'webdwooevents', $section, array( 'field' => $field ) );
				}
			}
		}
	}
	public function settings_section( $section ) {
		$html = '<p class="'.$section['id'].'"> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}
	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {
		$field = $args['field'];
		$html = '';
		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );
		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}
		switch( $field['type'] ) {
			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;
			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;
			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;
			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;
			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;
			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;
			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;
			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;
			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . esc_html__( 'Upload an image' , 'WEBDWooEVENT' ) . '" data-uploader_button_text="' . esc_html__( 'Use image' , 'WEBDWooEVENT' ) . '" class="image_upload_button button" value="'. esc_html__( 'Upload new image' , 'WEBDWooEVENT' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. esc_html__( 'Remove image' , 'WEBDWooEVENT' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;
			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;
		}
		switch( $field['type'] ) {
			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;
			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}
		echo $html;
	}
	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}
	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {
		// Build page HTML
		$html = '<div class="wrap" id="webdwooevents">' . "\n";
			$html .= '<h2>' . esc_html__( 'Settings' , 'WEBDWooEVENT' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					//$html .= '<li><a class="tab all current" href="#standard">' . esc_html__( 'All' , 'WEBDWooEVENT' ) . '</a></li>' . "\n";
					foreach( $this->settings as $section => $data ) {
						$html .= '<li><a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}
				$html .= '</ul>' . "\n";
				$html .= '<div class="clear"></div>' . "\n";
				// Get settings fields
				ob_start();
				settings_fields( 'webdwooevents' );
				do_settings_sections( 'webdwooevents' );
				$html .= ob_get_clean();
				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( esc_html__( 'Save Settings' , 'WEBDWooEVENT' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";
		echo $html;
	}
}