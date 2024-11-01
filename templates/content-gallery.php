<?php
global $product,$post;
$images = $product->get_gallery_attachment_ids();
$attachment_count = count( $images );
if ( has_post_thumbnail() || $attachment_count > 0) {
	if($attachment_count > 0){
		$images = $images;
	}else{
		$images = array(get_post_thumbnail_id());
	}
	?>
    <div class="webd-gallery" >
        <div class="is-carousel single-carousel post-gallery content-image carousel-has-control product-ct" id="post-gallery-<?php the_ID() ?>" data-navigation=1>
        <?php
        foreach($images as $attachment_id){
            if(is_numeric($attachment_id)){
                $thumbnail = wp_get_attachment_image_src($attachment_id,'full', true); 
            }else{
                $thumbnail[0] = $attachment_id;
                $thumbnail[2] = $thumbnail[1]='';
                $attachment_id = rand(0, 100000);
            }
            ?>
            <div class="single-gallery-item single-gallery-item-<?php echo esc_attr($attachment_id) ?>">
                <a href="<?php echo esc_url(get_permalink($attachment_id)); ?>" class="colorbox-grid" data-rel="post-gallery-<?php the_ID() ?>" data-content=".single-gallery-item-<?php echo esc_attr($attachment_id) ?>">
                <img src='<?php echo esc_url($thumbnail[0]); ?>'>
                </a>
                <div class="hidden">
                    <div class="popup-data dark-div">
                        <img src="<?php echo esc_url($thumbnail[0]) ?>" width="<?php echo esc_attr($thumbnail[1]) ?>" height="<?php echo esc_attr($thumbnail[2]) ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>">
                        <div class="popup-data-content">
                            <?php if(is_numeric($attachment_id)){ ?>
                            <h5><a href="<?php echo esc_url(get_permalink($attachment_id)); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title($attachment_id); ?></a></h5>
                            <?php } ?>
                        </div>
                    </div>
                </div><!--/hidden-->
            </div>
        <?php }//foreach attachments ?>
        </div><!--/is-carousel-->
    </div>
    <div class="clear"></div>
	<?php
}