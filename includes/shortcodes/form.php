<?php

defined( 'ABSPATH' ) || exit;

/**
 * Form Shortcode class.
 */
class PFORM_Shortcodes_Form {

	/**
	 * Store form data to be referenced later.
	 * 
	 * @var array
	 */
	public static $forms;
	
	/**
	 * Store information for multi-part forms.
	 *
	 * Forms that do not contain pages return false, otherwise returns an array
	 * that contains the number of total pages and page counter used when
	 * displaying pagebreak fields.
	 *
	 */
	public static $pages = false;

	
	public static $parts = array();

	/**
	 * Hooks in tab.
	 */
	public static function hooks() {
		add_action( 'pie_forms_frontend_output_success', 'pie_print_notices', 10, 2 );
		add_action( 'pie_forms_frontend_output', array( 'PFORM_Shortcodes_Form', 'header' ), 5, 4 );
		add_action( 'pie_forms_frontend_output', array( 'PFORM_Shortcodes_Form', 'fields' ), 10, 3 );
		add_action( 'pie_forms_display_field_before', array( 'PFORM_Shortcodes_Form', 'wrapper_start' ), 5, 2 );
		add_action( 'pie_forms_display_field_before', array( 'PFORM_Shortcodes_Form', 'label' ), 15, 2 );
		add_action( 'pie_forms_display_field_before', array( 'PFORM_Shortcodes_Form', 'description' ), 20, 2 );
		add_action( 'pie_forms_display_field_after', array( 'PFORM_Shortcodes_Form', 'messages' ), 3, 2 );
		add_action( 'pie_forms_display_field_after', array( 'PFORM_Shortcodes_Form', 'description' ), 5, 2 );
		add_action( 'pie_forms_display_field_after', array( 'PFORM_Shortcodes_Form', 'wrapper_end' ), 15, 2 );
		add_action( 'pie_forms_frontend_output', array( 'PFORM_Shortcodes_Form', 'honeypot' ), 15, 3 );
		add_action( 'pie_forms_frontend_output', array( 'PFORM_Shortcodes_Form', 'recaptcha' ), 20, 3 );
		add_action( 'pie_forms_frontend_output', array( 'PFORM_Shortcodes_Form', 'footer' ), 25, 3 );
	}

	/**
	 * Form footer area.
	 */
	public static function footer( $form_data, $title, $description ) {
		$form_id    	= absint( $form_data['id'] );
		$settings   	= isset( $form_data['settings'] ) ? $form_data['settings'] : array();
		$submit     	=  ( isset( $settings['submit_button_text'] ) && !empty($settings['submit_button_text']) ) ? esc_html($settings['submit_button_text']) : __( get_option('pf_global_submit_button_txt') );
		$submit_btn 	= Pie_Forms()->core()->pie_string_translation( $form_data['id'], 'submit_button', $submit );
		$submit_icon 	= ( isset( $settings['submit_button_icon'] ) && !empty($settings['submit_button_icon']) ) ? $settings['submit_button_icon'] : '';
		$submit_button  = empty($settings['submit_button']) || $settings['submit_button'] == 'text'  ? esc_html($submit_btn) : '<i class="'.$submit_icon.'"></i>';
		$submit_data 	= 'data-submit-text="' . esc_attr__($submit_button, 'pie-forms' ) . '"';

		$process    	= '';
		$classes    	= isset( $form_data['settings']['submit_button_class'] ) ? $form_data['settings']['submit_button_class'] : '';
		$parts      	= ! empty( self::$parts[ $form_id ] ) ? self::$parts[ $form_id ] : array();
		$visible 		= self::$pages ? 'display:none;' : '';

		$submit_button_processing = isset( $settings['submit_button_processing']) ? esc_html( $settings['submit_button_processing']) : 'text';
		$process_type 	=  $submit_button_processing == 'text' ? 'data-process-text-type="text"' : 'icon';
		// var_dump($process_type);die;
		// Visibility class.
		$visibility_class = apply_filters( 'pie_forms_field_submit_visibility_class', array(), $parts, $form_data );

		// Check for submit button processing-text.
		if(isset( $settings['submit_button_processing'] )  &&  $settings['submit_button_processing'] == 'icon'){
			$processing_icon = isset($settings['submit_button_processing_icon']) ? sprintf("<i class='%s'></i>",$settings['submit_button_processing_icon']) : '<i class="fas fa-spinner"></i>';
			$process = 'data-process-text="' . $processing_icon . '"'; 
			
		}else{
			if ( ! isset( $settings['submit_button_processing_text'] ) ) {
				$process = 'data-process-text="' . esc_attr__( 'Processing', 'pie-forms' ) . '"';
			} elseif ( ! empty( $settings['submit_button_processing_text'] ) ) {

				$process = 'data-process-text="' . esc_html($settings['submit_button_processing_text']) . '"';
			}
		}

		// Submit button area.
		$conditional_id = 'pie-submit-' . $form_id;
		if ( isset( $form_data['settings']['submit']['connection_1']['conditional_logic_status'] ) && '1' === $form_data['settings']['submit']['connection_1']['conditional_logic_status'] ) {
			$con_rules = array(
				'conditional_option' => isset( $form_data['settings']['submit']['connection_1']['conditional_option'] ) ? $form_data['settings']['submit']['connection_1']['conditional_option'] : '',
				'conditionals'       => isset( $form_data['settings']['submit']['connection_1']['conditionals'] ) ? $form_data['settings']['submit']['connection_1']['conditionals'] : '',
			);
		} else {
			$con_rules = '';
		}

		$conditional_rules = wp_json_encode( $con_rules );

		echo '<div class="pie-submit-container ' . esc_attr( implode( ' ', $visibility_class ) ) . '" style="' . esc_attr($visible) . '" >';

		echo '<input type="hidden" name="pie_forms[id]" value="' . esc_attr(absint( $form_id )) . '">';


		do_action( 'pie_forms_display_submit_before', $form_data );

		printf(
			"<button type='submit' name='pie_forms[submit]' class='pie-forms-submit-button pie-submit %s' id='pie-submit-%d' value='pie-submit' %s %s %s conditional_rules='%s' conditional_id='%s'>%s</button>",
			$classes, 
			$form_id, 
			$process, 
			$process_type, 
			$submit_data,
			$conditional_rules, 
			$conditional_id, 
			$submit_button
		);

		do_action( 'pie_forms_display_submit_after', $form_data );

		echo '</div>';
	}

	/**
	 * Message.
	 *
	 * @param array $field Field.
	 * @param array $form_data Form data.
	 */
	public static function messages( $field, $form_data ) {
		$error = $field['properties']['error'];

		if ( empty( $error['value'] ) || is_array( $error['value'] ) ) {
			return;
		}

		printf(
			'<label %s>%s</label>',
			Pie_Forms()->core()->pie_html_attributes( $error['id'], $error['class'], $error['data'], $error['attr'] ),
			esc_html( $error['value'] )
		);
	}

	/**
	 * Description.
	 *
	 * @param array $field Field.
	 * @param array $form_data Form data.
	 */
	public static function description( $field, $form_data ) {
		
		if ( in_array( $field['type'],  pie_forms()->core()->get_pro_form_field_types(), true ) ) {
			return;
		}

		$action = current_action();

		$description = $field['properties']['description'];

		// If the description is empty don't proceed.
		if ( empty( $description['value'] ) ) {
			return;
		}

		// Determine positioning.
		if ( 'pie_forms_display_field_before' === $action && 'before' !== $description['position'] ) {
			return;
		}
		if ( 'pie_forms_display_field_after' === $action && 'after' !== $description['position'] ) {
			return;
		}

		if ( 'before' === $description['position'] ) {
			$description['class'][] = 'pie-field-description-before';
		}

		printf(
			'<div %s>%s</div>',
			Pie_Forms()->core()->pie_html_attributes( $description['id'], $description['class'], $description['data'], $description['attr'] ),
			Pie_Forms()->core()->pie_string_translation( $form_data['id'], $field['id'], esc_html($description['value']), '-description' ) 
		);
	}

	/**
	 * Label.
	 */
	public static function label( $field, $form_data ) {
		$label = $field['properties']['label'];
		$hide_all_label = $form_data['settings']['hide_all_label'];
		
		// If the label is empty or disabled don't proceed.
		if ( empty( $label['value'] ) || $label['disabled'] || $hide_all_label == 1 ) {
			return;
		}
		if ( in_array( $field['type'],  pie_forms()->core()->get_pro_form_field_types(), true ) ) {
			return;
		}

		$required    = ($label['required'] || $field['type'] === 'gdpr') ? apply_filters( 'pie_forms_field_required_label', '<abbr class="required" title="' . esc_attr__( 'Required', 'pie-forms' ) . '">*</abbr>' ) : '';
		$custom_tags = apply_filters( 'pie_forms_field_custom_tags', false, $field, $form_data );

		
		
		
		$display_label = printf(
			'<label %s><span class="pf-label">%s</span> %s</label>',
			Pie_Forms()->core()->pie_html_attributes( $label['id'], $label['class'], $label['data'], $label['attr'] ),
				$label['value'],
			$required, 
			$custom_tags 
		);




		
	}

	/**
	 * Wrapper end.
	 */
	public static function wrapper_end( $field, $form_data ) {
		echo '</div>';
	}

	/**
	 * Wrapper start.
	 */
	public static function wrapper_start( $field, $form_data ) {
		$container                     = $field['properties']['container'];
		$container['data']['field-id'] = esc_attr( $field['id'] );
		printf(
			'<div %s>',
			Pie_Forms()->core()->pie_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);
	}

	/**
	 * Form header for displaying form title and description if enabled.
	 */
	public static function header( $form_data, $title, $description, $errors ) {
		$settings = isset( $form_data['settings'] ) ? $form_data['settings'] : array();

		// Check if title and/or description is enabled.
		if ( true === $title || true === $description ) {
			echo '<div class="pie-title-container">';

			if ( true === $title && ! empty( $settings['form_title'] ) ) {
				echo '<div class="pie-forms--title">' . esc_html( Pie_Forms()->core()->pie_string_translation( $form_data['id'], 'form_title', $settings['form_title'] ) ) . '</div>';
			}

			if ( true === $description && ! empty( $settings['form_description'] ) ) {
				echo '<div class="pie-forms--description">' . esc_textarea( Pie_Forms()->core()->pie_string_translation( $form_data['id'], 'form_description', $settings['form_description'] ) ) . '</div>';
			}

			echo '</div>';
		}

		
	}

	private static function wp_enqueue_assests($form_data, $atts){

		$is_ajax  	= $form_data['settings']['ajax_form_submission'];
		$PFjQueryUI	= Pie_Forms()->core()->pie_is_field_exists( $atts['id'], 'date' ) ? array('PFjQueryUI') :'';
		
		if ( Pie_Forms()->core()->pie_is_field_exists( $atts['id'], 'date' ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'PFjQueryUI' );

			//DATE TIME PICKER JS
			wp_register_style('PFjQueryUI', Pie_Forms::$url . 'assets/css/lib/jquery-ui.min.css', array(), Pie_Forms::VERSION );
		}
		

		//FRONT END FORM STYLE
		wp_enqueue_style('FrontEndFormCSS', Pie_Forms::$url . 'assets/css/front-end-form.css', $PFjQueryUI, Pie_Forms::VERSION );
		if(isset( $form_data['settings']['rtl_support'] ) && '1' === $form_data['settings']['rtl_support']){
			//RTL SUPPORT FORM STYLE
			wp_enqueue_style('RtlSupport', Pie_Forms::$url . 'assets/css/rtl-pie-form.css', $PFjQueryUI, Pie_Forms::VERSION );
		}

		//SUBMISSION MESSAGE IN POPUP
		if(isset( $form_data['settings']['enable_message_popup'] ) && '1' === $form_data['settings']['enable_message_popup']){
			wp_enqueue_style('SubmissionPopupStyle', Pie_Forms::$url . 'assets/css/submission-popup.css', array(), Pie_Forms::VERSION );
			wp_enqueue_style('JqueryConfirmStyle', Pie_Forms::$url . 'assets/css/lib/jquery-confirm.min.css', array(), Pie_Forms::VERSION );
			wp_enqueue_script( 'JqueryConfirm', Pie_Forms::$url . 'assets/js/lib/jquery-confirm.min.js', array(), Pie_Forms::VERSION );

		}
		//JQUERY VLAIDATION LIBRARY
		wp_register_script( 'jQueryValidation', Pie_Forms::$url . 'assets/js/lib/jquery.validate.min.js', array('jquery'), Pie_Forms::VERSION );
		wp_enqueue_script( 'jQueryValidation');
		
		wp_enqueue_style( 'fontawesomev515', Pie_Forms::$url . 'assets/css/lib/fontawesome.min.css', array(), Pie_Forms::VERSION );

	
		

		//FRONT END FORM SCRIPT
		do_action('pie_forms_enqueue_form_scripts' );
		wp_register_script( 'FrontEndFormJS', Pie_Forms::$url . 'assets/js/front-end-form.js', array(), Pie_Forms::VERSION );
		wp_enqueue_script( 'FrontEndFormJS');

		wp_register_script( 'FormAjaxSubmission', Pie_Forms::$url . 'assets/js/ajax-submission.js', array(), Pie_Forms::VERSION );
		if(1 == $is_ajax){
			wp_enqueue_script( 'FormAjaxSubmission' );

				// LOCALIZED SCRIPT - AJAX
			wp_localize_script( 'FormAjaxSubmission', 'pf_data',
				array(
					'ajax_url'            => admin_url( 'admin-ajax.php' ),
					'pf_ajax_submission' => wp_create_nonce( 'pie_forms_ajax_form_submission' ),
					'submit'              => esc_html__( 'Submit', 'pie-forms' ),
					'base_url' 			  => Pie_Forms::$url ,
					'error'               => esc_html__( 'Sorry, something went wrong. Please try again', 'pie-forms' ),
					'required'            => esc_html__( 'This field is required.', 'pie-forms' ),
				)
			);
		}

	}
	/**
	 * Form field area.
	 */
	public static function fields( $form_data, $title, $description ) {
		$structure 	= isset( $form_data['structure'] ) ? $form_data['structure'] : array();
		$is_ajax  	= $form_data['settings']['ajax_form_submission'];
		if ( empty( $form_data['form_fields'] ) ) {
			return;
		}
		
	
		
		
		// Form fields area.
		echo '<div class="pf-field-container">';

		wp_nonce_field( 'pie-forms_process_submit' );

		/**
		 * Hook: pie_forms_display_fields_before.
		 *
		 */
	

			echo '<div class="pf-field-row">';
			do_action( 'pie_forms_display_fields_before', $form_data );

			foreach ( $structure as $row_key => $row ) {
				
				// do_action( 'pie_forms_display_row_before', $row_key, $form_data );
					
				$field = isset( $form_data['form_fields'][ $row ] ) ? $form_data['form_fields'][ $row ] : array();
				

					// echo '<div class="pf-field-wrapper pie-form-' . $row_key. '" data-grid="' . esc_attr( $row_key ) . '">';

					
					//foreach ( $grid as $field_key ) {
				
						$field = apply_filters( 'pie_forms_field_data', $field, $form_data );

						$addon_field_check = apply_filters( 'pie_forms_addon_activated_field', false, $field );
						
						if( ( $field['type'] === "gdpr" && get_option('pf_gdpr_options') !== 'yes' ) || $addon_field_check){
							continue;
						}

						/* if($field['type'] == 'payment-single' && !Pie_Forms()->core()->pforms_check_payment_addon_active()){						
							continue;
						} */
						
						// Get field attributes.
						$attributes = self::get_field_attributes( $field, $form_data );

						// Get field properties.
						echo '<div class="pf-field-wrapper pie-form-' . esc_attr($row_key). '" data-grid="' . esc_attr( $row_key ) . '">';

						$properties = self::get_field_properties( $field, $form_data, $attributes );

						// Add properties to the field so it's available everywhere.
						$field['properties'] = $properties;
						do_action( 'pie_forms_display_field_before', $field, $form_data );
						do_action( "pie_forms_display_field_{$field['type']}", $field, $attributes, $form_data );

						echo '</div>';
						do_action( 'pie_forms_display_field_after', $field, $form_data );

					// echo '</div>';
				
					// do_action( 'pie_forms_display_row_after', $row_key, $form_data );
			}
		echo '</div>';

		do_action( 'pie_forms_display_fields_after', $form_data );

		echo '</div>';
	}

	
	/**
	 * Anti-spam honeypot output if configured.
	 */
	public static function honeypot( $form_data ) {
		$names = array( 'Name', 'Phone', 'Comment', 'Message', 'Email', 'Website' );
		if ( isset( $form_data['settings']['honeypot'] ) && '1' === $form_data['settings']['honeypot'] ) {
			echo '<div class="pf-honeypot-container pf-field-hp" hidden>';

				echo '<label for="pf-' . esc_attr($form_data['id']) . '-field-hp" class="pf-field-label">' . esc_html($names[ array_rand( $names ) ]) . '</label>';

				echo '<input type="text" name="pie_forms[hp]" id="pf-' . esc_attr($form_data['id']) . '-field-hp" class="input-text">';  

			echo '</div>';
		}
	}


	/**
	 * Google reCAPTCHA output if configured.
	 */
	public static function recaptcha( $form_data ) {
		$recaptcha_type      = get_option( 'pf_recaptcha_type', 'v2' );
		$visible 			 = self::$pages ? 'display:none;' : '';

		if ( 'v2' === $recaptcha_type ) {
			$site_key   = get_option( 'pf_recaptcha_v2_site_key' );
			$secret_key = get_option( 'pf_recaptcha_v2_secret_key' );
		} elseif ( 'v3' === $recaptcha_type ) {
			$site_key   = get_option( 'pf_recaptcha_v3_site_key' );
			$secret_key = get_option( 'pf_recaptcha_v3_secret_key' );
		}

		if ( ! $site_key || ! $secret_key ) {
			return;
		}

		if ( isset( $form_data['settings']['recaptcha_support'] ) && '1' === $form_data['settings']['recaptcha_support'] ) {
			$form_id = isset( $form_data['id'] ) ? absint( $form_data['id'] ) : 0;
			
			$data    = apply_filters(
				'pie_forms_frontend_recaptcha',
				array(
					'sitekey' => trim( sanitize_text_field( $site_key ) ),
				),
				$form_data
			);

			// Load reCAPTCHA support if form supports it.
			if ( $site_key && $secret_key ) {
				// Output the reCAPTCHA container.
				
				echo '<div class="pie-recaptcha-container" style="'. esc_attr($visible) .'" >';
				
				if ( 'v2' === $recaptcha_type ) {
					
					$message 				= get_option('pf_reCaptcha_validation');
					
					$global_captcha_message = (isset($message) && !empty($message)) ? $message : esc_html__( 'This field is required', 'pie-forms');

					echo '<div ' . Pie_Forms()->core()->pie_html_attributes( '', array( 'g-recaptcha','pie-field' ), $data ) . ' data-required-field-message="'.esc_html($global_captcha_message).'"></div>';
					
					echo '<input type="text"  name="g-recaptcha-hidden" class="pie-recaptcha-hidden" style="position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;" required>';

				} else {
					echo '<input type="hidden" name="pie_forms[recaptcha]" value="">';
				}

				echo '</div>';
			}
		}
	}

	/**
	 * Get field attributes.
	 */
	private static function get_field_attributes( $field, $form_data ) {
		$form_id    = absint( $form_data['id'] );
		$field_id   = esc_attr( $field['id'] );
		$attributes = array(
			'field_class'       => array( 'pie-field', 'pie-field-' . Pie_Forms()->core()->sanitize_html_class($field['type']), 'form-row' ),
			'field_id'          => array( sprintf( 'pie-form-field-%s-container',$field_id ) ),
			'field_style'       => '',
			'label_class'       => array( 'pie-field-label' ),
			'label_id'          => '',
			'description_class' => array( 'pie-field-description' ),
			'description_id'    => array(),
			'input_id'          => array( sprintf( 'pie-%d-field_%s', $form_id, $field_id ) ),
			'input_class'       => array(),
			'input_data'        => array(),
		);

		// Check user field defined classes.
		if ( ! empty( $field['css'] ) ) {
			$attributes['field_class'] = array_merge( $attributes['field_class'],  Pie_Forms()->core()->pform_sanitize_classes($field['css'], true) );
		}

		// Check for input column layouts.
		if ( ! empty( $field['input_columns'] ) ) {
			if ( 'inline' === $field['input_columns'] ) {
				$attributes['field_class'][] = 'pie-forms-list-inline';
			} elseif ( '' !== $field['input_columns'] ) {
				$attributes['field_class'][] = 'pie-forms-list-' . $field['input_columns'] . '-columns';
			}
		}

		// Input class.
		if ( ! in_array( $field['type'], array( 'checkbox', 'radio', 'multiselect' ), true ) ) {
			$attributes['input_class'][] = 'input-text';
		}

		// Check label visibility.
		if ( ! empty( $field['label_hide'] ) ) {
			$attributes['label_class'][] = 'pie-label-hide';
		}
		// Check label visibility.
		if ( ! empty( $field['is_search'] ) ) {
			$attributes['input_class'][] = 'pie-select-search';
		}

		// Check size.
		if ( ! empty( $field['size'] ) ) {
			$attributes['input_class'][] = 'pie-field-' . $field['size'];
		}

		// Check if required.
		if ( ! empty( $field['required'] ) ) {
			$attributes['field_class'][] = 'validate-required';
		}

		// Check if extra validation required.
		if ( in_array( $field['type'], array( 'email', 'phone' ), true ) ) {
			$attributes['field_class'][] = 'validate-' . esc_attr( $field['type'] );
		}

		// Check if there are errors.
		if ( isset( Pie_Forms()->task->errors[ $form_id ][ $field_id ] ) ) {
			$attributes['input_class'][] = 'pie-error';
			$attributes['field_class'][] = 'pie-got-error';
		}
	
		
		$attributes = apply_filters( 'pie_field_atts', $attributes, $field, $form_data );

		return $attributes;
	}

	/**
	 * Return base properties for a specific field.
	 */
	public static function get_field_properties( $field, $form_data, $attributes = array() ) {
		if ( empty( $attributes ) ) {
			$attributes = self::get_field_attributes( $field, $form_data );
		}

		// This filter is for backwards compatibility purposes.
		$types = array( 'text', 'textarea', 'number', 'email', 'hidden', 'url', 'html', 'title','multiselect', 'password', 'phone', 'address', 'checkbox', 'radio', 'select', 'fileupload' );

		if ( in_array( $field['type'], $types, true ) ) {
			$field = apply_filters( "pie_forms_{$field['type']}_field_display", $field, $attributes, $form_data );
		}

		$form_id  = absint( $form_data['id'] );
		$field_id = sanitize_text_field( $field['id'] );


		//Placeholder if label as placeholder
		$field['label'] 			= isset($field['label']) ? $field['label'] : '';
		$enable_placeholder 		= isset( $form_data['settings']['label_to_placeholder'] ) ? $form_data['settings']['label_to_placeholder']  : 0;
		$label_to_placeholder   	= $enable_placeholder == 1 ?  $field['label'] : '';
		$placeholder_if_required 	= isset($field['required']) == 1 ? $field['label'].'*' : $field['label'];
		$label_to_placeholder   	= $enable_placeholder == 1 ?  $placeholder_if_required : '';
		
		$field_description_postion 		= isset( $form_data['settings']['field_description_postion'] ) ? $form_data['settings']['field_description_postion']  : 0;
		

		
		// Field container data.
		$container_data = array();

		if ( isset($field['custom_regex']) && !empty($field['custom_regex'])){
			$container_data['set_rule'] = $field['custom_regex'];
		}
		
		if ( ! empty( $field['validation_rule'] ) ) {
			
			if($field['validation_rule'] == "custom_regex" && !empty($field['custom_regex'])){
				$container_data['set_rule'] = $field['custom_regex'];
			}

			//Alphabets and space only e.g:Haider Abbas
			if( isset($field['validation_rule']) && $field['validation_rule'] == "alpha_only" ){
				$container_data['set_rule'] = "^[a-zA-Z ]*$";
			}
		}
		if ( ! empty( $field['custom_validation_message'] ) ) {
			
			if( !empty($field['custom_validation_message'])){
				$container_data['custom_validation_message'] = $field['custom_validation_message'];
			}
			
		}
		// Embed required-field-message to the container if the field is required.
		if ( isset( $field['required'] ) && ( '1' === $field['required'] || true === $field['required'] ) || $field['type'] === 'gdpr' ) {
			
			$required_validation = get_option( 'pf_required_validation' );
			if ( in_array( $field['type'], array( 'number', 'email', 'url', 'phone' ), true ) ) {
				$required_validation = get_option( 'pf_' . $field['type'] . '_validation' );
			}

			//Required Validation
			$container_data['required-field-message'] = isset( $field['required-field-message'] ) && '' !== $field['required-field-message'] ? Pie_Forms()->core()->pie_string_translation( $form_data['id'], $field['id'], esc_html($field['required-field-message']), '-required-field-message' ) : $required_validation;

			
		}
		$errors     = isset( Pie_Forms()->task->errors[ $form_id ][ $field_id ] ) ? Pie_Forms()->task->errors[ $form_id ][ $field_id ] : '';
		
		$defaults   = isset( $_POST['pie_forms']['form_fields'][ $field_id ] ) && ( ! is_array( $_POST['pie_forms']['form_fields'][ $field_id ] ) && ! empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) ) ? sanitize_key($_POST['pie_forms']['form_fields'][ $field_id ]) : ''; 
		
		$placeholder 		= isset($field['placeholder'])? $field['placeholder'] : ""	;

		$field_with_required = !empty($field['required']) ? $placeholder."*" : $placeholder;

		

		$properties = apply_filters(
			'pie_forms_field_properties_' . $field['type'],
			array(
				'container'   => array(
					'attr'  => array(
						'style' => $attributes['field_style'],
					),
					'class' => $attributes['field_class'],
					'data'  => $container_data,
					'id'    => implode( '', array_slice( $attributes['field_id'], 0 ) ),
				),
				'label'       => array(
					'attr'     => array(
						'for' => sprintf( 'pie-%d-field_%s', $form_id, $field_id ),
					),
					'class'    => $attributes['label_class'],
					'data'     => array(),
					'disabled' => ! empty( $field['label_disable'] ) ? true : false,
					'hidden'   => ! empty( $field['label_hide'] ) ? true : false,
					'id'       => $attributes['label_id'],
					'required' => ! empty( $field['required'] ) ? true : false,
					'value'    => ! empty( $field['label'] ) ? $field['label'] : '',
				),
				'inputs'      => array(
					'primary' => array(
						'attr'     => array(
							'name'        => "pie_forms[form_fields][{$field_id}]",
							'value'       => ! empty( $field['default_value'] ) ? apply_filters( 'pie_forms_process_smart_tags', $field['default_value'], $form_data ) : $defaults,
							'placeholder' => !empty( $field['placeholder'] ) ? Pie_Forms()->core()->pie_string_translation( $form_data['id'], $field['id'], $field_with_required, '-placeholder' ) : $label_to_placeholder,
						),
						'class'    => $attributes['input_class'],
						'data'     => $attributes['input_data'],
						'id'       => implode( array_slice( $attributes['input_id'], 0 ) ),
						'required' => ! empty( $field['required'] ) ? 'required' : '',
					),
				),
				'error'       => array(
					'attr'  => array(
						'for' => sprintf( 'pie-%d-field_%s', $form_id, $field_id ),
					),
					'class' => array( 'pie-error' ),
					'data'  => array(),
					'id'    => '',
					'value' => ! empty( $errors ) ? $errors : '',
				),
				'description' => array(
					'attr'     => array(),
					'class'    => $attributes['description_class'],
					'data'     => array(),
					'id'       => implode( '', array_slice( $attributes['description_id'], 0 ) ),
					'position' => $field_description_postion,
					'value'    => ! empty( $field['description'] ) ? $field['description'] : '',
				),
			),
			$field,
			$form_data
		);

		return apply_filters( 'pie_forms_field_properties', $properties, $field, $form_data );
	}

	/**
	 * Output the shortcode.
	 */
	public static function output( $atts ) {

		$atts = shortcode_atts(
			array(
				'id'          => false,
				'title'       => false,
				'description' => false,
			),
			$atts,
			'output'
		);

		// Scripts load action.
		do_action( 'pie_forms_shortcode_scripts', $atts );

		ob_start();
		self::view( $atts['id'], $atts['title'], $atts['description'], $atts );
		echo ob_get_clean(); 
	}

	/**
	 * Form view.
	 */
	private static function view( $id, $title = false, $description = false, $atts = false ) {
		if ( empty( $id ) ) {
			return;
		}

		// GET THE FORM DATA.
		$data = Pie_Forms()->form()->get( (int) $id );
		$form = array_shift($data);

		if ( empty( $form ) ) {
			return;
		}
		
		// GET BASIC INFO.
		$form_data            = apply_filters( 'pie_forms_frontend_form_data', Pie_Forms()->core()->pform_decode( wp_unslash($form->form_data) ) );

		self::wp_enqueue_assests($form_data, $atts);

		$form_id              = absint( $form->form_id );
		
		$settings             = $form_data['settings'];
		
		$form_enabled         = isset( $form_data['form_enabled'] ) ? absint( $form_data['form_enabled'] ) : 1;

		$form_description	  = isset( $form_data['settings']['form_description'] ) ? $form_data['settings']['form_description'] : "" ;
		
		$form_title	  = isset( $form_data['settings']['form_title'] ) ? $form_data['settings']['form_title'] : "" ;
		
		$title_enabled 		  = isset( $form_data['settings']['enable_title'] ) ? absint( $form_data['settings']['enable_title'] ) : 1;
		$description_enabled 		  = isset( $form_data['settings']['enable_description'] ) ? absint( $form_data['settings']
		
		['enable_description'] ) : 1 ;

		$ajax_form_submission = isset( $settings['ajax_form_submission'] ) ? $settings['ajax_form_submission'] : 0;
		$enable_message_popup = isset( $settings['enable_message_popup'] ) ? $settings['enable_message_popup'] : 0;


		$recaptcha_support_on_single_form = isset( $settings['recaptcha_support'] ) ? $settings['recaptcha_support'] : 0;
		
		$action               = esc_url_raw( remove_query_arg( 'pie-forms' ) );
		$title                = filter_var( $title, FILTER_VALIDATE_BOOLEAN );
		$description          = filter_var( $description, FILTER_VALIDATE_BOOLEAN );
		$errors               = '';
			
		// If the form is disabled or does not contain any fields do not proceed.
		if ( empty( $form_data['form_fields'] ) ) {
			echo 'Pie Forms: no fields found';
			return;
		} elseif ( 1 !== $form_enabled ) {
			//if ( ! empty( $disable_message ) ) {
				printf( '<p class="pie-forms-form-disable-notice">%s</p>', "Form is disabled" );
			//}
			return;
		}
		
		// Before output hook.
		do_action( 'pie_forms_frontend_output_before', $form_data, $form );

		// Allow filter to return early if some condition is not meet.
		if ( ! apply_filters( 'pie_forms_frontend_load', true, $form_data ) ) {
			do_action( 'pie_forms_frontend_not_loaded', $form_data, $form );
			return;
		}
		
		// All checks have passed, so calculate multi-page details for the form.
		$pages = Pie_Forms()->core()->pform_get_pagebreak_details( $form_data );
		if ( $pages ) {
			self::$pages = $pages;
		} else {
			self::$pages = false;
		}

		?>
		<?php
		$success = apply_filters( 'pie_forms_success', false, $form_id );
		if ( $success && ! empty( $form_data ) ) {
			//echo 'form successfully submitted';
			echo apply_filters('pie_form_success_message',true);
			
			return;
		}


		// Allow final action to be customized.
		$action = apply_filters( 'pie_forms_frontend_form_action', $action, $form_data );

		// Allow form container classes to be filtered and user defined classes.
		$classes = apply_filters( 'pie_forms_frontend_container_class', array(), $form_data );
		if ( ! empty( $settings['form_class'] ) ) {
			$classes = array_merge( $classes, explode( ' ', $settings['form_class'] ) );
		}
		if ( ! empty( $settings['layout_class'] ) ) {
			$classes = array_merge( $classes, explode( ' ', $settings['layout_class'] ) );
		}

		if(isset( $settings['rtl_support'] ) && '1' === $settings['rtl_support']){
			$classes = array_merge( $classes, explode( ' ', 'rtl-active' ) );
		}

		$classes = Pie_Forms()->core()->pform_sanitize_classes( $classes, true );

		if($recaptcha_support_on_single_form){

			$recaptcha_type      = get_option( 'pf_recaptcha_type');
			
			$site_key 	= ($recaptcha_type == "v2") ? get_option( 'pf_recaptcha_v2_site_key' ) : get_option( 'pf_recaptcha_v3_site_key' );

			$recaptcha_language	 =  get_option( 'pf_recaptcha_language' );
		}

		$form_atts = apply_filters(
			'pie_forms_frontend_form_atts',
			array(
				'id'    => sprintf( 'pie-form-%d', absint( $form_id ) ),
				'class' => array( 'pie-form' ),
				'data'  => array(
					'formid'          => absint( $form_id ),
					'ajax_submission' => absint($ajax_form_submission),
					'message_popup' => absint($enable_message_popup)
				),
				'atts'  => array(
					'method'  => 'post',
					'enctype' => 'multipart/form-data',
					'action'  => esc_url( $action ),
					'recaptcha_type' => isset($recaptcha_type)? esc_html($recaptcha_type) : "",
					'recaptcha_key' => isset($site_key)? esc_html($site_key) : "",
					'recaptcha_language' => isset($recaptcha_language)  ? esc_html($recaptcha_language) : "",
				),
			),
			$form_data
		);
		// Begin to build the output.
		do_action( 'pie_forms_frontend_output_container_before', $form_data, $form );

		printf( '<div class="pf-container %s" id="pie-%d">', esc_attr( $classes ), absint( $form_id ) );

		do_action( 'pie_forms_frontend_output_form_before', $form_data, $form, $errors );

		if($title_enabled){
			echo "<h2>" .esc_html($form_title). "</h2>";	
		}

		if($description_enabled){
			echo "<p>" .esc_html($form_description). "</p>";
		}

		echo '<form ' .  Pie_Forms()->core()->pie_html_attributes($form_atts['id'], $form_atts['class'], $form_atts['data'], $form_atts['atts']) . '>';

		do_action( 'pie_forms_frontend_output', $form_data, $title, $description, $errors );

		echo '</form>';

		do_action( 'pie_forms_frontend_output_form_after', $form_data, $form );

		echo '</div><!-- .pie-container -->';

		// After output hook.
		do_action( 'pie_forms_frontend_output_after', $form_data, $form );

	}
}
