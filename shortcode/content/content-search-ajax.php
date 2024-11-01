<?php
global $the_query,$posts_per_page,$count,$layout,$idsc;
if($the_query->have_posts()){
	echo '<div class="webd-ajax-dfrs">';
	if($layout=='grid'){?>
        <div class="webd-grid-shortcode webd-grid-column-3">
            <div class="ct-grid">
                <div class="grid-container">
                <div class="grid-row">
                <?php 
                global $columns,$number_excerpt,$show_time,$orderby,$img_size;
                $columns = 3; $number_excerpt=15;$show_time=1;$orderby='';$img_size='wethumb_460x307';
                $i=0;
                $it = $the_query->found_posts;
                if($it < $count || $count=='-1'){ $count = $it;}
                if($count  > $posts_per_page){
                    $num_pg = ceil($count/$posts_per_page);
                    $it_ep  = $count%$posts_per_page;
                }else{
                    $num_pg = 1;
                }
                $arr_ids = array();
                while($the_query->have_posts()){ $the_query->the_post();
                    $arr_ids[] = get_the_ID();
                    $i++;
                    wooevent_template_plugin('grid', true);
                    if($i%3==0){?>
                        </div>
                        <div class="grid-row">
                        <?php
                    }
                }?>
                </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php
	}else{
		global $style,$show_time,$show_atc,$show_thumb;
		$style = 2;$show_time=1;$show_atc=0;$show_thumb=0;?>
		<div class="webd-table-lisst table-style-2">
			<table class="webd-table">
                <tbody>
				<?php
				$i=0;
				$it = $the_query->found_posts;
				if($it < $count || $count=='-1'){ $count = $it;}
				if($count  > $posts_per_page){
					$num_pg = ceil($count/$posts_per_page);
					$it_ep  = $count%$posts_per_page;
				}else{
					$num_pg = 1;
				} 
                while($the_query->have_posts()){ $the_query->the_post();
                    if(function_exists('wp_pagenavi')) {
                        $the_query->max_num_pages = $num_pg;
                    }
					wooevent_template_plugin('table', true);
                }?>
                </tbody>
			</table>
		</div>
	<?php }
	if($posts_per_page<$count && $num_pg > 1){
		wpext_pagenavi($the_query, $idsc);
	}
	echo '</div>';
}else{
	$textrsl = get_option('webd_text_no_resu')!='' ? get_option('webd_text_no_resu') : esc_html__('Nothing matched your search terms. Please try again with some different keywords.','WEBDWooEVENT');
	echo '<ul class="products webd-search-ajax no-result-info"><p class="woocommerce-info calendar-info">'.$textrsl.'</p></ul>';
}
