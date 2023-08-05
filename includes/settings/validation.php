<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Settings_Validation extends PFORM_Abstracts_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'validation';
		$this->label = __( 'Messages', 'pie-forms' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters(
			'pie_forms_validation_settings',
			array(
				array(
					'title' => esc_html__( 'Global Forms Messages', 'pie-forms' ),
					'type'  => 'title',
					//'desc'  => 'Validation Messages for Form Fields.',
					'id'    => 'validation_options',
				),
				array(
					'title'    => esc_html__( 'Required', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the required form field', 'pie-forms' ),
					'id'       => 'pf_required_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'This field is required.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Website URL', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the valid website url', 'pie-forms' ),
					'id'       => 'pf_url_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Please enter a valid URL.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Email', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the valid email', 'pie-forms' ),
					'id'       => 'pf_email_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Please enter a valid email address.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'reCaptcha', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the reCaptcha.', 'pie-forms' ),
					'id'       => 'pf_reCaptcha_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Please fill recaptcha', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Number', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the valid number', 'pie-forms' ),
					'id'       => 'pf_number_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Please enter a valid number.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Phone Number', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the message for the phone number', 'pie-forms' ),
					'id'       => 'pf_phone_validation',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 	350px;',
					'default'  => esc_html__( 'Please enter a phone number.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Form Submit Button Text', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the form submit button text', 'pie-forms' ),
					'id'       => 'pf_global_submit_button_txt',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Send.', 'pie-forms' ),
				),
				array(
					'title'    => esc_html__( 'Success Form Submission Message', 'pie-forms' ),
					'desc'     => esc_html__( 'Enter the Success form submission message', 'pie-forms' ),
					'id'       => 'pf_global_success_message',
					'type'     => 'text',
					'desc_tip' => true,
					'css'      => 'min-width: 350px;',
					'default'  => esc_html__( 'Form has been submitted.', 'pie-forms' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'validation_options',
				),
			)
		);

		return apply_filters( 'pie_forms_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		PFORM_Admin_Settings::save_fields( $settings );
	}
}