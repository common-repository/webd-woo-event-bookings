<?php
    global $woocommerce, $product;
    if ( ! function_exists( 'get_woocommerce_price_format' ) ) {
        $currency_pos = get_option( 'woocommerce_currency_pos' );
        switch ( $currency_pos ) {
            case 'left' :
                $format = '%1$s%2$s';
            break;
            case 'right' :
                $format = '%2$s%1$s';
            break;
            case 'left_space' :
                $format = '%1$s&nbsp;%2$s';
            break;
            case 'right_space' :
                $format = '%2$s&nbsp;%1$s';
            break;
        }
        $currency_fm = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), $format ) );
    } else {
        $currency_fm = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) );
    }
    $text = get_option('webd_text_total')!='' ? get_option('webd_text_total') :  esc_html__('Total:','WEBDWooEVENT');
    echo sprintf('<div id="product_total_price" style="display: block;">%s %s</div>',$text,'<span class="price">'.$product->get_price().'</span>');?>
    <script>
        if (typeof accounting === 'undefined') {
            (function(p,z){function q(a){return!!(""===a||a&&a.charCodeAt&&a.substr)}function m(a){return u?u(a):"[object Array]"===v.call(a)}function r(a){return"[object Object]"===v.call(a)}function s(a,b){var d,a=a||{},b=b||{};for(d in b)b.hasOwnProperty(d)&&null==a[d]&&(a[d]=b[d]);return a}function j(a,b,d){var c=[],e,h;if(!a)return c;if(w&&a.map===w)return a.map(b,d);for(e=0,h=a.length;e<h;e++)c[e]=b.call(d,a[e],e,a);return c}function n(a,b){a=Math.round(Math.abs(a));return isNaN(a)?b:a}function x(a){var b=c.settings.currency.format;"function"===typeof a&&(a=a());return q(a)&&a.match("%v")?{pos:a,neg:a.replace("-","").replace("%v","-%v"),zero:a}:!a||!a.pos||!a.pos.match("%v")?!q(b)?b:c.settings.currency.format={pos:b,neg:b.replace("%v","-%v"),zero:b}:a}var c={version:"0.4.1",settings:{currency:{symbol:"$",format:"%s%v",decimal:".",thousand:",",precision:2,grouping:3},number:{precision:0,grouping:3,thousand:",",decimal:"."}}},w=Array.prototype.map,u=Array.isArray,v=Object.prototype.toString,o=c.unformat=c.parse=function(a,b){if(m(a))return j(a,function(a){return o(a,b)});a=a||0;if("number"===typeof a)return a;var b=b||".",c=RegExp("[^0-9-"+b+"]",["g"]),c=parseFloat((""+a).replace(/\((.*)\)/,"-$1").replace(c,"").replace(b,"."));return!isNaN(c)?c:0},y=c.toFixed=function(a,b){var b=n(b,c.settings.number.precision),d=Math.pow(10,b);return(Math.round(c.unformat(a)*d)/d).toFixed(b)},t=c.formatNumber=c.format=function(a,b,d,i){if(m(a))return j(a,function(a){return t(a,b,d,i)});var a=o(a),e=s(r(b)?b:{precision:b,thousand:d,decimal:i},c.settings.number),h=n(e.precision),f=0>a?"-":"",g=parseInt(y(Math.abs(a||0),h),10)+"",l=3<g.length?g.length%3:0;return f+(l?g.substr(0,l)+e.thousand:"")+g.substr(l).replace(/(\d{3})(?=\d)/g,"$1"+e.thousand)+(h?e.decimal+y(Math.abs(a),h).split(".")[1]:"")},A=c.formatMoney=function(a,b,d,i,e,h){if(m(a))return j(a,function(a){return A(a,b,d,i,e,h)});var a=o(a),f=s(r(b)?b:{symbol:b,precision:d,thousand:i,decimal:e,format:h},c.settings.currency),g=x(f.format);return(0<a?g.pos:0>a?g.neg:g.zero).replace("%s",f.symbol).replace("%v",t(Math.abs(a),n(f.precision),f.thousand,f.decimal))};c.formatColumn=function(a,b,d,i,e,h){if(!a)return[];var f=s(r(b)?b:{symbol:b,precision:d,thousand:i,decimal:e,format:h},c.settings.currency),g=x(f.format),l=g.pos.indexOf("%s")<g.pos.indexOf("%v")?!0:!1,k=0,a=j(a,function(a){if(m(a))return c.formatColumn(a,f);a=o(a);a=(0<a?g.pos:0>a?g.neg:g.zero).replace("%s",f.symbol).replace("%v",t(Math.abs(a),n(f.precision),f.thousand,f.decimal));if(a.length>k)k=a.length;return a});return j(a,function(a){return q(a)&&a.length<k?l?a.replace(f.symbol,f.symbol+Array(k-a.length+1).join(" ")):Array(k-a.length+1).join(" ")+a:a})};if("undefined"!==typeof exports){if("undefined"!==typeof module&&module.exports)exports=module.exports=c;exports.accounting=c}else"function"===typeof define&&define.amd?define([],function(){return c}):(c.noConflict=function(a){return function(){p.accounting=a;c.noConflict=z;return c}}(p.accounting),p.accounting=c)})(this);
        }

        jQuery(function($){
        function addCommas(nStr){
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }
        var currency    = currency = ' <?php echo get_woocommerce_currency_symbol(); ?>';
            function priceformat() {
                var product_total ='';
                if($('div.product.product-type-grouped').length){
                    var $price_gr = '';
                    var $nb_gr = '';
                    $(".woocommerce div.product form.cart .group_table tr").each(function(){
                        if($(this).find('woocommerce-variation-price.ins.amount').length){
                            $price_gr = $(this).find('.woocommerce-Price-amount.ins.amount').text();
                        }else{
                            $price_gr = $(this).find('.woocommerce-Price-amount.amount').text();
                        }
                        $price_gr = $price_gr.replace( currency, '' );
                        $price_gr = $price_gr.replace( '<?php echo get_option( 'woocommerce_price_thousand_sep' );?>', '' );
                        $price_gr = $price_gr.replace( '<?php echo get_option( 'woocommerce_price_decimal_sep' );?>', '.' );
                        $price_gr = $price_gr.replace(/[^0-9\.]/g, '' );
                        $nb_gr =  $(this).find('input.qty').val();
                        
                        product_total = product_total*1 + ($price_gr * $nb_gr);
                    });
                }else{
                    if($('div.product.product-type-variable').length){
                        if($('.woocommerce-variation-price ins .amount').length){
                            product_total = jQuery('.woocommerce-variation-price ins .amount').text();
                        }
                        if(product_total==''){
                            product_total = jQuery('.woocommerce-variation-price .amount').text();
                        }
                    }else {
                        if($('.summary .price ins .amount').length){
                            product_total = jQuery('.summary .price ins .amount').text();
                        }
                        if(product_total==''){
                            product_total = jQuery('.summary .price .amount').text();
                        }
                    }
                    var quatity = jQuery('form.cart .quantity input.qty').val();
                    product_total = product_total.replace( currency, '' );
                    product_total = product_total.replace( '<?php echo get_option( 'woocommerce_price_thousand_sep' );?>', '' );
                    product_total = product_total.replace( '<?php echo get_option( 'woocommerce_price_decimal_sep' );?>', '.' );
                    product_total = product_total.replace(/[^0-9\.]/g, '' );
                    product_total = product_total* quatity;
                }
                $total_cr = accounting.formatMoney( product_total,{
                    symbol      : currency,
                    decimal     : '<?php echo get_option( 'woocommerce_price_decimal_sep' );?>',
                    thousand    : '<?php echo get_option( 'woocommerce_price_thousand_sep' );?>',
                    precision   : '<?php echo get_option( 'woocommerce_price_num_decimals' );?>',
                    format      : '<?php echo $currency_fm;?>'
                });
                jQuery('#product_total_price .price').html( $total_cr);
            }
            jQuery('form.cart .quantity input.qty').on('keyup', function(){ 
                priceformat();
            });
            jQuery('body').on('change','.variations select',function(){ priceformat(); });
            jQuery('body').on('click','form.cart .quantity #add_ticket, form.cart .quantity #minus_ticket', function(e) {
                priceformat();
            });
            jQuery('.variations input[type=radio]').on('click', function(e) {
                setTimeout(function(){
                    priceformat();
                }, 200);
            });
            priceformat();
            setTimeout(function(){
                priceformat();
            }, 200);
        });
    </script>   