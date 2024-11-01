<?php
function webd_custom_admin_css() {
	$webd_main_purpose = get_option('webd_main_purpose');
	$webd_layout_purpose = get_option('webd_slayout_purpose');
	echo '<style>';
	if($webd_layout_purpose == 'woo' && ($webd_main_purpose!='event' && $webd_main_purpose!='')){
		echo '
		.post-type-product .postbox-container #custom-field.postbox,
		.post-type-product .postbox-container #sponsors-of-event.postbox,
		.post-type-product .postbox-container #event-settings.postbox,
		.post-type-product .postbox-container #location-settings.postbox,
		.post-type-product .postbox-container #layout-settings.postbox{ height:0; overflow: hidden; margin-bottom:0; border:0;}
		.post-type-product .postbox-container #custom-field.postbox.active-c,
		.post-type-product .postbox-container #sponsors-of-event.postbox.active-c,
		.post-type-product .postbox-container #event-settings.postbox.active-c,
		.post-type-product .postbox-container #location-settings.postbox.active-c,
		.post-type-product .postbox-container #layout-settings.postbox.active-c{ height:auto; overflow: visible;    margin-bottom: 20px; border: 1px solid #e5e5e5;}
		';
	}
	echo '.post-type-product #ui-datepicker-div .ui-datepicker-year{display: inline-block !important;}
	.edit_form_line input.cat.textfield[name=cat]{display: inline-block !important;}</style>';
}
add_action( 'admin_head', 'webd_custom_admin_css' );
function webd_get_product_to_duplicate( $id ) {
	global $wpdb;
	
	$id = absint( $id );
	
	if ( ! $id ) {
		return false;
	}
	
	$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
	
	if ( isset( $post->post_type ) && $post->post_type == "revision" ) {
		$id   = $post->post_parent;
		$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
	}
	
	return $post[0];
}
function webd_duplicate_post_taxonomies( $id, $new_id, $post_type ) {
	$exclude    = array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_taxonomies', array() ) );
	$taxonomies = array_diff( get_object_taxonomies( $post_type ), $exclude );

	foreach ( $taxonomies as $taxonomy ) {
		$post_terms       = wp_get_object_terms( $id, $taxonomy );
		$post_terms_count = sizeof( $post_terms );

		for ( $i = 0; $i < $post_terms_count; $i++ ) {
			wp_set_object_terms( $new_id, $post_terms[ $i ]->slug, $taxonomy, true );
		}
	}
}

/**
 * Copy the meta information of a post to another post.
 *
 * @param mixed $id
 * @param mixed $new_id
 */
function webd_duplicate_post_meta( $id, $new_id ) {
	global $wpdb;

	$sql     = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", absint( $id ) );
	$exclude = array_map( 'esc_sql', array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_meta', array( 'total_sales' ) ) ) );

	if ( sizeof( $exclude ) ) {
		$sql .= " AND meta_key NOT IN ( '" . implode( "','", $exclude ) . "' )";
	}

	$post_meta = $wpdb->get_results( $sql );

	if ( sizeof( $post_meta ) ) {
		$sql_query_sel = array();
		$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

		foreach ( $post_meta as $post_meta_row ) {
			$sql_query_sel[] = $wpdb->prepare( "SELECT %d, %s, %s", $new_id, $post_meta_row->meta_key, $post_meta_row->meta_value );
		}

		$sql_query .= implode( " UNION ALL ", $sql_query_sel );
		$wpdb->query( $sql_query );
	}
}
function webd_duplicate_product( $post, $parent = 0, $post_status = '' ) {
	global $wpdb;

	$new_post_author    = wp_get_current_user();
	$new_post_date      = current_time( 'mysql' );
	$new_post_date_gmt  = get_gmt_from_date( $new_post_date );

	if ( $parent > 0 ) {
		$post_parent        = $parent;
		$post_status        = $post_status ? $post_status : 'publish';
	} else {
		$post_parent        = $post->post_parent;
		$post_status        = $post_status ? $post_status : 'draft';
	}

	// Insert the new template in the post table
	$wpdb->insert(
		$wpdb->posts,
		array(
			'post_author'               => $new_post_author->ID,
			'post_date'                 => $new_post_date,
			'post_date_gmt'             => $new_post_date_gmt,
			'post_content'              => $post->post_content,
			'post_content_filtered'     => $post->post_content_filtered,
			'post_title'                => $post->post_title,
			'post_excerpt'              => $post->post_excerpt,
			'post_status'               => $post_status,
			'post_type'                 => $post->post_type,
			'comment_status'            => $post->comment_status,
			'ping_status'               => $post->ping_status,
			'post_password'             => $post->post_password,
			'to_ping'                   => $post->to_ping,
			'pinged'                    => $post->pinged,
			'post_modified'             => $new_post_date,
			'post_modified_gmt'         => $new_post_date_gmt,
			'post_parent'               => $post_parent,
			'menu_order'                => $post->menu_order,
			'post_mime_type'            => $post->post_mime_type
		)
	);

	$new_post_id = $wpdb->insert_id;

	// Copy the taxonomies
	webd_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

	// Copy the meta information
	webd_duplicate_post_meta( $post->ID, $new_post_id );

	// Copy the children (variations)
	if ( ( $children_products = get_children( 'post_parent=' . $post->ID . '&post_type=product_variation' ) ) ) {
		foreach ( $children_products as $child ) {
			webd_duplicate_product( webd_get_product_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
		}
	}

	return $new_post_id;
}
function woometa_update($_post_e,$webd_ID,$post_id){
	if(isset($_post_e['_downloadable'])){
		update_post_meta($webd_ID, '_downloadable', sanitize_text_field($_post_e['_downloadable']));
	}
	if(isset($_post_e['_virtual'])){
		update_post_meta($webd_ID, '_virtual', sanitize_text_field($_post_e['_virtual']));
	}
	update_post_meta($webd_ID, '_visibility', sanitize_text_field($_post_e['_visibility']));
	update_post_meta($webd_ID, '_stock_status', sanitize_text_field($_post_e['_stock_status']));
	update_post_meta( $webd_ID, '_visibility', 'visible' );
    //update_post_meta( $webd_ID, '_stock_status', 'instock');
	update_post_meta($webd_ID, '_regular_price', sanitize_text_field($_post_e['_regular_price']));
	update_post_meta($webd_ID, '_sale_price', sanitize_text_field($_post_e['_sale_price']));
	update_post_meta($webd_ID, '_sold_individually', sanitize_text_field($_post_e['_sold_individually']));
	//deposit plugin support
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-deposits/woocommmerce-deposits.php' ) ) {
		if(isset($_post_e['_wc_deposits_enable_deposit'])){
			$enable_deposit = isset($_post_e['_wc_deposits_enable_deposit']) ? 'yes' : 'no';
			$force_deposit = isset($_post_e['_wc_deposits_force_deposit']) ? 'yes' : 'no';
			$enable_persons = isset($_post_e['_wc_deposits_enable_per_person']) ? 'yes' : 'no';
			$amount_type = (isset($_post_e['_wc_deposits_amount_type']) &&
								   (sanitize_text_field($_post_e['_wc_deposits_amount_type']) === 'fixed' ||
									sanitize_text_field($_post_e['_wc_deposits_amount_type']) === 'percent')) ?
									  sanitize_text_field($_post_e['_wc_deposits_amount_type']) : 'fixed';
			$amount = isset($_post_e['_wc_deposits_deposit_amount']) &&
					  is_numeric($_post_e['_wc_deposits_deposit_amount']) ? floatval($_post_e['_wc_deposits_deposit_amount']) : 0.0;
		
			if ($amount <= 0 || ($amount_type === 'percent' && $amount >= 100)) {
			  $enable_deposit = 'no';
			  $amount = '';
			}
		
			update_post_meta($webd_ID, '_wc_deposits_enable_deposit', $enable_deposit);
			update_post_meta($webd_ID, '_wc_deposits_force_deposit', $force_deposit);
			update_post_meta($webd_ID, '_wc_deposits_amount_type', $amount_type);
			update_post_meta($webd_ID, '_wc_deposits_deposit_amount', $amount);
		}else if(isset($_post_e['_wc_deposit_enabled'])){
			$enable_deposit = isset($_post_e['_wc_deposit_enabled']) ? sanitize_text_field($_post_e['_wc_deposit_enabled']) : '';
			$deposit_type = isset($_post_e['_wc_deposit_type']) ? sanitize_text_field($_post_e['_wc_deposit_type']) : '';
			$deposit_amount = isset($_post_e['_wc_deposit_amount']) ? sanitize_text_field($_post_e['_wc_deposit_amount']) : 'float';
			$payment_plans = isset($_post_e['_wc_deposit_payment_plans']) ? sanitize_text_field($_post_e['_wc_deposit_payment_plans']) : 'int';
			$selected_type = isset($_post_e['_wc_deposit_selected_type']) ? sanitize_text_field($_post_e['_wc_deposit_selected_type']) : '';
			$booking_persons = isset($_post_e['_wc_deposit_multiple_cost_by_booking_persons']) ? sanitize_text_field($_post_e['_wc_deposit_multiple_cost_by_booking_persons']) : 'issetyesno';
			
			update_post_meta($webd_ID, '_wc_deposit_enabled', $enable_deposit);
			update_post_meta($webd_ID, '_wc_deposit_type', $deposit_type);
			update_post_meta($webd_ID, '_wc_deposit_amount', $deposit_amount);
			update_post_meta($webd_ID, '_wc_deposit_payment_plans', $payment_plans);
			update_post_meta($webd_ID, '_wc_deposit_selected_type', $selected_type);
			update_post_meta($webd_ID, '_wc_deposit_multiple_cost_by_booking_persons', $booking_persons);
		}
	}
	//end
	
	if($_post_e['_sale_price']==''){
		update_post_meta( $webd_ID, '_price', isset($_post_e['_regular_price']) ? sanitize_text_field($_post_e['_regular_price']): '' );
	}else{
		update_post_meta( $webd_ID, '_price', isset($_post_e['_sale_price']) ? sanitize_text_field($_post_e['_sale_price']): '' );
	}
	update_post_meta($webd_ID, '_purchawebd_note', sanitize_text_field($_post_e['_purchawebd_note']));
	update_post_meta($webd_ID, '_featured', sanitize_text_field($_post_e['current_featured']));
	update_post_meta($webd_ID, '_weight', sanitize_text_field($_post_e['_weight']));
	update_post_meta($webd_ID, '_length', sanitize_text_field($_post_e['_length']));
	update_post_meta($webd_ID, '_width', sanitize_text_field($_post_e['_width']));
	update_post_meta($webd_ID, '_height', sanitize_text_field($_post_e['_height']));
	update_post_meta($webd_ID, '_sku', sanitize_text_field($_post_e['_sku']));
	update_post_meta($webd_ID, '_product_attributes', get_post_meta($post_id,'_product_attributes', true ));
	update_post_meta($webd_ID, '_sale_price_dates_from', sanitize_text_field($_post_e['_sale_price_dates_from']));
	update_post_meta($webd_ID, '_sale_price_dates_to', sanitize_text_field($_post_e['_sale_price_dates_to']));
	update_post_meta($webd_ID, '_manage_stock', sanitize_text_field($_post_e['_manage_stock']));
	update_post_meta($webd_ID, '_backorders', sanitize_text_field($_post_e['_backorders']));
	update_post_meta($webd_ID, '_stock', sanitize_text_field($_post_e['_stock']));
	update_post_meta($webd_ID, '_product_image_gallery', sanitize_text_field($_post_e['product_image_gallery'])); //the comma separated attachment id's of the product images
	//variation
	update_post_meta($webd_ID, '_min_variation_price', get_post_meta($post_id,'_min_variation_price', true ));
	update_post_meta($webd_ID, '_max_variation_price', get_post_meta($post_id,'_max_variation_price', true ));
	update_post_meta($webd_ID, '_min_price_variation_id', get_post_meta($post_id,'_min_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_price_variation_id', get_post_meta($post_id,'_max_price_variation_id', true ));
	update_post_meta($webd_ID, '_min_variation_regular_price', get_post_meta($post_id,'_min_variation_regular_price', true ));
	update_post_meta($webd_ID, '_max_variation_regular_price', get_post_meta($post_id,'_max_variation_regular_price', true ));
	update_post_meta($webd_ID, '_min_regular_price_variation_id', get_post_meta($post_id,'_min_regular_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_regular_price_variation_id', get_post_meta($post_id,'_max_regular_price_variation_id', true ));
	update_post_meta($webd_ID, '_min_variation_sale_price', get_post_meta($post_id,'_min_variation_sale_price', true ));
	update_post_meta($webd_ID, '_max_variation_sale_price', get_post_meta($post_id,'_max_variation_sale_price', true ));
	update_post_meta($webd_ID, '_min_sale_price_variation_id', get_post_meta($post_id,'_min_sale_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_sale_price_variation_id', get_post_meta($post_id,'_max_sale_price_variation_id', true ));
	
	//support min max quantity addon
	if(isset($_post_e['minimum_allowed_quantity']) && $_post_e['minimum_allowed_quantity']!=''){
		update_post_meta($webd_ID, 'minimum_allowed_quantity', sanitize_text_field($_post_e['minimum_allowed_quantity']));
	}
	if(isset($_post_e['maximum_allowed_quantity']) && $_post_e['maximum_allowed_quantity']!=''){
		update_post_meta($webd_ID, 'maximum_allowed_quantity', sanitize_text_field($_post_e['maximum_allowed_quantity']));
	}
	if(isset($_post_e['group_of_quantity']) && $_post_e['group_of_quantity']!=''){
		update_post_meta($webd_ID, 'group_of_quantity', sanitize_text_field($_post_e['group_of_quantity']));
	}
	if(isset($_post_e['minmax_do_not_count']) && $_post_e['minmax_do_not_count']!=''){
		update_post_meta($webd_ID, 'minmax_do_not_count', sanitize_text_field($_post_e['minmax_do_not_count']));
	}
	if(isset($_post_e['minmax_cart_exclude']) && $_post_e['minmax_cart_exclude']!=''){
		update_post_meta($webd_ID, 'minmax_cart_exclude', sanitize_text_field($_post_e['minmax_cart_exclude']));
	}
	if(isset($_post_e['minmax_category_group_of_exclude']) && $_post_e['minmax_category_group_of_exclude']!=''){
		update_post_meta($webd_ID, 'minmax_category_group_of_exclude', sanitize_text_field($_post_e['minmax_category_group_of_exclude']));
	}
	save_tmot_meta( $webd_ID, $_post_e );
	do_action('webd_save_custom_meta_recurr',$webd_ID,$post_id,$_post_e,$std=false);
	
	// Assign sizes and colors to the main product
	if(isset($_post_e['product-type']) && sanitize_text_field($_post_e['product-type'])=='variable'){
		if ($children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation' )) {
			foreach ( $children_products as $child ) {
				webd_duplicate_product( webd_get_product_to_duplicate( $child->ID ), $webd_ID, $child->post_status );
			}
		}
	}
	webd_duplicate_post_taxonomies($post_id, $webd_ID, 'product' );	
	//remove the product categories
//	wp_set_object_terms($webd_ID, '', 'product_cat', true);
//	//array list of all the categories this product belongs to
//	$product_categories = $_post_e['tax_input']['product_cat'];
//	//add product categories to the product
//	foreach($product_categories as $product_category) {
//		wp_set_object_terms($webd_ID, intval($product_category), 'product_cat', true);
//	}
//	//remove the product tags
//	wp_set_object_terms($webd_ID, '', 'product_tag', true);
//	//array list of all the categories this product belongs to
//	$product_tags = $_post_e['tax_input']['product_tag'];
//	//add product categories to the product
//	foreach($product_tags as $product_tag) {
//		$term_object = term_exists($product_tag, 'product_tag');
//		if($term_object == NULL) {
//			//create the category
//			$term_object = wp_insert_term($product_category, 'product_cat', array(
//				'parent' => 0 //parent term id if it should be a sub-category
//			));
//		}
//		 
//		wp_set_object_terms($webd_ID, intval($term_object['term_id']), 'product_tag', true);
//		 
//		unset($term_object);
//	}
//	 
//	/*
//	* update the product type.
//	*
//	* the product type can be eiher simple, grouped, external or variable.
//	*/
//	$term_object = term_exists($_post_e['product-type'], 'product_type');
//	if($term_object == NULL) {
//	$term_object = wp_insert_term($_post_e['product-type'], 'product_type');
//	}
//	wp_set_object_terms($webd_ID, intval($term_object['term_id']), 'product_type', true);
//	unset($term_object);

}
//update recurren event
function webd_update_recurren($_post_e,$webd_ID,$post_id,$stdate){
	
	$arr = array(
		'ID'           				=> $webd_ID,
		'post_author'               => sanitize_text_field( $_POST['title'] ),
		'post_content'              => sanitize_text_field($_post_e['post_content']),
		'post_title'                => sanitize_text_field($_post_e['post_title']),
		'post_excerpt'              => sanitize_text_field($_post_e['post_excerpt']),
		'post_status'               => sanitize_text_field($_post_e['post_status']),
		'comment_status'            => sanitize_text_field($_post_e['comment_status']),
		'ping_status'               => sanitize_text_field($_post_e['ping_status']),
		'post_password'             => sanitize_text_field($_post_e['post_password']),
		'post_parent'               => sanitize_text_field($_post_e['post_parent']),
		'menu_order'                => sanitize_text_field($_post_e['menu_order']),
		'post_mime_type'            => sanitize_text_field($_post_e['post_mime_type'])
	);
	if(isset($stdate)){
		$arr['post_title'] = apply_filters( 'webd_change_title_recurring', $arr['post_title'], $stdate );
	}
	wp_update_post( $arr );
	
	if(isset($_post_e['_downloadable'])){
		update_post_meta($webd_ID, '_downloadable', sanitize_text_field($_post_e['_downloadable']));
	}
	if(isset($_post_e['_virtual'])){
		update_post_meta($webd_ID, '_virtual', sanitize_text_field($_post_e['_virtual']));
	}
	update_post_meta($webd_ID, '_visibility', sanitize_text_field($_post_e['_visibility']));
	update_post_meta($webd_ID, '_stock_status', sanitize_text_field($_post_e['_stock_status']));
	update_post_meta( $webd_ID, '_visibility', 'visible' );
    //update_post_meta( $webd_ID, '_stock_status', 'instock');
	update_post_meta($webd_ID, '_regular_price', sanitize_text_field($_post_e['_regular_price']));
	update_post_meta($webd_ID, '_sale_price', sanitize_text_field($_post_e['_sale_price']));
	update_post_meta($webd_ID, '_sold_individually', sanitize_text_field($_post_e['_sold_individually']));
	//deposit plugin support
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-deposits/woocommmerce-deposits.php' ) ) {
		if(isset($_post_e['_wc_deposits_enable_deposit'])){
			$enable_deposit = isset($_post_e['_wc_deposits_enable_deposit']) ? 'yes' : 'no';
			$force_deposit = isset($_post_e['_wc_deposits_force_deposit']) ? 'yes' : 'no';
			$enable_persons = isset($_post_e['_wc_deposits_enable_per_person']) ? 'yes' : 'no';
			$amount_type = (isset($_post_e['_wc_deposits_amount_type']) &&
								   (sanitize_text_field($_post_e['_wc_deposits_amount_type']) === 'fixed' ||
									sanitize_text_field($_post_e['_wc_deposits_amount_type']) === 'percent')) ?
									  $_post_e['_wc_deposits_amount_type'] : 'fixed';
			$amount = isset($_post_e['_wc_deposits_deposit_amount']) &&
					  is_numeric($_post_e['_wc_deposits_deposit_amount']) ? floatval($_post_e['_wc_deposits_deposit_amount']) : 0.0;
		
			if ($amount <= 0 || ($amount_type === 'percent' && $amount >= 100)) {
			  $enable_deposit = 'no';
			  $amount = '';
			}
		
			update_post_meta($webd_ID, '_wc_deposits_enable_deposit', $enable_deposit);
			update_post_meta($webd_ID, '_wc_deposits_force_deposit', $force_deposit);
			update_post_meta($webd_ID, '_wc_deposits_amount_type', $amount_type);
			update_post_meta($webd_ID, '_wc_deposits_deposit_amount', $amount);
		}else if(isset($_post_e['_wc_deposit_enabled'])){
			$enable_deposit = isset($_post_e['_wc_deposit_enabled']) ? sanitize_text_field($_post_e['_wc_deposit_enabled']) : '';
			$deposit_type = isset($_post_e['_wc_deposit_type']) ? sanitize_text_field($_post_e['_wc_deposit_type']) : '';
			$deposit_amount = isset($_post_e['_wc_deposit_amount']) ? sanitize_text_field($_post_e['_wc_deposit_amount']) : 'float';
			$payment_plans = isset($_post_e['_wc_deposit_payment_plans']) ? sanitize_text_field($_post_e['_wc_deposit_payment_plans']) : 'int';
			$selected_type = isset($_post_e['_wc_deposit_selected_type']) ? sanitize_text_field($_post_e['_wc_deposit_selected_type']) : '';
			$booking_persons = isset($_post_e['_wc_deposit_multiple_cost_by_booking_persons']) ? sanitize_text_field($_post_e['_wc_deposit_multiple_cost_by_booking_persons']) : 'issetyesno';
			
			update_post_meta($webd_ID, '_wc_deposit_enabled', $enable_deposit);
			update_post_meta($webd_ID, '_wc_deposit_type', $deposit_type);
			update_post_meta($webd_ID, '_wc_deposit_amount', $deposit_amount);
			update_post_meta($webd_ID, '_wc_deposit_payment_plans', $payment_plans);
			update_post_meta($webd_ID, '_wc_deposit_selected_type', $selected_type);
			update_post_meta($webd_ID, '_wc_deposit_multiple_cost_by_booking_persons', $booking_persons);
		}
	}
	//end
	if($_post_e['_sale_price']==''){
		update_post_meta( $webd_ID, '_price', isset($_post_e['_regular_price']) ? sanitize_text_field($_post_e['_regular_price']): '' );
	}else{
		update_post_meta( $webd_ID, '_price', isset($_post_e['_sale_price']) ? sanitize_text_field($_post_e['_sale_price']): '' );
	}
	update_post_meta($webd_ID, '_purchawebd_note', sanitize_text_field($_post_e['_purchawebd_note']));
	update_post_meta($webd_ID, '_featured', sanitize_text_field($_post_e['current_featured']));
	update_post_meta($webd_ID, '_weight', sanitize_text_field($_post_e['_weight']));
	update_post_meta($webd_ID, '_length', sanitize_text_field($_post_e['_length']));
	update_post_meta($webd_ID, '_width', sanitize_text_field($_post_e['_width']));
	update_post_meta($webd_ID, '_height', sanitize_text_field($_post_e['_height']));
	update_post_meta($webd_ID, '_sku', sanitize_text_field($_post_e['_sku']));
	update_post_meta($webd_ID, '_product_attributes', get_post_meta($post_id,'_product_attributes', true ));
	update_post_meta($webd_ID, '_sale_price_dates_from', sanitize_text_field($_post_e['_sale_price_dates_from']));
	update_post_meta($webd_ID, '_sale_price_dates_to', sanitize_text_field($_post_e['_sale_price_dates_to']));
	update_post_meta($webd_ID, '_manage_stock', sanitize_text_field($_post_e['_manage_stock']));
	update_post_meta($webd_ID, '_backorders', sanitize_text_field($_post_e['_backorders']));
	if(get_option('webd_enable_recstock') == 'yes'){
		update_post_meta($webd_ID, '_stock', sanitize_text_field($_post_e['_stock']));
	}
	update_post_meta($webd_ID, '_product_image_gallery', sanitize_text_field($_post_e['product_image_gallery'])); //the comma separated attachment id's of the product images
	//variation
	update_post_meta($webd_ID, '_min_variation_price', get_post_meta($post_id,'_min_variation_price', true ));
	update_post_meta($webd_ID, '_max_variation_price', get_post_meta($post_id,'_max_variation_price', true ));
	update_post_meta($webd_ID, '_min_price_variation_id', get_post_meta($post_id,'_min_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_price_variation_id', get_post_meta($post_id,'_max_price_variation_id', true ));
	update_post_meta($webd_ID, '_min_variation_regular_price', get_post_meta($post_id,'_min_variation_regular_price', true ));
	update_post_meta($webd_ID, '_max_variation_regular_price', get_post_meta($post_id,'_max_variation_regular_price', true ));
	update_post_meta($webd_ID, '_min_regular_price_variation_id', get_post_meta($post_id,'_min_regular_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_regular_price_variation_id', get_post_meta($post_id,'_max_regular_price_variation_id', true ));
	update_post_meta($webd_ID, '_min_variation_sale_price', get_post_meta($post_id,'_min_variation_sale_price', true ));
	update_post_meta($webd_ID, '_max_variation_sale_price', get_post_meta($post_id,'_max_variation_sale_price', true ));
	update_post_meta($webd_ID, '_min_sale_price_variation_id', get_post_meta($post_id,'_min_sale_price_variation_id', true ));
	update_post_meta($webd_ID, '_max_sale_price_variation_id', get_post_meta($post_id,'_max_sale_price_variation_id', true ));
	do_action('webd_save_custom_meta_recurr',$webd_ID,$post_id,$_post_e,$stdate);
	// support tm product addon
	if(function_exists('tc_get_post_meta')){
		$tm_meta= tc_get_post_meta( $post_id , 'tm_meta' , true );
				
		if ( !empty($tm_meta) 
			&& is_array($tm_meta) 
			&& isset($tm_meta['tmfbuilder']) 
			&& is_array($tm_meta['tmfbuilder'])
			){
			tc_update_post_meta( $webd_ID, 'tm_meta', TM_EPO_HELPER()->recreate_element_ids($tm_meta) );
		}
	}
	
	if(isset($_post_e['product-type']) && sanitize_text_field($_post_e['product-type'])=='variable'){
		// delete old variation
		$child_products = get_children( 'post_parent=' . $webd_ID . '&post_type=product_variation' );
		if (is_array($child_products) && count($child_products) > 0) {
			// Delete all the Children of the Parent Page
			foreach($child_products as $child){
				wp_delete_post($child->ID, true);
			}
		}
		// add new instead of update
		if ($children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation' )) {
			foreach ( $children_products as $child ) {
				webd_duplicate_product( webd_get_product_to_duplicate( $child->ID ), $webd_ID, $child->post_status );
			}
		}
	}
	//remove the product categories
	wp_set_object_terms($webd_ID, null, 'product_cat');
	//array list of all the categories this product belongs to
	$product_categories = $_post_e['tax_input']['product_cat'];
	//add product categories to the product
	foreach($product_categories as $product_category) {
		wp_set_object_terms($webd_ID, intval($product_category), 'product_cat', true);
	}
	
	$df_cat = get_option('default_product_cat');
	if(!in_array($df_cat, $product_categories)){
		$term = get_term( $df_cat, 'product_cat' );
		$slug = $term->slug;
		wp_remove_object_terms( $webd_ID, $slug, 'product_cat' );
	}
	//remove the product tags
	wp_set_object_terms($webd_ID, null, 'product_tag');
	//array list of all the categories this product belongs to
	$product_tags = $_post_e['tax_input']['product_tag'];
	$product_tags = explode(",",$product_tags);
	//add product categories to the product
	if(!empty($product_tags)){
		foreach($product_tags as $product_tag) {
			$term_object = term_exists($product_tag, 'product_tag');
			if($term_object !== NULL) {
				wp_set_object_terms($webd_ID, intval($term_object['term_id']), 'product_tag', true);
			}
			unset($term_object);
		}
	}
	 
	
//	* update the product type.
//	*
//	* the product type can be eiher simple, grouped, external or variable.
//	
	$term_object = term_exists($_post_e['product-type'], 'product_type');
	if($term_object == NULL) {
	$term_object = wp_insert_term($_post_e['product-type'], 'product_type');
	}
	wp_set_object_terms($webd_ID, intval($term_object['term_id']), 'product_type', true);
	unset($term_object);

}
//edit link recurrence
add_filter( 'get_edit_post_link', 'webd_edit_post_link', 10, 3 );
function webd_edit_post_link( $url, $post_id, $context) {
    $ex_recurr = get_post_meta($post_id,'recurren_ext', true );
	if($ex_recurr!=''){
		$ex_recurr  = explode("_",$ex_recurr);
		if(isset($ex_recurr[1]) && $ex_recurr[1]!=''){
			$hasposts = get_post($ex_recurr[1]);
			if(isset($hasposts->post_type) && ($hasposts->post_type == 'product')){
				$url = add_query_arg( array('post'=>$ex_recurr[1]),  $url);
			}
		}
	}
    return $url;
}
//
add_filter('post_row_actions','webd_change_edit_product_rows',10, 2 );
function webd_change_edit_product_rows($actions,$post) {
	$ex_recurr = get_post_meta($post->ID,'recurren_ext', true );
	$ex_recurr  = explode("_",$ex_recurr);
	if(isset($ex_recurr[1]) && $ex_recurr[1]!='' && ( FALSE !== get_post_status( $ex_recurr[1] )) ){
		$can_edit_post = current_user_can( 'edit_post', $post->ID );
		if ( $can_edit_post ) {
		  $actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( esc_html__( 'Edit all','WEBDWooEVENT' ) ) . '">' . esc_html__( 'Edit all','WEBDWooEVENT' ) . '</a>';
		  if($ex_recurr[1] != $post->ID){
			  $actions['inline hide-if-no-js'] = '<a href="' . add_query_arg( array('post'=>$post->ID), get_edit_post_link( $post->ID, true )) . '" class="editsingle" title="' . esc_attr( esc_html__( 'Edit single','WEBDWooEVENT' ) ) . '">' . esc_html__( 'Edit single','WEBDWooEVENT' ) . '</a>';
		  }
		  $trash_link = str_replace('action=edit', 'action=trash-all', get_edit_post_link( $post->ID, true ));
		  $actions['trash trash-all'] = '<a href="' . add_query_arg( array('post_type'=> 'product'),$trash_link ) . '" class="editsingle" title="' . esc_attr( esc_html__( 'Trash all','WEBDWooEVENT' ) ) . '">' . esc_html__( 'Trash all','WEBDWooEVENT' ) . '</a>';
		}
	}
	return $actions;
}
add_action('init','webd_trash_all_recurring');
if(!function_exists('webd_trash_all_recurring')){
	function webd_trash_all_recurring() {
		if(is_admin() && isset($_GET['action']) && sanitize_text_field($_GET['action']) =='trash-all' && isset($_GET['post_type']) && sanitize_text_field($_GET['post_type']) =='product' && isset($_GET['post']) && sanitize_text_field($_GET['post']) !=''){
			if ( current_user_can( 'edit_post', sanitize_text_field($_GET['post']) ) ) {
				$ex_recurr = get_post_meta(sanitize_text_field($_GET['post']),'recurren_ext', true );
				if($ex_recurr!=''){
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
								'value'   => $ex_recurr,
								'compare' => '=',
							),
						),
					);
					$ex_posts = get_posts( $args );
					foreach($ex_posts as $item){
						wp_trash_post($item->ID);
					}
					wp_redirect( admin_url('edit.php?post_type=product') );
					exit;
				}
			}
			return;
		}
	}
}
//bubble
add_action( 'admin_menu', 'webd_pending_posts_bubble', 999 );
function webd_pending_posts_bubble() 
{
    global $menu;

    // Get all post types and remove Attachments from the list
    // Add '_builtin' => false to exclude Posts and Pages
    $args = array( 'public' => true ); 
    $post_types = get_post_types( $args );

    foreach( $post_types as $pt ){
		if( $pt == 'product'){
			// Count posts
			$cpt_count = wp_count_posts( $pt );
	
			if ( $cpt_count->pending ) 
			{
				// Menu link suffix, Post is different from the rest
				$suffix = ( 'post' == $pt ) ? '' : "?post_type=$pt";
	
				// Locate the key of 
				$key = webd_recursive_array_search_php( "edit.php$suffix", $menu );
	
				// Not found, just in case 
				if( !$key )
					return;
	
				// Modify menu item
				$menu[$key][0] .= sprintf(
					'<span class="update-plugins count-%1$s" style="background-color:white;color:red; margin-left:5px;"><span class="plugin-count">%1$s</span></span>',
					$cpt_count->pending 
				);
			}
		}
    }
}
function webd_recursive_array_search_php( $needle, $haystack ) 
{
    foreach( $haystack as $key => $value ) 
    {
        $current_key = $key;
        if( 
            $needle === $value 
            OR ( 
                is_array( $value )
                && webd_recursive_array_search_php( $needle, $value ) !== false 
            )
        ) 
        {
            return $current_key;
        }
    }
    return false;
}
// edit column admin 
add_filter( 'manage_product_posts_columns', 'webd_edit_columns',99 );
function webd_edit_columns( $columns ) {
	global $wpdb;
	unset($columns['date']);
	unset($columns['sku']);
	unset($columns['product_type']);
	$columns['webd_startdate'] = esc_html__( 'Start Date' , 'WEBDWooEVENT' );
	$columns['webd_enddate'] = esc_html__( 'End Date' , 'WEBDWooEVENT' );
			
	return $columns;
}
add_action( 'manage_product_posts_custom_column', 'webd_custom_columns',12);
function webd_custom_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'webd_startdate':
			$webd_startdate = get_post_meta($post->ID, 'webd_startdate', true);
			if($webd_startdate!=''){
				echo date_i18n( get_option('date_format'), $webd_startdate).' '.date_i18n(get_option('time_format'), $webd_startdate);
			}
			break;
		case 'webd_enddate':
			$webd_enddate = get_post_meta($post->ID, 'webd_enddate', true);
			if($webd_enddate!=''){
				echo date_i18n( get_option('date_format'), $webd_enddate).' '.date_i18n(get_option('time_format'), $webd_enddate);
			}
			break;		
	}
}
// sortable by date
if(!function_exists('webd_product_sortable_columns')){
		
	add_filter( 'manage_edit-product_sortable_columns', 'webd_product_sortable_columns',99 );
	
	function webd_product_sortable_columns( $columns ) {
		$custom = array(
			'price'    => 'price',
			'sku'      => 'sku',
			'name'     => 'title',
			'webd_startdate'     => 'webd_startdate',
			'webd_enddate'     => 'webd_enddate',
		);
		return wp_parse_args( $custom, $columns );
	}
	
}

if(!function_exists('webd_sortable_query_hook')){
	
	function webd_sortable_query_hook( $query ) {
		if ( is_admin() && $query->is_main_query() && is_post_type_archive( 'product' )) {
			if(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby']) =='webd_startdate'){
				$order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
				$query->set('orderby', 'meta_value_num');
				$query->set('order', $order);
				$query->set('meta_key', 'webd_startdate');
				return;
			}elseif(isset($_GET['orderby']) && sanitize_text_field($_GET['orderby']) =='webd_enddate'){
				$order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
				$query->set('orderby', 'meta_value_num');
				$query->set('order', $order);
				$query->set('meta_key', 'webd_enddate');
				return;
			}
		}
	}
	
	add_action( 'pre_get_posts', 'webd_sortable_query_hook' );
	
}
// default venue
add_action('wp_ajax_webd_add_venue', 'webd_add_venue_func' );
if(!function_exists('webd_add_venue_func')){
	function webd_add_venue_func(){
		$value = sanitize_text_field($_POST['value']);
		$webd_adress = $webd_phone = $webd_email = $webd_website ='';
		if(isset($value) && $value != '')
		{
			$webd_adress = get_post_meta( $value, 'webd_adress', true ) ;
			$webd_latitude_longitude = get_post_meta( $value, 'webd_latitude_longitude', true ) ;
			$webd_phone = get_post_meta( $value, 'webd_phone', true ) ;
			$webd_email = get_post_meta( $value, 'webd_email', true ) ;
			$webd_website = get_post_meta( $value, 'webd_website', true ) ;
		}
		$output =  array('webd_adress'=>$webd_adress,'webd_latitude_longitude'=> $webd_latitude_longitude,'webd_phone'=> $webd_phone,'webd_email'=> $webd_email,'webd_website'=> $webd_website);
		echo str_replace('\/', '/', json_encode($output));
		die;
	}
}
if(!function_exists('webd_add_new_venue')){
	add_action( 'save_post', 'webd_add_new_venue' );
	function webd_add_new_venue(){
		$value = isset($_POST['webd_default_venue']) ? sanitize_text_field($_POST['webd_default_venue']) :'';
		if(isset ($value['exc_mb-field-0']) && $value['exc_mb-field-0'] == '')
		{
			$venue_check = get_page_by_title($_POST['webd_adress']['exc_mb-field-0'],'OBJECT','webd_venue');
			if($venue_check->ID){
				return;
			}
			$attr = array(
				'post_title'    => sanitize_text_field( $_POST['webd_adress']['exc_mb-field-0'] ),
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'      => 'webd_venue',
			);
			remove_action( 'save_post', 'webd_add_new_venue');
			if($new_ID = wp_insert_post( $attr, false )){
				// update meta
				update_post_meta( $new_ID, 'webd_adress', sanitize_text_field($_POST['webd_adress']['exc_mb-field-0']));
				update_post_meta( $new_ID, 'webd_latitude_longitude', sanitize_text_field($_POST['webd_latitude_longitude']['exc_mb-field-0']));
				update_post_meta( $new_ID, 'webd_phone', sanitize_text_field($_POST['webd_phone']['exc_mb-field-0']));
				update_post_meta( $new_ID, 'webd_email', sanitize_text_field($_POST['webd_email']['exc_mb-field-0']));
				update_post_meta( $new_ID, 'webd_website', sanitize_text_field($_POST['webd_website']['exc_mb-field-0']));
				$_POST['webd_default_venue']['exc_mb-field-0'] = $new_ID;
			}
			add_action( 'save_post', 'webd_add_new_venue' );
		}
	}
}

if(!function_exists('webd_in_array_r')){
	function webd_in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && webd_in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
	
		return false;
	}
}

add_action( 'wp_ajax_webd_remove_event_from_recurr', 'webd_remove_event_from_recurr' );
add_action( 'wp_ajax_nopriv_webd_remove_event_from_recurr', 'webd_remove_event_from_recurr' );
function webd_remove_event_from_recurr(){
	$value 	= sanitize_text_field($_POST['date']);
	$id 	= sanitize_text_field($_POST['id']);
	$re_list = get_post_meta($id,'recurren_list', true );
	if(!empty($re_list)){
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'post__in' =>  $re_list,
		);
		$args ['meta_query'] = array(
			'relation' => 'AND',
			 array(
				'key'     => 'webd_startdate',
				'value'   => $value['webd_ct_stdate'],
				'compare' => '=',
			 ),
			 array(
				'key'     => 'webd_enddate',
				'value'   => $value['webd_ct_edate_end'],
				'compare' => '=',
			),
		);
		$this_posts = get_posts( $args );
		foreach ( $this_posts as $post ) {
			wp_delete_post($post->ID);
			unset($re_list[array_search($post->ID, $re_list)]);
			update_post_meta( $id, 'recurren_list', $re_list);
			echo 1;
			break;
		}
	}else{
		echo 0;
	}
	die;
}
if(!function_exists('webd_custom_fix_cf_css')){
	function webd_custom_fix_cf_css() {
		echo '<style>.edit_form_line input.cat.textfield[name=cat]{display: inline-block !important;}</style>';
	}
	add_action( 'admin_head', 'webd_custom_admin_css' );
}
function wooevent_wpml_duplicate_product( $new_id, $product_id, $stdate, $enddate ){
	$duplicated_products = array();
	//duplicate original first
	if(!class_exists('WCML_WC_Admin_Duplicate_Product')){ return;}
	global $sitepress, $woocommerce_wpml,$wpdb;
	$wpml_dupli = new WCML_WC_Admin_Duplicate_Product($woocommerce_wpml,$sitepress,$wpdb);
	$trid = $sitepress->get_element_trid( $product_id, 'post_product' );
	$orig_id = $sitepress->get_original_element_id_by_trid( $trid );
	$orig_lang = $woocommerce_wpml->products->get_original_product_language($product_id );
//echo $trid;echo $sitepress->get_element_trid( $new_id, 'post_product' );exit;
	$new_trid = $sitepress->get_element_trid( $new_id, 'post_product' );
    $new_orig_id = $new_id;
	$translations = $sitepress->get_element_translations( $trid, 'post_product' );
	
	/*$tr_newid = $sitepress->get_element_trid($new_id);
	$translation_nid = $sitepress->get_element_translations($tr_newid);
	foreach ($translation_nid as $key => $data) {
		//echo '<pre>';print_r($translation_nid);exit;
	}*/
	
	$duplicated_products[ 'translations' ] = array();
	if( $translations ){
//echo '<pre>';print_r($translations);//exit;
		foreach( $translations as $translation ){print_r(($translation));
			$exts = 0;
			$tr_newid = $sitepress->get_element_trid($new_id);
			$translation_nid = $sitepress->get_element_translations($tr_newid);
			//echo '<pre>';print_r($translation_nid);exit;

			if( !$translation->original && $translation->element_id != $product_id ){
				foreach( $translation_nid as $translation_n ){
					if($translation->language_code == $translation_n->language_code){
						$wpdb->update(
							$wpdb->posts,
							array(
								'post_title'     => get_the_title($translation->element_id),
								'post_content'   => get_the_content($translation->element_id),
							),
							array( 'ID' => $translation_n->element_id )
						);
						$exts = 1;
						break;
					}
				}
				if($exts != 1){
					$post_to_duplicate = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID=%d", $translation->element_id ) );
					//echo '<pre>';print_r($post_to_duplicate);exit;
					if( ! empty( $post_to_duplicate ) ) {
						$new_id_dl = $wpml_dupli->wc_duplicate_product( $post_to_duplicate );
						$new_id_obj = get_post( $new_id );
						$new_slug = wp_unique_post_slug( sanitize_title( $post_to_duplicate->post_title.$new_id_dl ), $new_id_dl, $post_to_duplicate->post_status, $post_to_duplicate->post_type, $new_id_obj->post_parent );
	
						$wpdb->update(
							$wpdb->posts,
							array(
								'post_name'     => $new_slug,
								'post_title'     => $post_to_duplicate->post_title,
								'post_status'   => 'publish'
							),
							array( 'ID' => $new_id_dl )
						);
						update_post_meta( $new_id_dl, 'webd_startdate', $stdate);
						update_post_meta( $new_id_dl, 'webd_enddate', $enddate);
	
						//do_action( 'wcml_after_duplicate_product' , $new_id, $post_to_duplicate );
						$sitepress->set_element_language_details( $new_id_dl, 'post_product', $new_trid, $translation->language_code );
						if( get_post_meta( $translation->element_id, '_icl_lang_duplicate_of' ) ){
							update_post_meta( $new_id_dl, '_icl_lang_duplicate_of', $new_orig_id );
						}
						$duplicated_products[ 'translations' ][] = $new_id_dl;
					}
				}
			}else{
				
				update_post_meta( $translation->element_id, 'webd_startdate', get_post_meta($product_id,'webd_startdate', true ));
				update_post_meta( $translation->element_id, 'webd_enddate', get_post_meta($product_id,'webd_enddate', true ));
			}
		}
	}
	$duplicated_products[ 'original' ] = $new_orig_id;

	return $duplicated_products;
}
/***** add filter order event to admin list *****/
if(!function_exists('webd_add_fitler_admin_events')){
	function webd_add_fitler_admin_events( $post_type, $which ) {
		if ( $post_type == 'product' ) {	
			// Display filter HTML
			echo "<select name='orderby' id='orderby' class='postform'>";
			echo '<option value="">' . esc_html__( 'Order by', 'WEBDWooEVENT' ) . '</option>';
			echo '<option value="upcoming" '.(( isset( $_GET['orderby'] ) && ( sanitize_text_field($_GET['orderby']) == 'upcoming' ) ) ? ' selected="selected"' : '' ).'>'.esc_html__( 'Upcoming Events', 'WEBDWooEVENT' ).'</option>';
			echo '<option value="past" '.(( isset( $_GET['orderby'] ) && ( sanitize_text_field($_GET['orderby']) == 'past' ) ) ? ' selected="selected"' : '' ).'>'.esc_html__( 'Past Events', 'WEBDWooEVENT' ).'</option>';
			echo '</select>';
			if(get_option('webd_delete_passevent')=='yes'){
				echo '<a class="clear-ev" style=" float: right;" href="'.add_query_arg( 'weclear_evpassed', '1', admin_url('edit.php?post_type=product') ).'" data-confirm="Are you sure you want to load this URL?">Clear all Events has passed</a>';
			}
		}
	
	}
	if( isset($_GET['post_type']) && (sanitize_text_field($_GET['post_type'])=='product') ){
		add_action( 'restrict_manage_posts', 'webd_add_fitler_admin_events' , 10, 2);
	}
}
add_action( 'pre_get_posts','webd_admin_filter_events_query_hook',101 );
if (!function_exists('webd_admin_filter_events_query_hook')) {
	function webd_admin_filter_events_query_hook($query) {
		if ( is_post_type_archive('product') && is_admin()) {
			if( isset($_GET['orderby']) && (sanitize_text_field($_GET['orderby'])=='upcoming' || sanitize_text_field($_GET['orderby'])=='past' ) ){
				$cure_time =  strtotime("now");
				$gmt_offset = get_option('gmt_offset');
				if($gmt_offset!=''){
					$cure_time = $cure_time + ($gmt_offset*3600);
				}
				$query->set('meta_key', 'webd_startdate');
				$query->set('orderby', 'meta_value_num');
				$query->set('meta_value', $cure_time);
				if(sanitize_text_field($_GET['orderby'])=='upcoming'){
					$query->set('meta_compare', '>');
					$query->set('order', 'ASC');
					
				}
				if(sanitize_text_field($_GET['orderby'])=='past'){
					$query->set('meta_compare', '<');
					$query->set('order', 'DESC');
				}
			}
		}
		return $query;
	}
}
/*--- clear event has passed ---*/
add_action( 'init', 'webd_clear_event_has_passed' );
if(!function_exists('webd_clear_event_has_passed')){
	function webd_clear_event_has_passed() {
		 if( isset( $_GET['weclear_evpassed'] ) && sanitize_text_field($_GET['weclear_evpassed']) == 1) {
			 if(get_option('webd_delete_passevent')!='yes'){
				 echo esc_html__("Error !! You don't have permission to access this url","WEBDWooEVENT");exit;
			 }
			 if ( is_user_logged_in() && current_user_can( 'manage_options' )){
				$cure_time =  strtotime(' -1 day');
				$gmt_offset = get_option('gmt_offset');
				if($gmt_offset!=''){
					$cure_time = $cure_time + ($gmt_offset*3600);
				}
				$args = array('post_type' => 'product', 'numberposts' => -1 );
				$args['meta_key']= 'webd_enddate';
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'  => 'webd_enddate',
						'value' => $cure_time,
						'compare' => '<'
					)
				);
				$my_posts = get_posts( $args );
				if(!empty($my_posts)){
					foreach ( $my_posts as $post ):
						wp_delete_post($post->ID);
					endforeach;
				}
				wp_redirect( admin_url('edit.php?post_type=product') );exit;
				
			 }else{
				 echo esc_html__("Error !! You don't have permission to access this url","WEBDWooEVENT");exit;
			 }
		 }
	}
}
if(!function_exists('save_tmot_meta')){
function save_tmot_meta( $post_id, $_data ) {
	if(!function_exists('tc_get_post_meta')){
		return;
	}
	global $woocommerce, $wpdb;

	$attributes = (array) maybe_unserialize( tc_get_post_meta( $post_id, '_product_attributes', true ) );
	
	if ( isset($_data['product-type']) || isset( $_data['variable_sku'] ) || isset( $_data['_sku'] ) ) {
		$_post_id                   = isset( $_data['tmcp_post_id'] ) ? $_data['tmcp_post_id'] : array();
		$tmcp_regular_price         = isset( $_data['tmcp_regular_price'] ) ? $_data['tmcp_regular_price'] : array();
		$tmcp_regular_price_type    = isset( $_data['tmcp_regular_price_type'] ) ? $_data['tmcp_regular_price_type'] : array();
		$tmcp_enabled               = isset( $_data['tmcp_enabled'] ) ? $_data['tmcp_enabled'] : array();
		$tmcp_required              = isset( $_data['tmcp_required'] ) ? $_data['tmcp_required'] : array();
		$tmcp_hide_price            = isset( $_data['tmcp_hide_price'] ) ? $_data['tmcp_hide_price'] : array();
		$tmcp_limit                 = isset( $_data['tmcp_limit'] ) ? $_data['tmcp_limit'] : array();
		$tmcp_menu_order            = isset( $_data['tmcp_menu_order'] ) ? $_data['tmcp_menu_order'] : array();
		$tmcp_attribute             = isset( $_data['tmcp_attribute'] ) ? $_data['tmcp_attribute'] : array();
		$tmcp_type                  = isset( $_data['tmcp_type'] ) ? $_data['tmcp_type'] : array();
		$tm_meta_cpf                = isset( $_data['tm_meta_cpf'] ) ? $_data['tm_meta_cpf'] : array();

		// update custom product settings
		tc_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );    
		
		if ( isset($_data['tm_meta_serialized'])){
			$tm_metas = $_data['tm_meta_serialized'];
			$tm_metas = stripslashes_deep($tm_metas);
			$tm_metas = rawurldecode($tm_metas);
			$tm_metas = nl2br($tm_metas);
			$tm_metas = json_decode($tm_metas, true);

			if($tm_metas || (is_array($tm_metas))){
				if (!isset($_SESSION)){
					session_start();
				}
				$import=false;
				if (isset($_SESSION['import_csv'])){
					$import = sanitize_text_field($_SESSION['import_csv']);
				}
				if (!empty($import)){     
					if (!empty($_SESSION['import_override'])){
						unset($tm_metas['tm_meta']['tmfbuilder']);
						$tm_metas=TM_EPO_ADMIN_GLOBAL()->import_array_merge($tm_metas,$import);
						unset($_SESSION['import_override']);
					}else{
						$tm_metas=TM_EPO_ADMIN_GLOBAL()->import_array_merge($tm_metas,$import);    
					}                        
					unset($_SESSION['import_csv']);
				}
				
				$old_data = tc_get_post_meta($post_id, 'tm_meta',true);

				if ( !empty($tm_metas) && is_array($tm_metas) && isset($tm_metas['tm_meta']) && is_array($tm_metas['tm_meta'])){
					$tm_meta=$tm_metas['tm_meta'];
					TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta');
				}else{
					TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, false, $old_data, 'tm_meta');
				}
			}
		}elseif ( isset($_data['tm_meta_serialized_wpml'])){
			$tm_metas = $_data['tm_meta_serialized_wpml'];
			$tm_metas = stripslashes_deep($tm_metas);
			$tm_metas = rawurldecode($tm_metas);
			$tm_metas = nl2br($tm_metas);
			$tm_metas = json_decode($tm_metas, true);
			if($tm_metas){
				
				$old_data = tc_get_post_meta($post_id, 'tm_meta_wpml',true);

				if ( !empty($tm_metas) && is_array($tm_metas) && isset($tm_metas['tm_meta']) && is_array($tm_metas['tm_meta'])){
					$tm_meta=$tm_metas['tm_meta'];
					TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta_wpml');
				}else{
					TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, false, $old_data, 'tm_meta_wpml');
				}
			}                
		}

		if (!empty($_post_id )){
			global $wpdb;
			$max_loop = max( array_keys( $_post_id ) );
			for ( $i = 0; $i <= $max_loop; $i ++ ) {

				if ( ! isset( $_post_id[ $i ] ) ){
					continue;
				}

				$tmcp_id = absint( $_post_id[ $i ] );

				// This will always be update post
				if ( $tmcp_id ) {
					// Enabled or disabled
					$post_status = isset( $tmcp_enabled[ $i ] ) ? 'publish' : 'private';

					// Generate a useful post title
					$post_title = sprintf( __( 'TM Extra Product Option #%s of %s', 'woocommerce-tm-extra-product-options' ), absint( $tmcp_id ), esc_html( get_the_title( $post_id ) ) );

					/*wp_update_post( wp_slash( array(
						'ID'            => $tmcp_id,
						'post_status'   => $post_status,
						'post_title'    => $post_title,
						'menu_order'    => $tmcp_menu_order[ $i ]
						)));*/
					$data = wp_slash( array(
						'post_status'   => $post_status,
						'post_title'    => $post_title,
						'menu_order'    => $tmcp_menu_order[ $i ]
						));
					$data = wp_unslash( $data );
					$where = array( 'ID' => $tmcp_id );
					if ( false === $wpdb->update( $wpdb->posts, $data, $where ) ) {
						if ( $wp_error ) {
							return new WP_Error('db_update_error', __('Could not update post in the database'), $wpdb->last_error);
						} else {
							return 0;
						}
					}
					// Update post meta

					// Price handling
					$clean_prices = array();
					$clean_prices_type = array();
					if ( isset( $tmcp_regular_price[ $i ] ) ) {
						foreach ( $tmcp_regular_price[ $i ] as $key=>$value ) {
							foreach ( $value as $k=>$v ) {
								if ( $v !== '' ) {
									$clean_prices[$key][$k] = wc_format_decimal( $v );
								}
							}
						}
					}
					if ( isset( $tmcp_regular_price_type[ $i ] ) ) {
						foreach ( $tmcp_regular_price_type[ $i ] as $key=>$value ) {
							foreach ( $value as $k=>$v ) {
								$clean_prices_type[$key][$k] = $v;
							}
						}
					}
					
					$regular_price = $clean_prices ;
					$regular_price_type = $clean_prices_type;
					update_post_meta( $tmcp_id, '_regular_price', $regular_price );
					update_post_meta( $tmcp_id, '_regular_price_type', $regular_price_type );

					$post_required      = isset( $tmcp_required[ $i ] ) ? 1 : '';
					$post_hide_price    = isset( $tmcp_hide_price[ $i ] ) ? 1 : '';
					$post_limit         = isset( $tmcp_limit[ $i ] ) ?  $tmcp_limit[ $i ] : '';
					update_post_meta( $tmcp_id, 'tmcp_required', $post_required );
					update_post_meta( $tmcp_id, 'tmcp_hide_price', $post_hide_price );
					update_post_meta( $tmcp_id, 'tmcp_limit', $post_limit );
					update_post_meta( $tmcp_id, 'tmcp_attribute', $tmcp_attribute[ $i ] );
					update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[$tmcp_attribute[ $i ]]['is_taxonomy'] );
					update_post_meta( $tmcp_id, 'tmcp_type', $tmcp_type[ $i ] );

				}
			}
		}
	}
}
}




add_action('init','webd_fix_recurring_old_version');
if(!function_exists('webd_fix_recurring_old_version')){
	function webd_fix_recurring_old_version() {
		if(is_admin() && isset($_GET['action']) && sanitize_text_field($_GET['action']) =='fix-recurring'){
			if ( current_user_can( 'manage_options' ) ) {
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
							'compare' => 'EXISTS',
						),
					),
				);
				$ex_posts = get_posts( $args );
				foreach($ex_posts as $item){
					$ex_recurr = get_post_meta($item->ID,'recurren_ext', true );
					$ex_recurr  = explode("_",$ex_recurr);
					if(isset($ex_recurr[1]) && $ex_recurr[1]!= $item->ID){
						delete_post_meta( $item->ID, 'webd_recurrence_end');
						delete_post_meta( $item->ID, 'webd_recurrence');
						delete_post_meta( $item->ID, 'webd_ctdate');
						delete_post_meta( $item->ID, 'webd_frequency');
						delete_post_meta( $item->ID, 'webd_weekday');
						delete_post_meta( $item->ID, 'webd_monthday');
						delete_post_meta( $item->ID, 'webd_mweekday');
					}
				}
				wp_redirect( admin_url('edit.php?post_type=product') );
				exit;
			}
			return;
		}
	}
}