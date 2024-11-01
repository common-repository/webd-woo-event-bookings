;(function($){
		function ex_carousel(){
			$(".is-carousel").each(function(){
				var carousel_id = $(this).attr('id');
				var auto_play = $(this).data('autoplay');
				var autospeed = $(this).data('autospeed');
				if(auto_play && autospeed!='' && autospeed > 0){
					auto_play = autospeed;
				}else if(auto_play){
					auto_play = true;
				}else{ auto_play = false;}
				var items = $(this).data('items');
				var navigation = $(this).data('navigation');
				var pagination = $(this).data('pagination');
				var paginationNumbers = $(this).data('paginationNumbers');
				//if (typeof webd_exlCarousel === "undefined") { return;}
				if($(this).hasClass('single-carousel')){ //single style
					$(this).webd_exlCarousel({
						singleItem:true,
						autoHeight: false,
						autoPlay: auto_play,
						navigation: navigation?true:false,
						autoHeight : true,
						navigationText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
						addClassActive : true,
						pagination:pagination?true:false,
						paginationNumbers:paginationNumbers?true:false,
						stopOnHover: true,
						slideSpeed : 600,
						transitionStyle : "fade"
					});
				}else{
					if($(this).hasClass('wenv-car')){ $auh = true;}
					$(this).webd_exlCarousel({
						autoPlay: auto_play,
						items: items?items:4,
						itemsDesktop: items?false:4,
						itemsDesktopSmall: items?(items>3?3:false):3,
						singleItem: items==1?true:false,
						autoHeight : false,
						navigation: navigation?true:false,
						paginationNumbers:paginationNumbers?true:false,
						navigationText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
						pagination:pagination?true:false,
						slideSpeed: 500,
						addClassActive : true
					});
				}
			});
		}	
		$(document).ready(function() {
			ex_carousel();
			jQuery( '.single .webd-main div.quantity:not(.buttons_added), .single .webd-main td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" id="add_ticket" class="plus" />' ).prepend( '<input type="button" value="-" id="minus_ticket" class="minus" />' );
		jQuery('.single:not(.fusion-body) .webd-main:not(.webd-remove-click) .buttons_added').on('click', '#minus_ticket',function() {
			var value = parseInt(jQuery(this).closest('.quantity.buttons_added').find('.input-text.qty').val()) - 1;
			if(value>0){

				jQuery(this).closest('.quantity.buttons_added').find('.input-text.qty').val(value);
			}
		});
		jQuery('.single:not(.fusion-body) .webd-main:not(.webd-remove-click) .buttons_added').on('click', '#add_ticket',function() {
			var value = parseInt(jQuery(this).prev().val()) + 1;
			jQuery(this).prev().val(value);
		});
		if(jQuery(".submit-time").length>0){
			var time_fm = jQuery(".wedate_format + .wetime_format").val();
			if(time_fm =='' || typeof time_fm =='undefined'){
				time_fm = "H:i:A";
			}
			jQuery(".submit-time").timepicker({
				"timeFormat":time_fm
			});
		}
		if(jQuery(".submit-date").length>0){
			var date_fm = jQuery(".submit-date + .wedate_format").val();
			if(date_fm=='' || typeof date_fm =='undefined'){
				date_fm = "mm/dd/yyyy";
			}
			jQuery(".submit-date:not(.show-ld)").datepicker({
					"todayHighlight" : true,
					"startDate": new Date(),
					"autoclose": true,
					"format":date_fm
			});
			jQuery(".submit-date.show-ld").each(function(){
				var date_fm = jQuery(".wesm-date .wedate_format").val();
				if(date_fm=='' || typeof date_fm =='undefined'){
					date_fm = "mm/dd/yyyy";
				}
				jQuery(".submit-date.show-ld").datepicker({
						"todayHighlight" : true,
						"autoclose": true,
						"format":date_fm
				});
				var startDate = $(this).val(); //Date Format YYYY-MM-DD
				$(this).val(startDate).datepicker("update");
			});
		}
		
		function getDataAttr(date) {
			var $mont = (date.getMonth() + 1);
			$mont = parseFloat($mont);
			if($mont < 10){
				return date.getFullYear() + "-0" + ($mont) + "-" + (date.getDate().toString().length === 2 ? date.getDate() : "0" + date.getDate());
			}else{
				return date.getFullYear() + "-" + ($mont) + "-" + (date.getDate().toString().length === 2 ? date.getDate() : "0" + date.getDate());
			}
		};
		function addClassByDate(date,id) {
			var dataAttr = getDataAttr(date);
			$("[data-date='" + dataAttr + "']").addClass("hasevent");
			var ids = $("[data-date='" + dataAttr + "']").attr( "data-ids");
			if(typeof ids !='undefined'){
				ids = ids+','+id;
			}else{
				ids = id;
			}
			$("[data-date='" + dataAttr + "']").attr( "data-ids", ids );
		}
		$(".webd-calendar").each(function(){
			var $this = $(this);
			var id_crsc  		= $(this).data('id');
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var calendar_cat  		= $('#'+id_crsc+' input[name=calendar_cat]').val();
			var taxonomy  		= $('#'+id_crsc+' input[name=taxonomy]').val();
			var terms  		= $('#'+id_crsc+' input[name=terms]').val();
			var webd_view_id  		= $('#'+id_crsc+' input[name=webd_view_id]').val();
			var calendar_ids  		= $('#'+id_crsc+' input[name=calendar_ids]').val();
			var ex_ids  		= $('#'+id_crsc+' input[name=ex_ids]').val();
			var calendar_orderby  		= $('#'+id_crsc+' input[name=calendar_orderby]').val();
			var calendar_view  		= $('#'+id_crsc+' input[name=calendar_view]').val();
			var calendar_defaultDate  		= $('#'+id_crsc+' input[name=calendar_defaultDate]').val();
			var calendar_firstDay  		= $('#'+id_crsc+' input[name=calendar_firstDay]').val();
			var show_bt  		= $('#'+id_crsc+' input[name=show_bt]').val();
			var scrolltime  		= $('#'+id_crsc+' input[name=scrolltime]').val();
			var mintime  		= $('#'+id_crsc+' input[name=mintime]').val();
			var maxtime  		= $('#'+id_crsc+' input[name=maxtime]').val();
			var viewas_button  		= $('#'+id_crsc+' input[name=viewas_button]').val();
			var current_url  		= $('#'+id_crsc+' input[name=current_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
			var ct_hd = 'title';
			if(viewas_button==''){
				ct_hd ='';
				viewas_button = 'title';
			}
			var events;
			var $defaultView =  'month';
			var $target = 'bottom';
			if(calendar_view == 'week'){
				$defaultView =  'agendaWeek';
				$target = 'mouse';
			}else if(calendar_view == 'day'){
				$defaultView =  'agendaDay';
				$target = 'mouse';
			}else if(calendar_view != ''){
				$defaultView = calendar_view;
				if(calendar_view != 'month'){ $target = 'mouse';}
			}
			if(!$('#'+id_crsc+' #calendar').hasClass('widget-style') && $(window).width() < 765 ){
				$defaultView =  'listMonth';
			}
			$('#'+id_crsc+' #calendar').fullCalendar({
				windowResize: function(view) {
					if(!$('#'+id_crsc+' #calendar').hasClass('widget-style')){
						if($(window).width() < 765 ){
							$('#'+id_crsc+' #calendar').fullCalendar('changeView','listMonth');
						}else{
							$('#'+id_crsc+' #calendar').fullCalendar('changeView',$defaultView);
						}
					}
				},
				views: {
				  listYear: {
					type: 'listYear',
					buttonText: $('#'+id_crsc+' input[name=yearl_text]').val()
				  }
				},
				header: {
					left: 'prev,next today',
					center: ct_hd,
					right: viewas_button
				},
				defaultDate: calendar_defaultDate,
				defaultView: $defaultView,
				firstDay: calendar_firstDay,
				locale: $('#'+id_crsc+' input[name=calendar_language]').val(),
				eventLimit: false, // allow "more" link when too many events
				nextDayThreshold: '00:00:00',
				scrollTime: scrolltime!='' ? scrolltime : '00:00:00',
				minTime: mintime!='' ? mintime : '00:00:00',
				maxTime: maxtime!='' ? maxtime : '24:00:00',
				events: function(start, end, timezone, callback) {
					$.ajax({
						type: 'GET',
						url: ajax_url,
						dataType: 'json',
						data: {
							action: 'webd_get_events_calendar',
							start: start.unix(),
							end: end.unix(),
							category: calendar_cat,
							taxonomy: taxonomy,
							terms: terms,
							webd_view_id: webd_view_id,
							ids: calendar_ids,
							ex_ids: ex_ids,
							orderby: calendar_orderby,
							show_bt: show_bt,
							current_url: current_url,
							type:calendar_view,
							lang:$('#'+id_crsc+' input[name=calendar_wpml]').val(),
							param_shortcode: param_shortcode,						
						},
						success: function(data){
							webd_event_ofday();
							if(data != '0')
							{
								events = (data);
								if(typeof(events)!='object' || events==null){
									$('#'+id_crsc+' .calendar-info').removeClass('hidden');
								}else{
									$('#'+id_crsc+' .calendar-info').addClass('hidden');
								}
								callback(events);
							}
						}
					});
				},
				eventRender: function(event, element) {
					if($('#'+id_crsc+' #calendar').hasClass('widget-style')){
						var evStartDate = new Date(event.start),
							evFinishDate = new Date(event.end);
						var userTimezoneOffset = evStartDate.getTimezoneOffset() * 60000;
						evStartDate = new Date(evStartDate.getTime() + userTimezoneOffset);	
						evFinishDate = new Date(evFinishDate.getTime() + userTimezoneOffset);	
						if (event.end) {
							while (evStartDate < evFinishDate) {
								addClassByDate(evStartDate,event.id);
								evStartDate.setDate(evStartDate.getDate() + 1);
							}
						} else {
							addClassByDate(evStartDate,event.id);
						}
					}else{
						element.find('.fc-title').html(event.title);
						element.find('.fc-list-item-title').html(event.title);
						var content = '<div class="webd-tooltip">'
						+'<div class="webd-tooltip-content">'
						+event.evlabel+' '
						+'<h4><a href="'+event.url+'">'+event.title+' '+event.sub_title+'</a></h4>'
						+'<p><i class="fa fa-calendar"></i>'+event.startdate+'</p>'
						+(event.enddate && '<p><i class="fa fa-calendar-times-o"></i>'+event.enddate+'</p>' || '')
						+'<p class="webd-info"><i class="fa fa-map-marker"></i> '+event.location + '</p>'
						+(event.status && '<p><i class="fa fa-ticket"></i> '+event.status+'</p>' || '')
						+(event.price && '<p class="tt-price"><i class="fa fa-shopping-basket"></i><span>'+event.price+'</span></p>' || '')
						+(event.url_ontt && '<p class="tt-bt"><a href="'+event.url_ontt+'" class="btn btn btn-primary webd-button">'+event.text_onbt+'</a></p>' || '')
						+'</div><div class="tt-image"><a href="'+event.url+'"><img src="'+event.thumbnail+'"/></a></div</div>';
						element.qtip({
							prerender: true,
							content: {text:content, button: 'Close'},
							style: {
								tip: {
									corner: false,
									width: 12
								},
								classes: 'ex-qtip'
							},
							position: {
								my: 'bottom left',
								at: 'bottom center',
								target:$target,
								viewport: $('body'),
							},
							show: {  solo: false,},
							hide: {
							  delay: 100,
							  fixed: true,
							  effect: function() { $(this).fadeOut(100); }
							},
						});
					}
				},
				eventAfterAllRender: function(event, element) {
					if($('#'+id_crsc+' #calendar').hasClass('widget-style')){
						$( '#'+id_crsc+' .fc-day-top.fc-today.hasevent' ).trigger( "click" );
					}
				},
				loading: function(bool) {
					if (bool) {
						$('#'+id_crsc).addClass('loading');
					}else {
						$('#'+id_crsc).removeClass('loading');
					}
				},
				viewRender: function(view, element) {
					if(view['name'] && view['name']=='listYear'){
						$('#'+id_crsc+' #calendar .webd-cal-filter-month').hide(200);
					}else{
						$('#'+id_crsc+' #calendar .webd-cal-filter-month').show(200);
					}
					if(!$('#'+id_crsc+' #calendar .fc-toolbar + .webd-cal-ftgr').length){
						$('#'+id_crsc+' #calendar .fc-toolbar').after( $('#'+id_crsc+' .webd-cal-ftgr').show());
					}
					$('#'+id_crsc+' #calendar select[name=product_cat], #'+id_crsc+' #calendar  select[name=product_loc], #'+id_crsc+' #calendar select[name=product_tag], #'+id_crsc+' #calendar select[name=product_spk]').on('change', function() {
						var sl_cat,sl_tag,sl_loc,sl_spk ='';
						if($('#'+id_crsc+' #calendar select[name=product_cat]').length){
							sl_cat = $('#'+id_crsc+' #calendar select[name=product_cat]').val();
						}
						if($('#'+id_crsc+' #calendar select[name=product_tag]').length){
							sl_tag = $('#'+id_crsc+' #calendar select[name=product_tag]').val();
						}
						if($('#'+id_crsc+' #calendar select[name=product_loc]').length){
							sl_loc = $('#'+id_crsc+' #calendar select[name=product_loc]').val();
						}
						if($('#'+id_crsc+' #calendar select[name=product_spk]').length){
							sl_spk = $('#'+id_crsc+' #calendar select[name=product_spk]').val();
						}
						$('#'+id_crsc+' #calendar').fullCalendar('removeEventSources');
						if($('#'+id_crsc+' #calendar').hasClass('widget-style')){
							jQuery('#'+id_crsc+' .fc-day-top').removeClass('hasevent');
						}
						$('#'+id_crsc+' #calendar').fullCalendar(
							'addEventSource', 
							function(start, end, timezone, callback) {
								//$('#'+id_crsc).addClass('loading');
								$.ajax({
									type: 'GET',
									url: ajax_url,
									dataType: 'json',
									data: {
										action: 'webd_get_events_calendar',
										start: start.unix(),
										end: end.unix(),
										category: sl_cat,
										taxonomy: taxonomy,
										terms: terms,
										webd_view_id: sl_spk,
										ids: calendar_ids,
										orderby: calendar_orderby,
										show_bt: show_bt,
										current_url: current_url,
										type:calendar_view,
										lang:$('#'+id_crsc+' input[name=calendar_wpml]').val(),
										tag:sl_tag,
										location:sl_loc,
										param_shortcode: param_shortcode,						
									},
									success: function(data){
										//$('#'+id_crsc).removeClass('loading');
										webd_event_ofday();
										if(data != '0'){
											events = (data);
											if(typeof(events)!='object' || events==null){
												$('#'+id_crsc+' .calendar-info').removeClass('hidden');
											}else{
												$('#'+id_crsc+' .calendar-info').addClass('hidden');
											}
										}
										callback(events);
									}// end filter
								});
							}
						);
					});
				}
			});
			$('#'+id_crsc+' select[name=cal-filter-month]').on('change', function() {
				$('#'+id_crsc+' #calendar').fullCalendar('gotoDate', $(this).val());
			});
		});
		webd_event_ofday();
		function webd_event_ofday(){
			jQuery(".fc-day-top").on('click', function() {
				if($(this).hasClass('hasevent')){
					var ids = $(this).data('ids');
					var id_crsc = $(this).closest('.webd-calendar').data('id');
					if($('#'+id_crsc).hasClass('loading')){ return;}
					$('#'+id_crsc+' .fc-day-top').removeClass('fc-today');
					$('#'+id_crsc).addClass('loading');
					$('#'+id_crsc+' .wt-eventday').addClass('de-active');
					$(this).addClass('fc-today');
					var param = {
						action: 'ex_loadevent_ofday',
						param_day: $(this).data('date'),
						ids:ids,
					};
					var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
					$.ajax({
						type: "post",
						url: ajax_url,
						dataType: 'html',
						data: (param),
						success: function(data){
							$('#'+id_crsc).removeClass('loading');
							$('#'+id_crsc+' .wt-eventday').removeClass('de-active');
							if(data != '0')
							{
								if(data == ''){ 
									$('#'+id_crsc+' .wt-eventday').html('');
								}
								else{
									$('#'+id_crsc+' .wt-eventday .day-event-details').remove();
									var $g_container = $('#'+id_crsc+' .wt-eventday');
									$g_container.append(data);
									setTimeout(function(){ 
										$('#'+id_crsc+' .wt-eventday').addClass("active");
									}, 200);
								}
							}else{$('#'+id_crsc+' .wt-eventday').html('');}
						}
					});
				}
			});
			return false;
		};
		$(".webd-coundown-item").each(function(){
			var cd_id = $(this).attr('id');
			var day_text  		= $('.webd-countdonw input[name=cd-days]').val();
			var hr_text  		= $('.webd-countdonw input[name=cd-hr]').val();
			var min_text  		= $('.webd-countdonw input[name=cd-min]').val();
			var sec_text  		= $('.webd-countdonw input[name=cd-sec]').val();
			var cd_date = $(this).data('date');
			if(cd_date==''){ return;}
			var cd_date_timezone = $(this).data('timezone');
			if(cd_date_timezone!='' && cd_date_timezone!='def'){
				cd_date_timezone = cd_date_timezone*60;
				var cd_date_tz = moment($(this).data('date'));
				
				var date_another = cd_date_tz.clone();
				date_another.utcOffset(cd_date_timezone);
				date_another.add(cd_date_tz.utcOffset() - date_another.utcOffset(), 'minutes');
				cd_date = date_another.toDate();
				
			}
			$(this).wecountdown(cd_date, function(event) {
				$(this).html(
				 event.strftime(''
				 + '<div class="cd-item"><span class="cd-number">%D</span><span> '+day_text+' </span></div>'
				 + '<div class="cd-item"><span class="cd-number">%H</span><span> '+hr_text+' </span></div>'
				 + '<div class="cd-item"><span class="cd-number">%M</span><span> '+min_text+' </span></div>'
				 + '<div class="cd-item"><span class="cd-number">%S</span><span> '+sec_text+'</span></div>'
				 ));
			});
		});
		jQuery(".webd-search-dropdown:not(.webd-sfilter)").on('click', 'li a', function(){
			jQuery(".webd-search-dropdown:not(.webd-sfilter) .webd-search-dropdown-button .button-label").html(jQuery(this).text());
			jQuery(".webd-product-cat").val(jQuery(this).data('value'));
			jQuery(".webd-search-dropdown").removeClass('open');
			return false;
		});
		jQuery(".button-scroll").click(function() {
			var $scrtop = jQuery(".summary").offset().top;
			jQuery('html, body').animate({
				scrollTop: ($scrtop-100)
			}, 500);
		});
		
		$('.webd-viewas .input-group-btn:not(.webd-sfilter), .wooevent-search .input-group-btn').on('click', function(e) {
			$menu = $(this);
			if (!$menu.hasClass('open')) {
				$menu.addClass('open');
				$(document).one('click', function closeTooltip(e) {
					if ($menu.has(e.target).length === 0 && $('.input-group-btn').has(e.target).length === 0) {
						$menu.removeClass('open');
					} else if ($menu.hasClass('open')) {
						$(document).one('click', closeTooltip);
					}
				});
			} else {
				$menu.removeClass('open');
			}
		}); 
		$('.webd-search-shortcode .input-group-btn.webd-sfilter').on('click', function(e) {
			$this = $(this);
			var id_crsc  		= $this.data('id');
			if(!$this.hasClass('webd-sfilter-close')){
				$this.addClass('webd-sfilter-close');
				$('#'+id_crsc+' .webd-filter-expand').addClass('active');
			}else{
				$this = $(this);
				$this.removeClass('webd-sfilter-close');
				$('#'+id_crsc+' .webd-filter-expand').removeClass('active');
			}
		});
		$('.loadmore-grid').on('click',function() {
			var $this_click = $(this);
			if($this_click.hasClass('table-loadmore') || $this_click.hasClass('webd_view-loadmore')){ return;}
			$this_click.addClass('disable-click');
			var id_crsc  		= $this_click.data('id');
			var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
			$('#'+id_crsc+' .loadmore-grid').addClass("loading");
			var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
			var param_ids  		= $('#'+id_crsc+' input[name=param_ids]').val();
			var page  		= $('#'+id_crsc+' input[name=current_page]').val();
			var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
				var param = {
					action: 'ex_loadmore_grid',
					param_query: param_query,
					param_ids: param_ids,
					id_crsc: id_crsc,
					page: page*1+1,
					param_shortcode: param_shortcode,
				};
	
				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: 'html',
					data: (param),
					success: function(data){
						if(data != '0')
						{
							n_page = n_page*1+1;
							$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
							if(data == ''){ 
								$('#'+id_crsc+' .loadmore-grid').remove();
							}
							else{
								$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
								var $g_container = $('#'+id_crsc+' .grid-container');
								$g_container.append(data);
								setTimeout(function(){ 
									$('#'+id_crsc+' .grid-row').addClass("active");
								}, 200);
								$('#'+id_crsc+' .loadmore-grid').removeClass("loading");
								$this_click.removeClass('disable-click');
							}
							if(n_page == num_page){
								$('#'+id_crsc+' .loadmore-grid').remove();
							}
							
						}else{$('.row.loadmore').html('error');}
					}
				});
			return false;	
		});
		$('.loadmore-grid.table-loadmore').on('click',function() {
			var $this_click = $(this);
			$this_click.addClass('disable-click');
			var id_crsc  		= $this_click.data('id');
			var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
			$('#'+id_crsc+' .loadmore-grid').addClass("loading");
			var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
			var page  		= $('#'+id_crsc+' input[name=current_page]').val();
			var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
			var current_url  		= $('#'+id_crsc+' input[name=current_url]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
				var param = {
					action: 'ex_loadmore_table',
					param_query: param_query,
					page: page*1+1,
					param_shortcode: param_shortcode,
					url_page: current_url,
				};
	
				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: 'html',
					data: (param),
					success: function(data){
						if(data != '0')
						{
							n_page = n_page*1+1;
							$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
							if(data == ''){ 
								$('#'+id_crsc+' .loadmore-grid').remove();
							}else{
								$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
								var $g_container = $('#'+id_crsc+' tbody');
								$g_container.append(data);
								setTimeout(function(){ 
									$('#'+id_crsc+' tbody .tb-load-item').addClass("active");
								}, 200);
								$('#'+id_crsc+' .loadmore-grid').removeClass("loading");
								$this_click.removeClass('disable-click');
							}
							if(n_page == num_page){
								$('#'+id_crsc+' .loadmore-grid').remove();
							}
						}else{$('.row.loadmore').html('error');}
					}
				});
			return false;	
		});
		$('.loadmore-grid.webd_view-loadmore').on('click',function() {
			var $this_click = $(this);
			$this_click.addClass('disable-click');
			var id_crsc  		= $this_click.data('id');
			var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
			$('#'+id_crsc+' .loadmore-grid').addClass("loading");
			var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
			var page  		= $('#'+id_crsc+' input[name=current_page]').val();
			var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
			var current_url  		= $('#'+id_crsc+' input[name=current_url]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
				var param = {
					action: 'ex_loadmore_webd_view',
					param_query: param_query,
					page: page*1+1,
					param_shortcode: param_shortcode,
					url_page: current_url,
				};
				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: 'html',
					data: (param),
					success: function(data){
						if(data != '0')
						{
							n_page = n_page*1+1;
							$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
							if(data == ''){ 
								$('#'+id_crsc+' .loadmore-grid').remove();
							}
							else{
								$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
								var $g_container = $('#'+id_crsc+' .grid-container');
								$g_container.append(data);
								setTimeout(function(){ 
									$('#'+id_crsc+' .grid-row').addClass("active");
								}, 200);
								$('#'+id_crsc+' .loadmore-grid').removeClass("loading");
								$this_click.removeClass('disable-click');
							}
							if(n_page == num_page){
								$('#'+id_crsc+' .loadmore-grid').remove();
							}
						}else{$('.row.loadmore').html('error');}
					}
				});
			return false;	
		});
		/*-Search ajax shortcode-*/
		function webd_ajax_search_sc(id_crsc,$page){
			$('.'+id_crsc).addClass('loading');
			var result_showin  		= $('#'+id_crsc+' input[name=result_showin]').val();
			var key_word  		= $('#'+id_crsc+' .wooevent-search-form input[name=s]').val();
			var cat  		= $('#'+id_crsc+' select[name=product_cat]').val();
			var tag  		= $('#'+id_crsc+' select[name=product_tag]').val();
			var year  		= $('#'+id_crsc+' select[name=evyear]').val();
			var location  		= $('#'+id_crsc+' select[name=location]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var search_layout  		= $('#'+id_crsc+' input[name=search_layout]').val();
			var param = {
				action: 'webd_ajax_search',
				key_word: key_word,
				cat: cat,
				tag: tag,
				year: year,
				location: location,
				page: $page,
				layout: search_layout,
				idsc: id_crsc,
			};
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'html',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data == ''){ 
						}
						else{
							//$this.removeClass('disable-click');
							if($('.'+id_crsc+' '+result_showin).length){
								$showin = $('.'+id_crsc+' '+result_showin);
							}else{ $showin = $(result_showin); }
							$($showin).fadeOut({
								duration:0,
								complete:function(){
									$( this ).empty();
								}
							});
							$('body').addClass('webd-ajax-mode');
							$('.'+id_crsc).removeClass("loading");
							
							$showin.append(data).fadeIn();
						}
					}else{ alert('error');}
				}
			});
			return false;
		}
		$.urlParam = function(name,url){
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
			if (results==null){
			   return null;
			}
			else{
			   return decodeURI(results[1]) || 0;
			}
		}
		$('body').on('click', '.webd-ajax-pagination li a.page-numbers', function(event) {
			$id  = $(this).closest('.webd-ajax-pagination').attr('id');
			$(this).closest('.webd-ajax-dfrs').addClass('loading');
			$('#'+$id+' li .page-numbers').removeClass('current');
			$(this).addClass('current');
			event.preventDefault();
			if(!$(this).hasClass('disable-click')){
				$(this).addClass('disable-click');
				$id = $(this).closest('.webd-ajax-pagination').data('id');
				webd_ajax_search_sc($id,$.urlParam('paged',$(this).attr('href')));
			}
		});
		$('.webd-ajax-search button.webd-search-submit').on('click',function() {
			var $this_click = $(this);
			var id_crsc  		= $this_click.data('id');
			$('.'+id_crsc).addClass('remove-view-tb');
			webd_ajax_search_sc(id_crsc,'');
			return false;	
		});
	});	
}(jQuery));
