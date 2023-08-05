<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_Name extends PFORM_Abstracts_Fields
{   

    public function __construct()
    {
        $this->name     = esc_html__( 'Name', 'pie-forms' );
		$this->type     = 'name';
		$this->icon     = 'name';
		$this->order    = 10;
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
					'validation_rule',
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

		//Default Email
		$default_name 			= 	is_user_logged_in() ? wp_get_current_user()->data->display_name : '';

		// Primary field.
		printf(
			'<input type="text" %s  value="%s"  %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			esc_attr( $default_name ),
			esc_attr( $primary['required'] )
		);
	}

}