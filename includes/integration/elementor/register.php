<?php
use Elementor\Plugin as ElementorPlugin;
/**
 * Improve Elementor Compatibility.
*/
class PFORM_Integration_Elementor_Register {

    function __construct() {
        $this->hooks();
    }
    
	/**
     * Integration hooks.
	*/
    protected function hooks() {
        // Skip if Elementor is not available.
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return;
		}
        
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'preview_assets' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_assets' ] );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widget' ) );
	}

	/**
	 * Load assets in the preview panel.
	 */
	public function preview_assets() {

		if ( ! ElementorPlugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		wp_enqueue_style(
			'PieformsElementorCSS',
			Pie_Forms::$url . "assets/css/integration/elementor/integration.css",
			'',
			Pie_Forms::VERSION
		);

		// wp_enqueue_script(
		// 	'PieformsElementorJS',
		// 	Pie_Forms::$url . "assets/js/integration/elementor/integration.js",
		// 	[ 'elementor-frontend', 'jquery', 'wp-util' ],
		// 	Pie_Forms::VERSION
		// );
	}

	/**
	 * Load an integration css in the elementor document.
	 */
	public function editor_assets() {

		if ( empty( $_GET['action'] ) || $_GET['action'] !== 'elementor' ) {
			return;
		}

		wp_enqueue_style(
			'PieformsElementorCSS',
			Pie_Forms::$url . "assets/css/integration/elementor/integration.css",
			'',
			Pie_Forms::VERSION
		);

		// wp_enqueue_script(
		// 	'PieformsElementorJS',
		// 	Pie_Forms::$url . "assets/js/integration/elementor/integration.js",
		// );
	}

	/**
	 * Register Pie Forms Widget.
	 */
	public function register_widget() {

		ElementorPlugin::instance()->widgets_manager->register_widget_type( new PFORM_Integration_Elementor_Widget() );
	}

}
