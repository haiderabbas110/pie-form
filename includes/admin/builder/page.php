<?php
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'PFORM_Admin_Builder_Page', false ) ) :

	/**
	 * PFORM_Admin_Builder_Page Class.
	 */
	 class PFORM_Admin_Builder_Page {

		/**
		 * Form object.
		 *
		 * @var object
		 */
		protected $form;

		/**
		 * Builder page id.
		 *
		 * @var string
		 */
		protected $id = '';

		/**
		 * Builder page label.
		 *
		 * @var string
		 */
		protected $label = '';

		/**
		 * Is sidebar available?
		 *
		 * @var boolean
		 */
		protected $sidebar = false;

		/**
		 * Array of form data.
		 *
		 * @var array
		 */
		public $form_data = array();

		/**
		 * Constructor.
		 */
		public function __construct() {

			$form_id         = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : 0;
			
			$data 			= Pie_Forms()->form()->get($form_id);
			$this->form      = array_shift($data);

			$this->form_data = is_object( $this->form ) ? Pie_Forms()->core()->pform_decode( wp_unslash($this->form->form_data) ) : array();
			// Init hooks.
			$this->init_hooks();

			// Hooks.
			add_filter( 'pie_forms_builder_tabs_array', array( $this, 'add_builder_page' ), 20 );
			add_action( 'pie_forms_builder_sidebar_' . $this->id, array( $this, 'output_sidebar' ) );
			add_action( 'pie_forms_builder_content_' . $this->id, array( $this, 'output_content' ) );
		}

		/**
		 * Get builder page ID.
		 *
		 * @return string
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get builder page label.
		 *
		 * @return string
		 */
		public function get_label() {
			return $this->label;
		}

		/**
		 * Get builder page sidebar.
		 *
		 * @return string
		 */
		public function get_sidebar() {
			return $this->sidebar;
		}

		/**
		 * Get builder page form data.
		 *
		 * @return string
		 */
		public function get_form_data() {
			return $this->form_data;
		}

		/**
		 * Add this page to builder.
		 *
		 * @param  array $pages Builder pages.
		 * @return mixed
		 */
		public function add_builder_page( $pages ) {
			$pages[ $this->id ] = array(
				'id'    => $this->id,
				'label'   => $this->label,
				'sidebar' => $this->sidebar,
			);

			return $pages;
		}

		//EMAIL & GENERAL SETTINGS
		public function add_sidebar_tab( $name, $slug, $icon = '', $container_name = 'setting' ) {
		/* 	$class  = '';
			$class .= 'default' === $slug ? ' default' : '';
			$class .= ! empty( $icon ) ? ' icon' : '';
			$active = ($slug == 'general') ? 'active' : '';

			echo '<a href="#" class="pf-panel-tab pf-' . esc_attr( $container_name ) . '-panel pie-forms-panel-sidebar-section pie-forms-panel-sidebar-section-' . esc_attr( $slug ) . esc_attr( $class ) .' '.$active.'" data-section="' . esc_attr( $slug ) . '">';
			
			echo esc_html( $name );
			echo '</a>'; */
		}

		/**
		 * Hook in tabs.
		 */
		public function init_hooks() {}

		/**
		 * Outputs the builder sidebar.
		 */
		public function output_sidebar() {}

		/**
		 * Outputs the builder content.
		 */
		public function output_content() {}
	}

endif;
