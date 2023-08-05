<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class PFORM_Fields_Payment_Single
 */

class PFORM_Fields_PaymentSingle extends PFORM_Abstracts_Fields{

	public function __construct() {
		$this->name     = esc_html__( 'Single Item', 'pie-forms' );
		$this->type     = 'payment-single';
		$this->icon     = 'single-item';
		$this->order    = 10;
		$this->group    = 'payment';
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
					'price_types',
					'price'
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'placeholder',
					'label_hide',
					'css'
				),
			)
		);
	

		// To replace
		$is_addon_donation_activated = get_option('pieforms_manager_addon_paypal_donation_activated');
		$is_addon_payment_activated  = get_option('pieforms_manager_addon_paypal_payment_activated');
		$is_addon_activated_stripe  = get_option('pieforms_manager_addon_stripe_payment_activated');
		$is_addon_activated_authorize_net 	= get_option('pieforms_manager_addon_authorizedotnet_activated');


		if(
			(
				!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') 
				||
				$is_addon_donation_activated == "Deactivated"
			)
			&&
			(
				!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php')
				||
				$is_addon_payment_activated == "Deactivated"
			)
			&&
			(
				!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php')
				||
				$is_addon_activated_stripe == "Deactivated"
			)
			&&
			(
				!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php')
				||
				$is_addon_activated_authorize_net == "Deactivated"
			)
		){
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
		// Customize value format for HTML emails.
		add_filter( 'pie_forms_html_field_value', array( $this, 'html_field_value' ), 10, 4 );
		add_filter('pie_forms_addon_activated_field', array( $this, 'pie_forms_display_label_before_print' ), 10, 2);

	}

	/**
	 * Customize format for HTML email notifications. Added different link generation for classic and modern uploader.
	 *
	 * @param string $val       Field value.
	 * @param array  $field     Field settings.
	 * @param array  $form_data Form data and settings.
	 * @param string $context   Value display context.
	 *
	 * @return string
	 */
	public function html_field_value( $val, $field, $form_data = array(), $context = '' ) {
		if ( empty( $field['value'] ) || $field['type'] !== $this->type ) {
			return $val;
		}
		
		$value =  isset($field['value']) ? $field['value'] : '';
		
		// Process classic uploader.
		return sprintf(
			'%s %s',
			esc_html( $value ),
			wp_kses( 
						'<p style="font-size:4px"><strong>Note : </strong>To check payment, log into your Paypal Account</p>',
						array( 'strong' => array(), 'p' => array() )
			)
		);

	}
	/**
	 * Field options panel inside the builder.
	 *
	 * @param array $field
	 */
	public function price_types( $field ){

		$value        = ! empty( $field['price_type'] ) ? esc_attr( $field['price_type'] ) : 'pre_defined';
		$label  = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'price_type',
				'value'   => esc_html__( 'Price Type', 'pie-forms' ),
				'tooltip' => esc_html__( 'Select a type to take payments.', 'pie-forms' ),
			),
			false
		);
		$select = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'price_type',
				'value'   => $value,
				'options' => array(
					'pre_defined'      => esc_html__( 'Fixed', 'pie-forms' ),
					'user_defined'      => esc_html__( 'User Defined', 'pie-forms' ),
				),
			),
			false
		);
		$args          = array(
			'slug'    => 'price_type',
			'content' => $label . $select,
		);
	
		$this->field_element( 'row', $field, $args );
	}
	
	/* 
	* Field options panel inside the builder.
	*
	* @param array $field
	*/
	public function price( $field ){

		$amount_value        = ! empty( $field['pf_price'] ) ? esc_attr( $field['pf_price'] ) : '0.00';
		$amount_label  = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'pf_price',
				'value'   => esc_html__( 'Price', 'pie-forms' ),
				'tooltip' => esc_html__( 'Add a price you want to take with this form.', 'pie-forms' ),
			),
			false
		);
		$amount_input = $this->field_element(
			'number',
			$field,
			array(
				'slug'    => 'pf_price',
				'value'   => $amount_value
			),
			false
		);
		$args2			= array(
			'slug'    => 'pf_price',
			'class'   => $field['price_type'] === 'user_defined' ? 'hidden' : '',
			'content' => $amount_label . $amount_input,
		);
		$this->field_element( 'row', $field, $args2 );
	}
	
	/**
	 * Formats and sanitizes field.
	 */
	public function format( $field_id, $field_submit, $form_data, $meta_key ) {
		if ( is_array( $field_submit ) ) {
			$field_submit = array_filter( $field_submit );
			$field_submit = implode( "\r\n", $field_submit );
		}

		$name = ! empty( $form_data['form_fields'][ $field_id ]['label'] ) ? make_clickable( $form_data['form_fields'][ $field_id ]['label'] ) : '';

		// Sanitize but keep line breaks.
		$payment_currency  = !empty($form_data['pf_payments']['paypal_standard']['currency']) ? $form_data['pf_payments']['paypal_standard']['currency'] : '';
		if (Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php')){
			$payment_currency  = !empty($form_data['pf_payments']['stripe']['currency']) ? $form_data['pf_payments']['stripe']['currency'] : '';
		} 
		if (Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php')){
			$payment_currency  = !empty($form_data['pf_payments']['authorizedotnet']['currency']) ? $form_data['pf_payments']['authorizedotnet']['currency'] : '';
		} 
		$value        =  Pie_Forms()->core()->pform_format_amount( Pie_Forms()->core()->pform_sanitize_amount( $field_submit, $payment_currency ) , true, $payment_currency );

		Pie_Forms()->task->form_fields[ $field_id ] = array(
			'name'     => $name,
			'value'    => $value,
			'id'       => $field_id,
			'currency' => $payment_currency,
			'type'     => $this->type,
			'meta_key' => $meta_key,
			'amount'   => Pie_Forms()->core()->pform_sanitize_amount( $value, $payment_currency )
		);
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data) {

		Pie_Forms()->core()->pie_forms_payment_single_builder_insider($field, $form_data, $this);

	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {

		Pie_Forms()->core()->pie_forms_payment_single_field_frontend($field, $field_atts, $form_data);

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
		$is_addon_activated_authorize_net 	= get_option('pieforms_manager_addon_authorizedotnet_activated');

		if(isset($field['type']) && $field['type'] == 'payment-single' && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') || $is_addon_activated == 'Deactivated') && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') || $is_addon_activated_pp == 'Deactivated') && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php') || $is_addon_activated_stripe == 'Deactivated')  && (!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php') || $is_addon_activated_authorize_net == 'Deactivated') ){
			$to_show = true;
		}

		return $to_show;
	}
}

