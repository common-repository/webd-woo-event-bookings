<?php
function parwebd_webd_venue_func($atts, $content){
	if(is_admin()){ return;}
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$ids =  isset($atts['ids']) ? $atts['ids'] :'';
	$layout =  isset($atts['layout']) ? $atts['layout'] :'';
	$count =  isset($atts['count']) ? $atts['count'] :'3';
	$columns =  isset($atts['columns']) && $atts['columns']!='' ? $atts['columns'] :'1';
	$order =  isset($atts['order']) ? $atts['order'] :'';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value 	= isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$style =  isset($atts['style']) ? $atts['style'] :'';
	$autoplay =  isset($atts['autoplay']) ? $atts['autoplay'] :'';
	$autoplayspeed =  isset($atts['autoplayspeed']) ? $atts['autoplayspeed'] :'';
	$args = array(
		'post_type' => 'webd_venue',
		'posts_per_page' => $count,
		'post_status' => 'publish',
		'post__in' =>  $ids,
		'order' => $order,
		'orderby' => $orderby,
		'meta_key' => $meta_key,
		'ignore_sticky_posts' => 1,
	);
	ob_start();
	$the_query = new WP_Query( $args );
	$class = 'webd-column-'.esc_attr($columns).' venue-style-'.esc_attr($style);
	if($layout=='carousel'){
		$class = 'webd-carousel venue-style-'.esc_attr($style);
	}
	if($the_query->have_posts()){?>
		<div class="webd-venues-sc <?php echo $class;?>" id="venue-<?php echo $ID;?>">
        	<div <?php if($layout=='carousel'){?> class=" wenv-car is-carousel" id="post-corousel-<?php echo $ID; ?>" data-items="<?php echo esc_attr($columns); ?>" <?php if($autoplay=='on'){?> data-autoplay=1 <?php }?> data-autospeed="<?php echo esc_attr($autoplayspeed);?>" data-navigation=1 data-pagination=1 <?php }?>>
			<?php
            while($the_query->have_posts()){ $the_query->the_post();
                wooevent_template_plugin('venue', true);
            }?>
            </div>
		</div>
		<?php
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_venues', 'parwebd_webd_venue_func' );
add_action( 'after_setup_theme', 'webd_venue_reg_vc' );
function webd_venue_reg_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Venues", "WEBDWooEVENT"),
	   "base" => "webd_venues",
	   "class" => "",
	   "icon" => "icon-table",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
	   	  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Layout", 'WEBDWooEVENT'),
			 "param_name" => "layout",
			 "value" => array(
			 	esc_html__('Grid', 'WEBDWooEVENT') => '',
				esc_html__('Carousel', 'WEBDWooEVENT') => 'carousel',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'WEBDWooEVENT'),
			 "param_name" => "style",
			 "value" => array(
				esc_html__('Style 1', 'WEBDWooEVENT') => '1',
				esc_html__('Style 2', 'WEBDWooEVENT') => '2',
				esc_html__('Style 3', 'WEBDWooEVENT') => '3',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Columns", 'WEBDWooEVENT'),
			 "param_name" => "columns",
			 "value" => array(
			 	esc_html__('', 'WEBDWooEVENT') => '',
				esc_html__('1 columns', 'WEBDWooEVENT') => '1',
				esc_html__('2 columns', 'WEBDWooEVENT') => '2',
				esc_html__('3 columns', 'WEBDWooEVENT') => '3',
				esc_html__('4 columns', 'WEBDWooEVENT') => '4',
				esc_html__('5 columns', 'WEBDWooEVENT') => '5',
			 ),
			 "description" => ''
		  ),	
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("IDs", "WEBDWooEVENT"),
			"param_name" => "ids",
			"value" => "",
			"description" => esc_html__("Specify post IDs to retrieve", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Count", "WEBDWooEVENT"),
			"param_name" => "count",
			"value" => "",
			"description" => esc_html__("Number of posts", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order", 'WEBDWooEVENT'),
			 "param_name" => "order",
			 "value" => array(
			 	esc_html__('DESC', 'WEBDWooEVENT') => 'DESC',
				esc_html__('ASC', 'WEBDWooEVENT') => 'ASC',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order by", 'WEBDWooEVENT'),
			 "param_name" => "orderby",
			 "value" => array(
			 	esc_html__('Date', 'WEBDWooEVENT') => 'date',
				esc_html__('ID', 'WEBDWooEVENT') => 'ID',
				esc_html__('Author', 'WEBDWooEVENT') => 'author',
			 	esc_html__('Title', 'WEBDWooEVENT') => 'title',
				esc_html__('Name', 'WEBDWooEVENT') => 'name',
				esc_html__('Modified', 'WEBDWooEVENT') => 'modified',
			 	esc_html__('Parent', 'WEBDWooEVENT') => 'parent',
				esc_html__('Random', 'WEBDWooEVENT') => 'rand',
				esc_html__('Menu order', 'WEBDWooEVENT') => 'menu_order',
				esc_html__('Meta value', 'WEBDWooEVENT') => 'meta_value',
				esc_html__('Meta value num', 'WEBDWooEVENT') => 'meta_value_num',
				esc_html__('Post__in', 'WEBDWooEVENT') => 'post__in',
				esc_html__('None', 'WEBDWooEVENT') => 'none',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta key", "WEBDWooEVENT"),
			"param_name" => "meta_key",
			"value" => "",
			"description" => esc_html__("Enter meta key to query", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta Value", "WEBDWooEVENT"),
			"param_name" => "meta_value",
			"value" => "",
			"description" => esc_html__("Enter meta value to query", "WEBDWooEVENT"),
		  ),
	   )
	));
	}
}