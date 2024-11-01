<?php

if ( ! defined( 'EXC_MB_DEV') )
	define( 'EXC_MB_DEV', false );

if ( ! defined( 'EXC_MB_PATH') )
	define( 'EXC_MB_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'EXC_MB_URL' ) )
	define( 'EXC_MB_URL', plugins_url( '', __FILE__ ) );

include_once( EXC_MB_PATH . '/classes.fields.php' );
include_once( EXC_MB_PATH . '/class.exc_mb-meta-box.php' );

// Make it possible to add fields in locations other than post edit screen.
include_once( EXC_MB_PATH . '/fields-anywhere.php' );

// include_once( EXC_MB_PATH . '/example-functions.php' );

/**
 * Get all the meta boxes on init
 *
 * @return null
 */
function exc_mb_init() {

	if ( ! is_admin() )
		return;

	// Load translations
	$textdomain = 'exc_mb';
	$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );
	load_textdomain( $textdomain, WP_LANG_DIR . '/custom-meta-boxes/' . $textdomain . '-' . $locale . '.mo' );
	$meta_boxes = apply_filters( 'exc_mb_meta_boxes', array() );
	if ( ! empty( $meta_boxes ) )
		foreach ( $meta_boxes as $meta_box )
			new EXC_MB_Meta_Box( $meta_box );

}
add_action( 'init', 'exc_mb_init', 50 );

/**
 * Return an array of built in available fields
 *
 * Key is field name, Value is class used by field.
 * Available fields can be modified using the 'exc_mb_field_types' filter.
 *
 * @return array
 */
function _exc_mb_available_fields() {

	return apply_filters( 'exc_mb_field_types', array(
		'text'				=> 'EXC_MB_Text_Field',
		'text_small' 		=> 'EXC_MB_Text_Small_Field',
		'text_url'			=> 'EXC_MB_URL_Field',
		'url'				=> 'EXC_MB_URL_Field',
		'radio'				=> 'EXC_MB_Radio_Field',
		'checkbox'			=> 'EXC_MB_Checkbox',
		'file'				=> 'EXC_MB_File_Field',
		'image' 			=> 'EXC_MB_Image_Field',
		'wysiwyg'			=> 'EXC_MB_wysiwyg',
		'textarea'			=> 'EXC_MB_Textarea_Field',
		'textarea_code'		=> 'EXC_MB_Textarea_Field_Code',
		'select'			=> 'EXC_MB_Select',
		'taxonomy_select'	=> 'EXC_MB_Taxonomy',
		'post_select'		=> 'EXC_MB_Post_Select',
		'date'				=> 'EXC_MB_Date_Field',
		'date_unix'			=> 'EXC_MB_Date_Timestamp_Field',
		'datetime_unix'		=> 'EXC_MB_Datetime_Timestamp_Field',
		'time'				=> 'EXC_MB_Time_Field',
		'colorpicker'		=> 'EXC_MB_Color_Picker',
		'title'				=> 'EXC_MB_Title',
		'group'				=> 'EXC_MB_Group_Field',
		'gmap'				=> 'EXC_MB_Gmap_Field',
		'number'			=> 'EXC_MB_Number_Field'
	) );

}

/**
 * Get a field class by type
 *
 * @param  string $type
 * @return string $class, or false if not found.
 */
function _exc_mb_field_class_for_type( $type ) {

	$map = _exc_mb_available_fields();

	if ( isset( $map[$type] ) )
		return $map[$type];

	return false;

}

/**
 * For the order of repeatable fields to be guaranteed, orderby meta_id needs to be set.
 * Note usermeta has a different meta_id column name.
 *
 *
 * @param  string $query
 * @return string $query
 */
function exc_mb_fix_meta_query_order($query) {

    $pattern = '/^SELECT (post_id|user_id), meta_key, meta_value FROM \w* WHERE post_id IN \([\d|,]*\)$/';

    if (
            0 === strpos( $query, "SELECT post_id, meta_key, meta_value" ) &&
            preg_match( $pattern, $query, $matches )
    ) {

            if ( isset( $matches[1] ) && 'user_id' == $matches[1] )
                    $meta_id_column = 'umeta_id';
            else
                    $meta_id_column = 'meta_id';

            $meta_query_orderby = ' ORDER BY ' . $meta_id_column;

            if ( false === strpos( $query, $meta_query_orderby ) )
                    $query .= $meta_query_orderby;

    }

    return $query;

}

if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) )
	add_filter( 'query', 'exc_mb_fix_meta_query_order', 1 );