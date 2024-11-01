jQuery(document).ready(function($) {

    /***** Colour picker *****/

    $('.colorpicker').hide();
    $('.colorpicker').each( function() {
        $(this).farbtastic( $(this).closest('.color-picker').find('.color') );
    });

    $('.color').click(function() {
        $(this).closest('.color-picker').find('.colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('.colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });


    /***** Uploading images *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );
        var preview_id = button_id.replace( '_button', '_preview' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
          file_frame.open();
          return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'uploader_title' ),
          button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
          },
          multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          attachment = file_frame.state().get('selection').first().toJSON();
		  //console.log(attachment);
          $("#"+field_id).val(attachment.id);
          if( preview_media ) {
          	$("#"+preview_id).attr('src',attachment.url);
          }
        });

        // Finally, open the modal
        file_frame.open();
    }

    jQuery('.image_upload_button').click(function() {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.image_delete_button').click(function() {
        jQuery(this).closest('td').find( '.image_data_field' ).val( '' );
        jQuery( '.image_preview' ).remove();
        return false;
    });


    /***** Navigation for settings page *****/

    // Make sure each heading has a unique ID.
    jQuery( 'ul#settings-sections.subsubsub' ).find( 'a' ).each( function ( i ) {
        var id_value = jQuery( this ).attr( 'href' ).replace( '#', '' );
        jQuery( 'h2:contains("' + jQuery( this ).text() + '")' ).attr( 'id', id_value ).addClass( 'section-heading' );
    });
	// if load
	// If the link is a tab, show only the specified tab.
	var toShow = jQuery( '#webdwooevents .subsubsub li:first-child a.tab' ).attr( 'href' );
	jQuery( '#webdwooevents .subsubsub li:first-child a.tab' ).addClass( 'current' );
    jQuery( '#webdwooevents .subsubsub li:first-child' ).addClass( 'active' );
	// Remove the first occurance of # from the selected string (will be added manually below).
	toShow = toShow.replace( '#', '');

	jQuery( '#webdwooevents h2, #webdwooevents form > p:not(".submit"), #webdwooevents table' ).hide();
	jQuery( 'h2#' + toShow ).show().nextUntil( 'h2.section-heading', 'p, table, table p' ).show();
    // Create nav links for settings page
    function webd_setting_active($this){
        $this.parents( '.subsubsub' ).find( '.current' ).removeClass( 'current' );
        $this.parents( '.subsubsub' ).find( '.active' ).removeClass( 'active' );
        $this.closest('li').addClass( 'active' );
        $this.addClass( 'current' );
        // If "All" is clicked, show all.
        //if ( jQuery( this ).hasClass( 'all' ) ) {
            //jQuery( '#webdwooevents h2, #webdwooevents form p, #webdwooevents table.form-table, p.submit' ).show();

            //return false;
        //}

        // If the link is a tab, show only the specified tab.
        var toShow = $this.attr( 'href' );
        window.location.hash = toShow;
        // Remove the first occurance of # from the selected string (will be added manually below).
        toShow = toShow.replace( '#', '');//toShow.replace( '#', '', toShow );

        jQuery( '#webdwooevents h2, #webdwooevents form > p:not(".submit"), #webdwooevents table' ).hide();
        jQuery( 'h2#' + toShow ).show().nextUntil( 'h2.section-heading', 'p, table, table p' ).show();
    }
    var hash = window.location.hash;
    if(hash!=''){
        var $this = $('a[href*="'+hash+'"]');
        webd_setting_active($this);
    }
    jQuery( '#webdwooevents .subsubsub a.tab' ).click( function ( e ) {
        // Move the "current" CSS class.
        var $this = jQuery( this );
        webd_setting_active($this);

        return false;
    });
    // Add hash to action
    $("#webdwooevents .submit input").on("click", function(e){
        e.preventDefault();
        var hash = window.location.hash;
        var $action = $('#webdwooevents > form').attr('action')+hash;
        $('#webdwooevents > form').attr('action', $action).submit();
    });
});