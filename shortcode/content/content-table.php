<?php
global $style,$show_time,$show_atc,$show_thumb,$show_spk;
global $ajax_load;
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
if( $webd_eventcolor!="" && $style=='3' ){
	$bgev_color = 'style="border-left:10px solid; border-left-color:'.$webd_eventcolor.'"';
}
$webd_category = webd_taxonomy_info('product_cat','on');

$bg_img ='';
if($show_thumb=='1'){
	$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($parent_id),'full' );
	if(isset($image_src[0]) && $image_src[0]!=''){
		$bg_img = 'style="background-image:url('.esc_url($image_src[0]).'); background-size: cover; background-repeat: no-repeat; background-position: center center;"';
	}
}
$cl_tp = 'product-type-'.$type;
if($style!='2' && $style!='3'){ ?>
	<tr class="<?php if(isset($ajax_load) && $ajax_load ==1){?>tb-load-item de-active<?php } echo esc_attr($cl_tp);?>" >
		<td class="webd-first-row <?php echo $show_thumb=='1' ? 'show-bg' : '';?>" <?php echo $bg_img!='' ? $bg_img : '';?> data-sort="<?php echo esc_attr($webd_startdate);?>"><?php if($webd_startdate!=''){ 
			$sttime = '';
			if($show_time=='1'){
				$sttime = ' - '.date_i18n(get_option('time_format'), $webd_startdate);
			}
			echo '<span>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
		} ?></td>
		<td data-sort="<?php echo esc_attr(get_the_title());?>">
			<?php webd_event_label_html();?>
			<h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
        	<?php webd_subtitle_html($parent_id);?>
			<span class="event-meta webd-hidden-screen">
			  <?php if($webd_adress!=''){?>
				  <span class="tb-meta tb-addre"><i class="fa fa-map-marker"></i> <?php echo $webd_adress;?></span>
			  <?php }if($price!=''){?>
				  <span class="tb-meta tb-pric"><i class="fa fa-shopping-basket"></i><?php echo $price;?></span>
			  <?php }if($webd_status!=''){?>
				  <span class="tb-meta tb-status"><i class="fa fa-ticket"></i> <?php echo $webd_status;?></span>
			  <?php }
			  $webd_webd_views = get_post_meta( get_the_ID(), 'webd_webd_views', true );
			  if($show_spk=='yes' && !empty($webd_webd_views)){
			  	$spk_so = $spk = '';
			  	$i = 0;
			  	foreach($webd_webd_views as $webd_view){
			  		$i++;
			  		$spk .= '<a href="'.get_permalink($webd_view).'">'.get_the_title($webd_view).'</a>';
			  		if($i > 0 && $i != count($webd_webd_views)){ $spk .= ', ';}
			  		if($i ==1) { $spk_so = get_the_title($webd_view);}
			  	}?>
				  <span class="tb-meta tb-spk"><i class="fa fa-users"></i> <?php echo $spk;?></span>
			  <?php }?>
			</span>
		</td>
		<td class="webd-mb-hide" data-sort="<?php echo esc_attr($webd_adress);?>"><?php echo $webd_adress;?></td>
		<td class="tb-price webd-mb-hide" data-sort="<?php echo esc_attr($product->get_price());?>"><span><?php echo $price;?></span></td>
		<?php if($webd_status!=''){?>
        <td class="webd-mb-hide" data-sort="<?php echo esc_attr($webd_status);?>"><?php echo $webd_status;?></td>
        <?php }?>

        <?php if($show_atc==1){?>
        <td class="webd-mb-hide">
			<?php 
			$stock_status = get_post_meta(get_the_ID(), '_stock_status',true);
			if($show_atc==1 && $stock_status !='outofstock'){
				$variations = '';
				$product = wc_get_product (get_the_ID());
				if($product!==false) { $variations = $product->get_type();}
				//$atts['id']= get_the_ID();
				$url = $product->add_to_cart_url() ;
				if($variations == 'variable'){
					$tbt = get_option('webd_text_sl_op')!='' ? get_option('webd_text_sl_op') : esc_html__('Select options','WEBDWooEVENT');
				}else if($url== get_the_permalink()){
					$tbt = get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');
				}else{
					global $url_page;
					if(isset($url_page) && $url_page!=''){
						$url = add_query_arg( array( 'add-to-cart' => get_the_ID() ),$url_page );
					}
					$tbt = get_option('webd_text_add_to_cart')!='' ? get_option('webd_text_add_to_cart') : esc_html__('Add to cart','WEBDWooEVENT');
				}?>
                <a class="btn btn btn-primary webd-button" <?php echo $bgev_color;?> href="<?php echo esc_url($url);?>">
                	<?php echo $tbt;?>
                </a>
                <?php
			}else{?>                              
                <a class="btn btn btn-primary webd-button" <?php echo $bgev_color;?> href="<?php the_permalink();?>"><?php echo get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');?></a>
            <?php } ?>
        </td>
        <?php }?>
        <?php if($show_spk=='yes'){?>
        <td class="webd-mb-hide" data-sort="<?php echo esc_attr($spk_so);?>"><?php echo $spk;?></td>
        <?php }?>
	</tr>
<?php }else{?>
	<tr class="<?php if(isset($ajax_load) && $ajax_load ==1){?>tb-load-item de-active<?php } echo esc_attr($cl_tp);?>">
        <td class="webd-first-row" <?php echo $bg_img!='' ? $bg_img : $bgev_color;?>>
		<?php if($webd_startdate!=''){ 
			$st_d = date_i18n( 'd', $webd_startdate);
			$st_m = date_i18n( 'F', $webd_startdate);
			if($webd_enddate!=''){
				$e_d = date_i18n( 'd', $webd_enddate);
				$e_m = date_i18n( 'F', $webd_enddate);
				
				if( ($st_d != $e_d) || ($st_m != $e_m) ){
					if($st_m != $e_m){
						echo '
						<span class="tb2-day tb-small">'.$st_d.'</span>
						<span class="tb2-month tb-small" style="display: block;"> '.$st_m.'</span>';	
					}else{
						echo '<span class="tb2-day tb-small">'.$st_d.' - '.$e_d.'</span>';	
					}
				}else{
					echo '<span class="tb2-day">'.$st_d.' </span>';	
				}
				
				if($st_m != $e_m){
					echo '
					<span> - </span><span class="tb2-day tb-small">'.$e_d.'</span>
					<span class="tb2-month tb-small" style="display: block;">'.$e_m.'</span>';
				}else{
					echo '<span class="tb2-month">'.$st_m.'</span>';
				}
			}else{
				echo '<span class="tb2-day">'.$st_d.' </span>';	
				echo '<span class="tb2-month">'.$st_m.'</span>';
			}
		} ?>
		</td>
		<td>
			<?php webd_event_label_html();?>
			<h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
            <?php webd_subtitle_html($parent_id);?>
            <?php
            $webd_webd_views = get_post_meta( get_the_ID(), 'webd_webd_views', true );
			if($show_spk=='yes' && !empty($webd_webd_views)){
				$spk_so = $spk = '';
				$i = 0;
				foreach($webd_webd_views as $webd_view){
					$i++;
					$spk .= '<a href="'.get_permalink($webd_view).'">'.get_the_title($webd_view).'</a>';
					if($i > 0 && $i != count($webd_webd_views)){ $spk .= ', ';}
					if($i ==1) { $spk_so = get_the_title($webd_view);}
				}?>
				<p class="tb-spk">
				<span><?php if($webd_text_webd_view!=''){ echo esc_attr($webd_text_webd_view);}else{ echo esc_html__('Speaker','WEBDWooEVENT');}?>: </span>
				<?php echo $spk;?>
				</p>
			<?php }?>
			<span class="event-meta">
			  <?php 
			  if($webd_startdate!=''){
				  $sttime = '';
				  if($show_time=='1'){
					  $sttime = ' - '.date_i18n(get_option('time_format'), $webd_startdate);
					  $edtime = ' - '.date_i18n(get_option('time_format'), $webd_enddate);
				  }
				  echo '<span class="tb-meta"><i class="fa fa-calendar"></i>'.date_i18n( get_option('date_format'), $webd_startdate).$sttime.'</span>';
				if($webd_enddate!=''){
					$e_d = date_i18n( 'd', $webd_enddate);
					$e_m = date_i18n( 'F', $webd_enddate);
					
					if( ($st_d != $e_d) || ($st_m != $e_m) ){
					  	echo '<span class="tb-meta"><i class="fa fa-calendar-times-o"></i>'.date_i18n( get_option('date_format'), $webd_enddate).$edtime.'</span>';
					}
				}
			  }
			  if($webd_adress!=''){?>
				  <span class="tb-meta"><i class="fa fa-map-marker"></i> <?php echo $webd_adress;?></span>
			  <?php }if($price!=''){?>
				  <span class="tb-meta"><i class="fa fa-shopping-basket"></i><?php echo $price;?></span>
			  <?php }if($webd_status!=''){?>
				  <span class="tb-meta"><i class="fa fa-ticket"></i> <?php echo $webd_status;?></span>
			  <?php }if($webd_category!=''){?>
				  <span class="tb-meta-cat " <?php echo $webd_eventcolor!='' ? 'style="border-left-color:'.$webd_eventcolor.'"' : '';?>><?php echo $webd_category;?></span>
			  <?php }?>
			</span>
		</td>
		<td class="tb-viewdetails">
            <span>
                <?php 
				$stock_status = get_post_meta(get_the_ID(), '_stock_status',true);
				if($show_atc==1 && $stock_status !='outofstock'){
					$variations = '';
					$product = wc_get_product (get_the_ID());
					if($product!==false) { $variations = $product->get_type();}
					//$atts['id']= get_the_ID();
					$url = $product->add_to_cart_url() ;
					if($variations == 'variable'){
						$tbt = get_option('webd_text_sl_op')!='' ? get_option('webd_text_sl_op') : esc_html__('Select options','WEBDWooEVENT');
					}else if($url== get_the_permalink()){
						$tbt = get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');
					}else{
						global $url_page;
						if(isset($url_page) && $url_page!=''){
							$url = add_query_arg( array( 'add-to-cart' => get_the_ID() ),$url_page );
						}
						$tbt = get_option('webd_text_add_to_cart')!='' ? get_option('webd_text_add_to_cart') : esc_html__('Add to cart','WEBDWooEVENT');
					}?>
                    <a class="btn btn btn-primary webd-button" <?php echo $bgev_color;?> href="<?php echo esc_url($url);?>">
                    	<?php echo $tbt;?>
                    </a>
                    <?php
				}else{?>                              
                    <a class="btn btn btn-primary webd-button" <?php echo $bgev_color;?> href="<?php the_permalink();?>"><?php echo get_option('webd_text_viewdetails')!='' ? get_option('webd_text_viewdetails') : esc_html__('View Details','WEBDWooEVENT');?></a>
                <?php } ?>
            </span>
		</td>
	</tr>
<?php }
