<?php
function parwebd_webd_timeline_func($atts, $content){
	if(is_admin()){ return;}
	global $ID,$number_excerpt;
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$posttype =  isset($atts['posttype']) ? $atts['posttype'] :'product';
	$ids =  isset($atts['ids']) ? $atts['ids'] :'';
	$ex_ids 		= isset($atts['ex_ids']) ? $atts['ex_ids'] : '';
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$order =  isset($atts['order']) ? $atts['order'] :'';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$cat =  isset($atts['cat']) ? $atts['cat'] :'';
	$tag =  isset($atts['tag']) ? $atts['tag'] :'';
	
	$taxonomy =  isset($atts['taxonomy']) ? $atts['taxonomy'] :'';
	$terms =  isset($atts['terms']) ? $atts['terms'] :'';
	$webd_view_id =  isset($atts['webd_view_id']) ? $atts['webd_view_id'] :'';
	
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '20';
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	if($posts_per_page =="" || $posts_per_page > $count){$posts_per_page = $count; $paged ='';}
	$featured = isset($atts['featured']) ? $atts['featured'] : '';
	$args = woo_event_query($posttype, $posts_per_page, $order, $orderby, $meta_key, $cat, $tag, $ids,$paged,$webd_view_id,$spe_day=false, $featured,'',$taxonomy,$terms,$ex_ids);
	ob_start();
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()){?>
		<div class="webd-timeline-shortcode" id="timeline-<?php echo $ID;?>">
        	<ul>
			<?php 
            while($the_query->have_posts()){ $the_query->the_post();
				wooevent_template_plugin('timeline', true);
            }?>
            </ul>
        </div>
		<?php
	}else{
		$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
		echo '<p>'.$noftrsl.'</p>';
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_timeline', 'parwebd_webd_timeline_func' );
add_action( 'after_setup_theme', 'webd_reg_timeline_vc' );
function webd_reg_timeline_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Timeline", "WEBDWooEVENT"),
	   "base" => "webd_timeline",
	   "class" => "",
	   "icon" => "",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
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
			  "heading" => esc_html__("Exclude IDs", "WEBDWooEVENT"),
			  "param_name" => "ex_ids",
			  "value" => "",
			  "description" => esc_html__("Exclude List post IDs to retrieve, separated by a comma", "WEBDWooEVENT"),
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
			"type" => "textfield",
			"heading" => esc_html__("Category", "WEBDWooEVENT"),
			"param_name" => "cat",
			"value" => "",
			"description" => esc_html__("List of cat ID (or slug), separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Tags", "WEBDWooEVENT"),
			"param_name" => "tag",
			"value" => "",
			"description" => esc_html__("List of tags, separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Custom Taxonomy", "WEBDWooEVENT"),
			"param_name" => "taxonomy",
			"value" => "",
			"description" => esc_html__("Enter name of Taxonomy", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Terms", "WEBDWooEVENT"),
			"param_name" => "terms",
			"value" => "",
			"description" => esc_html__("List of Terms ID (or slug) of custom taxonomy, separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Speaker id", "WEBDWooEVENT"),
			"param_name" => "webd_view_id",
			"value" => "",
			"description" => esc_html__("Enter webd_view id", "WEBDWooEVENT"),
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
				esc_html__('Upcoming', 'WEBDWooEVENT') => 'upcoming',
				esc_html__('Past', 'WEBDWooEVENT') => 'past',
				esc_html__('Day', 'WEBDWooEVENT') => 'day',
				esc_html__('Week', 'WEBDWooEVENT') => 'week',
				esc_html__('Month', 'WEBDWooEVENT') => 'month',
				esc_html__('Year', 'WEBDWooEVENT') => 'year',
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
			"heading" => esc_html__("Number of Excerpt", "WEBDWooEVENT"),
			"param_name" => "number_excerpt",
			"value" => "",
			"description" => esc_html__("Enter number", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show only Featured event", 'WEBDWooEVENT'),
			 "param_name" => "featured",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
		  ),
	   )
	));
	}
}