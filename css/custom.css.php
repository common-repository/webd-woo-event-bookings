<?php
$webd_main_color = get_option('webd_main_color');
$hex  = $webd_main_color = str_replace("#", "", $webd_main_color);

if(strlen($hex) == 3) {
  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
} else {
  $r = hexdec(substr($hex,0,2));
  $g = hexdec(substr($hex,2,2));
  $b = hexdec(substr($hex,4,2));
}
$rgb = $r.','. $g.','.$b;
$webd_shop_view = get_option('webd_shop_view');
if(($webd_shop_view!='list' && !isset($_GET['view']) && !is_search()) || ($webd_shop_view!='list' && sanitize_text_field($_GET['view'])!='list' && !is_search()) || (isset($_GET['view']) && sanitize_text_field($_GET['view'])!='list')){?>
    .webd-calendar-view ul.products:not(.webd-search-ajax){ display:none;}
    <?php
}
$webd_fontfamily = get_option('webd_fontfamily');
$main_font_family = explode(":", $webd_fontfamily);
$main_font_family = $main_font_family[0];
$webd_fontsize = get_option('webd_fontsize');
$webd_hfont = get_option('webd_hfont');
$h_font_family = explode(":", $webd_hfont);
$h_font_family = $h_font_family[0];
$webd_hfontsize = get_option('webd_hfontsize');
$webd_metafont = get_option('webd_metafont');
$meta_font_family = explode(":", $webd_metafont);
$meta_font_family = $meta_font_family[0];
$webd_matafontsize = get_option('webd_matafontsize');

$webd_main_purpose = get_option('webd_main_purpose');
if($webd_main_purpose=='meta'){
	if($webd_main_color!=''){?>
		.webd-latest-events-widget .thumb.item-thumbnail .item-evprice,
		.widget.webd-latest-events-widget .thumb.item-thumbnail .item-evprice,
		.webd-table-lisst.table-style-2 .webd-table .webd-first-row,
		.webd-calendar #calendar a.fc-event,
		.wpcf7 .webd-submit input[type="submit"], .webd-infotable .bt-buy.btn,
		.shop-webd-stdate,
		.btn.webd-button, .webd-icl-import .btn,
		.ex-loadmore .loadmore-grid,
		.webd-countdonw.list-countdown .cd-number,
		.webd-grid-shortcode figure.ex-modern-blog .date,
		.webd-grid-shortcode.webd-grid-column-1 figure.ex-modern-blog .ex-social-share ul li a,
		.webd-latest-events-widget .item .webd-big-date > div,
		.widget.webd-latest-events-widget .item .webd-big-date > div,
		.webd-timeline-shortcode ul li .timeline-content .tl-tdate,
		.webd-timeline-shortcode ul li:after,
		.webd-timeline-shortcode ul li .tl-point,
		.webd-timeline-shortcode ul li .timeline-content,
		.webd-calendar .wpex-spinner > div,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.widget-style .fc-day-top.hasevent .fc-day-number:after,
		.wt-eventday .day-event-details > div.day-ev-image .item-evprice,
		.webd-table-lisst .webd-table th,
		.webd-calendar .fc-toolbar button.fc-state-active, .webd-calendar .fc-toolbar button:hover,
		.webd-grid-shortcode figure.ex-modern-blog .ex-social-share{ background:#<?php echo esc_html($webd_main_color);?>}
		.webd-timeline-shortcode ul li .timeline-content:before{ border-right-color:#<?php echo esc_html($webd_main_color);?>}
		@media screen and (min-width: 768px) {
			.webd-timeline-shortcode ul li:nth-child(odd) .timeline-content:before{ border-left-color:#<?php echo esc_html($webd_main_color);?>}
		}
		.webd-venues-sc.venue-style-2 .vn-info span.vn-title,
		.webd-venues-sc.venue-style-3 .vn-info span.vn-title,
		.qtip h4,
		.webd-tooltip .webd-tooltip-content p.tt-price ins, .webd-tooltip .webd-tooltip-content p.tt-price :not(i),
		.webd-table-lisst .webd-table td.tb-price, .webd-table-lisst .webd-table td span.amount{ color:#<?php echo esc_html($webd_main_color);?>}
		.webd-calendar #calendar a.fc-event,
		.webd-table-lisst .webd-table{ border-color:#<?php echo esc_html($webd_main_color);?>}
		.webd-table-lisst.table-style-2 .webd-table .tb-meta-cat,
		.webd-table-lisst.table-style-2.table-style-3 .webd-table td.tb-viewdetails .btn.webd-button,
		.webd-table-lisst.table-style-2.table-style-3 .webd-table .webd-first-row{border-left-color:#<?php echo esc_html($webd_main_color);?>}
	<?php
	}
	if($webd_fontfamily!=''){?>
		.wpcf7 .webd-submit, .ui-timepicker-wrapper,
        .webd-calendar, .webd-grid-shortcode ,
		.webd-search-form input.form-control::-webkit-input-placeholder,
		.webd-search-form input.form-control,
		.webd-search-form input.form-control:-ms-input-placeholder,
		.webd-search-form input.form-control:-moz-placeholder,
		.webd-table-lisst .webd-table,
		.webd-content-webd_view,
		.webd-tooltip,
		.webd-countdonw{
			font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
		}
	<?php }
	if($webd_fontsize!=''){?>
		.webd-calendar, .webd-grid-shortcode,
		.woo-event-toolbar .webd-showdrd,
		.webd-social-share ul li,
		.webd-table-lisst .webd-table ,
		.webd-table-lisst .webd-table td h3,
		.wpcf7 .webd-submit,
		.wpcf7 .webd-submit input[type="text"],
		.wpcf7 .webd-submit textarea,
		.wpcf7 .webd-submit input[type="date"],
		.wpcf7 .webd-submit input[type="number"],
		.wpcf7 .webd-submit input[type="email"],
		.webd-table-lisst .webd-table td, .webd-table-lisst .webd-table th,
		.wooevent-search .btn.webd-product-search-dropdown-button,
		.webd-content-webd_view,
		.webd-grid-shortcode figure.ex-modern-blog .grid-excerpt,
		.btn.webd-button, .webd-icl-import .btn,
		.ex-loadmore .loadmore-grid,
		.webd-table-lisst .webd-table,
		.woo-event-toolbar .webd-search-form .webd-search-dropdown button,
		.webd-countdonw.list-countdown .cd-title a{
			font-size: <?php echo esc_html($webd_fontsize) ?>;
		}
		.webd-search-form input.form-control::-webkit-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control:-ms-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control:-moz-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
	
	<?php }
	if($h_font_family!=''){?>
		.qtip h4,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.webd-table-lisst .webd-table td h3 a,
		.webd-grid-shortcode figure.ex-modern-blog h3 a,
		.webd-infotable .wemap-details h4.wemap-title a,
		.webd-content-webd_view h3, figure.ex-modern-blog h3{
			font-family: "<?php echo esc_html($h_font_family);?>", sans-serif;
		}
	<?php }
	
	
	if($webd_hfontsize!=''){?>
		.woocommerce div.product .woocommerce-tabs ul.tabs li a,
        .webd-calendar h2,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.webd-content-webd_view h3, figure.ex-modern-blog h3, .woocommerce #exmain-content h3{
			font-size: <?php echo esc_html($webd_hfontsize); ?>;
		}
	<?php }
	if($meta_font_family!=''){?>
		.shop-webd-more-meta span,
		.webd-latest-events-widget .event-details span,
		.widget.webd-latest-events-widget .event-details span,
		.webd-grid-shortcode figure.ex-modern-blog .webd-more-meta span,
		.webd-woo-event-info span.sub-lb{
			font-family: "<?php echo esc_html($meta_font_family);?>", sans-serif;
		}
	<?php }
	if($webd_matafontsize!=''){?>
		.shop-webd-more-meta span,
		.webd-latest-events-widget .event-details span,
		.widget.webd-latest-events-widget .event-details span,
		.webd-grid-shortcode figure.ex-modern-blog .webd-more-meta span,
		.webd-woo-event-info span.sub-lb{
			font-size: <?php echo esc_html($webd_matafontsize); ?>;
		}
	<?php }?>
	<?php

}else{
	if($webd_main_color!=''){?>
		.webd-latest-events-widget .thumb.item-thumbnail .item-evprice,
		.widget.webd-latest-events-widget .thumb.item-thumbnail .item-evprice,
		.woocommerce table.my_account_orders th, .woocommerce table.shop_table th, .webd-table-lisst .webd-table th,
		.webd-table-lisst.table-style-2 .webd-table .webd-first-row,
		.webd-calendar #calendar a.fc-event,
		.wpcf7 .webd-submit input[type="submit"], .webd-infotable .bt-buy.btn,
		.woocommerce ul.products li.product a.button,
		.shop-webd-stdate,
		.btn.webd-button, .woocommerce div.product form.cart button.button, .woocommerce div.product form.cart div.quantity.buttons_added [type="button"], .woocommerce #exmain-content .webd-main.layout-2 .event-details .btn, .webd-icl-import .btn,
		.ex-loadmore .loadmore-grid,
		.webd-countdonw.list-countdown .cd-number,
		.webd-grid-shortcode figure.ex-modern-blog .date,
		.webd-grid-shortcode.webd-grid-column-1 figure.ex-modern-blog .ex-social-share ul li a,
		.webd-grid-shortcode figure.ex-modern-blog .ex-social-share,
		.woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,
		.webd-latest-events-widget .item .webd-big-date > div,
		.widget.webd-latest-events-widget .item .webd-big-date > div,
		.webd-timeline-shortcode ul li .timeline-content .tl-tdate,
		.webd-timeline-shortcode ul li:after,
		.webd-timeline-shortcode ul li .tl-point,
		.webd-timeline-shortcode ul li .timeline-content,
		.webd-calendar .wpex-spinner > div,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.widget-style .fc-day-top.hasevent .fc-day-number:after,
		.wt-eventday .day-event-details > div.day-ev-image .item-evprice,
		.webd-calendar .fc-toolbar button.fc-state-active, .webd-calendar .fc-toolbar button:hover,
		.woocommerce #exmain-content .webd-navigation div a{ background:#<?php echo esc_html($webd_main_color);?>}
		.woocommerce #exmain-content h4.wemap-title a, .webd-infotable .wemap-details h4.wemap-title a,
		.woocommerce #exmain-content .webd-woo-event-info a,
		.qtip h4,
		.webd-venues-sc.venue-style-2 .vn-info span.vn-title,
		.webd-venues-sc.venue-style-3 .vn-info span.vn-title,
		.webd-tooltip .webd-tooltip-content p.tt-price ins, .webd-tooltip .webd-tooltip-content p.tt-price :not(i),
		.webd-table-lisst .webd-table td.tb-price, .webd-table-lisst .webd-table td span.amount{ color:#<?php echo esc_html($webd_main_color);?>}
        .woocommerce div.product .woocommerce-tabs ul.tabs li.active,
		.woocommerce-page .woocommerce .myaccount_address, .woocommerce-page .woocommerce .address address, .woocommerce-page .woocommerce .myaccount_user,
		.webd-calendar #calendar a.fc-event,
		.woocommerce form.checkout_coupon, .woocommerce form.login, .woocommerce form.register, .woocommerce table.shop_table, .woocommerce table.my_account_orders, .webd-table-lisst .webd-table{ border-color:#<?php echo esc_html($webd_main_color);?>}
		.webd-timeline-shortcode ul li .timeline-content:before{ border-right-color:#<?php echo esc_html($webd_main_color);?>}
		@media screen and (min-width: 768px) {
			.webd-timeline-shortcode ul li:nth-child(odd) .timeline-content:before{ border-left-color:#<?php echo esc_html($webd_main_color);?>}
		}
		@media screen and (max-width: 600px) {
			.woocommerce table.shop_table th.product-remove, .woocommerce table.shop_table td.product-remove,
			.woocommerce table.shop_table_responsive tr:nth-child(2n) td.product-remove,
			.woocommerce-page table.shop_table tr.cart-subtotal:nth-child(2n-1){background: #<?php echo esc_html($webd_main_color);?>}
		}
		.webd-table-lisst.table-style-2 .webd-table .tb-meta-cat,
		.webd-table-lisst.table-style-2.table-style-3 .webd-table td.tb-viewdetails .btn.webd-button,
		.webd-table-lisst.table-style-2.table-style-3 .webd-table .webd-first-row{border-left-color:#<?php echo esc_html($webd_main_color);?>}
	<?php
	}
	if($webd_fontfamily!=''){?>
		.woocommerce-page form .form-row .input-text::-webkit-input-placeholder,
		.webd-search-form input.form-control::-webkit-input-placeholder,
		.woocommerce-page form .form-row .input-text::-moz-placeholder,
		.woocommerce-page form .form-row .input-text:-ms-input-placeholder,
		.webd-search-form input.form-control:-ms-input-placeholder,
		.woocommerce-page form .form-row .input-text:-moz-placeholder,
		.webd-search-form input.form-control:-moz-placeholder{
			font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
		}
        .wpcf7 .webd-submit, .ui-timepicker-wrapper,
		.webd-tooltip,
		.woocommerce-cart .woocommerce,
		.woocommerce-account .woocommerce,
		.woocommerce-checkout .woocommerce,
		.webd-timeline-shortcode ul li,
		.webd-search-form input.form-control,
		.webd-table-lisst .webd-table,
		.woocommerce #exmain-content .webd-sidebar input,
		.woocommerce #exmain-content .webd-sidebar,
		.webd-content-webd_view,
		.woocommerce #exmain-content,
		.webd-calendar,
		.webd-grid-shortcode, .webd-search-form,
		.webd-search-shortcode,
		.webd-countdonw{
			font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;
		}
	<?php }
	if($webd_fontsize!=''){?>
		.webd-calendar,
		.webd-timeline-shortcode ul li,
		.woocommerce-page .woocommerce,
		.woocommerce #exmain-content,
		.woo-event-toolbar .webd-showdrd,
		.webd-social-share ul li,
		body.woocommerce-page #exmain-content .related ul.products li.product h3,
		.woocommerce #exmain-content div.product form.cart .variations td.label,
		.webd-table-lisst .webd-table ,
		.webd-table-lisst .webd-table td h3,
		.wpcf7 .webd-submit,
		.woocommerce form .form-row input.input-text, .woocommerce form .form-row textarea,
		.wpcf7 .webd-submit input[type="text"],
		.woocommerce-cart table.cart td.actions .coupon .input-text,
		.wpcf7 .webd-submit textarea,
		.wpcf7 .webd-submit input[type="date"],
		.wpcf7 .webd-submit input[type="number"],
		.woocommerce .select2-container .select2-choice,
		.wpcf7 .webd-submit input[type="email"],
		.woocommerce-page .woocommerce .myaccount_user,
		.woocommerce table.shop_table .quantity input,
		.woocommerce-cart table.cart td, .woocommerce-cart table.cart th, .woocommerce table.my_account_orders th, .woocommerce table.my_account_orders td, .webd-table-lisst .webd-table td, .webd-table-lisst .webd-table th,
		.wooevent-search .btn.webd-product-search-dropdown-button,
		.webd-content-webd_view,
		.webd-grid-shortcode figure.ex-modern-blog .grid-excerpt,
		.woocommerce #exmain-content .webd-navigation div a,
		.woo-event-toolbar .webd-viewas .webd-viewas-dropdown-button,
		.woocommerce #exmain-content a, .woocommerce #exmain-content,
		.btn.webd-button, .woocommerce div.product form.cart button.button, .woocommerce div.product form.cart div.quantity.buttons_added [type="button"], .woocommerce #exmain-content .webd-main.layout-2 .event-details .btn, .webd-icl-import .btn,
		.ex-loadmore .loadmore-grid,
		.woocommerce form.checkout_coupon, .woocommerce form.login, .woocommerce form.register, .woocommerce table.shop_table, 
		.woocommerce table.my_account_orders, .webd-table-lisst .webd-table,
		.woo-event-toolbar .webd-search-form .webd-search-dropdown button,
		.webd-countdonw.list-countdown .cd-title a{
			font-size: <?php echo esc_html($webd_fontsize) ?>;
		}
		.woocommerce-page form .form-row .input-text::-webkit-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control::-webkit-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.woocommerce-page form .form-row .input-text::-moz-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.woocommerce-page form .form-row .input-text:-ms-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control:-ms-input-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.woocommerce-page form .form-row .input-text:-moz-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
		.webd-search-form input.form-control:-moz-placeholder{ font-size: <?php echo esc_html($webd_fontsize) ?>;  }
	
	<?php }
	if($h_font_family!=''){?>
		.qtip h4,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.webd-timeline-shortcode ul li .timeline-content h3 a,
		.webd-table-lisst .webd-table td h3 a,
		.webd-grid-shortcode figure.ex-modern-blog h3 a,
		.woocommerce-page .woocommerce h4,
		.woocommerce #exmain-content h4.wemap-title a, .webd-infotable .wemap-details h4.wemap-title a,
		.woocommerce #exmain-content h1, .woocommerce-page .woocommerce h2, .woocommerce-page .woocommerce h3, 
		.woocommerce-page.woocommerce-edit-account .woocommerce fieldset legend, .woocommerce #exmain-content h2, body.woocommerce div.product .woocommerce-tabs .panel h2, .woocommerce div.product .product_title, .webd-content-webd_view h3, figure.ex-modern-blog h3, .woocommerce #exmain-content h3,
		.archive.woocommerce #exmain-content h2,
		.archive.woocommerce #exmain-content h3,
		.woocommerce #exmain-content .webd-sidebar h2,
		.woocommerce #exmain-content .webd-sidebar h3,
		.woocommerce #exmain-content .webd-content-custom h1,
		.woocommerce #exmain-content .product > *:not(.woocommerce-tabs) h1,
		.woocommerce-page .woocommerce .product > *:not(.woocommerce-tabs) h2,
		.woocommerce-page .woocommerce .product > *:not(.woocommerce-tabs) h3,
		.woocommerce-page.woocommerce-edit-account .woocommerce fieldset legend,
		.woocommerce #exmain-content .product > *:not(.woocommerce-tabs) h2,
		body.woocommerce div.product .woocommerce-tabs .panel h2:first-child,
		.woocommerce div.product .product_title,
		.webd-content-webd_view h3,
		figure.ex-modern-blog h3,
		.woocommerce #reviews #comments h2,
		.woocommerce #reviews h3,
		.woocommerce #exmain-content .product > *:not(.woocommerce-tabs) h3{
			font-family: "<?php echo esc_html($h_font_family);?>", sans-serif;
		}
	<?php }
	if($webd_hfontsize!=''){?>
		.webd-calendar h2,
		.webd-calendar .widget-style .fc-row:first-child table th,
		.webd-timeline-shortcode ul li .timeline-content h3,
		.woocommerce #exmain-content h1, .woocommerce-page .woocommerce h2, .woocommerce-page .woocommerce h3, .woocommerce-page.woocommerce-edit-account .woocommerce fieldset legend, .woocommerce #exmain-content h2, body.woocommerce div.product .woocommerce-tabs .panel h2, .woocommerce div.product .product_title, .webd-content-webd_view h3, figure.ex-modern-blog h3, .woocommerce #exmain-content h3{
			font-size: <?php echo esc_html($webd_hfontsize); ?>;
		}
	<?php }
	if($meta_font_family!=''){?>
		.shop-webd-more-meta span,
		.webd-latest-events-widget .event-details span,
		.widget.webd-latest-events-widget .event-details span,
		.webd-grid-shortcode figure.ex-modern-blog .webd-more-meta span,
		.webd-woo-event-info span.sub-lb{
			font-family: "<?php echo esc_html($meta_font_family);?>", sans-serif;
		}
	<?php }
	if($webd_matafontsize!=''){?>
		.shop-webd-more-meta span,
		.webd-latest-events-widget .event-details span,
		.widget.webd-latest-events-widget .event-details span,
		.webd-grid-shortcode figure.ex-modern-blog .webd-more-meta span,
		.webd-woo-event-info span.sub-lb{
			font-size: <?php echo esc_html($webd_matafontsize); ?>;
		}
	<?php }?>
	@media screen and (max-width: 600px) {
		.woocommerce-page table.shop_table td.product-name:before {
			content: "<?php _e( 'Product', 'woocommerce' ); ?>";
		}
		.woocommerce-page table.shop_table td.product-price:before {
			content: "<?php _e( 'Price', 'woocommerce' ); ?>";
		}
		.woocommerce-page table.shop_table td.product-quantity:before {
			content: "<?php _e( 'Quantity', 'woocommerce' ); ?>";
		}
		.woocommerce-page table.shop_table td.product-subtotal:before {
			content: "<?php _e( 'Subtotal', 'woocommerce' ); ?>";
		}
		.woocommerce-page table.shop_table td.product-total:before {
			content: "<?php _e( 'Total', 'woocommerce' ); ?>";
		}
	}
	<?php
}
if($webd_main_color!=''){?>
	.webd-ajax-pagination ul li .page-numbers.current{border-color:#<?php echo esc_html($webd_main_color);?>; background-color: #<?php echo esc_html($webd_main_color);?>;}
<?php }
$webd_custom_css = get_option('webd_custom_css');
if($webd_custom_css!=''){
	echo $webd_custom_css;
}