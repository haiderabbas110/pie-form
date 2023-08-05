<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Aboutus_Aboutus.
 */
class PFORM_Notices_Notices {

	/**
	 * Constructor.
	 */
	public function __construct() {

		//JS SCRIPT
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		//Notice dismiss init action
		add_action('admin_init' , array($this, 'init') ,1);

		//Notice layout Filter
		add_filter('addon_notices' , array($this , 'get_output') , 1000);

	}

	public function init(){

		//NOTICE DISMISS ACTIONS
		add_action( 'wp_ajax_dismiss_pf_notice_for_zapier', array( $this, 'dismiss_pf_notice_for_zapier' ) );
		add_action( 'wp_ajax_dismiss_pf_notice_for_paypal', array( $this, 'dismiss_pf_notice_for_paypal' ) );
		add_action( 'wp_ajax_dismiss_pf_notice_for_stripe', array( $this, 'dismiss_pf_notice_for_stripe' ) );
		add_action( 'wp_ajax_dismiss_pf_notice_for_smart', array( $this, 'dismiss_pf_notice_for_smart' ) );
		add_action( 'wp_ajax_dismiss_pf_notice_for_mailchimp', array( $this, 'dismiss_pf_notice_for_mailchimp' ) );

	}

	public function admin_scripts() {
		//JS FILE
		wp_register_script( 'NoticeJs', Pie_Forms::$url . 'assets/js/noticejs.js', array(), Pie_Forms::VERSION, true );
		wp_enqueue_script('NoticeJs');
	}


	public function get_output() {
		
		$images_url = Pie_Forms::$url . 'assets/images/notices/';
		$plus_icon = Pie_Forms::$url . 'assets/images/notices/plus.png';
		$pf_icon = Pie_Forms::$url . 'assets/images/notices/pf-logo.png';
		$cros = Pie_Forms::$url . 'assets/images/header-close.svg';

		$how_you = esc_html__( "Here’s how you can do it", 'pie-forms' );
		
		//NOTICES DATA

		$available_notices = array(
	
			'Mailchimp' => array(
				'icon'  => esc_url($images_url . 'mailchimp.png'),
				'name'  => esc_html__( 'Mail Chimp Addon', 'pie-forms' ),
				'desc'  => esc_html__( 'Connect your MailChimp account with Pie Forms and add users to your MailChimp list directly on form submissions.', 'pie-forms' ),
				'doc'	=> esc_url('https://pieforms.com/documentation/how-to-use-the-mailchimp-add-on-with-pie-forms/?utm_source=adminarea&utm_medium=notification&utm_campaign=viewdocumentation'),
				'url'   => esc_url('https://store.genetech.co/checkout?add-to-cart=9099&code=userpf'),
				'get'	=> esc_html('Download Now'),
				'na'	=> esc_html('mailchimp'),
				'short'	=> esc_html('mc'),
				'last_class'	=> '',
				'nonce'	=> esc_html('pie_mc_user_nonce'),
				'show'	=> get_option( 'pf_notice_for_mailchimp_2' ),
				'show_p'	=> ( (	is_plugin_active('mailchimp-for-woocommerce/mailchimp-woocommerce.php') ||
									is_plugin_active('mailchimp-for-wp/mailchimp-for-wp.php') ||
				 					is_plugin_active('contact-form-7/wp-contact-form-7.php')) &&
				  					!is_plugin_active('pie-forms-for-wp-mailchimp/pie-forms-for-wp-mailchimp.php') ) ? true : false,
			),
			'Zapier' => array(
				'icon'  => esc_url($images_url . 'zapier.png'),
				'name'  => esc_html__( 'Zapier Addon', 'pie-forms' ),
				'desc'  => esc_html__( 'Automate your work in minutes with Pie Form’s Zapier Addon.', 'pie-forms' ),
				'doc'	=> esc_url('https://pieforms.com/documentation/how-to-install-and-use-the-zapier-addon-with-pie-forms/?utm_source=adminarea&utm_medium=notification&utm_campaign=viewdocumentation'),
				'url'   => esc_url('https://pieforms.com/addons/zapier-addon/?utm_source=adminarea&utm_medium=notification&utm_campaign=getitnow'),
				'get'	=> esc_html('Get It Now'),
				'na'	=> esc_html('zapier'),
				'short'	=> esc_html('zap'),
				'last_class'	=> '',
				'nonce'	=> esc_html('pie_zap_user_nonce'),
				'show'	=> get_option( 'pf_notice_for_zapier_2' ),
				'show_p'	=> ( (	is_plugin_active('contact-form-7/wp-contact-form-7.php') ||
									is_plugin_active('zapier/zapier.php') ) &&
				 					!is_plugin_active('pie-forms-for-wp-zapier/pie-forms-for-wp-zapier.php') ) ? true : false,
			),
			'Paypal' => array(
				'icon'  => esc_url($images_url . 'paypal.png'),
				'name'  => esc_html__( 'Paypal Addon', 'pie-forms' ),
				'desc'  => esc_html__( 'Collect payments and donations easily for your website with Pie Forms Paypal Payment Addon.', 'pie-forms' ),
				'doc'	=> esc_url('https://pieforms.com/documentation/how-to-use-the-paypal-payment-addon-with-pie-forms/?utm_source=adminarea&utm_medium=notification&utm_campaign=viewdocumentation'),
				'url'   => esc_url('https://pieforms.com/addons/paypal-payment-addon/?utm_source=adminarea&utm_medium=notification&utm_campaign=getitnow'),
				'get'	=> esc_html('Get It Now'),
				'na'	=> esc_html('paypal'),
				'short'	=> esc_html('pp'),
				'last_class'	=> '',
				'nonce'	=> esc_html('pie_pp_user_nonce'),
				'show'	=> get_option( 'pf_notice_for_paypal_2' ),
				'show_p'	=> ( (	is_plugin_active('paypal-donations/paypal-donations.php') || 
									is_plugin_active(' wordpress-easy-paypal-payment-or-donation-accept-plugin/WP_Accept_Paypal_Payment.php') || 
									is_plugin_active('wp-paypal/main.php') || 
									is_plugin_active('stripe/stripe-checkout.php') || 
									is_plugin_active('contact-form-7/wp-contact-form-7.php') || 
									is_plugin_active('easy-digital-downloads/easy-digital-downloads.php') || 
									is_plugin_active('wp-express-checkout/wp-express-checkout.php') ) && 
                					!is_plugin_active('pie-forms-for-wp-paypal-payment/pie-forms-for-wp-paypal-payment.php') ) ? true : false,
			),
			'Stripe' => array(
				'icon'  => esc_url($images_url . 'stripe.png'),
				'name'  => esc_html__( 'Stripe Addon', 'pie-forms' ),
				'desc'  => esc_html__( 'Easily collect payments and donations online with our Stripe addon.', 'pie-forms' ),
				'doc'	=> esc_url('https://pieforms.com/documentation/how-to-use-the-stripe-add-on-with-pie-forms/?utm_source=adminarea&utm_medium=notification&utm_campaign=viewdocumentation'),
				'url'   => esc_url('https://pieforms.com/addons/stripe-payment-addon/?utm_source=adminarea&utm_medium=notification&utm_campaign=getitnow'),
				'get'	=> esc_html('Get It Now'),
				'na'	=> esc_html('stripe'),
				'short'	=> esc_html('st'),
				'nonce'	=> esc_html('pie_st_user_nonce'),
				'last_class'	=> '',
				'show'	=> get_option( 'pf_notice_for_stripe_2' ),
				'show_p'	=> ( (	is_plugin_active('payment-gateway-stripe-and-woocommerce-integration/payment-gateway-stripe-and-woocommerce-integration.php') || 
                					is_plugin_active('stripe/stripe-checkout.php') || 
                					is_plugin_active('woo-stripe-payment/stripe-payments.php') || 
                					is_plugin_active('stripe-payments\accept-stripe-payments.php') || 
                					is_plugin_active('contact-form-7/wp-contact-form-7.php') || 
            						is_plugin_active('woocommerce-gateway-stripe/woocommerce-gateway-stripe.php') || 
                					is_plugin_active('easy-digital-downloads/easy-digital-downloads.php') || 
                					is_plugin_active('wp-express-checkout/wp-express-checkout.php') ) && 
                					!is_plugin_active('pie-forms-for-wp-stripe/pie-forms-for-wp-stripe.php') )  ? true : false,
			),
			'Smart_translation' => array(
				'icon'  => esc_url($images_url . 'smart.png'),
				'name'  => esc_html__( 'Smart Translation Addon', 'pie-forms' ),
				'desc'  => esc_html__( 'Translate user response with Google Translate or Deepl using Smart Translation Add-on.', 'pie-forms' ),
				'doc'	=> esc_url('https://pieforms.com/documentation/how-to-use-smart-translator-add-on-in-pie-forms/?utm_source=adminarea&utm_medium=notification&utm_campaign=viewdocumentation'),
				'url'   => esc_url('https://store.genetech.co/checkout?add-to-cart=10205&code=userpf'),
				'get'	=> esc_html('Download Now'),
				'short'	=> esc_html('smt'),
				'na'	=> esc_html('smart'),
				'nonce'	=> esc_html('pie_smt_user_nonce'),
				'last_class'	=> esc_html('last-img'),
				'show'	=> get_option( 'pf_notice_for_smart_2' ),
				'show_p'	=> ( (	is_plugin_active('contact-form-7/wp-contact-form-7.php') || 
                					is_plugin_active('loco-translate/loco.php ') || 
                					is_plugin_active('gtranslate/gtranslate.php') || 
                					is_plugin_active('tranzly/tranzly.php') || 
                					is_plugin_active('google-language-translator\google-language-translator.php')) && 
                					!is_plugin_active('pie-forms-for-wp-smart-translation\pie-forms-for-wp-smart-translation.php') )  ? true : false,
			),
		); 

		$data = '<div id="notice" class="notice_slide">';
		foreach($available_notices as $notice){

			if(! empty($notice['show']) || ! $notice['show_p']){
				continue;
			}

			$data .= '<div class="pie-notice notice-info pie-admin-notice notice_user '.$notice['short'].'_promo_users is-dismissible" data-'.$notice['short'].'user="'.esc_attr(wp_create_nonce('pie_'.$notice['short'].'_user_nonce')).'">';
			$data .= '<button style="top: 28px;" type="button" class="notice-dismis" onclick="close_notice(`'.$notice['short'].'`,`'.$notice['na'].'`);"><span class="screen-reader-text"></span><img src="'.$cros.'"></button>';
			$data .= '<div class="pr-launch-icon">';
					$data .= '<div class="plusicon"><img src="'.esc_url($plus_icon).'" alt="Plus Icon"></div>';
					$data .= '<div class="logo">';
						$data .= '<img src="'.esc_url($pf_icon).'" alt="Pie Forms logo">';
					$data .= '</div>';
					$data .= '<div class="logo">';
					$data .= '<img src="'.$notice['icon'].'" alt="Mail Chimp Logo" class=" '.$notice['last_class'].'">';
					$data .= '</div>';
				$data .= '</div>';
				$data .= '<div class="pf-headin">';
					$data .= '<h2 style="font-weight:700;">'.$notice['name'].'</h2>';
					$data .= '<p>'.$notice['desc'].'</p>'; 
					$data .= '<a href="'.$notice['doc'].'" target="_blank" id="pie_review_in_start" class="button button-primary">'.$how_you.'</a>';;
					$data .= '<a href="'.$notice['url'].'" target="_blank" id="pie-notice-get" class="button button-primary">'.$notice['get'].'</a>';
				$data .= '</div>';
			$data .= '</div>';
		
		?>
		<script type="text/javascript">		
			function close_notice(parameter , na){
				jQuery('.'+parameter+'_promo_users').fadeTo( 100, 0, function() {
					jQuery('.'+parameter+'_promo_users').slideUp( 100, function() {
						jQuery('.'+parameter+'_promo_users').remove();
					});
					nonce = jQuery('.'+parameter+'_promo_users').data(parameter+'user');		
					var data = {
						action: 'dismiss_pf_notice_for_'+na,
						nonce: nonce
					};
					jQuery.post( ajaxurl, data );
				});
			}
		</script><?php

		}

		$data .= '</div>';

		return $data;
	}

	//Notice Dissmiss Functions

	public function dismiss_pf_notice_for_mailchimp()
	{
		// Run a security check.
		if ( ! check_ajax_referer( 'pie_mc_user_nonce', 'nonce', false ) || !current_user_can('manage_options') ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'pie-forms' ) );
		}
		update_option( 'pf_notice_for_mailchimp_2', 'yes' );
	}

	public function dismiss_pf_notice_for_zapier()
	{
		// Run a security check.
		if ( ! check_ajax_referer( 'pie_zap_user_nonce', 'nonce', false ) || !current_user_can('manage_options') ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'pie-forms' ) );
		}
		update_option( 'pf_notice_for_zapier_2', 'yes' );
	}

	public function dismiss_pf_notice_for_paypal()
	{
		// Run a security check.
		if ( ! check_ajax_referer( 'pie_pp_user_nonce', 'nonce', false ) || !current_user_can('manage_options') ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'pie-forms' ) );
		}
		update_option( 'pf_notice_for_paypal_2', 'yes' );
	}

	public function dismiss_pf_notice_for_stripe()
	{
		// Run a security check.
		if ( ! check_ajax_referer( 'pie_st_user_nonce', 'nonce', false ) || !current_user_can('manage_options') ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'pie-forms' ) );
		}
		update_option( 'pf_notice_for_stripe_2', 'yes' );
	}

	public function dismiss_pf_notice_for_smart()
	{
		// Run a security check.
		if ( ! check_ajax_referer( 'pie_smt_user_nonce', 'nonce', false ) || !current_user_can('manage_options') ) {
			wp_send_json_error( esc_html__( 'Something went wrong.', 'pie-forms' ) );
		}
		update_option( 'pf_notice_for_smart_2', 'yes' );
	}

}