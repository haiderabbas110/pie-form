<?php if ( ! defined( 'ABSPATH' ) ) exit;
abstract class PFORM_Abstracts_Tools {

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
			add_filter( 'pie_forms_tools_tabs_array', array( $this, 'add_tools_page' ), 20 );
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
		public function add_tools_page( $pages ) {
			$pages[ $this->id ] = $this->label;
			
			return $pages;
		}	
	}
	