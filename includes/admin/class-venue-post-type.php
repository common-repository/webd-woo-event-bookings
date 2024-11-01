<?php
class WEBD_WEBD_WooEvent_Venue {
	public function __construct()
    {
        add_action( 'init', array( &$this, 'register_post_type' ) );
		add_filter( 'exc_mb_meta_boxes', array($this,'venue_metadata') );
    }

	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Venue','WEBDWooEVENT'),
			'singular_name'      => esc_html__('Venue','WEBDWooEVENT'),
			'add_new'            => esc_html__('Add New Venue','WEBDWooEVENT'),
			'add_new_item'       => esc_html__('Add New Venue','WEBDWooEVENT'),
			'edit_item'          => esc_html__('Edit Venue','WEBDWooEVENT'),
			'new_item'           => esc_html__('New Venue','WEBDWooEVENT'),
			'all_items'          => esc_html__('Venues','WEBDWooEVENT'),
			'view_item'          => esc_html__('View Venue','WEBDWooEVENT'),
			'search_items'       => esc_html__('Search Venue','WEBDWooEVENT'),
			'not_found'          => esc_html__('No Venue found','WEBDWooEVENT'),
			'not_found_in_trash' => esc_html__('No Venue found in Trash','WEBDWooEVENT'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Venues','WEBDWooEVENT')
		);
		
		$webd_venue_slug = get_option('webd_venue_slug');
		if($webd_venue_slug==''){
			$webd_venue_slug = 'venue';
		}
		$rewrite =  array( 'slug' => untrailingslashit( $webd_venue_slug ), 'with_front' => false, 'feeds' => true );
		$args = array(  
			'labels' => $labels,  
			'supports' => array('title','thumbnail','custom-fields','editor'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'menu_icon' =>  'dashicons-groups',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'rewrite' => $rewrite,
		);  
		register_post_type('webd_venue',$args);  
	}
	function venue_metadata(array $meta_boxes){
		// register meta
		$venue_settings = array(	
				array( 'id' => 'webd_adress', 'name' => esc_html__('Address', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Location Address of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_latitude_longitude', 'name' => esc_html__('Latitude and Longitude (optional)', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Physical address of your event location, if Event map shortcode cannot load your address, you need to fill Latitude and Longitude to fix it. Ex for London: 42.9869502,-81.243177', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'webd_phone', 'name' => esc_html__('Phone', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Contact Number of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_email', 'name' => esc_html__('Email', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Email Contact of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_website', 'name' => esc_html__('Website', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Website URL of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
		);

		$meta_boxes[] = array(
			'title' => __('Venue Info','WEBDWooEVENT'),
			'pages' => 'webd_venue',
			'fields' => $venue_settings,
			'priority' => 'high'
		);
		return $meta_boxes;
	}
	
}
$WEBD_WEBD_WooEvent_Venue = new WEBD_WEBD_WooEvent_Venue();
//Register bulk update button
function webd_bulk_update_venue_box() {
    add_meta_box( 'webd-bulk-update-venue', esc_html__( 'Bulk update venue info', 'WEBDWooEVENT' ), 'webd_bulk_update_venue_button', 'webd_venue', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'webd_bulk_update_venue_box',99);
 
//Add field
function webd_bulk_update_venue_button( $meta_id ) {
 
    $outline = '<label for="bulk-venue" class="exc_mb_metabox_description">'. esc_html__('This feature allow you can update venue info to all events contain this venue in one click', 'WEBDWooEVENT') .'</label>';
    $outline .= '<input type="button" name="bulk-update-venue" id="bulk-update-venue" class="button" value="'. esc_html__('Update','WEBDWooEVENT') .'" data-id="'.sanitize_text_field($_GET['post']).'" style="margin-top: 10px;"/>';
 
    echo $outline;
}
// ajax update venue
add_action( 'wp_ajax_webd_update_events_venue', 'webd_update_events_venue' );
add_action( 'wp_ajax_nopriv_webd_update_events_venue', 'webd_update_events_venue' );
function webd_update_events_venue(){
	if(!isset($_POST['id']) || !is_numeric($_POST['id'])){ echo 3; die;}
	$id = sanitize_text_field($_POST['id']);
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => 999,
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
	);
	$args ['meta_query'] = array(
		 array(
			'key'     => 'webd_default_venue',
			'value'   => $id,
			'compare' => '=',
		 ),
	);
	$webd_adress = get_post_meta( $id, 'webd_adress', true ) ;
	$webd_latitude_longitude = get_post_meta( $id, 'webd_latitude_longitude', true ) ;
	$webd_phone = get_post_meta( $id, 'webd_phone', true ) ;
	$webd_email = get_post_meta( $id, 'webd_email', true ) ;
	$webd_website = get_post_meta( $id, 'webd_website', true ) ;
	$this_posts = get_posts( $args );
	$found = 0;
	foreach ( $this_posts as $post ) {
		$found  = 1;
		if($webd_adress!=''){
			update_post_meta( $post->ID, 'webd_adress', $webd_adress);
		}
		if($webd_latitude_longitude!=''){
		update_post_meta( $post->ID, 'webd_latitude_longitude', $webd_latitude_longitude);
		}
		if($webd_phone!=''){
			update_post_meta( $post->ID, 'webd_phone', $webd_phone);
		}
		if($webd_email!=''){
			update_post_meta( $post->ID, 'webd_email', $webd_email);
		}
		if($webd_website!=''){
			update_post_meta( $post->ID, 'webd_website', $webd_website);
		}
	}
	echo $found;
	die;
}

// Add the textfield to the backend
if(!function_exists('webd_add_extra_price_field')){
    function webd_add_extra_price_field() {
    	woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-hidden',
	           'label'         => __('<h2>Set Day Wise Cost</h2> (All fields are optional.)'),
	           'type'          => 'hidden'
       		)
       );

    	//monday
        woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-monday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price monday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-monday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price monday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //tuesday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-tuesday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price tuesday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-tuesday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price tuesday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //wednesday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-wednesday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price wednes. : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-wednesday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price wednesday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //thursday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-thursday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price thursday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-thursday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price thursday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //friday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-friday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price friday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-friday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price friday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //saturday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-saturday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price saturday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-saturday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price saturday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );

        //sunday
         woocommerce_wp_text_input(
        	array(
	           'id'            => 'cost-price-regular-sunday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Regular price sunday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
        );
        woocommerce_wp_text_input(
       		array(
	           'id'            => 'cost-price-sale-sunday',
	           'class'         => 'short wc_input_price cost-price',
	           'label'         => __('Sale price sunday : ('.get_woocommerce_currency_symbol().')', 'yourtextdomain'),
	           'placeholder'   => 'optional',
	           'type'          => 'number'
       		)
       );


   }
}
add_action('woocommerce_product_options_pricing','webd_add_extra_price_field');

// Enable to save the extra field
function webd_save_extra_price_field($post_id) {
    $product = wc_get_product($post_id);

    //regular
    if(isset($_POST['cost-price-regular-monday'])) {
    	$title1   = isset( $_POST['cost-price-regular-monday'] ) ? $_POST['cost-price-regular-monday'] : '';
    	$product->update_meta_data( 'cost-price-regular-monday', sanitize_text_field( $title1 ) );
	}
	if(isset($_POST['cost-price-regular-tuesday'])) {
    	$title2   = isset( $_POST['cost-price-regular-tuesday'] ) ? $_POST['cost-price-regular-tuesday'] : '';
    	$product->update_meta_data( 'cost-price-regular-tuesday', sanitize_text_field( $title2 ) );
	}
	if(isset($_POST['cost-price-regular-wednesday'])) {
    	$title3   = isset( $_POST['cost-price-regular-wednesday'] ) ? $_POST['cost-price-regular-wednesday'] : '';
    	$product->update_meta_data( 'cost-price-regular-wednesday', sanitize_text_field( $title3 ) );
	}
	if(isset($_POST['cost-price-regular-thursday'])) {
    	$title4   = isset( $_POST['cost-price-regular-thursday'] ) ? $_POST['cost-price-regular-thursday'] : '';
    	$product->update_meta_data( 'cost-price-regular-thursday', sanitize_text_field( $title4 ) );
	}
	if(isset($_POST['cost-price-regular-friday'])) {
    	$title5   = isset( $_POST['cost-price-regular-friday'] ) ? $_POST['cost-price-regular-friday'] : '';
    	$product->update_meta_data( 'cost-price-regular-friday', sanitize_text_field( $title5 ) );
	}
	if(isset($_POST['cost-price-regular-saturday'])) {
    	$title6   = isset( $_POST['cost-price-regular-saturday'] ) ? $_POST['cost-price-regular-saturday'] : '';
    	$product->update_meta_data( 'cost-price-regular-saturday', sanitize_text_field( $title6 ) );
	}
	if(isset($_POST['cost-price-regular-sunday'])) {
    	$title7   = isset( $_POST['cost-price-regular-sunday'] ) ? $_POST['cost-price-regular-sunday'] : '';
    	$product->update_meta_data( 'cost-price-regular-sunday', sanitize_text_field( $title7 ) );
	}  

    //sale
    if(isset($_POST['cost-price-sale-monday'])) {
  		$title8    = isset( $_POST['cost-price-sale-monday'] ) ? $_POST['cost-price-sale-monday'] : '';
  		$product->update_meta_data( 'cost-price-sale-monday', sanitize_text_field( $title8 ) );
  	}
  	if(isset($_POST['cost-price-sale-tuesday'])) {
    	$title9    = isset( $_POST['cost-price-sale-tuesday'] ) ? $_POST['cost-price-sale-tuesday'] : '';
    	$product->update_meta_data( 'cost-price-sale-tuesday', sanitize_text_field( $title9 ) );
	}
	if(isset($_POST['cost-price-sale-wednesday'])) {
    	$title10   = isset( $_POST['cost-price-sale-wednesday'] ) ? $_POST['cost-price-sale-wednesday'] : '';
    	$product->update_meta_data( 'cost-price-sale-wednesday', sanitize_text_field( $title10 ) );
	}
	if(isset($_POST['cost-price-sale-thursday'])) {
    	$title11   = isset( $_POST['cost-price-sale-thursday'] ) ? $_POST['cost-price-sale-thursday'] : '';
    	$product->update_meta_data( 'cost-price-sale-thursday', sanitize_text_field( $title11 ) );

	}
	if(isset($_POST['cost-price-sale-friday'])) {
    	$title12   = isset( $_POST['cost-price-sale-friday'] ) ? $_POST['cost-price-sale-friday'] : '';
    	$product->update_meta_data( 'cost-price-sale-friday', sanitize_text_field( $title12 ) );
	}
	if(isset($_POST['cost-price-sale-saturday'])) {
    	$title13   = isset( $_POST['cost-price-sale-saturday'] ) ? $_POST['cost-price-sale-saturday'] : '';
    	$product->update_meta_data( 'cost-price-sale-saturday', sanitize_text_field( $title13 ) );
	}
	if(isset($_POST['cost-price-sale-sunday'])) {
    	$title14   = isset( $_POST['cost-price-sale-sunday'] ) ? $_POST['cost-price-sale-sunday'] : 
    	$product->update_meta_data( 'cost-price-sale-sunday', sanitize_text_field( $title14 ) );
	}

	if(isset($_POST['cost-price-regular-monday']) || isset($_POST['cost-price-regular-tuesday']) || isset($_POST['cost-price-regular-wednesday']) || isset($_POST['cost-price-regular-thursday']) || isset($_POST['cost-price-regular-friday']) || isset($_POST['cost-price-regular-saturday']) || isset($_POST['cost-price-regular-sunday']) || isset($_POST['cost-price-sale-monday']) || isset($_POST['cost-price-sale-tuesday']) || isset($_POST['cost-price-sale-wednesday']) || isset($_POST['cost-price-sale-thursday']) || isset($_POST['cost-price-sale-friday']) || isset($_POST['cost-price-sale-saturday']) || isset($_POST['cost-price-sale-sunday'])) {
		//make product object
    	$product->save();
	}


    //Regular
    if(isset($_POST['cost-price-regular-monday'])) {
        if(is_numeric($_POST['cost-price-regular-monday'])){
            update_post_meta($product_id, 'cost-price-regular-monday', sanitize_text_field($_POST['cost-price-regular-monday']));
        }
    }
    if(isset($_POST['cost-price-regular-tuesday'])) {
        if(is_numeric($_POST['cost-price-regular-tuesday'])){
            update_post_meta($product_id, 'cost-price-regular-tuesday', sanitize_text_field($_POST['cost-price-regular-tuesday']));
        }
    }
    if(isset($_POST['cost-price-regular-wednesday'])) {
        if(is_numeric($_POST['cost-price-regular-wednesday'])){
            update_post_meta($product_id, 'cost-price-regular-wednesday', sanitize_text_field($_POST['cost-price-regular-wednesday']));
        }
    }
    if(isset($_POST['cost-price-regular-thursday'])) {
        if(is_numeric($_POST['cost-price-regular-thursday'])){
            update_post_meta($product_id, 'cost-price-regular-thursday', sanitize_text_field($_POST['cost-price-regular-thursday']));
        }
    }
    if(isset($_POST['cost-price-regular-friday'])) {
        if(is_numeric($_POST['cost-price-regular-friday'])){
            update_post_meta($product_id, 'cost-price-regular-friday', sanitize_text_field($_POST['cost-price-regular-friday']));
        }
    }
    if(isset($_POST['cost-price-regular-saturday'])) {
        if(is_numeric($_POST['cost-price-regular-saturday'])){
            update_post_meta($product_id, 'cost-price-regular-saturday', sanitize_text_field($_POST['cost-price-regular-saturday']));
        }
    }
    if(isset($_POST['cost-price-regular-sunday'])) {
        if(is_numeric($_POST['cost-price-regular-sunday'])){
            update_post_meta($product_id, 'cost-price-regular-sunday', sanitize_text_field($_POST['cost-price-regular-sunday']));
        }
    }

    //Sale
     if(isset($_POST['cost-price-sale-monday'])) {
        if(is_numeric($_POST['cost-price-sale-monday'])){
            update_post_meta($product_id, 'cost-price-sale-monday', sanitize_text_field($_POST['cost-price-sale-monday']));
        }
    }
    if(isset($_POST['cost-price-sale-tuesday'])) {
        if(is_numeric($_POST['cost-price-sale-tuesday'])){
            update_post_meta($product_id, 'cost-price-sale-tuesday', sanitize_text_field($_POST['cost-price-sale-tuesday']));
        }
    }
    if(isset($_POST['cost-price-sale-wednesday'])) {
        if(is_numeric($_POST['cost-price-sale-wednesday'])){
            update_post_meta($product_id, 'cost-price-sale-wednesday', sanitize_text_field($_POST['cost-price-sale-wednesday']));
        }
    }
    if(isset($_POST['cost-price-sale-thursday'])) {
        if(is_numeric($_POST['cost-price-sale-thursday'])){
            update_post_meta($product_id, 'cost-price-sale-thursday', sanitize_text_field($_POST['cost-price-sale-thursday']));
        }
    }
    if(isset($_POST['cost-price-sale-friday'])) {
        if(is_numeric($_POST['cost-price-sale-friday'])){
            update_post_meta($product_id, 'cost-price-sale-friday', sanitize_text_field($_POST['cost-price-sale-friday']));
        }
    }
    if(isset($_POST['cost-price-sale-saturday'])) {
        if(is_numeric($_POST['cost-price-sale-saturday'])){
            update_post_meta($product_id, 'cost-price-sale-saturday', sanitize_text_field($_POST['cost-price-sale-saturday']));
        }
    }
    if(isset($_POST['cost-price-sale-sunday'])) {
        if(is_numeric($_POST['cost-price-sale-sunday'])){
            update_post_meta($product_id, 'cost-price-sale-sunday', sanitize_text_field($_POST['cost-price-sale-sunday']));
        }
    }
}
add_action('save_post', 'webd_save_extra_price_field');

