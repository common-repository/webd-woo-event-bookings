<?php
$webd_startdate = get_post_meta( get_the_ID(), 'webd_startdate', true );
$webd_enddate = get_post_meta( get_the_ID(), 'webd_enddate', true )  ;
global $img_size,$show_time,$style,$number_excerpt;
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
global $img_size;
$_image = get_the_post_thumbnail($parent_id,$img_size);
if(get_post_type(get_the_ID()) == 'product_variation') {
	$variation = wc_get_product(get_the_ID());
	$parent_id = $variation->get_parent_id();
	if($variation->get_image_id()!='' ){
		$_image = $variation->get_image($img_size);
	}
}else{ $parent_id = get_the_ID();}
$webd_adress = get_post_meta( $parent_id, 'webd_adress', true );
$webd_status = woo_event_status( get_the_ID(), $webd_enddate);
if(get_option('webd_dis_status') =='yes'){ $webd_status ='';}
$webd_eventcolor = webd_event_custom_color(get_the_ID());
if($webd_eventcolor==''){$webd_eventcolor = webd_autochange_color();}
$bgev_color = '';
if($webd_eventcolor!=""){
	$bgev_color = 'style="background-color:'.$webd_eventcolor.'"';
}
$html_baget = webd_badget_html();
?>
<div class="item-post-n">
	<figure class="ex-modern-blog">
		<div class="image">
        	<?php echo ex_cat_info('','product', '', true);?>
        	<a href="<?php the_permalink(); ?>" class="link-more">
				<?php echo $_image;?>
            </a>
            <?php echo $html_baget;?>
		</div>
		<div class="grid-content <?php if($html_baget!=''){ echo 'gr-withbg';}?>">
			<figcaption>
            	<?php if($webd_startdate!=''){?>
				<div class="date" <?php echo $bgev_color;?>><span class="day"><?php echo date_i18n( 'd', $webd_startdate); ?></span><span class="month"><?php echo date_i18n('M', $webd_startdate); ?></span></div>
				<?php webd_event_label_html();?>
                <?php }
				if($style == 'webd-car-modern'){?>
                	<div class="webd-ca-title">
						<h3><a href="<?php the_permalink(); ?>" class="link-more"><?php the_title();?></a></h3>
                        <?php webd_subtitle_html($parent_id);?>
                        <?php
                        if($webd_startdate!=''){ 
							$sttime = '';
							if($show_time=='1'){
								$sttime = ' - '.date_i18n(get_option('time_format'), $webd_startdate);
							}
							echo '<span>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
						}
						?>
                    </div>
                <?php }else{?>
                	<h3><a href="<?php the_permalink(); ?>" class="link-more"><?php the_title();?></a></h3>
                    <?php webd_subtitle_html($parent_id);?>
                <?php }?>
				<div class="webd-more-meta">
				<?php
					if($webd_startdate!='' && $style != 'webd-car-modern'){ 
						$sttime = '';
						if($show_time=='1'){
							$sttime = ' - '.date_i18n(get_option('time_format'), $webd_startdate);
						}
						echo '<span><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
					}
					if($webd_adress!='' && $style == 'webd-car-modern'){?>
				  		<span class="tb-meta"><i class="fa fa-map-marker"></i> <?php echo $webd_adress;?></span>
			  		<?php }
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
				</div>
                <?php if($number_excerpt!='0'){?>
				<div class="grid-excerpt"><?php echo wp_trim_words($number_excerpt,$more = '...');?></div>
                <?php }
                do_action( 'webd_after_carousel_content_html' );	
                ?>
			</figcaption>
		</div>
	</figure>    
</div>