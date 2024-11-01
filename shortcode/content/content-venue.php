<?php
?>
<div class="item-venue">
	<div class="webd-contai">
        <a href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>"><span class="info-img"><?php the_post_thumbnail('full');?></span>
            <span class="webg-grad"></span>
        </a>
        <div class="vn-info">
            <span class="vn-title"><a href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>"><?php the_title();?></a></span>
            <span class="vn-events"><?php echo webd_get_number_post_by_meta(get_the_ID()).' '.esc_html__('Events','webd');?></span>
        </div>
    </div>
</div>