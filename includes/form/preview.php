<?php

defined( 'ABSPATH' ) || exit;


class PFORM_Form_Preview {
	private static $form_id = 0;
	private static $in_content_filter = false;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		self::$form_id = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : 0; 

		if ( ! is_admin() && isset( $_GET['pf_preview'] ) ) {
			add_action( 'template_redirect', array( __CLASS__, 'form_preview_init' ) );
			add_filter( 'template_include', array( __CLASS__, 'template_include' ) );
		}
	}

	/**
	 * Limit page templates to singular pages only.
	 *
	 * @return string
	 */
	public static function template_include() {
		return locate_template( array( 'page.php', 'single.php', 'index.php' ) );
	}

	/**
	 * Hook in methods to enhance the form preview.
	 */
	public static function form_preview_init() {
		if ( ! is_user_logged_in() || is_admin() ) {
			return;
		}

		if ( 0 < self::$form_id ) {
			add_filter( 'the_content', array( __CLASS__, 'form_preview_content_filter' ) );
			add_filter( 'get_the_excerpt', array( __CLASS__, 'form_preview_content_filter' ) );
			add_filter( 'post_thumbnail_html', '__return_empty_string' );
		}
	}

	/**
	 * Filter the content and insert form preview content.
	 */
	public static function form_preview_content_filter( $content ) {
		if ( ! is_user_logged_in() || ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		self::$in_content_filter = true;

		// Remove the filter we're in to avoid nested calls.
		remove_filter( 'the_content', array( __CLASS__, 'form_preview_content_filter' ) );

			if ( function_exists( 'apply_shortcodes' ) ) {
				$content = apply_shortcodes( '[pie_form id="' . absint( self::$form_id ) . '"]' );
			} else {
				// @todo Remove when start supporting WP 5.4 or later.
				$content = do_shortcode( '[pie_form id="' . absint( self::$form_id ) . '"]' );
			}

		self::$in_content_filter = false;

		return $content;
	}
}


