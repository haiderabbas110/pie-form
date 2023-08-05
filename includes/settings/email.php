<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Settings_Email extends PFORM_Abstracts_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = esc_html__( 'Email', 'pie-forms' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 */
	public function get_settings() {
		$settings = apply_filters(
			'pie_forms_email_settings',
			array(
				array(
					'title' => esc_html__( 'Email Settings', 'pie-forms' ),
					'type'  => 'title',
					'id'    => 'email_settings',
				),
				array(
					'title'    => esc_html__( 'Enable copies', 'pie-forms' ),
					'desc'     => esc_html__( 'Enable the use of Cc and Bcc email addresses.', 'pie-forms' ),
					'id'       => 'pf_enable_email_copies',
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'title'   => esc_html__( 'Template', 'pie-forms' ),
					'type'    => 'checkbox',
					'id'      => 'pf_email_template',
					'desc'    => esc_html__( 'Enable HTML Template.', 'pie-forms' ),
					'default' => 'yes',
				),
				array(
					'title'    => esc_html__( 'Header Image', 'pie-forms' ),
					'desc'     => wp_kses( __( 'Upload or choose a logo to be displayed at the top of email notifications.<br>Recommended size is 300x100 or smaller for best support on all devices.', 'pie-forms' ), [ 'br' => [] ] ),
					'id'       => 'pf_email_header_image',
					'type'     => 'image',
					'css'      => 'min-width: 350px;',
					'default'  => '',
				),
				array(
					'title'    => esc_html__( 'Background Color', 'pie-forms' ),
					'id'       => 'pf_email_background-color',
					'type'     => 'color',
					'css'      => 'min-width: 350px;',
					'class'    => 'pie-forms-color-picker',
					'default'  => '#e9eaec',
				),
				array(
					'title'    => esc_html__( 'Footer Content', 'pie-forms' ),
					'id'       => 'pf_email_custom_footer_content',
					'type'     => 'tinymce',
					'default'  => '',
				),
				array(
					'title'    => '',
					'id'       => 'pf_email_enable_custom_footer',
					'desc'     => esc_html__( 'Show footer in email.', 'pie-forms' ),
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'email_settings',
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