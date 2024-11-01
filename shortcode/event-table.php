<?php
function parwebd_webd_table_func($atts, $content){
	if(is_admin()){ return;}
	global $style,$show_time,$show_atc,$show_thumb,$show_spk;
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$posttype =  isset($atts['posttype']) ? $atts['posttype'] :'product';
	$ids =  isset($atts['ids']) ? $atts['ids'] :'';
	$ex_ids 		= isset($atts['ex_ids']) ? $atts['ex_ids'] : '';
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$order =  isset($atts['order']) ? $atts['order'] :'';
	$show_atc =  isset($atts['show_atc']) ? $atts['show_atc'] :'';
	$orderby =  isset($atts['orderby']) ? $atts['orderby'] :'';
	$meta_key 	= isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value 	= isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$show_spk =  isset($atts['show_spk']) ? $atts['show_spk'] :'';
	$cat =  isset($atts['cat']) ? $atts['cat'] :'';
	$tag =  isset($atts['tag']) ? $atts['tag'] :'';
	$style =  isset($atts['style']) ? $atts['style'] :'';
	
	$taxonomy =  isset($atts['taxonomy']) ? $atts['taxonomy'] :'';
	$terms =  isset($atts['terms']) ? $atts['terms'] :'';
	$webd_view_id =  isset($atts['webd_view_id']) ? $atts['webd_view_id'] :'';
	
	$data_qr =  isset($atts['data_qr']) ? $atts['data_qr'] : $webd_view_id;
	$show_time =  isset($atts['show_time']) ? $atts['show_time'] :'';
	$show_thumb =  isset($atts['show_thumb']) ? $atts['show_thumb'] :'';
	$search_sort =  isset($atts['search_sort']) ? $atts['search_sort'] :'';
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	if($posts_per_page =="" || $posts_per_page > $count){$posts_per_page = $count; $paged ='';}
	$featured = isset($atts['featured']) ? $atts['featured'] : '';
	$args = woo_event_query($posttype, $posts_per_page, $order, $orderby, $meta_key, $cat, $tag, $ids,$paged,$data_qr,$spe_day=false, $featured,$meta_value,$taxonomy,$terms,$ex_ids);
	global $wp;
	$crurl =  home_url( $wp->request );
	ob_start();
	$the_query = new WP_Query( $args );
	$icon ='';
	if($the_query->have_posts()){?>
		<div class="webd-table-lisst <?php echo 'table-style-'.esc_attr($style); if($style=='3'){ echo ' table-style-2';} if($search_sort=='1'){ echo ' table-lv-sort';}?>" id="table-<?php echo $ID;?>">
        	<?php if($search_sort=='1'){
				$icon = '<i class="fa fa-sort" aria-hidden="true"></i>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function ($) {
						if(!jQuery.fn.sortElements){
							jQuery.fn.sortElements = (function(){
								var sort = [].sort;
								return function(comparator, getSortable) {
									getSortable = getSortable || function(){return this;};
									var placements = this.map(function(){
										var sortElement = getSortable.call(this),
											parentNode = sortElement.parentNode,
											nextSibling = parentNode.insertBefore(
												document.createTextNode(''),
												sortElement.nextSibling
											);
										return function() {
											if (parentNode === this) {
												throw new Error(
													"You can't sort elements if any one is a descendant of another."
												);
											}
											parentNode.insertBefore(this, nextSibling);
											parentNode.removeChild(nextSibling);
										};
									});
									return sort.call(this, comparator).each(function(i){
										placements[i].call(getSortable.call(this));
									});
								};
							})();
						}
						var table = $('#table-<?php echo $ID;?>');
						$('#table-<?php echo $ID;?> .webd-sort')
							//.wrapInner('<span title="sort this column"/>')
							.each(function(){
								var th = $(this),
									thIndex = th.index(),
									inverse = false;
								th.on('click', function(){
									$('#table-<?php echo $ID;?> th').removeClass('s-descending');
									$('#table-<?php echo $ID;?> th').removeClass('s-ascending');
									if(inverse == true){
										$(this).addClass('s-descending');
										$(this).removeClass('s-ascending');
									}else{
										$(this).removeClass('s-descending');
										$(this).addClass('s-ascending');
									}
									table.find('td').filter(function(){
										return $(this).index() === thIndex;
									}).sortElements(function(a, b){
										// using data console.log($(a).data('sort'));
										//return $.text([a]) > $.text([b]) ?
										return $(a).data('sort') > $(b).data('sort') ?
											inverse ? -1 : 1
											: inverse ? 1 : -1;
									}, function(){
										// parentNode is the element we want to move
										return this.parentNode; 
									});
									inverse = !inverse;
								});
						});
						$("#table-<?php echo $ID;?> .tb-search").on("keyup", function() {
							var value = this.value.toLowerCase().trim();
							$("#table-<?php echo $ID;?> table tr").each(function (index) {
								//if (!index) return;
								$(this).find("td").each(function () {
									var id = $(this).text().toLowerCase().trim();
									var not_found = (id.indexOf(value) == -1);
									$(this).closest('tr').toggle(!not_found);
									return not_found;
								});
							});
						});
					});
				</script>
                 <div class="r-search">
                    <label><?php echo get_option('webd_text_search')!='' ? get_option('webd_text_search') : esc_html__('Search: ','WEBDWooEVENT');?></label>
                    <input type="text" class="tb-search">
                </div>
            <?php }?>
			<table class="webd-table">
            	<?php if($style!='2' && $style!='3'){?>
                <thead class="thead-inverse">
                  <tr>
                    <th class="webd-sort"><?php echo get_option('webd_text_stdate')!='' ? get_option('webd_text_stdate') : esc_html__("Start Date", "WEBDWooEVENT");?>
                    	<?php echo $icon;?>
                    </th>
                    <th class="webd-sort">
						<?php echo get_option('webd_text_name')!='' ? get_option('webd_text_name') : esc_html__("Name", "WEBDWooEVENT");?>
                    	<span class="webd-hidden-screen"><?php echo get_option('webd_text_details')!='' ? get_option('webd_text_details') : esc_html__("Details", "WEBDWooEVENT");?></span>
                        <?php echo $icon;?>
                    </th>
                    <th class="webd-mb-hide webd-sort"><?php echo get_option('webd_text_loca')!='' ? get_option('webd_text_loca') : esc_html__("Location", "WEBDWooEVENT");?>
                    	<?php echo $icon;?>
                    </th>
                    <th class="webd-mb-hide webd-sort"><?php echo get_option('webd_text_price')!='' ? get_option('webd_text_price') : esc_html__("Price", "WEBDWooEVENT");?>
                    	<?php echo $icon;?>
                    </th>
                    <?php if(get_option('webd_dis_status') !='yes'){?>
                    <th class="webd-mb-hide webd-sort"><?php echo get_option('webd_text_status')!='' ? get_option('webd_text_status') : esc_html__("Status", "WEBDWooEVENT");?>
                    	<?php echo $icon;?>
                    </th>
                    <?php }?>

                    <?php if($show_atc=='1'){?>
                    	<th class="webd-mb-hide"></th>
                    <?php }?>

					<?php if($show_spk =='yes'){?>
                    <th class="webd-mb-hide webd-sort"><?php echo get_option('webd_text_webd_view')!='' ? get_option('webd_text_webd_view') : esc_html__("Speaker", "WEBDWooEVENT");?>
                    	<?php echo $icon;?>
                    </th>
                    <?php }?>

                  </tr>
                </thead>
                <?php }?>
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
                    $i++;
                    if(($num_pg == $paged) && $num_pg!='1'){
                        if($i > $it_ep){ break;}
                    }
					wooevent_template_plugin('table', true);
                }?>
                </tbody>
			</table>
            <?php
			if(function_exists('wp_pagenavi')) {
				?>
                <div class="webd-pagenavi">
					<?php
                    wp_pagenavi(array( 'query' => $the_query));
                    ?>
                </div>
                <?php
			}else{
				if($posts_per_page<$count){
					$loadtrsl = get_option('webd_text_loadm')!='' ? get_option('webd_text_loadm') : esc_html__('Load more','WEBDWooEVENT');
					echo '
						<div class="ex-loadmore">
							<input type="hidden"  name="id_table" value="table-'.$ID.'">
							<input type="hidden"  name="num_page" value="'.$num_pg.'">
							<input type="hidden"  name="num_page_uu" value="1">
							<input type="hidden"  name="current_page" value="1">
							<input type="hidden"  name="current_url" value="'.esc_url($crurl).'">
							<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
							<input type="hidden"  name="param_query" value="'.esc_html(str_replace('\/', '/', json_encode($args))).'">
							<input type="hidden" id="param_shortcode" name="param_shortcode" value="'.esc_html(str_replace('\/', '/', json_encode($atts))).'">
							<a  href="javascript:void(0)" class="loadmore-grid table-loadmore" data-id="table-'.$ID.'">
								<span class="load-text">'.$loadtrsl.'</span><span></span>&nbsp;<span></span>&nbsp;<span></span>
							</a>';
					echo'</div>';
				}
			}?>
		</div>
		<?php
	}else{
		$noftrsl = get_option('webd_text_no_evf')!='' ? get_option('webd_text_no_evf') : esc_html__('No Events Found','WEBDWooEVENT');
		if(($orderby=='has_signed_up' || $orderby=='has_submited') && !is_user_logged_in()){
			$noftrsl = get_option('webd_text_protect_ct')!='' ? get_option('webd_text_protect_ct') : esc_html__('Please Login to See','WEBDWooEVENT');
			echo '<h2>'.$noftrsl.'</h2>';
		}else{
			echo '<p>'.$noftrsl.'</p>';
		}
	}
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}
add_shortcode( 'webd_table', 'parwebd_webd_table_func' );
add_action( 'after_setup_theme', 'webd_reg_vc' );
function webd_reg_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WEBDWooEvents - Table", "WEBDWooEVENT"),
	   "base" => "webd_table",
	   "class" => "",
	   "icon" => "icon-table",
	   "controls" => "full",
	   "category" => esc_html__('WEBDWooEvents','WEBDWooEVENT'),
	   "params" => array(
	   	  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'WEBDWooEVENT'),
			 "param_name" => "style",
			 "value" => array(
			 	esc_html__('Default', 'WEBDWooEVENT') => '',
				esc_html__('Style 1', 'WEBDWooEVENT') => '1',
				esc_html__('Style 2', 'WEBDWooEVENT') => '2',
				esc_html__('Border style', 'WEBDWooEVENT') => '3',
			 ),
			 "description" => ''
		  ),	
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("IDs", "WEBDWooEVENT"),
			"param_name" => "ids",
			"value" => "",
			"description" => esc_html__("Specify post IDs to retrieve", "WEBDWooEVENT"),
		  ),
		  array(
			  "admin_label" => true,
			  "type" => "textfield",
			  "heading" => esc_html__("Exclude IDs", "WEBDWooEVENT"),
			  "param_name" => "ex_ids",
			  "value" => "",
			  "description" => esc_html__("Exclude List post IDs to retrieve, separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Count", "WEBDWooEVENT"),
			"param_name" => "count",
			"value" => "",
			"description" => esc_html__("Number of posts", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Posts per page", "WEBDWooEVENT"),
			"param_name" => "posts_per_page",
			"value" => "",
			"description" => esc_html__("Number items per page", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Category", "WEBDWooEVENT"),
			"param_name" => "cat",
			"value" => "",
			"description" => esc_html__("List of cat ID (or slug), separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Tags", "WEBDWooEVENT"),
			"param_name" => "tag",
			"value" => "",
			"description" => esc_html__("List of tags, separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Custom Taxonomy", "WEBDWooEVENT"),
			"param_name" => "taxonomy",
			"value" => "",
			"description" => esc_html__("Enter name of Taxonomy", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Terms", "WEBDWooEVENT"),
			"param_name" => "terms",
			"value" => "",
			"description" => esc_html__("List of Terms ID (or slug) of custom taxonomy, separated by a comma", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Speaker id", "WEBDWooEVENT"),
			"param_name" => "webd_view_id",
			"value" => "",
			"description" => esc_html__("Enter webd_view id", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order", 'WEBDWooEVENT'),
			 "param_name" => "order",
			 "value" => array(
			 	esc_html__('DESC', 'WEBDWooEVENT') => 'DESC',
				esc_html__('ASC', 'WEBDWooEVENT') => 'ASC',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order by", 'WEBDWooEVENT'),
			 "param_name" => "orderby",
			 "value" => array(
			 	esc_html__('Date', 'WEBDWooEVENT') => 'date',
				esc_html__('ID', 'WEBDWooEVENT') => 'ID',
				esc_html__('Author', 'WEBDWooEVENT') => 'author',
			 	esc_html__('Title', 'WEBDWooEVENT') => 'title',
				esc_html__('Name', 'WEBDWooEVENT') => 'name',
				esc_html__('Modified', 'WEBDWooEVENT') => 'modified',
			 	esc_html__('Parent', 'WEBDWooEVENT') => 'parent',
				esc_html__('Random', 'WEBDWooEVENT') => 'rand',
				esc_html__('Menu order', 'WEBDWooEVENT') => 'menu_order',
				esc_html__('Meta value', 'WEBDWooEVENT') => 'meta_value',
				esc_html__('Meta value num', 'WEBDWooEVENT') => 'meta_value_num',
				esc_html__('Post__in', 'WEBDWooEVENT') => 'post__in',
				esc_html__('Upcoming', 'WEBDWooEVENT') => 'upcoming',
				esc_html__('Ongoing & Upcoming', 'WEBDWooEVENT') => 'ontoup',
				esc_html__('Past', 'WEBDWooEVENT') => 'past',
				esc_html__('Day', 'WEBDWooEVENT') => 'day',
				esc_html__('Week', 'WEBDWooEVENT') => 'week',
				esc_html__('Month', 'WEBDWooEVENT') => 'month',
				esc_html__('Year', 'WEBDWooEVENT') => 'year',
				esc_html__('None', 'WEBDWooEVENT') => 'none',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta key", "WEBDWooEVENT"),
			"param_name" => "meta_key",
			"value" => "",
			"description" => esc_html__("Enter meta key to query", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta Value", "WEBDWooEVENT"),
			"param_name" => "meta_value",
			"value" => "",
			"description" => esc_html__("Enter meta value to query", "WEBDWooEVENT"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show add to cart", 'WEBDWooEVENT'),
			 "param_name" => "show_atc",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => esc_html__("Show add to cart button instead if view details button", 'WEBDWooEVENT'),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show time", 'WEBDWooEVENT'),
			 "param_name" => "show_time",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show Thumbnail", 'WEBDWooEVENT'),
			 "param_name" => "show_thumb",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show only Featured event", 'WEBDWooEVENT'),
			 "param_name" => "featured",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable Live Search & Sort", 'WEBDWooEVENT'),
			 "param_name" => "search_sort",
			 "value" => array(
			 	esc_html__('No', 'WEBDWooEVENT') => '',
				esc_html__('Yes', 'WEBDWooEVENT') => '1',
			 ),
			 "description" => ''
		  ),
	   )
	));
	}
}