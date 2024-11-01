<?php
global $woocommerce, $post,$webd_main_purpose;
$webd_startdate = webd_global_startdate();
$webd_enddate = webd_global_enddate() ;
$webd_adress = get_post_meta( $post->ID, 'webd_adress', true ) ;
$webd_phone = get_post_meta( $post->ID, 'webd_phone', true ) ;
$webd_email = get_post_meta( $post->ID, 'webd_email', true ) ;
$webd_website = get_post_meta( $post->ID, 'webd_website', true ) ;
$webd_webd_views = get_post_meta( $post->ID, 'webd_webd_views', true );
$webd_schedu = get_post_meta( $post->ID, 'webd_schedu', false );?>
<div class="webd-woo-event-info">
	<?php
	if(!is_array($webd_webd_views) && $webd_webd_views!=''){
		$webd_webd_views = explode(",",$webd_webd_views);
	}
	if(is_array($webd_webd_views) && $webd_main_purpose!='woo'){?>
    <span class="sub-lb spk-sub"><?php echo get_option('webd_text_webd_view')!='' ? get_option('webd_text_webd_view') :  esc_html__('Speaker','WEBDWooEVENT');?></span>
	<div class="webd_view-info row">
		<?php
        foreach($webd_webd_views as $webd_view){ $i++?>
            <div class="col-md-6">
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
        if($webd_startdate){?>
            <div class="col-md-6 event-startdate">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_start')!='' ? get_option('webd_text_start') : esc_html__('Start','WEBDWooEVENT');?></span>           	
                        <div class="media-heading">
                            <?php 
                            echo date_i18n( get_option('date_format'), $webd_startdate).' ';
                            if(date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate)){ 
                                echo date_i18n(get_option('time_format'), $webd_startdate);
                            }?>
                        </div>
                    </div>
                </div>
            </div><?php
        }
        if($webd_enddate){?>
            <div class="col-md-6 event-enddate">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_end')!='' ? get_option('webd_text_end') : esc_html__('End','WEBDWooEVENT');?> </span>     	
                        <div class="media-heading">
                            <?php 
                            if($webd_enddate!=$webd_startdate){
                                echo date_i18n( get_option('date_format'), $webd_enddate);
                            }
                            if(date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate)){ 
                                echo ' '.date_i18n(get_option('time_format'), $webd_enddate);
                            }?>
                        </div>
                    </div>
                </div>
            </div><?php
        }?>
    </div>
    <div class="row location-info">
            <?php 
            if($webd_adress){?>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <span class="sub-lb"><?php echo get_option('webd_text_addres')!='' ? get_option('webd_text_addres') : esc_html__('Address','WEBDWooEVENT');?> </span>          	
                        <div class="media-heading">
                            <?php echo $webd_adress;?>&nbsp;&nbsp;
                            <a href="http://maps.google.com/?q=<?php echo $webd_adress;?>" target="_blank" class="map-link small-text"><?php echo get_option('webd_text_vmap')!='' ? get_option('webd_text_vmap') : esc_html__('View map','WEBDWooEVENT') ?> <i class="fa fa-map-marker"></i></a>
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
                        <span class="sub-lb"><?php echo get_option('webd_text_phone')!='' ? get_option('webd_text_phone') : esc_html__('Phone: ','WEBDWooEVENT');?></span>      	
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
</div>