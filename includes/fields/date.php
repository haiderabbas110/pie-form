<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Date extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Date', 'pie-forms' );
		$this->type     = 'date';
		$this->icon     = 'date';
		$this->order    = 20;
		$this->group    = 'basic';
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
				),
			),
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
		echo '<input type="text" placeholder="' . esc_attr( $placeholder ) . '" class="widefat" disabled>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Define data.
		$primary 				= $field['properties']['inputs']['primary'];

		$class = array_merge( array( 'jquery-ui-field' ), $primary['class'] );

		// Primary field.
		printf(
			'<input type="text" %s %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $class, $primary['data'], $primary['attr'] ),
			esc_attr( $primary['required'] )
		);
	}


}
