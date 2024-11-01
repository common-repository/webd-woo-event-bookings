<?php
class WEBD_WooEvent_Speaker {
	public function __construct()
    {
        add_action( 'init', array( &$this, 'ex_register_post_type' ) );
		add_filter( 'exc_mb_meta_boxes', array($this,'webd_view_metadata') );
    }

	function ex_register_post_type(){
		$labels = array(
			'name'               => esc_html__('Speaker','WEBDWooEVENT'),
			'singular_name'      => esc_html__('Speaker','WEBDWooEVENT'),
			'add_new'            => esc_html__('Add New Speaker','WEBDWooEVENT'),
			'add_new_item'       => esc_html__('Add New Speaker','WEBDWooEVENT'),
			'edit_item'          => esc_html__('Edit Speaker','WEBDWooEVENT'),
			'new_item'           => esc_html__('New Speaker','WEBDWooEVENT'),
			'all_items'          => esc_html__('All Speakers','WEBDWooEVENT'),
			'view_item'          => esc_html__('View Speaker','WEBDWooEVENT'),
			'search_items'       => esc_html__('Search Speaker','WEBDWooEVENT'),
			'not_found'          => esc_html__('No Speaker found','WEBDWooEVENT'),
			'not_found_in_trash' => esc_html__('No Speaker found in Trash','WEBDWooEVENT'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Speaker','WEBDWooEVENT')
		);
		$webd_webd_view_slug = get_option('webd_webd_view_slug');
		if($webd_webd_view_slug==''){
			$webd_webd_view_slug = 'webd_view';
		}
		$rewrite = array( 'slug' => untrailingslashit( $webd_webd_view_slug ), 'with_front' => false, 'feeds' => true );
		$args = array(  
			'labels' => $labels,  
			'menu_position' => 8, 
			'supports' => array('title','editor','thumbnail', 'excerpt','custom-fields'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon' =>  'dashicons-groups',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'rewrite' => $rewrite,
		);  
		register_post_type('event-webd-view',$args);  
	}
	function webd_view_metadata(array $meta_boxes){
		$group_fields = array(
			array( 'id' => 'webd_custom_title',  'name' => esc_html__('Title', 'WEBDWooEVENT'), 'type' => 'text' ),
			array( 'id' => 'webd_custom_content', 'name' => esc_html__('Content', 'WEBDWooEVENT'), 'type' => 'text', 'desc' => '', 'repeatable' => false),
		);
		foreach ( $group_fields as &$field ) {
			$field['id'] = str_replace( 'field', 'gfield', $field['id'] );
		}
	
		$meta_boxes[] = array(
			'title' => esc_html__('Custom Field', 'WEBDWooEVENT'),
			'pages' => 'event-webd-view',
			'fields' => array(
				array(
					'id' => 'webd_custom_metadata',
					'name' => esc_html__('Custom Metadata', 'WEBDWooEVENT'),
					'type' => 'group',
					'repeatable' => true,
					'sortable' => true,
					'fields' => $group_fields,
					'desc' => esc_html__('Custom metadata for this post', 'WEBDWooEVENT')
				)
			),
			'priority' => 'high'
		);	
		$webd_view_settings = array(	
				array( 'id' => 'webd_view_position', 'name' => esc_html__('Position:', 'WEBDWooEVENT'), 'type' => 'text','desc' => esc_html__('Position of webd_view', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'facebook', 'name' => esc_html__('Facebook:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'instagram', 'name' => esc_html__('Instagram:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'envelope', 'name' => esc_html__('Email:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter email contact of webd_view', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'twitter', 'name' => esc_html__('Twitter:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'linkedin', 'name' => esc_html__('LinkedIn:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false),	
				array( 'id' => 'tumblr', 'name' => esc_html__('Tumblr:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'pinterest', 'name' => esc_html__('Pinterest:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),	
				array( 'id' => 'youtube', 'name' => esc_html__('YouTube:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'flickr', 'name' => esc_html__('Flickr:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),	
				array( 'id' => 'github', 'name' => esc_html__('GitHub:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'dribbble', 'name' => esc_html__('Dribbble:', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Enter link to webd_view profile page', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
			);

		$meta_boxes[] = array(
			'title' => __('Speaker Info','WEBDWooEVENT'),
			'pages' => 'event-webd-view',
			'fields' => $webd_view_settings,
			'priority' => 'high'
		);
		return $meta_boxes;
	}
	
}
$WEBD_WooEvent_Speaker = new WEBD_WooEvent_Speaker();