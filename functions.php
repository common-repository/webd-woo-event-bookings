<?php
if (!function_exists('webd_filter_wc_get_template_fix')) {
	function webd_filter_wc_get_template_fix($located, $template_name, $args, $template_path, $default_path){
		if($located==''){
			return get_stylesheet_directory() . '/webd-event-bookings-daywiwebd-cost/blank.php';;
		}else{
			return $located;
		}
	}
	add_filter( 'wc_get_template', 'webd_filter_wc_get_template_fix', 999, 5 );
}
