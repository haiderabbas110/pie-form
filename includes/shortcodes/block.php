<?php
defined( 'ABSPATH' ) || exit;

/**
 * Guten Block Class.
 */
class PFORM_Shortcodes_Block {

	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the block and its scripts.
	 */
	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		register_block_type(
			'pie-forms-for-wp/form-selector',
			array(
				'attributes'      => array(
					'formId'             => array(
						'type' => 'string',
					),
					'className'          => array(
						'type' => 'string',
					),
					'displayTitle'       => array(
						'type' => 'boolean',
					),
					'displayDescription' => array(
						'type' => 'boolean',
					),
				),
				'editor_style'    => 'pie-forms-block-editor',
				'editor_script'   => 'pie-forms-block-editor',
				'render_callback' => array( $this, 'get_form_html' ),
			)
		);
	}

	/**
	 * Load Gutenberg block scripts.
	 */
	public function enqueue_block_editor_assets() {
		wp_register_style(
			'pie-forms-block-editor',
			Pie_Forms()->plugin_url() . '/assets/css/gutenberg/pie-forms.css',
			array( 'wp-edit-blocks' ),
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( Pie_Forms::$dir . '/assets/css/gutenberg/pie-forms.css' ) : Pie_Forms::VERSION
		);

		wp_register_script(
			'pie-forms-block-editor',
			Pie_Forms()->plugin_url() . '/assets/js/gutenberg/form-block.js',
			array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-components' ),
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? filemtime( Pie_Forms::$dir . '/assets/js/gutenberg/form-block.js' ) : Pie_Forms::VERSION,
			true
		);

		$form_block_data = array(
			'forms' => Pie_forms()->form()->get_multiple(  ),
			'i18n'  => array(
				'title'            => esc_html__( 'Pie Forms', 'pie-forms' ),
				'description'      => esc_html__( 'Select and display one of your forms.', 'pie-forms' ),
				'form_keywords'    => array(
					esc_html__( 'form', 'pie-forms' ),
					esc_html__( 'contact', 'pie-forms' ),
					esc_html__( 'survey', 'pie-forms' ),
				),
				'form_select'      => esc_html__( 'Select a Form', 'pie-forms' ),
				'form_settings'    => esc_html__( 'Form Settings', 'pie-forms' ),
				'form_selected'    => esc_html__( 'Form', 'pie-forms' ),
				'show_title'       => esc_html__( 'Show Title', 'pie-forms' ),
				'show_description' => esc_html__( 'Show Description', 'pie-forms' ),
			),
		);
		wp_localize_script( 'pie-forms-block-editor', 'pf_form_block_data', $form_block_data );
	}

	/**
	 * Get form HTML to display in a Gutenberg block.
	 */
	public function get_form_html( $attr ) {
		$form_id = ! empty( $attr['formId'] ) ? absint( $attr['formId'] ) : 0;
		
		if ( empty( $form_id ) ) {
			return '';
		}

		// Wrapper classes.
		$classes = 'pie-forms';
		if ( isset( $attr['className'] ) ) {
			$classes .= ' ' . $attr['className'];
		}

		$is_gb_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
		$title        = ! empty( $attr['displayTitle'] ) ? true : false;
		$description  = ! empty( $attr['displayDescription'] ) ? true : false;

		// Disable form fields if called from the Gutenberg editor.
		if ( $is_gb_editor ) {
			add_filter(
				'pie_forms_frontend_container_class',
				function ( $classes ) {
					$classes[] = 'pf-gutenberg-form-selector';
					$classes[] = 'pf-container-full';
					return $classes;
				}
			);
			add_action(
				'pie_forms_frontend_output',
				function () {
					echo '<fieldset disabled>';
				},
				3
			);
			add_action(
				'pie_forms_frontend_output',
				function () {
					echo '</fieldset>';
				},
				30
			);
		}

		return PFORM_Shortcodes_Display::shortcode_wrapper(
			array( 'PFORM_Shortcodes_Form', 'output' ),
			array(
				'id'          => absint($form_id),
				'title'       => esc_html($title),
				'description' => esc_html($description),
			),
			array(
				'class' => Pie_Forms()->core()->pform_sanitize_classes( $classes ),
			)
		);
	}
}