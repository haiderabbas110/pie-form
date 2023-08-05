<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Settings_General extends PFORM_Abstracts_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = esc_html__( 'General', 'pie-forms' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 */
	
	public function get_settings() {
		$settings = apply_filters(
			'pie_forms_general_settings',
			array(
				array(
					'title' => esc_html__( 'Settings', 'pie-forms' ),
					'type'  => 'title',
					'id'    => 'general_settings',
				),
				array(
					'title'    => esc_html__( '', 'pie-forms' ),
					'desc'     => esc_html__( 'Check this to enable GDPR related features and enhancements.', 'pie-forms' ),
					'id'       => 'pf_gdpr_options',
					'type'     => 'checkbox',
					'default'  => 'no',
					'class'		=> 'pie-forms-delete-option'
				),
				array(
					'title'    => esc_html__( '', 'pie-forms' ),
					'desc'     => esc_html__( 'Delete database on uninstalling plugin.', 'pie-forms' ),
					'id'       => 'pf_delete_options',
					'type'     => 'checkbox',
					'default'  => 'no',
					'class'		=> 'pie-forms-delete-option'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'general_settings',
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