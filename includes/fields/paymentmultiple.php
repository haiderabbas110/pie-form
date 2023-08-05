<?php
defined( 'ABSPATH' ) || exit;

/**
 * Payment Radio Field.
 */
class PFORM_Fields_PaymentMultiple extends PFORM_Abstracts_Fields {
	/**
	 * Constructor.
	 */

	public function __construct() {

		// Define field type information.
		$this->name     = esc_html__( 'Multiple Items', 'pie-forms' );
		$this->type     = 'payment-multiple';
		$this->icon     = 'multiple-items';
		$this->order    = 50;
		$this->group    = 'payment';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'First Item', 'pie-forms' ),
				'value'   => '10.00',
				'image'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Second Item', 'pie-forms' ),
				'value'   => '25.00',
				'image'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Third Item', 'pie-forms' ),
				'value'   => '50.00',
				'image'   => '',
				'default' => '',
			),
		);
        $this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'choices',
					'show_price_after_labels',
					'choices_images',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'show_values',
					'label_hide',
					'css',
				),
			)
		);
		$is_addon_activated = get_option('pieforms_manager_addon_paypal_payment_activated');
		$is_addon_activated_stripe = get_option('pieforms_manager_addon_stripe_payment_activated');
		$is_addon_authorize_net = get_option('pieforms_manager_addon_authorizedotnet_activated');

		
		if(!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php')  && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php') && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php') || $is_addon_activated == "Deactivated" && $is_addon_activated_stripe == "Deactivated" && $is_addon_authorize_net == "Deactivated"){
			$this->is_addon = true;
			$this->is_pro 	= true;
			parent::__construct();
			return;
		}else{
			parent::__construct();
		}
    }

    /**
	 * Hook in tabs.
	 */
	public function init_hooks() {
		// Customize HTML field values.
		add_filter( 'pie_forms_html_field_value', array( $this, 'html_field_value' ), 10, 4 );

		// Define additional field properties.
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );

		add_filter('pie_forms_addon_activated_field', array( $this, 'pie_forms_display_label_before_print' ), 10, 2);

	}

	/**
	 * Return images, if any, for HTML supported values.
	 *
	 * @param string $value     Field value.
	 * @param array  $field     Field settings.
	 * @param array  $form_data Form data and settings.
	 * @param string $context   Value display context.
	 *
	 * @return string
	 */
	public function html_field_value( $value, $field, $form_data = array(), $context = '' ) {

		$addon_field_check = apply_filters( 'pie_forms_addon_activated_field', false, $field );

		if (isset($field['type']) && 'payment-multiple' === $field['type'] && !$addon_field_check) {
			if (
				'entry-table' !== $context
				&& ! empty( $field['value'] )
				&& ! empty( $field['image'] )
				&& apply_filters( 'pie_forms_checkbox_field_html_value_images', true, $context )
			) {
				return sprintf(
					'<span style="max-width:200px;display:block;margin:0 0 5px 0;"><img src="%s" style="max-width:100%%;display:block;margin:0;"></span>%s',
					esc_url( $field['image'] ),
					esc_html( $field['value'] ) . wp_kses( 
						'<p style="font-size:4px"><strong>Note : </strong>To check payment, log into your Paypal Account</p>',
						array( 'strong' => array(), 'p' => array() ) )
				);
			} else {
				return esc_html( $field['value'] ) . wp_kses( 
					'<p style="font-size:4px"><strong>Note : </strong>To check payment, log into your Paypal Account</p>',
					array( 'strong' => array(), 'p' => array() ) );
			}
		}

		return $value;
	}

	/**
	 * Define additional field properties.
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {
		// Define data.
		$form_id  = absint( $form_data['id'] );
		$field_id = $field['id'];
		$choices  = $field['choices'];

		// Remove primary input.
		unset( $properties['inputs']['primary'] );

		// Set input container (ul) properties.
		$properties['input_container'] = array(
			'class' => array( 'choices-ul' ),
			'data'  => array(),
			'attr'  => array(),
			'id'    => "pf-{$form_id}-field_{$field_id}",
		);

		// Set input properties.
		foreach ( $choices as $key => $choice ) {

			$depth                        = isset( $choice['depth'] ) ? absint( $choice['depth'] ) : 1;
			$properties['inputs'][ $key ] = array(
				'container' => array(
					'attr'  => array(),
					'class' => array( "choice-{$key}", "depth-{$depth}" ),
					'data'  => array(),
					'id'    => '',
				),
				'label'     => array(
					'attr'  => array(
						'for' => "pf-{$form_id}-field_{$field_id}_{$key}",
					),
					'class' => array( 'pie-forms-field-label-inline' ),
					'data'  => array(),
					'id'    => '',
					'text'  => Pie_Forms()->core()->pie_string_translation( $form_id, $field_id, $choice['label'], '-choice-' . $key ),
				),
				'attr'      => array(
					'name'  => "pie_forms[form_fields][{$field_id}]",
					'value' => isset( $field['show_values'] ) ? $choice['value'] : $choice['label'],
				),
				'class'     => array( 'pie-forms-payment-price' ),
				'data'      => array(
					'amount' => $choice['value'],
				),
				'id'        => "pf-{$form_id}-field_{$field_id}_{$key}",
				'image'     => isset( $choice['image'] ) ? $choice['image'] : '',
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);
		}

		// Required class for validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'pf-field-required';
		}

		// Custom properties if image choices are enabled.
		if ( ! empty( $field['choices_images'] ) ) {

			$properties['input_container']['class'][] = 'pie-forms-image-choices';

			foreach ( $properties['inputs'] as $key => $inputs ) {
				$properties['inputs'][ $key ]['container']['class'][] = 'pie-forms-image-choices-item';
			}
		}

		// Add selected class for choices with defaults.
		foreach ( $properties['inputs'] as $key => $inputs ) {
			if ( ! empty( $inputs['default'] ) ) {
				$properties['inputs'][ $key ]['container']['class'][] = 'pie-forms-selected';
			}
		}

		return $properties;
	}

	// /**
	//  * Field options panel inside the builder.
	//  *
	//  * @param array $field Field settings.
	//  */
	public function show_price_after_labels( $field ) {
	// 	/*
	// 	 * Basic field options for Multiple Items.
	// 	 */

	// 	Show price after item labels.
		$fld  = $this->field_element(
			'checkbox',
			$field,
			array(
				'slug'    => 'show_price_after_labels',
				'value'   => isset( $field['show_price_after_labels'] ) ? '1' : '0',
				'desc'    => esc_html__( 'Show price after item labels', 'pie-forms' ),
				'tooltip' => esc_html__( 'Check this option to show price of the item after the label.', 'pie-forms' ),
			),
			false
		);
		$args = array(
			'slug'    => 'show_price_after_labels',
			'content' => $fld,
		);
		$this->field_element( 'row', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field , $form_data ) {

		Pie_Forms()->core()->pie_forms_payment_multiple_field_builder_insider($field, $form_data, $this);
	
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @param array $field      Field settings.
	 * @param array $deprecated Deprecated array.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $field_atts, $form_data ) {

		Pie_Forms()->core()->pie_forms_payment_multiple_field_frontend($field, $field_atts, $form_data);
		
	}

	/**
	 * Show values field option.
	 */
	public function show_values( $field ) {
		// Show Values toggle option. This option will only show if already used or if manually enabled by a filter.
		if ( ! empty( $field['show_values'] ) || apply_filters( 'pie_forms_fields_show_options_setting', false ) ) {
			$args = array(
				'slug'    => 'show_values',
				'content' => $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'show_values',
						'value'   => isset( $field['show_values'] ) ? $field['show_values'] : '0',
						'desc'    => __( 'Show Values', 'pie-forms' ),
						'tooltip' => __( 'Check this to manually set form field values.', 'pie-forms' ),
					),
					false
				),
			);
			$this->field_element( 'row', $field, $args );
		}
	}
	
	/**
	 * Validate field on form submit.
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted form data.
	 * @param array $form_data    Form data and settings.
	 */
	/* public function validate( $field_id, $field_submit, $form_data ) {

		// Basic required check - If field is marked as required, check for entry data.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) && empty( $field_submit ) ) {

			Pie_Forms()->task->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();
		}

		// Validate that the option selected is real.
		if ( ! empty( $field_submit ) && empty( $form_data['fields'][ $field_id ]['choices'][ $field_submit ] ) ) {
			Pie_Forms()->task->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Invalid payment option.', 'pie-forms' );
		}
	} */

	/**
	 * Format and sanitize field.
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Submitted form data.
	 * @param array  $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data, $meta_key ) {
		$field        = $form_data['form_fields'][ $field_id ];
		$name         = sanitize_text_field( $field['label'] );
		$value        = '';
		$amount       = 0;
		$choice_label = '';
		$image        = '';

		$currency     = isset($form_data['pf_payments']['paypal_standard']['currency']) ? $form_data['pf_payments']['paypal_standard']['currency'] : 'USD';
		if (Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php')){
			$currency  = !empty($form_data['pf_payments']['stripe']['currency']) ? $form_data['pf_payments']['stripe']['currency'] : '';
		}
		if (Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php')){
			$currency  = !empty($form_data['pf_payments']['authorizedotnet']['currency']) ? $form_data['pf_payments']['authorizedotnet']['currency'] : '';
		}
		if ( ! empty( $field_submit ) && ! empty( $field['choices'] ) ) {

			foreach( $field['choices'] as $choice ){
				if( !empty($choice['label']) && $choice['label'] == $field_submit){
					$amount = $choice['value'];
					$value  = $amount;
					$choice_label = sanitize_text_field( $choice['label'] );
					$value        = $choice_label . ' - ' . Pie_Forms()->core()->pform_format_amount( Pie_Forms()->core()->pform_sanitize_amount( $value, $currency ) , true, $currency );
					
					if ( ! empty( $field['choices_images'] ) ) {
						$image = ! empty( $choice['image'] ) ? esc_url_raw( $choice['image'] ) : '';
					}
				}
		
			}
		}

		Pie_Forms()->task->form_fields[ $field_id ] = array(
			'name'         => $name,
			'value'        => $value,
			'value_choice' => $choice_label,
			'value_raw'    => sanitize_text_field( $field_submit ),
			'amount'       => Pie_Forms()->core()->pform_sanitize_amount( $amount, $currency ),
			'amount_raw'   => $amount,
			'currency'     => $currency,
			'image'        => $image,
			'id'           => $field_id,
			'type'         => $this->type,
			'meta_key'	   => $meta_key 
		);
	}

	/**
	 * Display field if the addon is activated.
	 * 
	 * @param boolean $to_show
	 * @param array $field
	 */
	function pie_forms_display_label_before_print($to_show, $field){
		$is_addon_activated = get_option('pieforms_manager_addon_paypal_donation_activated');
		$is_addon_activated_pp = get_option('pieforms_manager_addon_paypal_payment_activated');
		$is_addon_activated_stripe 	= get_option('pieforms_manager_addon_stripe_payment_activated');
		$is_addon_authorize_net = get_option('pieforms_manager_addon_authorizedotnet_activated');

		if(isset($field['type']) && $field['type'] == 'payment-multiple' && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') || $is_addon_activated_pp == 'Deactivated') && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') || $is_addon_activated == 'Deactivated') && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php') || $is_addon_activated_stripe == 'Deactivated') && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php') || $is_addon_authorize_net == 'Deactivated')){
			$to_show = true;
		}

		return $to_show;
	}
}