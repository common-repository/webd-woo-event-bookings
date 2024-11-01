<?php
function parwebd_webd_countdown_func($atts, $content){
	if(is_admin()){ return;}
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$single = isset($atts['single']) ? $atts['single'] : '0';
	$style = isset($atts['style']) ? $atts['style'] : '';
	$posttype = 'product';
	$cat 		=isset($atts['cat']) ? $atts['cat'] : '';
	$tag 	= isset($atts['tag']) ? $atts['tag'] : '';
	$ids 		= isset($atts['ids']) ? $atts['ids'] : '';
	$ex_ids 		= isset($atts['ex_ids']) ? $atts['ex_ids'] : '';
	$count 		= isset($atts['count']) ? $atts['count'] : '6';
	$order 	= isset($atts['order']) ? $atts['order'] : '';
	$orderby 	= isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value 	= isset($atts['meta_value']) ? $atts['meta_value'] : '';
	
	$show_title = isset($atts['show_title']) ? $atts['show_title'] : true;
	$featured = isset($atts['featured']) ? $atts['featured'] : '';
	
	$taxonomy =  isset($atts['taxonomy']) ? $atts['taxonomy'] :'';
	$terms =  isset($atts['terms']) ? $atts['terms'] :'';
	$webd_view_id =  isset($atts['webd_view_id']) ? $atts['webd_view_id'] :'';

	
	$args = woo_event_query($posttype, $count, $order, $orderby, $meta_key, $cat, $tag, $ids, $page=false, $webd_view_id,$spe_day=false, $featured,$meta_value,$taxonomy,$terms,$ex_ids);
	ob_start();
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()){
		wp_enqueue_script( 'moment', WEBD_EVENT_BOOKINGS.'js/fullcalendar/lib/moment.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'webd-jquery.countdown', WEBD_EVENT_BOOKINGS.'js/jquery.countdown/jquery.countdown.js', array( 'jquery' ) );
		?>
    	<div class="webd-countdonw <?php if($single!='1'){?>list-countdown<?php } echo ' style-'.$style;?>">
        	<input type="hidden"  name="cd-days" value="<?php echo get_option('webd_text_day')!='' ? get_option('webd_text_day') : esc_html__('days','WEBDWooEVENT');?>">
            <input type="hidden"  name="cd-hr" value="<?php echo get_option('webd_text_hours')!='' ? get_option('webd_text_hours') : esc_html__('hours','WEBDWooEVENT');?>">
            <input type="hidden"  name="cd-min" value="<?php echo get_option('webd_text_min')!='' ? get_option('webd_text_min') : esc_html__('min','WEBDWooEVENT');?>">
            <input type="hidden"  name="cd-sec" value="<?php echo get_option('webd_text_sec')!='' ? get_option('webd_text_sec') : esc_html__('sec','WEBDWooEVENT');?>">
        	<div class="row">
				<?php while($the_query->have_posts()){ $the_query->the_post(); 
					if(get_post_meta( get_the_ID(), 'webd_startdate', true )!=''){
						$webd_eventcolor ='';
						$webd_eventcolor = webd_event_custom_color(get_the_ID());
						if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
						$bg_style ='';
						$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
						if($style=='modern'){
							$bg_style = 'style="background-image:url('.esc_url($image_src[0]).');"';
						}
						?>
						<div class="col-md-12">
						<div class="webd-evcount" <?php echo $bg_style;?>>
							<?php
							if($style=='modern'){
								echo '<div class="bg-gra">';
							}
							if($show_title!=false){?>
							<span class="cd-title">
                            	<a href="<?php the_permalink();?>"><?php echo get_the_title();?></a>
                                <?php webd_subtitle_html();?>
                            </span>
							<?php }
							if($webd_eventcolor!="" && $style!='modern'){?>
								<style type="text/css" scoped>
									.webd-countdonw.list-countdown #countdown-<?php echo get_the_ID();?> .cd-number { background-color:<?php echo $webd_eventcolor; ?>}
								</style>
								<?php
							}
							$webd_time_zone = get_post_meta(get_the_ID(),'webd_time_zone',true);
							?>
							<span class="webd-coundown-item" id="countdown-<?php echo get_the_ID();?>" data-date="<?php echo date_i18n('Y-m-d H:i', get_post_meta( get_the_ID(), 'webd_startdate', true ))?>" data-timezone="<?php echo esc_attr($webd_time_zone);?>"></span>
							<?php 
							if($style=='modern'){
								echo '</div>';
							}
							?>
						</div>
						</div>
                	<?php }
				}?>
            </div>
        </div>
        <?php
	}elseif($single!='1'){
		$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
		echo '<div class="alert alert-success">'.$noftrsl.'</div>';
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_countdown', 'parwebd_webd_countdown_func' );
add_action( 'after_setup_theme', 'webd_reg_countdown_vc' );
function webd_reg_countdown_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - CountDown", "WEBDWooEVENT"),
	   "base" => "webd_countdown",
	   "class" => "",
	   "icon" => "icon-countdown",
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
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'WEBDWooEVENT'),
			 "param_name" => "style",
			 "value" => array(
			 	esc_html__('Classic', 'WEBDWooEVENT') => 'classic',
				esc_html__('Modern', 'WEBDWooEVENT') => 'modern',
			 ),
			 "description" => ''
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
				esc_html__('Comment count', 'WEBDWooEVENT') => 'comment_count',
				esc_html__('Menu order', 'WEBDWooEVENT') => 'menu_order',
				esc_html__('Meta value', 'WEBDWooEVENT') => 'meta_value',
				esc_html__('Meta value num', 'WEBDWooEVENT') => 'meta_value_num',
				esc_html__('Post__in', 'WEBDWooEVENT') => 'post__in',
				esc_html__('Upcoming', 'WEBDWooEVENT') => 'upcoming',
				esc_html__('Ongoing & Upcoming', 'WEBDWooEVENT') => 'ontoup',
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
			"heading" => esc_html__("Meta Value", "WEBDWooEVENT"),
			"param_name" => "meta_value",
			"value" => "",
			"description" => esc_html__("Enter meta value to query", "WEBDWooEVENT"),
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