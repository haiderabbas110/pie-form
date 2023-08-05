<?php
defined( 'ABSPATH' ) || exit;

	class PFORM_Admin_Aboutus {

		/**
		 * Output admin fields.
		 */
		public static function output_info($settings) {
			echo wp_kses($settings, Pie_Forms()->core()->pform_get_allowed_tags());
		}
	}