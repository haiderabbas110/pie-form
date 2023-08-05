<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Select extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Select Dropdown', 'pie-forms' );
		$this->type     = 'select';
		$this->icon     = 'dropdown';
		$this->order    = 50;
		$this->group    = 'basic';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'Option 1', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Option 2', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Option 3', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
		);
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'radio',
					'enhanced_select',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'size',
					'placeholder',
					'label_hide',
					'is_search',
					'css',
				),
			),
			'conditional-logic' => array(
				'field_options' => array(
					'upgrade-to-pro',
				)
			)
		);

		parent::__construct();
	}

	
	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {
		// Label.
		$this->field_preview_option( 'label', $field );

		// Choices.
		$this->field_preview_option( 'choices', $field );

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Setup and sanitize the necessary data.
		$search_select 	   		= isset($field['is_search']) ? $field['is_search'] : '';		
		$primary           		= $field['properties']['inputs']['primary'];
		$field             		= apply_filters( 'pie_forms_select_field_display', $field, $field_atts, $form_data );
		$field_with_required = 	!empty($field['required']) ? $field['placeholder']."*" : $field['placeholder'];
		$field_placeholder 		= ! empty( $field['placeholder'] ) ? Pie_Forms()->core()->pie_string_translation( $form_data['id'], $field['id'], $field_with_required, '-placeholder' ) : '';
		$choices           		= $field['choices'];
		$has_default       		= false;
		
		$enable_placeholder 	= isset( $form_data['settings']['label_to_placeholder'] ) ? $form_data['settings']['label_to_placeholder']  : 0;
		$placeholder_if_required = isset($field['required']) == 1 ? $field['label'].'*' : $field['label'];
		$label_to_placeholder    = $enable_placeholder == 1 ?  $placeholder_if_required : '';


		
		// Check to see if any of the options have selected by default.
		foreach ( $choices as $choice ) {
			if ( isset( $choice['default'] ) ) {
				$has_default = true;
				break;
			}
		}
		if($search_select ==  1){
			// enqueue style and script
			wp_enqueue_style('MultiselectCSS', Pie_Forms::$url . 'assets/css/lib/select2.min.css', array(), Pie_Forms::VERSION );	
			wp_register_script( 'MutliselectJS', Pie_Forms::$url . 'assets/js/lib/select2.min.js', array(), Pie_Forms::VERSION );
			wp_enqueue_script( 'MutliselectJS');
		}
		$search_select_class = $search_select == 1 ? 'pie-forms-search-select' : ''; 
		// Primary select field.
		printf(
			"<select class='".$search_select_class."' type='select' %s %s>",
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ), 
			$primary['required'] 
		);

		// Optional placeholder.
		if ( ! empty( $field_placeholder ) ) {
			printf( '<option value="" class="placeholder" %s>%s</option>', selected( false, $has_default, false ), esc_html( $field_placeholder ) );
		}else if(!empty($label_to_placeholder)){
			printf( '<option value="" class="placeholder" %s>%s</option>', selected( false, $has_default, false ), esc_html( $label_to_placeholder ) );
		}

		// Build the select options.
		foreach ( $choices as $key => $choice ) {
			// Register string for translation.
			$choice['label'] = Pie_Forms()->core()->pie_string_translation( $form_data['id'], $field['id'], $choice['label'], '-choices-' . $key );

			$selected = isset( $choice['default'] ) && empty( $field_placeholder ) ? '1' : '0';
			$val      = isset( $field['show_values'] ) ? esc_attr( $choice['value'] ) : esc_attr( $choice['label'] );

			printf( '<option value="%s" %s>%s</option>', esc_attr( $val ), selected( '1', $selected, false ), esc_html( $choice['label'] ) );
		}

		echo '</select>';
	}

	/**
	 * Formats and sanitizes field.
	 */
	public function format( $field_id, $field_submit, $form_data, $meta_key ) {
		$field     = $form_data['form_fields'][ $field_id ];
		$name      = make_clickable( $field['label'] );
		$value_raw = $field_submit;
		$value     = '';

		$data = array(
			'name'      => $name,
			'value'     => '',
			'value_raw' => $value_raw,
			'id'        => $field_id,
			'type'      => $this->type,
			'meta_key'  => $meta_key,
		);

	
		if ( ! empty( $field['show_values'] ) && '1' === $field['show_values'] ) {
			foreach ( $field['choices'] as $choice ) {
				if ( $choice['value'] === $field_submit ) {
					$value = $choice['label'];
					break;
				}
			}

			$data['value'] =  $value ;
		} else {
			$data['value'] = $value_raw;
		}

		// save field after push.
		Pie_Forms()->task->form_fields[ $field_id ] = $data;

	}
}
