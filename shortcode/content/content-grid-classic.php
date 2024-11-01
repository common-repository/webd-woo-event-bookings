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
		<div class="image <?php if($html_baget!=''){ echo 'gr-withbg';}?>">
			<a href="<?php the_permalink(); ?>" class="link-more">
				<?php echo get_the_post_thumbnail($parent_id,$img_size);?>
                <?php if($webd_startdate!=''){?>
				<div class="shop-webd-stdate" <?php echo $bgev_color;?>><span class="day"><?php echo date_i18n( 'd', $webd_startdate); ?></span><span class="month"><?php echo date_i18n('M', $webd_startdate); ?></span></div>
                <?php }?>
            </a>
            <?php echo $html_baget;?>  
		</div>
		<div class="grid-content">
			<figcaption>
				<?php webd_event_label_html();?>
				<div class="shop-webd-more-meta">
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
                <h3><a href="<?php the_permalink(); ?>" class="link-more">
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
                <?php if($number_excerpt!='0'){?>
				<div class="grid-excerpt"><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,$more = '...');?></div>
                <?php }?>
                <a class="btn btn btn-primary webd-button" <?php echo $bgev_color;?> href="<?php the_permalink();?>">
					<?php echo get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');?>
                </a>
                <?php do_action( 'webd_after_grid_content_html' );?>
                <div class="clear"></div>
			</figcaption>
		</div>
        
	</figure>    
</div>