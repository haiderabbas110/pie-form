<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Phone extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name   = esc_html__( 'Phone', 'pie-forms' );
		$this->type   = 'phone';
		$this->icon   = 'phone-icon';
		$this->order  = 100;
		$this->group  = 'basic';
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
					'placeholder',
					'label_hide',
					'css',
					'custom_regex',
					'custom_validation_message',
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

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="tel" placeholder="' . esc_attr( $placeholder ) . '" class="widefat" disabled>';

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
			'<input type="tel" %s %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			esc_attr( $primary['required'] )
		);
	}
}
