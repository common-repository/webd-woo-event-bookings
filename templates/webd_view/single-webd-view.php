<?php
get_header();
?>
<div class="webd-content-webd_view spk-single">
	<?php
	// Start the loop.
	while ( have_posts() ) : the_post();
		$webd_custom_metadata = get_post_meta( get_the_ID(), 'webd_custom_metadata', false );?>
        <div class="webd-info-sp">
            <div class="row">
            <div class="col-md-4">
            <div class="webd_view-avatar">
                <?php if(has_post_thumbnail()){?>
                <div class="img-spk"><?php the_post_thumbnail('wethumb_300x300');?></div>
                <?php } ?>
                <span><?php echo get_post_meta( get_the_ID(), 'webd_view_position', true );?></span>
            </div>
            </div>
            <div class="col-md-8">
            <div class="webd_view-details">
                <h3 class="webd_view-title">
                    <?php the_title();?>
                </h3>
                <?php if(is_array($webd_custom_metadata) && !empty($webd_custom_metadata)){
					$number = count($webd_custom_metadata);?>
                    <div class="webd-custom-meta-info">
                    <div class="row">
                    	<?php 
						$i = 0;
						foreach($webd_custom_metadata as $item){
							$i++;?>
                        	<div class="col-md-6 col-sm-6">
                                <div class="media">
                                    <div class="media-body">
                                        <div class="custom-details">
                                            <span class="sub-lb webd-sub-lb"><?php echo $item['webd_custom_title'];?></span>
                                            <span class="webd-sub-ct media-heading"><?php echo $item['webd_custom_content'];?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        	<?php 
							if($i%2==0){?>
							</div>
                            <div class="row">	
							<?php }
						}?>
                    </div>
                    </div>
                <?php }?>
                <div class="webd_view-info">
                    <div class="webd_view-content"><?php the_content();?></div>
                </div>
                <div class="webd-social-share"><?php  echo webd_view_print_social_accounts();?></div>
                <div class="webd_view-event-list">
                    <h3 class="webd_view-title"><?php echo get_option('webd_text_upc')!='' ? get_option('webd_text_upc') : esc_html__('Upcoming Events','WEBDWooEVENT');?></h3>
                    <?php 
                    $getlist = woo_event_query('product', 999, 'ASC', 'date', '', '', '', '', '', get_the_ID());
                    $getlist = new WP_Query($getlist);
                    if($getlist->have_posts()){
                        $post_ids = wp_list_pluck( $getlist->posts, 'ID' );
                        if(!empty($post_ids)){
                            $args = array(
                                'post_type' => 'product',
                                'posts_per_page' => -1,
                                'post_status' => 'publish',
                                'post__in' =>  $post_ids,
                                'ignore_sticky_posts' => 1,
                            );
                            $args['orderby']= 'meta_value_num';
                            $args['order']= 'ASC';
                            $args['meta_key']= 'webd_startdate';
                            $args['meta_query'] = array(
                                array(
                                    'key'  => 'webd_startdate',
                                    'value' => strtotime("now"),
                                    'compare' => '>'
                                )
                            );
                        }
                        $args = apply_filters( 'webd_spk_args_qr', $args );
                        $getlist = new WP_Query($args);
                        if($getlist->have_posts()){
                            $post_ids = wp_list_pluck( $getlist->posts, 'ID' );
                            if(!empty($post_ids)){
                                echo do_shortcode('[webd_table orderby="post__in" order="ASC" style="1" ids="'.implode( ',', $post_ids ).'" count="100" posts_per_page="5"]');
                            }else{
                                $text = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
                                echo '<p>'.$text.'</p>';
                            }
                        }else{
                            $text = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
                            echo '<p>'.$text.'</p>';
                        }
                    }else{
                        $text = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
                        echo '<p>'.$text.'</p>';
                    }?>
                </div>
            </div>
            </div>
            </div>
        </div>
		<?php
	endwhile;?>
</div>
<?php get_footer(); ?>
