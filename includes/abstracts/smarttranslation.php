<?php if ( ! defined( 'ABSPATH' ) ) exit;
abstract class PFORM_Abstracts_Smarttranslation {

		/**
		 * Setting page id.
		 */
		protected $id = '';

		/**
		 * Setting page label.
		 */
		protected $label = '';

		/**
		 * Constructor.
		 */
		public function __construct() {
				add_filter( 'pie_forms_smarttranslation_tabs_array', array( $this, 'add_settings_page' ), 20 );
				add_action( 'pie_forms_settings_' . $this->id, array( $this, 'output' ) );
				add_action( 'pie_forms_settings_save_' . $this->id, array($this, 'save') );	
		}

		/**
		 * Get settings page ID.
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get settings page label.
		 *
		 * @since  1.2.0
		 * @return string
		 */
		public function get_label() {
			return $this->label;
		}

		/**
		 * Add this page to settings.
		 *
		 * @param  array $pages Setting pages.
		 * @return mixed
		 */
		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = $this->label;
			
			return $pages;
		}

		/**
		 * Get settings array.
		 *
		 * @return array
		 */
		public function get_settings() {
			return apply_filters( 'pie_forms_get_settings_' . $this->id, array() );
		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {
			return apply_filters( 'pie_forms_get_sections_' . $this->id, array() );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			$settings = $this->get_settings();
			PFORM_Admin_Settings::output_fields( $settings );
		}

		/**
		 * Save settings.
		 */
		public function save() {	
			global $current_section;
			$settings = $this->get_settings();
			PFORM_Admin_Settings::save_fields( $settings );

			if ( $current_section ) {
				do_action( 'pie_forms_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}
	