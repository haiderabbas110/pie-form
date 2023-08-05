<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_CheckOut extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Checkout', 'pie-forms' );
		$this->type     = 'checkout';
		$this->icon     = 'checkout';
		$this->order    = 60;
		$this->group    = 'payment';
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'placeholder',
					'creditcard',
					'cvv',
					'exp_month',
					'exp_year',
					'label_hide',
					'css',
				),
			),
			// 'conditional-logic' => array(
			// 	'field_options' => array(
			// 		'enable_conditional_logic',
			// 		'show_hide_dropdown',
			// 		'conditional_rules',
			// 	),
			// ),
		);

		if( !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php')  && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php')	){
			$this->is_addon = true;
			parent::__construct();
			return;
		}else{
			parent::__construct();
		}
	}

	public function init_hooks() {
		add_filter('pie_forms_addon_activated_field', array( $this, 'pie_forms_display_label_before_print' ), 10, 2);
	}

	
	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data) {
        $creditcard_placeholder  = isset($field['creditcard']) && ! empty( $field['creditcard'] ) ? esc_attr( $field['creditcard'] ) : '';
        $cvv_placeholder       = isset( $field['cvv']) && ! empty( $field['cvv'] ) ? esc_attr( $field['cvv'] ) : '';
        $sublabel_hide          = ! empty( $field['sublabel_hide'] ) ? 'sublabel_hide' : '';

        // Label.
		$this->field_preview_option( 'label', $field );
        //Credit Card
        echo '<div class="pie-forms-field-row pie-forms-creditcard">';
			printf('<label for=%s class="pie-forms-sub-label creditcard-label-%s %s">Card Number</label>', $field['id'], $field['id'] , $sublabel_hide);
            printf('<input type="text" class="widefat pie-field-creditcard" disabled="" placeholder="%s">',esc_attr( $creditcard_placeholder ));
        echo '</div>';

        //CVV
        echo '<div class="pie-forms-field-row pie-forms-cvv">';
			printf('<label for=%s class="pie-forms-sub-label cvv-label-%s %s">CVV</label>', $field['id'], $field['id'] , $sublabel_hide);
            printf('<input type="text" class="widefat pie-field-cvv" disabled="" placeholder="%s">',esc_attr( $cvv_placeholder ));
        echo '</div>';

        //Expiry Date
        echo '<div class="pie-forms-field-row pie-forms-exp-date">';
			printf('<label for=%s class="pie-forms-sub-label exp-date-label-%s %s">Expiry Date (Month/Year)</label>', $field['id'], $field['id'] , $sublabel_hide);
			printf('<div class="exp-date">
					<select id="authorize_month" data-stripe="exp_month" name="authorize_month" disabled="">
					<option value="01">Month</option>
					</select>
					<select id="authorize_year" data-stripe="exp_year" name="authorize_year" disabled="" ><option>Year</option></select></div>');
        echo '</div>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Define data
		$primary 				= $field['properties']['inputs']['primary'];
		$sublabel_hide         = ! empty( $field['sublabel_hide'] ) && $field['sublabel_hide']  === '1' ? 'sub-label-hide' : '';

		//CreditCard Number
		echo '<div class="pie-forms-field-row-creditcard">';
		printf('<label for=%s class="pie-forms-field-sublabel after %s">Card Number <small>(without space and dashes)</small></label>', $primary['id'], $sublabel_hide);
		printf(
			'<input type="text" %s name=%s placeholder="%s" %s data-rule-card="true">',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'].'-creditcard', $primary['class'] ),$primary['attr']['name'].'[creditcard]', 
			esc_attr(isset($field['creditcard']) ? $field['creditcard'] : "") ,
			esc_attr( $primary['required'])
		);
		echo '</div>';



		//CVV number
		echo '<div class="pie-forms-field-row-cvv">';		
		printf('<label for=%s class="pie-forms-field-sublabel after %s">CVV <small>(without space and dashes)</small></label>', $primary['id'], $sublabel_hide);
		printf(
			'<input type="text" %s name=%s placeholder="%s" %s data-rule-cvv="true">',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'].'-cvv', $primary['class'] ),$primary['attr']['name'].'[cvv]', 
			esc_attr(isset($field['cvv']) ? $field['cvv'] : "") ,
			esc_attr( $primary['required'])
		);
		echo '</div>';





		//credit card expiry
		$state_label  = ' Expiry Date (Month/Year)'  ;
		printf('<label for=%s class="pie-forms-field-sublabel after %s">%s</label>', $primary['id'].'-exp-date', $sublabel_hide , $state_label);
echo '<div class="exp-date">';

		echo '<div class="pie-forms-field-row-exp_month">';		

		printf(
			'<select type="text" %s name=%s placeholder="%s" %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'].'-exp_month', $primary['class']), $primary['attr']['name'].'[exp_month]',
			esc_attr(isset($field['exp_month']) ? $field['exp_month'] : "") , 
			esc_attr( $primary['required'])
		);
				printf('<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>');
		printf('</select>');
		echo '</div>';
		$year_option = "";

		for ( $year = ( intval( date( "Y" ) ) ); $year <= ( intval( date( "Y" ) + 20 ) ); $year ++ ) {
			$year_option .= "<option value='" . $year . "'>" . ( __( $year, "piereg" ) ) . "</option>";
		}
		echo '<div class="pie-forms-field-row-exp_year">';		

		printf(
			'<select type="text" %s name=%s placeholder="%s" %s>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'].'-exp_year', $primary['class']), $primary['attr']['name'].'[exp_year]',
			esc_attr(isset($field['exp_year']) ? $field['exp_year'] : "") , 
			esc_attr( $primary['required'])
		);
			printf($year_option);	
		printf('</select>');
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Display field if the addon is activated.
	 * 
	 * @param boolean $to_show
	 * @param array $field
	 */
	function pie_forms_display_label_before_print($to_show, $field){

		if(isset($field['type']) && $field['type'] == 'checkout' && ( !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php')  && !Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php')	) ){
			$to_show = true;
		}
		return $to_show;
	}
}
