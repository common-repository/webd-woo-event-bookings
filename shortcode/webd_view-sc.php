<?php
function parwebd_webd_webd_view_func($atts, $content){
	if(is_admin()){ return;}
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$posttype ='event-webd-view';
	$ids =  isset($atts['ids']) ? $atts['ids'] :'';
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'3';
	$order =  isset($atts['order']) ? $atts['order'] :'';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$cat =  isset($atts['cat']) ? $atts['cat'] :'';
	$tag =  isset($atts['tag']) ? $atts['tag'] :'';
	
	$autoplay =  isset($atts['autoplay']) ? $atts['autoplay'] :'';
	global $img_size,$show_meta;
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'wethumb_460x307';
	$show_meta =  isset($atts['show_meta']) ? $atts['show_meta'] :'';
	$grid_autoplay ='off' ;
	$args = woo_event_query($posttype, $count, $order, $orderby, $meta_key, $cat, $tag, $ids,'');
	ob_start();
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()){?>
		<div class="webd-carousel webd-spekers-sc webd-grid-shortcode webd-grid-column-1" id="grid-<?php echo $ID;?>">
        	<div class="grid-container">
                <div class="is-carousel" id="post-corousel-<?php echo $ID; ?>" data-items="<?php echo esc_attr($posts_per_page); ?>" <?php if($autoplay=='on'){?> data-autoplay=1 <?php }?> data-navigation=1 data-pagination=1>
                    <?php 
                    $i=0;
                    $it = $the_query->found_posts;
                    if($it < $count || $count=='-1'){ $count = $it;}
                    if($count  > $posts_per_page){
                        $num_pg = ceil($count/$posts_per_page);
                        $it_ep  = $count%$posts_per_page;
                    }else{
                        $num_pg = 1;
                    }
                    while($the_query->have_posts()){ $the_query->the_post();
                        ?>
                        <div class="grid-row">
                        <?php
                        wooevent_template_plugin('webd_viewsc', true);
                        ?>
                        </div>
                        <?php
                    }?>            
                </div>
            </div>
            <div class="clear"></div>
        </div>
		<?php
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_webd_views', 'parwebd_webd_webd_view_func' );
add_action( 'after_setup_theme', 'webd_reg_webd_webd_views_vc' );
function webd_reg_webd_webd_views_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Speakers Carousel", "WEBDWooEVENT"),
	   "base" => "webd_webd_views",
	   "class" => "",
	   "icon" => "icon-webd_view",
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
			"heading" => esc_html__("Count", "WEBDWooEVENT"),
			"param_name" => "count",
			"value" => "",
			"description" => esc_html__("Number of posts", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Posts per page", "WEBDWooEVENT"),
			"param_name" => "posts_per_page",
			"value" => "",
			"description" => esc_html__("Number items per page", 'WEBDWooEVENT'),
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
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Autoplay", 'WEBDWooEVENT'),
			 "param_name" => "autoplay",
			 "value" => array(
				esc_html__('Off', 'WEBDWooEVENT') => 'off',
				esc_html__('On', 'WEBDWooEVENT') => 'on',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show metadata", 'WEBDWooEVENT'),
			 "param_name" => "show_meta",
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