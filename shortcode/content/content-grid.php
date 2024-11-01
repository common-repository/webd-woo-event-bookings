<?php
global $columns,$number_excerpt,$show_time,$orderby,$img_size;
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
				<?php echo get_the_post_thumbnail($parent_id,$img_size);?>
            </a> 
            <?php echo $html_baget;?>
		</div>
		<div class="grid-content <?php if($html_baget!=''){ echo 'gr-withbg';}?>">
			<figcaption>
            	<?php if($webd_startdate!=''){?>
				<div class="date" <?php echo $bgev_color;?>><span class="day"><?php echo date_i18n( 'd', $webd_startdate); ?></span><span class="month"><?php echo date_i18n('M', $webd_startdate); ?></span></div>
                <?php }?>
				<h3>
					<?php webd_event_label_html();?>
					<a href="<?php the_permalink(); ?>" class="link-more">
					<?php  if($orderby=='has_submited'){
						$title = get_the_title();
						$ft_ch = explode(":",$title);
						if(!isset($ft_ch[0])){ $ft_ch[0] ='';}
						if(get_post_status()=='pending'){
							$pd = get_option('webd_text_pending');
							$pd = $pd!='' ? $pd : esc_html__('[pending] ','WEBDWooEVENT');
							echo '<span>'.$pd.'</span>'.str_replace($ft_ch[0],'',$title);
						}elseif(get_post_status()=='trash'){
							$tsh = get_option('webd_text_trash');
							$tsh = $tsh!='' ? $tsh : esc_html__('[trash] ','WEBDWooEVENT');
							echo '<span>'.$tsh.'</span>'.str_replace($ft_ch[0],'',$title);
						}else{
							the_title();
						}
					}else{ the_title(); }?>
                </a><?php webd_subtitle_html($parent_id);?></h3>
				<div class="webd-more-meta">
				<?php
					if($webd_startdate!=''){
						$st_d = date_i18n( 'd', $webd_startdate);
						$st_m = date_i18n( 'F', $webd_startdate);
						$sttime = '';
						if($show_time=='1'){
							$sttime = ' - '.date_i18n(get_option('time_format'), $webd_startdate);
						}
						echo '<span><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
					}
					if($webd_enddate!=''){
                        $e_d = date_i18n( 'd', $webd_enddate);
						$e_m = date_i18n( 'F', $webd_enddate);
						if( ($st_d != $e_d) || ($st_m != $e_m) ){
	                        $edtime = '';
	                        if($show_time=='1'){
	                            $edtime = ' - '.date_i18n(get_option('time_format'), $webd_enddate);
	                        }
	                        echo '<span class="webd-ed-gr"><i class="fa fa-calendar-times-o"></i>'.date_i18n( get_option('date_format'), $webd_enddate).$edtime.'</span>';
	                    }
                    }
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
				<div class="grid-excerpt"><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,$more = '...');?></div>
                <?php }
                do_action( 'webd_after_grid_content_html' );
                ?>
			</figcaption>
			<div class="ex-social-share" <?php if($columns!=1){ echo $bgev_color;}?> id="ex-social-<?php echo get_the_ID();?>">
				<?php 
				if($columns==1 && $webd_eventcolor!=''){
					echo '<style type="text/css" scoped>
					.webd-grid-shortcode.webd-grid-column-1 figure.ex-modern-blog .ex-social-share#ex-social-'.get_the_ID().' ul li a{ background-color:'.$webd_eventcolor.'}
					</style>';
				}
				echo webd_social_share();?>
			</div>
		</div>
	</figure>    
</div>