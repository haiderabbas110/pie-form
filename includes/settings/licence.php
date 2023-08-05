<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Settings_Licence extends PFORM_Abstracts_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'licence';
		$this->label = esc_html__( 'licence Key', 'pie-forms' );

		parent::__construct();
	}
	
	/**
	 * Get settings array.
	 */
	public function get_settings() {
		$active = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php') ? true : false;

		// $not_active = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php')  || Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-protected/pie-forms-for-wp-protected.php')? false : true || ( Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-broadcast/pie-forms-for-wp-broadcast.php')? false : true );
		// if(Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php'))

		$active_protected = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-protected/pie-forms-for-wp-protected.php') ? true : false;

		$active_drip = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-drip/pie-forms-for-wp-drip.php') ? true : false;

		$active_broadcast = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-broadcast/pie-forms-for-wp-broadcast.php') ? true : false;
		
		$active_smarttranslation = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-smart-translation/pie-forms-for-wp-smart-translation.php') ? true : false;
		
		$active_paypal_donation = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') ? true : false;
		
		$active_paypal_payment = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') ? true : false;
		
		$active_stripe = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php') ? true : false;
		
		$active_zapier = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-zapier/pie-forms-for-wp-zapier.php') ? true : false;

		$active_pabbly = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-pabbly/pie-forms-for-wp-pabbly.php') ? true : false;
		
		$active_quiz = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-quiz/pie-forms-for-wp-quiz.php') ? true : false;

		$active_activecampaign = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-activecampaign/pie-forms-for-wp-activecampaign.php') ? true : false;

		$active_authorizedotnet = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-authorize-net/pie-forms-for-wp-authorize-net.php') ? true : false;

		$active_mailchimp = Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-mailchimp/pie-forms-for-wp-mailchimp.php') ? true : false;

		$settings = apply_filters(
			'pie_forms_licence_settings',
			array(
				array(
					'title' => esc_html__( 'Activate Addons Licence Settings', 'pie-forms' ),
					'type'  => 'title',
					'id'    => 'licence_settings',
				),
				array(
					'title' 		=> esc_html__( 'Upgrade To Premium Version', 'pie-forms' ),
					'type'  		=> 'upgrade',
					'id'    		=> 'upgrade_to_pro',
					'class'    		=> 'pie-forms-upgrade',
					'is_visible'	=> false
				),
				array(
					'title'			=> esc_html__('Multipage Forms Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'multipart',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_multipart_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_multipart_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active
				),
				array(
					'title'			=> esc_html__('Password Protected Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'protected',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_protected_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_protected_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_protected
				),
				array(
					'title'			=> esc_html__('Drip Campaign Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'drip',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_drip_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_drip_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_drip
				),
				array(
					'title'			=> esc_html__('Broadcast Campaign Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'broadcast',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_broadcast_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_broadcast_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_broadcast
				),
				array(
					'title'			=> esc_html__('Smart Auto Translation Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'smarttranslation',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_smarttranslation_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_smarttranslation_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_smarttranslation
				),
				array(
					'title'			=> esc_html__('Paypal Donation Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'paypal_donation',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_paypal_donation_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_paypal_donation_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_paypal_donation
				),
				array(
					'title'			=> esc_html__('Paypal Payment Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'paypal_payment',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_paypal_payment_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_paypal_payment_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_paypal_payment
				),
				array(
					'title'			=> esc_html__('Stripe Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'stripe_payment',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_stripe_payment_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_stripe_payment_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_stripe
				),
				array(
					'title'			=> esc_html__('Zapier Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'zapier',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_zapier_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_zapier_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_zapier
				),
				array(
					'title'			=> esc_html__('Pabbly Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'pabbly',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_pabbly_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_pabbly_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_pabbly
				),
				array(
					'title'			=> esc_html__('Quiz Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'quiz',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_quiz_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_quiz_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_quiz
				),
				array(
					'title'			=> esc_html__('Active Campaign Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'activecampaign',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_activecampaign_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_activecampaign_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_activecampaign
				),
				array(
					'title'			=> esc_html__('Authorize.Net Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'authorizedotnet',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_authorizedotnet_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_authorizedotnet_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_authorizedotnet
				),
				array(
					'title'			=> esc_html__('Mailchimp Addon' , 'pie-forms'),
					'type'			=> 'addons_license',
					'id'			=> 'mailchimp',
					'email_address'	=> array(
						'placeholder'   => esc_html__( 'Email Address', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_mailchimp_licence_email',
 						'default'  		=> '',

					),
					'license_key'	=> array(
						'placeholder'   => esc_html__( 'License key', 'pie-forms' ),
						'desc'     		=> esc_html__( '', 'pie-forms' ),
						'id'       		=> 'pieforms_addon_mailchimp_licence_key',
						'default'  		=> '',
					),
					'is_visible'	=> $active_mailchimp
				),
				array(
					'type' => 'sectionend',
					'id'   => 'licence_settings',
				),
			)
		);

		return apply_filters( 'pie_forms_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		PFORM_Admin_Settings::save_fields( $settings );
	}
}