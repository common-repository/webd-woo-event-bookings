<?php
global $woocommerce, $post,$webd_main_purpose;
$webd_startdate = webd_global_startdate();
$webd_enddate = webd_global_enddate() ;
$webd_schedu = get_post_meta( $post->ID, 'webd_schedu', false );
wooevent_template_plugin('event-sponsors');
if($webd_main_purpose!='woo'){?>
    <div class="clear"></div>
    <?php webd_ical_google_button( $post->ID ); ?>
    <div class="webd-woo-event-schedu col-md-12">
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h3><?php echo get_option('webd_text_status')!='' ? get_option('webd_text_status') : esc_html__('Status','WEBDWooEVENT')?></h3>
                <div class="webd-sche-detail webd-status ">
                    <?php echo do_shortcode('[webd_countdown single="1" ids ="'.$post->ID.'" show_title="0"]');
                    global $product; 
                    $total = get_post_meta($post->ID, 'total_sales', true);
					if(get_option('webd_dis_status') !='yes'){?>
                    <p><i class="fa fa-ticket"></i> 
                        <?php echo woo_event_status( $post->ID, $webd_enddate)?>
                    </p>
                    <?php }
					if(get_option('webd_dis_hassold') !='yes'){?>
                    <p><i class="fa fa-user"></i> 
						<?php 
						$hasstrsl = get_option('webd_text_hassold')!='' ? get_option('webd_text_hassold') : esc_html__('Has Sold','WEBDWooEVENT');
						echo $total.'  '.$hasstrsl;?>
                    </p>
                    <?php } ?>
                </div>
                <div class="clear"></div>
                <?php if(!empty($webd_schedu)){ ?>
                    <h3 class="h3-ev-schedu"><?php echo get_option('webd_text_schedule')!='' ? get_option('webd_text_schedule') : esc_html__('Schedule','WEBDWooEVENT')?></h3>
                    <div class="webd-sche-detail ev-schedu">
                        <?php foreach($webd_schedu as $item){ ?>
                                <p><?php echo $item; ?></p>
                                <?php 
                        }?>
                    </div>
                <?php }?>
            </div>
            <div class="col-md-6 col-sm-6">
                <?php if(get_option('webd_single_map') =='yes'){
					if(get_post_meta( $post->ID, 'webd_latitude_longitude', true )!=''){
						$addre = $webd_latitude_longitude = get_post_meta($post->ID,'webd_latitude_longitude', true );
						$webd_latitude_longitude = explode(',',$webd_latitude_longitude);
						if(isset($webd_latitude_longitude[0]) && $webd_latitude_longitude[0] !=''){

							$apiRequest = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($webd_latitude_longitude[0]).','.trim($webd_latitude_longitude[1]).'&sensor=false';
							$output = wp_remote_get( 'https://api.github.com/users/blobaugh' );
							$status = $output->status;
							$addre = ($status=="OK")?$output->results[1]->formatted_address:'';
						}
					}else{
						$addre = get_post_meta( $post->ID, 'webd_adress', true );
					}
					if($addre!=''){
						$webd_zoom_map = get_option('webd_zoom_map');
						$webd_zoom_map = $webd_zoom_map!='' && is_numeric($webd_zoom_map) ? $webd_zoom_map : 10;
						$webd_map_lg = get_option('webd_map_lg')!='' ? get_option('webd_map_lg') : 'en';
						?>
						<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0"width="100%" height="100%" src="https://maps.google.com/maps?hl=<?php echo esc_attr($webd_map_lg);?>&q=<?php echo ($addre);?>&ie=UTF8&t=roadmap&z=<?php echo esc_attr( $webd_zoom_map );?>&iwloc=B&output=embed"></iframe>
						<?php
					}
				}else{ echo do_shortcode('[webd_map ids="'.get_the_ID().'" height="300"]');}?>
            </div>
        </div>
    </div>
<?php }
$off_ssocial = get_option('webd_ssocial');
if($off_ssocial!='off'){
	?>
	<div class="webd-social-share col-md-12">
		<div class="row">
			<?php echo  webd_social_share();?>
		</div>
	</div>
<?php }?>
<div class="clear"></div>