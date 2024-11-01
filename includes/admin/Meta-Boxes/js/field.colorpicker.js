/**
 * ColorPickers
 */

EXC_MB.addCallbackForInit( function() {

	// Colorpicker
	jQuery('input:text.exc_mb_colorpicker').wpColorPicker();

} );

EXC_MB.addCallbackForClonedField( 'EXC_MB_Color_Picker', function( newT ) {

	// Reinitialize colorpickers
    newT.find('.wp-color-result').remove();
	newT.find('input:text.exc_mb_colorpicker').wpColorPicker();

} );