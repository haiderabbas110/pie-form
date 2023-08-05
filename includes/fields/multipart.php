<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class PFORM_Fields_Multipart
 */

class PFORM_Fields_Multipart extends PFORM_Abstracts_Fields{

	public function __construct() {

		$this->name     = esc_html__( 'Multipart', 'pie-forms' );
		$this->type     = 'multipart';
		$this->icon     = 'multipart';
		$this->order    = 220;
		$this->group    = 'advanced';
	
		$is_addon_activated = get_option('pieforms_manager_addon_multipart_activated');

		if(!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php') || $is_addon_activated == "Deactivated"){
			$this->is_addon = true;
			$this->is_pro 	= true;
			parent::__construct();
			return;
		}else{
			parent::__construct();
		}
		
	}
		
	/**
	 * Hook in tabs.
	 */
	public function init_hooks() {

		add_filter('pie_forms_addon_activated_field', array( $this, 'pie_forms_display_label_before_print' ), 10, 2);

	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @param array $field
	 */
	public function field_options( $field ) {


		$position       = ! empty( $field['position'] ) ? $field['position'] : '';
		$position_class = ! empty( $field['position'] ) ? 'pie-forms-pagebreak-' . $position : '';

		// Hidden field indicating the position.
		$this->field_element(
			'text',
			$field,
			array(
				'type'  => 'hidden',
				'slug'  => 'position',
				'value' => $position,
				'class' => 'position',
			)
		);

		/*
		 * Basic field options.
		 */
		// Options open markup.
		$this->field_option( 'basic-options', $field, array(
			'markup' => 'open',
			'class'  => $position_class,
		) );

		$lbl    = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'meta-key',
				'value'   => esc_html__( 'Meta Key', 'pie-forms' ),
				'tooltip' =>  esc_html__( 'Keys to be stored in database', 'pie-forms' ),
			),
			false
		);
		$fld    = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'meta-key',
				'class' => 'pf-input-meta-key',
				'value' => ! empty( $field['meta-key'] ) ? esc_attr( $field['meta-key'] ) : Pie_Forms()->core()->pform_get_meta_key_field_option( $field ),
				'readonly' => 'readonly',
			),
			false
		);
		$this->field_element( 'row', $field, array(
			'slug'    => 'meta-key',
			'content' => $lbl . $fld,
		) );

		// Options specific to the top pagebreak.
		if ( 'top' === $position ) {

			// Indicator theme.
			$themes = array(
				'progress'  => esc_html__( 'Progress Bar', 'pie-forms' ),
				'circles'   => esc_html__( 'Circles', 'pie-forms' ),
				'connector' => esc_html__( 'Connector', 'pie-forms' ),
				'none'      => esc_html__( 'None', 'pie-forms' ),
			);
			$lbl    = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'indicator',
					'value'   => esc_html__( 'Progress Indicator', 'pie-forms' ),
					'tooltip' => esc_html__( 'Select theme for Page Indicator which is displayed at the top of the form.', 'pie-forms' ),
				),
				false
			);
			$fld    = $this->field_element(
				'select',
				$field,
				array(
					'slug'    => 'indicator',
					'value'   => ! empty( $field['indicator'] ) ? esc_attr( $field['indicator'] ) : 'progress',
					'options' => apply_filters( 'pieforms_pagebreak_indicator', $themes ),
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'indicator',
				'content' => $lbl . $fld,
			) );

			// Indicator color picker.
			$lbl = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'indicator_color',
					'value'   => esc_html__( 'Page Indicator Color', 'pie-forms' ),
					'tooltip' => esc_html__( 'Select the primary color for the Page Indicator theme.', 'pie-forms' ),
				),
				false
			);
			$fld = $this->field_element(
				'text',
				$field,
				array(
					'slug'  => 'indicator_color',
					'value' => ! empty( $field['indicator_color'] ) ? esc_attr( $field['indicator_color'] ) : '#133950',
					'class' => 'pie-forms-color-picker',
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'indicator_color',
				'content' => $lbl . $fld,
				'class'   => 'color-picker-row',
			) );
		} // End if().

		// Page Title, don't display for bottom pagebreaks.
		if ( 'bottom' !== $position ) {
			$lbl = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'title',
					'value'   => esc_html__( 'Page Title', 'pie-forms' ),
					'tooltip' => esc_html__( 'Enter text for the page title.', 'pie-forms' ),
				),
				false
			);
			$fld = $this->field_element(
				'text',
				$field,
				array(
					'slug'  => 'title',
					'value' => ! empty( $field['title'] ) ? esc_attr( $field['title'] ) : '',
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'title',
				'content' => $lbl . $fld,
			) );
		}

		// Next label.
		if ( empty( $position ) ) {
			$lbl = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'next',
					'value'   => esc_html__( '"Next" Label', 'pie-forms' ),
					'tooltip' => esc_html__( 'Enter text for Next page navigation button.', 'pie-forms' ),
				),
				false
			);
			$fld = $this->field_element(
				'text',
				$field,
				array(
					'slug'  => 'next',
					'value' => ! empty( $field['next'] ) ? esc_attr( $field['next'] ) : esc_html__( 'Next', 'pie-forms' ),
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'next',
				'content' => $lbl . $fld,
			) );
		}

		// Options not available to top pagebreaks.
		if ( 'top' !== $position ) {

			// Previous button toggle.
			$lbl = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'prev_toggle',
					'value'   => esc_html__( 'Display Previous', 'pie-forms' ),
					'tooltip' => esc_html__( 'Toggle displaying the Previous page navigation button.', 'pie-forms' ),
				),
				false
			);
			$fld = $this->field_element(
				'toggle',
				$field,
				array(
					'slug'  => 'prev_toggle',
					'value' => ! empty( $field['prev_toggle'] ) || ! empty( $field['prev'] ) ? false : true,
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'prev_toggle',
				'content' => $lbl . $fld,
			) );

			// Previous button label.
			$lbl = $this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'prev',
					'value'   => esc_html__( '"Previous" Label', 'pie-forms' ),
					'tooltip' => esc_html__( 'Enter text for Previous page navigation button.', 'pie-forms' ),
				),
				false
			);
			$fld = $this->field_element(
				'text',
				$field,
				array(
					'slug'  => 'prev',
					'value' => ! empty( $field['prev'] ) ? esc_attr( $field['prev'] ) : esc_html__( 'Previous', 'pie-forms' ),
				),
				false
			);
			$this->field_element( 'row', $field, array(
				'slug'    => 'prev',
				'content' => $lbl . $fld,
				'class'   => empty( $field['prev_toggle'] ) ? '' : '',
			) );
		} // End if().

		// Options close markup.
		$this->field_option( 'basic-options', $field, array(
			'markup' => 'close',
		) );

		/*
		 * Advanced field options.
		 */

		// Advanced options are not available to bottom pagebreaks.
		if ( 'bottom' !== $position ) {

			// Options open markup.
			$this->field_option( 'advanced-options', $field, array(
				'markup' => 'open',
				'class'  => $position_class,
			) );

			// Custom CSS classes.
			$this->field_option( 'css', $field );

			// Options close markup.
			$this->field_option( 'advanced-options', $field, array(
				'markup' => 'close',
			) );
		} // End if().
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {

		do_action('pie_forms_multipart_field_builder_insider' , $field);
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {

		do_action('pie_forms_multipart_field_frontend' , $field, $field_atts, $form_data);
	}

	
	/**
	 * Display field if the addon is activated.
	 * 
	 * @param boolean $to_show
	 * @param array $field
	 */
	public function pie_forms_display_label_before_print($to_show, $field){
	
		if(isset($field['type']) && $field['type'] == 'multipart' && ( !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php') )){
			$to_show = true;
		}
		return $to_show;
	}

}
