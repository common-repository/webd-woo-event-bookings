<?php
class WEBD_WooEvent_Reminder_Email {
	public function __construct()
    {
		add_filter( 'exc_mb_meta_boxes', array($this,'email_reminder_metadata') );
		// Schedule the event when the order is completed.
		add_action( 'woocommerce_order_status_completed', array( $this, 'remind_email' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'remind_email_fb' ), 11, 1 );
		// Trigger the email.
		add_action( 'wooevent_email_reminder', array( &$this, 'send_mail' ),10,3 );
		add_action( 'wooevent_email_reminder_2', array( $this, 'send_mail' ),10,3 );
		add_action( 'wooevent_email_reminder_3', array( $this, 'send_mail' ),10,3 );
		add_action( 'wooevent_email_reminder_feedback', array( $this, 'send_mail_fb' ),10,2 );
		// send email to attendee 
		add_action( 'webd_send_email_reminder', array( &$this, 'send_mail_attendee' ),10,1 );
    }
	/**
	 * Gets permalinks from order.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Permalinks.
	 */
	protected function get_permalinks_from_order( $order_id ) {
		global $wpdb;
		$permalinks = '<ul>';

		// Get all order items.
		$order_item_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT
				order_item_id
				FROM
				{$wpdb->prefix}woocommerce_order_items
				WHERE
				order_id = %d
			", $order_id )
		);

		// Get products ids.
		foreach ( $order_item_ids as $order_item_id ) {
			$product_id = $wpdb->get_row(
				$wpdb->prepare( "
					SELECT
					meta_value
					FROM
					{$wpdb->prefix}woocommerce_order_itemmeta
					WHERE
					order_item_id = %d
					AND
					meta_key = '_product_id'
				", $order_item_id ),
				ARRAY_N
			);

			// Test whether the product actually was found.
			if ( is_array( $product_id ) ) {
				$product_ids[] = implode( $product_id );
			}
		}

		// Creates products links.
		foreach ( $product_ids as $product_id ) {
			$permalinks .= sprintf( '<li><a href="%1$s" target="_blank">%1$s</a></li>', get_permalink( $product_id ) );
		}

		$permalinks .= '</ul>';

		return $permalinks;
	}
	/**
	 * Gets permalinks from order.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Permalinks.
	 */
	protected function get_idproduct_from_order( $order_id ) {
		global $wpdb;
		// Get all order items.
		$order_item_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT
				order_item_id
				FROM
				{$wpdb->prefix}woocommerce_order_items
				WHERE
				order_id = %d
			", $order_id )
		);

		// Get products ids.
		foreach ( $order_item_ids as $order_item_id ) {
			$product_id = $wpdb->get_row(
				$wpdb->prepare( "
					SELECT
					meta_value
					FROM
					{$wpdb->prefix}woocommerce_order_itemmeta
					WHERE
					order_item_id = %d
					AND
					meta_key = '_product_id'
				", $order_item_id ),
				ARRAY_N
			);

			// Test whether the product actually was found.
			if ( is_array( $product_id ) ) {
				$product_ids[] = implode( $product_id );
			}
		}

		return $product_ids;
	}
	/**
	 * Remind email action.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function remind_email( $order_id ) {
		$interval_type  = get_option( 'webd_email_timeformat' )!='' ? get_option( 'webd_email_timeformat' ) : 1;
		$interval_count = get_option( 'webd_email_delay' )!='' ? get_option( 'webd_email_delay' ) : 604800;
		
		$webd_emtimeformat_2  = get_option( 'webd_emtimeformat_2' );
		$webd_emdelay_2 = get_option( 'webd_emdelay_2' );
		
		$webd_emtimeformat_3  = get_option( 'webd_emtimeformat_3' );
		$webd_emdelay_3 = get_option( 'webd_emdelay_3' );
		
		$product_ids = $this->get_idproduct_from_order( $order_id );

		do_action('webd_reminder_email_schedule', $order_id);

		foreach ( $product_ids as $product_id ) {
			if(get_option('webd_emreminder_single') == 'on'){
				$sg_em1 = get_post_meta( $product_id, 'webd_email_delay', true );
				$sg_em2 = get_post_meta( $product_id, 'webd_emdelay_2', true );
				$sg_em3 = get_post_meta( $product_id, 'webd_emdelay_3', true );
				if($sg_em1!='' || $sg_em2!='' || $sg_em3!=''){
					$interval_type  = get_post_meta( $product_id, 'webd_email_timeformat', true );
					$interval_count = $sg_em1;
					
					$webd_emtimeformat_2  = get_post_meta( $product_id, 'webd_emtimeformat_2', true );
					$webd_emdelay_2 = $sg_em2;
					
					$webd_emtimeformat_3  = get_post_meta( $product_id, 'webd_emtimeformat_3', true );
					$webd_emdelay_3 = $sg_em3;
				}
			}
			$startdate = get_post_meta( $product_id, 'webd_startdate', true ) ;
			//$this -> send_mail($order_id, $product_id, 1);
			if($startdate!='' && is_numeric($startdate)){
				$gmt_offset = get_option('gmt_offset');
				// First time Sending email
				$webd_startdate = $startdate - ($gmt_offset*3600);
				if(is_numeric($interval_count) && is_numeric($interval_type)){
					$interval = $startdate*1 - ($interval_count*$interval_type);
					if(is_numeric($gmt_offset)){
						$interval = $interval - ($gmt_offset*3600);
					}
					//echo $interval.'  -now- '.time();exit;
					if(($webd_startdate > $interval) && ($webd_startdate > time()) && ($interval > time())){
						wp_schedule_single_event( $interval, 'wooevent_email_reminder', array( $order_id, $product_id, 1 ) );
					}
				}
				// Second time Sending email
				if(is_numeric($webd_emdelay_2) && is_numeric($webd_emtimeformat_2)){
					$interval_2 = $startdate*1 - ($webd_emdelay_2*$webd_emtimeformat_2);
					if(is_numeric($gmt_offset)){
						$interval_2 = $interval_2 - ($gmt_offset*3600);
					}
					if(($webd_startdate > $interval_2) && ($webd_startdate > time()) && ($interval_2 > time())){
						wp_schedule_single_event( $interval_2, 'wooevent_email_reminder_2', array( $order_id, $product_id, 2 ) );
					}
				}
				// Third time Sending email
				if(is_numeric($webd_emdelay_3) && is_numeric($webd_emtimeformat_3)){
					$interval_3 = $startdate*1 - ($webd_emdelay_3*$webd_emtimeformat_3);
					if(is_numeric($gmt_offset)){
						$interval_3 = $interval_3 - ($gmt_offset*3600);
					}
					if(($webd_startdate > $interval_3) && ($webd_startdate > time()) && ($interval_3 > time())){
						wp_schedule_single_event( $interval_3, 'wooevent_email_reminder_3', array( $order_id, $product_id, 3 ) );
					}
				}
			}
			
		}
	}

	/**
	 * Sends the email notification.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function send_mail( $order_id , $product_id, $nb) {
		global $woocommerce;
		$mailer = $woocommerce->mailer();
		$order = new WC_Order( $order_id );
		if(method_exists($order,'get_status') && $order->get_status()!='completed'){
			return;
		}

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		$all_day = get_post_meta($product_id,'webd_allday', true );
		$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true ) ;
		$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true ) ;
		$st_d = date_i18n( 'd', $webd_startdate);
		$e_d = date_i18n( 'd', $webd_enddate);
		$date = date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate);
		if( $all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate))){ 
			if($st_d != $e_d){
				$date .= ' - '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);
			}else{
				$date .= ' - '.date_i18n(get_option('time_format'), $webd_enddate);
			}
		}elseif($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)==date_i18n(get_option('time_format'), $webd_enddate))){
			if($st_d != $e_d){
				$date .= ' - '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);
			}
		}
		$link = get_permalink( $product_id );
		$title = get_the_title($product_id);
		if( method_exists('WC_Order', 'get_billing_first_name') ){
			$f_name = $order->get_billing_first_name();
			$l_name = $order->get_billing_last_name();
		}else{
			$f_name = $order->billing_first_name;
			$l_name = $order->billing_last_name;
		}
		$cus_name = $f_name.' '.$l_name;
		$webd_webd_views = get_post_meta( $product_id, 'webd_webd_views', true );
		$spk = '';
		if(is_array($webd_webd_views)){
			$i = 0;
			foreach($webd_webd_views as $webd_view){
				$i++;
				$spk .= get_the_title($webd_view);
				if($i > 1 && $i!= count($webd_webd_views)){ $spk .=', '; }
			}
		}
		$webd_venue = get_the_title(get_post_meta( $product_id, 'webd_default_venue', true )) ;
		// subject
		$email_subject = get_option('webd_email_subject');
		if($email_subject ==''){
			$email_subject = esc_html__( 'Email Reminder from ', 'WEBDWooEVENT' ).get_bloginfo( 'name', 'display' );
		}else{
			$email_subject = str_replace('[eventitle]', $title, $email_subject);
			$email_subject = str_replace('[eventdate]', $date, $email_subject);
			$email_subject = str_replace('[eventspk]', $spk, $email_subject);
			$email_subject = str_replace('[eventvn]', $webd_venue, $email_subject);
			$email_subject = str_replace('[customer_name]', $cus_name, $email_subject);
			$email_subject = str_replace('[f_name]', $f_name, $email_subject);
			$email_subject = str_replace('[l_name]', $l_name, $email_subject);
		}
		$subject = apply_filters(
			'wooevent_reminder_email_subject',$email_subject, $order, $product_id
		);
		// Message title.
		$message_title = apply_filters(
			'wooevent_reminder_email_title',$email_subject,$order,$product_id
		);
		// Message body.
		$webd_email_content = '';
		if($nb == 2 || $nb == 3){
			$webd_email_content = get_post_meta($product_id,'webd_email_content_'.$nb, true );	
		}
		if($webd_email_content ==''){
			$webd_email_content = get_post_meta($product_id,'webd_email_content', true );
		}
		// add action before send email reminder
		$arr_data = array();
		$arr_data['order_id'] = $order_id;
		$arr_data['product_id'] = $product_id;
		$arr_data['nb'] = $nb;
		$arr_data['date'] = $date;
		$arr_data['title'] = $title;
		$arr_data['spk'] = $spk;
		$arr_data['venue'] = $webd_venue;
		$arr_data['email_content'] = $webd_email_content;
		$arr_data['link'] = $link;
		$arr_data['order'] = $order;
		$arr_data['email'] = $order->billing_email;
		do_action( 'webd_send_email_reminder', $arr_data);

		if(preg_replace('/\s+/', '', $webd_email_content) == ''){
			$body = '<p>' . esc_html__( 'This is an automatic reminder of the following event', 'WEBDWooEVENT' ) . '</p>' .
				'<h2><a href="'.$link.'">'.$title.'</a></h2>'.
				'<p><strong>' . esc_html__( 'Date', 'WEBDWooEVENT' ) . ': </strong>'.$date.'</p>'.
				'<p>' . esc_html__( 'Additional information', 'WEBDWooEVENT' ) . '</p>'.
				'<p>' . esc_html__( 'This is a reminder that you had registered for "','WEBDWooEVENT').$title.'".'.esc_html__('We look forward to seeing you', 'WEBDWooEVENT' ) . '</p>';
			
			$ct_email = '';
			if($nb == 2 || $nb == 3){
				$ct_email = get_option('webd_email_content_'.$nb);	
			}
			if($ct_email==''){
				$ct_email = get_option('webd_email_content');
			}
			if($ct_email!=''){
				$ct_email = str_replace('[eventitle]', $title, $ct_email);
				$ct_email = str_replace('[eventdate]', $date, $ct_email);
				$ct_email = str_replace('[eventlink]', $link, $ct_email);
				$ct_email = str_replace('[eventspk]', $spk, $ct_email);
				$ct_email = str_replace('[eventvn]', $webd_venue, $ct_email);
				$ct_email = str_replace('[customer_name]', $cus_name, $ct_email);
				$ct_email = str_replace('[f_name]', $f_name, $ct_email);
				$ct_email = str_replace('[l_name]', $l_name, $ct_email);
				$body = $ct_email;
			}
		}else{
			$webd_email_content = str_replace('[eventitle]', $title, $webd_email_content);
			$webd_email_content = str_replace('[eventdate]', $date, $webd_email_content);
			$webd_email_content = str_replace('[eventlink]', $link, $webd_email_content);
			$webd_email_content = str_replace('[eventspk]', $spk, $webd_email_content);
			$webd_email_content = str_replace('[eventvn]', $webd_venue, $webd_email_content);
			$webd_email_content = str_replace('[customer_name]', $cus_name, $webd_email_content);
			$webd_email_content = str_replace('[f_name]', $f_name, $webd_email_content);
			$webd_email_content = str_replace('[l_name]', $l_name, $webd_email_content);
			$body = $webd_email_content;
		}

		$message_body = apply_filters(
			'wooevent_reminder_email_message',
			$body,
			$order,
			$product_id
		);

		// Sets the message template.
		$message = apply_filters( 
			'wooevent_reminder_email_template',
			$mailer->wrap_message( $message_title, $message_body ),
			$message_title, 
			$message_body,
			$webd_email_content
		);

		// Send the email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}
	/**
	 * Remind email feedback action.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function remind_email_fb( $order_id ) {
		if(get_option('webd_email_reminder_fb') == 'off'){ return;}
		$interval_type  = get_option( 'webd_email_fbtimefm' )!='' ? get_option( 'webd_email_fbtimefm' ) : 1;
		$interval_count = get_option( 'webd_email_fbdelay' )!='' ? get_option( 'webd_email_fbdelay' ) : '';
		
		$product_ids = $this->get_idproduct_from_order( $order_id );
		$gmt_offset = get_option('gmt_offset');
		$sgos = get_option('webd_reminder_fbsg');
		foreach ( $product_ids as $product_id ) {
			if($sgos == 'on'){
				$sg_em1 = get_post_meta( $product_id, 'webd_email_fbdelay', true );
				if($sg_em1!=''){
					$interval_type  = get_post_meta( $product_id, 'webd_email_fbtimefm', true );
					$interval_count = $sg_em1;
				}
			}
			$enddate = get_post_meta( $product_id, 'webd_enddate', true ) ;
			if($enddate!='' && is_numeric($enddate)){
				// time Sending email
				if(is_numeric($interval_count) && is_numeric($interval_type)){
					$interval = $enddate*1 + ($interval_count*$interval_type);
					if(is_numeric($gmt_offset)){
						$interval = $interval - ($gmt_offset*3600);
					}
					//echo $interval.'  -now- '.time();exit;
					if($interval > time()){
						wp_schedule_single_event( $interval, 'wooevent_email_reminder_feedback', array( $order_id, $product_id ) );
					}
				}
			}
			
		}
	}
	/**
	 * Sends the email feedback notification.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function send_mail_fb( $order_id , $product_id) {
		global $woocommerce;
		$mailer = $woocommerce->mailer();
		$order = new WC_Order( $order_id );
		if(method_exists($order,'get_status') && $order->get_status()!='completed'){
			return;
		}
		$webd_email_content = get_post_meta($product_id,'webd_email_fbcontent', true );
		$ct_email = get_option('webd_email_fbcontent');
		if(preg_replace('/\s+/', '', $webd_email_content) =='' && preg_replace('/\s+/', '', $ct_email)==''){
			return;
		}
		
		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";
		
		if( method_exists('WC_Order', 'get_billing_first_name') ){
			$f_name = $order->get_billing_first_name();
			$l_name = $order->get_billing_last_name();
		}else{
			$f_name = $order->billing_first_name;
			$l_name = $order->billing_last_name;
		}
		$cus_name = $f_name.' '.$l_name;

		$all_day = get_post_meta($product_id,'webd_allday', true );
		$webd_startdate = get_post_meta( $product_id, 'webd_startdate', true ) ;
		$webd_enddate = get_post_meta( $product_id, 'webd_enddate', true ) ;
		$st_d = date_i18n( 'd', $webd_startdate);
		$e_d = date_i18n( 'd', $webd_enddate);
		$date = date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate);
		if( $all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)!=date_i18n(get_option('time_format'), $webd_enddate))){ 
			if($st_d != $e_d){
				$date .= ' - '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);
			}else{
				$date .= ' - '.date_i18n(get_option('time_format'), $webd_enddate);
			}
		}elseif($all_day!='1' && (date_i18n(get_option('time_format'), $webd_startdate)==date_i18n(get_option('time_format'), $webd_enddate))){
			if($st_d != $e_d){
				$date .= ' - '.date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);
			}
		}
		$link = get_permalink( $product_id );
		$title = get_the_title($product_id);

		$webd_webd_views = get_post_meta( $product_id, 'webd_webd_views', true );
		$spk = '';
		if(is_array($webd_webd_views)){
			$i = 0;
			foreach($webd_webd_views as $webd_view){
				$i++;
				$spk .= get_the_title($webd_view);
				if($i > 1 && $i!= count($webd_webd_views)){ $spk .=', '; }
			}
		}
		$webd_venue = get_the_title(get_post_meta( $product_id, 'webd_default_venue', true )) ;
		// subject
		$email_subject = get_option('webd_email_fbsubject');
		if($email_subject ==''){
			$email_subject = esc_html__( 'Email Thank you from ', 'WEBDWooEVENT' ).get_bloginfo( 'name', 'display' );
		}else{
			$email_subject = str_replace('[eventitle]', $title, $email_subject);
			$email_subject = str_replace('[eventdate]', $date, $email_subject);
			$email_subject = str_replace('[eventspk]', $spk, $email_subject);
			$email_subject = str_replace('[eventvn]', $webd_venue, $email_subject);
			$email_subject = str_replace('[customer_name]', $cus_name, $email_subject);
			$email_subject = str_replace('[f_name]', $f_name, $email_subject);
			$email_subject = str_replace('[l_name]', $l_name, $email_subject);
		}
		// subject
		$subject = apply_filters(
			'wooevent_reminder_fb_email_subject',
			$email_subject,
			$order,
			$product_id
		);
		// Message title.
		$message_title = apply_filters(
			'wooevent_reminder_fb_email_title',
			$email_subject,
			$order,
			$product_id
		);
		// Message body.
		if(preg_replace('/\s+/', '', $webd_email_content) == ''){
			if($ct_email!=''){
				$ct_email = str_replace('[eventitle]', $title, $ct_email);
				$ct_email = str_replace('[eventdate]', $date, $ct_email);
				$ct_email = str_replace('[eventlink]', $link, $ct_email);
				$ct_email = str_replace('[customer_name]', $cus_name, $ct_email);
				$ct_email = str_replace('[f_name]', $f_name, $ct_email);
				$ct_email = str_replace('[l_name]', $l_name, $ct_email);
				$body = $ct_email;
			}
		}else{
			$webd_email_content = str_replace('[eventitle]', $title, $webd_email_content);
			$webd_email_content = str_replace('[eventdate]', $date, $webd_email_content);
			$webd_email_content = str_replace('[eventlink]', $link, $webd_email_content);
			$webd_email_content = str_replace('[customer_name]', $cus_name, $webd_email_content);
			$webd_email_content = str_replace('[f_name]', $f_name, $webd_email_content);
			$webd_email_content = str_replace('[l_name]', $l_name, $webd_email_content);
			$body = $webd_email_content;
		}

		$message_body = apply_filters(
			'wooevent_reminder_fb_email_message',
			$body,
			$order,
			$product_id
		);

		// Sets the message template.
		$message = apply_filters( 
			'wooevent_reminder_fb_email_template',
			$mailer->wrap_message( $message_title, $message_body ),
			$message_title, 
			$message_body,
			$webd_email_content
		);

		// Send the email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}

	/**
	 * Sends the email reminder to attendee list.
	 *
	 * @param  int  $order_id Order ID.
	 *
	 * @return void
	 */
	public function send_mail_attendee( $arr_data) {
		if(get_option('webd_emreminder_atte')!='yes'){
			return;
		}
		$order_id = $arr_data['order_id'];
		$product_id = $arr_data['product_id'];
		$nb = $arr_data['nb'];
		$date = $arr_data['date'];
		$title = $arr_data['title'];
		$spk = $arr_data['spk'];
		$webd_venue = $arr_data['venue'];
		$webd_email_content = $arr_data['email_content'];
		$link = $arr_data['link'];
		$order = $arr_data['order'];
		$email_ord = $arr_data['email'];

		global $woocommerce;
		$mailer = $woocommerce->mailer();
		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		$number_item = count($order->get_items());
		if($number_item > 0){
			for($i=1;$i<=$number_item; $i++){
				$attendee = get_post_meta($order_id,'att_info-'.$product_id.'_'.$i, true);
				if($attendee!=''){
					$attendee = explode("][",$attendee);
					if(!empty($attendee)){
						$i=0;
						foreach($attendee as $item){
							$i++;
							$item = explode("||",$item);
							$_email = isset($item[0]) && $item[0]!='' ? $item[0] : '';
							if($_email!='' && (filter_var($_email, FILTER_VALIDATE_EMAIL)) && $email_ord != $_email ){
								$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
								$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
								$cus_name = $f_name.' '.$l_name;
								// subject
								$email_subject = get_option('webd_email_subject');
								if($email_subject ==''){
									$email_subject = esc_html__( 'Email Reminder from ', 'WEBDWooEVENT' ).get_bloginfo( 'name', 'display' );
								}else{
									$email_subject = str_replace('[eventitle]', $title, $email_subject);
									$email_subject = str_replace('[eventdate]', $date, $email_subject);
									$email_subject = str_replace('[eventspk]', $spk, $email_subject);
									$email_subject = str_replace('[eventvn]', $webd_venue, $email_subject);
									$email_subject = str_replace('[customer_name]', $cus_name, $email_subject);
									$email_subject = str_replace('[f_name]', $f_name, $email_subject);
									$email_subject = str_replace('[l_name]', $l_name, $email_subject);
								}
								$subject = apply_filters(
									'wooevent_reminder_email_subject',$email_subject, $order, $product_id
								);
								// Message title.
								$message_title = apply_filters(
									'wooevent_reminder_email_title',$email_subject,$order,$product_id
								);
								if(preg_replace('/\s+/', '', $webd_email_content) == ''){
									$body = '<p>' . esc_html__( 'This is an automatic reminder of the following event', 'WEBDWooEVENT' ) . '</p>' .
										'<h2><a href="'.$link.'">'.$title.'</a></h2>'.
										'<p><strong>' . esc_html__( 'Date', 'WEBDWooEVENT' ) . ': </strong>'.$date.'</p>'.
										'<p>' . esc_html__( 'Additional information', 'WEBDWooEVENT' ) . '</p>'.
										'<p>' . esc_html__( 'This is a reminder that you had registered for "','WEBDWooEVENT').$title.'".'.esc_html__('We look forward to seeing you', 'WEBDWooEVENT' ) . '</p>';
									$ct_email = '';
									if($nb == 2 || $nb == 3){
										$ct_email = get_option('webd_email_content_'.$nb);	
									}
									if($ct_email==''){
										$ct_email = get_option('webd_email_content');
									}
									if($ct_email!=''){
										$ct_email = str_replace('[eventitle]', $title, $ct_email);
										$ct_email = str_replace('[eventdate]', $date, $ct_email);
										$ct_email = str_replace('[eventlink]', $link, $ct_email);
										$ct_email = str_replace('[eventspk]', $spk, $ct_email);
										$ct_email = str_replace('[eventvn]', $webd_venue, $ct_email);
										$ct_email = str_replace('[customer_name]', $cus_name, $ct_email);
										$ct_email = str_replace('[f_name]', $f_name, $ct_email);
										$ct_email = str_replace('[l_name]', $l_name, $ct_email);
										$body = $ct_email;
									}
								}else{
									$webd_email_content = str_replace('[eventitle]', $title, $webd_email_content);
									$webd_email_content = str_replace('[eventdate]', $date, $webd_email_content);
									$webd_email_content = str_replace('[eventlink]', $link, $webd_email_content);
									$webd_email_content = str_replace('[eventspk]', $spk, $webd_email_content);
									$webd_email_content = str_replace('[eventvn]', $webd_venue, $webd_email_content);
									$webd_email_content = str_replace('[customer_name]', $cus_name, $webd_email_content);
									$webd_email_content = str_replace('[f_name]', $f_name, $webd_email_content);
									$webd_email_content = str_replace('[l_name]', $l_name, $webd_email_content);
									$body = $webd_email_content;
								}
								$message_body = apply_filters(
									'wooevent_reminder_email_message',
									$body,
									$order,
									$product_id
								);
								// Sets the message template.
								$message = apply_filters( 
									'wooevent_reminder_email_template',
									$mailer->wrap_message( $message_title, $message_body ),
									$message_title, 
									$message_body,
									$webd_email_content
								);
								// Send the email.
								$mailer->send( $_email, $subject, $message, $headers, '' );
							}
						}
					}
				}
			}
		}	
	}


	// register metadata
	
	function email_reminder_metadata(array $meta_boxes){
		if(get_option('webd_emreminder_single') == 'on'){
			$event_reminder_time = array(
				array( 'id' => 'webd_email_delay',  'name' => esc_html__('The First time', 'WEBDWooEVENT'), 'type' => 'text' ),
				array( 'id' => 'webd_email_timeformat', 'name' => 'Type', 'type' => 'select', 'options' => array( 
					'' => esc_html__('', 'WEBDWooEVENT'), 
					'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
					'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
					'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
					'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
					'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
					'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
				),
				'desc' => esc_html__('Select type of time', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
				array( 'id' => 'webd_email_content',  'name' => esc_html__('Content of Email', 'WEBDWooEVENT'), 'type' => 'wysiwyg', 'options' => array( 'editor_height' => '150' ) ),
				array( 'id' => 'webd_emdelay_2',  'name' => esc_html__('The Second time', 'WEBDWooEVENT'), 'type' => 'text' ),
				array( 'id' => 'webd_emtimeformat_2', 'name' => 'Type', 'type' => 'select', 'options' => array( 
					'' => esc_html__('', 'WEBDWooEVENT'), 
					'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
					'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
					'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
					'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
					'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
					'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
				),
				'desc' => esc_html__('Select type of time', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
				array( 'id' => 'webd_email_content_2',  'name' => esc_html__('Content of second Email', 'WEBDWooEVENT'), 'type' => 'wysiwyg', 'options' => array( 'editor_height' => '150' ) ),
				array( 'id' => 'webd_emdelay_3',  'name' => esc_html__('The Third time', 'WEBDWooEVENT'), 'type' => 'text' ),
				array( 'id' => 'webd_emtimeformat_3', 'name' => 'Type', 'type' => 'select', 'options' => array( 
					'' => esc_html__('', 'WEBDWooEVENT'), 
					'1' => esc_html__( 'seconds', 'WEBDWooEVENT' ),
					'60' => esc_html__( 'minutes', 'WEBDWooEVENT' ),
					'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
					'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
					'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
					'18144000' => esc_html__( 'months', 'WEBDWooEVENT' ),
				),
				'desc' => esc_html__('Select type of time', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
				array( 'id' => 'webd_email_content_3',  'name' => esc_html__('Content of Third Email', 'WEBDWooEVENT'), 'type' => 'wysiwyg', 'options' => array( 'editor_height' => '150' ) ),
			);
			$meta_boxes[] = array(
				'title' => __('Email reminder Setting','WEBDWooEVENT'),
				'context' => 'side',
				'pages' => 'product',
				'fields' => $event_reminder_time,
				'priority' => '',
			);
		}
		
		if(get_option('webd_email_reminder_fb') != 'off' && get_option('webd_reminder_fbsg') == 'on'){
			$event_reminder_fb = array(
				array( 'id' => 'webd_email_fbdelay',  'name' => esc_html__('Time for sending', 'WEBDWooEVENT'), 'type' => 'text' ),
				array( 'id' => 'webd_email_fbtimefm', 'name' => 'Type', 'type' => 'select', 'options' => array( 
					'' => esc_html__('', 'WEBDWooEVENT'), 
					'3600' => esc_html__( 'hours', 'WEBDWooEVENT' ),
					'86400' => esc_html__( 'days', 'WEBDWooEVENT' ),
					'604800' => esc_html__( 'weeks', 'WEBDWooEVENT' ),
				),
				'desc' => esc_html__('Select type of time', 'WEBDWooEVENT') , 'repeatable' => false, 'multiple' => false),
				array( 'id' => 'webd_email_fbcontent',  'name' => esc_html__('Content of Email', 'WEBDWooEVENT'), 'type' => 'wysiwyg', 'options' => array( 'editor_height' => '150' ) ),
			);
			$meta_boxes[] = array(
				'title' => __('Email Feedback Setting','WEBDWooEVENT'),
				'context' => 'side',
				'pages' => 'product',
				'fields' => $event_reminder_fb,
				'priority' => '',
			);
		}
		return $meta_boxes;
	}

}
$WEBD_WooEvent_Reminder_Email = new WEBD_WooEvent_Reminder_Email();