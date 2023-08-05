<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Pie_Fields_FirstName
 */
class PFORM_Fields_Email extends PFORM_Abstracts_Fields
{   
    public function __construct()
    {
        $this->name     = esc_html__( 'Email', 'pie-forms' );
		$this->type     = 'email';
		$this->icon     = 'email';
		$this->order    = 90;
		$this->group    = 'basic';
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
					'required',
					'required_field_message',
					'confirmation',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'size',
					'placeholder',
					'confirmation_placeholder',
					'label_hide',
					'sublabel_hide',
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
		echo '<input type="email" placeholder="' . esc_attr( $placeholder ) . '" class="widefat" disabled>';

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
		$default_email 			= 	is_user_logged_in() ? wp_get_current_user()->data->user_email : '';

		// Primary field.
		printf(
			'<input type="email" %s value="%s" %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			esc_attr( $default_email),
			esc_attr( $primary['required'] )
		);
	}
}
