<?php
class WEBD_WooEvent_Meta {
	public function __construct()
    {
		add_action( 'save_post', array($this,'recurrence_event') );
		add_action( 'init', array($this,'init'), 0);
    }
	function init(){
		add_filter( 'exc_mb_meta_boxes', array($this,'wooevent_metadata') );
	}
	//Recurrence event
	function recurrence_event( $post_id ) {
		$post_id = isset($_POST['post_ID']) && sanitize_text_field($_POST['post_ID'])!='' ? sanitize_text_field($_POST['post_ID']) : $post_id;
		if('product' == get_post_type()){
			do_action('webd_before_create_recurring_event',$post_id,$_POST);
			$_recurr = get_post_meta($post_id,'recurren_ext', true );
			if($_recurr!=''){
				$mrcc_id  = explode("_",$_recurr);
				if(isset($mrcc_id[1]) && $mrcc_id[1]!=''){
					$_ctdate = get_post_meta($mrcc_id[1],'webd_ctdate', false );
					$_crrlist = get_post_meta($mrcc_id[1],'recurren_list', true );
					$_std = get_post_meta($post_id,'webd_startdate', true );
					$_edt = get_post_meta($post_id,'webd_enddate', true );
					$_ald = get_post_meta($post_id,'webd_allday', true );
					if(!empty($_ctdate)){
						foreach($_ctdate as $ix=> $valu){
							if($valu['webd_ct_allday'] == $_ald && $valu['webd_ct_stdate'] == $_std && $valu['webd_ct_edate_end'] == $_edt  ){
								unset($_ctdate[$ix]);
								if (is_array($_crrlist) && ($key = array_search($post_id, $_crrlist)) !== false) {
									unset($_crrlist[$key]);
									update_post_meta( $mrcc_id[1], 'recurren_list', array_values($_crrlist));
								}
								update_post_meta( $post_id, 'recurren_ext', '');
								delete_post_meta( $mrcc_id[1], 'webd_ctdate');
								foreach($_ctdate as $ix=> $valu){
									add_post_meta($mrcc_id[1], 'webd_ctdate', $valu, false);
								}
								break;
							}
						}
					}
				}
				if($_POST['webd_recurrence']['exc_mb-field-0']!= get_post_meta($post_id,'webd_recurrence', true )){
					if($_recurr!=''){
						$args = array(
							'post_type' => 'product',
							'post_status' => 'publish',
							'posts_per_page' => -1,
							'order' => 'ASC',
							'meta_key' => 'recurren_ext',
							'orderby' => 'meta_value_num',
							'meta_query' => array(
								array(
									'key'     => 'recurren_ext',
									'value'   => $_recurr,
									'compare' => '=',
								),
							),
						);
						update_post_meta( $post_id, 'recurren_list', '');
						update_post_meta( $post_id, 'recurren_ext', '');
						$ex_posts = get_posts( $args );
						foreach($ex_posts as $item){
							wp_delete_post($item->ID);
						}
					}
				}
			}
			
		}
		if('product' != get_post_type() || !isset($_POST['webd_recurrence'])) { return;}
		$recurrence 	= sanitize_text_field($_POST['webd_recurrence']);
		$webd_startdate = sanitize_text_field($_POST['webd_startdate']);
		$webd_enddate 	= sanitize_text_field($_POST['webd_enddate']);
		$cv_sd = strtotime(str_replace("/","-",$webd_startdate['exc_mb-field-0']['date']));
		if(isset($webd_startdate['exc_mb-field-0']['date']) && $cv_sd!=''){
			$_POST['webd_startdate']['exc_mb-field-0']['date'] = date("d/m/Y", $cv_sd);
		}
		$cv_sd = strtotime(str_replace("/","-",$webd_enddate['exc_mb-field-0']['date']));
		if(isset($webd_enddate['exc_mb-field-0']['date']) && $cv_sd !=''){
			$_POST['webd_enddate']['exc_mb-field-0']['date'] = date("d/m/Y", $cv_sd);
		}
		if(get_option('webd_date_picker')=='dmy'){
			$_POST['webd_startdate']['exc_mb-field-0']['date'] 	= str_replace(".","-",$webd_startdate['exc_mb-field-0']['date']);
			$_POST['webd_enddate']['exc_mb-field-0']['date'] 	= str_replace(".","-",$webd_enddate['exc_mb-field-0']['date']);
			$_POST['webd_recurrence_end']['exc_mb-field-0'] 	= str_replace(".","-",$_POST['webd_recurrence_end']['exc_mb-field-0'] );
		}
		global $product;
		$ex_recurr = get_post_meta($post_id,'recurren_ext', true );
		if($recurrence['exc_mb-field-0']!= get_post_meta($post_id,'webd_recurrence', true )){
			$recurren_list = get_post_meta($post_id,'recurren_list', true );
			if(!empty($recurren_list)){
				foreach($recurren_list as $idit){
					update_post_meta( $idit, 'recurren_ext', '');
				}
			}
		}
		$attach_id = get_post_thumbnail_id($post_id);
		if(!isset($recurrence['exc_mb-field-0']) || $recurrence['exc_mb-field-0']==''){
			update_post_meta( $post_id, 'recurren_ext', '');
			update_post_meta( $post_id, 'recurren_list', '');
			return;
		}
		if ($recurrence['exc_mb-field-0']=='day' || $recurrence['exc_mb-field-0']=='week' || $recurrence['exc_mb-field-0']=='month') {
			
			$webd_recurrence_end = sanitize_text_field($_POST['webd_recurrence_end']);
			$ev_date 	= (strtotime($webd_enddate['exc_mb-field-0']['date']) - strtotime($webd_startdate['exc_mb-field-0']['date']));
			$c_number 	= $ev_date/86400;
			$date_ed 	=  (strtotime($webd_recurrence_end['exc_mb-field-0'])- strtotime($webd_startdate['exc_mb-field-0']['date']));
			if($recurrence['exc_mb-field-0']=='day'){
				if($ev_date!=0){
					$date_ed = floor($date_ed/($ev_date + 86400));
				}elseif($ev_date==0){$date_ed = $date_ed/86400;}//echo $date_ed;exit;
				$number_plus = $c_number + 1;
			}elseif($recurrence['exc_mb-field-0']=='week'){
				if($ev_date!=0){
					$num_w = $ev_date + 86400*7;
					$date_ed = round($date_ed/$num_w);
				}else{ $date_ed = round($date_ed/(86400*7));}
				$number_plus = 7;
			}elseif($recurrence['exc_mb-field-0']=='month'){
				if($ev_date!=0){
					$n_m = $ev_date + 86400*30;
					$date_ed = round($date_ed/$n_m);
				}else{ $date_ed = round($date_ed/(86400*30));}
				$number_plus = 30;
			}
			if($ex_recurr !=''){
				$ev_stc = get_post_meta($post_id,'webd_startdate', true );
				$ev_edc = get_post_meta($post_id,'webd_enddate', true );
				$ev_recurrence_end = get_post_meta($post_id,'webd_recurrence_end', true );
				$ev_recurrence = get_post_meta($post_id,'webd_recurrence', true );
				$cr_st = strtotime($webd_startdate['exc_mb-field-0']['date'] .' '. $webd_startdate['exc_mb-field-0']['time']);
				$cr_ed = strtotime($webd_enddate['exc_mb-field-0']['date'] .' '. $webd_enddate['exc_mb-field-0']['time']);
				$cr_rec = strtotime($webd_recurrence_end['exc_mb-field-0']);
				$ctmcheck = 0;
				if($ev_stc !=$cr_st || $ev_edc!= $cr_ed || $ev_recurrence_end!= $cr_rec || $ev_recurrence!= $recurrence['exc_mb-field-0']){
					$ctmcheck = 1;
				}
				$args = array(
					'post_type' => 'product',
					'post_status' => get_post_status ( $post_id ),
					'post__not_in' => array( $post_id ),
					'posts_per_page' => -1,
					'order' => 'ASC',
					'meta_key' => 'recurren_ext',
					'orderby' => 'meta_value_num',
					'meta_query' => array(
						array(
							'key'     => 'recurren_ext',
							'value'   => $ex_recurr,
							'compare' => '=',
						),
					),
				);
				$ex_posts = get_posts( $args );
				foreach($ex_posts as $item){
					remove_action( 'save_post', array($this,'recurrence_event' ));
					$stdate = get_post_meta($item->ID,'webd_startdate', true );
					$enddate = get_post_meta($item->ID,'webd_enddate', true );
					if($ctmcheck==1){
						wp_delete_post($item->ID);
					}else{
						webd_update_recurren($_POST,$item->ID,$post_id,$stdate);
						if (class_exists('SitePress') && function_exists('wooevent_wpml_duplicate_product')) {
							wooevent_wpml_duplicate_product( $item->ID, $post_id,$stdate,$enddate );
						}
						if($attach_id!=''){
							set_post_thumbnail( $item->ID, $attach_id );
						}elseif(isset($_POST['_thumbnail_id']) & $_POST['_thumbnail_id']!=''){
							set_post_thumbnail( $item->ID, sanitize_text_field($_POST['_thumbnail_id'] ));
						}
						update_post_meta( $item->ID, 'webd_startdate', $stdate);
						update_post_meta( $item->ID, 'webd_enddate', $enddate);
						update_post_meta( $item->ID, 'recurren_list', '');
						delete_post_meta( $item->ID, 'webd_ctdate');
						
						delete_post_meta( $item->ID, 'webd_recurrence_end');
						delete_post_meta( $item->ID, 'webd_recurrence');
						delete_post_meta( $item->ID, 'webd_ctdate');
						delete_post_meta( $item->ID, 'webd_frequency');
						delete_post_meta( $item->ID, 'webd_weekday');
						delete_post_meta( $item->ID, 'webd_monthday');
						delete_post_meta( $item->ID, 'webd_mweekday');
					}
					remove_action( 'save_post', array($this,'recurrence_event' ));
				}
				if($ctmcheck!=1){
					return;
				}
			}
			if ( current_user_can( 'manage_options' ) ) {
				$p_status = 'publish';
			}else{
				$p_status = 'pending';
			}
			
			for($i = 1; $i <= $date_ed; $i++){
				$attr = array(
				  'post_title'    => sanitize_text_field( $_POST['post_title'] ),
				  'post_content'  => sanitize_text_field($_POST['post_content']),
				  'post_status'   => $p_status,
				  'post_author'   => get_current_user_id(),
				  'post_type'     => 'product',
				  'post_excerpt'  => sanitize_text_field($_POST['excerpt']),
				);
				$number_dt = $i*$number_plus;
				if($recurrence['exc_mb-field-0']=='month'){
					$st_date = strtotime("+".$i." month", strtotime($webd_startdate['exc_mb-field-0']['date']));
				}else{
					$st_date = strtotime("+".$number_dt." day", strtotime($webd_startdate['exc_mb-field-0']['date']));
				}

				$en_date = strtotime("+".$c_number." day", $st_date);
				
				$diff_st = strtotime($webd_startdate['exc_mb-field-0']['date'] .' '. $webd_startdate['exc_mb-field-0']['time'])- strtotime($webd_startdate['exc_mb-field-0']['date']);
				$st_date = $st_date + $diff_st;
				
				$diff_ed = strtotime($webd_enddate['exc_mb-field-0']['date'] .' '. $webd_enddate['exc_mb-field-0']['time'])- strtotime($webd_enddate['exc_mb-field-0']['date']);
				$en_date = $en_date + $diff_ed;
				$br_cre = strtotime($webd_recurrence_end['exc_mb-field-0']) + 86399;
				if($en_date > $br_cre){
					$en_date = strtotime($webd_recurrence_end['exc_mb-field-0'])+ $diff_ed;
				}
				
				if($st_date > $br_cre){
					break;
				}else{
					$arr_ids = $this->create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id);
					update_post_meta( $post_id, 'recurren_list', $arr_ids);
					if (class_exists('SitePress')) {
						wp_update_post( array('ID' => $post_id), false );
					}
				}
			}
		}else if($recurrence['exc_mb-field-0']=='custom'){
			$this->recurring_custom($_POST,$webd_startdate,$webd_enddate,$post_id,$ex_recurr);
		}else{
			return;
		}
	}
	function recurring_custom($data,$webd_startdate,$webd_enddate,$post_id,$ex_recurr){
		$webd_frequency = $data['webd_frequency']['exc_mb-field-0'];
		$attach_id = get_post_thumbnail_id($post_id);
		if(isset($data['_thumbnail_id'])){
			$attach_id = $data['_thumbnail_id'];
		}
		if($ex_recurr !=''){
			$ctmcheck = '';
			$ctmcheck = $this->update_recurring($data,$webd_startdate,$webd_enddate,$post_id,$data['webd_recurrence_end'],$ex_recurr,$attach_id);
			if($ctmcheck!=1){ return;}
		}
		if ( current_user_can( 'manage_options' ) ) {
			$p_status = 'publish';
		}else{
			$p_status = 'pending';
		}
		$arr_ids = array();
		/*-- Create custom recurring date--*/
		if($webd_frequency=='ct_date'){
			if(isset($data['webd_ctdate']) && !empty($data['webd_ctdate'])){
				foreach ($data['webd_ctdate'] as $item){
					$it_st = $item['webd_ct_stdate'];
					$it_e = $item['webd_ct_edate_end'];
					if($it_st['exc_mb-field-0']['date']!=''){
						$st_date = strtotime($it_st['exc_mb-field-0']['date'] .' '. $it_st['exc_mb-field-0']['time']);
						if(isset($it_e['exc_mb-field-0']['date']) && $it_e['exc_mb-field-0']['date']!=''){
							$en_date = strtotime($it_e['exc_mb-field-0']['date'] .' '. $it_e['exc_mb-field-0']['time']);
						}else{
							$st_to_end = (strtotime($webd_enddate['exc_mb-field-0']['date']) - strtotime($webd_startdate['exc_mb-field-0']['date']));
							$c_number = $st_to_end/86400;
							$en_date = strtotime("+".$c_number." day", $st_date);
							$en_hou = strtotime($webd_enddate['exc_mb-field-0']['date'] .' '. $webd_enddate['exc_mb-field-0']['time'])- strtotime($webd_enddate['exc_mb-field-0']['date']);
							$en_date = $en_date + $en_hou;
						}
						$all_day = $item['webd_ct_allday']['exc_mb-field-0'];
						$attr = array(
						  'post_title'    => sanitize_text_field( $data['post_title'] ),
						  'post_content'  => $data['post_content'],
						  'post_status'   => $p_status,
						  'post_author'   => get_current_user_id(),
						  'post_type'      => 'product',
						  'post_excerpt' => $data['excerpt'],
						);
						$arr_ids[] = $this->create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id,$all_day);
					}
				}
				update_post_meta( $post_id, 'recurren_list', $arr_ids);
			}
		}else{
			$st_frst = $next_st = strtotime($webd_startdate['exc_mb-field-0']['date'] .' '. $webd_startdate['exc_mb-field-0']['time']);
			
			$time_ofst = strtotime($webd_startdate['exc_mb-field-0']['date'] .' '. $webd_startdate['exc_mb-field-0']['time']) - strtotime($webd_startdate['exc_mb-field-0']['date']);
			
			$next_ed = strtotime($webd_enddate['exc_mb-field-0']['date'] .' '. $webd_enddate['exc_mb-field-0']['time']);
			$nb_bw = $next_ed - $next_st;
			$br_cre = strtotime($data['webd_recurrence_end']['exc_mb-field-0']) + 86399;
			$every_x = $every_m = $data['webd_every_x']['exc_mb-field-0'];
			if($every_x < 1){ return;}

			if($webd_frequency=='week'){
				$every_x = $every_x * 7;
				$next_st = strtotime("+".$every_x." day", $next_st);
				//$next_st = strtotime($next_st);
			}else if($webd_frequency=='month'){
				$next_st = strtotime("+".$every_x." month", $next_st);
			}else{
				$next_st = strtotime("+".$every_x." day", $next_st);
			}
			$attr = array(
			  'post_title'    => sanitize_text_field( $data['post_title'] ),
			  'post_content'  => $data['post_content'],
			  'post_status'   => $p_status,
			  'post_author'   => get_current_user_id(),
			  'post_type'      => 'product',
			  'post_excerpt' => $data['excerpt'],
			);
			$webd_weekday = isset($data['webd_weekday']['exc_mb-field-0']) ? $data['webd_weekday']['exc_mb-field-0'] : array();
			if($webd_frequency=='week' && !empty($webd_weekday)){
				if(date('D', $next_st) == 'Sun'){
					$next_st = $next_st - 84600;
				}
				$next_st = strtotime('monday this week', $next_st) + $time_ofst;
				$i = 0;
				while($next_st < $br_cre){
					$i ++;
					if($i==1 && count($webd_weekday) > 1){// create of same week with first event
						foreach ($webd_weekday as $item){
							$st_date = strtotime($item.' this week', $st_frst) + $time_ofst;
							$en_date = $st_date + $nb_bw*1;
							if($en_date > $br_cre){$en_date = $br_cre;}
							if($st_date > $br_cre){ break;}
							if($st_date > $st_frst){
								$arr_ids[] = $this->create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id);
							}
						}
					}
					foreach ($webd_weekday as $item){
						$st_date = strtotime($item.' this week', $next_st) + $time_ofst;
						$en_date = $st_date + $nb_bw*1;
						if($en_date > $br_cre){$en_date = $br_cre;}
						if($st_date > $br_cre){ break;}
						if($st_date > $st_frst){
							$arr_ids[] = $this->create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id);
						}
					}
					$next_st = strtotime("+".$every_x." day", $st_date);
					if(date('D', $next_st) == 'Sun'){
						$next_st = $next_st - 84600;
					}
					$next_st = strtotime('monday this week', $next_st);
				}
			}else{
				if($webd_frequency=='month'){
					$webd_mthday = isset($data['webd_monthday']['exc_mb-field-0']) ? $data['webd_monthday']['exc_mb-field-0'] : '';
					if(!is_numeric($webd_mthday)){
						$webd_mweekday = $data['webd_mweekday']['exc_mb-field-0'];
						if($webd_mthday==''){ return;}
						$next_st = strtotime($webd_mthday.' '.$webd_mweekday.' of this month', $next_st) + $time_ofst;
					}else{
						$last_dom = strtotime('last day of this month', $next_st);
						$next_st =  strtotime(date('Y-m-'.$webd_mthday,$next_st).' '. $webd_startdate['exc_mb-field-0']['time']);
						if($next_st > $last_dom){
							$st_date = $next_st;
							$next_st = strtotime("+".$every_m." month", $last_dom);
							$next_st = strtotime(date('Y-m-'.$webd_mthday,$next_st).' '. $webd_startdate['exc_mb-field-0']['time']);
						}
					}
				}
				while($next_st < $br_cre){
					$st_date = $next_st;
					$en_date = $st_date + $nb_bw*1;
					$arr_ids[] = $this->create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id);
					if($webd_frequency!='month'){
						$next_st = strtotime("+".$every_x." day", $st_date);
					}else{
						$next_st = strtotime("+".$every_m." month", $st_date);
						if(!is_numeric($webd_mthday)){
							$webd_mweekday = $data['webd_mweekday']['exc_mb-field-0'];
							if($webd_mthday==''){ return;}
							$next_st = strtotime($webd_mthday.' '.$webd_mweekday.' of this month', $next_st) + $time_ofst;
						}else{
							$last_dom = strtotime('last day of this month', $next_st);
							$next_st =  strtotime(date('Y-m-'.$webd_mthday,$next_st).' '. $webd_startdate['exc_mb-field-0']['time']);
							if($next_st > $last_dom){
								$st_date = $next_st;
								$next_st = strtotime("+".$every_m." month", $last_dom);
								$next_st = strtotime(date('Y-m-'.$webd_mthday,$st_date).' '. $webd_startdate['exc_mb-field-0']['time']);
							}
						}
					}					
				}
			}
			update_post_meta( $post_id, 'recurren_list', $arr_ids);
		}
	}
	function create_new_recurring($attr,$st_date,$en_date,$post_id,$attach_id,$all_day=false){
		remove_action( 'save_post', array($this,'recurrence_event' ));
		$attr['post_title'] = apply_filters( 'webd_change_title_recurring', $attr['post_title'], $st_date );
		if($webd_ID = wp_insert_post( $attr, false )){
			if($attach_id!=''){
				set_post_thumbnail( $webd_ID, $attach_id );
			}
			// update meta
			update_post_meta( $webd_ID, 'webd_startdate', $st_date);
			update_post_meta( $webd_ID, 'webd_enddate', $en_date);
			if(isset($all_day) && $all_day!=''){
				update_post_meta( $webd_ID, 'webd_allday', $all_day);
			}
			update_post_meta( $webd_ID, 'recurren_ext', 'event_'.$post_id);
			update_post_meta( $post_id, 'recurren_ext', 'event_'.$post_id);
			woometa_update($_POST,$webd_ID, $post_id);
			update_post_meta( $webd_ID, 'recurren_list', '');
			
			delete_post_meta( $webd_ID, 'webd_recurrence_end');
			delete_post_meta( $webd_ID, 'webd_recurrence');
			delete_post_meta( $webd_ID, 'webd_ctdate');
			delete_post_meta( $webd_ID, 'webd_frequency');
			delete_post_meta( $webd_ID, 'webd_weekday');
			delete_post_meta( $webd_ID, 'webd_monthday');
			delete_post_meta( $webd_ID, 'webd_mweekday');
		}
		//WPML support
		if (class_exists('SitePress')) {
			global $sitepress,$wpdb;
			$trid = $sitepress->get_element_trid( $post_id, 'post_product');
			$orig_id = $sitepress->get_original_element_id_by_trid( $trid );
			$orig_lang = $this->get_original_product_language( $post_id );
			//$sitepress->set_element_language_details($webd_ID, 'post_product', false, $orig_lang);
			$new_trid = $sitepress->get_element_trid( $webd_ID, 'post_product' );
			$set_language_args = array(
				'element_id'    		=> $webd_ID,
				'element_type'  		=> 'post_product',
				'trid'   				=> $new_trid,
				'language_code'   		=> sanitize_text_field($_POST['icl_post_language']),
				'source_language_code' 	=> $orig_lang
			);
			do_action( 'wpml_set_element_language_details', $set_language_args );
		}
		$arr_ids = $webd_ID;
		add_action( 'save_post', array($this,'recurrence_event') );
		return $arr_ids;
	}
	// update 
	function update_recurring($data,$webd_startdate,$webd_enddate,$post_id,$recurr_end,$ex_recurr,$attach_id){
		$webd_frequency = $data['webd_frequency']['exc_mb-field-0'];
		$recurren_list = get_post_meta($post_id,'recurren_list', true );
		/*-- update custom date--*/
		if($webd_frequency=='ct_date'){
			$j = 0;
			if(!empty($recurren_list)){
				if(isset($data['webd_ctdate']) && !empty($data['webd_ctdate'])){
					$attach_id = get_post_thumbnail_id($post_id);
					//echo '<pre>';print_r($data['webd_ctdate']);echo '</pre>';exit;
					$_ids_ne = array();
					foreach ($data['webd_ctdate'] as $item){
						//echo '<pre>';print_r($item);echo '</pre>';
						$it_st = $item['webd_ct_stdate'];
						$it_e = $item['webd_ct_edate_end'];
						$st_date = strtotime($it_st['exc_mb-field-0']['date'] .' '. $it_st['exc_mb-field-0']['time']);
						$e_date = strtotime($it_e['exc_mb-field-0']['date'] .' '. $it_e['exc_mb-field-0']['time']);
						$all_day = $item['webd_ct_allday']['exc_mb-field-0'];
						if(  ($it_st['exc_mb-field-0']['date']!='') && $j < count($recurren_list) && is_numeric($recurren_list[$j]) && (FALSE !== get_post_status( $recurren_list[$j])) ){
							remove_action( 'save_post', array($this,'recurrence_event' ));
							
							webd_update_recurren($_POST,$recurren_list[$j],$post_id,$st_date);
							if (class_exists('SitePress') && function_exists('wooevent_wpml_duplicate_product')) {
								wooevent_wpml_duplicate_product( $recurren_list[$j], $post_id,$st_date,$e_date );
							}
							if($attach_id!=''){
								set_post_thumbnail( $recurren_list[$j], $attach_id );
							}
							update_post_meta( $recurren_list[$j], 'webd_startdate', $st_date);
							update_post_meta( $recurren_list[$j], 'webd_enddate', $e_date);
							update_post_meta( $recurren_list[$j], 'webd_allday', $all_day);
							update_post_meta( $recurren_list[$j], 'recurren_list', '');
							update_post_meta( $recurren_list[$j], 'recurren_ext', 'event_'.$post_id);
							delete_post_meta( $recurren_list[$j], 'webd_recurrence');
							delete_post_meta( $recurren_list[$j], 'webd_recurrence_end');
							delete_post_meta( $recurren_list[$j], 'webd_every_x');
							delete_post_meta( $recurren_list[$j], 'webd_ctdate');
							delete_post_meta( $recurren_list[$j], 'webd_frequency');
							delete_post_meta( $recurren_list[$j], 'webd_weekday');
							delete_post_meta( $recurren_list[$j], 'webd_monthday');
							delete_post_meta( $recurren_list[$j], 'webd_mweekday');
							
							remove_action( 'save_post', array($this,'recurrence_event' ));
						}elseif( ($it_st['exc_mb-field-0']['date']!='') && ( ( FALSE === get_post_status( $recurren_list[$j] ) ) || ($j >= count($recurren_list)) ) ){
							if ( current_user_can( 'manage_options' ) ) {
								$p_status = 'publish';
							}else{
								$p_status = 'pending';
							}
							$attr = array(
							  'post_title'    => sanitize_text_field( $data['post_title'] ),
							  'post_content'  => $data['post_content'],
							  'post_status'   => $p_status,
							  'post_author'   => get_current_user_id(),
							  'post_type'      => 'product',
							  'post_excerpt' => $data['excerpt'],
							);
							$_ids_ne[] = $this->create_new_recurring($attr,$st_date,$e_date,$post_id,$attach_id);
						}
						$j++;
					}
					$arr_ids = array_merge($recurren_list,$_ids_ne);
					update_post_meta( $post_id, 'recurren_list', $arr_ids);
				}
				return false;
			}else{
				return true;
			}
		}else{
			//echo '<pre>';print_r($data);echo '</pre>';exit;
			$ev_stc = get_post_meta($post_id,'webd_startdate', true );
			$ev_edc = get_post_meta($post_id,'webd_enddate', true );
			$ev_recurrence_end = get_post_meta($post_id,'webd_recurrence_end', true );
			$ev_recurrence = get_post_meta($post_id,'webd_recurrence', true );
			$cr_st = strtotime($webd_startdate['exc_mb-field-0']['date'] .' '. $webd_startdate['exc_mb-field-0']['time']);
			$cr_ed = strtotime($webd_enddate['exc_mb-field-0']['date'] .' '. $webd_enddate['exc_mb-field-0']['time']);
			$cr_rec = strtotime($recurr_end['exc_mb-field-0']);
			$ctmcheck = 0;
			if($ev_stc !=$cr_st || $ev_edc!= $cr_ed || $ev_recurrence_end!= $cr_rec || $ev_recurrence!= $data['webd_recurrence']['exc_mb-field-0']){
				$ctmcheck = 1;
			}elseif($ev_recurrence == $data['webd_recurrence']['exc_mb-field-0']){
				$webd_frequency = get_post_meta($post_id,'webd_frequency', true );
				if($webd_frequency == $data['webd_frequency']['exc_mb-field-0']){
					if(get_post_meta($post_id,'webd_every_x', true ) != $data['webd_every_x']['exc_mb-field-0']){
						$ctmcheck = 1;
					}elseif($webd_frequency=='week'){
						$webd_weekday = get_post_meta($post_id,'webd_weekday', true );
						$diff = array_diff($webd_weekday,$data['webd_weekday']['exc_mb-field-0']);
						if(empty($diff)){
							$diff = array_diff($data['webd_weekday']['exc_mb-field-0'],$webd_weekday);
						}
						if(!empty($diff)){
							$ctmcheck = 1;
						}
					}elseif($webd_frequency=='month'){
						$webd_monthday = get_post_meta($post_id,'webd_monthday', true );
						$webd_mweekday = get_post_meta($post_id,'webd_mweekday', true );
						if( ($webd_monthday!= $data['webd_monthday']['exc_mb-field-0']) || ($webd_mweekday != $data['webd_mweekday']['exc_mb-field-0']) ){
							$ctmcheck = 1;
						}
					}
				}else{
					$ctmcheck = 1;
				}
			}
			if(!empty($recurren_list)){
				//print_r($recurren_list);exit;
				foreach($recurren_list as $item){
					remove_action( 'save_post', array($this,'recurrence_event' ));
					$stdate = get_post_meta($item,'webd_startdate', true );
					$enddate = get_post_meta($item,'webd_enddate', true );
					if($ctmcheck==1){
						wp_delete_post($item);
					}else{
						webd_update_recurren($_POST,$item,$post_id,$stdate);
						if($attach_id!=''){
							set_post_thumbnail( $item, $attach_id );
						}
						if (class_exists('SitePress')) {
							wooevent_wpml_duplicate_product( $item, $post_id,$stdate,$enddate );
						}
						update_post_meta( $item, 'webd_startdate', $stdate);
						update_post_meta( $item, 'webd_enddate', $enddate);
						update_post_meta( $item, 'recurren_list', '');
						delete_post_meta( $item, 'webd_ctdate');
					}
					remove_action( 'save_post', array($this,'recurrence_event' ));
				}
			}else{
				// support old version 
				$args = array(
					'post_type' => 'product',
					'post_status' => get_post_status ( $post_id ),
					'post__not_in' => array( $post_id ),
					'posts_per_page' => -1,
					'order' => 'ASC',
					'meta_key' => 'recurren_ext',
					'orderby' => 'meta_value_num',
					'meta_query' => array(
						array(
							'key'     => 'recurren_ext',
							'value'   => $ex_recurr,
							'compare' => '=',
						),
					),
				); 
				$ex_posts = get_posts( $args );
				//echo '<pre>';print_r($ex_posts);echo '</pre>';exit;
				foreach($ex_posts as $item){
					remove_action( 'save_post', array($this,'recurrence_event' ));
					$stdate = get_post_meta($item->ID,'webd_startdate', true );
					$enddate = get_post_meta($item->ID,'webd_enddate', true );
					if($ctmcheck==1){
						wp_delete_post($item->ID);
					}else{
						webd_update_recurren($_POST,$item->ID,$post_id,$stdate);
						if($attach_id!=''){
							set_post_thumbnail( $item->ID, $attach_id );
						}
						if (class_exists('SitePress') && function_exists('wooevent_wpml_duplicate_product')) {
							wooevent_wpml_duplicate_product( $item->ID, $post_id,$stdate,$enddate );
						}
						update_post_meta( $item->ID, 'webd_startdate', $stdate);
						update_post_meta( $item->ID, 'webd_enddate', $enddate);
						update_post_meta( $item->ID, 'recurren_list', '');
						delete_post_meta( $item->ID, 'webd_ctdate');
					}
					remove_action( 'save_post', array($this,'recurrence_event' ));
				}
			}
			return $ctmcheck;
		}
	}
	// Get original product language
    function get_original_product_language( $product_id ){

        $cache_key = $product_id;
        $cache_group = 'original_product_language';

        $temp_language = wp_cache_get( $cache_key, $cache_group );
        if($temp_language) return $temp_language;

        global $wpdb;

        $language = $wpdb->get_var( $wpdb->prepare( "
                            SELECT t2.language_code FROM {$wpdb->prefix}icl_translations as t1
                            LEFT JOIN {$wpdb->prefix}icl_translations as t2 ON t1.trid = t2.trid
                            WHERE t1.element_id=%d AND t1.element_type=%s AND t2.source_language_code IS NULL", $product_id, 'post_'.get_post_type($product_id) ) );

        wp_cache_set( $cache_key, $language, $cache_group );

        return $language;
    }
	function wooevent_metadata(array $meta_boxes){
		$arr_dat = array();
		$arr_dat['first']= esc_html__('First', 'WEBDWooEVENT');
		$arr_dat['second']= esc_html__('Second', 'WEBDWooEVENT');
		$arr_dat['third']= esc_html__('Third', 'WEBDWooEVENT');
		$arr_dat['fourth']= esc_html__('Fourth', 'WEBDWooEVENT');
		$arr_dat['fifth']= esc_html__('Fifth', 'WEBDWooEVENT');
		$arr_dat['last']= esc_html__('Last', 'WEBDWooEVENT');
		for($i = 1; $i < 32; $i++){
			$arr_dat[$i] = $i;
		}
		$time_settings = array(	
			array( 'id' => 'webd_allday',  'name' => esc_html__('All Day', 'WEBDWooEVENT'), 'cols' => 12, 'type' => 'checkbox' ),
			array( 'id' => 'webd_startdate', 'name' => esc_html__('Start Date:', 'WEBDWooEVENT'), 'cols' => 4, 'type' => 'datetime_unix','desc' => esc_html__('', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_enddate', 'name' => esc_html__('End Date:', 'WEBDWooEVENT'), 'cols' => 4, 'type' => 'datetime_unix' ,'desc' => esc_html__('', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_time_zone', 'name' => esc_html__('Timezone', 'WEBDWooEVENT'), 'type' => 'select', 
				'options' => array( 
					'def' => esc_html__('Default', 'WEBDWooEVENT'), 
					'-12' => esc_html__('UTC-12', 'WEBDWooEVENT'), 
					'-11.5' => esc_html__('UTC-11:30', 'WEBDWooEVENT'),
					'-11' => esc_html__('UTC-11', 'WEBDWooEVENT'),
					'-10.5' => esc_html__('UTC-10:30', 'WEBDWooEVENT'),
					'-10' => esc_html__('UTC-10', 'WEBDWooEVENT'),
					'-9.5' => esc_html__('UTC-9:30', 'WEBDWooEVENT'),
					'-9' => esc_html__('UTC-9', 'WEBDWooEVENT'),
					'-8.5' => esc_html__('UTC-8:30', 'WEBDWooEVENT'),
					'-8' => esc_html__('UTC-8', 'WEBDWooEVENT'),
					'-7.5' => esc_html__('UTC-7:30', 'WEBDWooEVENT'),
					'-7' => esc_html__('UTC-7', 'WEBDWooEVENT'),
					'-6.5' => esc_html__('UTC-6:30', 'WEBDWooEVENT'),
					'-6' => esc_html__('UTC-6', 'WEBDWooEVENT'),
					'-5.5' => esc_html__('UTC-5:30', 'WEBDWooEVENT'),
					'-5' => esc_html__('UTC-5', 'WEBDWooEVENT'),
					'-4.5' => esc_html__('UTC-4:30', 'WEBDWooEVENT'),
					'-4' => esc_html__('UTC-4', 'WEBDWooEVENT'),
					'-3.5' => esc_html__('UTC-3:30', 'WEBDWooEVENT'),
					'-3' => esc_html__('UTC-3', 'WEBDWooEVENT'),
					'-2.5' => esc_html__('UTC-2:30', 'WEBDWooEVENT'),
					'-2' => esc_html__('UTC-2', 'WEBDWooEVENT'),
					'-1.5' => esc_html__('UTC-1:30', 'WEBDWooEVENT'),
					'-1' => esc_html__('UTC-1', 'WEBDWooEVENT'),
					'-0.5' => esc_html__('UTC-0:30', 'WEBDWooEVENT'),
					'+0' => esc_html__('UTC+0', 'WEBDWooEVENT'),
					'0.5' => esc_html__('UTC+0:30', 'WEBDWooEVENT'),
					'1' => esc_html__('UTC+1', 'WEBDWooEVENT'),
					'1.5' => esc_html__('UTC+1:30', 'WEBDWooEVENT'),
					'2' => esc_html__('UTC+2', 'WEBDWooEVENT'),
					'2.5' => esc_html__('UTC+2:30', 'WEBDWooEVENT'),
					'3' => esc_html__('UTC+3', 'WEBDWooEVENT'),
					'3.5' => esc_html__('UTC+3:30', 'WEBDWooEVENT'),
					'4' => esc_html__('UTC+4', 'WEBDWooEVENT'),
					'4.5' => esc_html__('UTC+4:30', 'WEBDWooEVENT'),
					'5' => esc_html__('UTC+5', 'WEBDWooEVENT'),
					'5.30' => esc_html__('UTC+5:30', 'WEBDWooEVENT'),
					'5.45' => esc_html__('UTC+5:45', 'WEBDWooEVENT'),
					'6' => esc_html__('UTC+6', 'WEBDWooEVENT'),
					'6.5' => esc_html__('UTC+6:30', 'WEBDWooEVENT'),
					'7' => esc_html__('UTC+7', 'WEBDWooEVENT'),
					'7.5' => esc_html__('UTC+7:30', 'WEBDWooEVENT'),
					'8' => esc_html__('UTC+8', 'WEBDWooEVENT'),
					'8.30' => esc_html__('UTC+8:30', 'WEBDWooEVENT'),
					'8.45' => esc_html__('UTC+8:45', 'WEBDWooEVENT'),
					'9' => esc_html__('UTC+9', 'WEBDWooEVENT'),
					'9.5' => esc_html__('UTC+9:30', 'WEBDWooEVENT'),
					'10' => esc_html__('UTC+10', 'WEBDWooEVENT'),
					'10.30' => esc_html__('UTC+10:30', 'WEBDWooEVENT'),
					'11' => esc_html__('UTC+11', 'WEBDWooEVENT'),
					'11.5' => esc_html__('UTC+11:30', 'WEBDWooEVENT'),
					'12' => esc_html__('UTC+12', 'WEBDWooEVENT'),
					'12.45' => esc_html__('UTC+12:45', 'WEBDWooEVENT'),
					'13' => esc_html__('UTC+13', 'WEBDWooEVENT'),
					'13.45' => esc_html__('UTC+13:45', 'WEBDWooEVENT'),
					'14' => esc_html__('UTC+14', 'WEBDWooEVENT'),
				),
				'cols' => 4,
				'desc' => '' , 
				'repeatable' => false,
				'multiple' => false
			),
			array( 'id' => 'webd_recurrence', 'name' => esc_html__('Recurrence', 'WEBDWooEVENT'), 'cols' => 4, 'type' => 'select', 'options' => array( 'day' => esc_html__('Every Day', 'WEBDWooEVENT'),  'week' => esc_html__('Every Week', 'WEBDWooEVENT'),  'month' => esc_html__('Every Month', 'WEBDWooEVENT'), 'custom' => esc_html__('Custom', 'WEBDWooEVENT') ), 'allow_none' => true, 'sortable' => false, 'repeatable' => false ),
			
			array( 'id' => 'webd_recurrence_end', 'name' => esc_html__('End Date of Recurrence:', 'WEBDWooEVENT'), 'cols' => 8, 'type' => 'date_unix' ,'desc' => esc_html__('', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'webd_frequency', 'name' => esc_html__('Frequency', 'WEBDWooEVENT'), 'cols' => 2, 'type' => 'select', 'options' => array( 'daily' => esc_html__('Daily', 'WEBDWooEVENT'),  'week' => esc_html__('Weekly', 'WEBDWooEVENT'),  'month' => esc_html__('Monthy', 'WEBDWooEVENT'), 'ct_date' => esc_html__('Custom date', 'WEBDWooEVENT')), 'allow_none' => false, 'sortable' => false, 'repeatable' => false ),
			array( 'id' => 'webd_every_x', 'name' => esc_html__('Every X', 'WEBDWooEVENT'), 'cols' => 2, 'type' => 'number','desc' => '' ,'default' => 1, 'repeatable' => false, 'multiple' => false ),
			array( 
				'id' => 'webd_weekday', 
				'name' => esc_html__('On day:', 'WEBDWooEVENT'), 
				'type' => 'select', 'options' => array( 
					'monday' => esc_html__('Monday', 'WEBDWooEVENT'), 
					'tuesday' => esc_html__('Tuesday', 'WEBDWooEVENT'), 
					'wednesday' => esc_html__('Wednesday', 'WEBDWooEVENT'), 
					'thursday' => esc_html__('Thursday', 'WEBDWooEVENT'), 
					'friday' => esc_html__('Friday', 'WEBDWooEVENT'), 
					'saturday' => esc_html__('Saturday', 'WEBDWooEVENT'), 
					'sunday' => esc_html__('Sunday', 'WEBDWooEVENT') 
				),
				'cols' => 4,
				'desc' => '',
				'multiple' => true 
			),
			
			array( 
				'id' => 'webd_monthday', 
				'name' => esc_html__('Month On:', 'WEBDWooEVENT'), 
				'type' => 'select', 'options' => $arr_dat,
				'cols' => 2,
				'desc' => '',
				'multiple' => false 
			),
			array( 
				'id' => 'webd_mweekday', 
				'name' => esc_html__('Day:', 'WEBDWooEVENT'), 
				'type' => 'select', 'options' => array( 
					'mon' => esc_html__('Monday', 'WEBDWooEVENT'), 
					'tue' => esc_html__('Tuesday', 'WEBDWooEVENT'), 
					'wed' => esc_html__('Wednesday', 'WEBDWooEVENT'), 
					'thu' => esc_html__('Thursday', 'WEBDWooEVENT'), 
					'fri' => esc_html__('Friday', 'WEBDWooEVENT'), 
					'sat' => esc_html__('Saturday', 'WEBDWooEVENT'), 
					'sun' => esc_html__('Sunday', 'WEBDWooEVENT'),
					'day' => esc_html__('Day', 'WEBDWooEVENT')
				),
				'cols' => 2,
				'desc' => '',
				'multiple' => false 
			),
			
			array( 'id' => 'webd_ctdate', 'name' => esc_html__('Custom Date:', 'WEBDWooEVENT'), 'type' => 'group', 'cols' => 12, 'fields' => array(
				array( 'id' => 'webd_ct_allday',  'name' => esc_html__('All Day', 'WEBDWooEVENT'), 'cols' => 2, 'type' => 'checkbox' ),
				array( 'id' => 'webd_ct_stdate', 'name' => esc_html__('Start Date:', 'WEBDWooEVENT'), 'cols' => 5, 'type' => 'datetime_unix','desc' => '' , 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'webd_ct_edate_end', 'name' => esc_html__('End Date:', 'WEBDWooEVENT'), 'cols' => 5, 'type' => 'datetime_unix','desc' => '' , 'repeatable' => false, 'multiple' => false ),
			), 'repeatable' => true, 'multiple' => false, 'sortable' => true ),
			
			
			array( 'id' => 'webd_webd_views', 'name' => esc_html__('Speakers', 'WEBDWooEVENT'),'type' => 'post_select', 'uwebd_ajax' => true, 'query' => array( 'post_type' => 'event-webd-view' ),'allow_none' => true, 'desc' => esc_html__('Choose webd_view for this event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => true ),
			array( 'id' => 'webd_stop_booking', 'name' => esc_html__('Stop booking before event start', 'WEBDWooEVENT'), 'type' => 'text', 'desc' => esc_html__('Enter number', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_sun_offset', 'name' => esc_html__('DST Offset for Sunrise/sunset', 'WEBDWooEVENT'), 'type' => 'text', 'desc' => '', 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_eventcolor', 'name' => esc_html__('Color', 'WEBDWooEVENT'), 'type' => 'colorpicker', 'repeatable' => false, 'multiple' => true ),	
		);
		$time_settings = apply_filters( 'webd_meta_setting_field', $time_settings );
		if(get_option('webd_webd_view')=='yes'){
			unset($time_settings[12]);
		}
		if(get_option('webd_sunsire_set')!='yes'){
			unset($time_settings[14]);
		}
		$location_settings = array(		
			array( 'id' => 'webd_default_venue', 'name' => esc_html__('Select venue saved', 'WEBDWooEVENT'),'type' => 'post_select', 'uwebd_ajax' => true, 'query' => array( 'post_type' => 'webd_venue' ),'allow_none' => true, 'desc' => esc_html__('Leave blank to use new venue', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'webd_adress', 'name' => esc_html__('Address', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Location Address of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_latitude_longitude', 'name' => esc_html__('Latitude and Longitude (optional)', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Physical address of your event location, if Event map shortcode cannot load your address, you need to fill Latitude and Longitude to fix it. separated by a comma. Ex for London: 42.9869502,-81.243177', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'webd_phone', 'name' => esc_html__('Phone', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Contact Number of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_email', 'name' => esc_html__('Email', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Email Contact of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_website', 'name' => esc_html__('Website', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Website URL of event', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			//array( 'id' => 'webd_subscribe_url', 'name' => esc_html__('Subscribe url', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Link to a subscribe form. Only work if no price is set.', 'WEBDWooEVENT'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'webd_schedu', 'name' => esc_html__('Schedule', 'WEBDWooEVENT'), 'type' => 'text' ,'desc' => esc_html__('Add Schedule for this event', 'WEBDWooEVENT'), 'repeatable' => true, 'multiple' => true ),
			array( 'id' => 'webd_iconmap', 'name' => esc_html__('Map Icon', 'WEBDWooEVENT'), 'type' => 'image', 'repeatable' => false, 'size' => array(100,100), 'show_size' => false ),
			
		);
		if(get_option('webd_venue_off')=='yes'){
			unset($location_settings[0]);
		}
		$event_layout = array(	
			array( 'id' => 'webd_layout', 'name' => esc_html__('Layout', 'WEBDWooEVENT'), 'type' => 'select', 'options' => array( '' => esc_html__('Default', 'WEBDWooEVENT'), 'layout-1' => esc_html__('Layout 1', 'WEBDWooEVENT'), 'layout-2' => esc_html__('Layout 2', 'WEBDWooEVENT'),'layout-3' => esc_html__('Layout 3', 'WEBDWooEVENT')),'desc' => esc_html__('Select "Default" to use settings in Event Options', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'webd_sidebar', 'name' => esc_html__('Sidebar', 'WEBDWooEVENT'), 'type' => 'select', 'options' => array( '' => esc_html__('Default', 'WEBDWooEVENT'), 'right' => esc_html__('Right', 'WEBDWooEVENT'), 'left' => esc_html__('Left', 'WEBDWooEVENT'),'hide' => esc_html__('Hidden', 'WEBDWooEVENT')),'desc' => esc_html__('Select "Default" to use settings in Event Options', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
		);
		$event_purpose = array(	
			array( 'id' => 'webd_layout_purpose', 'name' => '', 'type' => 'select', 'options' => array( 'woo' => esc_html__('WooCommere', 'WEBDWooEVENT'), 'event' => esc_html__('Event', 'WEBDWooEVENT')),'desc' => esc_html__('Select Layout Purpose for this product', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false)
		);
		
		$webd_main_purpose = get_option('webd_main_purpose');
		if($webd_main_purpose!='woo'){
			$meta_boxes[] = array(
				'id' => 'event-settings',
				'title' => __('Event Settings','WEBDWooEVENT'),
				'pages' => 'product',
				'fields' => $time_settings,
				'priority' => 'high'
			);
			$meta_boxes[] = array(
				'id' => 'location-settings',
				'title' => __('Location Settings','WEBDWooEVENT'),
				'pages' => 'product',
				'fields' => $location_settings,
				'priority' => 'high'
			);
		}
		if($webd_main_purpose=='custom' || $webd_main_purpose=='meta'){
			if($webd_main_purpose=='meta'){
				$event_purpose = array(	
					array( 'id' => 'webd_layout_purpose', 'name' => '', 'type' => 'select', 'options' => array( 'def' => esc_html__('Default', 'WEBDWooEVENT'), 'woo' => esc_html__('WooCommere', 'WEBDWooEVENT'), 'event' => esc_html__('Event', 'WEBDWooEVENT')),'desc' => esc_html__('Select Default to use setting in plugin setting', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false)
				);
			}
			$meta_boxes[] = array(
				'title' => __('Layout Purpose','WEBDWooEVENT'),
				'context' => 'side',
				'pages' => 'product',
				'fields' => $event_purpose,
				'priority' => 'high'
			);
		}
		$event_layout = apply_filters( 'webd_change_layout_meta', $event_layout );
		$meta_boxes[] = array(
			'id' => 'layout-settings',
			'title' => __('Layout Settings','WEBDWooEVENT'),
			'pages' => 'product',
			'fields' => $event_layout,
			'priority' => 'high'
		);
		$group_fields = array(
			array( 'id' => 'webd_custom_title',  'name' => esc_html__('Title', 'WEBDWooEVENT'), 'type' => 'text' ),
			array( 'id' => 'webd_custom_content', 'name' => esc_html__('Content', 'WEBDWooEVENT'), 'type' => 'text', 'desc' => '', 'repeatable' => false),
		);
		foreach ( $group_fields as &$field ) {
			$field['id'] = str_replace( 'field', 'gfield', $field['id'] );
		}
	
		$meta_boxes[] = array(
			'id' => 'custom-field',
			'title' => esc_html__('Event Custom info', 'WEBDWooEVENT'),
			'pages' => 'product',
			'fields' => array(
				array(
					'id' => 'webd_custom_metadata',
					'name' => esc_html__('Add new', 'WEBDWooEVENT'),
					'type' => 'group',
					'repeatable' => true,
					'sortable' => true,
					'fields' => $group_fields,
					'desc' => esc_html__('Add custom event information instead of fixed info above', 'WEBDWooEVENT')
				)
			),
			'priority' => 'high'
		);
		
		$group_sponsors = array(
			array( 'id' => 'webd_sponsors_link',  'name' => esc_html__('Link', 'WEBDWooEVENT'), 'type' => 'text' ),
			array( 'id' => 'webd_sponsors_logo', 'name' => esc_html__('Logo', 'WEBDWooEVENT'), 'type' => 'image', 'desc' => '', 'repeatable' => false, 'show_size' => false),
		);
		foreach ( $group_fields as &$field ) {
			$field['id'] = str_replace( 'field', 'gfield', $field['id'] );
		}
	
		$meta_boxes[] = array(
			'id' => 'sponsors-of-event',
			'title' => esc_html__('Event Sponsors', 'WEBDWooEVENT'),
			'pages' => 'product',
			'fields' => array(
				array(
					'id' => 'webd_sponsors',
					'name' => esc_html__('Add Sponsor', 'WEBDWooEVENT'),
					'type' => 'group',
					'repeatable' => true,
					'sortable' => true,
					'fields' => $group_sponsors,
					'desc' => esc_html__('Add Sponsor for this event', 'WEBDWooEVENT')
				)
			),
			'priority' => 'high'
		);
		// Custom label
		$event_label = array(
				array( 'id' => 'webd_label',  'name' => esc_html__('Label name', 'WEBDWooEVENT'), 'type' => 'text','desc' => esc_html__('Enter name of label, Ex: Featured, Canceled', 'WEBDWooEVENT') ),
				array( 'id' => 'webd_label_color',  'name' => esc_html__('Color', 'WEBDWooEVENT'), 'type' => 'colorpicker'),
			);
		$meta_boxes[] = array(
			'id' => 'custom-label',
			'title' => __('Event Label','WEBDWooEVENT'),
			'context' => 'side',
			'pages' => 'product',
			'fields' => $event_label,
			'priority' => '',
		);
		return $meta_boxes;
	}
	function meta_date_picker(){
		wp_enqueue_script( 'jquery-ui-timepicker-addon', trailingslashit( WEBD_EVENT_BOOKINGS ) . 'js/time-picker/jquery-ui-timepicker-addon.js', array( 'jquery') );
		wp_enqueue_style( 'jquery-ui-timepicker-addon-css', trailingslashit( WEBD_EVENT_BOOKINGS ) . 'js/time-picker/jquery-ui-timepicker-addon.css');
	}
    function woo_event_meta_tab(){
        echo '<li class="wooevent_options_tab"><a href="#wooevent_options">'.esc_html__('Event Settings','WEBDWooEVENT').'</a></li>';
    }
}
$WEBD_WooEvent_Meta = new WEBD_WooEvent_Meta();

if( get_option('webd_cat_ctcolor') == 'on' ){
	/* Category color */
	add_action( 'product_cat_add_form_fields', 'webd_color_fields', 10 );
	add_action ( 'product_cat_edit_form_fields', 'webd_color_fields');
	
	function webd_color_fields( $tag ) {
		wp_enqueue_script( 'webd-color-picker', WEBD_EVENT_BOOKINGS. 'js/jscolor.min.js', array('jquery'), '2.0' );
		$t_id 					= isset($tag->term_id) ? $tag->term_id : '';
		$webd_category_color 			= get_option( "webd_category_color_$t_id")?get_option( "webd_category_color_$t_id"):'';
		?>
		<tr class="form-field" style="">
			<th scope="row" valign="top">
				<label for="webd-category-color"><?php esc_html_e('Color','WEBDWooEVENT'); ?></label>
			</th>
			<td>
				<input type="text" name="webd-category-color" id="webd-category-color" class="jscolor {required:false}" style="margin-bottom:15px;" value="<?php echo esc_attr($webd_category_color) ?>" />
			</td>
		</tr>
		<?php
	}
	//save color fields
	add_action ( 'edited_product_cat', 'webd_save_extra_color_fileds', 10, 2);
	add_action( 'created_product_cat', 'webd_save_extra_color_fileds', 10, 2 );
	function webd_save_extra_color_fileds( $term_id ) {
		if ( isset( $_POST[sanitize_key('webd-category-color')] ) ) {
			$webd_category_color = sanitize_text_field($_POST['webd-category-color']);
			update_option( "webd_category_color_$term_id", $webd_category_color );
		}
	}
}
if( get_option('webd_enable_subtitle') == 'yes' ){
	function webd_sub_title_metabox() {
	
		global $post; ## global post object
		if($post->post_type == 'product'){
			wp_nonce_field( plugin_basename( __FILE__ ), 'webd_sub_title_nonce' ); ## Create nonce
			$webd_subtitle = get_post_meta($post->ID, 'webd_subtitle', true); ## Get the subtitle?>
			<p>
				<input type="text" name="webd_subtitle" placeholder="<?php esc_html_e('Sub Title','WEBDWooEVENT');?>" id="sub_title" class="widefat" value="<?php if(isset($webd_subtitle)) { echo $webd_subtitle; } ?>" />
			</p>
			<?php
		}
	}
	add_action( 'edit_form_after_title', 'webd_sub_title_metabox' );
	function webd_save_sub_title($post_id, $post) {
		global $post;
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return false; ## Block if doing autosave
	
		if ( isset($post->ID) && !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID; ## Block if user doesn't have priv
		}
	
		if (isset($_POST['webd_sub_title_nonce']) && !wp_verify_nonce( $_POST['webd_sub_title_nonce'], plugin_basename(__FILE__) )) {
	
	
		} else {
			if(isset($_POST['webd_subtitle'])) {
				update_post_meta($post->ID, 'webd_subtitle', sanitize_text_field($_POST['webd_subtitle']));
			} else if(isset($post->ID)){
				update_post_meta($post->ID, 'webd_subtitle', '');
			}
		}
	
		return false;
	
	}
	add_action('save_post', 'webd_save_sub_title', 1, 2);
	
}
