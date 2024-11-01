<?php
get_header();
?>
<div class="webd-content-webd_view spk-list">
	<?php
	// Start the loop.
	while ( have_posts() ) : the_post();?>
    <div class="webd-info-sp">
    	<div class="row">
        <div class="col-md-4">
        <div class="webd_view-avatar">
        	<?php if(has_post_thumbnail()){?>
			<div class="img-spk"><a href="<?php the_permalink();?>"><?php the_post_thumbnail('wethumb_300x300');?></a></div>
            <?php } ?>
        </div>
        </div>
        <div class="col-md-8">
        <div class="webd_view-details">
        	<h3 class="webd_view-title">
				<a href="<?php the_permalink();?>"><?php the_title();?></a>
            </h3>
            <span><?php echo get_post_meta( get_the_ID(), 'webd_view_position', true );?></span>
            <div class="webd_view-info">
                <div class="webd_view-content"><?php the_excerpt();?></div>
            </div>
            <div class="webd-social-share"><?php  echo webd_view_print_social_accounts();?></div>
        </div>
        </div>
        </div>
	</div>
    <?php
	endwhile;?>
    <?php
	if(function_exists('paginate_links')) {
		echo '<div class="spekaers-pagination">';
		echo paginate_links( array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format'       => '',
			'add_args'     => false,
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) );
		echo '</div>
		<style type="text/css">
			.spekaers-pagination ul{text-align: center;}
			.spekaers-pagination ul li{ list-style:none; width:auto; display: inline-block;}
			.spekaers-pagination ul li a,
			.spekaers-pagination ul li span{
				display: inline-block;
				background: none;
				background-color: #FFFFFF;
				padding: 6px 15px 0 15px;
				color: rgba(153,153,153,1.0);
				margin: 0px 10px 10px 0;
				min-width: 40px;
				min-height: 40px;
				text-align: center;
				text-decoration: none;
				vertical-align: top;
				font-size: 16px;
				font-weight: bold;
				border-radius: 0px;
				-webkit-border-radius: 0px;
				box-shadow: 0 0 1px rgba(0, 0, 0, 0.15);
				transition: all .2s;
				border: 0;
				line-height: 1.7;
			}
			.spekaers-pagination ul li a:hover,
			.spekaers-pagination ul li span.current{ color: rgba(119,119,119,1.0); background-color: rgba(238,238,238,1.0);}
		}
		</style>';
	}?>
</div>
<?php get_footer(); ?>
