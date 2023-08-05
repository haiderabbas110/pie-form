<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Number extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Number', 'pie-forms' );
		$this->type     = 'number';
		$this->icon     = 'number';
		$this->order    = 80;
		$this->group    = 'advanced';
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'step',
					'min_value',
					'max_value',
					'default_value',
					'placeholder',
					'label_hide',
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
	 * Hook in tabs.
	 */
	public function init_hooks() {
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Step field option.
	 */
	public function step( $field ) {
		$label       = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'step',
				'value'   => esc_html__( 'Step', 'pie-forms' ),
				'tooltip' => esc_html__( 'Allows users to enter specific legal number intervals.', 'pie-forms' ),
			),
			false
		);
		$input_field = $this->field_element(
			'text',
			$field,
			array(
				'type'  => 'number',
				'slug'  => 'step',
				'min'	=> '1',
				'class' => 'pf-input-number-step',
				'value' => isset( $field['step'] ) ? $field['step'] : 1,
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'step',
				'content' => $label . $input_field,
			)
		);
	}

	/**
	 * Minimum value field option.
	 */
	public function min_value( $field ) {
		$label       = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'min_value',
				'value'   => esc_html__( 'Min Value', 'pie-forms' ),
				'tooltip' => esc_html__( 'Minimum value user is allowed to enter.', 'pie-forms' ),
			),
			false
		);
		$input_field = $this->field_element(
			'text',
			$field,
			array(
				'type'  => 'number',
				'slug'  => 'min_value',
				'min'	=> '1',
				'class' => 'pf-input-number',
				'value' => isset( $field['min_value'] ) ? $field['min_value'] : '',
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'min_value',
				'content' => $label . $input_field,
			)
		);
	}

	/**
	 * Maximum value field option.
	 */
	public function max_value( $field ) {
		$label       = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'max_value',
				'value'   => esc_html__( 'Max Value', 'pie-forms' ),
				'tooltip' => esc_html__( 'Maximum value user is allowed to enter.', 'pie-forms' ),
			),
			false
		);
		$input_field = $this->field_element(
			'text',
			$field,
			array(
				'type'  => 'number',
				'slug'  => 'max_value',
				'min'	=> '0',
				'class' => 'pf-input-number',
				'value' => isset( $field['max_value'] ) ? $field['max_value'] : '',
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'max_value',
				'content' => $label . $input_field,
			)
		);
	}

	/**
	 * Define additional field properties.
	 */
	public function field_properties( $properties, $field, $form_data ) {
		// Input primary: step interval.
		if ( ! empty( $field['step'] ) ) {
			$properties['inputs']['primary']['attr']['step'] = (float) $field['step'];
		}

		// Input primary: minimum value.
		if ( ! empty( $field['min_value'] ) ) {
			$properties['inputs']['primary']['attr']['min'] = (float) $field['min_value'];
		}

		// Input primary: maximum value.
		if ( ! empty( $field['max_value'] ) ) {
			$properties['inputs']['primary']['attr']['max'] = (float) $field['max_value'];
		}

		return $properties;
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="number" placeholder="' . esc_attr($placeholder) . '" class="widefat" disabled>'; // @codingStandardsIgnoreLine.

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Define data.
		$primary = $field['properties']['inputs']['primary'];
		
		// Primary field.
		printf(
			'<input type="number" %s %s min="0">',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			esc_attr( $primary['required'] )
		);
	}

}
