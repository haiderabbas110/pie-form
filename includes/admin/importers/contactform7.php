<?php
/**
 * Contact Form 7 Importer class.
 *
 */
class PFORM_Admin_Importers_Contactform7 extends PFORM_Abstracts_Importer {

	/**
	 * @inheritdoc
	 */
	public function init() {

		$this->name = 'Contact Form 7';
		$this->slug = 'contact-form-7';
		$this->path = 'contact-form-7/wp-contact-form-7.php';
	}

	/**
	 * @inheritdoc
	 */
	public function get_forms() {

		$forms_final = array();

		if ( ! $this->is_active() ) {
			return $forms_final;
		}

		$forms = WPCF7_ContactForm::find( array(
			'posts_per_page' => - 1,
		) );

		if ( ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				if ( ! empty( $form ) && ( $form instanceof WPCF7_ContactForm ) ) {
					$forms_final[ $form->id() ] = $form->title();
				}
			}
		}

		return $forms_final;
	}

	/**
	 * Get a single form.
	 *
	 *
	 * @param int $id Form ID.
	 *
	 * @return WPCF7_ContactForm|bool
	 */
	public function get_form( $id ) {

		$form = WPCF7_ContactForm::find( array(
			'posts_per_page' => 1,
			'p'              => $id,
		) );

		if ( ! empty( $form[0] ) && ( $form[0] instanceof WPCF7_ContactForm ) ) {
			return $form[0];
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function import_form() {

		// Run a security check.
		check_ajax_referer( 'pieforms-admin', 'nonce' );
		
		
		// Define some basic information.
		$analyze  = isset( $_POST['analyze'] );
		$cf7_id   = ! empty( $_POST['form_id'] ) ? (int) sanitize_key($_POST['form_id']) : 0;
		$cf7_form = $this->get_form( $cf7_id );
		
		if ( ! $cf7_form ) {
			wp_send_json_error( array(
				'error' => true,
				'name'  => esc_html__( 'Unknown Form', 'pie-forms' ),
				'msg'   => esc_html__( 'The form you are trying to import does not exist.', 'pie-forms' ),
			) );
		}

		$cf7_form_name      = $cf7_form->title();
		$cf7_fields         = $cf7_form->scan_form_tags();
		$cf7_properties     = $cf7_form->get_properties();
		$cf7_recaptcha      = false;
		$fields_pro_plain   = array( );
		$fields_pro_omit    = array( 'file' );
		$fields_unsupported = array( 'quiz', 'hidden' );
		$upgrade_plain      = array();
		$upgrade_omit       = array();
		$unsupported        = array();
		
		$form               = array(
			'id'       		=> '',
			'form_enabled'	=> '1',
			'form_field_id' => '',
			'shortcode'		=> '',
			'form_fields'   => array(),
			'settings' => array(
				'form_title' 							=> $cf7_form_name,
				'enable_title' 							=> 0,
				'form_description' 						=>  '',
				'enable_description' 					=> 0 ,
				'successful_form_submission_message' 	=> '',
				'redirect_to' 							=> 'same',
				'custom_page' 							=> '2',
				'external_url' 							=> '',
				'layout_class' 							=> 'one-column',
				'form_class' 							=> '',
				'submit_button_text' 					=> esc_html__('Submit','pie-forms'),
				'submit_button_processing_text' 		=> esc_html__('Processing','pie-forms') ,
				'submit_button_class' 					=> '',
				'honeypot' 								=> '1',
				'hide_all_label' 						=> '0',
				'label_to_placeholder' 					=> '0' ,
				'ajax_form_submission' 					=> '0' ,
				'form_limit_device' 					=> '' ,
				
				'import_form_id'         => $cf7_id,
			),
			'email' =>  array (
				'pf_to_email' => '{admin_email}' ,
				'pf_from_name' => 'Pie Forms' ,
				'pf_from_email' => '{admin_email}' ,
				'pf_reply_to' => '' ,
				'pf_carboncopy' => '' ,
				'pf_blindcarboncopy' => '' ,
				'pf_email_subject' => 'Blank Form' ,
				'pf_email_message' => '{all_fields}' ,
				'pf_email_user_enable' => '' ,
				'pf_to_email_user' => '' ,
				'pf_email_user_subject' => 'Thank Your For Your Entry - ' . $cf7_form_name ,
				'pf_email_user_message' => '' ,
			),
			'structure' => array(),
		);

		

		// If form does not contain fields, bail.
		if ( empty( $cf7_fields ) ) {
			wp_send_json_success( array(
				'error' => true,
				'name'  => sanitize_text_field( $cf7_form_name ),
				'msg'   => esc_html__( 'No form fields found.', 'pie-forms' ),
			) );
		}
		

		// Convert fields.
		foreach ( $cf7_fields as $key => $cf7_field ) {

			if ( ! $cf7_field instanceof WPCF7_FormTag ) {
				continue;
			}
			// Try to determine field label to use.
			$label = $this->get_field_label( $cf7_properties['form'], $cf7_field->type, $cf7_field->name );
			
			// Next, check if field is unsupported. If supported make note and
			// then continue to the next field.
			if ( in_array( $cf7_field->basetype, $fields_unsupported, true ) ) {
				$unsupported[] = $label;
				continue;
			}
			

			// Now check if this install is Lite. If it is Lite and it's a
			// field type not included, make a note then continue to the next
			// field.
			if ( ! Pie_Forms()->pro && in_array( $cf7_field->basetype, $fields_pro_plain, true ) ) {
				$upgrade_plain[] = $label;
			}
			if ( ! Pie_Forms()->pro && in_array( $cf7_field->basetype, $fields_pro_omit, true ) ) {
				$upgrade_omit[] = $label;
				continue;
			}
		
		

			// Determine next field ID to assign.
			
			$field_id = Pie_Forms()->core()->field_unique_key( sanitize_key(wp_unslash( $_POST['form_id'] )) );
			$field['label'] = $label;
			switch ( $cf7_field->basetype ) {

				// Plain text, email, URL, number, and textarea fields.
				case 'text':
				case 'email':
				case 'url':
				case 'number':
				case 'textarea':
					
					$type = $cf7_field->basetype;
					// if ( 'url' === $type ) {
					// 	$type = 'text';
					// }
					$form['form_fields'][ $field_id ] = array(
						'id'            => $field_id,
						'type'          => $type,
						'label'         => $label,
						'meta-key' 		=> Pie_Forms()->core()->pform_get_meta_key_field_option( $field ),
						'size'          => 'medium',
						'required'      => $cf7_field->is_required() ? '1' : '',
						'placeholder'   => $this->get_field_placeholder_default( $cf7_field ),
						'default_value' => $this->get_field_placeholder_default( $cf7_field, 'default' ),
						'css'			=> $this->get_field_option_default( $cf7_field , "class" ),
						'min_value'		=> $this->get_field_option_default( $cf7_field , "min" ),
						'max_value'		=> $this->get_field_option_default( $cf7_field , "max" ),
						'cf7_name'      => $cf7_field->name,
						

          
          
					);
					
					break;
				
				// Phone number field.
				case 'tel':
					$form['form_fields'][ $field_id ] = array(
						'id'            => $field_id,
						'type'          => 'phone',
						'label'         => $label,
						'format'        => 'international',
						'size'          => 'medium',
						'required'      => $cf7_field->is_required() ? '1' : '',
						'placeholder'   => $this->get_field_placeholder_default( $cf7_field ),
						'default_value' => $this->get_field_placeholder_default( $cf7_field, 'default' ),
						'css'			=> $this->get_field_option_default( $cf7_field, "class" ),
						'cf7_name'      => $cf7_field->name,
					);
					
					break;

				// Date field.
				case 'date':
					
					$type = 'date';

					$form['form_fields'][ $field_id ] = array(
						'id'               => $field_id,
						'type'             => $type,
						'label'            => $label,
						'format'           => 'date',
						'size'             => 'medium',
						'required'         => $cf7_field->is_required() ? '1' : '',
						'date_placeholder' => '',
						'date_format'      => 'm/d/Y',
						'date_type'        => 'datepicker',
						'time_format'      => 'g:i A',
						'time_interval'    => 30,
						'cf7_name'         => $cf7_field->name,
						'placeholder'      => $this->get_field_placeholder_default( $cf7_field ),
						'default_value'    => $this->get_field_placeholder_default( $cf7_field, 'default' ),
						'css'			   => $this->get_field_option_default( $cf7_field, "class" ),
					);
					break;

				// Select, radio, and checkbox fields.
				case 'select':
				case 'radio':
				case 'checkbox':
					$choices = array();
					$options = (array) $cf7_field->labels;

					foreach ( $options as $option ) {
						$choices[] = array(
							'label' => $option,
							'value' => '',
						);
					}

					$form['form_fields'][ $field_id ] = array(
						'id'       => $field_id,
						'type'     => $cf7_field->basetype,
						'label'    => $label,
						'choices'  => $choices,
						'size'     => 'medium',
						'required' => $cf7_field->is_required() ? '1' : '',
						'cf7_name' => $cf7_field->name,
						'css'	 	=> $this->get_field_option_default( $cf7_field, "class" ),
					);

					if ( 'select' === $cf7_field->basetype && $cf7_field->has_option( 'include_blank' ) ) {
						$form['form_fields'][ $field_id ]['placeholder'] = '---';
					}
					break;

				// File upload field.
				case 'file':
					$extensions = '';
					$max_size   = '';
					$file_types = $cf7_field->get_option( 'filetypes' );
					$limit      = $cf7_field->get_option( 'limit' );

					if ( ! empty( $file_types[0] ) ) {
						$extensions = implode( ',', explode( '|', strtolower( preg_replace( '/[^A-Za-z0-9|]/', '', strtolower( $file_types[0] ) ) ) ) );
					}

					if ( ! empty( $limit[0] ) ) {
						$limit = $limit[0];
						$mb    = ( strpos( $limit, 'm' ) !== false );
						$kb    = ( strpos( $limit, 'kb' ) !== false );
						$limit = (int) preg_replace( '/[^0-9]/', '', $limit );
						if ( $mb ) {
							$max_size = $limit;
						} elseif ( $kb ) {
							$max_size = round( $limit / 1024, 1 );
						} else {
							$max_size = round( $limit / 1048576, 1 );
						}
					}

					$form['form_fields'][ $field_id ] = array(
						'id'         => $field_id,
						'type'       => 'fileupload',
						'label'      => $label,
						'size'       => 'medium',
						'allowed_file_extension' => $extensions,
						'max_file_size'   => $max_size,
						'max_uploads'=> '',
						'required'   => $cf7_field->is_required() ? '1' : '',
						'cf7_name'   => $cf7_field->name,
						'css'			=> $this->get_field_option_default( $cf7_field, "class" ),
					);
					break;

				// Acceptance field.
				case 'acceptance':
					$form['form_fields'][ $field_id ] = array(
						'id'         => $field_id,
						'type'       => 'checkbox',
						'label'      => esc_html__( 'Acceptance Field', 'pie-forms' ),
						'choices'    => array(
							1 => array(
								'label' => $label,
								'value' => '',
							),
						),
						'size'       => 'medium',
						'required'   => '1',
						'label_hide' => '1',
						'cf7_name'   => $cf7_field->name,
					);
					break;

				// ReCAPTCHA field.
				case 'recaptcha':
					$cf7_recaptcha = true;
			}
			
			if($cf7_field->type != "submit"){

				$form['structure']['list_'.$key] =  $field_id;
			}
		}

		// If we are only analyzing the form, we can stop here and return the
		// details about this form.
		if ( $analyze ) {
			wp_send_json_success( array(
				'name'          => $cf7_form_name,
				'upgrade_plain' => $upgrade_plain,
				'upgrade_omit'  => $upgrade_omit,
			) );
		}
		
		// Settings.
		// Confirmation message.
		if ( ! empty( $cf7_properties['messages']['mail_sent_ok'] ) ) {
			$form['settings']['successful_form_submission_message'] = $cf7_properties['messages']['mail_sent_ok'];
		}
		
		// ReCAPTCHA.
		if ( $cf7_recaptcha ) {

			
			$recaptcha_type   = get_option( 'pf_recaptcha_type', 'v2' );

			// If the user has already defined v2 reCAPTCHA keys in the Pie Forms
			// settings, use those.
			$site_key   = get_option( 'pf_recaptcha_' . $recaptcha_type . '_site_key' );
			$secret_key = get_option( 'pf_recaptcha_' . $recaptcha_type . '_secret_key' );
			$type       = get_option( 'pf_recaptcha_type', 'v2' );


			$form['settings']['recaptcha_support'] = 1;
			
			
			
			// Try to abstract keys from CF7.
			if ( empty( $site_key ) || empty( $secret_key ) ) {
				$cf7_settings = get_option( 'wpcf7' );
				if ( ! empty( $cf7_settings['recaptcha'] ) && is_array( $cf7_settings['recaptcha'] ) ) {
					foreach ( $cf7_settings['recaptcha'] as $key => $val ) {
						if ( ! empty( $key ) && ! empty( $val ) ) {
							$site_key   = $key;
							$secret_key = $val;
						}
					}
				
					update_option( 'pf_recaptcha_' . $recaptcha_type . '_site_key', $site_key );
					update_option( 'pf_recaptcha_' . $recaptcha_type . '_secret_key', $secret_key );
				}
			}

		}
		

		// Setup email notifications.
		if ( ! empty( $cf7_properties['mail']['subject'] ) ) {
			$form['email']['pf_email_subject'] = $this->get_smarttags( $cf7_properties['mail']['subject'], $form['form_fields'] );
		}
		
		if ( ! empty( $cf7_properties['mail']['recipient'] ) ) {
			$form['email']['pf_to_email'] = $this->get_smarttags( $cf7_properties['mail']['recipient'], $form['form_fields'] );

		}

		if ( ! empty( $cf7_properties['mail']['body'] ) ) {
			$form['email']['pf_email_message'] = $this->get_smarttags( $cf7_properties['mail']['body'], $form['form_fields'] );

		}

		if ( ! empty( $cf7_properties['mail']['additional_headers'] ) ) {
			
			$form['email']['pf_reply_to'] = $this->get_replyto( $cf7_properties['mail']['additional_headers'], $form['form_fields'] );
			$form['email']['pf_carboncopy'] = $this->get_copies( $cf7_properties['mail']['additional_headers'], "CC" );
			$form['email']['pf_blindcarboncopy'] = $this->get_copies( $cf7_properties['mail']['additional_headers'], "BCC" );

		}
		if ( ! empty( $cf7_properties['mail']['sender'] ) ) {
			$sender = $this->get_sender_details( $cf7_properties['mail']['sender'], $form['form_fields'] );
			if ( $sender ) {
				$form['settings']['email']['sender_name']    = $sender['name'];
				$form['settings']['email']['sender_address'] = $sender['address'];
			}
		}

		if ( ! empty( $cf7_properties['mail_2'] ) && '1' == $cf7_properties['mail_2']['active'] ) {
			// Check if a secondary notification is enabled, if so set defaults
			// and set it up.

			//enable user email
			$form['email']['pf_email_user_enable'] = "1";

			
			if ( ! empty( $cf7_properties['mail_2']['subject'] ) ) {
				$form['email']['pf_email_user_subject'] = $this->get_smarttags( $cf7_properties['mail_2']['subject'], $form['form_fields'] );
			}

			if ( ! empty( $cf7_properties['mail_2']['recipient'] ) ) {
				$form['email']['pf_to_email_user'] = $this->get_smarttags( $cf7_properties['mail_2']['recipient'], $form['form_fields'] );

			}

			if ( ! empty( $cf7_properties['mail_2']['body'] ) ) {
				$form['email']['pf_email_user_message'] = $this->get_smarttags( $cf7_properties['mail_2']['body'], $form['form_fields'] );
			}
			

		}
		

		$this->add_form( $form, $unsupported, $upgrade_plain, $upgrade_omit );
	}

	/**
	 * Lookup and return the placeholder or default value.
	 *
	 * @param object $field Field object.
	 * @param string $type Type of the field.
	 *
	 * @return string
	 */
	public function get_field_placeholder_default( $field, $type = 'placeholder' ) {

		$placeholder   = '';
		$default_value = (string) reset( $field->values );

		if ( $field->has_option( 'placeholder' ) || $field->has_option( 'watermark' ) ) {
			$placeholder   = $default_value;
			$default_value = '';
		}

		if ( 'placeholder' === $type ) {
			return $placeholder;
		}

		return $default_value;
	}

	/**
	 * Lookup and return the placeholder or default value.
	 *
	 * @param object $field Field object.
	 * @param string $type Type of the field.
	 *
	 * @return string
	 */
	public function get_field_option_default( $field, $type = 'class' ) {

		$result = array();
		foreach ($field['options'] as $key => $value) {
			
			if($type == "min" || $type == "max"){
				$str_pattern = "([0-9-]+)";	
			}else{
				$str_pattern = "([a-z-]+)";
			}

			$pattern = '/'.$type.':'.$str_pattern.'/i';

			preg_match($pattern, $value, $matches);
		
			$result[] = $matches[1];
			
		}
		return trim(implode(' ',$result));
		
	}

	/**
	 * Get the field label.
	 *
	 * @param string $form Form data and settings.
	 * @param string $type Field type.
	 * @param string $name Field name.
	 *
	 */
	public function get_field_label( $form, $type, $name = '' ) {

		preg_match_all( '/<label>([ \w\S\r\n\t]+?)<\/label>/', $form, $matches );

		foreach ( $matches[1] as $match ) {

			$match = trim( str_replace( "\n", '', $match ) );

			preg_match( '/\[(?:' . preg_quote( $type ) . ') ' . $name . '(?:[ ](.*?))?(?:[\r\n\t ](\/))?\]/', $match, $input_match );

			if ( ! empty( $input_match[0] ) ) {
				return strip_shortcodes( sanitize_text_field( str_replace( $input_match[0], '', $match ) ) );
			}
		}

		$label = sprintf(
			/* translators: %1$s - field type; %2$s - field name if available. */
			esc_html__( '%1$s Field %2$s', 'pie-forms' ),
			ucfirst( $type ),
			! empty( $name ) ? "($name)" : ''
		);

		return trim( $label );
	}

	/**
	 * @inheritdoc
	 */
	public function get_smarttags( $string, $fields ) {

		preg_match_all( '/\[(.+?)\]/', $string, $tags );
		
		if ( empty( $tags[1] ) ) {
			return $string;
		}

		// Process form-tags and mail-tags.
		foreach ( $tags[1] as $tag ) {
			foreach ( $fields as $field ) {
				$labelname = lcfirst(ucwords($field['label']));

				$label =  str_replace( ' ', '', $labelname )."_{$field['id']}";
				
				if ( ! empty( $field['cf7_name'] ) && $field['cf7_name'] === $tag ) {
					$string = str_replace( '<[' . $tag . ']>', '{field_id="' .$label . '"}', $string );
					$string = str_replace( '[' . $tag . ']', '{field_id="' .$label . '"}', $string );
				}
			}
		}

		// Process CF7 tags that we can map with Pie Forms alternatives.
		$string = str_replace(
			[
				'[_remote_ip]',
				'[_date]',
				'[_serial_number]',
				'[_post_id]',
				'[_post_title]',
				'[_post_url]',
				'[_url]',
				'[_post_author]',
				'[_post_author_email]',
				'[_site_admin_email]',
				'[_user_login]',
				'[_user_email]',
				'[_user_first_name]',
				'[_user_last_name]',
				'[_user_nickname]',
				'[_user_display_name]',
			],
			[
				'{user_ip}',
				'{date format="m/d/Y"}',
				'{entry_id}',
				'{page_id}',
				'{page_title}',
				'{page_url}',
				'{page_url}',
				'{author_display}',
				'{author_email}',
				'{admin_email}',
				'{user_display}',
				'{user_email}',
				'{user_first_name}',
				'{user_last_name}',
				'{user_display}',
				'{user_full_name}',
			],
			$string
		);

		// Replace those CF7 that are used in Notifications by default and that we can't leave empty.
		$string = str_replace(
			[
				'[_site_title]',
				'[_site_description]',
				'[_site_url]',
			],
			[
				get_bloginfo( 'name' ),
				get_bloginfo( 'description' ),
				get_bloginfo( 'url' ),
			],
			$string
		);

		
		return $string;

	}

	/**
	 * Find Reply-To in headers if provided.
	 *
	 * @param string $headers CF7 email headers.
	 * @param array  $fields List of fields.
	 */
	public function get_replyto( $headers, $fields ) {

		if ( strpos( $headers, 'Reply-To:' ) !== false ) {

			preg_match( '/Reply-To: \[(.+?)\]/', $headers, $tag );

			if ( ! empty( $tag[1] ) ) {
				foreach ( $fields as $field ) {
					
					$labelname = lcfirst(ucwords($field['label']));
					$label =  str_replace( ' ', '', $labelname )."_{$field['id']}";

					if ( ! empty( $field['cf7_name'] ) && $field['cf7_name'] === $tag[1] ) {
						return  '{field_id="' . $label . '"}';
					}
				}
			}
		}

		return '';
	}

	/**
	 * Find Copies in headers if provided.
	 *
	 * @param string $headers CF7 email headers.
	 * @param array  $Matches copies.
	 */
	public function get_copies( $headers, $copies) {
		$email_pattern = "([\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+)";
		$_copies = '/'.$copies.': '.$email_pattern.'/i';
		

		preg_match($_copies, $headers, $matches);
		
		if ( ! empty( $matches[1] ) ) {
			update_option("pf_enable_email_copies","yes");
			return $matches[1];
		}
	
	}

	/**
	 * Sender information.
	 *
	 * @param string $sender Sender strings in "Name <email@example.com>" format.
	 * @param array  $fields List of fields.
	 */
	public function get_sender_details( $sender, $fields ) {

		preg_match( '/(.+?)\<(.+?)\>/', $sender, $tag );

		if ( ! empty( $tag[1] ) && ! empty( $tag[2] ) ) {
			return array(
				'name'    => $this->get_smarttags( $tag[1], $fields ),
				'address' => $this->get_smarttags( $tag[2], $fields ),
			);
		}

		return false;
	}
}