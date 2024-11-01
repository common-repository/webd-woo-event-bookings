<?php
function parwebd_webd_search_func($atts, $content){
	if(is_admin()){ return;}
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$cats =  isset($atts['cats']) ? $atts['cats'] :'';
	$tags =  isset($atts['tags']) ? $atts['tags'] :'';
	$years =  isset($atts['years']) ? $atts['years'] :'';
	$month =  isset($atts['month']) ? $atts['month'] :'';
	$location =  isset($atts['location']) ? $atts['location'] :'';
	$show_viewas =  isset($atts['show_viewas']) ? $atts['show_viewas'] :'';
	$search_mode =  isset($atts['search_mode']) ? $atts['search_mode'] :'';
	$search_ajax =  isset($atts['search_ajax']) ? $atts['search_ajax'] :'';
	$search_layout =  isset($atts['search_layout']) ? $atts['search_layout'] :'';
	$result_showin =  isset($atts['result_showin']) && $atts['result_showin']!='' ? $atts['result_showin'] :'.webd-ajax-result';
	if($show_viewas==1){ $result_showin = '.webd-view-as-s';}
	ob_start();
	?>
    <div class="webd-search-container webd-s<?php echo esc_attr($ID);?>" data-id ="webd-s<?php echo esc_attr($ID);?>">
    	<div class="webd-loading">
            <div class="wpex-spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
        <div class="woo-event-toolbar webd-search-shortcode <?php if($search_ajax=='1'){?> webd-ajax-search<?php }?>" id="webd-s<?php echo esc_attr($ID);?>">
                <div class="row">
                    <div class="<?php if($show_viewas==1){?> col-md-8 <?php }else{?>col-md-12<?php }?>">
                        <div class="webd-search-form">
                            <span class="search-lb lb-sp"><?php echo get_option('webd_text_search')!='' ? get_option('webd_text_search') : esc_html__('Search','WEBDWooEVENT');?></span>
                            <input type="hidden"  name="ajax_url" value="<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>">
                            <input type="hidden"  name="result_showin" value="<?php echo esc_attr($result_showin);?>">
                            <input type="hidden"  name="search_layout" value="<?php echo esc_attr($search_layout);?>">
                            <input type="hidden"  name="search_id" value="webd-s<?php echo esc_attr($ID);?>">
                            <form role="search" method="get" id="searchform" class="wooevent-search-form" action="<?php echo home_url(); ?>/">
                                <div class="input-group">
                                    <div class="input-group-btn webd-search-dropdown webd-sfilter" data-id="webd-s<?php echo esc_attr($ID);?>">
                                      <button name="product_cat" type="button" class="btn btn-default webd-search-dropdown-button webd-showdrd"><span class="button-label"><?php echo get_option('webd_text_evfilter')!='' ? get_option('webd_text_evfilter') : esc_html__('Filter','WEBDWooEVENT'); ?></span> <span class="fa fa-angle-down"></span></button>
                                    </div>
                                    <input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php echo get_option('webd_text_search')!='' ? get_option('webd_text_search') : esc_html__('Search','WEBDWooEVENT'); ?>" class="form-control" />
                                    <input type="hidden" name="post_type" value="product" />
                                    <span class="input-group-btn">
                                        <button type="submit" id="searchsubmit" class="btn btn-default webd-search-submit" <?php if(isset($ID) && $ID!=''){?> data-id ="webd-s<?php echo esc_attr($ID);?>" <?php }?>><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                                <?php if($search_mode=='event'){?>
                                    <input type="hidden" name="sm" value="event">
                                <?php }?>
                                <?php webd_search_filters($cats,$tags,$years,$location,$month,$ID);?>
                        </form>
                        </div>
                    </div>
                    <?php if($show_viewas==1){
                        webd_show_viewas();
                    }?>
                </div>
            </div>
            <?php 
			if($show_viewas!=1 && $search_ajax=='1'){
				echo '<div class="webd-ajax-result"></div>';
			}
            if($show_viewas==1){
                echo '<div class="webd-view-as-s">';
                    $webd_shop_view = get_option('webd_shop_view');
                    $webd_firstday = get_option('webd_firstday');
                    if(isset($_GET['view']) && sanitize_text_field($_GET['view']=='day') || !isset($_GET['view']) && $webd_shop_view=='day'){
                        echo do_shortcode('[webd_calendar cat="'.$cats.'" view="agendaDay" firstday="'.$webd_firstday.'"]');
                    }elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='week' || !isset($_GET['view']) && $webd_shop_view=='week'){
                        echo do_shortcode('[webd_calendar cat="'.$cats.'" view="agendaWeek" firstday="'.$webd_firstday.'"]');
                    }elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='map' || !isset($_GET['view']) && $webd_shop_view=='map'){
                        echo do_shortcode('[webd_map cat="'.$cats.'" tag="'.$tags.'" type="upcoming"]');
                    }elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='list' || !isset($_GET['view']) && $webd_shop_view=='list'){
                        echo do_shortcode('[webd_grid cat="'.$cats.'" tag="'.$tags.'"  style="classic" columns="3" count="999" posts_per_page="6" orderby="upcoming"]');
                    }elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='month' || !isset($_GET['view']) && $webd_shop_view=='month'){
                        echo do_shortcode('[webd_calendar cat="'.$cats.'" firstday="'.$webd_firstday.'"]');
                    }elseif(!isset($_GET['view']) ){
                        
                    }
                echo '</div>';
            }?>
        </div>
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}

if(!function_exists('webd_show_viewas')){
	function webd_show_viewas(){
		?>
		<div class="col-md-4">
			<div class="webd-viewas vs_search">
				<?php $pageURL = 'http';
				if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
				$pageURL .= "://";
				if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}?>
				<span class="viewas-lb lb-sp"><?php echo get_option('webd_text_viewas')!='' ? get_option('webd_text_viewas') : esc_html__('View as','WEBDWooEVENT');?></span>
				<div class="input-group-btn webd-viewas-dropdown">
					<button name="webd-viewas" type="button" class="btn btn-default webd-viewas-dropdown-button webd-showdrd">
						<span class="button-label">
							<?php 
							$webd_shop_view = get_option('webd_shop_view');
							if(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='day' || !isset($_GET['view']) && $webd_shop_view=='day'){
								echo get_option('webd_text_day')!='' ? get_option('webd_text_day') : esc_html__('Day','WEBDWooEVENT'); 
							}elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='week' || !isset($_GET['view']) && $webd_shop_view=='week'){
								echo get_option('webd_text_week')!='' ? get_option('webd_text_week') : esc_html__('Week','WEBDWooEVENT');
							}elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='map' || !isset($_GET['view']) && $webd_shop_view=='map'){
								echo get_option('webd_text_map')!='' ? get_option('webd_text_map') : esc_html__('Map','WEBDWooEVENT'); 
							}elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='list' || !isset($_GET['view']) && $webd_shop_view=='list'){
								echo get_option('webd_text_list')!='' ? get_option('webd_text_list') : esc_html__('List','WEBDWooEVENT');
							}elseif(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='month' || !isset($_GET['view']) && $webd_shop_view=='month'){
								echo get_option('webd_text_month')!='' ? get_option('webd_text_month') : esc_html__('Month','WEBDWooEVENT');
							}elseif(!isset($_GET['view']) ){
								echo '<span>'.get_option('webd_text_select')!='' ? get_option('webd_text_select') : esc_html__('Select','WEBDWooEVENT').'</span>';
							}?>
						</span> <span class="icon-arr fa fa-angle-down"></span>
					</button>
					<ul class="webd-dropdown-select">
						<?php if((!isset($_GET['view']) && $webd_shop_view !='list') || (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='list')){?>
							<li><a href="<?php echo add_query_arg( array('view' => 'list'), $pageURL); ?>" data-value=""><?php echo get_option('webd_text_list')!='' ? get_option('webd_text_list') : esc_html__('List','WEBDWooEVENT'); ?></a></li>
						<?php }
						if((!isset($_GET['view']) && $webd_shop_view !='map') || (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='map')){?>
							<li><a href="<?php echo add_query_arg( array('view' => 'map'), $pageURL); ?>" data-value=""><?php echo get_option('webd_text_map')!='' ? get_option('webd_text_map') : esc_html__('Map','WEBDWooEVENT'); ?></a></li>
						<?php }
						if((!isset($_GET['view']) && $webd_shop_view !='month') ||  (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='month')){?>
						<li><a href="<?php echo add_query_arg( array('view' => 'month'), $pageURL); ?>" data-value=""><?php echo get_option('webd_text_month')!='' ? get_option('webd_text_month') : esc_html__('Month','WEBDWooEVENT'); ?></a></li>
						<?php }
						if((!isset($_GET['view']) && $webd_shop_view !='week') || (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='week')){?>
						<li><a href="<?php echo add_query_arg( array('view' => 'week'), $pageURL); ?>" data-value=""><?php echo get_option('webd_text_week')!='' ? get_option('webd_text_week') : esc_html__('Week','WEBDWooEVENT'); ?></a></li>
						<?php }
						if((!isset($_GET['view']) && $webd_shop_view !='week') || (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='day')){?>
						<li><a href="<?php echo add_query_arg( array('view' => 'day'), $pageURL); ?>" data-value=""><?php echo get_option('webd_text_day')!='' ? get_option('webd_text_day') : esc_html__('Day','WEBDWooEVENT'); ?></a></li>
						<?php }?>
					</ul>
				</div><!-- /btn-group -->
			</div>
		</div>
		<?php
		
	}
}
if(!function_exists('webd_search_filters')){
	function webd_search_filters($cat_include, $tag_include, $webd_syear_include, $webd_location_include, $webd_month_include, $ID){
		$column = 4;
		if($cat_include=='hide'){
			$column = $column -1;
		}
		if($tag_include=='hide'){
			$column = $column -1;
		}
		if($webd_syear_include=='hide'){
			$column = $column -1;
		}
		if($column=='3'){ $class = 'col-md-4';}elseif($column=='2'){$class = 'col-md-6';}
		elseif($column=='1'){$class = 'col-md-12';}else{$class = 'col-md-3';}
		$all_text = get_option('webd_text_all')!='' ? get_option('webd_text_all') : esc_html__('All','WEBDWooEVENT');?>
		<div class="webd-filter-expand <?php echo esc_attr('webd-column-'.$column)?> row">
			<?php 
			if($cat_include!='hide'){
				$args = array( 'hide_empty' => false ); 
				if($cat_include!=''){
					$cat_include = explode(",", $cat_include);
					if(is_numeric($cat_include[0])){
						$args['include'] = $cat_include;
					}else{
						$args['slug'] = $cat_include;
					}
				}
				$terms = get_terms('product_cat', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
					<div class="webd-filter-cat <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo get_option('webd_text_evcat')!='' ? get_option('webd_text_evcat') : esc_html__('Category','WEBDWooEVENT');?></span>
                        <select name="product_cat">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
							foreach ( $terms as $term ) {
								$selected = '';
								if((isset($_GET['product_cat']) && sanitize_text_field($_GET['product_cat']) == $term->slug)){
									$selected ='selected';
								}
								echo '<option value="'. $term->slug .'" '.$selected.'>'. $term->name .'</option>';
							}?>
                        </select>
					</div>
			<?php } 
			}
			if($tag_include!='hide'){
				$args = array( 'hide_empty' => false ); 
				if($tag_include!=''){
					$tag_include = explode(",", $tag_include);
					if(is_numeric($tag_include[0])){
						$args['include'] = $tag_include;
					}else{
						$args['slug'] = $tag_include;
					}
				}
				$terms = get_terms('product_tag', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
					<div class="webd-filter-tag <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo get_option('webd_text_evtag')!='' ? get_option('webd_text_evtag') : esc_html__('Tags','WEBDWooEVENT');?></span>
                        <select name="product_tag">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
							foreach ( $terms as $term ) {
								$selected = '';
								if((isset($_GET['product_tag']) && sanitize_text_field($_GET['product_tag']) == $term->slug)){
									$selected ='selected';
								}	
								echo '<option value="'. $term->slug .'" '.$selected.'>'. $term->name .'</option>';
							}
                              ?>
                        </select>
					</div>
			<?php } 
			} 
			if($webd_syear_include!='hide'){
				$cr_y = date("Y");
				if($webd_syear_include!=''){
					$arr_ya = explode(",", $webd_syear_include);
				}else{
					$arr_ya = array($cr_y-2,$cr_y-1,$cr_y,$cr_y+1,$cr_y+2);
				}
				if ( ! empty( $arr_ya ) ){ ?>
					<div class="webd-filter-year <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo get_option('webd_text_evyears')!='' ? get_option('webd_text_evyears') : esc_html__('Years','WEBDWooEVENT');?></span>
                        <select name="evyear">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
							foreach ($arr_ya as $item ) {
								$selected = '';
								if((isset($_GET['evyear']) && sanitize_text_field($_GET['evyear']) == $item)){
									$selected ='selected';
								}	
								echo '<option value="'. $item .'" '.$selected.'>'. $item .'</option>';
							}?>
                        </select>
					</div>
			<?php }
			}
			if($webd_location_include!='hide'){
				if($webd_location_include!=''){
					$ids = explode(",", $webd_location_include);
				}else{ $ids = '';}
				$args = array(
					'post_type' => 'webd_venue',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'post__in' =>  $ids,
					'ignore_sticky_posts' => 1,
				);
				$the_query = new WP_Query( $args );
				if($the_query->have_posts()){ ?>
					<div class="webd-filter-loc <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo get_option('webd_text_loca')!='' ? get_option('webd_text_loca') : esc_html__('Locations','WEBDWooEVENT');?></span>
                        <select name="location">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
								while($the_query->have_posts()){ $the_query->the_post();
								  $selected = '';
								  if((isset($_GET['location']) && sanitize_text_field($_GET['location']) == get_the_ID())){
									  $selected ='selected';
								  }
								  echo '<option value="'. get_the_ID() .'" '.$selected.'>'. get_the_title() .'</option>';
                              }?>
                        </select>
					</div>
				<?php }
				wp_reset_postdata();
			}
			
			if($webd_month_include=='upcoming_month' || $webd_month_include=='month'){
				$cr_m = date("m");
				if($webd_month_include!='' && $webd_month_include!='upcoming_month'){
					$arr_ya = explode(",", $webd_syear_include);
				}else{
					
					$m2 = $cr_m+1 >12  ? $cr_m+1 - 12 : $cr_m+1;
					$m3 = $cr_m+2 >12  ? $cr_m+2 - 12 : $cr_m+2;
					$m4 = $cr_m+3 >12  ? $cr_m+3 - 12 : $cr_m+3;
					$m5 = $cr_m+4 >12  ? $cr_m+4 - 12 : $cr_m+4;
					$arr_ya = array($cr_m,$m2,$m3,$m4,$m5);
				}
				if ( ! empty( $arr_ya ) ){ ?>
					<div class="webd-filter-year <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo esc_html__('Month','WEBDWooEVENT');?></span>
                        <select name="<?php echo $webd_month_include=='upcoming_month' ? 'month_up' : 'month';?>">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
							foreach ($arr_ya as $item ) {
								$selected = '';
								if((isset($_GET['month']) && sanitize_text_field($_GET['month']) ==$item) || (isset($_GET['month_up']) && sanitize_text_field($_GET['month_up']) ==$item)){
									$selected ='selected';
								}
								if($webd_month_include=='upcoming_month'){
									echo '<option value="'. $item .'" '.$selected.'>'. webd_convert_month_to_text($item) .'</option>';
								}else{
									echo '<option value="'. $item .'" '.$selected.'>'. $item .'</option>';
								}
							}?>
                        </select>
					</div>
				<?php }
			}
			?>
            <span class="input-group-btn">
                <button type="submit" id="searchsubmit" class="btn-default webd-search-submit" data-id ="webd-s<?php echo esc_attr($ID);?>"><?php echo get_option('webd_text_evfilter')!='' ? get_option('webd_text_evfilter') : esc_html__('Filter','WEBDWooEVENT'); ?></button>
            </span>
        </div>
	<?php
    }
}


add_shortcode( 'webd_search', 'parwebd_webd_search_func' );
add_action( 'after_setup_theme', 'webd_search_reg_vc' );
function webd_search_reg_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Search", "WEBDWooEVENT"),
	   "base" => "webd_search",
	   "class" => "",
	   "icon" => "icon-search",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Search mode", 'WEBDWooEVENT'),
			 "param_name" => "search_mode",
			 "value" => array(
			 	esc_html__('All Products', 'WEBDWooEVENT') => '',
				esc_html__('Only product is event', 'WEBDWooEVENT') => 'event',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Category", "WEBDWooEVENT"),
			"param_name" => "cats",
			"value" => "",
			"description" => esc_html__("List of Category ID (or slug), separated by a comma, Ex: 13,14 (enter hide to hire this field)", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Tags", "WEBDWooEVENT"),
			"param_name" => "tags",
			"value" => "",
			"description" => esc_html__("List of Tags ID (or slug), separated by a comma, Ex: 13,14 (enter hide to hire this field)", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Locations", "WEBDWooEVENT"),
			"param_name" => "location",
			"value" => "",
			"description" => esc_html__("List of Venue ID, separated by a comma, Ex: 13,14 (enter hide to hire this field)", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Years", "WEBDWooEVENT"),
			"param_name" => "years",
			"value" => "",
			"description" => esc_html__("List of year, separated by a comma, Ex: 2015,2016,2017 (enter hide to hire this field)", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Search Ajax", 'WEBDWooEVENT'),
			 "param_name" => "search_ajax",
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
			 "heading" => esc_html__("Search Ajax layout", 'WEBDWooEVENT'),
			 "param_name" => "search_layout",
			 "value" => array(
			 	esc_html__('Table', 'WEBDWooEVENT') => '',
				esc_html__('Grid', 'WEBDWooEVENT') => 'grid',
			 ),
			 "description" => esc_html__("Show search ajax result in table or grid layout", 'WEBDWooEVENT'),
		  ),
		  
		 array(
		  "admin_label" => true,
		  "type" => "textfield",
		  "heading" => esc_html__("Search ajax result show in", "WEBDWooEVENT"),
		  "param_name" => "result_showin",
		  "value" => "",
		  "description" => esc_html__("Enter class or id of element you want to show search result, default show in search shortcode element", "WEBDWooEVENT"),
		),
		array(
		  "admin_label" => true,
		   "type" => "dropdown",
		   "class" => "",
		   "heading" => esc_html__("Show view as", 'WEBDWooEVENT'),
		   "param_name" => "show_viewas",
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