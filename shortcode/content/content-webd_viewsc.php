<?php 
global $img_size,$show_meta;
$webd_custom_metadata = get_post_meta( get_the_ID(), 'webd_custom_metadata', false );
?>
<div class="item-post-n">
	<figure class="ex-modern-blog">
		<div class="image">
        	<a href="<?php the_permalink(); ?>" class="link-more">
				<?php the_post_thumbnail($img_size);?>
            </a>
		</div>
		<div class="grid-content">
			<figcaption>
            	<div class="s-ttname">
                    <h3><a href="<?php the_permalink(); ?>" class="link-more"><?php the_title();?></a></h3>
                    <span><?php echo get_post_meta( get_the_ID(), 'webd_view_position', true );?></span>
                </div>
				<?php if($show_meta == '1' && is_array($webd_custom_metadata) && !empty($webd_custom_metadata)){
					$number = count($webd_custom_metadata);?>
                    <div class="webd-meta-info">
                    	<?php 
						foreach($webd_custom_metadata as $item){?>
                        	<div class="s-ctmeta">
                                <span class="s-title"><?php echo $item['webd_custom_title'];?></span>
                                <span class="s-content"><?php echo $item['webd_custom_content'];?></span>
                            </div>
							<?php
						}?>
                    </div>
                <?php }?>
			</figcaption>
		</div>
	</figure>   
</div>