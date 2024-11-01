<?php
global $event_items;
$check_ev = false;
foreach ( $event_items as $item ) {
	$product_id = $item['product_id'];
	$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true );
	$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true );
	if($webd_startdate!=''){
		$check_ev = true;
		break;
	}
}
if($check_ev == true){
	?>
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:left;"><?php echo get_option('webd_text_evname')!='' ? get_option('webd_text_evname') : esc_html__( 'Event Name', 'WEBDWooEVENT' ); ?></th>
				<th class="td" scope="col" style="text-align:left;"><?php echo get_option('webd_text_evdate')!='' ? get_option('webd_text_evdate') : esc_html__( 'Event Date', 'WEBDWooEVENT' ); ?></th>
				<th class="td" scope="col" style="text-align:left;"><?php echo get_option('webd_text_evlocati')!='' ? get_option('webd_text_evlocati') : esc_html__( 'Event Location', 'WEBDWooEVENT' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $event_items as $item ) {
					$product_name = $item['name'];
					$product_id = $item['product_id'];
					$product_variation_id = $item['variation_id'];
					$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true );
					$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true );
					$all_day = get_post_meta($product_id,'webd_allday', true );
					if($webd_startdate!=''){
					?>
					<tr>
						<td class="td" scope="col" style="text-align:left;"><?php echo $item['name'];?></td>
						<td class="td" scope="col" style="text-align:left;">
							<span class="">
                            <b><?php echo get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') : esc_html__('Start Date','WEBDWooEVENT');?>: </b>
							<?php
							echo date_i18n( get_option('date_format'), $webd_startdate).' ';
                            if(($webd_enddate=='') || ($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate)))){ 
                                echo date_i18n(get_option('time_format'), $webd_startdate);
                            }
							?>
                            </span><br>
							<span class=""><b><?php echo get_option('webd_text_edate')!='' ? get_option('webd_text_edate') : esc_html__('End Date','WEBDWooEVENT');?>: </b>
							<?php
							echo date_i18n( get_option('date_format'), $webd_enddate);
                            if($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate))){ 
                                echo ' '.date_i18n(get_option('time_format'), $webd_enddate);
                            }elseif($all_day=='1'){ 
								$alltrsl = get_option('webd_text_allday')!='' ? get_option('webd_text_allday') : esc_html__('(All day)','WEBDWooEVENT');
								echo '<span> '.$alltrsl.'</span>';
							}?>
                            </span><br>
                            <?php 
							$webd_show_timezone = get_option('webd_show_timezone');
							$webd_time_zone = get_post_meta($product_id,'webd_time_zone', true );
							if($webd_show_timezone=='yes' && $webd_time_zone!='' && $webd_time_zone!='def'){?>
                            <span class=""><b><?php echo get_option('webd_text_timezone_')!='' ? get_option('webd_text_timezone_') : esc_html__('Timezone','WEBDWooEVENT');?>: </b>
                            	<?php 
								if (strpos($webd_time_zone, '-') !== false) {
									echo 'UTC'.$webd_time_zone;
								}else{
									echo 'UTC+'.$webd_time_zone;
								}?>
                            </span><br>
                            <?php }?>
						</td>
						<td class="td" scope="col" style="text-align:left;"><?php echo get_post_meta( $product_id, 'webd_adress', true );?></td>
					</tr>
					<?php
					}else{
						$product_id = wp_get_post_parent_id( $product_id );
						$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true );
						$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true );
						if($webd_startdate!=''){
							?>
							<tr>
								<td class="td" scope="col" style="text-align:left;"><?php echo get_the_title($product_id);?></td>
								<td class="td" scope="col" style="text-align:left;">
									<span class=""><b><?php echo get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') : esc_html__('Start Date','WEBDWooEVENT');?>: </b><?php echo date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate);?></span><br>
									<span class=""><b><?php echo get_option('webd_text_edate')!='' ? get_option('webd_text_edate') : esc_html__('End Date','WEBDWooEVENT');?>: </b><?php echo date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);?></span><br>
                                    <?php 
									$webd_show_timezone = get_option('webd_show_timezone');
									$webd_time_zone = get_post_meta($product_id,'webd_time_zone', true );
									if($webd_show_timezone=='yes' && $webd_time_zone!='' && $webd_time_zone!='def'){?>
									<span class=""><b><?php echo get_option('webd_text_timezone_')!='' ? get_option('webd_text_timezone_') : esc_html__('Timezone','WEBDWooEVENT');?>: </b>
										<?php 
										if (strpos($webd_time_zone, '-') !== false) {
											echo apply_filters( 'wooevent_timezone_html', 'UTC'.$webd_time_zone, $webd_time_zone );
										}else{
											echo apply_filters( 'wooevent_timezone_html', 'UTC+'.$webd_time_zone, $webd_time_zone );
										}?>
									</span><br>
									<?php }?>
                                    
								</td>
								<td class="td" scope="col" style="text-align:left;"><?php echo get_post_meta( $product_id, 'webd_adress', true );?></td>
							</tr>
							<?php
						}
					}
					
				} ?>
		</tbody>
		<tfoot>
		</tfoot>
	</table>
	<?php
}