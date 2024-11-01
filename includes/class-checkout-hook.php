<?php
class WEBD_WooEvent_Checkouthook {
	public function __construct()
    {
		add_action('woocommerce_after_order_notes', array( &$this,'add_user_data_booking'));
		add_action( 'woocommerce_before_checkout_process', array( &$this,'verify_checkout'));
		add_action( 'woocommerce_checkout_update_order_meta', array( &$this,'saveto_order_meta'));
		
		//add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this,'my_custom_checkout_field'), 10, 1 );
		add_action( 'woocommerce_after_order_itemmeta', array( &$this,'show_adminorder_ineach_metadata'), 10, 3 );
		add_action( 'woocommerce_order_item_meta_end', array( &$this,'show_order_ineach_metadata'), 10, 3 ); 
    }


	/*function my_custom_checkout_field($order){
		echo '<p><strong>'.__('Phone From Checkout Form').':</strong> ' . get_post_meta( $order->id, 'my_field_name', true ) . '</p>';
	}*/
	
	function verify_checkout(){
		$attendees_required = get_option('webd_attendees_required');
		if($attendees_required=='yes' && isset($_POST['webd_ids'])){
			foreach($_POST['webd_ids'] as $item){
				if ( ! empty( $_POST['webd_if_name'][$item] ) ) {
					for( $i = 0 ; $i < count($_POST['webd_if_name'][$item]); $i++){
						if(!isset($_POST['webd_if_name'][$item][$i]) || $_POST['webd_if_name'][$item][$i] =='' ){
							wc_add_notice( esc_html__( 'Please fill name attendees ' ,'WEBDWooEVENT'), 'error' );
						}
						if(!isset($_POST['webd_if_lname'][$item][$i]) || $_POST['webd_if_lname'][$item][$i] =='' ){
							wc_add_notice( esc_html__( 'Please last name attendees ' ,'WEBDWooEVENT'), 'error' );
						}
						if(!isset($_POST['webd_if_email'][$item][$i]) || $_POST['webd_if_email'][$item][$i] =='' ){
							wc_add_notice( esc_html__( 'Please email attendees' ,'WEBDWooEVENT'), 'error' );
						}
					}
				}else{
					wc_add_notice( esc_html__( 'Please fill info' ,'WEBDWooEVENT'), 'error' );
				}
			}
		}
		
	}
	function add_user_data_booking( $checkout ) {
		$c_it = 0;
		$n = $q_ty = 0;
		ob_start();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$n ++ ;
			$id = $cart_item['product_id'];
			if(isset($cart_item['variation_id']) && $cart_item['variation_id']!=''){
				$id = $cart_item['variation_id'];
			}
			$value_id = $id.'_'.$n;
			$value_id = apply_filters( 'webd_attendee_key', $value_id, $cart_item );
			$_product = wc_get_product ($id);
			if(get_post_meta( $id,'webd_startdate', true) !='' || webd_if_product_isevent($id)== true){
				$c_it ++;
				if($c_it==1){
					$t_atten = get_option('webd_text_attende_')!='' ? get_option('webd_text_attende_') : esc_html__('Attendees info','WEBDWooEVENT');
					echo '<div class="user_checkout_field"><h3>' . $t_atten . '</h3>';
				}
				$t_fname = get_option('webd_text_fname_')!='' ? get_option('webd_text_fname_') : esc_html__('First Name: ','WEBDWooEVENT');
				$t_lname = get_option('webd_text_lname_')!='' ? get_option('webd_text_lname_') : esc_html__('Last Name: ','WEBDWooEVENT');
				$t_email = get_option('webd_text_email_')!='' ? get_option('webd_text_email_') : esc_html__('Email: ','WEBDWooEVENT');
				$attendees_required = get_option('webd_attendees_required');
				echo '<div class="gr-product">';
					echo '<h4>('.$c_it.') '. get_the_title($id) . '</h4>';
					echo '<input type="hidden" name="webd_ids[]" value="'.$value_id.'">';
					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0) {
						echo '<div class="w-product">';
						for($i=0; $i < $cart_item['quantity']; $i++){
							$q_ty ++;
							woocommerce_form_field( 
								'webd_if_name['.$value_id.']['.$i.']', 
								array(
									'type'          => 'text',
									'class'         => array('webd-ct-class form-row-wide first-el'),
									'label'         => $t_fname.'('.($i+1).')',
									'placeholder'   => '',
									'required'          => $attendees_required=='yes' ? true : false,
								), 
								''
							);
							woocommerce_form_field( 
								'webd_if_lname['.$value_id.']['.$i.']', 
								array(
									'type'          => 'text',
									'class'         => array('webd-ct-class form-row-wide'),
									'label'         => $t_lname.'('.($i+1).')',
									'placeholder'   => '',
									'required'          => $attendees_required=='yes' ? true : false,
								), 
								''
							);
							woocommerce_form_field( 'webd_if_email['.$value_id.']['.$i.']', 
								array(
									'type'          => 'text',
									'class'         => array('webd-ct-class form-row-wide'),
									'label'         => $t_email.'('.($i+1).')',
									'placeholder'   => '',
									'required'          => $attendees_required=='yes' ? true : false,
								), 
								''
							);
							do_action( 'webd_after_custom_field', $value_id, $i );
						}
						echo '</div>';
					}
				echo '</div>';
				if($c_it==1){
					echo '</div>';
				}
			}
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		$hide_if_one='no';
		$hide_if_one = apply_filters( 'webd_attendee_hideifone', $hide_if_one );
		if($q_ty=='1' && $hide_if_one=='yes'){
			return;
		}else{
			echo $output_string;
		}
	
	}
	function saveto_order_meta( $order_id ) {
		if ( ! empty( $_POST['webd_ids'] ) ) {
			foreach($_POST['webd_ids'] as $item){
				if ( ! empty( $_POST['webd_if_name'][$item] ) ) {
					$nl_meta = $ticket_if = '';
					$nbid= count($_POST['webd_if_name'][$item]);
					for( $i = 0 ; $i < $nbid; $i++){
						$name  = sanitize_text_field( $_POST['webd_if_name'][$item][$i] );
						$lname = sanitize_text_field( $_POST['webd_if_lname'][$item][$i] );
						$email = sanitize_text_field( $_POST['webd_if_email'][$item][$i] );
						if($nl_meta!=''){
							$nl_meta = $nl_meta.']['.$email.'||'.$name.'||'.$lname;
						}else{
							$nl_meta = $email.'||'.$name.'||'.$lname;
						}
						$nl_meta = apply_filters( 'webd_custom_field_extract', $nl_meta,$_POST,$item, $i);
						// new ticket info
						$ticket_if =  $email.'|'.$name.'|'.$lname;
						do_action( 'webd_save_data_ticket_info',$order_id,$item, $ticket_if );
					}
					update_post_meta( $order_id, 'att_info-'.$item, $nl_meta );
				}
			}
		}
	}
	function show_adminorder_ineach_metadata($item_id, $item, $_product){
		$id = $item['product_id'];
		if(!isset($_GET['post']) || $_GET['post']==''){ return;}
		$order = new WC_Order( sanitize_text_field($_GET['post']) );
		$order_items = $order->get_items();
		$n = 0; $find = 0;
		foreach ($order_items as $items_key => $items_value) {
			$n ++;
			if($items_value->get_id() == $item_id){
				$find = 1;
				break;
			}
		}
		if($find == 0){ return;}
		
		$value_id = $id.'_'.$n;
		$value_id = apply_filters( 'webd_attendee_key', $value_id, $item );
		
		$metadata = get_post_meta(sanitize_text_field($_GET['post']),'att_info-'.$value_id, true);
		// support very old version
		if($metadata == ''){
			$metadata = get_post_meta(sanitize_text_field($_GET['post']),'att_info-'.$id, true);
		}
		// ver 3.6
		if($metadata == '' && $item['variation_id']!=''){
			$id = $item['variation_id'];
			$value_id = $id.'_'.$n;
			$metadata = get_post_meta($order->get_id(),'att_info-'.$value_id, true);
		}
		if($metadata !=''){
			
			$t_atten = get_option('webd_text_attende_')!='' ? get_option('webd_text_attende_') : esc_html__('Attendees info','WEBDWooEVENT');
			$t_name = get_option('webd_text_name_')!='' ? get_option('webd_text_name_') : esc_html__('Name: ','WEBDWooEVENT');
			$t_email = get_option('webd_text_email_')!='' ? get_option('webd_text_email_') : esc_html__('Email: ','WEBDWooEVENT');
			
			$metadata = explode("][",$metadata);
			if(!empty($metadata)){
				$i=0;
				foreach($metadata as $item){
					$i++;
					$item = explode("||",$item);
					$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
					$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
					echo '<div class="webd-user-info">'.$t_atten.' ('.$i.') ';
					echo  $f_name!='' && $l_name!='' ? '<span><b>'.$t_name.'</b>'.$f_name.' '.$l_name.'</span>' : '';
					echo  isset($item[0]) && $item[0]!='' ? '<span><b>'.$t_email.' </b>'.$item[0].'</span>' : '';
					do_action( 'webd_after_order_info', $item);
					echo '</div>';
				}
			}
		}
	}
	
	function show_order_ineach_metadata($item_id, $item, $order){
		$id = $item['product_id'];
		//echo $item_id;
		//echo '<pre>'; print_r($order); echo ('</pre>');exit;
		$order_items = $order->get_items();
		$n = 0; $find = 0;
		foreach ($order_items as $items_key => $items_value) {
			$n ++;
			if($items_value->get_id() == $item_id){
				$find = 1;
				break;
			}
		}
		if($find == 0){ return;}
		$value_id = $id.'_'.$n;
		$value_id = apply_filters( 'webd_attendee_key', $value_id, $item );
		
		$metadata = get_post_meta($order->get_id(),'att_info-'.$value_id, true);
		// support very old version
		if($metadata == ''){
			$metadata = get_post_meta($order->get_id(),'att_info-'.$id, true);
		}
		// ver 3.6
		if($metadata == '' && $item['variation_id']!=''){
			$id = $item['variation_id'];
			$value_id = $id.'_'.$n;
			$metadata = get_post_meta($order->get_id(),'att_info-'.$value_id, true);
		}
		if($metadata !=''){
			
			$t_atten = get_option('webd_text_attende_')!='' ? get_option('webd_text_attende_') : esc_html__('Attendees info','WEBDWooEVENT');
			$t_name = get_option('webd_text_name_')!='' ? get_option('webd_text_name_') : esc_html__('Name: ','WEBDWooEVENT');
			$t_email = get_option('webd_text_email_')!='' ? get_option('webd_text_email_') : esc_html__('Email: ','WEBDWooEVENT');
			
			$metadata = explode("][",$metadata);
			if(!empty($metadata)){
				$i=0;
				foreach($metadata as $item){
					$i++;
					$item = explode("||",$item);
					$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
					$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
					echo '<div class="webd-user-info">'.$t_atten.' ('.$i.') <br>';
					echo  $f_name!='' && $l_name!='' ? '<span><b>'.$t_name.'</b>'.$f_name.' '.$l_name.'</span><br>' : '';
					echo  isset($item[0]) && $item[0]!='' ? '<span><b>'.$t_email.' </b>'.$item[0].'</span><br>' : '';
					do_action( 'webd_after_order_info', $item);
					echo '</div>';
				}
			}
		}
	}

	
}
$WEBD_WooEvent_Checkouthook = new WEBD_WooEvent_Checkouthook();