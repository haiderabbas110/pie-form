<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Form_Task {

	public $errors;

	public $form_fields;

	public $entry_id = 0;

	public $form_data = array();

	public function __construct() {
		add_action( 'wp', array( $this, 'listen_task' ) );
		add_action('pie_form_entry_save' , array( $this, 'entry_save' ) ,10 ,4);
	}

	public function listen_task() {
		if ( ! empty( $_GET['pie_forms_return'] ) ) { 
			$this->entry_confirmation_redirect( '', sanitize_key(wp_unslash( $_GET['pie_forms_return']) ) ); 
		}
		
		if ( ! empty( $_POST['pie_forms']['id'] ) ) { 
			$this->do_task( wp_unslash( sanitize_post($_POST['pie_forms']) ) ); 
		}
	}

	/**
	 * Do task of form entry
	 */
	public function do_task( $entry ) {
		try {
			$this->errors           = array();
			$this->form_fields      = array();
			$form_id                = absint( $entry['id'] );
			$data                   = Pie_Forms()->form()->get( $form_id );
			$form	 				= array_shift($data);
			$response_data          = array();
			$honeypot				= false;
			$this->ajax_err         = array();
			$this->entries 			= false;
			
			// Check nonce for form submission.
			if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'pie-forms_process_submit' ) ) { 
				$this->errors[ $form_id ]['header'] = esc_html__( 'We were unable to process your form, please try again.', 'pie-forms' );
				return $this->errors;
			}
			
			
			// Formatted form data for hooks.
			$this->form_data = apply_filters( 'pie_forms_process_before_form_data', Pie_Forms()->core()->pform_decode( wp_unslash($form->form_data) ), $entry );
				
			// Pre-process/validate hooks and filter. Data is not validated or cleaned yet so use with caution.
			$entry = apply_filters( 'pie_forms_process_before_filter', $entry, $this->form_data );
			
			do_action( 'pie_forms_process_before', $entry, $this->form_data );
			do_action( "pie_forms_process_before_{$form_id}", $entry, $this->form_data );
			
			$ajax_form_submission = isset( $this->form_data['settings']['ajax_form_submission'] ) ? $this->form_data['settings']['ajax_form_submission'] : 0;
			if ( '1' === $ajax_form_submission ) {

				// Prepare fields for entry_save.
				foreach ( $this->form_data['form_fields'] as $field ) {
					if ( '' === isset( $this->form_data['form_fields']['meta-key'] ) ) {
						continue;
					}
					
					$field_id     = $field['id'];
					$field_type   = $field['type'];
					$field_submit = isset( $entry['form_fields'][ $field_id ] ) ? $entry['form_fields'][ $field_id ] : '';

					$exclude = array( 'title', 'html', 'captcha');
					
					if ( ! in_array( $field_type, $exclude, true ) ) {
						$this->form_fields[ $field_id ] = array(
							'id'       => $field_id,
							'name'     => sanitize_text_field( $field['label'] ),
							'meta_key' => $this->form_data['form_fields'][ $field_id ]['meta-key'],
							'type'     => $field_type,
							'value'    => Pie_Forms()->core()->pform_sanitize_textarea_field( $field_submit ),
						);
					}
				}
			}

			// Validate fields.
			foreach ( $this->form_data['form_fields'] as $field ) {
				$field_id     	= $field['id'];
				$field_type   	= $field['type'];
				$field_submit 	= isset( $entry['form_fields'][ $field_id ] ) ? $entry['form_fields'][ $field_id ] : '';
				$disable_entry 	= isset( $this->form_data['settings']['disable_user_entry'] ) && '1' === $this->form_data['settings']['disable_user_entry'];
				
				// 'gdpr' === $field_type && $field_submit === "on" || - removed since	version_name
				if ( !$disable_entry ) {
					$this->entries = true;
				}


				do_action( "pie_forms_process_validate_{$field_type}", $field_id, $field_submit, $this->form_data, $field_type );

			}

			// If validation issues occur, send the results accordingly.
			if ( $ajax_form_submission && count( $this->ajax_err ) ) {
				$response_data['error']    = $this->ajax_err;
				$response_data['message']  = __( 'Form has not been submitted, please see the errors below.', 'pie-forms' );
				$response_data['response'] = 'error';
				return $response_data;
			}

			// reCAPTCHA check.
			$recaptcha_type      = get_option( 'pf_recaptcha_type', 'v2' );
			$invisible_recaptcha = get_option( 'pf_recaptcha_v2_invisible', 'no' );

			if ( 'v2' === $recaptcha_type && 'no' === $invisible_recaptcha ) {
				$site_key   = get_option( 'pf_recaptcha_v2_site_key' );
				$secret_key = get_option( 'pf_recaptcha_v2_secret_key' );
			} elseif ( 'v2' === $recaptcha_type && 'yes' === $invisible_recaptcha ) {
				$site_key   = get_option( 'pf_recaptcha_v2_invisible_site_key' );
				$secret_key = get_option( 'pf_recaptcha_v2_invisible_secret_key' );
			} elseif ( 'v3' === $recaptcha_type ) {
				$site_key   = get_option( 'pf_recaptcha_v3_site_key' );
				$secret_key = get_option( 'pf_recaptcha_v3_secret_key' );
			}


			if ( ! empty( $site_key ) && ! empty( $secret_key ) && isset( $this->form_data['settings']['recaptcha_support'] ) && '1' === $this->form_data['settings']['recaptcha_support'] ) {
				$error = esc_html__( 'Google reCAPTCHA verification failed, please try again later.', 'pie-forms' );
				$token = ! empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field(wp_unslash( $_POST['g-recaptcha-response']) ) : false;

				if ( 'v3' === $recaptcha_type ) {
					$token = ! empty( $_POST['pie_forms']['recaptcha'] ) ? sanitize_text_field(wp_unslash( $_POST['pie_forms']['recaptcha'] )) : false;
				}

				$raw_response = wp_safe_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $token );

				if ( ! is_wp_error( $raw_response ) ) {
					$response = json_decode( wp_remote_retrieve_body( $raw_response ) );

					// Check reCAPTCHA response.
					if ( empty( $response->success ) || ( 'v3' === $recaptcha_type && $response->score <= apply_filters( 'pie_forms_recaptcha_v3_threshold', '0.5' ) ) ) {
						if ( 'v3' === $recaptcha_type ) {
							if ( isset( $response->score ) ) {
								$error .= ' (' . esc_html( $response->score ) . ')';
							}
						}
						$this->errors[ $form_id ]['header'] = $error;
						return $this->errors;
					}
				}
			}


		

			
			// Early honeypot validation - before actual processing.
			if ( isset( $this->form_data['settings']['honeypot'] ) && '1' === $this->form_data['settings']['honeypot'] && ! empty( $entry['hp'] ) ) {
				$honeypot = esc_html__( 'Pie Forms honeypot field triggered.', 'pie-forms' );
			}

			$honeypot = apply_filters( 'pie_forms_process_honeypot', $honeypot, $this->form_fields, $entry, $this->form_data );

			// If spam - return early.
			if ( $honeypot ) {
				echo "error found of honepot from id ". esc_html(absint( $this->form_data['id'] ));
				return $this->errors;
			}

			// Pass the form created date into the form data.
			$this->form_data['created'] = $form->created_at;
			// Format fields.
				
			foreach ( (array) $this->form_data['form_fields'] as $field ) {
				$field_id     = $field['id'];
				$field_key    = isset( $field['meta-key'] ) ? $field['meta-key'] : '';
				$field_type   = $field['type'];
				$field_submit = isset( $entry['form_fields'][ $field_id ] ) ? $entry['form_fields'][ $field_id ] : '';

				do_action( "pie_forms_process_format_{$field_type}", $field_id, $field_submit, $this->form_data, $field_key );
			}
			

			// This hook is for internal purposes and should not be leveraged.
			do_action( 'pie_forms_process_format_after', $this->form_data );

			// Process hooks/filter - this is where most addons should hook
			// because at this point we have completed all field validation and
			// formatted the data.
			$this->form_fields = apply_filters( 'pie_forms_process_filter', $this->form_fields, $entry, $this->form_data );

			// Initial error check.
			$errors = apply_filters( 'pie_forms_process_initial_errors', $this->errors, $this->form_data );

			if ( ! empty( $errors[ $form_id ] ) ) {
				if ( empty( $errors[ $form_id ]['header'] ) ) {
					$errors[ $form_id ]['header'] = __( 'Form has not been submitted, please see the errors below.', 'pie-forms' );
				}
				$this->errors = $errors;
				return $this->errors;
			}
			
			do_action( 'pie_forms_process', $this->form_fields, $entry, $this->form_data );
			do_action( "pie_forms_process_{$form_id}", $this->form_fields, $entry, $this->form_data );

			$this->form_fields = apply_filters( 'pie_forms_process_after_filter', $this->form_fields, $entry, $this->form_data );
			
			// One last error check - don't proceed if there are any errors.
			if ( ! empty( $this->errors[ $form_id ] ) ) {
				if ( empty( $this->errors[ $form_id ]['header'] ) ) {
					$this->errors[ $form_id ]['header'] = esc_html__( 'Form has not been submitted, please see the errors below.', 'pie-forms' );
				}
				return $this->errors;
			}

			// if user entries setting is not disabled.
			if($this->entries){
				$entry_id = $this->entry_save( $this->form_fields, $entry, $this->form_data['id'], $this->form_fields );
				$entry['entry_id']	= $entry_id;
			}

			// Success - send email notification.
			$this->entry_email( $this->form_fields, $entry, $this->form_data, 'entry' );
			// Success - add entry to database.
			
			// @todo remove this way of printing notices.
			add_filter( 'pie_forms_success', array( $this, 'check_success_message' ), 10, 2 );
			
			// Pass completed and formatted fields in POST.
			$_POST['pie-forms']['complete'] = $this->form_fields;

			// Post-process hooks.
			do_action( 'pie_forms_process_complete', $this->form_fields, $entry, $this->form_data );
			do_action( "pie_forms_process_complete_{$form_id}", $this->form_fields, $entry, $this->form_data);
		} catch ( Exception $e ) {
			$e->getMessage();
		}

		$message = $this->pform_print_message();

		if ( '1' === $ajax_form_submission ) {
			$response_data['message']  = $message;
			$response_data['response'] = 'success';
			$settings                  = $this->form_data['settings'];

			// Backward Compatibility Check.
			switch ( $settings['redirect_to'] ) {
				case '0':
					$settings['redirect_to'] = 'same';
					break;

				case '1':
					$settings['redirect_to'] = 'custom_page';
					break;

				case '2':
					$settings['redirect_to'] = 'external_url';
					break;
			}

			if ( isset( $settings['redirect_to'] ) && 'external_url' === $settings['redirect_to'] ) {
				$response_data['redirect_url'] = isset( $settings['external_url'] ) ? esc_url( $settings['external_url'] ) : 'undefined';
			} elseif ( isset( $settings['redirect_to'] ) && 'custom_page' === $settings['redirect_to'] ) {
				$response_data['redirect_url'] = isset( $settings['custom_page'] ) ? get_page_link( absint( $settings['custom_page'] ) ) : 'undefined';
			}elseif( isset( $settings['redirect_to'] ) && 'download_url' === $settings['redirect_to'] ){
				$response_data['download_url'] = isset( $settings['download_url'] ) && !empty( $settings['download_url'] ) ? esc_url( $settings['download_url'] ) : 'undefined';
			}
			
			$response_data = apply_filters('pieforms_ajax_submission_reponse', $response_data);
			return $response_data;
		}
			add_filter('pie_form_success_message',array( $this, 'pform_print_message' ), 10, 2);

		$this->entry_confirmation_redirect( $this->form_data );
	}

	/**
	 * Saves entry to database.
	 */
	public function entry_save( $fields, $entry, $form_id, $form_data = array() ) {
		global $wpdb;


		
		do_action( 'pie_forms_process_entry_save', $fields, $entry, $form_id, $form_data );

		$fields      = apply_filters( 'pie_forms_entry_save_data', $fields, $entry, $form_data );
		$entry_id    = false;
		
		$entry_data = array(
			'form_id'         => $form_id,
			'fields'          => wp_json_encode( $fields ),
			'status'          => 'publish',
			'subscription'    	=> 1,
			'created_at'    => current_time( 'mysql' ),
			
		);
		
		if ( ! $entry_data['form_id'] ) {
			return new WP_Error( 'no-form-id', __( 'No form ID was found.', 'pie-forms' ) );
		}

		// Create entry.
		$success = $wpdb->insert( $wpdb->prefix . 'pf_entries', $entry_data );

		if ( is_wp_error( $success ) || ! $success ) {
			return new WP_Error( 'could-not-create', __( 'Could not create an entry', 'pie-forms' ) );
		}

		$entry_id = $wpdb->insert_id;

		// Create meta data.
		if ( $entry_id ) {
			foreach ( $fields as $field ) {
				$field = apply_filters( 'pie_forms_entry_save_fields', $field, $form_data, $entry_id );
				// Add only whitelisted fields to entry meta.
				if ( in_array( $field['type'], array( 'html', 'title' ), true ) ) {
					continue;
				}


				// If empty label is provided for choice field, don't store their data nor send email.
				if ( in_array( $field['type'], array( 'radio' ), true ) ) {
					if ( isset( $field['value']['label'] ) && '' === $field['value']['label'] ) {
						continue;
					}
				} elseif ( in_array( $field['type'], array( 'checkbox', ), true ) ) {
					if ( isset( $field['value']['label'] ) && ( empty( $field['value']['label'] ) || '' === current( $field['value']['label'] ) ) ) {
						continue;
					}
				}

				if ( isset( $field['meta_key'], $field['value'] ) && '' !== $field['value'] ) {
					$entry_metadata = array(
						'entry_id'   => $entry_id,
						'meta_key'   => sanitize_key( $field['meta_key'] ),
						'meta_value' => maybe_serialize( $field['value'] ), 
					);

					// Insert entry meta.
					$wpdb->insert( $wpdb->prefix . 'pf_entrymeta', $entry_metadata );
				}
			}
		}

		$this->entry_id = $entry_id;

		do_action( 'pie_forms_complete_entry_save', $entry_id, $fields, $entry, $form_id, $form_data );

		return $this->entry_id;
	}

	/**
	 * Process AJAX form submission.
	 */
	public function ajax_form_submission( $posted_data ) {
		add_filter( 'wp_redirect', array( $this, 'ajax_process_redirect' ), 999 );
		$process = $this->do_task( stripslashes_deep( $posted_data ) );
		return $process;
	}

	/**
	 * Check the sucessful message.
	 */
	public function check_success_message( $status, $form_id ) {
		if ( isset( $this->form_data['id'] ) && absint( $this->form_data['id'] ) === $form_id ) {
			return true;
		}
		return false;
	}

	public function pform_print_message(){

		$global_message = !empty(get_option('pf_global_success_message')) ? get_option('pf_global_success_message') : "Form has been submitted.";
		
		$message = ( isset( $this->form_data['settings']['successful_form_submission_message'] ) && !empty($this->form_data['settings']['successful_form_submission_message']) ) ? $this->form_data['settings']['successful_form_submission_message'] : __( esc_html($global_message), 'pie-forms' );
		$html = '<div class="pf-success-msg">';
			$html .= apply_filters('pie_forms_success_message_text', esc_html($message));
		$html .= '</div>';
		
		$html = apply_filters('pie_forms_quiz_result', $html);
		$html = apply_filters('pie_forms_authorizedotnet_msg', $html);

		return $html;
	}


	/**
	 * Redirects user to a page or URL specified in the form confirmation settings.
	 */
	public function entry_confirmation_redirect( $form_data = '', $hash = '' ) {
		
		$this->form_data = $form_data;
		$settings = $this->form_data['settings'];

		// Backward Compatibility Check.
		switch ( $settings['redirect_to'] ) {
			case '0':
				$settings['redirect_to'] = 'same';
				break;

			case '1':
				$settings['redirect_to'] = 'custom_page';
				break;

			case '2':
				$settings['redirect_to'] = 'external_url';
				break;
		}

		if ( isset( $settings['redirect_to'] ) && 'custom_page' === $settings['redirect_to'] ) {
			?>
				<script>	
				var redirect = '<?php echo esc_url( get_page_link( $settings['custom_page'] ) ); ?>';
				window.setTimeout( function () {
					window.location.href = redirect;
				})
				</script>
			<?php
		} elseif ( isset( $settings['redirect_to'] ) && 'external_url' === $settings['redirect_to'] ) {
			?>
			<script>
				window.setTimeout( function () {
					window.location.href = '<?php echo esc_url( $settings['external_url'] ); ?>';
				})
				</script>
			<?php
		}  elseif ( isset( $settings['redirect_to'] ) && 'download_url' === $settings['redirect_to'] && !empty( $settings['download_url'] ) ){
				add_action('wp_footer', array( $this, 'pform_download_file' ) , 100);
		}

	}

	public function pform_download_file(){
		$settings = $this->form_data['settings'];

		?>
		<script>
				const link = document.createElement('a');
				link.href = "<?php echo esc_url($settings['download_url']) ?>";
				// link.download = 'file';
				link.dispatchEvent(new MouseEvent('click'));
			</script>
		<?php
	}
	/**
	 * Sends entry email notifications.
	 */
	public function entry_email( $fields, $entry, $form_data, $context = '' ) {
		
		$fields = apply_filters( 'pie_forms_entry_email_data', $fields, $entry, $form_data );

		$notification = isset( $form_data['email'] ) ? $form_data['email'] : array();
		$email        = array();

		$pf_to_email = isset( $notification['pf_to_email'] ) ? $notification['pf_to_email'] : '';
		$email_copy  = get_option('pf_enable_email_copies');
		
		// SUBJECT
		$email['subject']        = ! empty( $notification['pf_email_subject'] ) ? $notification['pf_email_subject'] : sprintf( esc_html__( 'New %s Entry', 'pie-form' ), esc_html__( $form_data['email']['pf_email_subject'], 'pie-forms' ));

		// TO ADDRESS
		$email['address']        = explode( ',', apply_filters( 'pie_forms_process_smart_tags', $pf_to_email, $form_data, $fields, $this->entry_id ) );
		
		// TO ADDRESS SANITIZE
		$email['address']        = array_map( 'sanitize_email', $email['address'] );

		// SENDER NAME
		$email['sender_name']    = ! empty( $notification['pf_from_name'] ) ? $notification['pf_from_name'] : get_bloginfo( 'name' );
		
		// FROM EMAIL
		$email['from_email']     = ! empty( $notification['pf_from_email'] ) ? $notification['pf_from_email'] : get_option( 'admin_email' );

		// REPLY TO
		$email['reply_to']       = ! empty( $notification['pf_reply_to'] ) ? $notification['pf_reply_to'] : $email['from_email'];

		// CC
		$email['cc']       		 = ! empty( $notification['pf_carboncopy'] ) ? $notification['pf_carboncopy'] : '';

		// BCC
		$email['bcc']       	 = ! empty( $notification['pf_blindcarboncopy'] ) ? $notification['pf_blindcarboncopy'] : '';


		// MESSAGE BODY
		$email['message']        = ! empty( $notification['pf_email_message'] ) ? $notification['pf_email_message'] : '{all_fields}';
		
		$email                   = apply_filters( 'pie_forms_entry_email_atts', $email, $fields, $entry, $form_data );
		
		// Create new email.
		$emails = new PFORM_Email_Settings();
		$emails->__set( 'form_data', $form_data );
		$emails->__set( 'fields', $fields );
		$emails->__set( 'entry_id', '' );
		$emails->__set( 'from_name', $email['sender_name'] );
		$emails->__set( 'from_address', $email['from_email'] );
		$emails->__set( 'reply_to', $email['reply_to'] );
		
		// Maybe include Cc and Bcc email addresses.
		if ( 'yes' === $email_copy ) {
			if ( ! empty( $notification['pf_carboncopy'] ) ) {
				$emails->__set( 'cc', $email['cc'] );
			}
			if ( ! empty( $notification['pf_blindcarboncopy'] ) ) {
				$emails->__set( 'bcc', $email['bcc'] );
			}
		}
		// Send entry email.
		foreach ( $email['address'] as $address ) {
			
			$emails->send( trim( $address ), $email['subject'], $email['message'] );
		}


		// SEND EMAIL TO USER

		$pf_to_email_user 	= isset( $notification['pf_to_email_user'] ) ? $notification['pf_to_email_user'] : '';

		$email_user 		= array();

		// TO ADDRESS
		$email_user['user_email_address'] = explode( ',', apply_filters( 'pie_forms_process_smart_tags', $pf_to_email_user, $form_data, $fields, $this->entry_id ) );

		// TO ADDRESS SANITIZE
		$email_user['user_email_address'] = array_map( 'sanitize_email', $email_user['user_email_address'] );

		// SUBJECT
		$email_user['subject']            = ! empty( $notification['pf_email_user_subject'] ) ? $notification['pf_email_user_subject'] :  __('Thank Your For Your Entry', 'pie-forms');

		// SENDER NAME
		$email_user['sender_name']        = ! empty( $notification['pf_user_from_name'] ) ? $notification['pf_user_from_name'] : get_bloginfo( 'name' );
		
		// FROM EMAIL
		$email_user['from_email']  		  = ! empty( $notification['pf_user_from_email'] ) ? $notification['pf_user_from_email'] : get_option( 'admin_email' );
		

		$emails->__set( 'from_name', $email_user['sender_name'] );
		$emails->__set( 'from_address', $email_user['from_email'] );
		$emails->__set( 'reply_to', $email['reply_to'] );
		$emails->__set( 'cc', '' );
		$emails->__set( 'bcc', '' );
		$emails->__set( 'headers', '' );
		
		if(array_filter($email_user['user_email_address']) && $notification['pf_email_user_enable'] === "1"){
			foreach ( $email_user['user_email_address'] as $address ) {
				$emails->send( trim( $address ), $email_user['subject'], $notification['pf_email_user_message'] );
			}
		}

		
	}

}
