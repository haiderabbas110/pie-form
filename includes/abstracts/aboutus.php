<?php if ( ! defined( 'ABSPATH' ) ) exit;
abstract class PFORM_Abstracts_Aboutus {

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
			add_filter( 'pie_forms_aboutus_tabs_array', array( $this, 'add_aboutus_page' ), 20 );
			add_action( 'pie_forms_aboutus_' . $this->id, array( $this, 'output' ) );
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
		public function add_aboutus_page( $pages ) {
			$pages[ $this->id ] = $this->label;
			
			return $pages;
		}	
		
		/**
		 * Output the settings.
		 */
		public function output() {
			$settings = $this->get_output();
			PFORM_Admin_Aboutus::output_info($settings);
		}
		
		/**
		 * Get settings array.
		 *
		 * @return array
		 */
		public function get_output() {
			$data = '';
			return apply_filters( 'pie_forms_get_aboutus_output_' . $this->id,$data );
		}

	}
	