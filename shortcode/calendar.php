<?php
function parwebd_webd_calendar_func($atts, $content){
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$uwebd_shortcode =  isset($atts['uwebd_shortcode']) ? $atts['uwebd_shortcode'] :'1'; 
	$view =  isset($atts['view']) ? $atts['view'] :'month';
	$viewas_button =  isset($atts['viewas_button']) ? $atts['viewas_button'] :'';
	$defaultDate =  isset($atts['defaultdate']) && $atts['defaultdate']!='' ? $atts['defaultdate'] : date('Y-m-d');
	$firstDay =  isset($atts['firstday']) && $atts['firstday']!='' ? $atts['firstday'] : '1';
	$scrolltime =  isset($atts['scrolltime']) && $atts['scrolltime']!='' ? $atts['scrolltime'] : '';
	$class =  isset($atts['class']) ? $atts['class'] :'';
	$ids 		= isset($atts['ids']) ? $atts['ids'] : '';
	$ex_ids 		= isset($atts['ex_ids']) ? $atts['ex_ids'] : '';
	$style =  isset($atts['style']) ? $atts['style'] :''; 
	$cat 		=isset($atts['cat']) ? $atts['cat'] : '';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$show_search =  isset($atts['show_search']) ? $atts['show_search'] :'';
	$calendar_language =  isset($atts['calendar_language']) ? $atts['calendar_language'] :'';
	$show_bt =  isset($atts['show_bt']) ? $atts['show_bt'] :'';
	$show_ical =  isset($atts['show_ical']) ? $atts['show_ical'] :'';

	$mintime =  isset($atts['mintime']) ? $atts['mintime'] :'';
	$maxtime =  isset($atts['maxtime']) ? $atts['maxtime'] :'';
	
	$taxonomy =  isset($atts['taxonomy']) ? $atts['taxonomy'] :'';
	$terms =  isset($atts['terms']) ? $atts['terms'] :'';
	$webd_view_id =  isset($atts['webd_view_id']) ? $atts['webd_view_id'] :'';
	$cat_list 		= isset($atts['cat_list']) ? $atts['cat_list'] : '';
	$loc_list 		= isset($atts['loc_list']) ? $atts['loc_list'] : '';
	$tag_list 		= isset($atts['tag_list']) ? $atts['tag_list'] : '';
	$spk_list 		= isset($atts['spk_list']) ? $atts['spk_list'] : '';
	if($style =='modern'){ $class .=" widget-style";}
	wp_enqueue_script( 'moment', WEBD_EVENT_BOOKINGS.'js/fullcalendar/lib/moment.min.js', array( 'jquery' ), '3.5', true  );
	wp_enqueue_script( 'webd-fullcalendar', WEBD_EVENT_BOOKINGS.'js/fullcalendar/fullcalendar.min.js', array( 'jquery' ), '3.5', true  );
	$language_crr = esc_attr(get_option('webd_calendar_lg'));
	if($calendar_language != ''){
		$language_crr = $calendar_language;
	}
	if($language_crr!='' && $language_crr!='en'){
		wp_enqueue_script( 'webd-fullcalendar-language', WEBD_EVENT_BOOKINGS.'js/fullcalendar/locale/'.$language_crr.'.js', array( 'jquery' ), '3.5', true  );
	}
	if(get_option('webd_qtip_js')!='on'){
		wp_enqueue_script( 'webd-jquery-qtip',  WEBD_EVENT_BOOKINGS.'js/fullcalendar/lib/qtip/jquery.qtip.min.js' , array( 'jquery' ), '3.5', true  );
	}
	ob_start();
	$webd_shop_view = get_option('webd_shop_view');
	$webd_search_enable = get_option('webd_search_enable');
	if(is_shop() && $webd_search_enable!='disable' && $show_search=='1'){ echo wooevent_search_view_bar($ID);}

	global $wp;
	$crurl =  home_url( $wp->request );
	?>
    <div class="webd-calendar <?php echo esc_attr($style);?>-style" data-id ="<?php echo esc_attr($ID);?>" id="<?php echo esc_attr($ID);?>">
        <div class="webd-loading">
            <div class="wpex-spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
        <?php 
		$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
		echo '<div class="alert alert-warning calendar-info hidden"><i class="fa fa-exclamation-triangle"></i>'.$noftrsl.'</div>';
		
		if((isset($_GET['view']) && sanitize_text_field($_GET['view'])=='list' && $show_search=='1') || is_search()&& $show_search=='1' || is_shop()&& $show_search=='1' && $webd_shop_view=='list' && !isset($_GET['view']) ){
		}elseif((isset($_GET['view']) && sanitize_text_field($_GET['view'])=='map' ) || $webd_shop_view=='map' ){
			echo do_shortcode('[webd_map type="upcoming"]');
		}elseif(is_shop() || $uwebd_shortcode=='1' || $show_search!='1'){
			if(is_shop() && $show_search=='1'){
				if(isset($_GET['view'])){ 
					$cal_view = sanitize_text_field($_GET['view']);
				}elseif($webd_shop_view!=''){ 
					$cal_view = $webd_shop_view;
				}else{ 
					$cal_view = 'month';
				}?>
            	<input type="hidden"  name="calendar_view" value="<?php echo esc_attr($cal_view);?>">
            <?php }else{?>
            	<input type="hidden"  name="calendar_view" value="<?php if($view!=''){ echo esc_attr($view);}else{ echo 'month';}?>">
            <?php }
			
			$wpml_crr = '';
			if (class_exists('SitePress')){
				global $sitepress;
				$wpml_crr = $sitepress->get_current_language();
				if($language_crr != ''){
					$language_crr = $wpml_crr;
				}
			}
			?>
            
            <input type="hidden"  name="calendar_language" value="<?php echo $language_crr;?>">
            <input type="hidden"  name="calendar_wpml" value="<?php echo $wpml_crr;?>">
            <input type="hidden"  name="calendar_defaultDate" value="<?php echo esc_attr($defaultDate);?>">
            <input type="hidden"  name="calendar_firstDay" value="<?php echo esc_attr($firstDay);?>">
            <input type="hidden"  name="calendar_orderby" value="<?php echo esc_attr($orderby);?>">
            <input type="hidden"  name="calendar_cat" value="<?php echo esc_attr($cat);?>">
            <input type="hidden" name="param_shortcode" value="<?php echo esc_html(str_replace('\/', '/', json_encode($atts)));?>">
            <input type="hidden"  name="taxonomy" value="<?php echo esc_attr($taxonomy);?>">
            <input type="hidden"  name="terms" value="<?php echo esc_attr($terms);?>">
            <input type="hidden"  name="webd_view_id" value="<?php echo esc_attr($webd_view_id);?>">
            <input type="hidden"  name="current_url" value="<?php echo esc_url($crurl);?>">
            <input type="hidden"  name="calendar_ids" value="<?php echo esc_attr($ids);?>">
            <input type="hidden"  name="ex_ids" value="<?php echo esc_attr($ex_ids);?>">
            <input type="hidden"  name="show_bt" value="<?php echo esc_attr($show_bt);?>">
            <input type="hidden"  name="scrolltime" value="<?php echo esc_attr($scrolltime);?>">
            <input type="hidden"  name="mintime" value="<?php echo esc_attr($mintime);?>">
            <input type="hidden"  name="maxtime" value="<?php echo esc_attr($maxtime);?>">
            <input type="hidden"  name="viewas_button" value="<?php echo esc_attr($viewas_button);?>">
            <input type="hidden"  name="yearl_text" value="<?php echo get_option('webd_text_yal')!='' ? esc_attr(get_option('webd_text_yal')) : esc_html__('List Year','WEBDWooEVENT');?> ">
            <div class="webd-ctnr-cal">
                <div class="webd-cal-ftgr" style=" display:none;"><?php webd_calendar_month_select($cat_list, $tag_list,$loc_list, $spk_list);?></div>
                <div id="calendar" class="<?php echo esc_attr($class);?>"></div>
                <?php if($show_ical=='yes'){?>
                    <div class="weical-bt">
                        <a class="btn btn-primary webd-button" href="<?php echo home_url().'?ical_events=we&category='.esc_attr($cat).'&tax='.esc_attr($taxonomy).'&terms='.esc_attr($terms).'&webd_view='.esc_attr($webd_view_id); ?>"><?php echo get_option('webd_text_ical')!='' ? get_option('webd_text_ical') : esc_html__('+ Ical Import','WEBDWooEVENT');?></a>
                    </div>
                <?php }?>
            </div>
        <?php }?>
        <input type="hidden"  name="ajax_url" value="<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>">
        <?php
		if($style=='modern'){
			$day_event = '';
			echo '<div class="wt-eventday">'.$day_event.'</div>';
		}
		?>
    </div>
    <?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_calendar', 'parwebd_webd_calendar_func' );
add_action( 'after_setup_theme', 'webd_reg_calendar_vc' );
function webd_reg_calendar_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Calendar", "WEBDWooEVENT"),
	   "base" => "webd_calendar",
	   "class" => "",
	   "icon" => "icon-calendar",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
	   	  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("View", 'WEBDWooEVENT'),
			 "param_name" => "view",
			 "value" => array(
			 	esc_html__('Month', 'WEBDWooEVENT') => '',
				esc_html__('Basic Week', 'WEBDWooEVENT') => 'basicWeek',
				esc_html__('Agenda Week', 'WEBDWooEVENT') => 'agendaWeek',
				esc_html__('Basic Day', 'WEBDWooEVENT') => 'basicDay',
				esc_html__('Agenda Day ', 'WEBDWooEVENT') => 'agendaDay',
				esc_html__('List Month', 'WEBDWooEVENT') => 'listMonth',
				esc_html__('List Year', 'WEBDWooEVENT') => 'listYear',
			 ),
			 "description" => ''
		  ),
		  array(
			  "admin_label" => true,
			  "type" => "checkbox",
			  "heading" => esc_html__("Show view as", "WEBDWooEVENT"),
			  "param_name" => "viewas_button",
			  "value" => array(
				  esc_html__('Month', 'WEBDWooEVENT') => 'month',
				  esc_html__('Basic Week', 'WEBDWooEVENT') => 'basicWeek',
				  esc_html__('Agenda Week', 'WEBDWooEVENT') => 'agendaWeek',
				  esc_html__('Basic Day', 'WEBDWooEVENT') => 'basicDay',
				  esc_html__('Agenda Day ', 'WEBDWooEVENT') => 'agendaDay',
				  esc_html__('List Month', 'WEBDWooEVENT') => 'listMonth',
				  esc_html__('List Year', 'WEBDWooEVENT') => 'listYear',
			  ),
			  "description" => esc_html__("Select view as button you want to to show", "WEBDWooEVENT"),
		  ),
		  array(
			  "admin_label" => true,
			  "type" => "textfield",
			  "heading" => esc_html__("IDs", "WEBDWooEVENT"),
			  "param_name" => "ids",
			  "value" => "",
			  "description" => esc_html__("List post IDs to retrieve, separated by a comma", "WEBDWooEVENT"),
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
			 "heading" => esc_html__("Calendar Month Style", 'WEBDWooEVENT'),
			 "param_name" => "style",
			 "value" => array(
				esc_html__('Classic', 'WEBDWooEVENT') => '',
				esc_html__('Modern', 'WEBDWooEVENT') => 'modern',
			 ),
			 'dependency' 	=> array(
				'element' => 'view',
				'value'   => array(''),
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show button", 'WEBDWooEVENT'),
			 "param_name" => "show_bt",
			 "value" => array(
				esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Add to cart', 'WEBDWooEVENT') => 'addtocart',
				esc_html__('View Details', 'WEBDWooEVENT') => 'details',
			 ),
			 'dependency' 	=> array(
				'element' => 'style',
				'value'   => array(''),
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Default date", "WEBDWooEVENT"),
			"param_name" => "defaultdate",
			"value" => "",
			"description" => esc_html__("The initial date displayed when the calendar first loads. Ex:2016-05-19", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "dropdown",
			"heading" => esc_html__("First day", "WEBDWooEVENT"),
			"param_name" => "firstday",
			"value" => array(
				esc_html__('Monday', 'WEBDWooEVENT') => '1',
				esc_html__('Tuesday', 'WEBDWooEVENT') => '2',
				esc_html__('Wednesday', 'WEBDWooEVENT') => '3',
				esc_html__('Thursday,', 'WEBDWooEVENT') => '4',
				esc_html__('Friday', 'WEBDWooEVENT') => '5',
				esc_html__('Saturday', 'WEBDWooEVENT') => '6',
				esc_html__('Sunday', 'WEBDWooEVENT') => '7',
			 ),
			"description" => esc_html__("The day that each week begins.", "WEBDWooEVENT"),
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
			 "heading" => esc_html__("Order by", 'WEBDWooEVENT'),
			 "param_name" => "orderby",
			 "value" => array(
				esc_html__('Default', 'WEBDWooEVENT') => '',
				esc_html__('Upcoming', 'WEBDWooEVENT') => 'upcoming',
				esc_html__('Past', 'WEBDWooEVENT') => 'past',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show Ical export button", 'WEBDWooEVENT'),
			 "param_name" => "show_ical",
			 "value" => array(
				esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => 'yes',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Calendar language", 'WEBDWooEVENT'),
			 "param_name" => "calendar_language",
			 "value" => array(
				esc_html__('Default', 'WEBDWooEVENT') => '',
				esc_html__( 'ar-ma', 'WEBDWooEVENT' ) => 'ar-ma',
				esc_html__( 'ar-sa', 'WEBDWooEVENT' ) => 'ar-sa',
				esc_html__( 'ar-tn', 'WEBDWooEVENT' ) => 'ar-tn',
				esc_html__( 'ar', 'WEBDWooEVENT' ) => 'ar',
				esc_html__( 'bg', 'WEBDWooEVENT' ) => 'bg',
				esc_html__( 'ca', 'WEBDWooEVENT' ) => 'ca',
				esc_html__( 'cs', 'WEBDWooEVENT' ) => 'cs',
				esc_html__( 'da', 'WEBDWooEVENT' ) => 'da',
				esc_html__( 'de-at', 'WEBDWooEVENT' ) => 'de-at',
				esc_html__( 'de', 'WEBDWooEVENT' ) => 'de',
				esc_html__( 'el', 'WEBDWooEVENT' ) => 'el',
				esc_html__( 'en-au', 'WEBDWooEVENT' ) => 'en-au',
				esc_html__( 'en-ca', 'WEBDWooEVENT' ) => 'en-ca',
				esc_html__( 'en-gb', 'WEBDWooEVENT' ) => 'en-gb',
				esc_html__( 'en-ie', 'WEBDWooEVENT' ) => 'en-ie',
				esc_html__( 'en-nz', 'WEBDWooEVENT' ) => 'en-nz',
				esc_html__( 'es', 'WEBDWooEVENT' ) => 'es',
				esc_html__( 'fa', 'WEBDWooEVENT' ) => 'fa',
				esc_html__( 'fi', 'WEBDWooEVENT' ) => 'fi',
				esc_html__( 'fr-ca', 'WEBDWooEVENT' ) => 'fr-ca',
				esc_html__( 'fr-ch', 'WEBDWooEVENT' ) => 'fr-ch',
				esc_html__( 'fr', 'WEBDWooEVENT' ) => 'fr',
				esc_html__( 'he', 'WEBDWooEVENT' ) => 'he',
				esc_html__( 'hi', 'WEBDWooEVENT' ) => 'hi',
				esc_html__( 'hr', 'WEBDWooEVENT' ) => 'hr',
				esc_html__( 'hu', 'WEBDWooEVENT' ) => 'hu',
				esc_html__( 'id', 'WEBDWooEVENT' ) => 'id',
				esc_html__( 'is', 'WEBDWooEVENT' ) => 'is',
				esc_html__( 'it', 'WEBDWooEVENT' ) => 'it',
				esc_html__( 'ja', 'WEBDWooEVENT' ) => 'ja',
				esc_html__( 'ko', 'WEBDWooEVENT' ) => 'ko',
				esc_html__( 'lt', 'WEBDWooEVENT' ) => 'lt',
				esc_html__( 'lv', 'WEBDWooEVENT' ) => 'lv',
				esc_html__( 'nb', 'WEBDWooEVENT' ) => 'nb',
				esc_html__( 'nl', 'WEBDWooEVENT' ) => 'nl',
				esc_html__( 'pl', 'WEBDWooEVENT' ) => 'pl',
				esc_html__( 'pt-br', 'WEBDWooEVENT' ) => 'pt-br',
				esc_html__( 'pt', 'WEBDWooEVENT' ) => 'pt',
				esc_html__( 'ro', 'WEBDWooEVENT' ) => 'ro',
				esc_html__( 'ru', 'WEBDWooEVENT' ) => 'ru',
				esc_html__( 'sk', 'WEBDWooEVENT' ) => 'sk',
				esc_html__( 'sl', 'WEBDWooEVENT' ) => 'sl',
				esc_html__( 'sr-cyrl', 'WEBDWooEVENT' ) => 'sr-cyrl',
				esc_html__( 'sr', 'WEBDWooEVENT' ) => 'sr',
				esc_html__( 'sv', 'WEBDWooEVENT' ) => 'sv',
				esc_html__( 'th', 'WEBDWooEVENT' ) => 'th',
				esc_html__( 'tr', 'WEBDWooEVENT' ) => 'tr',
				esc_html__( 'uk', 'WEBDWooEVENT' ) => 'uk',
				esc_html__( 'vi', 'WEBDWooEVENT' ) => 'vi',
				esc_html__( 'zh-cn', 'WEBDWooEVENT' ) => 'zh-cn',
				esc_html__( 'zh-tw', 'WEBDWooEVENT' ) => 'zh-tw',
				esc_html__( 'wpml', 'WEBDWooEVENT' ) => 'wpml',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("List Category in filter", "WEBDWooEVENT"),
			"param_name" => "cat_list",
			"value" => "",
			"description" => esc_html__("Enter list of category id to show in select box filter, leave blank to show all, enter hide to hide this box", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("List Tags in filter", "WEBDWooEVENT"),
			"param_name" => "tag_list",
			"value" => "",
			"description" => esc_html__("Enter list of Tags id to show in select box filter, leave blank to show all, enter hide to hide this box", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("List Venues in filter", "WEBDWooEVENT"),
			"param_name" => "loc_list",
			"value" => "",
			"description" => esc_html__("Enter list of Venues id to show in select box filter, leave blank to show all, enter hide to hide this box", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("List Speaker in filter", "WEBDWooEVENT"),
			"param_name" => "spk_list",
			"value" => "",
			"description" => esc_html__("Enter list of Venues id to show in select box filter, leave blank to show all, enter hide to hide this box", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Min time", "WEBDWooEVENT"),
			"param_name" => "mintime",
			"value" => "",
			"description" => esc_html__("Determines the first time slot that will be displayed for each day, default 00:00:00 (only use for weekly view)", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Max time", "WEBDWooEVENT"),
			"param_name" => "maxtime",
			"value" => "",
			"description" => esc_html__("Determines the last time slot that will be displayed for each day, default 24:00:00 (only use for weekly view)", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Class", "WEBDWooEVENT"),
			"param_name" => "class",
			"value" => "",
			"description" => esc_html__("Enter class for custom css", 'WEBDWooEVENT'),
		  ),
	   )
	));
	}
}