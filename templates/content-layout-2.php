<?php
global $woocommerce, $post,$webd_main_purpose;
$webd_startdate = webd_global_startdate() ;
?>
<div class="webd-content-custom col-md-12">
	<?php
    if($webd_main_purpose=='woo'){
         wooevent_template_plugin('gallery');
    }else{?>
    <div class="webd-info-top">
        <div class="event-details">
        	<div class="event-info-left">
        		<?php webd_event_label_html( $post->ID )?>
            	<h1 class="ev-title">
                	<?php the_title();?>
                </h1>
                <?php 
				global $product;
				$type = $product->get_type();
				$price ='';
				if($type=='variable'){
					$price = webd_variable_price_html();
				}else{
					  if ( $price_html = $product->get_price_html() ) :
						  $price = $price_html; 
					  endif; 	
				}?>
                <h3 class="event-price"><?php echo $price;?></h3>
                <div class="button-scroll btn btn-primary"><?php 
				$webd_text_join_now = get_option('webd_text_join_now');
				if($webd_text_join_now!=''){
					echo $webd_text_join_now;
				}else{
					esc_html_e('Join Now','WEBDWooEVENT');
				}
				?>
                </div>
            </div>
            <div class="event-info-right">
				<?php wooevent_template_plugin('event-meta'); ?>
            </div>
    	</div>
	</div>
    <?php }?>
    <div class="content-dt"><?php echo wpautop(apply_filters('the_content',get_the_content($post->ID)));?></div>
	<style type="text/css">.woocommerce .webd-main.layout-2 .images{ display:none !important}</style>
</div>
<div class="clear"></div>