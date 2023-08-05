<?php

/**
 * Class Divi.
 */
class PFORM_Integration_Divi_Register {

	public function __construct()
	{
		$this->load();
		$this->allow_load();
	}
	/**
	 * Load integration
	 *
	 * @return bool
	 */
	public function allow_load() {

		if ( function_exists( 'et_divi_builder_init_plugin' ) ) {
			return true;
		}

		$allow_themes = [ 'Divi', 'Extra' ];
		$theme        = wp_get_theme();
		$theme_name   = $theme->get_template();
		$theme_parent = $theme->parent();

		return (bool) array_intersect( [ $theme_name, $theme_parent ], $allow_themes );
	}

	/**
	 * Load integration
	 */
	public function load() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'et_builder_ready', [ $this, 'register_module' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_styles' ], 5 );

		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_pieforms_divi_preview', [ $this, 'preview' ] );
		}

		if ( $this->is_divi_builder() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'builder_styles' ], 12 );
			add_action( 'wp_enqueue_scripts', [ $this, 'builder_scripts' ] );
		}
	}

	/**
	 * Check is div
	 *
	 * @return bool
	 */
	private function is_divi_builder() {

		return ! empty( $_GET['et_fb'] ); 
	}


	/**
	 * Get current style name.
	 * Overwrite st
	 *
	 * @return string
	 */
	public function get_current_styles_name() {

		$disable_css ='disable-css';
		if ( 1 === $disable_css ) {
			return 'full';
		}
		if ( 2 === $disable_css ) {
			return 'base';
		}

		return '';
	}

	/**
	 * Is the Divi 
	 *
	 * @return bool
	 */
	protected function is_divi_plugin_loaded() {

		if ( ! is_singular() ) {
			return false;
		}

		return function_exists( 'et_is_builder_plugin_active' );
	}

	/**
	 * Register frontend_styles
	 */
	public function frontend_styles() {

		if ( ! $this->is_divi_plugin_loaded() ) {
			return;
		}
	
	}

	/**
	 * Load styles 
	 */
	public function builder_styles() {

		wp_enqueue_style(
			'PieformsdiviCSS',
			Pie_Forms::$url . "assets/css/integration/divi/integration.css",
			null,
			Pie_Forms::VERSION
		);
	}

	/**
	 * Load scripts
	 */
	public function builder_scripts() {

		wp_enqueue_script(
			'PieformsdiviJS',
			Pie_Forms::$url . "assets/js/integration/divi/formselector.js",
			[ 'react', 'react-dom' ],
			Pie_Forms::VERSION,
			true
		);
		wp_localize_script(
			'PieformsdiviJS',
			'pieforms_divi_builder',
			[
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'pieforms_divi_builder' ),
				'placeholder'       => Pie_Forms::$url . "assets/images/integrations/divi/pieforms-icon.svg",
				'placeholder_title' => esc_html__( 'Pie Forms', 'pie-forms' ),
			]
		);
	}

	/**
	 * Register mod
	 */
	public function register_module() {
		if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}

		new PFORM_Integration_Divi_Module();
	}

	/**
	 * Ajax handler
	 */
	public function preview() {

		check_ajax_referer( 'pieforms_divi_builder', 'nonce' );

		$form_id    = absint( filter_input( INPUT_POST, 'form_id', FILTER_SANITIZE_NUMBER_INT ) );
		$show_title = 'on' === filter_input( INPUT_POST, 'show_title', FILTER_SANITIZE_STRING );
		$show_desc  = 'on' === filter_input( INPUT_POST, 'show_desc', FILTER_SANITIZE_STRING );

		add_filter(
			'pieforms_frontend_container_class',
			function ( $classes ) {

				$classes[] = 'pieforms-gutenberg-form-selector';
				$classes[] = 'pieforms-container-full';

				return $classes;
			}
		);
		add_action(
			'pieforms_frontend_output',
			function () {

				echo '<fieldset disabled>';
			},
			3
		);
		add_action(
			'pieforms_frontend_output',
			function () {

				echo '</fieldset>';
			},
			30
		);

		wp_send_json_success(
			do_shortcode(
				sprintf(

					'[pie_form id="%1$s" title="%2$s" description="%3$s"]',
					absint( $form_id ),
					(bool) apply_filters( 'pieforms_divi_builder_form_title', esc_html($show_title), absint($form_id) ),
					(bool) apply_filters( 'pieforms_divi_builder_form_desc', esc_html($show_desc), absint($form_id) )
				)
			)
		);
	}
}
