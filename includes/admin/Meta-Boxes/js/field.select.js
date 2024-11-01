var exc_mbSelectInit = function() {

	jQuery( '.exc_mb_select' ).each( function() {

		var el = jQuery(this);
 		var fieldID = el.attr( 'data-field-id'); // JS Friendly ID

 		// If fieldID is set
 		// If fieldID options exist
 		// If Element is not hidden template field.
 		// If elemnt has not already been initialized.
 		// Gutenberg remove this code: && el.is( ':visible' )
 		//if ( fieldID && fieldID in window.exc_mb_select_fields && el.is( ':visible' ) && ! el.hasClass( 'select2_351-added' ) ) {
 		if ( fieldID && fieldID in window.exc_mb_select_fields && ! el.hasClass( 'select2_351-added' ) ) {

 			// Get options for this field.
 			options = window.exc_mb_select_fields[fieldID];

			el.addClass( 'select2_351-added' ).select2_351( options );

 		}

	})

};

// Hook this in for all the required fields.
EXC_MB.addCallbackForInit( exc_mbSelectInit );
EXC_MB.addCallbackForClonedField( 'EXC_MB_Select', exc_mbSelectInit );
EXC_MB.addCallbackForClonedField( 'EXC_MB_Post_Select', exc_mbSelectInit );
EXC_MB.addCallbackForClonedField( 'EXC_MB_Taxonomy', exc_mbSelectInit );
