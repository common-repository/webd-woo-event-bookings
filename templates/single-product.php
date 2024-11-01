<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$webd_sidebar = get_post_meta(get_the_ID(),'webd_sidebar',true);
if($webd_sidebar==''){
	$webd_sidebar = get_option('webd_sidebar','right');
}
get_header( 'shop' );
$webd_layout = wooevent_global_layout();
if($webd_layout=='layout-3'){
	$webd_layout ='layout-2 layout-3';
}
$clss ='';
if(!is_active_sidebar('wooevent-sidebar')){
	$clss = 'no-sidebar';
}
if(isset($_GET['view']) && sanitize_text_field($_GET['view'])=='list' && is_shop()){
	$clss .= ' webd-list-view';
}elseif(is_shop()){
	$clss .= ' webd-calendar-view';
}
$webd_click_remove = get_option('webd_click_remove','');
if($webd_click_remove=='yes'){
	$clss .= ' webd-remove-click';
}
global $webd_main_purpose;
$webd_main_purpose = get_option('webd_main_purpose');
$webd_layout_purpose = get_post_meta(get_the_ID(),'webd_layout_purpose',true);
if($webd_main_purpose=='custom' && $webd_layout_purpose!='event'){
	$webd_main_purpose = 'woo';
}
?>
<div class="container">
	<div id="exmain-content" class="row<?php if($webd_main_purpose=='woo'){ echo ' hidden-info-event';}?>">
    
    <div id="content" class="webd-main <?php echo $webd_layout.' '.$clss.' '; echo $webd_sidebar!='hide'?'col-md-9':'col-md-12' ?><?php echo ($webd_sidebar == 'left') ? " revert-layout":"";?>">
		<?php
            /**
             * woocommerce_before_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action( 'woocommerce_before_main_content' );
        ?>
    
            <?php while ( have_posts() ) : the_post(); ?>
    
                <?php wooevent_template_plugin('single-product'); ?>
    
            <?php endwhile; // end of the loop. ?>
    
        <?php
            /**
             * woocommerce_after_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action( 'woocommerce_after_main_content' );
        ?>
	</div>
    <?php 
	if($webd_sidebar != 'hide'){?>
        <div class="webd-sidebar col-md-3">
        <?php
            /**
             * woocommerce_sidebar hook.
             *
             * @hooked woocommerce_get_sidebar - 10
             */
            dynamic_sidebar('wooevent-sidebar');
        ?>
        </div>
    <?php }?>
    </div>
</div>
<?php get_footer( 'shop' ); ?>
