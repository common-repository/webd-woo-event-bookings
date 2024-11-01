<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}
if(function_exists('wc_get_related_products')){
	$related = wc_get_related_products( $product->get_id(), 3 );
}else{
	$related = $product->get_related( $posts_per_page );
}
if ( !$related  ) {
	return;
}

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->get_id() )
) );

$products                    = new WP_Query( $args );
$woocommerce_loop['name']    = 'related';
$woocommerce_loop['columns'] = apply_filters( 'woocommerce_related_products_columns', $columns );
$webd_main_purpose = get_option('webd_main_purpose');
$webd_layout_purpose = get_post_meta(get_the_ID(),'webd_layout_purpose',true);
if($webd_main_purpose=='custom' && $webd_layout_purpose!='event'){
	$webd_main_purpose = 'woo';
}

if ( $products->have_posts() ) : ?>

	<div class="related products">

		<h2><?php if($webd_main_purpose=='woo'){ _e( 'Related Products', 'woocommerce' );}else{ echo get_option('webd_text_related')!='' ? get_option('webd_text_related') : esc_html__( 'Related Events', 'WEBDWooEVENT' ); } ?></h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
