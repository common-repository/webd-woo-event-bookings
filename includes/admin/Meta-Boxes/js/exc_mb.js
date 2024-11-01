/**
 * Custom jQuery for Custom Metaboxes and Fields
 */

/*jslint browser: true, devel: true, indent: 4, maxerr: 50, sub: true */
/*global jQuery, tb_show, tb_remove */

'use strict';

var EXC_MB = {

	_initCallbacks: [],
	_clonedFieldCallbacks: [],
	_deletedFieldCallbacks: [],

	_sortStartCallbacks: [],
	_sortEndCallbacks: [],

	init : function() {

		jQuery( '.field.repeatable' ).each( function() {
			EXC_MB.isMaxFields( jQuery(this) );
		} );

		// Unbind & Re-bind all EXC_MB events to prevent duplicates.
		jQuery(document).unbind( 'click.EXC_MB' );
		jQuery(document).on( 'click.EXC_MB', '.exc_mb-delete-field', EXC_MB.deleteField );
		jQuery(document).on( 'click.EXC_MB', '.repeat-field', EXC_MB.repeatField );

		// When toggling the display of the meta box container - reinitialize
		jQuery(document).on( 'click.EXC_MB', '.postbox h3, .postbox .handlediv', EXC_MB.init );

		EXC_MB.doneInit();

		jQuery('.field.exc_mb-sortable' ).each( function() {
			EXC_MB.sortableInit( jQuery(this) );
		} );


	},

	repeatField : function( e ) {

		var templateField, newT, field, index, attr;

		field = jQuery( this ).closest('.field' );

		e.preventDefault();
		jQuery(this).blur();

		if ( EXC_MB.isMaxFields( field, 1 ) )
			return;

		templateField = field.children( '.field-item.hidden' );

		newT = templateField.clone();
		newT.removeClass( 'hidden' );

		var excludeInputTypes = '[type=submit],[type=button],[type=checkbox],[type=radio],[readonly]';
		newT.find( 'input' ).not( excludeInputTypes ).val( '' );

		newT.find( '.exc_mb_upload_status' ).html('');

		newT.insertBefore( templateField );

		 // Recalculate group ids & update the name fields..
		index = 0;
		attr  = ['id','name','for','data-id','data-name'];

		field.children( '.field-item' ).not( templateField ).each( function() {

			var search  = field.hasClass( 'EXC_MB_Group_Field' ) ? /exc_mb-group-(\d|x)*/g : /exc_mb-field-(\d|x)*/g;
			var replace = field.hasClass( 'EXC_MB_Group_Field' ) ? 'exc_mb-group-' + index : 'exc_mb-field-' + index;

			jQuery(this).find( '[' + attr.join('],[') + ']' ).each( function() {

				for ( var i = 0; i < attr.length; i++ )
					if ( typeof( jQuery(this).attr( attr[i] ) ) !== 'undefined' )
						jQuery(this).attr( attr[i], jQuery(this).attr( attr[i] ).replace( search, replace ) );

			} );

			index += 1;

		} );

		EXC_MB.clonedField( newT );

		if ( field.hasClass( 'exc_mb-sortable' ) )
			EXC_MB.sortableInit( field );


	},

	deleteField : function( e ) {
		var $this = jQuery(this);
		var fieldItem, field;

		e.preventDefault();
		$this.blur();

		if ( ! confirm( EXC_MBData.strings.confirmDeleteField ) ) {
			return;
		}
		if($this.data('date') && $this.data('date')!=''){
			var param = {
				action: 'webd_remove_event_from_recurr',
				date: jQuery(this).data('date'),
				id : jQuery(this).data('id')
			};
			jQuery('#wpbody-content').addClass('loading');
			if(!jQuery('.webd_ctdate .wpex-loading').length){
				jQuery('.webd_ctdate').prepend('<div class="wpex-loading"><div class="wpex-spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div></div>');
			}
			var ajax_url = $this.data('url');
			jQuery.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'html',
				data: (param),
				success: function(data){
					//alert(data);
					jQuery('#wpbody-content').removeClass('loading');
					if(data != '0'){
					}else{}
					
					fieldItem = $this.closest('.field-item' );
					field     = fieldItem.closest( '.field' );
			
					EXC_MB.isMaxFields( field, -1 );
					EXC_MB.deletedField( fieldItem );
			
					fieldItem.fadeOut(300, function() {fieldItem.remove();});
				}
			});
		}else{
			fieldItem = $this.closest('.field-item' );
			field     = fieldItem.closest( '.field' );
	
			EXC_MB.isMaxFields( field, -1 );
			EXC_MB.deletedField( fieldItem );
	
			fieldItem.fadeOut(300, function() {fieldItem.remove();});
		}

	},

	/**
	 * Prevent having more than the maximum number of repeatable fields.
	 * When called, if there is the maximum, disable .repeat-field button.
	 * Note: Information Passed using data-max attribute on the .field element.
	 *
	 * @param jQuery .field
	 * @param int modifier - adjust count by this ammount. 1 If adding a field, 0 if checking, -1 if removing a field... etc
	 * @return null
	 */
	isMaxFields: function( field, modifier ) {

		var count, addBtn, min, max, count;

		modifier = (modifier) ? parseInt( modifier, 10 ) : 0;

		addBtn = field.children( '.repeat-field' );
		count  = field.children('.field-item').not('.hidden').length + modifier; // Count after anticipated action (modifier)
		max    = field.attr( 'data-rep-max' );

		// Show all the remove field buttons.
		field.find( '> .field-item > .exc_mb-delete-field, > .field-item > .group > .exc_mb-delete-field' ).show();

		if ( typeof( max ) === 'undefined' )
			return false;

		// Disable the add new field button?
		if ( count >= parseInt( max, 10 ) )
			addBtn.attr( 'disabled', 'disabled' );
		else
			addBtn.removeAttr( 'disabled' );

		if ( count > parseInt( max, 10 ) )
			return true;

	},

	addCallbackForInit: function( callback ) {

		this._initCallbacks.push( callback )

	},

	/**
	 * Fire init callbacks.
	 * Called when EXC_MB has been set up.
	 */
	doneInit: function() {

		var _this = this,
			callbacks = EXC_MB._initCallbacks;

		if ( callbacks ) {
			for ( var a = 0; a < callbacks.length; a++) {
				callbacks[a]();
			}
		}

	},

	addCallbackForClonedField: function( fieldName, callback ) {

		if ( jQuery.isArray( fieldName ) )
			for ( var i = 0; i < fieldName.length; i++ )
				EXC_MB.addCallbackForClonedField( fieldName[i], callback );

		this._clonedFieldCallbacks[fieldName] = this._clonedFieldCallbacks[fieldName] ? this._clonedFieldCallbacks[fieldName] : []
		this._clonedFieldCallbacks[fieldName].push( callback )

	},

	/**
	 * Fire clonedField callbacks.
	 * Called when a field has been cloned.
	 */
	clonedField: function( el ) {

		// also check child elements
		el.add( el.find( 'div[data-class]' ) ).each( function( i, el ) {

			el = jQuery( el )
			var callbacks = EXC_MB._clonedFieldCallbacks[el.attr( 'data-class') ]

			if ( callbacks )
				for ( var a = 0; a < callbacks.length; a++ )
					callbacks[a]( el );

		})
	},

	addCallbackForDeletedField: function( fieldName, callback ) {

		if ( jQuery.isArray( fieldName ) )
			for ( var i = 0; i < fieldName.length; i++ )
				EXC_MB._deletedFieldCallbacks( fieldName[i], callback );

		this._deletedFieldCallbacks[fieldName] = this._deletedFieldCallbacks[fieldName] ? this._deletedFieldCallbacks[fieldName] : []
		this._deletedFieldCallbacks[fieldName].push( callback )

	},

	/**
	 * Fire deletedField callbacks.
	 * Called when a field has been cloned.
	 */
	deletedField: function( el ) {

		// also check child elements
		el.add( el.find( 'div[data-class]' ) ).each( function(i, el) {

			el = jQuery( el )
			var callbacks = EXC_MB._deletedFieldCallbacks[el.attr( 'data-class') ]

			if ( callbacks )
				for ( var a = 0; a < callbacks.length; a++ )
					callbacks[a]( el )

		})
	},

	sortableInit : function( field ) {

		var items = field.find(' > .field-item').not('.hidden');

		field.find( '> .field-item > .exc_mb-handle' ).remove();

		items.each( function() {
			jQuery(this).append( '<div class="exc_mb-handle"></div>' );
		} );

		field.sortable( {
			handle: "> .exc_mb-handle" ,
			cursor: "move",
			items: " > .field-item",
			beforeStop: function( event, ui ) { EXC_MB.sortStart( jQuery( ui.item[0] ) ); },
			deactivate: function( event, ui ) { EXC_MB.sortEnd( jQuery( ui.item[0] ) ); },
		} );

	},

	sortStart : function ( el ) {

		// also check child elements
		el.add( el.find( 'div[data-class]' ) ).each( function(i, el) {

			el = jQuery( el )
			var callbacks = EXC_MB._sortStartCallbacks[el.attr( 'data-class') ]

			if ( callbacks )
				for ( var a = 0; a < callbacks.length; a++ )
					callbacks[a]( el )

		})

	},

	addCallbackForSortStart: function( fieldName, callback ) {

		if ( jQuery.isArray( fieldName ) )
			for ( var i = 0; i < fieldName.length; i++ )
				EXC_MB.addCallbackForSortStart( fieldName[i], callback );

		this._sortStartCallbacks[fieldName] = this._sortStartCallbacks[fieldName] ? this._sortStartCallbacks[fieldName] : []
		this._sortStartCallbacks[fieldName].push( callback )

	},

	sortEnd : function ( el ) {

		// also check child elements
		el.add( el.find( 'div[data-class]' ) ).each( function(i, el) {

			el = jQuery( el )
			var callbacks = EXC_MB._sortEndCallbacks[el.attr( 'data-class') ]

			if ( callbacks )
				for ( var a = 0; a < callbacks.length; a++ )
					callbacks[a]( el )

		})

	},

	addCallbackForSortEnd: function( fieldName, callback ) {

		if ( jQuery.isArray( fieldName ) )
			for ( var i = 0; i < fieldName.length; i++ )
				EXC_MB.addCallbackForSortEnd( fieldName[i], callback );

		this._sortEndCallbacks[fieldName] = this._sortEndCallbacks[fieldName] ? this._sortEndCallbacks[fieldName] : []
		this._sortEndCallbacks[fieldName].push( callback )

	}

}

jQuery(document).ready( function() {

	EXC_MB.init();

});
