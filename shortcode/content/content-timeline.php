<?php
global $number_excerpt;
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
if(get_post_type(get_the_ID()) == 'product_variation') {
    $variation = wc_get_product(get_the_ID());
    $parent_id = $variation->get_parent_id();
}else{ $parent_id = get_the_ID();}
$webd_adress = get_post_meta( $parent_id, 'webd_adress', true );
$webd_status = woo_event_status( get_the_ID(), $webd_enddate);
if(get_option('webd_dis_status') =='yes'){ $webd_status ='';}
$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($parent_id),'full' );
$bg_style = 'style="background-image:url('.esc_url($image_src[0]).');"';

$webd_eventcolor = webd_event_custom_color(get_the_ID());
if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
$bgev_color = $bd_cl = '';
if($webd_eventcolor!=""){
	$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
	$bd_cl =  $webd_eventcolor;
}
?>
<li id="tlct-<?php the_ID();?>">
    <div class="timeline-content">
    	 <?php
         if($webd_startdate!=''){ ?>
    	<div class="tl-tdate" <?php echo $bgev_color!='' ? $bgev_color : '';?>>
            <span class="tlday"><?php echo date_i18n( 'd', $webd_startdate)?></span>
            <div>
                <span><?php echo date_i18n( 'l', $webd_startdate)?></span>
                <span><?php echo date_i18n( 'F, Y', $webd_startdate)?></span>
            </div>
            <?php
			if($bd_cl!=''){
				?>
				<style>
				.webd-timeline-shortcode ul li#tlct-<?php the_ID();?> .timeline-content:before {
					border-left-color: transparent;
					border-right-color:<?php echo $bd_cl;?>
				}
				@media screen and (min-width: 768px) {
					.webd-timeline-shortcode ul li#tlct-<?php the_ID();?>:nth-child(odd) .timeline-content:before{
						border-right-color: transparent;
						border-left-color:<?php echo $bd_cl;?>;
					}
				}
				</style>
				<?php
			}
			 ?>
        </div>
        <?php }?>
        <?php
		if(1==10){?>
			<a class="img-left" href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>">
				<span class="info-img"><?php the_post_thumbnail('wethumb_460x307');?></span>
			</a>
		<?php }?>
        <div class="webd-more-meta" <?php echo $bg_style;?>>
        	<div class="bg-inner">
                <?php webd_event_label_html();?>
                <h3><a href="<?php the_permalink(); ?>" class="link-more"><?php the_title();?></a>
                <?php webd_subtitle_html($parent_id);?>
                </h3>
                <?php
                    if($price!=''){
                        echo  '<span><i class="fa fa-shopping-basket"></i>'.$price.'</span>';
                    }
                    if($webd_status!=''){
                        echo '
                        <span>
                            <i class="fa fa-ticket"></i>
                            '.$webd_status.'
                        </span>';
                    }
                ?>
                <div class="timeline-excerpt"><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,$more = '...');?></div>
            </div>
        </div>
    </div>
    <div class="tl-point" <?php echo $bgev_color!='' ? $bgev_color : '';?>></div>
    <div class="tl-readmore-center">
        <a href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>">
            <?php echo get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</li>