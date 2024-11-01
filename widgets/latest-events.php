<?php
class WE_Latest_Events_Widget extends WP_Widget {	

	function __construct() {
    	$widget_ops = array(
			'classname'   => 'webd-latest-events-widget', 
			'description' => esc_html__('Latest Events','WEBDWooEVENT')
		);
    	parent::__construct('webd-latest-events', esc_html__('WE - Latest Events ','WEBDWooEVENT'), $widget_ops);
	}


	function widget($args, $instance) {
		ob_start();
		extract($args);
		
		$ids 		= empty($instance['ids']) ? '' : $instance['ids'];
		$title 		= empty($instance['title']) ? '' : $instance['title'];
		$title      = apply_filters('widget_title', $title);
		$cats 		= empty($instance['cats']) ? '' : $instance['cats'];
		$tags 		= empty($instance['tags']) ? '' : $instance['tags'];
		$number 	= empty($instance['number']) ? 5 : $instance['number'];
		$sort_by 	= empty($instance['sort_by']) ? '' : $instance['sort_by'];
		$order 		= empty($instance['order']) ? '' : $instance['order'];
		$style 		= empty($instance['style']) ? '' : $instance['style'];
		$args 		= woo_event_query('product', $number, $order, $sort_by, $meta_key='', $cats, $tags, $ids,'');
		$the_query 	= new WP_Query( $args );
		$html 		= $before_widget;
		$html .='<div class="webd-latest-event '.$style.'">';
		if ( $title ) $html .= $before_title . $title . $after_title; 
		if($the_query->have_posts()):
			while($the_query->have_posts()): $the_query->the_post();
				if(get_post_type(get_the_ID()) == 'product_variation') {
					$variation = wc_get_product(get_the_ID());
					$parent_id = $variation->get_parent_id();
				}else{ $parent_id = get_the_ID();}
				
				$webd_eventcolor = webd_event_custom_color($parent_id);
				if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
				$bgev_color = '';
				if($webd_eventcolor!=""){
					$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
				}
				$webd_startdate = get_post_meta( get_the_ID(), 'webd_startdate', true );
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

				$html .='<div class="item">';
					if($style=='modern'){
						if($webd_startdate!=''){
						$html .='
						<div class="webd-big-date">
							<div '.$bgev_color.'>
								<span class="item-date" '.$bgev_color.'>'.date_i18n( 'd', $webd_startdate).'</span>
								<span class="item-date">'.date_i18n( 'M', $webd_startdate).'</span>
							</div>
						</div>';
						}
					}else{
						if(has_post_thumbnail(get_the_ID())){
							$html .='<div class="thumb item-thumbnail">
								<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">
									<div class="item-thumbnail">
										'.get_the_post_thumbnail(get_the_ID(),'wethumb_85x85').'
										<span class="bg-overlay"></span>
										<span class="item-evprice" '.$bgev_color.'>'.$price.'</span>
									</div>
								</a>
							</div>';
						}
					}
					$html .='<div class="event-details item-content">
						<h3><a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'" class="main-color-1-hover">'.the_title_attribute('echo=0').'</a></h3>';
						if($style=='modern'){
							$html .='<span class="item-evdate">'.$price.'</span>';
						}elseif($webd_startdate!=''){
						$html .='
						<span class="item-evdate">'.date_i18n( get_option('date_format'), $webd_startdate).'</span>';
						}
						$html .='
					</div>';
				$html .='<div class="clearfix"></div></div>';
			endwhile;
		endif;
		$html .='</div>';
		$html .= $after_widget;
		echo $html;
		wp_reset_postdata();
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['ids'] = strip_tags($new_instance['ids']);
		$instance['sort_by'] = esc_attr($new_instance['sort_by']);
		$instance['order'] = esc_attr($new_instance['order']);
		$instance['style'] = esc_attr($new_instance['style']);
		$instance['tags'] = strip_tags($new_instance['tags']);
        $instance['cats'] = strip_tags($new_instance['cats']);
		$instance['number'] = absint($new_instance['number']);
		return $instance;
	}
	
	
	
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$ids = isset($instance['ids']) ? esc_attr($instance['ids']) : '';
		$tags = isset($instance['tags']) ? esc_attr($instance['tags']) : '';
		$cats = isset($instance['cats']) ? esc_attr($instance['cats']) : '';
		$sort_by = isset($instance['sort_by']) ? esc_attr($instance['sort_by']) : '';
		$order = isset($instance['order']) ? esc_attr($instance['order']) : '';
		$style = isset($instance['style']) ? esc_attr($instance['style']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:','WEBDWooEVENT'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("style"); ?>">
            <?php esc_html_e('Style','WEBDWooEVENT');	 ?>:
            <select id="<?php echo $this->get_field_id("style"); ?>" name="<?php echo $this->get_field_name("style"); ?>">
                <option value="classic"<?php selected( $style, "classic" ); ?>><?php esc_html_e('Classic','WEBDWooEVENT');?></option>
                <option value="modern"<?php selected( $style, "modern" ); ?>><?php esc_html_e('Modern','WEBDWooEVENT');?></option>
            </select>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('ids'); ?>"><?php esc_html_e('ID list show:','WEBDWooEVENT'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="text" value="<?php echo $ids; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('tags'); ?>"><?php esc_html_e('Tags:','WEBDWooEVENT'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo $tags; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('cats'); ?>"><?php esc_html_e('Categories : (ID or Slug. Ex: 1, 2)','WEBDWooEVENT'); ?></label> 
          <textarea rows="4" cols="46" id="<?php echo $this->get_field_id('cats'); ?>" name="<?php echo $this->get_field_name('cats'); ?>"><?php echo $cats; ?></textarea>

        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number:','WEBDWooEVENT'); ?></label>
            <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("sort_by"); ?>">
            <?php esc_html_e('Order by','WEBDWooEVENT');	 ?>:
            <select id="<?php echo $this->get_field_id("sort_by"); ?>" name="<?php echo $this->get_field_name("sort_by"); ?>">
                <option value="date"<?php selected( $sort_by, "date" ); ?>><?php esc_html_e('Latest','WEBDWooEVENT');?></option>
                <option value="upcoming"<?php selected( $sort_by, "upcoming" ); ?>><?php esc_html_e('Upcoming Events','WEBDWooEVENT');?></option>
                <option value="ontoup"<?php selected( $sort_by, "ontoup" ); ?>><?php esc_html_e('Ongoing & Upcoming Events','WEBDWooEVENT');?></option>
                <option value="past"<?php selected( $sort_by, "past" ); ?>><?php esc_html_e('Past Events','WEBDWooEVENT');?></option>
                <option value="title"<?php selected( $sort_by, "title" ); ?>><?php esc_html_e('Title','WEBDWooEVENT');?></option>
                <option value="post__in"<?php selected( $sort_by, "post__in" ); ?>><?php esc_html_e('Post__in','WEBDWooEVENT');?></option>
            </select>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("order"); ?>">
            <?php esc_html_e('Order','WEBDWooEVENT');	 ?>:
            <select id="<?php echo $this->get_field_id("order"); ?>" name="<?php echo $this->get_field_name("order"); ?>">
                <option value="DESC"<?php selected( $order, "DESC" ); ?>><?php esc_html_e('DESC','WEBDWooEVENT');?></option>
                <option value="ASC"<?php selected( $order, "ASC" ); ?>><?php esc_html_e('ASC','WEBDWooEVENT');?></option>
            </select>
            </label>
        </p>
	<?php
	}
}
// register widget
if(!function_exists('webd_latest_event_register_widgets')){
	function webd_latest_event_register_widgets() {
		register_widget( 'WE_Latest_Events_Widget' );
	}
	add_action( 'widgets_init', 'webd_latest_event_register_widgets' );
}