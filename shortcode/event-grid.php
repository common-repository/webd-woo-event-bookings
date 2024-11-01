<?php
function parwebd_webd_grid_func($atts, $content){
	if(is_admin()){ return;}
	global $columns,$number_excerpt,$show_time,$orderby,$img_size,$atts_sc;
	$atts_sc = $atts;
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$posttype =  isset($atts['posttype']) ? $atts['posttype'] :'product';
	$ids =  isset($atts['ids']) ? $atts['ids'] :'';
	$ex_ids 		= isset($atts['ex_ids']) ? $atts['ex_ids'] : '';
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$order =  isset($atts['order']) ? $atts['order'] :'';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$cat =  isset($atts['cat']) ? $atts['cat'] :'';
	$tag =  isset($atts['tag']) ? $atts['tag'] :'';
	$style =  isset($atts['style']) ? $atts['style'] :'';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$columns =  isset($atts['columns']) && $atts['columns']!='' ? $atts['columns'] :'3';
	if(!isset($atts['columns'])){ $atts['columns'] = $columns;}
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'wethumb_460x307';
	$show_time =  isset($atts['show_time']) ? $atts['show_time'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value 	= isset($atts['meta_value']) ? $atts['meta_value'] : '';
	
	$taxonomy =  isset($atts['taxonomy']) ? $atts['taxonomy'] :'';
	$terms =  isset($atts['terms']) ? $atts['terms'] :'';
	$webd_view_id =  isset($atts['webd_view_id']) ? $atts['webd_view_id'] :'';
	
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	if($posts_per_page =="" || $posts_per_page > $count){$posts_per_page = $count; $paged ='';}
	$featured = isset($atts['featured']) ? $atts['featured'] : '';
	$args = woo_event_query($posttype, $posts_per_page, $order, $orderby, $meta_key, $cat, $tag, $ids,$paged,$webd_view_id,$spe_day=false, $featured,$meta_value,$taxonomy,$terms,$ex_ids);
	
	ob_start();
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()){?>
		<div class="webd-grid-shortcode <?php echo 'webd-grid-column-'.esc_attr($columns).' gr-'.$style; if($orderby=='has_submited'){ echo ' submit-list';}?>" id="grid-<?php echo $ID;?>">
        	<div class="ct-grid">
                <div class="grid-container">
                <div class="grid-row">
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
				$arr_ids = array();
                while($the_query->have_posts()){ $the_query->the_post();
					$arr_ids[] = get_the_ID();
                    if(function_exists('wp_pagenavi')) {
                        $the_query->max_num_pages = $num_pg;
                    }
                    $i++;
                    if(($num_pg == $paged) && $num_pg!='1'){
                        if($i > $it_ep){ break;}
                    }
                    if($style=='classic'){
                        wooevent_template_plugin('grid-classic', true);
                    }else{
                        wooevent_template_plugin('grid', true);
                    }
                    if($i%$columns==0){?>
                        </div>
                        <div class="grid-row">
                        <?php
                    }
                }?>
                </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
			if(function_exists('wp_pagenavi')) {
				?>
                <div class="webd-pagenavi">
					<?php
                    wp_pagenavi(array( 'query' => $the_query));
                    ?>
                </div>
                <?php
			}else{
				if($posts_per_page<$count){
					$loadtrsl = get_option('webd_text_loadm')!='' ? get_option('webd_text_loadm') : esc_html__('Load more','WEBDWooEVENT');
					echo '
						<div class="ex-loadmore">
							<input type="hidden"  name="id_grid" value="grid-'.$ID.'">
							<input type="hidden"  name="num_page" value="'.$num_pg.'">
							<input type="hidden"  name="num_page_uu" value="1">
							<input type="hidden"  name="current_page" value="1">
							<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
							<input type="hidden"  name="param_query" value="'.esc_html(str_replace('\/', '/', json_encode($args))).'">
							<input type="hidden"  name="param_ids" value="'.esc_html(str_replace('\/', '/', json_encode($arr_ids))).'">
							<input type="hidden" id="param_shortcode" name="param_shortcode" value="'.esc_html(str_replace('\/', '/', json_encode($atts))).'">
							<a  href="javascript:void(0)" class="loadmore-grid" data-id="grid-'.$ID.'">
								<span class="load-text">'.$loadtrsl.'</span><span></span>&nbsp;<span></span>&nbsp;<span></span>
							</a>';
					echo'</div>';
				}
			}?>
        </div>
        <div class="clearfix"></div>
		<?php
	}else{
		$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
		if(($orderby=='has_signed_up' || $orderby=='has_submited') && !is_user_logged_in()){
			$noftrsl = get_option('webd_text_protect_ct')!='' ? get_option('webd_text_protect_ct') : esc_html__('Please Login to See','WEBDWooEVENT');
			echo '<h2>'.$noftrsl.'</h2>';
		}else{
			echo '<p>'.$noftrsl.'</p>';
		}
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_grid', 'parwebd_webd_grid_func' );
add_action( 'after_setup_theme', 'webd_reg_grid_vc' );
function webd_reg_grid_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Grid", "WEBDWooEVENT"),
	   "base" => "webd_grid",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
		   array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'WEBDWooEVENT'),
			 "param_name" => "style",
			 "value" => array(
			 	esc_html__('Modern', 'WEBDWooEVENT') => '',
				esc_html__('Classic', 'WEBDWooEVENT') => 'classic',
				
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
			"heading" => esc_html__("Posts per page", "WEBDWooEVENT"),
			"param_name" => "posts_per_page",
			"value" => "",
			"description" => esc_html__("Number items per page", 'WEBDWooEVENT'),
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
				esc_html__('Ongoing & Upcoming', 'WEBDWooEVENT') => 'ontoup',
				esc_html__('Past', 'WEBDWooEVENT') => 'past',
				esc_html__('Day', 'WEBDWooEVENT') => 'day',
				esc_html__('Week', 'WEBDWooEVENT') => 'week',
				esc_html__('Month', 'WEBDWooEVENT') => 'month',
				esc_html__('Year', 'WEBDWooEVENT') => 'year',
				esc_html__('None', 'WEBDWooEVENT') => 'none',
				esc_html__('Has signed up', 'WEBDWooEVENT') => 'has_signed_up',
				esc_html__('Has Submited', 'WEBDWooEVENT') => 'has_submited',
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
			 "heading" => esc_html__("Show time", 'WEBDWooEVENT'),
			 "param_name" => "show_time",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
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