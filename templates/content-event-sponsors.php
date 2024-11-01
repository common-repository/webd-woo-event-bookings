<?php
global $woocommerce, $post;
$webd_sponsors = get_post_meta( $post->ID, 'webd_sponsors', false );
if(is_array($webd_sponsors) && !empty($webd_sponsors)){?>
<div class="clear"></div>
<div class="webd-woo-event-schedu woo-sponsors col-md-12">
	<h3><?php echo get_option('webd_text_spon')!='' ? get_option('webd_text_spon') : esc_html__('Sponsors','WEBDWooEVENT')?></h3>
    <div class="event-sponsors">
        <div class="is-carousel" data-items="6" data-autoplay=1  data-navigation=1 data-pagination=0>
            <?php 
            foreach($webd_sponsors as $item){
                if(isset($item['webd_sponsors_link']) && $item['webd_sponsors_link']!=''){?>
                    <div class="item-sponsor">
                        <?php echo '<a href="'.esc_url($item['webd_sponsors_link']).'" target="_blank">'.wp_get_attachment_image( $item['webd_sponsors_logo'], 'full' ).'</a>'; ?>
                    </div>
                <?php }else{?>
                    <div class="item-sponsor">
                        <?php echo wp_get_attachment_image( $item['webd_sponsors_logo'], 'full' ); ?>
                    </div>
                <?php 
                }
            }?>
        </div>
    </div>
</div>
<?php }?>