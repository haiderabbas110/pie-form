<?php
defined( 'ABSPATH' ) || exit;

	class PFORM_Admin_Addons {

		/**
		 * Get section content for the extensions screen.
		 *
		 * @return array
		 */
		public static function get_extension_data() {

			$raw_extensions 	= Pie_Forms::$dir.'includes/templates/addons.json' ;

			$handle 			= fopen($raw_extensions, "r");
			$templates 			= fread($handle, filesize($raw_extensions));
			$extension_data     = json_decode(  $templates );
			
			if ( ! empty( $extension_data ) ) {
				set_transient( 'pf_extensions_section', $extension_data, WEEK_IN_SECONDS );
			}
			return apply_filters( 'pie_forms_extensions_section_data', $extension_data );
		}

	}