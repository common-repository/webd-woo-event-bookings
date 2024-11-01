<?php

/**
 * Abstract class for all fields.
 * Subclasses need only override html()
 *
 * @abstract
 */
abstract class EXC_MB_Field {

	public $value;
	public $field_index = 0;

	public function __construct( $name, $title, array $values, $args = array() ) {

		$this->id 		= $name;
		$this->name		= $name . '[]';
		$this->title 	= $title;
		$this->args  = wp_parse_args( $args, $this->get_default_args() );

		// Deprecated argument: 'std'
		if ( ! empty( $this->args['std'] ) && empty( $this->args['default'] ) ) {
			$this->args['default'] = $this->args['std'];
			_deprecated_argument( 'EXC_MB_Field', '0.9', "field argument 'std' is deprecated, use 'default' instead" );
		}

		if ( ! empty( $this->args['options'] ) && is_array( reset( $this->args['options'] ) ) ) {
			$re_format = array();
			foreach ( $this->args['options'] as $option ) {
				$re_format[$option['value']] = $option['name'];
			}
			$this->args['options'] = $re_format;
		}

		// If the field has a custom value populator callback
		if ( ! empty( $args['values_callback'] ) )
			$this->values = call_user_func( $args['values_callback'], get_the_id() );
		else
			$this->values = $values;

		$this->value = reset( $this->values );

	}

	/**
	 * Get the default args for the abstract field.
	 * These args are available to all fields.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return apply_filters(
			'exc_mb_field_default_args',
			array(
				'desc'                => '',
				'repeatable'          => false,
				'sortable'            => false,
				'repeatable_max'      => null,
				'show_label'          => false,
				'readonly'            => false,
				'disabled'            => false,
				'default'             => '',
				'cols'                => '12',
				'style'               => '',
				'class'               => '',
				'data_delegate'       => null,
				'save_callback'       => null,
				'string-repeat-field' => __( 'Add New', 'exc_mb' ),
				'string-delete-field' => __( 'Remove Field', 'exc_mb' ),
			),
			get_class( $this )
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		if ( isset( $this->args['sortable'] ) && $this->args['sortable'] )
			wp_enqueue_script( 'jquery-ui-sortable' );

	}

	/**
	 * Enqueue all styles required by the field.
	 *
	 * @uses wp_enqueue_style()
	 */
	public function enqueue_styles() {}

	/**
	 * Output the field input ID attribute.
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @param  string $append
	 * @return null
	 */
	public function id_attr( $append = null ) {

		printf( 'id="%s"', esc_attr( $this->get_the_id_attr( $append ) ) );

	}

	/**
	 * Output the for attribute for the field.
	 *
	 *
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @param  string $append
	 * @return null
	 */
	public function get_the_id_attr( $append = null ) {

		$id = $this->id;

		if ( isset( $this->parent ) ) {
			$parent_id = preg_replace( '/exc_mb\-field\-(\d+|x)/', 'exc_mb-group-$1', $this->parent->get_the_id_attr() );
			$id = $parent_id . '[' . $id . ']';
		}

		$id .= '-exc_mb-field-' . $this->field_index;

		if ( ! is_null( $append ) )
			$id .= '-' . $append;

		$id = str_replace( array( '[', ']', '--' ), '-', $id );

		return $id;

	}

	/**
	 * Return the field input ID attribute value.
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @param  string $append
	 * @return string id attribute value.
	 */
	public function for_attr( $append = null ) {

		printf( 'for="%s"', esc_attr( $this->get_the_id_attr( $append ) ) );

	}

	public function name_attr( $append = null ) {

		printf( 'name="%s"', esc_attr( $this->get_the_name_attr( $append ) ) );

	}

	public function get_the_name_attr( $append = null ) {

		$name = str_replace( '[]', '', $this->name );

		if ( isset( $this->parent ) ) {
			$parent_name = preg_replace( '/exc_mb\-field\-(\d+|x)/', 'exc_mb-group-$1', $this->parent->get_the_name_attr() );
			$name = $parent_name . '[' . $name . ']';
		}

		$name .= "[exc_mb-field-$this->field_index]";

		if ( ! is_null( $append ) )
			$name .= $append;

		return $name;

	}

	public function class_attr( $classes = '' ) {

		if ( $classes = implode( ' ', array_map( 'sanitize_html_class', array_filter( array_unique( explode( ' ', $classes . ' ' . $this->args['class'] ) ) ) ) ) ) { ?>

			class="<?php echo esc_attr( $classes ); ?>"

		<?php }

	}

	/**
	 * Get JS Safe ID.
	 *
	 * For use as a unique field identifier in javascript.
	 */
	public function get_js_id() {

		return str_replace( array( '-', '[', ']', '--' ),'_', $this->get_the_id_attr() ); // JS friendly ID

	}

	public function boolean_attr( $attrs = array() ) {

		if ( $this->args['readonly'] )
			$attrs[] = 'readonly';

		if ( $this->args['disabled'] )
			$attrs[] = 'disabled';

		$attrs = array_filter( array_unique( $attrs ) );

		foreach ( $attrs as $attr )
			echo esc_html( $attr ) . '="' . esc_attr( $attr ) . '"';

	}

	/**
	 * Check if this field has a data delegate set
	 *
	 * @return boolean
	 */
	public function has_data_delegate() {
		return (bool) $this->args['data_delegate'];
	}

	/**
	 * Get the array of data from the data delegate
	 *
	 * @return array mixed
	 */
	protected function get_delegate_data() {

		if ( $this->args['data_delegate'] )
			return call_user_func_array( $this->args['data_delegate'], array( $this ) );

		return array();

	}

	public function get_value() {
	   return ( $this->value || $this->value === '0' ) ? $this->value : $this->args['default'];
	}

	public function &get_values() {
		return $this->values;
	}

	public function set_values( array $values ) {

		$this->values = $values;

		unset( $this->value );

	}

	public function parwebd_save_values() {}

	public function parwebd_save_value() {}

	/**
	 * @todo this surely only works for posts
	 * @todo why do values need to be passed in, they can already be passed in on construct
	 */
	public function save( $post_id, $values ) {

		// Don't save readonly values.
		if ( $this->args['readonly'] )
			return;

		$this->values = $values;
		$this->parwebd_save_values();

		// Allow override from args
		if ( ! empty( $this->args['save_callback'] ) ) {

			call_user_func( $this->args['save_callback'], $this->values, $post_id );

			return;

		}

		// If we are not on a post edit screen
		if ( ! $post_id )
			return;

		delete_post_meta( $post_id, $this->id );

		foreach( $this->values as $v ) {

			$this->value = $v;
			$this->parwebd_save_value();

			if ( $this->value || $this->value === '0' )
				add_post_meta( $post_id, $this->id, $this->value );

		}
	}

	public function title() {

		if ( $this->title ) { ?>

			<div class="field-title">
				<label <?php $this->for_attr(); ?>>
					<?php echo esc_html( $this->title ); ?>
				</label>
			</div>

		<?php }

	}

	public function description() {

		if ( ! empty( $this->args['desc'] ) ) { ?>

			<div class="exc_mb_metabox_description">
				<?php echo wp_kses_post( $this->args['desc'] ); ?>
			</div>

		<?php }

	}

	public function display() {

		// If there are no values and it's not repeateble, we want to do one with empty string
		if ( ! $this->get_values() && ! $this->args['repeatable'] )
			$values = array( '' );
		else
			$values = $this->get_values();

		$this->title();

		$this->description();

		$i = 0;
		if( isset( $this->args['type'] ) && $this->args['type'] == 'gmap' ) {
			$values = array( $values );
		}
		foreach ( $values as $key => $value ) {

			$this->field_index = $i;
			$this->value = $value; ?>

			<div class="field-item" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="position: relative; <?php echo esc_attr( $this->args['style'] ); ?>">

			<?php if ( $this->args['repeatable'] ) : ?>
				<button class="exc_mb-delete-field" title="<?php echo esc_attr( $this->args['string-delete-field'] ); ?>">
					<span class="exc_mb-delete-field-icon">&times;</span>
				</button>
			<?php endif; ?>

			<?php $this->html(); ?>

			</div>

		<?php

			$i++;

		}

		// Insert a hidden one if it's repeatable
		if ( $this->args['repeatable'] ) {

			$this->field_index = 'x'; // x used to distinguish hidden fields.
			$this->value = ''; ?>

			<div class="field-item hidden" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="position: relative; <?php echo esc_attr( $this->args['style'] ); ?>">

			<?php if ( $this->args['repeatable'] ) : ?>
				<button class="exc_mb-delete-field" title="<?php echo esc_attr( $this->args['string-delete-field'] ); ?>">
					<span class="exc_mb-delete-field-icon">&times;</span>
					<?php echo esc_html( $this->args['string-delete-field'] ); ?>
				</button>
			<?php endif; ?>

			<?php $this->html(); ?>

			</div>

			<button class="button repeat-field"><?php echo esc_html( $this->args['string-repeat-field'] ); ?></button>

		<?php }

	}

}

/**
 * Standard text field.
 *
 * @extends EXC_MB_Field
 */
class EXC_MB_Text_Field extends EXC_MB_Field {

	public function html() { ?>

		<input type="text" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

	<?php }
}

class EXC_MB_Text_Small_Field extends EXC_MB_Text_Field {

	public function html() {

		$this->args['class'] .= ' exc_mb_text_small';

		parent::html();

	}
}

/**
 * Field for image upload / file updoad.
 *
 * @todo ability to set image size (preview image) from caller
 */
class EXC_MB_File_Field extends EXC_MB_Field {

	/**
	 * Return the default args for the File field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'library-type' => array( 'video', 'audio', 'text', 'application' )
			)
		);
	}

	function enqueue_scripts() {

		global $post_ID;
		$post_ID = isset($post_ID) ? (int) $post_ID : 0;

		parent::enqueue_scripts();

		wp_enqueue_media( array( 'post' => $post_ID ));
		wp_enqueue_script( 'exc_mb-file-upload', trailingslashit( EXC_MB_URL ) . 'js/file-upload.js', array( 'jquery', 'exc_mb-scripts' ) );

	}

	public function html() {

		if ( $this->get_value() ) {
			$src = wp_mime_type_icon( $this->get_value() );
			$size = getimagesize( str_replace( site_url(), ABSPATH, $src ) );
			$icon_img = '<img src="' . $src . '" ' . $size[3] . ' />';
		}

		$data_type = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="exc_mb-file-wrap" <?php echo 'data-type="' . esc_attr( $data_type ) . '"'; ?>>

			<div class="exc_mb-file-wrap-placeholder"></div>

			<button class="button exc_mb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>">
				<?php esc_html_e( 'Add File', 'exc_mb' ); ?>
			</button>

			<div class="exc_mb-file-holder type-file <?php echo $this->get_value() ? '' : 'hidden'; ?>">

				<?php if ( $this->get_value() ) : ?>

					<?php if ( isset( $icon_img ) ) echo $icon_img; ?>

					<div class="exc_mb-file-name">
						<strong><?php echo esc_html( basename( get_attached_file( $this->get_value() ) ) ); ?></strong>
					</div>

				<?php endif; ?>

			</div>

			<button class="exc_mb-remove-file button <?php echo $this->get_value() ? '' : 'hidden'; ?>">
				<?php esc_html_e( 'Remove', 'exc_mb' ); ?>
			</button>

			<input type="hidden"
				<?php $this->class_attr( 'exc_mb-file-upload-input' ); ?>
				<?php $this->name_attr(); ?>
				value="<?php echo esc_attr( $this->value ); ?>"
			/>

		</div>

	<?php }

}

class EXC_MB_Image_Field extends EXC_MB_File_Field {

	/**
	 * Return the default args for the Image field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
			'size' => 'thumbnail',
			'library-type' => array( 'image' ),
			'show_size' => false
			)
		);
	}

	public function html() {

		if ( $this->get_value() )
			$image = wp_get_attachment_image_src( $this->get_value(), $this->args['size'], true );

		// Convert size arg to array of width, height, crop
		$size = $this->parwebd_image_size( $this->args['size'] );

		// Inline styles
		$styles              = sprintf( 'width: %1$dpx; height: %2$dpx; line-height: %2$dpx', intval( $size['width'] ), intval( $size['height'] ) );
		$placeholder_styles  = sprintf( 'width: %dpx; height: %dpx;', intval( $size['width'] ) - 8, intval( $size['height'] ) - 8 );

		$data_type           = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="exc_mb-file-wrap" style="<?php echo esc_attr( $styles ); ?>" data-type="<?php echo esc_attr( $data_type ); ?>">

			<div class="exc_mb-file-wrap-placeholder" style="<?php echo esc_attr( $placeholder_styles ); ?>">

				<?php if ( $this->args['show_size'] ) : ?>
					<span class="dimensions">
						<?php printf( '%dpx &times; %dpx', intval( $size['width'] ), intval( $size['height'] ) ); ?>
					</span>
				<?php endif; ?>

			</div>

			<button class="button exc_mb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>" data-nonce="<?php echo wp_create_nonce( 'exc_mb-file-upload-nonce' ); ?>">
				<?php esc_html_e( 'Add Image', 'exc_mb' ); ?>
			</button>

			<div class="exc_mb-file-holder type-img <?php echo $this->get_value() ? '' : 'hidden'; ?>" data-crop="<?php echo (bool) $size['crop']; ?>">

				<?php if ( ! empty( $image ) ) : ?>
					<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo intval( $image[1] ); ?>" height="<?php echo intval( $image[2] ); ?>" />
				<?php endif; ?>

			</div>

			<button class="exc_mb-remove-file button <?php echo $this->get_value() ? '' : 'hidden'; ?>">
				<?php esc_html_e( 'Remove', 'exc_mb' ); ?>
			</button>

			<input type="hidden"
				<?php $this->class_attr( 'exc_mb-file-upload-input' ); ?>
				<?php $this->name_attr(); ?>
				value="<?php echo esc_attr( $this->value ); ?>"
			/>

		</div>

	<?php }

	/**
	 * Parse the size argument to get pixel width, pixel height and crop information.
	 *
	 * @param  string $size
	 * @return array width, height, crop
	 */
	private function parwebd_image_size( $size ) {

		// Handle string for built-in image sizes
		if ( is_string( $size ) && in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			return array(
				'width'  => get_option( $size . '_size_w' ),
				'height' => get_option( $size . '_size_h' ),
				'crop'   => get_option( $size . '_crop' )
			);
		}

		// Handle string for additional image sizes
		global $_wp_additional_image_sizes;
		if ( is_string( $size ) && isset( $_wp_additional_image_sizes[$size] ) ) {
			return array(
				'width'  => $_wp_additional_image_sizes[$size]['width'],
				'height' => $_wp_additional_image_sizes[$size]['height'],
				'crop'   => $_wp_additional_image_sizes[$size]['crop']
			);
		}

		// Handle default WP size format.
		if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) )
			$size = array( 'width' => $size[0], 'height' => $size[1] );

		return wp_parse_args( $size, array(
			'width'  => get_option( 'thumbnail_size_w' ),
			'height' => get_option( 'thumbnail_size_h' ),
			'crop'   => get_option( 'thumbnail_crop' )
		) );

	}

	/**
	 * Ajax callback for outputing an image src based on post data.
	 *
	 * @return null
	 */
	static function request_image_ajax_callback() {

		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'exc_mb-file-upload-nonce' ) ) )
			return;

		$id = intval( $_POST['id'] );

		$size = array(
			intval( $_POST['width'] ),
			intval( $_POST['height'] ),
			'crop' => (bool) $_POST['crop']
		);

		$image = wp_get_attachment_image_src( $id, $size );
		echo reset( $image );

		die(); // this is required to return a proper result
	}

}
add_action( 'wp_ajax_exc_mb_request_image', array( 'EXC_MB_Image_Field', 'request_image_ajax_callback' ) );

/**
 * Standard text meta box for a URL.
 *
 */
class EXC_MB_Number_Field extends EXC_MB_Field {

	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'step' => '',
			)
		);
	}

	public function html() { ?>

		<input step="<?php echo esc_attr( $this->args['step'] ); ?>" type="number" min="1" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_number code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value )!='' ? esc_attr( $this->value ) : 1; ?>" />

	<?php }
}

/**
 * Standard text meta box for a URL.
 *
 */
class EXC_MB_URL_Field extends EXC_MB_Field {

	public function html() { ?>

		<input type="text" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_url code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( esc_url( $this->value ) ); ?>" />

	<?php }
}

/**
 * Date picker box.
 *
 */
class EXC_MB_Date_Field extends EXC_MB_Field {

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'exc_mb-jquery-ui', trailingslashit( EXC_MB_URL ) . 'css/select-js-css/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'exc_mb-datetime', trailingslashit( EXC_MB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'exc_mb-scripts' ) );
	}

	public function html() { ?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_small exc_mb_datepicker' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value ); ?>" />

	<?php }
}

class EXC_MB_Time_Field extends EXC_MB_Field {

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'exc_mb-jquery-ui', trailingslashit( EXC_MB_URL ) . 'css/select-js-css/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'exc_mb-timepicker', trailingslashit( EXC_MB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'exc_mb-scripts' ) );
		wp_enqueue_script( 'exc_mb-datetime', trailingslashit( EXC_MB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'exc_mb-scripts' ) );
	}

	public function html() { ?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_small exc_mb_timepicker' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value ); ?>"/>

	<?php }

}

/**
 * Date picker for date only (not time) box.
 *
 */
class EXC_MB_Date_Timestamp_Field extends EXC_MB_Field {

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'exc_mb-jquery-ui', trailingslashit( EXC_MB_URL ) . 'css/select-js-css/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'exc_mb-timepicker', trailingslashit( EXC_MB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'exc_mb-scripts' ) );
		wp_enqueue_script( 'exc_mb-datetime', trailingslashit( EXC_MB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'exc_mb-scripts' ) );

	}

	public function html() {
		$dfm = 'm\/d\/Y';
		$tfm = 'h:i A';
		$stdfm = get_option('webd_date_picker');
		if($stdfm=='dmy'){
			$dfm = 'd\.m\.Y';
			$tfm = 'H:i';
		}?>

		<input <?php $this->id_attr(); ?> data-format="<?php echo esc_attr($stdfm) ?>" <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_small exc_mb_datepicker' ); ?> type="text" <?php $this->name_attr(); ?>  value="<?php echo $this->value ? esc_attr( date( $dfm, $this->value ) ) : '' ?>" />

	<?php }

	public function parwebd_save_values() {

		foreach( $this->values as &$value )
			$stdfm = get_option('webd_date_picker');
			if($stdfm=='dmy'){
				$value = str_replace(".","-",$value);
				$value = str_replace("/","-",$value);
			}
			$value = strtotime( $value );

		sort( $this->values );

	}

}

/**
 * Date picker for date and time (seperate fields) box.
 *
 */
class EXC_MB_Datetime_Timestamp_Field extends EXC_MB_Field {

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'exc_mb-jquery-ui', trailingslashit( EXC_MB_URL ) . 'css/select-js-css/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'exc_mb-timepicker', trailingslashit( EXC_MB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'exc_mb-scripts' ) );
		wp_enqueue_script( 'exc_mb-datetime', trailingslashit( EXC_MB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'exc_mb-scripts' ) );
	}

	public function html() { 
		$dfm = 'm\/d\/Y';
		$tfm = 'h:i A';
		$stdfm = get_option('webd_date_picker');
		if($stdfm=='dmy'){
			$dfm = 'd\.m\.Y';
			$tfm = 'H:i';
		}?>

		<input <?php $this->id_attr('date'); ?> data-format="<?php echo esc_attr($stdfm) ?>" <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_small exc_mb_datepicker' ); ?> type="text" <?php $this->name_attr( '[date]' ); ?>  value="<?php echo $this->value ? esc_attr( date( $dfm, $this->value ) ) : '' ?>" />
		<input <?php $this->id_attr('time'); ?> data-format="<?php echo esc_attr($stdfm) ?>" <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_text_small exc_mb_timepicker' ); ?> type="text" <?php $this->name_attr( '[time]' ); ?> value="<?php echo $this->value ? esc_attr( date( $tfm, $this->value ) ) : '' ?>" />

	<?php }

	public function parwebd_save_values() {

		// Convert all [date] and [time] values to a unix timestamp.
		// If date is empty, assume delete. If time is empty, assume 00:00.
		foreach( $this->values as $key => &$value ) {
			//European date format
			//$value['date'] = explode("/",$value['date']);
			//$value['date'] = implode(".",$value['date']);
			$stdfm = get_option('webd_date_picker');
			if($stdfm=='dmy'){
				$value['date'] = str_replace(".","-",$value['date']);
				$value['date'] = str_replace("/","-",$value['date']);
			}
			if ( empty( $value['date'] ) )
				unset( $this->values[$key] );
			else
				$value = strtotime( $value['date'] . ' ' . $value['time'] );
		}
		//echo $value;exit;
		$this->values = array_filter( $this->values );
		sort( $this->values );

		parent::parwebd_save_values();

	}

}

/**
 * Standard text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 */
class EXC_MB_Textarea_Field extends EXC_MB_Field {

	public function html() { ?>

		<textarea <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> rows="<?php echo ! empty( $this->args['rows'] ) ? esc_attr( $this->args['rows'] ) : 4; ?>" <?php $this->name_attr(); ?>><?php echo esc_html( $this->value ); ?></textarea>

	<?php }

}

/**
 * Code style text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 */
class EXC_MB_Textarea_Field_Code extends EXC_MB_Textarea_Field {

	public function html() {

		$this->args['class'] .= ' code';

		parent::html();

	}

}

/**
 *  Colour picker
 *
 */
class EXC_MB_Color_Picker extends EXC_MB_Field {

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'exc_mb-colorpicker', trailingslashit( EXC_MB_URL ) . 'js/field.colorpicker.js', array( 'jquery', 'wp-color-picker', 'exc_mb-scripts' ) );
		wp_enqueue_style( 'wp-color-picker' );
	}

	public function html() { ?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'exc_mb_colorpicker exc_mb_text_small' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

	<?php }

}

/**
 * Standard radio field.
 *
 * Args:
 *  - bool "inline" - display the radio buttons inline
 */
class EXC_MB_Radio_Field extends EXC_MB_Field {

	/**
	 * Return the default args for the Radio input field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'options' => array(),
			)
		);
	}

	public function html() {

		if ( $this->has_data_delegate() )
			$this->args['options'] = $this->get_delegate_data(); ?>

			<?php foreach ( $this->args['options'] as $key => $value ): ?>

			<input <?php $this->id_attr( 'item-' . $key ); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> type="radio" <?php $this->name_attr(); ?>  value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $this->get_value() ); ?> />
			<label <?php $this->for_attr( 'item-' . $key ); ?> style="margin-right: 20px;">
				<?php echo esc_html( $value ); ?>
			</label>

			<?php endforeach; ?>

	<?php }

}

/**
 * Standard checkbox field.
 *
 */
class EXC_MB_Checkbox extends EXC_MB_Field {

	public function title() {}

	public function html() { ?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> type="checkbox" <?php $this->name_attr(); ?>  value="1" <?php checked( $this->get_value() ); ?> />
		<label <?php $this->for_attr(); ?>><?php echo esc_html( $this->title ); ?></label>

	<?php }

}


/**
 * Standard title used as a splitter.
 *
 */
class EXC_MB_Title extends EXC_MB_Field {

	public function title() {
		?>

		<div class="field-title">
			<h2 <?php $this->class_attr(); ?>>
				<?php echo esc_html( $this->title ); ?>
			</h2>
		</div>

		<?php

	}

	public function html() {}

}

/**
 * wysiwyg field.
 *
 */
class EXC_MB_wysiwyg extends EXC_MB_Field {

	/**
	 * Return the default args for the WYSIWYG field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'options' => array(),
			)
		);
	}

	function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'exc_mb-wysiwyg', trailingslashit( EXC_MB_URL ) . 'js/field-wysiwyg.js', array( 'jquery', 'exc_mb-scripts' ) );
	}

	public function html() {

		$id   = $this->get_the_id_attr();
		$name = $this->get_the_name_attr();

		$field_id = $this->get_js_id();

		printf( '<div class="exc_mb-wysiwyg" data-id="%s" data-name="%s" data-field-id="%s">', $id, $name, $field_id );

		if ( $this->is_placeholder() ) 	{

			// For placeholder, output the markup for the editor in a JS var.
			ob_start();
			$this->args['options']['textarea_name'] = 'exc_mb-placeholder-name-' . $field_id;
			wp_editor( '', 'exc_mb-placeholder-id-' . $field_id, $this->args['options'] );
			$editor = ob_get_clean();
			$editor = str_replace( array( "\n", "\r" ), "", $editor );
			$editor = str_replace( array( "'" ), '"', $editor );

			?>

			<script>
				if ( 'undefined' === typeof( exc_mb_wysiwyg_editors ) )
					var exc_mb_wysiwyg_editors = {};
				exc_mb_wysiwyg_editors.<?php echo $field_id; ?> = '<?php echo $editor; ?>';
			</script>

			<?php

		} else {

			$this->args['options']['textarea_name'] = $name;
			echo wp_editor( $this->get_value(), $id, $this->args['options'] );

		}

		echo '</div>';

	}

	/**
	 * Check if this is a placeholder field.
	 * Either the field itself, or because it is part of a repeatable group.
	 *
	 * @return bool
	 */
	public function is_placeholder() {

		if ( isset( $this->parent ) && ! is_int( $this->parent->field_index ) )
			return true;

		else return ! is_int( $this->field_index );

	}

}

/**
 * Standard select field.
 *
 * @supports "data_delegate"
 * @args
 *     'options'     => array Array of options to show in the select, optionally use data_delegate instead
 *     'allow_none'   => bool|string Allow no option to be selected (will place a "None" at the top of the select)
 *     'multiple'     => bool whether multiple can be selected
 */
class EXC_MB_Select extends EXC_MB_Field {

	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

	}

	/**
	 * Return the default args for the Select field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'options'         => array(),
				'multiple'        => false,
				'select2_351_options' => array(),
				'allow_none'      => false,
			)
		);
	}

	public function parwebd_save_values(){

		if ( isset( $this->parent ) && isset( $this->args['multiple'] ) && $this->args['multiple'] )
			$this->values = array( $this->values );

	}

	public function get_options() {

		if ( $this->has_data_delegate() )
			$this->args['options'] = $this->get_delegate_data();

		return $this->args['options'];
	}

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'select2_351-WEBDWooEVENT', trailingslashit( EXC_MB_URL ) . 'js/select-js-css/select2_351/select2_351.js', array( 'jquery' ) );
		wp_enqueue_script( 'field-select-WEBDWooEVENT', trailingslashit( EXC_MB_URL ) . 'js/field.select.js', array( 'jquery', 'select2_351-WEBDWooEVENT', 'exc_mb-scripts' ),'1.1' );
	}

	public function enqueue_styles() {

		parent::enqueue_styles();

		wp_enqueue_style( 'select2_351-WEBDWooEVENT', trailingslashit( EXC_MB_URL ) . 'js/select-js-css/select2_351/select2_351.css' );
	}

	public function html() {

		if ( $this->has_data_delegate() )
			$this->args['options'] = $this->get_delegate_data();

		$this->output_field();

		$this->output_script();

	}

	public function output_field() {

		$val = (array) $this->get_value();

		$name = $this->get_the_name_attr();
		$name .= ! empty( $this->args['multiple'] ) ? '[]' : null;

		$none = is_string( $this->args['allow_none'] ) ? $this->args['allow_none'] : __( 'None', 'exc_mb' );

		?>

		<select
			<?php $this->id_attr(); ?>
			<?php $this->boolean_attr(); ?>
			<?php printf( 'name="%s"', esc_attr( $name ) ); ?>
			<?php printf( 'data-field-id="%s" ', esc_attr( $this->get_js_id() ) ); ?>
			<?php echo ! empty( $this->args['multiple'] ) ? 'multiple' : '' ?>
			<?php $this->class_attr( 'exc_mb_select' ); ?>
			style="width: 100%"
		>

			<?php if ( $this->args['allow_none'] ) : ?>
				<option value=""><?php echo $none; ?></option>
			<?php endif; ?>

			<?php foreach ( $this->args['options'] as $value => $name ): ?>
			   <option <?php selected( in_array( $value, $val ) ) ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>

		</select>

		<?php
	}

	public function output_script() {

		$options = wp_parse_args( $this->args['select2_351_options'], array(
			'placeholder' => __( 'Type to search', 'exc_mb' ),
			'allowClear'  => true,
		) );

		?>

		<script type="text/javascript">

			(function($) {

				var options = <?php echo  json_encode( $options ); ?>

				if ( 'undefined' === typeof( window.exc_mb_select_fields ) )
					window.exc_mb_select_fields = {};

				var id = <?php echo json_encode( $this->get_js_id() ); ?>;
				window.exc_mb_select_fields[id] = options;

			})( jQuery );

		</script>

		<?php
	}

}

class EXC_MB_Taxonomy extends EXC_MB_Select {

	/**
	 * Return the default args for the Taxonomy select field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'taxonomy'   => '',
				'hide_empty' => false,
			)
		);
	}


	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

	}

	public function get_delegate_data() {

		$terms = $this->get_terms();

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$term_options = array();

		foreach ( $terms as $term )
			$term_options[$term->term_id] = $term->name;

		return $term_options;

	}

	private function get_terms() {

		return get_terms( $this->args['taxonomy'], array( 'hide_empty' => $this->args['hide_empty'] ) );

	}

}

/**
 * Post Select field.
 *
 * @supports "data_delegate"
 * @args
 *     'options'     => array Array of options to show in the select, optionally use data_delegate instead
 *     'allow_none'   => bool Allow no option to be selected (will palce a "None" at the top of the select)
 *     'multiple'     => bool whether multiple can be selected
 */
class EXC_MB_Post_Select extends EXC_MB_Select {

	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		if ( ! $this->args['uwebd_ajax'] ) {

			$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

		}

		}

	/**
	 * Return the default args for the Post select field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'query'    => array(),
				'uwebd_ajax' => false,
				'multiple' => false,
			)
		);
	}

	public function get_delegate_data() {

		$data = array();

		foreach ( $this->get_posts() as $post_id )
			$data[$post_id] = get_the_title( $post_id );

		return $data;

	}

	private function get_posts() {

		$this->args['query']['fields'] = 'ids';
		$query = new WP_Query( $this->args['query'] );

		return isset( $query->posts ) ? $query->posts : array();

	}

	public function parwebd_save_value() {

		// AJAX multi select2_351 data is submitted as a string of comma separated post IDs.
		// If empty, set to false instead of empty array to ensure the meta entry is deleted.
		if ( $this->args['uwebd_ajax'] && $this->args['multiple'] ) {
			$this->value = ( ! empty( $this->value ) ) ? explode( ',', $this->value ) : false;
		}

	}

	public function output_field() {

		// If AJAX, must use input type not standard select.
		if ( $this->args['uwebd_ajax'] ) :

			?>

			<input
				<?php $this->id_attr(); ?>
				<?php printf( 'value="%s" ', esc_attr( implode( ',' , (array) $this->value ) ) ); ?>
				<?php printf( 'name="%s"', esc_attr( $this->get_the_name_attr() ) ); ?>
				<?php printf( 'data-field-id="%s" ', esc_attr( $this->get_js_id() ) ); ?>
				<?php $this->boolean_attr(); ?>
				class="exc_mb_select"
				style="width: 100%"
			/>

			<?php

		else :

			parent::output_field();

		endif;

	}

	public function output_script() {

		parent::output_script();

		?>

		<script type="text/javascript">

			(function($) {

				if ( 'undefined' === typeof( window.exc_mb_select_fields ) )
					return false;

				// Get options for this field so we can modify it.
				var id = <?php echo json_encode( $this->get_js_id() ); ?>;
				var options = window.exc_mb_select_fields[id];

				<?php if ( $this->args['uwebd_ajax'] && $this->args['multiple'] ) : ?>
					// The multiple setting is required when using ajax (because an input field is used instead of select)
					options.multiple = true;
				<?php endif; ?>

				<?php if ( $this->args['uwebd_ajax'] && ! empty( $this->value ) ) : ?>

					options.initSelection = function( element, callback ) {

						var data = [];

						<?php if ( $this->args['multiple'] ) : ?>

							<?php foreach ( (array) $this->value as $post_id ) : ?>
								data.push( <?php echo json_encode( array( 'id' => $post_id, 'text' => html_entity_decode( get_the_title( $post_id ) ) ) ); ?> );
							<?php endforeach; ?>

						<?php else : ?>

							data = <?php echo json_encode( array( 'id' => $this->value, 'text' => html_entity_decode( get_the_title( $this->get_value() ) ) ) ); ?>;

						<?php endif; ?>

						callback( data );

					};

				<?php endif; ?>

				<?php if ( $this->args['uwebd_ajax'] ) : ?>

					<?php $this->args['query']['fields'] = 'ids'; ?>

					var ajaxData = {
						action  : 'exc_mb_post_select',
						post_id : '<?php echo intval( get_the_id() ); ?>', // Used for user capabilty check.
						nonce   : <?php echo json_encode( wp_create_nonce( 'exc_mb_select_field' ) ); ?>,
						query   : <?php echo json_encode( $this->args['query'] ); ?>
					};

					options.ajax = {
						url: <?php echo json_encode( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>,
						type: 'POST',
						dataType: 'json',
						data: function( term, page ) {
							ajaxData.query.s = term;
							ajaxData.query.paged = page;
							return ajaxData;
						},
						results : function( results, page ) {
							var postsPerPage = ajaxData.query.posts_per_page = ( 'posts_per_page' in ajaxData.query ) ? ajaxData.query.posts_per_page : ( 'showposts' in ajaxData.query ) ? ajaxData.query.showposts : 10;
							var isMore = ( page * postsPerPage ) < results.total;
							return { results: results.posts, more: isMore };
						}
					}

				<?php endif; ?>

			})( jQuery );

		</script>

		<?php
	}

}

// TODO this should be in inside the class
function exc_mb_ajax_post_select() {

	$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
	$nonce   = ! empty( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : false;
	$args    = ! empty( $_POST['query'] ) ? sanitize_text_field($_POST['query']) : array();

	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'exc_mb_select_field' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		echo json_encode( array( 'total' => 0, 'posts' => array() ) );
		exit;
	}

	$args['fields'] = 'ids'; // Only need to retrieve post IDs.

	$query = new WP_Query( $args );

	$json = array( 'total' => $query->found_posts, 'posts' => array() );

	foreach ( $query->posts as $post_id ) {
		array_push( $json['posts'], array( 'id' => $post_id, 'text' => html_entity_decode( get_the_title( $post_id ) ) ) );
	}

	echo json_encode( $json );

	exit;

}
add_action( 'wp_ajax_exc_mb_post_select', 'exc_mb_ajax_post_select' );

/**
 * Field to group child fieids
 * pass $args[fields] array for child fields
 * pass $args['repeatable'] for cloing all child fields (set)
 *
 * @todo remove global $post reference, somehow
 */
class EXC_MB_Group_Field extends EXC_MB_Field {

	static $added_js;
	private $fields = array();

	function __construct() {

		$args = func_get_args(); // you can't just put func_get_args() into a function as a parameter
		call_user_func_array( array( 'parent', '__construct' ), $args );

		if ( ! empty( $this->args['fields'] ) ) {
			foreach ( $this->args['fields'] as $f ) {

				$class = _exc_mb_field_class_for_type( $f['type'] );
				$this->add_field( new $class( $f['id'], $f['name'], array(), $f ) );

			}
		}

	}

	/**
	 * Return the default args for the Group field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'fields'              => array(),
				'string-repeat-field' => __( 'Add New Group', 'exc_mb' ),
				'string-delete-field' => __( 'Remove Group', 'exc_mb' ),
			)
		);
	}

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		foreach ( $this->args['fields'] as $f ) {
			$class = _exc_mb_field_class_for_type( $f['type'] );
			$field = new $class( '', '', array(), $f );
			$field->enqueue_scripts();
		}

	}

	public function enqueue_styles() {

		parent::enqueue_styles();

		foreach ( $this->args['fields'] as $f ) {
			$class = _exc_mb_field_class_for_type( $f['type'] );
			$field = new $class( '', '', array(), $f );
			$field->enqueue_styles();
		}

	}

	public function display() {

		global $post;

		$field = $this->args;
		$values = $this->get_values();

		$this->title();
		$this->description();

		if ( ! $this->args['repeatable'] && empty( $values ) ) {
			$values = array( null );
		}

		if ( $values ) {

			$i = 0;
			foreach ( $values as $value ) {

				$this->field_index = $i;
				$this->value = $value;

				?>

				<div class="field-item" data-class="<?php echo esc_attr( get_class($this) ) ?>" style="<?php echo esc_attr( $this->args['style'] ); ?>">
					<?php $this->html(); ?>
				</div>

				<?php

				$i++;

			}

		}

		if ( $this->args['repeatable'] ) {

			$this->field_index = 'x'; // x used to distinguish hidden fields.
			$this->value = '';

			?>

				<div class="field-item hidden" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="<?php echo esc_attr( $this->args['style'] ); ?>">
					<?php $this->html(); ?>
				</div>

				<button class="button repeat-field">
					<?php echo esc_html( $this->args['string-repeat-field'] ); ?>
				</button>

		<?php }

	}

	public function html() {

		$fields = &$this->get_fields();
		$value  = $this->get_value();

		// Reset all field values.
		foreach ( $fields as $field ) {
			$field->set_values( array() );
		}

		// Set values for this field.
		if ( ! empty( $value ) ) {
			foreach ( $value as $field_id => $field_value ) {
				$field_value = ( ! empty( $field_value ) ) ? $field_value : array();
				if ( ! empty( $fields[$field_id] ) ) {
					$fields[$field_id]->set_values( (array) $field_value );
			}
			}
		}

		?>

		<?php if ( $this->args['repeatable'] ) : 
			$rec_list = get_post_meta(get_the_ID(),'recurren_list', true );
			$valu = $aj_url = '';
			if(!empty($rec_list)){
				$valu = $value !='' ? esc_html(str_replace('\/', '/', json_encode($value))) : '';
				$aj_url = admin_url( 'admin-ajax.php' );
			}?>
			<button class="exc_mb-delete-field" data-date="<?php echo $valu;?>" data-url="<?php echo esc_url($aj_url); ?>" data-id="<?php echo esc_attr(get_the_ID()); ?>">
				<span class="exc_mb-delete-field-icon">&times;</span>
				<?php echo esc_html( $this->args['string-delete-field'] ); ?>
			</button>
		<?php endif; ?>

		<?php EXC_MB_Meta_Box::layout_fields( $fields ); ?>

	<?php }

	public function parwebd_save_values() {

		$fields = &$this->get_fields();
		$values = &$this->get_values();

		foreach ( $values as &$group_value ) {
			foreach ( $group_value as $field_id => &$field_value ) {

				if ( ! isset( $fields[$field_id] ) ) {
					$field_value = array();
					continue;
				}

				$field = $fields[$field_id];
				$field->set_values( $field_value );
				$field->parwebd_save_values();

				$field_value = $field->get_values();

				// if the field is a repeatable field, store the whole array of them, if it's not repeatble,
				// just store the first (and only) one directly
				if ( ! $field->args['repeatable'] )
					$field_value = reset( $field_value );
			}
		}

	}

	public function add_field( EXC_MB_Field $field ) {
		$field->parent = $this;
		$this->fields[$field->id] = $field;
	}

	public function &get_fields() {
		return $this->fields;
	}

	public function set_values( array $values ) {

		$fields       = &$this->get_fields();
		$this->values = $values;

		// Reset all field values.
		foreach ( $fields as $field ) {
			$field->set_values( array() );
		}

		foreach ( $values as $value ) {
			foreach ( $value as $field_id => $field_value ) {
				$fields[$field_id]->set_values( (array) $field_value );
			}
		}

	}

}


/**
 * Google map field class for EXC_MB
 *
 * It enables the google places API and doesn't store the place
 * name. It only stores latitude and longitude of the selected area.
 *
 * Note
 */
class EXC_MB_Gmap_Field extends EXC_MB_Field {

	/**
	 * Return the default args for the Map field.
	 *
	 * @return array $args
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'field_width'         => '100%',
				'field_height'        => '250px',
				'default_lat'         => '51.5073509',
				'default_long'        => '-0.12775829999998223',
				'default_zoom'        => '8',
				'string-marker-title' => __( 'Drag to set the exact location', 'exc_mb' ),
			)
		);
	}

	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'exc_mb-google-maps', '//maps.google.com/maps/api/js?sensor=true&libraries=places' );
		wp_enqueue_script( 'exc_mb-google-maps-script', trailingslashit( EXC_MB_URL ) . 'js/field-gmap.js', array( 'jquery', 'exc_mb-google-maps' ) );

		wp_localize_script( 'exc_mb-google-maps-script', 'EXC_MBGmaps', array(
			'defaults' => array(
				'latitude'  => $this->args['default_lat'],
				'longitude' => $this->args['default_long'],
				'zoom'      => $this->args['default_zoom'],
			),
			'strings'  => array(
				'markerTitle' => $this->args['string-marker-title']
			)
		) );

	}

	public function html() {

		// Ensure all args used are set
		$value = wp_parse_args(
			$this->get_value(),
			array( 'lat' => null, 'long' => null, 'elevation' => null )
		);

		$style = array(
			sprintf( 'width: %s;', $this->args['field_width'] ),
			sprintf( 'height: %s;', $this->args['field_height'] ),
			'border: 1px solid #eee;',
			'margin-top: 8px;'
		);

		?>

		<input type="text" <?php $this->class_attr( 'map-search' ); ?> <?php $this->id_attr(); ?> />

		<div class="map" style="<?php echo esc_attr( implode( ' ', $style ) ); ?>"></div>

		<input type="hidden" class="latitude"  <?php $this->name_attr( '[lat]' ); ?>       value="<?php echo esc_attr( $value['lat'] ); ?>" />
		<input type="hidden" class="longitude" <?php $this->name_attr( '[long]' ); ?>      value="<?php echo esc_attr( $value['long'] ); ?>" />
		<input type="hidden" class="elevation" <?php $this->name_attr( '[elevation]' ); ?> value="<?php echo esc_attr( $value['elevation'] ); ?>" />

		<?php
	}
}
