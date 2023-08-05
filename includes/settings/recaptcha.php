<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Settings_reCAPTCHA.
 */
class PFORM_Settings_reCAPTCHA extends PFORM_Abstracts_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'recaptcha';
		$this->label = esc_html__( 'reCAPTCHA', 'pie-forms' );
		parent::__construct();
		
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$recaptcha_type = get_option( 'pf_recaptcha_type', 'v2' );
		$invisible      = get_option( 'pf_recaptcha_v2_invisible', 'no' );
		$settings       = apply_filters(
			'pie_forms_recaptcha_settings',
			array(
				array(
					'title' => esc_html__( 'Google reCAPTCHA Integration', 'pie-forms' ),
					'type'  => 'title',
					/* translators: %1$s - Google reCAPTCHA docs url */
					//'desc'  => sprintf( __( 'Google Captcha easy to integrate' ) ),
					'id'    => 'integration_options',
				),
				array(
					'title'    => esc_html__( 'reCAPTCHA type', 'pie-forms' ),
					'desc'     => esc_html__( 'Choose the type of reCAPTCHA for this site key.', 'pie-forms' ),
					'id'       => 'pf_recaptcha_type',
					'default'  => 'v2',
					'type'     => 'select',
					'options'  => array(
						'v2' => esc_html__( 'reCAPTCHA v2', 'pie-forms' ),
						'v3' => esc_html__( 'reCAPTCHA v3', 'pie-forms' ),
					),
					'class'    => 'pie-forms-recaptcha-type',
					'desc_tip' => true,
				),
				array(
					'title'      => esc_html__( 'Site Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your site key for your reCAPTCHA v2.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v2_site_key',
					'is_visible' => 'v2' === $recaptcha_type && 'no' === $invisible,
					'default'    => '',
					'desc_tip'   => true,
				),
				array(
					'title'      => esc_html__( 'Secret Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your secret key for your reCAPTCHA v2.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v2_secret_key',
					'is_visible' => 'v2' === $recaptcha_type && 'no' === $invisible,
					'default'    => '',
					'desc_tip'   => true,
				),
				array(
					'title'      => esc_html__( 'Site Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your site key for your reCAPTCHA v2.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v2_invisible_site_key',
					'is_visible' => 'v2' === $recaptcha_type && 'yes' === $invisible,
					'default'    => '',
					'desc_tip'   => true,
				),
				array(
					'title'      => esc_html__( 'Secret Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your secret key for your reCAPTCHA v2.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v2_invisible_secret_key',
					'is_visible' => 'yes' === $invisible && 'v2' === $recaptcha_type,
					'default'    => '',
					'desc_tip'   => true,
				),
				// array(
				// 	'title'      => esc_html__( 'Invisible reCAPTCHA', 'pie-forms' ),
				// 	'type'       => 'checkbox',
				// 	'desc'       => esc_html__( 'Enable Invisible reCAPTCHA.', 'pie-forms' ),
				// 	'id'         => 'pf_recaptcha_v2_invisible',
				// 	'is_visible' => 'v2' === $recaptcha_type,
				// 	'default'    => 'no',
				// ),
				array(
					'title'      => esc_html__( 'Site Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your site key for your reCAPTCHA v3.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v3_site_key',
					'is_visible' => 'v3' === $recaptcha_type,
					'default'    => '',
					'desc_tip'   => true,
				),
				array(
					'title'      => esc_html__( 'Secret Key', 'pie-forms' ),
					'type'       => 'text',
					/* translators: %1$s - Google reCAPTCHA docs url */
					'desc'       => sprintf( esc_html__( 'Please enter your secret key for your reCAPTCHA v3.', 'pie-forms' ) ),
					'id'         => 'pf_recaptcha_v3_secret_key',
					'is_visible' => 'v3' === $recaptcha_type,
					'default'    => '',
					'desc_tip'   => true,
				),
				array(
					'title'    => esc_html__( 'Language', 'pie-forms' ),
					'desc'     => esc_html__( 'Choose the language of reCAPTCHA.', 'pie-forms' ),
					'id'       => 'pf_recaptcha_language',
					'default'  => 'en',
					'type'     => 'select',
					'options'  => Pie_Forms()->core()->pform_captcha_languages(),
					'desc_tip' => true,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'integration_options',
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