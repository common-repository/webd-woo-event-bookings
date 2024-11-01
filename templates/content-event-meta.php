<?php
global $woocommerce, $post,$webd_main_purpose;
if($webd_main_purpose=='woo'){
	return;
}
	
$webd_startdate = webd_global_startdate();
$webd_enddate = webd_global_enddate() ;
$webd_adress = get_post_meta( $post->ID, 'webd_adress', true ) ;
$webd_lat = get_post_meta( $post->ID, 'webd_latitude_longitude', true ) ;
$webd_phone = get_post_meta( $post->ID, 'webd_phone', true ) ;
$webd_email = get_post_meta( $post->ID, 'webd_email', true ) ;
$webd_website = get_post_meta( $post->ID, 'webd_website', true ) ;
$webd_webd_views = get_post_meta( $post->ID, 'webd_webd_views', true );
$webd_schedu = get_post_meta( $post->ID, 'webd_schedu', false );?>
<div class="webd-woo-event-info col-md-12">
	<?php
	if(!is_array($webd_webd_views) && $webd_webd_views!=''){
		$webd_webd_views = explode(",",$webd_webd_views);
	}
	$webd_text_webd_view = get_option('webd_text_webd_view');
	if(is_array($webd_webd_views)){?>
    <span class="sub-lb spk-sub"><?php if($webd_text_webd_view!=''){ echo esc_attr($webd_text_webd_view);}else{ echo esc_html__('Speaker','WEBDWooEVENT');}?></span>
	<div class="webd_view-info row">
		<?php
        foreach($webd_webd_views as $webd_view){?>
            <div class="col-md-6 col-sm-6">
                <div class="media">
                    <div class="media-body">
                        <div class="media-heading">
                            <div class="webd_view-avatar"><?php echo get_the_post_thumbnail($webd_view, 'thumbnail')?></div>
                            <div class="webd_view-details">
                                <span><a href="<?php echo get_permalink($webd_view);?>"><?php echo get_the_title($webd_view);?></a></span>
                                <span><?php echo get_post_meta( $webd_view, 'webd_view_position', true );?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><?php
        }?>
    </div>
    <?php }?>
    <div class="date-info row">
        <?php
		$webd_show_timezone = get_option('webd_show_timezone');
		$webd_time_zone = get_post_meta($post->ID,'webd_time_zone', true );
		$class_d = 'col-md-6';
		if($webd_show_timezone=='yes' && $webd_time_zone!='' && $webd_time_zone!='def'){
			$class_d = 'col-md-4';
		}
		$all_day = get_post_meta($post->ID,'webd_allday', true );
        if($webd_startdate){?>
            <div class="<?php echo esc_attr($class_d);?> event-startdate">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_start')!='' ? get_option('webd_text_start') : esc_html__('Start','WEBDWooEVENT');?></span>           	
                        <div class="media-heading">
                            <?php 
                            echo date_i18n( get_option('date_format'), $webd_startdate).' ';
                            if(($webd_enddate=='') || ($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate)))){ 
                                echo date_i18n(get_option('time_format'), $webd_startdate);
                            }?>
                        </div>
                    </div>
                </div>
            </div><?php
        }
        if($webd_enddate){?>
            <div class="<?php echo esc_attr($class_d);?> event-enddate">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_end')!='' ? get_option('webd_text_end') : esc_html__('End','WEBDWooEVENT');?> </span>     	
                        <div class="media-heading">
                            <?php 
                            echo date_i18n( get_option('date_format'), $webd_enddate);
                            if($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate))){ 
                                echo ' '.date_i18n(get_option('time_format'), $webd_enddate);
                            }elseif($all_day=='1'){ 
								$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
								echo '<span> '.$alltrsl.'</span>';
							}?>
                        </div>
                    </div>
                </div>
            </div><?php
        }
		if($class_d == 'col-md-4'){?>
        	<div class="<?php echo esc_attr($class_d);?> event-timezone">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_timezone_')!='' ? get_option('webd_text_timezone_') : esc_html__('Timezone','WEBDWooEVENT');?> </span>     	
                        <div class="media-heading">
                            <?php 
							if (strpos($webd_time_zone, '-') !== false) {
								echo apply_filters( 'wooevent_timezone_html', 'UTC'.$webd_time_zone, $webd_time_zone );
							}else{
                            	echo apply_filters( 'wooevent_timezone_html', 'UTC+'.$webd_time_zone, $webd_time_zone );
							}
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
    <?php
	$sun_offset = get_post_meta($post->ID,'webd_sun_offset', true );
	if(get_option('webd_sunsire_set')=='yes' && $webd_lat!='' && $webd_startdate!='' && $sun_offset!='off'){
		$lat_log = explode(",",$webd_lat) ;
		$sun_info = date_sun_info($webd_startdate,$lat_log[0],$lat_log[1]);
		$gmt_offset = get_option('gmt_offset');
		$sunrise = $sun_info['sunrise'] + ($gmt_offset*3600);
		$sunset = $sun_info['sunset'] + ($gmt_offset*3600);
		if($sun_offset!='' && is_numeric($sun_offset)){
			$sunrise = $sunrise + ($sun_offset*3600);
			$sunset = $sunset + ($sun_offset*3600);
		}?>
        <div class="row sunset-sunsire-info">
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                         <span class="sub-lb"><?php echo get_option('webd_text_sunrise')!='' ? get_option('webd_text_sunrise') : esc_html__('Sunrise','WEBDWooEVENT');?> </span>
                        <div class="media-heading">
                            <?php  echo date(get_option('time_format'),$sunrise);?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_sunset')!='' ? get_option('webd_text_sunset') : esc_html__('Sunset','WEBDWooEVENT');?> </span>    	
                        <div class="media-heading">
                            <?php  echo date(get_option('time_format'),$sunset);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
    <div class="row location-info">
            <?php 
            if($webd_adress){?>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_addres')!='' ? get_option('webd_text_addres') : esc_html__('Address','WEBDWooEVENT');?> </span>          	
                        <div class="media-heading">
                            <?php echo $webd_adress;?>&nbsp;&nbsp;
                            <a href="http://maps.google.com/?q=<?php echo $webd_lat!='' ? $webd_lat : $webd_adress;?>" target="_blank" class="map-link small-text"><?php echo get_option('webd_text_vmap')!='' ? get_option('webd_text_vmap') : esc_html__('View map','WEBDWooEVENT') ?> <i class="fa fa-map-marker"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            <?php 
            if($webd_phone){?>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_phone')!='' ? get_option('webd_text_phone') : esc_html__('Phone','WEBDWooEVENT');?> </span>      	
                        <div class="media-heading">
                            <?php echo $webd_phone;?>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
    </div>
    
    <div class="row more-info">
            <?php 
            if($webd_email){?>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_email')!='' ? get_option('webd_text_email') : esc_html__('Email','WEBDWooEVENT');?></span>   	
                        <div class="media-heading">
                            <a href="mailto:<?php echo $webd_email;?>"><?php echo $webd_email;?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            <?php 
            if($webd_website){?>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_web')!='' ? get_option('webd_text_web') : esc_html__('Website','WEBDWooEVENT');?></span> 	
                        <div class="media-heading">
                            <a href="<?php echo esc_url($webd_website);?>" target="_blank"><?php echo esc_url($webd_website);?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
    </div>
    <?php 
	$webd_custom_metadata = get_post_meta( $post->ID, 'webd_custom_metadata', false );
	if(is_array($webd_custom_metadata) && !empty($webd_custom_metadata)){
		$number = count($webd_custom_metadata);?>
		<div class="row webd-custom-event-info">
			<?php 
			$i = 0;
			foreach($webd_custom_metadata as $item){
				$i++;?>
				<div class="col-md-6 col-sm-6">
					<div class="media">
						<div class="media-body">
							<span class="sub-lb"><?php echo $item['webd_custom_title'];?></span>
							<div class="media-heading">
                                <span class="webd-sub-ct media-heading"><?php echo $item['webd_custom_content'];?></span>
							</div>
						</div>
					</div>
				</div>
				<?php 
				if($i < $number && $i % 2==0){?>
				</div>
				<div class="row">	
				<?php }
			}?>
		</div>
	<?php }
    do_action('webd_single_after_meta_html')?>
</div>