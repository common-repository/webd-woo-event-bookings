<?php
if(!function_exists('webd_startsWith')){
	function webd_startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}
} 
if(!function_exists('webd_get_google_fonts_url')){
	function webd_get_google_fonts_url ($font_names) {
	
		$font_url = '';
	
		$font_url = add_query_arg( 'family', urlencode(implode('|', $font_names)) , "//fonts.googleapis.com/css" );
		return $font_url;
	} 
}
if(!function_exists('webd_get_google_font_name')){
	function webd_get_google_font_name($family_name){
		$name = $family_name;
		if(webd_startsWith($family_name, 'http')){
			// $family_name is a full link, so first, we need to cut off the link
			$idx = strpos($name,'=');
			if($idx > -1){
				$name = substr($name, $idx);
			}
		}
		$idx = strpos($name,':');
		if($idx > -1){
			$name = substr($name, 0, $idx);
			$name = str_replace('+',' ', $name);
		}
		return $name;
	}
}


function webd_filter_wc_get_template_single($template, $slug, $name){
	if($slug=='content' && $name =='single-product'){
		return wooevent_template_plugin('single-product');
	}else{ 
		return $template;
	}
}
function filter_wc_get_template_shop($template, $slug, $name){
	if($slug=='content' && $name =='product'){
		return wooevent_template_plugin('product');
	}else{ 
		return $template;
	}
}
function webd_filter_wc_get_template_related($located, $template_name, $args){
	if($template_name =='single-product/related.php'){
		if (locate_template('webd-event-bookings-daywiwebd-cost/related.php') != '') {
			return get_template_part('webd-event-bookings-daywiwebd-cost/related');
		} else {
			return WEBD_EVENT_BOOKINGS_URL().'templates/related.php';
		}
	}else{ 
		return $located;
	}
}

function webd_filter_wc_get_template_no_result($located, $template_name, $args){
	if(($template_name =='loop/no-products-found.php')){
		$shop_view = get_option('webd_shop_view');
		if(is_tax() || ($shop_view=='list' && !isset($_GET['view'])) || (isset($_GET['view']) && sanitize_text_field($_GET['view'])=='list' ) ){
			if (locate_template('webd-event-bookings-daywiwebd-cost/no-products-found.php') != '') {
				return get_template_part('webd-event-bookings-daywiwebd-cost/no-products-found');
			} else {
				return WEBD_EVENT_BOOKINGS_URL().'templates/no-products-found.php';
			}
		}else{
			return $located;
		}
	}else{ 
		return $located;
	}
}

$webd_main_purpose = get_option('webd_main_purpose');
if($webd_main_purpose!='meta'){
	add_filter( 'wc_get_template_part', 'webd_filter_wc_get_template_single', 10, 3 );
	add_filter( 'wc_get_template_part', 'filter_wc_get_template_shop', 99, 3 );
	//if($webd_main_purpose=='custom'){
		add_filter( 'wc_get_template', 'webd_filter_wc_get_template_related', 99, 3 );
		add_filter( 'wc_get_template', 'webd_filter_wc_get_template_no_result', 99, 3 );
	//}

}
// Change number or products per row to 3
if(!function_exists('wooevent_template_plugin')){
	function wooevent_template_plugin($pageName,$shortcode=false){
		if(isset($shortcode) && $shortcode== true){
			if (locate_template('webd-event-bookings-daywiwebd-cost/content-shortcode/content-' . $pageName . '.php') != '') {
				get_template_part('webd-event-bookings-daywiwebd-cost/content-shortcode/content', $pageName);
			} else {
				include WEBD_EVENT_BOOKINGS_URL().'shortcode/content/content-' . $pageName . '.php';
			}

		}else{
			if (locate_template('webd-event-bookings-daywiwebd-cost/content-' . $pageName . '.php') != '') {
				get_template_part('webd-event-bookings-daywiwebd-cost/content', $pageName);
			} else {
				include WEBD_EVENT_BOOKINGS_URL().'templates/content-' . $pageName . '.php';
			}
		}
	}
}
//
if(!function_exists('ex_cat_info')){
	function ex_cat_info($status,$post_type = false, $tax=false, $show_once= false, $hide_link=false){
		ob_start();
		if($status=='off'){ return;}
		if(isset($post_type) && $post_type!='post'){
			if($post_type == 'product' && class_exists('Woocommerce')){
				$tax = 'product_cat';
			}
			if(isset($tax) && $tax!=''){
				$args = array(
					'hide_empty'        => false, 
				);
				$terms = get_the_terms(get_the_ID(), $tax);
				if(!empty($terms)){
					$c_tax = count($terms);
					?>
					<span class="info-cat">
						<?php
						$i=0;
						foreach ( $terms as $term ) {
							$i++;
							if(isset($hide_link) && $hide_link ==1){
								echo $term->name;
							}else{
								echo '<a href="'.get_term_link( $term ).'" title="' . $term->name . '">'. $term->name .'</a>';
							}
							if($show_once==1){break;}
							if($i != $c_tax){ echo ', ';}
						}
						?>
                    </span>
                    <?php
				}
			}
		}else{
			$category = get_the_category();
			if(!isset($show_once) || $show_once!='1'){
				if(!empty($category)){
					?>
					<span class="info-cat">
						<i class="ion-ios-photos-outline"></i>
						<?php the_category(', '); ?>
					</span>
					<?php  
				}
			}else{
				if(!empty($category)){
					?>
					<span class="info-cat">
						<i class="ion-ios-photos-outline"></i>
						<?php
						foreach($category as $cat_item){
							if(is_array($cat_item) && isset($cat_item[0]))
								$cat_item = $cat_item[0];
								echo '
									<a href="' . esc_url(get_category_link( $cat_item->term_id )) . '" title="' . esc_html__('View all posts in ') . $cat_item->name . '">' . $cat_item->name . '</a>';
								if($show_once==1){
									break;
								}
							}
							?>
                    </span>
                    <?php
				}
			}
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
// Get has purchased
function webd_get_all_products_ordered_by_user() {
    $orders = webd_get_all_user_orders(get_current_user_id(), 'completed');
    if(empty($orders)) {
        return false;
    }
    $order_list = '(' . join(',', $orders) . ')';//let us make a list for query
    //so, we have all the orders made by this user that were completed.
    //we need to find the products in these orders and make sure they are downloadable.
    global $wpdb;
    $query_select_order_items = "SELECT order_item_id as id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id IN {$order_list}";
    $query_select_product_ids = "SELECT meta_value as product_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key=%s AND order_item_id IN ($query_select_order_items)";
    $products = $wpdb->get_col($wpdb->prepare($query_select_product_ids, '_product_id'));
    return $products;
}
function webd_get_all_user_orders($user_id, $status = 'completed') {
    if(!$user_id) {
        return false;
    }
    $status = array( 'wc-completed' );
    $status = apply_filters( 'webd_order_status_of_user', $status );
    $args = array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => $user_id,
        'post_type' => 'shop_order',
        'post_status' => $status
        
    );
    $posts = get_posts($args);
    //get the post ids as order ids
    return wp_list_pluck($posts, 'ID');
}
// Query function
if(!function_exists('woo_event_query')){
	function woo_event_query($posttype, $count, $order, $orderby, $meta_key, $cat, $tag, $ids,$page=false,$data_qr=false,$spe_day=false, $feature=false, $meta_value=false, $taxonomy=false, $terms=false, $ex_ids=false ){
		if($orderby=='has_signed_up' || $orderby=='signed_upcoming'  || $orderby=='signed_past'){
			if(get_current_user_id()){
				$ids = webd_get_all_products_ordered_by_user(); 
				if($ids =='' || empty($ids)){ $ids ='-1';}
			}else{
				$ids = '-1';
			}
		}elseif($orderby=='has_submited'){
			if(get_current_user_id()){
				$ids = get_user_meta(get_current_user_id(), '_my_submit', true);
				if($ids =='' || empty($ids)){ $ids ='-1';}
			}else{
				$ids = '-1';
			}
		}
		$texo = array();
		if($tag!=''){
			$tags = explode(",",$tag);
			if(is_numeric($tags[0])){$field_tag = 'term_id'; }
			else{ $field_tag = 'slug'; }
			if(count($tags)>1){
				  $texo['relation'] = 'OR';
				  foreach($tags as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'product_tag',
							  'field' => $field_tag,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => 'product_tag',
							  'field' => $field_tag,
							  'terms' => $tags,
						  )
				  );
			}
		}
		//cats
		if($cat!=''){
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(!is_array($texo)){ $texo = array();}
			$texo['relation'] = 'OR';
			if(count($cats)>1){
				  foreach($cats as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'product_cat',
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo[] = 
					  array(
							  'taxonomy' => 'product_cat',
							  'field' => $field,
							  'terms' => $cats,
				  );
			}
		}
		
		//taxonomy
		if(isset($taxonomy) && $taxonomy!='' && isset($terms) && $terms!=''){
			$terms = explode(",",$terms);
			if(is_numeric($terms[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(!is_array($texo)){ $texo = array();}
			$texo['relation'] = 'OR';
			if(count($terms)>1){
				  foreach($terms as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo[] = 
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $terms,
				  	);
			}
			
			
		}
		$cure_time =  strtotime("now");
		$gmt_offset = get_option('gmt_offset');
		if($gmt_offset!=''){
			$cure_time = $cure_time + ($gmt_offset*3600);
		}
		if($ids!='' || (is_array($ids) && !empty($ids))){ //specify IDs
			
			if(!is_array($ids)){
				$ids = explode(",", $ids);
			}
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'post__in' =>  $ids,
				'order' => $order,
				'orderby' => $orderby,
				'meta_key' => $meta_key,
				'ignore_sticky_posts' => 1,
			);
			if($orderby=='has_signed_up'){
				$args['meta_query'] = array(
					array(
						'key'  => 'webd_startdate',
						'value' => 0,
						'compare' => '>'
					)
				);
			}elseif($orderby=='signed_upcoming'){
				$args['orderby']= 'meta_value_num';
				$args['order']= 'ASC';
				$args['meta_key']= 'webd_startdate';
				$args['meta_query'] = array(
					array(
						'key'  => 'webd_startdate',
						'value' => strtotime("now"),
						'compare' => '>'
					)
				);
			}elseif($orderby=='signed_past'){
				$args['orderby']= 'meta_value_num';
				$args['order']= 'DESC';
				$args['meta_key']= 'webd_enddate';
				$args['meta_query'] = array(
					array(
						'key'  => 'webd_enddate',
						'value' => strtotime("now"),
						'compare' => '<'
					)
				);
			}elseif($orderby=='upcoming'){
				if($order==''){$order='ASC';}
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
				$args['meta_key']= 'webd_startdate';
				/*$args['meta_value']= $cure_time;
				$args['meta_compare']= '>';*/
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'  => 'webd_startdate',
						'value' => $cure_time,
						'compare' => '>'
					)
				);
			}elseif($orderby=='past'){
				if($order==''){$order='DESC';}
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
				$args['meta_key']= 'webd_enddate';
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'  => 'webd_enddate',
						'value' => $cure_time,
						'compare' => '<'
					)
				);
			}
			if(isset($texo)){
				$args += array('tax_query' => $texo);
			}
		}elseif($ids==''){
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'order' => $order,
				'orderby' => $orderby,
				'meta_key' => $meta_key,
				'ignore_sticky_posts' => 1,
			);
			if(isset($texo)){
				$args += array('tax_query' => $texo);
			}
			if($orderby=='ontoup'){
				if($order==''){$order='ASC';}
				$args['meta_query'] = array( 
					 array(
						'key'     => 'webd_enddate',
						'value'   => $cure_time,
						'compare' => '>',
					),
				);
				$args['meta_key']= 'webd_startdate';
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='upcoming'){
				if($order==''){$order='ASC';}
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
				$args['meta_key']= 'webd_startdate';
				/*$args['meta_value']= $cure_time;
				$args['meta_compare']= '>';*/
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'  => 'webd_startdate',
						'value' => $cure_time,
						'compare' => '>'
					)
				);
			}elseif($orderby=='past'){
				if($order==''){$order='DESC';}
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
				$args['meta_key']= 'webd_enddate';
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'  => 'webd_enddate',
						'value' => $cure_time,
						'compare' => '<'
					)
				);
			}elseif($orderby=='day'){
				if($order==''){$order='ASC';}
				$d_start =  strtotime(date('m/d/Y'));
				$d_end = $d_start + 86400;
				$args += array(
						 'meta_key' => 'webd_startdate',
						 'meta_query' => array( 
						 'relation' => 'AND',
						  array('key'  => 'webd_startdate',
							   'value' => $d_start,
							   'compare' => '>'),
						  array('key'  => 'webd_startdate',
							   'value' => $d_end,
							   'compare' => '<')
						 )
				);
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='week'){
				/*$day = date('w');
				$week_start = date('m/d/Y', strtotime('-'.$day.' days'));
				$week_end = date('m/d/Y', strtotime('+'.(6-$day).' days'));*/
				
				if($order==''){$order='ASC';}
				$week_start =  strtotime('next Monday -1 week', strtotime('this sunday'));
				$week_end = $week_start + 604799;
				$args += array(
						 'meta_key' => 'webd_startdate',
						 //'meta_value' => date('m/d/Y'),
						 'meta_query' => array( 
						 'relation' => 'AND',
						  array('key'  => 'webd_startdate',
							   'value' => $week_start,
							   'compare' => '>'),
						  array('key'  => 'webd_startdate',
							   'value' => $week_end,
							   'compare' => '<=')
						 )
				);
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='month'){
				$month_start = date("m/1/Y") ;
				if($order==''){$order='DESC';}
				$month_end =  date("m/t/Y") ;
				$args += array(
						 'meta_key' => 'webd_startdate',
						 //'meta_value' => date('m/d/Y'),
						 'meta_query' => array( 
						 'relation' => 'AND',
						  array('key'  => 'webd_startdate',
							   'value' => strtotime($month_start),
							   'compare' => '>'),
						  array('key'  => 'webd_startdate',
							   'value' => strtotime($month_end),
							   'compare' => '<=')
						 )
				);
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='year'){
				$y_start = date("1/1/Y") ;
				$y_end =  date("12/t/Y") ;
				if($order==''){$order='DESC';}
				$args += array(
						 'meta_key' => 'webd_startdate',
						 //'meta_value' => date('m/d/Y'),
						 'meta_query' => array( 
						 'relation' => 'AND',
						  array('key'  => 'webd_startdate',
							   'value' => strtotime($y_start),
							   'compare' => '>'),
						  array('key'  => 'webd_startdate',
							   'value' => strtotime($y_end),
							   'compare' => '<=')
						 )
				);
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}
		}	
		if(isset($page) && $page!=''){
			$args['paged'] = $page;
		}
		if($orderby=='has_submited'){
			$args['post_status'] = array( 'publish', 'pending', 'trash' );
		}
		if(isset($meta_value) && $meta_value!='' && $meta_key!=''){
			if(!empty($args['meta_query'])){
				$args['meta_query']['relation'] = 'AND';
			}
			$multi_mtvl = explode(",",$meta_value);
			if(count($multi_mtvl) > 1 ){
				$mt_mul = array();
				$mt_mul['relation'] = 'OR';
				foreach($multi_mtvl as $item) {
					$mt_mul[] = array(
						'key'  => $meta_key,
						'value' => $item,
						'compare' => '='
					);
				}
				$args['meta_query'][] = array( $mt_mul);
			}else{
				$args['meta_query'][] = array(
					'key'  => $meta_key,
					'value' => $meta_value,
					'compare' => '='
				);
			}
		}
		if(isset($feature) && $feature==1){
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			);
		}
		if(isset($data_qr) && $data_qr!='' && is_numeric($data_qr)){
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]= 
				 array(
					'key' => 'webd_webd_views',
					'value' => $data_qr,
					'compare' => 'LIKE'
			);
		}
		if(get_option('woocommerce_hide_out_of_stock_items')=='yes' && $posttype =='product'){
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key'       => '_stock_status',
				'value'     => 'outofstock',
				'compare'   => 'NOT IN'
			);
		}
		if( isset($ex_ids) && $ex_ids!='' || ( isset($ex_ids) && is_array($ex_ids)) ){
			if(!is_array($ex_ids)){
				$ex_ids = explode(",", $ex_ids);
			}
			$args['post__not_in'] = $ex_ids;
		}
		return apply_filters( 'wooevent_query', $args,$orderby );
	}
}
//View search bar
if(!function_exists('wooevent_search_view_bar')){
	function wooevent_search_view_bar($ID=false){
		ob_start();
		$search_ajax = get_option('webd_search_ajax')=='yes' ? 1 : '';
		$webd_search_style = get_option('webd_search_style');
		?>
        <div class="woo-event-toolbar">
        	<div class="row">
                <div class="<?php if(is_search()){?>col-md-12<?php }else{?> col-md-8<?php }?>">
                <?php 
					$cat_include =  get_option('webd_scat_include');
					$tag_include = get_option('webd_stag_include');
					$webd_syear_include = get_option('webd_syear_include');
					$webd_loca_include = get_option('webd_loca_include');
					if(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='map'){
					}
					echo do_shortcode('[webd_search cats="'.$cat_include.'" tags="'.$tag_include.'" location="'.$webd_loca_include.'" years="'.$webd_syear_include.'" search_ajax="'.$search_ajax.'" result_showin=".woo-event-toolbar + .webd-calendar"]');
				?>
                </div>
                <?php if(!is_search()){?>
                    <div class="col-md-4">
                        <div class="webd-viewas">
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
                <?php }?>
            </div>

        </div>    
    	<?php
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
// Ical button
function woo_events_ical() {
	if(isset($_GET['ical_product']) && is_numeric($_GET['ical_product']) && sanitize_text_field($_GET['ical_product'])>0){
		// - start collecting output -
		$startdate = get_post_meta(sanitize_text_field($_GET['ical_product']),'webd_startdate', true );
		if($startdate==''){ return;}
		ob_start();
		// - file header -
		header('Content-type: text/calendar');
		header('Content-Disposition: attachment; filename="'.sanitize_text_field(get_the_title($_GET['ical_product'])).' - ical.ics"');
		$content = "BEGIN:VCALENDAR\r\n";
		$content .= "VERSION:2.0\r\n";
		$content .= 'PRODID:-//'.get_bloginfo('name')."\r\n";
		$content .= "CALSCALE:GREGORIAN\r\n";
		$content .= "METHOD:PUBLISH\r\n";
		$content .= 'X-WR-CALNAME:'.get_bloginfo('name')."\r\n";// Remove this code to disable create new calendar outlook
		$content .= 'X-ORIGINAL-URL:'.get_permalink(sanitize_text_field($_GET['ical_product']))."\r\n";
		$content .= 'X-WR-CALDESC:'.sanitize_text_field(get_the_title(sanitize_text_field($_GET['ical_product'])))."\r\n";
		
		$content .= webd_ical_event_generate(sanitize_text_field($_GET['ical_product']));
		$content .= "END:VCALENDAR\r\n";
		// - full output -
		$tfeventsical = ob_get_contents();
		ob_end_clean();
		$content = apply_filters( 'webd_ical_html', $content, $_GET);
		echo $content;
		exit;
	}else{ return;}
}
add_action('init','woo_events_ical');
if(!function_exists('webd_ical_event_generate')){
	function webd_ical_event_generate($id){
		$startdate = get_post_meta($id,'webd_startdate', true );
		if($startdate==''){ return;}		
		$date_format = get_option('date_format');
		$hour_format = get_option('time_format');
		if($startdate){
			$startdate = gmdate("Ymd\THis", $startdate);// convert date ux
		}
		$enddate = get_post_meta($id,'webd_enddate', true );
		if($enddate){
			$enddate = gmdate("Ymd\THis", $enddate);
		}
		
		$gmts = get_gmt_from_date($startdate); // this function requires Y-m-d H:i:s, hence the back & forth.
		$gmts = strtotime($gmts);
		
		// - grab gmt for end -
		//$gmte = date('Y-m-d H:i:s', $conv_enddate);
		$gmte = get_gmt_from_date($enddate); // this function requires Y-m-d H:i:s, hence the back & forth.
		$gmte = strtotime($gmte);
		
		// - Set to UTC ICAL FORMAT -
		$stime = date('Ymd\THis', $gmts);
		$etime = date('Ymd\THis', $gmte);
		
		$webd_time_zone = get_post_meta($id,'webd_time_zone', true );
		if($webd_time_zone=='' || $webd_time_zone=='def'){
			$gmt_offset = get_option('gmt_offset');
			$webd_time_zone = $gmt_offset;
		}
		$webd_time_zone = $webd_time_zone * 3600;
		$tz = timezone_name_from_abbr('', $webd_time_zone, 0);
		if( $tz ==''){ 
			$tz = timezone_name_from_abbr('', ($webd_time_zone + 1800), 0);
		}
		if($tz!=''){
			$tz = ';TZID='.$tz;
		}
		$title = sanitize_text_field(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8'));
		// - item output -
		$content .= "BEGIN:VEVENT\r\n";
		$content .= 'DTSTART'.$tz.':'.$startdate."\r\n";
		$content .= 'DTEND'.$tz.':'.$enddate."\r\n";
		$content .= 'SUMMARY:'.$title."\r\n";
		$content .= 'DESCRIPTION:'.sanitize_text_field(get_post($id)->post_excerpt, true)."\r\n";
		$content .= 'URL:'.get_permalink($id)."\r\n";
        $content .= 'LOCATION:'.get_post_meta($id, 'webd_adress', true )."\r\n";
		$content .= "END:VEVENT\r\n";
		// - full output -
		
		return $content;
	}
}
// Ical in calendar
function webd_ical_events() {
	if(isset($_GET['ical_events']) && sanitize_text_field($_GET['ical_events'])=='we'){
		// - start collecting output -
		$time_now =  strtotime("now");
		$gmt_offset = get_option('gmt_offset');
		if($gmt_offset!=''){
			$time_now = $time_now + ($gmt_offset*3600);
		}
		$args = array(
			  'post_type' => 'product',
			  'posts_per_page' => -1,
			  'post_status' => 'publish',
			  'ignore_sticky_posts' => 1,
			  'meta_key' => 'webd_startdate',
			  'orderby' => 'meta_value_num',
			  'meta_query' => array(
			  array('key'  => 'webd_startdate',
				   'value' => $time_now,
				   'compare' => '>'),
			  ),
			  'suppress_filters' => false 
		);
		//cats
		$cat ='';
		if(isset($_GET['category']) && $_GET['category']!=''){
			$cat = sanitize_text_field($_GET['category']);
		}
		$taxonomy 		=  isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) :'';
		$terms 			=  isset($_GET['terms']) ? sanitize_text_field($_GET['terms']) :'';
		$webd_view_id 	=  isset($_GET['webd_view']) ? sanitize_text_field($_GET['webd_view']) :'';
		$texo = array();
		if($cat!=''){
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			$texo['relation'] = 'OR';
			if(count($cats)>1){
				  foreach($cats as $iterm) {
					  $texo[] = array(
						  'taxonomy' => 'product_cat',
						  'field' => $field,
						  'terms' => $iterm,
					  );
				  }
			  }else{
				  $texo []= array(
					  'taxonomy' => 'product_cat',
					  'field' => $field,
					  'terms' => $cats,
				  );
			}
		}
		if(isset($taxonomy) && $taxonomy!='' && isset($terms) && $terms!=''){
			$terms = explode(",",$terms);
			if(is_numeric($terms[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			$texo []= array('relation' => 'OR',);
			if(count($terms)>1){
				  foreach($terms as $iterm) {
					  $texo[] =  array(
						  'taxonomy' => $taxonomy,
						  'field' => $field,
						  'terms' => $iterm,
					  );
				  }
			  }else{
				  $texo[] =  array(
					  'taxonomy' => $taxonomy,
					  'field' => $field,
					  'terms' => $terms,
				  );
			}
		}
		if(isset($webd_view_id) && $webd_view_id!='' && is_numeric($webd_view_id)){
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]=  array(
					'key' => 'webd_webd_views',
					'value' => $webd_view_id,
					'compare' => 'LIKE'
			);
		}
		if(isset($texo)){
			$args += array('tax_query' => $texo);
		}
		$the_query = get_posts( $args );
		if(!empty($the_query)){
			// - file header -
			header('Content-type: text/calendar');
			header('Content-Disposition: attachment; filename="'.esc_attr(get_bloginfo('name')).' - ical.ics"');
			// - content header -
			$content = "BEGIN:VCALENDAR\r\n";
			$content .= "VERSION:2.0\r\n";
			$content .= 'PRODID:-//'.get_bloginfo('name')."\r\n";
			$content .= "CALSCALE:GREGORIAN\r\n";
			$content .= "METHOD:PUBLISH\r\n";
			$content .= apply_filters( 'webd_ical_cal_name', 'X-WR-CALNAME:'.get_bloginfo('name')."\r\n");// Remove this code to disable create new calendar outlook
			$content .= 'X-ORIGINAL-URL:'.home_url()."\r\n";
			$content .= 'X-WR-CALDESC:'.esc_attr(get_the_title($id))."\r\n";
			foreach ( $the_query as $post ){
				$content .= webd_ical_event_generate($post->ID);
			}
			$content .= "END:VCALENDAR\r\n";
			echo $content;
		}
		exit;
	}else{ return;}
}
add_action('init','webd_ical_events');

//Status
if(!function_exists('woo_event_status')){
	function woo_event_status( $post_id, $webd_enddate=false){
		if(!$webd_enddate){$webd_enddate = get_post_meta( $post_id, 'webd_enddate', true ) ;}
		global $product; 
		$all_day = get_post_meta($post_id,'webd_allday', true );
		if($all_day==1){
			$gmd = gmdate("Y-m-d", $webd_enddate);
			$webd_enddate = strtotime($gmd) + 86399;
		}
		$webd_main_purpose = webd_global_main_purpose();
		$stock_status = get_post_meta($post_id, '_stock_status',true);
		$numleft  = $product->get_stock_quantity();
		$type = $product->get_type();
		if($type=='variable' && $numleft < 1){
			$numleft  = 0;
			$all_st = 'out';
			//foreach ($product->get_available_variations() as $key_status) {
				//echo get_post_meta( $key_status['variation_id'], '_stock_status', true );
				
			//}
			
			foreach ($product->get_available_variations() as $key) {
				$stk_v_stt = get_post_meta( $key['variation_id'], '_stock_status', true ) ;
				if($stk_v_stt !='outofstock'){
					$all_st = 'in';
				}
				if(!isset($key['max_qty']) || $key['max_qty']==''){
					if($stk_v_stt !='outofstock'){
						$numleft=0;
						break;
					}
				}else{
					$numleft  = $numleft + $key['max_qty'];
				}
			}
			if($all_st == 'out'){ $stock_status ='outofstock';}
		}
		if($stock_status !='outofstock') { 
			  $now =  strtotime("now");
			  
			  $webd_time_zone = get_post_meta($post_id,'webd_time_zone',true);
			  if($webd_time_zone!='' && $webd_time_zone!='def'){
				  $webd_time_zone = $webd_time_zone * 60 * 60;
				  $now = $webd_time_zone + $now;
			  }
			  
			  if($now > $webd_enddate && $webd_enddate!=''){
				  $stt =  get_option('webd_text_event_pass')!='' ? get_option('webd_text_event_pass') : esc_html__('This event has passed','WEBDWooEVENT');
			  }else{
				  if($numleft==0){
					  $stt = get_option('webd_text_unl_tic')!='' ? get_option('webd_text_unl_tic') : esc_html__('Unlimited tickets','WEBDWooEVENT');
					  if($webd_main_purpose=='woo'){
						  $stt = get_option('webd_text_unl_pie')!='' ? get_option('webd_text_unl_pie') : esc_html__('Unlimited pieces','WEBDWooEVENT');
					  }
				  }else{
					  $qtyavtrsl = get_option('webd_text_qty_av')!='' ? get_option('webd_text_qty_av') :  esc_html__(' Qty Available','WEBDWooEVENT');
					  $stt = $numleft.'  '.$qtyavtrsl;
					  if($webd_main_purpose=='woo'){
						  $pietrsl = get_option('webd_text_pie_av')!='' ? get_option('webd_text_pie_av') :  esc_html__(' Pieces Available','WEBDWooEVENT');
						  $stt = $numleft.'  '.$pietrsl;
					  }
				  }
			  }
		  }else{ 
			  $stt = get_option('webd_text_no_tk')!='' ? get_option('webd_text_no_tk') : esc_html__('There are no ticket available at this time.','WEBDWooEVENT'); 
			  if($webd_main_purpose=='woo'){
				  $stt = get_option('webd_text_no_pie')!='' ? get_option('webd_text_no_pie') : esc_html__('There are no pieces available at this time.','WEBDWooEVENT'); 
			  }
		  }
		  $stt = apply_filters( 'webd_ticket_status', $stt,$post_id );
		  return $stt;
	}
}
//Calendar event ajax
if(!function_exists('webd_get_product_type_fix')){
	function webd_get_product_type_fix( $product_id ) {
		$post_type = get_post_type( $product_id );
		if ( 'product_variation' === $post_type ) {
			return 'variation';
		} elseif ( 'product' === $post_type ) {
			$terms = get_the_terms( $product_id, 'product_type' );
			return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		} else {
			return false;
		}
	}
}

add_action( 'wp_ajax_webd_get_events_calendar', 'webd_get_events_calendar',99 );
add_action( 'wp_ajax_nopriv_webd_get_events_calendar', 'webd_get_events_calendar',99 );
function webd_get_events_calendar() {
	$atts = json_decode( stripslashes( sanitize_text_field($_GET['param_shortcode']) ), true );
	$curl = sanitize_text_field($_GET['lang']);
	if (class_exists('SitePress')){
		global $sitepress;
		$sitepress->switch_lang($curl, true);
	}
	$result ='';
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
	);
	$time_now =  strtotime("now");
	if(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby'])=='upcoming'){
		$_GET['start'] = $time_now;
	}elseif(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby'])=='past'){
		$_GET['end'] = $time_now;
	}
	if($_GET['end'] && $_GET['start']){
		$args = array(
			  'post_type' => 'product',
			  'posts_per_page' => -1,
			  'post_status' => 'publish',
			  'ignore_sticky_posts' => 1,
			  'meta_key' => 'webd_startdate',
			  'orderby' => 'meta_value_num',
			  'meta_query' => array(
			  'relation' => 'AND',
			  array('key'  => 'webd_enddate',
				   'value' => sanitize_text_field($_GET['start']),
				   'compare' => '>='),
			  array('key'  => 'webd_startdate',
				   'value' => sanitize_text_field($_GET['end']),
				   'compare' => '<=')
			  ),
			  'suppress_filters' => false 
		);
		//cats
		$taxonomy 	=  isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) :'';
		$terms 		=  isset($_GET['terms']) ? sanitize_text_field($_GET['terms']) :'';
		$webd_view_id =  isset($_GET['webd_view_id']) ? sanitize_text_field($_GET['webd_view_id']) :'';
		$texo = array();
		if(isset($_GET['category']) && $_GET['category']!=''){
			$cat = sanitize_text_field($_GET['category']);
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			$texo['relation'] = 'OR';
			if(count($cats)>1){
				  
				  foreach($cats as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'product_cat',
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo []= 
					  array(
							  'taxonomy' => 'product_cat',
							  'field' => $field,
							  'terms' => $cats,
				  );
			}
		}
		if(isset($_GET['tag']) && $_GET['tag']!=''){
			$tag 	= sanitize_text_field($_GET['tag']);
			$tags 	= explode(",",$tag);
			if(is_numeric($tags[0])){$field_tag = 'term_id'; }
			else{ $field_tag = 'slug'; }
			if(count($tags)>1){
				  $texo['relation'] = 'AND';
				  foreach($tags as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'product_tag',
							  'field' => $field_tag,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => 'product_tag',
							  'field' => $field_tag,
							  'terms' => $tags,
						  )
				  );
			}
		}
		if(isset($taxonomy) && $taxonomy!='' && isset($terms) && $terms!=''){
			$terms = explode(",",$terms);
			if(is_numeric($terms[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			$texo['relation'] = 'OR';
			if(count($terms)>1){
				  foreach($terms as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo[] = 
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $terms,
				  	);
			}
		}
		if(isset($webd_view_id) && $webd_view_id!='' && is_numeric($webd_view_id)){
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]= 
				 array(
					'key' => 'webd_webd_views',
					'value' => $webd_view_id,
					'compare' => 'LIKE'
			);
		}
		if(isset($_GET['location']) && $_GET['location']!='' && is_numeric($_GET['location'])){
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]= 
				 array(
					'key' => 'webd_default_venue',
					'value' => sanitize_text_field($_GET['location']),
					'compare' => '='
			);
		}
		if(isset($texo)){
			$args += array('tax_query' => $texo);
		}
		if(isset($_GET['ids']) && $_GET['ids']!=''){
			if(!is_array($_GET['ids'])){
				$ids = explode(",", sanitize_text_field($_GET['ids']));
			}
			$args['post__in'] = $ids;
		}
		if(isset($_GET['ex_ids']) && $_GET['ex_ids']!=''){
			if(!is_array($_GET['ex_ids'])){
				$ex_ids = explode(",", sanitize_text_field($_GET['ex_ids']));
			}
			$args['post__not_in'] = $ex_ids;
		}
		global $post;
		if(get_option('woocommerce_hide_out_of_stock_items')=='yes'){
			$args['meta_query'][] = array(
				'key'       => '_stock_status',
				'value'     => 'outofstock',
				'compare'   => 'NOT IN'
			);
		}
		$args = apply_filters( 'webd_calendar_query_var', $args );
		$the_query = get_posts( $args );
		$it = count($the_query);
		$rs=array();
		$show_bt =  isset($atts['show_bt']) ? $atts['show_bt'] :'';
		$current_url =  isset($_GET['current_url']) ? sanitize_text_field($_GET['current_url']) :'';
		if(!empty($the_query)){
			$date_format = get_option('date_format');
			$hour_format = get_option('time_format');
			$result = array();
			foreach ( $the_query as $post ) : setup_postdata( $post );
				if(get_post_type(get_the_ID()) == 'product_variation') {
					$variation = wc_get_product(get_the_ID());
					$parent_id = $variation->get_parent_id();
					if($variation->get_image_id()!='' ){
						$thub_id = $variation->get_image_id();
					}else{
						$thub_id = get_post_thumbnail_id($parent_id);
					}
				}else{ 
					$parent_id = get_the_ID();
					$thub_id = get_post_thumbnail_id($parent_id);
				}
				$image_src = wp_get_attachment_image_src( $thub_id,'thumb_150x160' );
				$image_src = apply_filters( 'webd_qtip_image_size', $image_src, get_post_thumbnail_id() );
				$webd_startdate_unix = get_post_meta(get_the_ID(),'webd_startdate', true );
				$webd_enddate_unix = get_post_meta(get_the_ID(),'webd_enddate', true );
				$all_day = get_post_meta(get_the_ID(),'webd_allday', true );
				if($webd_startdate_unix!=''){
				   // $startdate_cal = gmdate("Ymd\THis", $startdate);
					$webd_startdate = gmdate("Y-m-d\TH:i:s", $webd_startdate_unix);// convert date ux
				}
				if($webd_enddate_unix!=''){
					$webd_enddate = gmdate("Y-m-d\TH:i:s", $webd_enddate_unix);
					/*
					fix fullcalendar old version
					*/
					if($all_day==1){
						$webd_enddate = gmdate("Y-m-d\TH:i:s", $webd_enddate_unix*1 + 1); 
					}
				}
				if($all_day==1){
					$start_hourtime = $end_hourtime = '';
				}
				if($webd_startdate_unix!=''){
					$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
					if($all_day=='1'){ 
					  $h_st = '';
					  $h_e = $alltrsl;
					}else{ 
						$h_st = date_i18n( $hour_format, $webd_startdate_unix);  
						$h_e = date_i18n( $hour_format, $webd_enddate_unix);
					}
					if(date_i18n( $date_format, $webd_startdate_unix) == date_i18n( $date_format, $webd_enddate_unix)){
						if($all_day!='1'){ $h_e = ' - '.$h_e;}
						$dt_fm = date_i18n( $date_format, $webd_startdate_unix).' '.$h_st.$h_e;
						$edt_fm ='';
					}else{
						$dt_fm = date_i18n( $date_format, $webd_startdate_unix).' '.$h_st;
						if($webd_enddate_unix!=''){
							$edt_fm = date_i18n( $date_format, $webd_enddate_unix).' '.$h_e;
						}
					}
					global $product;	
					$type = $product->get_type();
					$price ='';
					if($type=='variable'){
						$price = webd_variable_price_html();
					}else{
						  if ( $price_html = $product->get_price_html() ) :
							  $price = $price_html; 
						  endif; 	
					}
					$webd_eventcolor = webd_event_custom_color($parent_id);
					if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
					$url_tt = $tbt = '';
					if($show_bt == 'addtocart'){
						$variations = '';
						$product = wc_get_product(get_the_ID());
						if($product!==false) { $variations = $product->get_type();}
						if($variations == 'variable'){
							
							$url_tt = get_permalink();
							$tbt = get_option('webd_text_sl_op')!='' ? get_option('webd_text_sl_op') : esc_html__('Select options','WEBDWooEVENT');
						}else{
							if(get_post_type(get_the_ID()) == 'product_variation') {
								$url_tt = add_query_arg( array('add-to-cart' => $parent_id,'variation_id' => get_the_ID()), get_permalink());
							}else{
								$url_tt = add_query_arg( array('add-to-cart' => get_the_ID()), get_permalink());
							}
							$tbt = get_option('webd_text_add_to_cart')!='' ? get_option('webd_text_add_to_cart') : esc_html__('Add to cart','WEBDWooEVENT');
							$tbt = apply_filters( 'webd_ctinfo_before_atc', $tbt, get_the_ID(), $parent_id,$url_tt);
							$url_tt = apply_filters( 'webd_qtip_link_atc', $url_tt, get_the_ID(), $parent_id);
						}
					}elseif($show_bt == 'details'){
						$url_tt = get_permalink();
						$tbt = get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');
					}
					$status_tk =  woo_event_status( get_the_ID(), $webd_enddate_unix);
					if(get_option('webd_dis_status') =='yes'){ $status_tk ='';}
					$sub_title =  webd_subtitle_html(get_the_ID(),true);
					if($all_day==1){
						$webd_startdate = gmdate("Y-m-d", $webd_startdate_unix);
						$webd_enddate = date('Y-m-d', ($webd_enddate_unix + 86400)) ;
					}
					$ar_rs= array(
						'id'=> get_the_ID(),
						'number'=> $it,
						'title'=> esc_attr(get_the_title()),
						'url'=> get_permalink(),
						'start'=>$webd_startdate,
						'end'=>$webd_enddate,
						'startdate'=> $dt_fm,
						'enddate'=> $edt_fm,
						'unix_startdate'=> $webd_startdate_unix,
						'unix_enddate'=> $webd_enddate_unix,
						'thumbnail' => $image_src[0],
						'price'=> $price,
						'color'=> $webd_eventcolor,
						'status'=>  $status_tk,
						'description'=> get_the_excerpt(),
						'location' => get_post_meta($parent_id,'webd_adress', true ),
						'allDay' => $all_day,
						'url_ontt'=> $url_tt,
						'text_onbt'=> $tbt,
						'sub_title'=> $sub_title,
						'evlabel'=> webd_event_label_html($parent_id,true),
					);
					if($product->is_featured()){
						$ar_rs['title'] = esc_attr(get_the_title()) .'<i class="fa fa-star"></i>';
					}
				}
				$result[]=$ar_rs;
			endforeach;
			$result = apply_filters( 'webd_event_json_info', $result);
			wp_reset_postdata();
		}
		echo str_replace('\/', '/', json_encode($result));
		exit;
	}
}
//
if(!function_exists('webd_social_share')){
	function webd_social_share( $id = false){
		$id = get_the_ID();
		$tl_share_button = array('fb','tw','li','tb','gg','pin','vk','em','wa');
		ob_start();
		if(is_array($tl_share_button) && !empty($tl_share_button)){
			?>
			<ul class="wooevent-social-share">
				<?php if(in_array('fb', $tl_share_button)){ ?>
					<li class="facebook">
						<a class="trasition-all" title="<?php esc_html_e('Share on Facebook','WEBDWooEVENT');?>" href="#" target="_blank" rel="nofollow" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+'<?php echo urlencode(get_permalink($id)); ?>','facebook-share-dialog','width=626,height=436');return false;"><i class="fa fa-facebook"></i>
						</a>
					</li>
				<?php }
	
				if(in_array('tw', $tl_share_button)){ ?>
					<li class="twitter">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Share on Twitter','WEBDWooEVENT');?>" rel="nofollow" target="_blank" onclick="window.open('http://twitter.com/share?text=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;url=<?php echo urlencode(get_permalink($id)); ?>','twitter-share-dialog','width=626,height=436');return false;"><i class="fa fa-twitter"></i>
						</a>
					</li>
				<?php }
	
				if(in_array('li', $tl_share_button)){ ?>
						<li class="linkedin">
							<a class="trasition-all" href="#" title="<?php esc_html_e('Share on LinkedIn','WEBDWooEVENT');?>" rel="nofollow" target="_blank" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(get_permalink($id)); ?>&amp;title=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;source=<?php echo urlencode(get_bloginfo('name')); ?>','linkedin-share-dialog','width=626,height=436');return false;"><i class="fa fa-linkedin"></i>
							</a>
						</li>
				<?php }
	
				if(in_array('tb', $tl_share_button)){ ?>
					<li class="tumblr">
					   <a class="trasition-all" href="#" title="<?php esc_html_e('Share on Tumblr','WEBDWooEVENT');?>" rel="nofollow" target="_blank" onclick="window.open('http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink($id)); ?>&amp;name=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>','tumblr-share-dialog','width=626,height=436');return false;"><i class="fa fa-tumblr"></i>
					   </a>
					</li>
				<?php }
	
				 if(in_array('pin', $tl_share_button)){ ?>
					 <li class="pinterest">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Pin this','WEBDWooEVENT');?>" rel="nofollow" target="_blank" onclick="window.open('//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($id)) ?>&amp;media=<?php echo urlencode(wp_get_attachment_url( get_post_thumbnail_id($id))); ?>&amp;description=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>','pin-share-dialog','width=626,height=436');return false;"><i class="fa fa-pinterest"></i>
						</a>
					 </li>
				 <?php }
				 
				 if(in_array('vk', $tl_share_button)){ ?>
					 <li class="vk">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Share on VK','WEBDWooEVENT');?>" rel="nofollow" target="_blank" onclick="window.open('//vkontakte.ru/share.php?url=<?php echo urlencode(get_permalink(get_the_ID())); ?>','vk-share-dialog','width=626,height=436');return false;"><i class="fa fa-vk"></i>
						</a>
					 </li>
				 <?php }
	
				 if(in_array('em', $tl_share_button)){ ?>
					<li class="email">
						<a class="trasition-all" href="mailto:?subject=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;body=<?php echo urlencode(get_permalink($id)) ?>" title="<?php esc_html_e('Email this','WEBDWooEVENT');?>"><i class="fa fa-envelope"></i>
						</a>
					</li>
				<?php }
                if(in_array('wa', $tl_share_button)){ ?>
					<li class="whatsapp">
						<a class="trasition-all" href="whatsapp://send?text=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?> - <?php echo urlencode(get_permalink($id)) ?>" data-action="share/whatsapp/share" title="<?php esc_html_e('Share via Whatsapp','WEBDWooEVENT');?>"><i class="fa fa-whatsapp"></i>
						</a>
					</li>
				<?php }?>
                
			</ul>
			<?php
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
// member social
if(!function_exists('webd_view_print_social_accounts')){
	function webd_view_print_social_accounts(){
		$accounts = array('facebook','instagram','envelope','twitter','linkedin','tumblr','pinterest','youtube','flickr','github','dribbble');
		
		$html ='';
		foreach($accounts as $account){
			$url = get_post_meta( get_the_ID(), $account, true );
			if($url){
				if($account == 'envelope'){
					$url = 'mailto:' . $url;
				}
				$html .= '<li class="'.$account.'"><a href="'.$url.'" title="'.$account.'"><i class="fa fa-'.$account.'"></i></a></li>';
			}
		}
		if($html !=''){
			$html ='<ul class="wooevent-social-share webd_view-social">'.$html.'</ul>';
		}
		return $html;
	}
}
//Global function
function wooevent_global_layout(){
	if(is_singular('product')){
		global $layout,$post;
		if(isset($layout) && $layout!=''){
			return $layout;
		}
		$layout = get_post_meta( $post->ID, 'webd_layout', true );
		if($layout ==''){
			$layout = get_option('webd_slayout');
		}
		return $layout;
		}
}
function webd_global_startdate(){
	global $webd_startdate, $post;
	if(isset($webd_startdate) && $webd_startdate!='' && is_main_query() && is_singular('product')){
		return $webd_startdate;
	}
	$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true ) ;
	return $webd_startdate;
}
function webd_global_enddate(){
	global $webd_enddate, $post;
	if(isset($webd_enddate) && $webd_enddate!='' && is_main_query() && is_singular('product')){
		return $webd_enddate;
	}
	$webd_enddate = get_post_meta( $post->ID, 'webd_enddate', true ) ;
	return $webd_enddate;
}
function webd_global_search_result_page(){
	global $webd_search_result;
	if(isset($webd_search_result)){
		return $webd_search_result;
	}
	$webd_search_result = get_option('webd_search_result') ;
	return $webd_search_result;
}
function webd_global_main_purpose(){
	global $webd_main_purpose;
	if(isset($webd_main_purpose) && $webd_main_purpose!=''){
		return $webd_main_purpose;
	}
	$webd_main_purpose = get_option('webd_main_purpose');
	return $webd_main_purpose;
}
function webd_global_default_spurpose(){
	global $webd_layout_purpose,$post;
	if(isset($webd_layout_purpose) && $webd_layout_purpose!=''){
		return $webd_layout_purpose;
	}
	$webd_layout_purpose = get_post_meta($post->ID,'webd_layout_purpose',true);
	if($webd_layout_purpose=='' || $webd_layout_purpose=='def'){
		if(webd_global_main_purpose() =='meta'){
			if(function_exists('webd_event_cat_custom_layout')){
				$webd_layout_purpose = webd_event_cat_custom_layout($post->ID);
				if($webd_layout_purpose!=''){
					return $webd_layout_purpose;
				}
			}
		}
		$webd_layout_purpose = get_option('webd_slayout_purpose','woo');
	}
	return $webd_layout_purpose;
}

//edit link recurrence
add_action( 'admin_bar_menu', 'toolbar_link_recurren_edit', 999 );

function toolbar_link_recurren_edit( $wp_admin_bar ) {
	if(is_singular('product')){
		global $post;
		$ex_recurr = get_post_meta($post->ID,'recurren_ext', true );
		$ex_recurr  = explode("_",$ex_recurr);
		if(isset($ex_recurr[1]) && $ex_recurr[1]!=''){
			$wp_admin_bar->remove_node( 'edit' );
			$args_e = array(
				'id'    => 'edit-single',
				'title' => 'Edit Single',
				'href'  => get_edit_post_link( $post->ID, true ),
				'meta'  => array( 'class' => 'single-edit' )
			);
			$wp_admin_bar->add_node( $args_e );
			$args = array(
				'id'    => 'recurren_edit',
				'title' => 'Edit All Recurrence',
				'href'  => get_edit_post_link( $ex_recurr[1], true ),
				'meta'  => array( 'class' => 'recurren-page' )
			);
			$wp_admin_bar->add_node( $args );
		}
	}
}
//barcode
add_action( 'wpo_wcpdf_after_order_details', 'webdwooevents_add_barcode', 10, 2 );
function webdwooevents_add_barcode ($template_type, $order) {
	$items = $order->get_items();
	$fev = 0;
	foreach ( $items as $item ) {
		if($item->get_meta('_startdate')!=''){
			$fev = 1;
			break;
		}
	}
	if($fev == 0){ return;}
	?>
        <?php
}
//Add info to pdf invoice
add_action( 'wpo_wcpdf_after_item_meta', 'webdwooevents_add_event_meta', 10, 3 );
function webdwooevents_add_event_meta ($template_type, $item, $order) {
	$webd_startdate = get_post_meta( $item['product_id'], 'webd_startdate', true ) ;
	$webd_enddate = get_post_meta( $item['product_id'], 'webd_enddate', true ) ;
	$webd_adress = get_post_meta( $item['product_id'], 'webd_adress', true ) ;
	$all_day = get_post_meta($item['product_id'],'webd_allday', true );
	$html ='';
	$stdatetrsl = get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') :  esc_html__('Start Date','WEBDWooEVENT');
	$edatetrsl = get_option('webd_text_edate')!='' ? get_option('webd_text_edate') : esc_html__('End Date','WEBDWooEVENT');
	$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
	if($all_day!='1' && $webd_startdate!=''){
		$html .='<dl class="meta">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate).'</dl>';
		if($webd_enddate!=''){
			$html .='<dl class="meta">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate).'</dl>';
		}
	}elseif($webd_startdate!=''){
		$html .='<dl class="meta">'.$stdatetrsl.': '.date_i18n( get_option('date_format'), $webd_startdate).'</dl>';
		if($webd_enddate!=''){
			$html .='<dl class="meta">'.$edatetrsl.': '.date_i18n( get_option('date_format'), $webd_enddate).' '.$alltrsl.'</dl>';
		}
	}
	if($webd_adress!=''){
	  $eaddtrsl = get_option('webd_text_addres')!='' ? get_option('webd_text_addres') : esc_html__('Address','WEBDWooEVENT');
	  $html .='<dl class="meta">'.$eaddtrsl.': '.$webd_adress.'</dl>';
	}
	// user info
	
	$order_items = $order->get_items();
	$n = 0; $find = 0;
	foreach ($order_items as $items_key => $items_value) {
		$n ++;
		if($items_value->get_id() == $item['item_id']){
			$find = 1;
			break;
		}
	}
	if($find == 0){ return;}
	$value_id = $item['product_id'].'_'.$n;
	$value_id = apply_filters( 'webd_attendee_key', $value_id, $item );
	
	$metadata = get_post_meta($order-> get_id(),'att_info-'.$value_id, true);
	if($metadata == ''){
		$metadata = get_post_meta($order->get_id(),'att_info-'.$item['product_id'], true);
	}
	if($metadata !=''){
		
		$t_atten = get_option('webd_text_attende_')!='' ? get_option('webd_text_attende_') : esc_html__('Attendees info','WEBDWooEVENT');
		$t_name = get_option('webd_text_name_')!='' ? get_option('webd_text_name_') : esc_html__('Name: ','WEBDWooEVENT');
		$t_email = get_option('webd_text_email_')!='' ? get_option('webd_text_email_') : esc_html__('Email: ','WEBDWooEVENT');
		
		$metadata = explode("][",$metadata);
		if(!empty($metadata)){
			$i=0;
			foreach($metadata as $item){
				$i++;
				$item = explode("||",$item);
				$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
				$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
				$html .= '<div class="webd-user-info">'.$t_atten.' ('.$i.') ';
				$html .=  $f_name!='' && $l_name!='' ? '<span style="margin-right:15px;"><b>'.$t_name.'</b>'.$f_name.' '.$l_name.'</span>' : '';
				$html .=  isset($item[0]) && $item[0]!='' ? '<span><b>'.$t_email.' </b>'.$item[0].'</span>' : '';
				$html .= '</div>';
			}
		}
	}
	
	
	echo $html;
}
//ver 1.1
add_action( 'wp_ajax_ex_loadmore_grid', 'ajax_ex_loadmore_grid' );
add_action( 'wp_ajax_nopriv_ex_loadmore_grid', 'ajax_ex_loadmore_grid' );

function ajax_ex_loadmore_grid(){
	global $columns,$number_excerpt,$show_time,$orderby,$img_size;
	$atts 		= json_decode( stripslashes( sanitize_text_field($_POST['param_shortcode']) ), true );
	$columns 	= $atts['columns']	=  isset($atts['columns']) ? $atts['columns'] : 1;
	$img_size 	=  isset($atts['img_size']) ? $atts['img_size'] :'wethumb_460x307';
	$show_time 	=  isset($atts['show_time']) ? $atts['show_time'] :'';
	$orderby 	=  isset($atts['orderby']) ? $atts['orderby'] :'';
	$count 		=  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$style 		=  isset($atts['style']) ? $atts['style'] :'';
	$page 		= sanitize_text_field($_POST['page']);
	$param_query = json_decode( stripslashes( sanitize_text_field($_POST['param_query']) ), true );
	$param_ids 	= '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	if($orderby =='rand' && is_array($param_ids)){
		$param_query['post__not_in'] = $param_ids;
		$param_query['paged'] = 1;
	}
	//echo '<pre>';
	//print_r($param_query);//exit;
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		?>
        <div class="grid-row de-active">
        <?php
		$i =0;
		$arr_ids = array();
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			$arr_ids[] = get_the_ID();
			if($style=='classic'){
				wooevent_template_plugin('grid-classic', true);
			}else{
				wooevent_template_plugin('grid', true);
			}
			if($i%$columns==0){?>
				</div>
				<div class="grid-row de-active">
				<?php
			}
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
		//echo esc_html(str_replace('\/', '/', json_encode($arr_ids)));exit;
		if($orderby =='rand' && is_array($param_ids)){
		?>
        <script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#<?php  echo esc_html__($_POST['id_crsc']);?> input[name=param_ids]').val(<?php echo str_replace('\/', '/', json_encode(array_merge($param_ids,$arr_ids)));?>);
		});
        </script>
        <?php 
		}?>
        </div>
        <?php
	}
	$html = ob_get_clean();
	echo  $html;
	die;
}
//table load
add_action( 'wp_ajax_ex_loadmore_table', 'ajax_ex_loadmore_table' );
add_action( 'wp_ajax_nopriv_ex_loadmore_table', 'ajax_ex_loadmore_table' );

function ajax_ex_loadmore_table(){
	global $style,$show_time,$show_atc,$show_thumb,$url_page;
	$url_page = $_POST['url_page']!='' ? sanitize_text_field($_POST['url_page']) :'';
	$atts 		= json_decode( stripslashes( sanitize_text_field($_POST['param_shortcode']) ), true );
	$style 		=  isset($atts['style']) ? $atts['style'] :'';
	$count 		=  isset($atts['count']) ? $atts['count'] :'6';
	$show_atc 	=  isset($atts['show_atc']) ? $atts['show_atc'] :'';
	$show_time 	=  isset($atts['show_time']) ? $atts['show_time'] :'';
	$show_thumb =  isset($atts['show_thumb']) ? $atts['show_thumb'] :'';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$page 	= sanitize_text_field($_POST['page']);
	$style 	=  isset($atts['style']) ? $atts['style'] :'';
	$param_query = json_decode( stripslashes( sanitize_text_field($_POST['param_query']) ), true );
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	global $ajax_load;
	if($the_query->have_posts()){
		while($the_query->have_posts()){ $the_query->the_post();
			$ajax_load =1;
			wooevent_template_plugin('table', true);
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
	}
	$html = ob_get_clean();
	echo  $html;
	die;
}
//webd_view load
add_action( 'wp_ajax_ex_loadmore_webd_view', 'ajax_ex_loadmore_webd_view' );
add_action( 'wp_ajax_nopriv_ex_loadmore_webd_view', 'ajax_ex_loadmore_webd_view' );
function ajax_ex_loadmore_webd_view(){
	global $img_size,$show_meta;
	$atts 		= json_decode( stripslashes( sanitize_text_field($_POST['param_shortcode']) ), true );
	$count 		=  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$page 		= sanitize_text_field($_POST['page']);
	$columns 	=  isset($atts['columns']) && $atts['columns']!='' ? $atts['columns'] :'3';
	if(!isset($atts['columns'])){ $atts['columns'] = $columns;}
	$param_query = json_decode( stripslashes( sanitize_text_field($_POST['param_query']) ), true );
	$img_size 	=  isset($atts['img_size']) ? $atts['img_size'] :'wethumb_460x307';
	$show_meta 	=  isset($atts['show_meta']) ? $atts['show_meta'] :'';
	$param_ids 	= '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	if($orderby =='rand' && is_array($param_ids)){
		$param_query['post__not_in'] = $param_ids;
		$param_query['paged'] = 1;
	}
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){ ?>
        <div class="grid-row de-active">
	        <?php
			$i =0;
			$arr_ids = array();
			while($the_query->have_posts()){ $the_query->the_post();
				$i++;
				$arr_ids[] = get_the_ID();
				wooevent_template_plugin('webd_viewsc', true);
				if($i%$columns==0){?>
					</div>
					<div class="grid-row de-active">
					<?php
				}
				if($end_it_nb!='' && $end_it_nb == $i){break;}
			}
			if($orderby =='rand' && is_array($param_ids)){?>
		        <script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#<?php  echo esc_html__($_POST['id_crsc']);?> input[name=param_ids]').val(<?php echo str_replace('\/', '/', json_encode(array_merge($param_ids,$arr_ids)));?>);
				});
		        </script>
		        <?php 
			}?>
        </div>
        <?php
	}
	$html = ob_get_clean();
	echo  $html;
	die;
}
// auto change color when low stock
if(!function_exists('webd_autochange_color')){
	function webd_autochange_color(){
		$color = '';
		$webd_auto_color = get_option('webd_auto_color');
		if($webd_auto_color=='on'){
			global $product,$post;
			$stock_status = get_post_meta($post->ID, '_stock_status',true);
			$_manage_stock = get_post_meta($post->ID, '_manage_stock',true);
			$numleft  = $product->get_stock_quantity();
			if($_manage_stock=='yes' && $numleft > 0){
				$stock_status = 'instock';
			}
			$type = $product->get_type();
			if($type=='variable' && $numleft < 1){
				$all_st = 'out';
				foreach ($product->get_available_variations() as $key_status) {
					if(get_post_meta( $key_status['variation_id'], '_stock_status', true ) !='outofstock'){
						$all_st = 'in';
					}
				}
				if($all_st == 'out'){ $stock_status ='outofstock';}
				else{ $stock_status = 'instock';}
			}
			if($stock_status !='outofstock') { 
				$total = get_post_meta($post->ID, 'total_sales', true);
				if($total >= $numleft && $numleft!=0){
					$color = '#FFEB3B';
				}elseif($numleft=='0'){
					//$color = '#cc0000';
				}
			}else{
				$color = '#cc0000';
			}
		}
		$color = apply_filters( 'webd_event_auto_color', $color, $webd_auto_color);
		return $color;
	}
}
if(!function_exists('webd_variable_price_html')){
	function webd_variable_price_html(){
		$fromtrsl = get_option('webd_text_from')!='' ? get_option('webd_text_from') : esc_html__('From  ','WEBDWooEVENT');
		global $product; 
		$price_html = wc_price($product->get_variation_price('min')).$product->get_price_suffix();
		return apply_filters( 'webd_variable_price_html', $fromtrsl.' '.$price_html, $product->get_variation_price('min'), $product,$fromtrsl);
	}
}

if(!function_exists('webd_hide_booking_form')){
	function webd_hide_booking_form(){
		$time_stops = get_post_meta(get_the_ID(),'webd_stop_booking', true );
		$hour = false;
		if (strpos($time_stops, 'h') !== false) {
			$hour = true;
			$time_stops = str_replace("h","",$time_stops);
		}
		if($time_stops =='' || $time_stops < 0 ){
			$time_stops = get_option('webd_stop_booking');
			if (strpos($time_stops, 'h') !== false) {
				$hour = true;
				$time_stops = str_replace("h","",$time_stops);
			}
		}
		$webd_startdate =get_post_meta( get_the_ID(), 'webd_startdate', true );
		if(is_numeric($time_stops)  && $webd_startdate !='' && $webd_startdate > 0){
			$time_now =  strtotime("now");
			$webd_time_zone = get_post_meta(get_the_ID(),'webd_time_zone',true);
			if($webd_time_zone!='' && $webd_time_zone!='def'){
				$webd_time_zone = $webd_time_zone * 60 * 60;
				$time_now = $webd_time_zone + $time_now;
			}
			$webd_main_purpose = webd_global_main_purpose();
			$webd_layout_purpose = webd_global_default_spurpose();
			if($hour == true){
				$time_stops = $webd_startdate - $time_stops*3600;
			}else{
				$time_stops = $webd_startdate - $time_stops*86400;
			}
			if(($time_now > $time_stops  && $webd_main_purpose=='event') || ($time_now > $time_stops  && $webd_layout_purpose=='event') || ($time_now > $time_stops  && $webd_layout_purpose=='custom')){
				return '
				<style type="text/css">.woocommerce div.product form.cart, .woocommerce div.product p.cart{ display:none !important}</style>';
			}
		}
		return;
	}
}
if(!function_exists('webd_update_total_sales')){
	add_action( 'woocommerce_order_status_cancelled', 'webd_update_total_sales' );
	function webd_update_total_sales($order_id) {
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			$total = get_post_meta( $product_id, 'total_sales', true );
			if($total !='' && $total > 0){
				$total = $total -1;
				update_post_meta( $product_id, 'total_sales', $total);
			}
		}
	}
}
add_action( 'wp_ajax_ex_loadevent_ofday', 'ajax_ex_loadevent_ofday' );
add_action( 'wp_ajax_nopriv_ex_loadevent_ofday', 'ajax_ex_loadevent_ofday' );
if(!function_exists('ajax_ex_loadevent_ofday')){
	function ajax_ex_loadevent_ofday(){
		$spe_day 	= sanitize_text_field($_POST['param_day']);
		$ids 		= sanitize_text_field($_POST['ids']);
		if($ids==''){ exit;}
		if(!is_array($ids)){
			$ids = explode(",", $ids);
		}
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => '-1',
			'post_status' => 'publish',
			'post__in' =>  $ids,
			'order' => 'ASC',
			'orderby' => 'meta_value_num',
			'meta_key' => 'webd_startdate',
			'ignore_sticky_posts' => 1,
		);
		$args =  apply_filters( 'webd_modern_cal_query_args', $args );
		echo webd_calendar_modern_data($args);
		exit;
	}
}
if(!function_exists('webd_calendar_modern_data')){
	function webd_calendar_modern_data($args) {
		ob_start();
		$the_query = new WP_Query( $args );
		$day_event = '';
		if($the_query->have_posts()){
			while($the_query->have_posts()){ $the_query->the_post();
				$webd_startdate = get_post_meta( get_the_ID(), 'webd_startdate', true );
				$webd_enddate = get_post_meta( get_the_ID(), 'webd_enddate', true )  ;
				global $product;	
				$type = $product->get_type();
				$price ='';
				if($type=='variable'){
					$price = webd_variable_price_html();
				}else{
					if ( $price_html = $product->get_price_html() ) :
						$price = $price_html; 
					endif; 	
				}
				$webd_adress = get_post_meta( get_the_ID(), 'webd_adress', true );
				$webd_status = woo_event_status( get_the_ID(), $webd_enddate);
				if(get_option('webd_dis_status') =='yes'){ $webd_status ='';}

				if(get_post_type(get_the_ID()) == 'product_variation') {
					$variation = wc_get_product(get_the_ID());
					$parent_id = $variation->get_parent_id();
				}else{ $parent_id = get_the_ID();}
				
				$webd_eventcolor = webd_event_custom_color($parent_id);;
				if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
				$bgev_color = '';
				if($webd_eventcolor!=""){
					$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
				}
				?>
                
                <div class="day-event-details">
                	<?php 
					if(has_post_thumbnail(get_the_ID())){?>
                    <div class="day-ev-image">
                    	<a href="<?php the_permalink(); ?>" class="link-more">
							<?php the_post_thumbnail('wethumb_85x85');
							echo '<span class="bg-overlay"></span>';
							if($price!=''){
								echo '<span class="item-evprice" '.$bgev_color.'>'.$price.'</span>';
							}
							?>
                        </a>
                    </div>
                    <?php }?>
                    <div class="day-ev-des">
                        <h3><a href="<?php the_permalink(); ?>" class="link-more">
                            <?php  the_title();?>
                        </a></h3>
                        <div class="webd-more-meta">
                        <?php
                            if($webd_startdate!=''){
                                $sttime = '<span> - '.date_i18n(get_option('time_format'), $webd_startdate).'</span>';
                                echo '<span class="st-date"><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
                            }
                            if($webd_status!=''){
                                echo '
                                <span>
                                    <i class="fa fa-ticket"></i>
                                    '.$webd_status.'
                                </span>';
                            }
                        ?>
                        </div>
                        <div class="ev-excerpt"><?php echo get_the_excerpt();?></div>
                    </div>
                </div>
                <?php
			}
		}else{
			$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
			echo '<span class="day-event-details">'.$noftrsl.'</span>';
		}
		wp_reset_postdata();
		$day_event = ob_get_contents();
		ob_end_clean();
		return $day_event;
	}
}
add_action('woocommerce_new_order_item','webd_add_info_to_order_item_meta',10,2);
if(!function_exists('webd_add_info_to_order_item_meta')){
	function webd_add_info_to_order_item_meta($item_id, $item)
	{
		if(is_admin()){ return;}
		$values = $item->legacy_values;
		$_ev_date = get_post_meta( $values['product_id'], 'webd_startdate', true );
		if($_ev_date!='')
		{
			$_ev_date = date_i18n( get_option('date_format'), $_ev_date).' - '.date_i18n(get_option('time_format'), $_ev_date);
			wc_add_order_item_meta($item_id,'_startdate',$_ev_date);
		}
		$_ev_edate = get_post_meta( $values['product_id'], 'webd_enddate', true );
		if($_ev_edate!='')
		{
			$_ev_edate = date_i18n( get_option('date_format'), $_ev_edate).' - '.date_i18n(get_option('time_format'), $_ev_edate);
			wc_add_order_item_meta($item_id,'_enddate',$_ev_edate);
		}
		
	}
}
if(!function_exists('webd_event_custom_color')){
	function webd_event_custom_color($id){
		if($id==''){
			return;	
		}
		$webd_eventcolor = get_post_meta( $id, 'webd_eventcolor', true );
		$webd_cat_color = get_option('webd_cat_ctcolor');
		if($webd_eventcolor=='' && $webd_cat_color=='on'){
			$args = array(
				'hide_empty'        => true, 
			);
			$terms = wp_get_post_terms($id, 'product_cat', $args);
			if(!empty($terms) && !is_wp_error( $terms )){
				foreach ( $terms as $term ) {
					$webd_eventcolor = get_option('webd_category_color_' . $term->term_id);
					$webd_eventcolor = str_replace("#", "", $webd_eventcolor);
					if($webd_eventcolor!=''){
						$webd_eventcolor = '#'.$webd_eventcolor;
						break;
					}
				}
			}
		}
		$webd_eventcolor = apply_filters( 'webd_event_customcolor', $webd_eventcolor,$id );
		return $webd_eventcolor;
	}
}
if(!function_exists('webd_taxonomy_info')){
	function webd_taxonomy_info( $tax, $link=false, $id= false){
		if(isset($id) && $id!=''){
			$product_id = $id;
		}else{
			$product_id = get_the_ID();
		}
		$post_type = 'product';
		ob_start();
		if(isset($tax) && $tax!=''){
			$args = array(
				'hide_empty'        => false, 
			);
			$terms = wp_get_post_terms($product_id, $tax, $args);
			if(!empty($terms) && !is_wp_error( $terms )){
				$c_tax = count($terms);
				$i=0;
				foreach ( $terms as $term ) {
					$i++;
					if(isset($link) && $link=='off'){
						echo $term->name;
					}else{
						echo '<a href="'.get_term_link( $term ).'" title="' . $term->name . '">'. $term->name .'</a>';
					}
					if($i != $c_tax){ echo '<span>, </span>';}
				}
			}
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
if(!function_exists('webd_ical_google_button')){
	function webd_ical_google_button( $id ){
		if(isset($id) && $id!=''){
			$product_id = $id;
		}else{
			$product_id = get_the_ID();
		}
		$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true ) ;
		if($webd_startdate==''){ return;}
		$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true ) ;
		$excerpt = get_post_field('post_excerpt', $product_id);
		if($excerpt !=''){
			$excerpt = apply_filters('the_excerpt', $excerpt);
		}
		$title = urlencode(html_entity_decode(get_the_title($product_id), ENT_COMPAT, 'UTF-8'));
		$all_day = get_post_meta($product_id,'webd_allday', true );
		$webd_time_zone = get_post_meta($product_id,'webd_time_zone', true );
		if($webd_time_zone=='' || $webd_time_zone=='def'){
			$gmt_offset = get_option('gmt_offset');
			$webd_time_zone = $gmt_offset;
		}
		$webd_time_zone = $webd_time_zone * 3600;
		$tz = timezone_name_from_abbr('', $webd_time_zone, 0);
		if( $tz ==''){ 
			$tz = timezone_name_from_abbr('', ($webd_time_zone + 1800), 0);
		}
		if($tz!=''){ $tz = '&ctz='.urlencode($tz);}
		if($all_day!='1'){
			$st = gmdate("Ymd\THis", $webd_startdate);
			if($webd_enddate!=''){
				$en = gmdate("Ymd\THis", $webd_enddate);
			}else{$en =$st;}
		}else{
			$st = gmdate("Ymd", $webd_startdate);
			if($webd_enddate!=''){
				$webd_enddate = $webd_enddate + 86400;
				$en = gmdate("Ymd", $webd_enddate);
			}else{$en =$st;}
		}
		?>
        <div class="webd-icl-import col-md-12">
            <div class="row">
                    <div class="btn btn-primary"><a href="<?php echo home_url().'?ical_product='.$product_id; ?>"><?php echo get_option('webd_text_ical')!='' ? get_option('webd_text_ical') : esc_html__('+ Ical Import','WEBDWooEVENT');?></a></div>

                    <div class="btn btn-primary"><a href="https://www.google.com/calendar/render?dates=<?php  echo $st;?>/<?php echo $en; echo $tz;?>&action=TEMPLATE&text=<?php echo $title;?>&location=<?php echo esc_attr(urlencode(get_post_meta($product_id,'webd_adress', true )));?>&details=<?php echo esc_attr(urlencode( strip_tags($excerpt) ) );?>"><?php echo get_option('webd_text_ggcal')!='' ? get_option('webd_text_ggcal') : esc_html__('+ Google calendar','WEBDWooEVENT');?></a></div>
            </div>
        </div>
        <?php
	}
}
if(!function_exists('webd_ical_google_button_inorder')){
	add_action( 'woocommerce_order_item_meta_end', 'webd_ical_google_button_inorder', 19, 3 );
	function webd_ical_google_button_inorder($item_id, $item, $order){
		$id = $item['product_id'];
		webd_ical_google_button( $id );
	}
}
if(!function_exists('webd_show_custom_meta_inorder')){
	add_action( 'woocommerce_order_item_meta_end', 'webd_show_custom_meta_inorder', 9, 3 );
	function webd_show_custom_meta_inorder($item_id, $item, $order){
		$id = $item['product_id'];
		$webd_startdate = get_post_meta( $id, 'webd_startdate', true ) ;
		$webd_enddate = get_post_meta( $id, 'webd_enddate', true ) ;
		$lbst = get_option('webd_text_start')!='' ? get_option('webd_text_start') : esc_html__('Start','WEBDWooEVENT');
		$lbe = get_option('webd_text_end')!='' ? get_option('webd_text_end') : esc_html__('End','WEBDWooEVENT');
		$all_day = get_post_meta($id,'webd_allday', true );
		if($webd_startdate!=''){
			$st_html = date_i18n( get_option('date_format'), $webd_startdate).' ';
            if(($webd_enddate=='') || ($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate)))){ 
            	$st_html .= date_i18n(get_option('time_format'), $webd_startdate);
           	}
			
			echo '<br><span><strong>'.$lbst.':</strong> '.$st_html.'</span><br>';
		}
		if($webd_enddate!=''){
			$en_html = date_i18n( get_option('date_format'), $webd_enddate);
			if($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate))){ 
				$en_html .= ' '.date_i18n(get_option('time_format'), $webd_enddate);
			}elseif($all_day=='1'){ 
				$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
				$en_html .= ' '.$alltrsl;
			}
			
			
			echo '<span><strong>'.$lbe.':</strong> '.$en_html.'</span><br>';
		}
	}
}
/* Search hook*/
add_action( 'pre_get_posts','webd_event_search_hook_change',101 );
if (!function_exists('webd_event_search_hook_change')) {
	function webd_event_search_hook_change($query) {
		if( is_search() && $query->is_main_query() && is_shop() && !is_admin() ){
			if( isset($_GET['orderby']) && $_GET['orderby']!='' ){
				$cure_time =  strtotime("now");
				$query->set('meta_key', 'webd_startdate');
				$query->set('meta_value', $cure_time);
				if(sanitize_text_field($_GET['orderby'])=='upcoming'){
					$query->set('meta_compare', '>');
				}
				if(sanitize_text_field($_GET['orderby'])=='past'){
					$query->set('meta_compare', '<');
				}
			}
			if( isset($_GET['location']) && $_GET['location']!='' ){
				$meta_query_args['relation'] = 'AND';
				$meta_query_args = array(
					array(
					  'key' => 'webd_default_venue',
					  'value' => array (sanitize_text_field($_GET['location'])),
					  'compare' => 'IN',
					),
				);
			}
			if( isset($_GET['evyear']) && $_GET['evyear']!='' ){
				$start = strtotime('first day of January '.sanitize_text_field($_GET['evyear']) );
				$end = strtotime('last day of December '.sanitize_text_field($_GET['evyear']) ) + 86399;
				if(isset($_GET['month_up']) && $_GET['month_up']!=''){
					$cr_m = date("m");
					$m = webd_convert_month_to_text($_GET['month_up']);
					if($cr_m < sanitize_text_field($_GET['month_up']) || $cr_m > sanitize_text_field($_GET['month_up'])){
						$y = sanitize_text_field($_GET['evyear']);
						if($cr_m < sanitize_text_field($_GET['month_up'])){
							$y = sanitize_text_field($_GET['evyear']) + 1;
						}
						$start = strtotime('first day of '.$m.' '.$y );
						$end = strtotime('last day of '.$m.' '.$y ) + 86399;
					}elseif($cr_m == sanitize_text_field($_GET['month_up'])){
						$cure_time =  strtotime("now");
						$gmt_offset = get_option('gmt_offset');
						if($gmt_offset!=''){
							$cure_time = $cure_time + ($gmt_offset*3600);
						}
						$start = $cure_time;
						$end = strtotime('last day of '.$m.' '.sanitize_text_field($_GET['evyear']) ) + 86399;
					}else{ echo 'error';exit;}
				}elseif(isset($_GET['month']) && $_GET['month']!=''){
					$m = webd_convert_month_to_text(sanitize_text_field($_GET['month']));
					$start = strtotime('first day of '.$m.' '.sanitize_text_field($_GET['evyear']) );
					$end = strtotime('last day of '.$m.' '.sanitize_text_field($_GET['evyear']) ) + 86399;
				}
				$meta_query_args [] =
					array('key'  => 'webd_startdate',
						 'value' => $start,
						 'compare' => '>');
				$meta_query_args [] =		 
					array('key'  => 'webd_startdate',
						 'value' => $end,
						 'compare' => '<='
				);
			}else if((isset($_GET['month_up']) && $_GET['month_up']!='') || (isset($_GET['month']) && $_GET['month']!='')){
				if(isset($_GET['month_up']) && $_GET['month_up']!=''){
					$cr_m = date("m");
					$m = webd_convert_month_to_text($_GET['month_up']);
					if($cr_m < sanitize_text_field($_GET['month_up']) || $cr_m > sanitize_text_field($_GET['month_up'])){
						$y = sanitize_text_field($_GET['evyear']);
						if($cr_m < sanitize_text_field($_GET['month_up'])){
							$y = sanitize_text_field($_GET['evyear']) + 1;
						}
						$start = strtotime('first day of '.$m.' '.$y );
						$end = strtotime('last day of '.$m.' '.$y ) + 86399;
					}elseif($cr_m == sanitize_text_field($_GET['month_up'])){
						$cure_time =  strtotime("now");
						$gmt_offset = get_option('gmt_offset');
						if($gmt_offset!=''){
							$cure_time = $cure_time + ($gmt_offset*3600);
						}
						$start = $cure_time;
						$end = strtotime('last day of '.$m.' '.sanitize_text_field($_GET['evyear']) ) + 86399;
					}else{ echo 'error';exit;}
				}elseif(isset($_GET['month']) && $_GET['month']!=''){
					$m = webd_convert_month_to_text($_GET['month']);
					$start = strtotime('first day of '.$m.' '.sanitize_text_field($_GET['evyear']) );
					$end = strtotime('last day of '.$m.' '.sanitize_text_field($_GET['evyear']) ) + 86399;
				}
				$meta_query_args [] =
					array('key'  => 'webd_startdate',
						 'value' => $start,
						 'compare' => '>');
				$meta_query_args [] =		 
					array('key'  => 'webd_startdate',
						 'value' => $end,
						 'compare' => '<='
				);
			}else if( isset($_GET['sm']) && sanitize_text_field($_GET['sm'])=='event' ){
				$meta_query_args [] =
					array('key'  => 'webd_startdate',
						 'value' => 0,
						 'compare' => '>');
			}
			if(isset($meta_query_args)){
				$query->set('meta_query', $meta_query_args);
			}
		}
		//return $query;
	}
}
if(!function_exists('webd_convert_month_to_text')){
	function webd_convert_month_to_text($month){
		if($month=='01'){
			$m = 'January';
		}else if($month=='02'){
			$m = 'February';
		}else if($month=='03'){
			$m = 'March';
		}else if($month=='04'){
			$m = 'April';
		}else if($month=='05'){
			$m = 'May';
		}else if($month=='06'){
			$m = 'June';
		}else if($month=='07'){
			$m = 'July';
		}else if($month=='08'){
			$m = 'August';
		}else if($month=='09'){
			$m = 'September';
		}else if($month=='10'){
			$m = 'October';
		}else if($month=='11'){
			$m = 'November';
		}else if($month=='12'){
			$m = 'December';
		}else{ echo 'error';exit;}
		return $m;
	}
}
add_filter( 'the_content', 'webd_venues_the_content', 20 );
function webd_venues_the_content($content){
	if ( is_singular('webd_venue') ){
		$content = $content.'[webd_grid style="classic" count="999" posts_per_page="6" columns="3" meta_key="webd_default_venue" meta_value="'.get_the_ID().'"]';
	}
	return do_shortcode($content);
}

if(!function_exists('webd_login_if_sc')){
	function webd_login_if_sc( $atts,$content ) {
		$mess =  isset($atts['message']) ? $atts['message'] : esc_html__('Please login to submit event','webd');
		$login_url =  isset($atts['login_url']) ? $atts['login_url'] : '';
		ob_start();
		if(is_user_logged_in()){
			echo $content;
		}else{
			if($login_url!=''){
				echo '<p class="webd-log-requied"><a href="'.esc_url($login_url).'">'.$mess.'</a></p>';
			}else{
				echo '<p class="webd-log-requied">'.$mess.'</p>';
			}
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
	add_shortcode( 'webd_lgrequied', 'webd_login_if_sc' );
}

// Support schema
if(!function_exists('webd_google_event_schema')){
	function webd_google_event_schema() {
		if(get_option('webd_schema')!='yes'){
			return;
		}
		if(is_singular('product')){
			global $post;
			$_product = wc_get_product ($post->ID);
			$type = $_product->get_type();
			if($type=='variable'){
				$price = $_product->get_variation_price('min');
			}else{
				$price = $_product->get_price();
			}
			$webd_startdate = get_post_meta( $post->ID, 'webd_startdate', true ) ;
			if($webd_startdate==''){ return;}
			$webd_enddate = get_post_meta( $post->ID, 'webd_enddate', true ) ;
			$excerpt = get_post_field('post_excerpt', $post->ID);
			if($excerpt !=''){
				$excerpt = apply_filters('the_excerpt', $excerpt);
			}
			$all_day = get_post_meta($post->ID,'webd_allday', true );
			if($all_day!='1'){
				$st = gmdate("Y-m-d\THis", $webd_startdate);
				if($webd_enddate!=''){
					$en = gmdate("Y-m-d\THis", $webd_enddate);
				}else{$en =$st;}
			}else{
				$st = gmdate("Ymd", $webd_startdate);
				if($webd_enddate!=''){
					$webd_enddate = $webd_enddate + 86400;
					$en = gmdate("Ymd", $webd_enddate);
				}else{$en =$st;}
			}
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'full' );
			$image_src = isset($image_src[0]) && $image_src[0]!='' ? $image_src[0] : '';
			?>
			<script type="application/ld+json">
            {
				"@context": "http://schema.org",
				"@type": "Event",
				"name": "<?php echo esc_attr(get_the_title($post->ID));?>",
				"startDate": "<?php echo esc_attr($st);?>",
				"location": {
				  "@type": "Place",
				  "name": "<?php echo esc_attr(get_the_title(get_post_meta( $post->ID, 'webd_default_venue', true )));?>",
				  "address": "<?php echo esc_attr(get_post_meta( $post->ID, 'webd_adress', true ));?>"
				},
				"image": [
				  "<?php echo esc_url($image_src); ?>"
				 ],
				"description": "<?php echo esc_attr($excerpt);?>",
				"offers": {
				  "@type": "Offer",
				  "url": "<?php echo esc_attr(get_permalink($post->ID));?>",
				  "validFrom": "<?php echo get_the_date($time = $post->post_date);?>",
				  "price": "<?php echo esc_attr($price);?>",
				  "priceCurrency": "<?php echo esc_attr(get_woocommerce_currency()); ?>",
				  <?php 
				  $stock_status = get_post_meta($post->ID, '_stock_status',true);
				  if($stock_status !='outofstock') {
					  echo '"availability": "http://schema.org/SoldOut"';
				  }else{
				  	  echo '"availability": "http://schema.org/InStock"';	
				  }?>
				},
				<?php
				$webd_webd_views = get_post_meta( $post->ID, 'webd_webd_views', true );
				if(!is_array($webd_webd_views) && $webd_webd_views!=''){
					$webd_webd_views = explode(",",$webd_webd_views);
				}
				if(is_array($webd_webd_views)){
					?>
					"performer": <?php if(count($webd_webd_views) >1){?>[<?php }?>
					<?php
					$i = 0;
					foreach($webd_webd_views as $webd_view){
						$i ++;
						$spk_img = wp_get_attachment_image_src( get_post_thumbnail_id($webd_view),'full' );
						$spk_img = isset($spk_img[0]) && $spk_img[0]!='' ? $spk_img[0] : '';?>
						{
						  "@type": "Person",
						  "image": "<?php echo esc_url($spk_img);?>",
						  "name": "<?php echo esc_attr(get_the_title($webd_view));?>"
						}<?php if($i!=count($webd_webd_views) || count($webd_webd_views)==1 ){ echo ',';}
					}
					if(count($webd_webd_views) >1){?>],<?php }
				}?>
				"endDate": "<?php echo esc_attr($en);?>"
            }
            </script>
			<?php
		}
	}
	add_action('wp_head', 'webd_google_event_schema');
}
if(!function_exists('webd_if_product_isevent')){
	function webd_if_product_isevent($id){
		$webd_mpurpose = get_option('webd_main_purpose');
		$webd_glayout = get_option('webd_slayout_purpose');
		$webd_slayout = get_post_meta($id,'webd_layout_purpose',true);
		if($webd_mpurpose =='meta'){
			if($webd_glayout !='event' && $webd_slayout =='event' || $webd_glayout =='event' && $webd_slayout !='woo'){
				return true;
			}
		}else if($webd_mpurpose =='custom'){
			if($webd_slayout =='event'){
				return true;
			}
		}else if($webd_mpurpose =='' || $webd_mpurpose =='event'){
			return true;
		}
		return false;
	}
}

if(!function_exists('webd_calendar_month_select')){
	function webd_calendar_month_select($cat_include,$tag_include,$location_include,$spk_include){?>
        <div class="webd-calendar-filter">
            <div class="webd-cal-filter-month">
                <select name="cal-filter-month" class="webd-mft-select">
                    <option value=""><?php echo esc_html__('Months','WEBDWooEVENT');?></option>
					<?php 
                    $currentMonth = (int)date('m');
            
                    for ($x = $currentMonth; $x < $currentMonth + 12; $x++) {
                        $date = date_i18n('F j, Y', mktime(0, 0, 0, $x, 1));
                        $value = date('Y-m-d', mktime(0, 0, 0, $x, 1));
                        $selected = '';
                        if((isset($_GET['month']) && sanitize_text_field($_GET['month']) ==$value)){
                            $selected ='selected';
                        }
                        echo '<option value="'. $value .'" '.$selected.'>'. $date .'</option>';
                    }?>
                </select>
            </div>
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
                    <div class="webd-cal-filter-cat">
                        <select name="product_cat">
                            <option value=""><?php echo get_option('webd_text_evcat')!='' ? get_option('webd_text_evcat') : esc_html__('All Categories','WEBDWooEVENT');?></option>
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
					<div class="webd-filter-tag">
                        <select name="product_tag">
                            <option value=""><?php echo get_option('webd_text_evtag')!='' ? get_option('webd_text_evtag') : esc_html__('Tags','WEBDWooEVENT');?></option>
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
			if($location_include!='hide'){
				if($location_include!=''){
					$ids = explode(",", $location_include);
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
					<div class="webd-filter-loc">
                        <select name="product_loc">
                            <option value=""><?php echo get_option('webd_text_loca')!='' ? get_option('webd_text_loca') : esc_html__('Locations','WEBDWooEVENT');?></option>
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
			if($spk_include!='hide'){
				if($spk_include!=''){
					$ids = explode(",", $spk_include);
				}else{ $ids = '';}
				$args = array(
					'post_type' => 'event-webd-view',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'post__in' =>  $ids,
					'ignore_sticky_posts' => 1,
				);
				$the_query = new WP_Query( $args );
				if($the_query->have_posts()){ ?>
					<div class="webd-filter-webd_view">
                        <select name="product_spk">
                            <option value=""><?php echo get_option('webd_text_webd_view')!='' ? get_option('webd_text_webd_view') : esc_html__('Speakers','WEBDWooEVENT');?></option>
                            <?php 
								while($the_query->have_posts()){ $the_query->the_post();
								  $selected = '';
								  if((isset($_GET['webd_view']) && sanitize_text_field($_GET['webd_view']) == get_the_ID())){
									  $selected ='selected';
								  }
								  echo '<option value="'. get_the_ID() .'" '.$selected.'>'. get_the_title() .'</option>';
                              }?>
                        </select>
					</div>
				<?php }
				wp_reset_postdata();
			}
			?>
        </div>
        <div class="clearfix"></div>
        <?php
	}
}
// subtitle html
if(!function_exists('webd_subtitle_html')){
	function webd_subtitle_html($id=false,$return=false){
		if( get_option('webd_enable_subtitle') != 'yes' ){ return '';}
		if(isset($id) && is_numeric($id)){}else{ $id = get_the_ID();}
		$subtitle = get_post_meta($id,'webd_subtitle',true);
		if($subtitle ==''){ return '';}
        $html ='<div class="webd-subtitle">
            <span>'.$subtitle.'</span>
        </div>';
		if(isset($return) && $return==true){
			return $html;
		}else{
			echo $html;
		}
			
		
	}
}
if(!function_exists('webd_get_number_post_by_meta')){
	function webd_get_number_post_by_meta($id){
		global $wpdb;
		$query = $wpdb->get_results($wpdb->prepare(
			"
			SELECT * 
			FROM {$wpdb->prefix}postmeta 
			WHERE meta_key = %s
			AND meta_value = %f
			",
			'webd_default_venue',
			$id
		));
		return count($query);
	}
}
if(!function_exists('wpext_pagenavi')){
	function wpext_pagenavi($the_query,$idsc){
		if(function_exists('paginate_links')) {
			echo '<div class="webd-ajax-pagination" data-id="'.$idsc.'" id="pag-'.rand(10,9999).'">';
			$args = array(
				'base'         => home_url( '/%_%' ),
				'format'       => '?paged=%#%',
				'add_args'     => '',
				'show_all'     => false,
				'current' => isset($_POST['page']) && sanitize_text_field($_POST['page'])!='' ? sanitize_text_field($_POST['page']) : max( 1, get_query_var('paged') ),
				'total' => $the_query->max_num_pages,
				'prev_text'    => '&larr;',
				'next_text'    => '&rarr;',
				'type'         => 'list',
				'end_size'     => 2,
				'mid_size'     => 2
			);
			$args['add_args'] = array(
				'post_type' => 'product',
				's' => isset($_POST['key_word']) ? sanitize_text_field($_POST['key_word']) : '',
				'product_cat' => sanitize_text_field($_POST['cat']),
				'product_tag' => sanitize_text_field($_POST['tag']),
				'evyear' => sanitize_text_field($_POST['year']),
				'location' => sanitize_text_field($_POST['location'])
			);
			echo paginate_links($args);
		}
	}
}
//ajax search shortcode new
add_action( 'wp_ajax_webd_ajax_search', 'webd_ajax_search_result' );
add_action( 'wp_ajax_nopriv_webd_ajax_search', 'webd_ajax_search_result' );
if(!function_exists('webd_ajax_search_result')){
	function webd_ajax_search_result(){
		$page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : '';
		global $posts_per_page,$count,$layout,$idsc;
		$idsc 	= isset($_POST['idsc']) ? $_POST['idsc'] : '';
		$layout = isset($_POST['layout']) ? $_POST['layout'] : '';
		$posts_per_page = get_option( 'posts_per_page' )!='' ? get_option( 'posts_per_page' ) : 3;
		$count 	= 999;
		$args 	= array(
			'post_type' => 'product',
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			's' => $_POST['key_word'],
			'ignore_sticky_posts' => 1,
		);
		$args['paged'] = $page;
		$cat = isset($_POST['cat']) && $_POST['cat']!='' ? sanitize_text_field($_POST['cat']) : '';
		$tag = isset($_POST['tag']) && $_POST['tag']!='' ? sanitize_text_field($_POST['tag']) : '';
		$year = isset($_POST['year']) && $_POST['year']!='' ? sanitize_text_field($_POST['year']) : '';
		if($year!=''){
			$year = explode(",",$year);
			$year = array_filter($year);
			sort($year);
			if(count($year)>1){
				$start = mktime(0, 0, 0, 1, 1, $year[0]);
				$end = mktime(0, 0, 0, 12, 31, end($year));
				$args ['meta_query']=  array( 
				   'relation' => 'AND',
					array('key'  => 'webd_startdate',
						 'value' => $start,
						 'compare' => '>'),
					array('key'  => 'webd_startdate',
						 'value' => $end,
						 'compare' => '<=')
				);
			}else if(!empty($year)){
				$year = date($year[0]);
				$start = mktime(0, 0, 0, 1, 1, $year);
				$end = mktime(0, 0, 0, 12, 31, $year);
				$args ['meta_query']=  array( 
				   'relation' => 'AND',
					array('key'  => 'webd_startdate',
						 'value' => $start,
						 'compare' => '>'),
					array('key'  => 'webd_startdate',
						 'value' => $end,
						 'compare' => '<=')
				);
			}
		}
		if($tag!=''){
			$texo['relation'] = 'AND';
			$tags = explode(",",$tag);
			if(is_numeric($tags[0])){$field_tag = 'term_id'; }
			else{ $field_tag = 'slug'; }
			if(count($tags)>1){
				  foreach($tags as $iterm) {
					  if($iterm!=''){
					  $texo[] = array(
							  'taxonomy' => 'product_tag',
							  'field' => $field_tag,
							  'terms' => $iterm,
						  );
					  }
				  }
			  }else{
				  if(!empty($tags)){
				  $texo[] = array(
						  'taxonomy' => 'product_tag',
						  'field' => $field_tag,
						  'terms' => $tags,
				  );
				  }
			}
		}
		if($cat!=''){
			$texo['relation'] = 'AND';
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(count($cats)>1){
				  foreach($cats as $iterm) {
					  if($iterm!=''){
					  $texo[] = array(
							  'taxonomy' => 'product_cat',
							  'field' => $field,
							  'terms' => $iterm,
						  );
					  }
				  }
			  }else{
				  if(!empty($cats)){
					  $texo[] = array(
								  'taxonomy' => 'product_cat',
								  'field' => $field,
								  'terms' => $cats,
					  );
				  }
			}
		}
		if( isset($_POST['location']) && $_POST['location']!='' ){
			$args ['meta_query'][]= 
				array(
				  'key' => 'webd_default_venue',
				  'value' => array ($_POST['location']),
				  'compare' => 'IN',
			);
		}
		if(isset($texo)){
			$args += array('tax_query' => $texo);
		}
		$args = apply_filters( 'webd_ajax_search_arg', $args );
		global $the_query;
		$the_query = new WP_Query( $args );
		$it = $the_query->post_count;
		ob_start();
		wooevent_template_plugin('search-ajax', true);
		$html = ob_get_clean();
		ob_end_clean();
		echo  $html;
		die;
	}
}
if(!function_exists('webd_badget_html')){
	function webd_badget_html (){
		global $product;
		$html = '';
		if ( $product->is_on_sale() ) {
			$html = '<span class="woocommerce-webd-onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>';
		}else if( $product->is_featured() ) {
			$html = '<span class="woocommerce-webd-onsale webd-featured">' . esc_html__( 'Featured', 'WEBDWooEVENT' ) . '</span>';
		}else {
			if(function_exists('wc_get_rating_html')){
				$rating_html = wc_get_rating_html($product->get_average_rating());
			}else{
				$rating_html = $product->get_rating_html();
			}
			if ( get_option( 'woocommerce_enable_review_rating' ) != 'no' && $rating_html!=''){
					$html = '<div class="woocommerce-webd-onsale woocommerce">'.$rating_html.'</div>';
			}
		}
		return apply_filters( 'webd_badget_html', $html, $product );
	}
}
// Live total in Simple or variable product
add_action( 'woocommerce_before_add_to_cart_quantity', 'webd_update_live_total_price_html', 32 );
function webd_update_live_total_price_html() {
	global $product_type;
	if(get_option('webd_enable_livetotal')=='yes' && $product_type!='grouped'){
		wooevent_template_plugin('live-total');
	}
}
// Live total in Group product
add_action( 'woocommerce_before_add_to_cart_form', 'webd_global_product_type', 32 );
function webd_global_product_type() {
	global $product_type,$product;
	$product_type = $product->get_type();;
}
add_action( 'woocommerce_before_add_to_cart_button', 'webd_group_live_total_price_html', 32 );
function webd_group_live_total_price_html() {
	global $product_type;
	if(get_option('webd_enable_livetotal')=='yes' && $product_type=='grouped'){
		wooevent_template_plugin('live-total');
	}
}
/*------------ support Advanced Order Export --------------*/
// start date
add_filter('woe_get_order_product_fields', 'webd_ev_startdate', 10, 2);
function webd_ev_startdate($fields,$format) {
	$fields['start_date'] = array( 'label' => esc_html__( 'Start date', 'WEBDWooEVENT' ), 'colname' => esc_html__( 'Start date', 'WEBDWooEVENT' ), 'checked' => 1 );
	return $fields;
}
add_filter('woe_get_order_product_value_start_date', 'webd_get_product_start_date_from_order', 10, 4);
function webd_get_product_start_date_from_order($value,$order, $item, $product) {
	$product_id = $item->get_product_id();
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if( $item->is_type('variable') && is_plugin_active( 'webd-event-bookings-daywiwebd-cost-variation-recurrence/variation-recurrence.php' )) {
        $product_id = $item->get_variation_id();
    }
	$meta_std = get_post_meta( $product_id, 'webd_startdate', true );
	if($meta_std!=''){
		$meta_std = date_i18n( get_option('date_format'), $meta_std).' - '.date_i18n(get_option('time_format'), $meta_std);
	}
	return $meta_std;
}
// end date
add_filter('woe_get_order_product_fields', 'webd_ev_enddate', 10, 2);
function webd_ev_enddate($fields,$format) {
	$fields['end_date'] = array( 'label' => esc_html__( 'End date', 'WEBDWooEVENT' ), 'colname' => esc_html__( 'End date', 'WEBDWooEVENT' ), 'checked' => 1 );
	return $fields;
}
add_filter('woe_get_order_product_value_end_date', 'webd_get_product_end_date_from_order', 10, 4);
function webd_get_product_end_date_from_order($value,$order, $item, $product) {
	$product_id = $item->get_product_id();
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if( $item->is_type('variable') && is_plugin_active( 'webd-event-bookings-daywiwebd-cost-variation-recurrence/variation-recurrence.php' )) {
        $product_id = $item->get_variation_id();
    }
	$meta_std = get_post_meta( $product_id, 'webd_enddate', true );
	if($meta_std!=''){
		$meta_std = date_i18n( get_option('date_format'), $meta_std).' - '.date_i18n(get_option('time_format'), $meta_std);
	}
	return $meta_std;
}
// Address
add_filter('woe_get_order_product_fields', 'webd_ev_address', 10, 2);
function webd_ev_address($fields,$format) {
	$fields['webd_address'] = array( 'label' => esc_html__( 'Location', 'WEBDWooEVENT' ), 'colname' => esc_html__( 'Location', 'WEBDWooEVENT' ), 'checked' => 1 );
	return $fields;
}
add_filter('woe_get_order_product_value_webd_address', 'get_product_webd_address_from_order', 10, 4);
function get_product_webd_address_from_order($value,$order, $item, $product) {
	$product_id = $item->get_product_id();
	$webd_adress = get_post_meta( $product_id, 'webd_adress', true ) ;
	return $webd_adress;
}
// Trainer name
add_filter('woe_get_order_product_fields', 'webd_ev_webd_view', 10, 2);
function webd_ev_webd_view($fields,$format) {
	$fields['webd_webd_view'] = array( 'label' => esc_html__( 'Speaker', 'WEBDWooEVENT' ), 'colname' => esc_html__( 'Speaker', 'WEBDWooEVENT' ), 'checked' => 1 );
	return $fields;
}
add_filter('woe_get_order_product_value_webd_webd_view', 'get_product_webd_webd_view_from_order', 10, 4);
function get_product_webd_webd_view_from_order($value,$order, $item, $product) {
	$product_id = $item->get_product_id();
	$webd_webd_views = get_post_meta( $product_id, 'webd_webd_views', true );
	if(!is_array($webd_webd_views) && $webd_webd_views!=''){
		$webd_webd_views = explode(",",$webd_webd_views);
	}
	$webd_view_text = '';
	if(is_array($webd_webd_views)){
		$cou = count($webd_webd_views);
		$i=0;
		foreach($webd_webd_views as $webd_view){
			$i++;
			$webd_view_text .= get_the_title($webd_view);
			if($i!=$cou){ $webd_view_text = $webd_view_text.' | ';}
		}
	}
	return $webd_view_text;
}
// Attendee info
add_filter('woe_get_order_product_fields', 'webd_ev_attendee_info', 10, 2);
function webd_ev_attendee_info($fields,$format) {
	$fields['webd_attendee'] = array( 'label' => esc_html__( 'Attendee info', 'WEBDWooEVENT' ), 'colname' => esc_html__( 'Attendee info', 'WEBDWooEVENT' ), 'checked' => 1 );
	return $fields;
}
add_filter('woe_get_order_product_value_webd_attendee', 'get_product_webd_attendee_from_order', 10, 4);
function get_product_webd_attendee_from_order($value,$order, $item, $product) {
	$id = $item->get_product_id();
	$order_items = $order->get_items();
	$n = 0; $find = 0;
	foreach ($order_items as $items_key => $items_value) {
		$n ++;
		if($items_value->get_id() == $item->get_id()){
			$find = 1;
			break;
		}
	}
	if($find == 0){ return;}
	$value_id = $id.'_'.$n;
	$value_id = apply_filters( 'webd_attendee_key', $value_id, $item );
	$metadata = get_post_meta($order->id,'att_info-'.$value_id, true);
	if($metadata == '' ){
		$metadata = get_post_meta($order->id,'att_info-'.$id, true);
	}
	$html='';
	if($metadata !=''){
		$metadata = explode("][",$metadata);
		if(!empty($metadata)){
			$i=0;
			foreach($metadata as $item){
				$i++;
				$item = explode("||",$item);
				$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
				$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
				if($i > 0){echo ' ';}
				$html .=  $f_name!='' || $l_name!='' ? ' '.esc_html__('Name: ','WEBDWooEVENT').$f_name.' '.$l_name : '';
				$html .=  isset($item[0]) && $item[0]!='' ? ' '.esc_html__('Email: ','WEBDWooEVENT').$item[0] : '';
				if($i!= count($metadata)){
					$html .= ' | ';
				}
			}
		}
	}
	return $html;
}
// event meta shortcode for WooCommerce layout builder 
if(!function_exists('webd_eventmeta_element_shortcode')){
	function webd_eventmeta_element_shortcode(){
		global $woocommerce, $post;
		ob_start();
		wooevent_template_plugin('event-meta');
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		
	}
	add_shortcode( 'webd_evmeta', 'webd_eventmeta_element_shortcode' );
}
if(!function_exists('webd_eventschedu_element_shortcode')){
	function webd_eventschedu_element_shortcode(){
		global $woocommerce, $post;
		ob_start();
		wooevent_template_plugin('event-schedu');
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		
	}
	add_shortcode( 'webd_evstatus', 'webd_eventschedu_element_shortcode' );
}

add_filter( 'body_class', 'webd_evdate_custom_class' );
function webd_evdate_custom_class( $classes ) {
	global $post;
    if ( get_post_meta( $post->ID, 'webd_startdate', true ) !='' ) {
        $classes[] = 'webd-product-with-date';
    }
    return $classes;
}


function webd_event_label_html( $id=false, $rt=false ) {
    $html = '';
    if(!isset($id) || $id ==''){
    	$id = get_the_ID();
    }
    $label = get_post_meta( $id, 'webd_label', true );
    if (  $label !='' ) {
    	$color = get_post_meta( $id, 'webd_label_color', true );
    	$color = $color!='' ? $color : '#FF5722';
        $html = '
			<span class="webd-event-label" style="background: '.esc_attr($color).';">'.$label.'</span>
        ';
    }
    $html = apply_filters( 'webd_event_label_html', $html );
    if(isset($rt) && $rt==1 ){
	    return $html;
	}else{
		echo $html;
	}
}
add_action( 'woocommerce_single_product_summary', 'webd_event_label_html', 4 );