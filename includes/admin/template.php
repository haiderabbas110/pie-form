<?php

defined( 'ABSPATH' ) || exit;


class PFORM_Admin_Template {


	public function __construct() {

	}


	public static function page_output() {
        
		 
		$templates       = array();
		$refresh_url     = add_query_arg(
			array(
				'page'               => 'pf-builder&create-form=1',
				'action'             => 'pf-template-refresh',
				'pf-template-nonce' => wp_create_nonce( 'refresh' ),
			),
			admin_url( 'admin.php' )
		);
		$category  = 'free'; 
		$templates = self::get_template_data( $category );
			return $templates;

}

/**
 * Get section content for the template screen.
 */
public static function get_template_data() {
		$template_data 	= Pie_Forms()->core()->templateJson();
		$handle 		= fopen($template_data, "r");
		$raw_templates 	= fread($handle, filesize($template_data));
				
		if ( ! is_wp_error( $raw_templates ) ) {
			$template_data = json_decode(  $raw_templates );

			if ( ! empty( $template_data->templates ) ) {
				set_transient( 'pf_template_section', $template_data, WEEK_IN_SECONDS );
			}
		}

		if ( ! empty( $template_data->templates ) ) {
			return apply_filters( 'pie_forms_template_section_data', $template_data->templates );
		}
	}

}