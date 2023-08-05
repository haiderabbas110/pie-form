<?php

defined( 'ABSPATH' ) || exit;


class PFORM_Core_Ajax {


	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'save_form'               => false,
			'create_form'             => false,
			'get_next_id'             => false,
			'enabled_form'            => false,
			'ajax_form_submission'    => true,
			'activate_addon'       	  => true,
			'deactivate_addon'        => true,
			'install_addon'           => true
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_pie_forms_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {

				add_action( 'wp_ajax_nopriv_pie_forms_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				add_action( 'pf_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Ajax handler to get next form ID.
	 */
	public static function get_next_id() {

		$form_id = isset( $_POST['form_id'] ) ? sanitize_key(absint( $_POST['form_id'] )) : 0;
		if ( $form_id < 1 ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'Invalid form', 'pie-forms' ),
				)
			);
		}
		if ( ! current_user_can( apply_filters( 'pie_forms_manage_cap', 'manage_options' ) ) ) {
			wp_send_json_error();
		}
		$field_key      = PIE_Forms()->core()->field_unique_key( $form_id );
		$field_id_array = explode( '-', $field_key );
		$new_field_id   = ( $field_id_array[ count( $field_id_array ) - 1 ] + 1 );
		wp_send_json_success(
			array(
				'field_id'  => $new_field_id,
				'field_key' => $field_key,
			)
		);
	}

	/**
	 * Triggered when clicking the form toggle.
	 */
	public static function enabled_form() {
		// Run a security check.
		check_ajax_referer( 'pie_forms_enabled_form', 'security' );

		$form_id = isset( $_POST['form_id'] ) ? absint(sanitize_key( $_POST['form_id'] )) : 0;
		$enabled = isset( $_POST['enabled'] ) ? absint(sanitize_key( $_POST['enabled']) ) : 0;
		
		
		$form_fields =  Pie_Forms()->core()->get_form_fields($form_id);
	
		$form_fields['form_enabled'] = $enabled;
		
		Pie_Forms()->form()->update( $form_id, $form_fields );
	}

	/**
	 * AJAX create new form.
	 */
	public static function create_form() {
		
        if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) )
        die ( 'Nonce Error!');
        
		//$pie_form_name   = $_POST['form_name'] ;
		//$pie_form_template = $_POST['form_template'];
		
		$pie_form_name   = isset( $_POST['form_name'] ) ? sanitize_text_field( $_POST['form_name'] ) : esc_html__( __('Blank Form', 'pie-forms') );
		
		$pie_form_template = isset( $_POST['form_template'] ) ? sanitize_text_field( wp_unslash( $_POST['form_template'] ) ) : 'blank';
		

		global $wpdb;
								
		$form_id = Pie_Forms()->form()->insert_into_pf_forms( $pie_form_name );
							
		// TEMPLATES START
		$raw_templates 	= Pie_Forms()->core()->templateJson();
		$handle 		= fopen($raw_templates, "r");
		$templates 		= fread($handle, filesize($raw_templates));
		$templates     = json_decode(  $templates );

		if ( ! empty( $templates ) ) {
			
			foreach ( $templates->templates as $template_data ) {
				if ( $pie_form_template == $template_data->slug ) {
					$decoded = base64_decode( $template_data->settings );
					
					$unslesh = wp_unslash($decoded);
					
					$base64_decode = json_decode($unslesh);
				

					$base64_decode->id = $form_id;
					$base64_decode->settings->form_title = 	$pie_form_name;
				
						
					$data    = array(
						'form_id' 		=> $form_id,
						'form_title' 	=>  $pie_form_name,
						'form_data' 	=>  Pie_Forms()->core()->pform_encode($base64_decode),
					);
					$format = array('%d','%s','%s');
					$wpdb->insert( $wpdb->prefix . 'pf_fields', $data, $format );
				
					}

				
			}
		}

        echo json_encode($form_id);
        exit();

		wp_send_json_error(
			array(
				'error' => esc_html__( 'Something went wrong, please try again later', 'pie-forms' ),
			)
		);
	}
	/**
	 * AJAX Form save.
	 */
	public static function save_form() {

		check_ajax_referer( 'pf_save_form_nonce', 'security' );
		
		// Check for form data.
		if ( empty( $_POST['form_data'] ) ) {
			die( esc_html__( 'No data provided', 'pie-forms' ) );
		}
		
		$form_post = json_decode( sanitize_post(stripslashes( $_POST['form_data'] )) );
		
		$data = array();
		

		if ( ! is_null( $form_post ) && $form_post ) {
			foreach ( $form_post as $post_input_data ) {
				preg_match( '#([^\[]*)(\[(.+)\])?#', $post_input_data->name, $matches );

				$array_bits = array( $matches[1] );

				if ( isset( $matches[3] ) ) {
					$array_bits = array_merge( $array_bits, explode( '][', $matches[3] ) );
				}

				$new_post_data = array();

				for ( $i = count( $array_bits ) - 1; $i >= 0; $i -- ) {
					if ( count( $array_bits ) - 1 === $i ) {
						$new_post_data[ $array_bits[ $i ] ] = wp_slash( $post_input_data->value );
					} else {
						$new_post_data = array(
							$array_bits[ $i ] => $new_post_data,
						);
					}
				}

				$data = array_replace_recursive( $data, $new_post_data );
			}
		}

		// Check for empty meta key.
		$empty_meta_data = array();
		if ( ! empty( $data['form_fields'] ) ) {
			foreach ( $data['form_fields'] as $field_key => $field ) {
				if ( ! empty( $field['label'] ) ) {
					// Only allow specific html in label.
					$data['form_fields'][ $field_key ]['label'] = wp_kses(
						$field['label'],
						array(
							'a'      => array(
								'href'  => array(),
								'class' => array(),
							),
							'span'   => array(
								'class' => array(),
							),
							'em'     => array(),
							'small'  => array(),
							'strong' => array(),
						)
					);

					// Register string for translation.
					Pie_Forms()->core()->pie_string_translation( $data['id'], $field['id'], $field['label'] );
				}

				if ( empty( $field['meta-key'] ) && ! in_array( $field['type'], array( 'html', 'title', 'captcha' ), true ) ) {
					$empty_meta_data[] = $field['label'];
				}
			}
			
			if ( ! empty( $empty_meta_data ) ) {
				wp_send_json_error(
					array(
						'errorTitle'   => esc_html__( 'Meta Key missing', 'pie-forms' ),
						/* translators: %s: empty meta data */
						'errorMessage' => sprintf( esc_html__( 'Please add Meta key for fields: %s', 'pie-forms' ), '<strong>' . implode( ', ', $empty_meta_data ) . '</strong>' ),
					)
				);
			}
		}


		// Fix for sorting field ordering.
		if ( isset( $data['structure'], $data['form_fields'] ) ) {
			$structure           = Pie_Forms()->core()->pform_flatten_array( $data['structure'] );
			$data['form_fields'] = array_merge( array_intersect_key( array_flip( $structure ), $data['form_fields'] ), $data['form_fields'] );
		}
		$form_id = Pie_Forms()->form()->update( $data['id'], $data );
		do_action( 'pie_forms_save_form', $form_id, $data );

		if ( ! $form_id ) {
			wp_send_json_error(
				array(
					'errorTitle'   => esc_html__( 'Form not found', 'pie-forms' ),
					'errorMessage' => esc_html__( 'An error occurred while saving the form.', 'pie-forms' ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'form_name'    => esc_html( $data['settings']['form_title'] ),
				)
			);
		}
	}


		/**
	 * Ajax handler for form submission.
	 */
	public static function ajax_form_submission() {
	
		check_ajax_referer( 'pie_forms_ajax_form_submission', 'security' );
		if ( ! empty( $_POST['pie_forms']['id'] ) ) {
			$process = Pie_Forms()->task->ajax_form_submission( stripslashes_deep( sanitize_post($_POST['pie_forms']) ) ); 
			if ( 'success' === $process['response'] ) {
				wp_send_json_success( $process );
			}

			wp_send_json_error( $process );
		}
	}

	/**
	 * AJAX Activate Addon.
	 */
	public static function activate_addon() {

		check_ajax_referer( 'pf_addons_nonce', 'security' );

		// Check for permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'pie-forms' ) );
		}
	
		if ( isset( $_POST['plugin'] ) ) {
		
			if ( ! empty( $_POST['type'] ) ) {
				$type = sanitize_key( $_POST['type'] );
			}
	
			$plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
			$activate = activate_plugins( $plugin );
	
			// do_action( 'pieforms_plugin_activated', $plugin );
	
			if ( ! is_wp_error( $activate ) ) {
				if ( 'plugin' === $type ) {
					wp_send_json_success( esc_html__( 'Plugin activated.', 'pie-forms' ) );
				} else {
					wp_send_json_success( esc_html__( 'Addon activated.', 'pie-forms' ) );
				}
			}
		}
	
		wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'pie-forms' ) );
	}

	/**
	 * AJAX Deactivate Addon.
	 */
	public static function deactivate_addon() {
	
		check_ajax_referer( 'pf_addons_nonce', 'security' );

		// Check for permissions.
		if ( ! current_user_can( 'deactivate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Plugin deactivation is disabled for you on this site.', 'pie-forms' ) );
		}
	
		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}
	
		if ( isset( $_POST['plugin'] ) ) {
			$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
	
			deactivate_plugins( $plugin );
	
			do_action( 'pieforms_plugin_deactivated', $plugin );
	
			if ( 'plugin' === $type ) {
				wp_send_json_success( esc_html__( 'Plugin deactivated.', 'pie-forms' ) );
			} else {
				wp_send_json_success( esc_html__( 'Addon deactivated.', 'pie-forms' ) );
			}
		}
	
		wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'pie-forms' ) );
	}

	/**
	 * AJAX Install Addon.
	 */	
	public static function install_addon() {

		// Run a security check.
		if ( ! check_ajax_referer( 'pf_install_nonce', 'security', false ) ) {
			wp_send_json_error( esc_html__( 'Your session expired. Please reload the page.', 'pie-forms' ) );
		}
		
		$generic_error = esc_html__( 'There was an error while performing your request.', 'pie-forms' );
		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}
	
		$error = esc_html__( 'Could not install addon. Please download from pieforms.com and install manually.', 'pie-forms' );
	
		if ( empty( $_POST['plugin'] ) ) {
			wp_send_json_error( $error );
		}
		  
		// Set the current screen to avoid undefined notices.
		set_current_screen( 'pie-forms_page_pf-aboutus' );
		
		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'pie-forms_page_pf-aboutus',
				),
				admin_url( 'admin.php' )
			)
		);
		
	
		$creds = request_filesystem_credentials( $url, '', false, false, null );
	
		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( $error );
		}
	
		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( $error );
		}
	
		/*
		 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		 */
		require_once(plugin_dir_path( __FILE__ ) . '/helper/install-skin.php');
		require_once(plugin_dir_path( __FILE__ ) . '/helper/plugin-silent-upgrader.php');
	
		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
	
		// Create the plugin upgrader with our custom skin.
		$installer = new PFORM_Helper_PluginSilentUpgrader( new PFORM_Helper_Install_Skin() );
	
		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $_POST['plugin'] ) ) {
			wp_send_json_error( $error );
		}
	
		$installer->install( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) );
	
		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();
	
		$plugin_basename = $installer->plugin_info();
	
		if ( empty( $plugin_basename ) ) {
			wp_send_json_error( $error );
		}
	
		$result = array(
			'msg'          => $generic_error,
			'is_activated' => false,
			'basename'     => $plugin_basename,
		);
	
		// Check for permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			$result['msg'] = 'plugin' === $type ? esc_html__( 'Plugin installed.', 'pie-forms' ) : esc_html__( 'Addon installed.', 'pie-forms' );
	
			wp_send_json_success( $result );
		}
	
		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename );
	
		if ( ! is_wp_error( $activated ) ) {
			$result['is_activated'] = true;
			$result['msg']          = 'plugin' === $type ? esc_html__( 'Plugin installed & activated.', 'pie-forms' ) : esc_html__( 'Addon installed & activated.', 'pie-forms' );
	
			wp_send_json_success( $result );
		}
	
		// Fallback error just in case.
		wp_send_json_error( $result );
	}

}

PFORM_Core_Ajax::init();
