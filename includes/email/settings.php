<?php

defined( 'ABSPATH' ) || exit;

/**
 * Email class.
 */
class PFORM_Email_Settings {

	/**
	 * Holds the from address.
	 */
	private $from_address;

	/**
	 * Holds the from name.
	 */
	private $from_name;

	/**
	 * Holds the reply-to address.
	 */
	private $reply_to = false;

	/**
	 * Holds the carbon copy addresses.
	 */
	private $cc = false;

	/**
	 * Holds the blind carbon copy addresses.
	 */
	private $bcc = false;

	/**
	 * Holds the email content type.
	 */
	private $content_type;

	/**
	 * Holds the email headers.
	 */
	private $headers;

	/**
	 * Holds the email headers.
	 */
	static $header_image = array();

	/**
	 * Holds the email attachments.
	 */
	public $attachments = '';

	/**
	 * Whether to send email in HTML.
	 */
	private $html = true;

	/**
	 * The email template to use.
	 */
	private $template;

	/**
	 * Form data.
	 */
	public $form_data = array();

	/**
	 * Fields, formatted, and sanitized.
	 */
	public $fields = array();

	/**
	 * Entry ID.
	 */
	public $entry_id = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( 'no' === $this->get_template() ) {
			$this->html = false;
		}
		// Hooks.
		add_action( 'pie_forms_email_send_before', array( $this, 'send_before' ) );
		add_action( 'pie_forms_email_send_after', array( $this, 'send_after' ) );
		
	}

	/**
	 * Set a property.
	 */
	public function __set( $key, $value ) {
		$this->$key = $value;
	}

	/**
	 * Get the email from name.
	 *
	 * @return string The email from name.
	 */
	public function get_from_name() {
		if ( ! empty( $this->from_name ) ) {
			$this->from_name = $this->process_tag( $this->from_name );
		} else {
			$this->from_name = get_bloginfo( 'name' );
		}

		return apply_filters( 'pie_forms_email_from_name', wp_specialchars_decode( $this->from_name ), $this );
	}

	/**
	 * Get the email from address.
	 *
	 * @return string The email from address.
	 */
	public function get_from_address() {
		if ( ! empty( $this->from_address ) ) {
			$this->from_address = $this->process_tag( $this->from_address );
		} else {
			$this->from_address = get_option( 'admin_email' );
		}

		return apply_filters( 'pie_forms_email_from_address', $this->from_address, $this );
	}

	/**
	 * Get the email reply-to.
	 *
	 * @return string The email reply-to address.
	 */
	public function get_reply_to() {
		if ( ! empty( $this->reply_to ) ) {
			$this->reply_to = $this->process_tag( $this->reply_to );

			if ( ! is_email( $this->reply_to ) ) {
				$this->reply_to = false;
			}
		}

		return apply_filters( 'pie_forms_email_reply_to', $this->reply_to, $this );
	}

	/**
	 * Get the email carbon copy addresses.
	 *
	 * @return string The email carbon copy addresses.
	 */
	public function get_cc() {
		if ( ! empty( $this->cc ) ) {
			$this->cc  = $this->process_tag( $this->cc );
			$addresses = array_map( 'trim', explode( ',', $this->cc ) );

			foreach ( $addresses as $key => $address ) {
				if ( ! is_email( $address ) ) {
					unset( $addresses[ $key ] );
				}
			}

			$this->cc = implode( ',', $addresses );
		}

		return apply_filters( 'pie_forms_email_cc', $this->cc, $this );
	}

	/**
	 * Get the email blind carbon copy addresses.
	 *
	 * @return string The email blind carbon copy addresses.
	 */
	public function get_bcc() {
		if ( ! empty( $this->bcc ) ) {
			$this->bcc = $this->process_tag( $this->bcc );
			$addresses = array_map( 'trim', explode( ',', $this->bcc ) );

			foreach ( $addresses as $key => $address ) {
				if ( ! is_email( $address ) ) {
					unset( $addresses[ $key ] );
				}
			}

			$this->bcc = implode( ',', $addresses );
		}

		return apply_filters( 'pie_forms_email_bcc', $this->bcc, $this );
	}

	/**
	 * Get the email content type.
	 *
	 * @return string The email content type.
	 */
	public function get_content_type() {
		if ( ! $this->content_type && $this->html ) {
			$this->content_type = apply_filters( 'pie_forms_email_default_content_type', 'text/html', $this );
		} elseif ( ! $this->html ) {
			$this->content_type = 'text/plain';
		}

		return apply_filters( 'pie_forms_email_content_type', $this->content_type, $this );
	}

	/**
	 * Get the email headers.
	 *
	 * @return string The email headers.
	 */
	public function get_headers() {
		if ( ! $this->headers ) {
			$this->headers = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
			if ( $this->get_reply_to() ) {
				$this->headers .= "Reply-To: {$this->get_reply_to()}\r\n";
			}
			if ( $this->get_cc() ) {
				$this->headers .= "Cc: {$this->get_cc()}\r\n";
			}
			if ( $this->get_bcc() ) {
				$this->headers .= "Bcc: {$this->get_bcc()}\r\n";
			}
			$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
		}

		return apply_filters( 'pie_forms_email_headers', $this->headers, $this );
	}

	/**
	 * Build the email.
	 *
	 * @param  string $message The email message.
	 * @return string
	 */
	public function build_email( $message ) {
		if ( false === $this->html ) {
			$message = $this->process_tag( $message, false, true );
			$message = str_replace( '{all_fields}', $this->pie_forms_html_field_value( false ), $message );

			return apply_filters( 'pie_forms_email_message', $message, $this );
		}

		ob_start();

		$this::$header_image = $this->get_header_image();

		Pie_Forms()->core()->pform_get_template( 'email/header-default.php' );

		// Hooks into the email header.
		do_action( 'pie_forms_email_header', $this );

		Pie_Forms()->core()->pform_get_template( 'email/body-default.php' );

		// Hooks into the email body.
		do_action( 'pie_forms_email_body', $this );

		Pie_Forms()->core()->pform_get_template( 'email/footer-default.php' );

		// Hooks into the email footer.
		do_action( 'pie_forms_email_footer', $this );

		$message = $this->process_tag( $message, false );
		$message = nl2br( $message );

		$body    = ob_get_clean();
		
		$message = str_replace( '{email}', $message, $body );
		$message = str_replace( '{all_fields}', $this->pie_forms_html_field_value( true ), $message );
		$message = make_clickable( $message );

		return apply_filters( 'pie_forms_email_message', $message, $this );
	}

	/**
	 * Send the email.
	 */
	public function send( $to, $subject, $message, $attachments = '' ) {
		
		// Don't send if email address is invalid.
		if ( ! is_email( $to ) ) {
			return false;
		}

		// Hooks before email is sent.
		do_action( 'pie_forms_email_send_before', $this );
		$message           = $this->build_email( $message );
		$this->attachments = apply_filters( 'pie_forms_email_attachments', $this->attachments, $this );
		$subject           = Pie_Forms()->core()->pform_decode_string( $this->process_tag( $subject ) );
		
		// Let's do this.
		$sent = wp_mail( $to, $subject, $message, $this->get_headers(), $this->attachments );
		
		// Hooks after the email is sent.
		do_action( 'pie_forms_email_send_after', $this );

		return $sent;
	}

	/**
	 * Add filters/actions before the email is sent.
	 */
	public function send_before() {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}

	/**
	 * Remove filters/actions after the email is sent.
	 */
	public function send_after() {
		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}

	/**
	 * Converts text formatted HTML. This is primarily for turning line breaks
	 * into <p> and <br/> tags.
	 *
	 * @param  string $message Text to convert.
	 * @return string
	 */
	public function text_to_html( $message ) {
		if ( 'text/html' === $this->content_type || true === $this->html ) {
			$message = wpautop( $message );
		}

		return $message;
	}

	/**
	 * Processes a smart tag.
	 */
	public function process_tag( $string = '', $sanitize = true, $linebreaks = false ) {
		$tag = apply_filters( 'pie_forms_process_smart_tags', $string, $this->form_data, $this->fields, $this->entry_id );
		//$tag = pform_decode_string( $tag );

		if ( $sanitize ) {
			if ( $linebreaks ) {
				$tag = Pie_Forms()->core()->pform_sanitize_textarea_field( $tag );
			} else {
				$tag = sanitize_text_field( $tag );
			}
		}
		return $tag;
	}

	/**
	 * Process the all fields smart tag if present.
	 *
	 * @param  bool $html Toggle to use HTML or plaintext.
	 * @return string
	 */
	public function pie_forms_html_field_value( $html = true ) {
			
		if ( empty( $this->fields ) ) {
			return '';
		}
		
		
		// Make sure we have an entry id.
		if ( ! empty( $this->entry_id ) ) {
			$this->form_data['entry_id'] = (int) $this->entry_id;
		}
		
		$message = '';

		if ( $html ) {
			/*
			 * HTML emails.
			 */
			ob_start();

			// Hooks into the email field.
			do_action( 'pie_forms_email_field', $this );

			//pf_get_template( 'email/emails/field-' . $this->get_template() . '.php' );
			Pie_Forms()->core()->pform_get_template( 'email/field-default.php' );
			$field_template = ob_get_clean();
			$empty_message  = '<em>' . __( ' ', 'pie-forms' ) . '</em>';
			
			$field_iterator = 1;
			foreach ( $this->fields as $meta_id => $field ) {
				$addon_field_check = apply_filters( 'pie_forms_addon_activated_field', false, $field);

				if($field['type'] !== 'multipart' && !$addon_field_check){
					
					// If there's the export data filter, utilize that and re-loop promptly.
					if ( has_filter( "pie_forms_field_exporter_{$field['type']}" ) && 'signature' !== $field['type'] ) {
						$is_placeholder = isset($this->form_data['settings']['label_to_placeholder']) ? $this->form_data['settings']['label_to_placeholder'] : "";
	
						$formatted_string          = apply_filters( "pie_forms_field_exporter_{$field['type']}", $field, 'email-html', 2 );
						
						$field_data = !empty($this->form_data['form_fields'][$meta_id]) ? $this->form_data['form_fields'][$meta_id] : $formatted_string['label'];
						$formatted_string['label'] =  $field['type'] === 'customhtml' ? '' : $formatted_string['label'];

						$formatted_string['value'] =  apply_filters('pie_forms_html_field_value', $formatted_string['value'],
							isset( $this->fields[ $field['id'] ] ) ? $this->fields[ $field['id'] ] : $field,
							$this->form_data,
							'email-html'
						);
						$formatted_string['value'] = $field['type'] === 'signature' ? Pie_Forms()->core()->pform_signature_image($formatted_string['value']) : $formatted_string['value'];	

						$formatted_string['value'] = false === $formatted_string['value'] ? $empty_message : $formatted_string['value'];
	
						$placeholder = !empty($this->form_data['form_fields'][$meta_id]['placeholder']) ? $this->form_data['form_fields'][$meta_id]['placeholder'] : $formatted_string['label'];
						
						$formatted_string['label'] = $is_placeholder === "1"  ? $placeholder : $formatted_string['label'] ;
						
						$field_item = $field_template;
						if ( 1 === $field_iterator ) {
							$field_item = str_replace( 'border-top:1px solid #dddddd;', '', $field_item );
						}
						// Inject the label and value into the email template.
						$field_item = str_replace( '{field_name}', $formatted_string['label'], $field_item );
						$field_item = str_replace( '{field_value}', $formatted_string['value'], $field_item );
						
						$message .= wpautop( $field_item );
	
						// For BW compatibility reasons.
						++$field_iterator;
						continue;
					}
	
					$field_val   = empty( $field['value'] ) && '0' !== $field['value'] ? $empty_message : $field['value'];
					$field_name  = isset( $field_val['name'] ) ? $field_val['name'] : $field['name'];
					$field_label = ! empty( $field_val['label'] ) ? $field_val['label'] : $field_val;
					$field_type  = $field['type'];
	
					// If empty label is provided for choice field, don't store their data nor send email.
					if ( in_array( $field_type, array( 'radio', 'payment-multiple' ), true ) ) {
						if ( isset( $field_val['label'] ) && '' === $field_val['label'] ) {
							continue;
						}
					} elseif ( in_array( $field_type, array( 'checkbox', 'payment-checkbox' ), true ) ) {
						if ( isset( $field_val['label'] ) && ( empty( $field_val['label'] ) || '' === current( $field_val['label'] ) ) ) {
							continue;
						}
					}
	
	
					if ( 'rating' !== $field_type ) {
						if ( is_array( $field_label ) ) {
							$field_html = array();
							foreach ( $field_label as $meta_val ) {
								$field_html[] = esc_html( $meta_val );
							}
							$field_val = implode( ', ', $field_html );
						} else {
							$field_val = esc_html( $field_label );
						}
					}
	
					if ( empty( $field_name ) ) {
						$field_name = sprintf(
							/* translators: %d - field ID. */
							esc_html__( 'Field ID #%d', 'pie-forms' ),
							absint( $field['id'] )
						);
					}
	
					$field_item = $field_template;
					if ( 1 === $field_iterator ) {
						$field_item = str_replace( 'border-top:1px solid #dddddd;', '', $field_item );
					}
	
					$field_item  = str_replace( '{field_name}', $field_name, $field_item );
					$field_value = apply_filters( 'pie_forms_html_field_value', Pie_Forms()->core()->pform_decode_string( $field_val ), $field['value'], $this->form_data, 'email-html' );
					$field_item  = str_replace( '{field_value}', $field_value, $field_item );
	
					$message .= wpautop( $field_item );
					$field_iterator ++;
				}
			}
		} else {
			/*
			 * Plain Text emails.
			 */
			foreach ( $this->fields as $field ) {
				if ( ! apply_filters( 'pie_forms_email_display_empty_fields', false ) && ( empty( $field['value'] ) && '0' !== $field['value'] ) ) {
					continue;
				}

				$field_val  = empty( $field['value'] ) && '0' !== $field['value'] ? esc_html__( ' ', 'pie-forms' ) : $field['value'];

				if ( is_array( $field_val ) ) {
					$field_name = $field_val['name'];

					$field_html = array();

					foreach ( $field_val['label'] as $meta_val ) {
						$field_html[] = $meta_val;
					}

					$field_val = implode( ', ', $field_html );
				}else{
					$field_name = $field['name'];
				}

				if ( empty( $field_name ) ) {
					$field_name = sprintf(
						/* translators: %d - field ID. */
						esc_html__( 'Field ID #%d', 'pie-forms' ),
						absint( $field['id'] )
					);
				}

				$message    .= '--- ' . Pie_Forms()->core()->pform_decode_string( $field_name ) . " ---\r\n\r\n";
				$field_value = Pie_Forms()->core()->pform_decode_string( $field_val ) . "\r\n\r\n";
				$message    .= apply_filters( 'pie_forms_plaintext_field_value', $field_value, $field['value'], $this->form_data, 'email-plain' );
			}
		}

		if ( empty( $message ) ) {
			$empty_message = esc_html__( 'An empty form was submitted.', 'pie-forms' );
			$message       = $html ? wpautop( $empty_message ) : $empty_message;
		}
		
		return $message;
	}

	/**
	 * Email kill switch if needed.
	 *
	 * @return bool
	 */
	public function is_email_disabled() {
		return (bool) apply_filters( 'pie_forms_disable_all_emails', false, $this );
	}

	/**
	 * Get the enabled email template.
	 */
	public function get_template() {
		if ( ! $this->template ) {
			$this->template = get_option( 'pf_email_template', 'yes' );
		}

		return apply_filters( 'pie_forms_email_template', $this->template );
	}
	
	/**
	 * Get header image URL from settings.
	 *
	 * @return array
	 */
	function get_header_image() {

		$img = array(
			'url' => get_option( 'pf_email_header_image' ),
		);

		return apply_filters( 'pforms_emails_templates_get_header_image', $img, $this );
	}
}
