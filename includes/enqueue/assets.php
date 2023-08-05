<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Enqueue_Assets {
	public $ver = Pie_Forms::VERSION;

	public $menu_slug = 'pie-forms';


	public function __construct() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}


	/**
		 * ENQUEUE STYLES.
	*/
	public function admin_styles() {

		//=====================ADDNEW=========================

		
		if( isset($_GET[ 'page' ]) && $_GET['page'] == 'pie-addnew') {
			wp_enqueue_style('JqueryConfirmStyle', Pie_Forms::$url . 'assets/css/lib/jquery-confirm.min.css', array(), $this->ver );
			wp_enqueue_style('AddNewCSS', Pie_Forms::$url . 'assets/css/addnew.css', array(), $this->ver );
		}

		//=====================BUILDER=========================


		if( isset($_GET[ 'form_id' ]) && $_GET['page'] == $this->menu_slug) {
			wp_enqueue_style('jQueryUI', Pie_Forms::$url . 'assets/css/lib/jquery-ui.min.css', array(), $this->ver );
				
			wp_enqueue_style('CommonCss', Pie_Forms::$url . 'assets/css/common.css', array(), $this->ver );
			
			wp_enqueue_style('JqueryConfirmStyle', Pie_Forms::$url . 'assets/css/lib/jquery-confirm.min.css', array(), $this->ver );
			
			wp_enqueue_style('PerfectScrollbar', Pie_Forms::$url . 'assets/css/lib/perfect-scrollbar.min.css', array(), $this->ver );
			wp_enqueue_style('MultiselectCSS', Pie_Forms::$url . 'assets/css/lib/select2.min.css', array(), $this->ver );	

            wp_enqueue_style('ColorpickerCss', Pie_Forms::$url . 'assets/css/lib/jquery.minicolors.css', array(), $this->ver );
			
			wp_enqueue_style('IconpickerCss', Pie_Forms::$url . 'assets/css/lib/iconpicker.css', array(), $this->ver );

			wp_enqueue_style('FormBuilderCss', Pie_Forms::$url . 'assets/css/form-builder.css', array(), $this->ver );
		}
		if( isset($_GET[ 'page' ]) && $_GET['page'] == 'pf-tools' || isset($_GET[ 'page' ]) && $_GET['page'] == 'pf-aboutus' || isset($_GET[ 'page' ]) && $_GET['page'] == 'pie-forms' ){ 
			wp_enqueue_style( 'fontawesomev515', Pie_Forms::$url . 'assets/css/lib/fontawesome.min.css', array(), $this->ver );
		}

		//=====================FONTAWESOME=========================

		if( isset($_GET[ 'page' ]) && $_GET['page'] == 'pf-aboutus' || isset($_GET[ 'page' ]) && $_GET['page'] == 'pie-forms' ) {
			wp_enqueue_style( 'fontawesomev515', Pie_Forms::$url . 'assets/css/lib/fontawesome.min.css', array(), $this->ver );
		}
		//=====================GUTENBERG POPUP=========================
		wp_enqueue_style('GutenbergPopup', Pie_Forms::$url . 'assets/css/gutenberg/popup.css', array(), $this->ver );
		wp_enqueue_style('adminCSS', Pie_Forms::$url . 'assets/css/admin.css', array(), $this->ver );
		wp_enqueue_style('slickCSS', Pie_Forms::$url . 'assets/css/lib/slick.min.css', array(), $this->ver );
	}

	/**
	 	* ENQUEUE SCRIPTS.
	 */
	public function admin_scripts() {

		//=====================BUILDER=========================

		wp_enqueue_media();
		
		wp_register_script( 'JqueryConfirm', Pie_Forms::$url . 'assets/js/lib/jquery-confirm.min.js', array(), $this->ver );
            
		wp_register_script( 'PerfectScrollbar', Pie_Forms::$url . 'assets/js/lib/perfect-scrollbar.min.js', array(), $this->ver );

		wp_register_script( 'SlickJS', Pie_Forms::$url . 'assets/js/lib/slick.min.js', array(), $this->ver );

		wp_register_script( 'Colorpicker', Pie_Forms::$url . 'assets/js/lib/jquery.minicolors.min.js', array(), $this->ver );

		wp_register_script( 'Iconpicker', Pie_Forms::$url . 'assets/js/lib/iconpicker.js', array(), $this->ver, true );
		
		wp_register_script( 'FormBuilderJS', Pie_Forms::$url . 'assets/js/form-builder.js', array('jquery-ui-sortable','jquery-ui-droppable'), $this->ver, true );

		wp_register_script( 'MutliselectJS', Pie_Forms::$url . 'assets/js/lib/select2.min.js', array(), $this->ver );
		
		wp_register_script( 'formImport', Pie_Forms::$url . 'assets/js/form-import.js', array(), $this->ver );

		if( isset($_GET[ 'tab' ], $_GET['page']) && $_GET['page'] == 'pf-tools') {
			wp_enqueue_script( 'formImport' );

			wp_localize_script('formImport', 'pf_import',
				array(
					'admin_nonce'                   => wp_create_nonce( 'pieforms-admin' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
			 
		if( isset($_GET[ 'form_id' ]) && $_GET['page'] == $this->menu_slug) {
			
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script( 'JqueryConfirm' );
			wp_enqueue_script( 'PerfectScrollbar' );
			wp_enqueue_script( 'Colorpicker');			
			wp_enqueue_script( 'Iconpicker');
			wp_enqueue_script( 'FormBuilderJS');
			wp_enqueue_script( 'MutliselectJS');
	
			// LOCALIZED SCRIPT - AJAX
			wp_localize_script( 'FormBuilderJS', 'pf_data',
			array( 
				'ajax_url' 					   => admin_url( 'admin-ajax.php' ),
				'iconpickerjson_url' 		   => Pie_Forms::$url . 'assets/js/lib/iconpicker.json',
				'nonce' 					   => wp_create_nonce('ajax-nonce'),
				'pf_get_next_id' 			   => wp_create_nonce('clone-nonce'),
				'pf_save_form' 		  		   => wp_create_nonce( 'pf_save_form_nonce' ),
				'form_id'                      => isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : 0, 
				'i18n_ok'                      => esc_html__( 'OK', 'pie-forms' ),
				'i18n_cancel'                  => esc_html__( 'Cancel', 'pie-forms' ),
				'pf_enabled_form'              => wp_create_nonce( 'pie_forms_enabled_form' ),
				'i18n_delete_field_confirm'    => esc_html__( 'Are you sure you want to delete this field?', 'pie-forms' ),
				'i18n_field_error_choice'      => esc_html__( 'This item must contain at least one choice.', 'pie-forms' ),
				'i18n_copy'                    => esc_html__( '(copy)', 'pie-forms' ),
				'admin_nonce'                  => wp_create_nonce( 'pieforms-admin' ),
				'new_field_nonce'              => wp_create_nonce( 'pieforms_new_field_nonce' ),
				'isPro'                        => false,
				'i18n_field_title_empty'       => esc_html__( 'Form Name Required ', 'pie-forms' ),
				'i18n_field_title_payload'     => esc_html__( 'Please enter form name', 'pie-forms' ),
				'smart_tags_other'             => PFORM_Email_Tags::other_smart_tags(),
				) 
			);
		} 

		// Builder upgrade.
		wp_register_script( 'pf_upgrade', Pie_Forms::$url . 'assets/js/upgrade.js', array(), $this->ver );
		wp_enqueue_script('pf_upgrade');
		wp_localize_script(
			'pf_upgrade',
			'pf_upgrade',
			array(
				'upgrade_title'         		=> esc_html__( 'is part of the Premium Plan', 'pie-forms' ),
				'upgrade_message'       		=> esc_html__( 'Upgrade to Premium, and unlock all the amazing fields and features.', 'pie-forms' ),
				'upgrade_button'        		=> esc_html__( 'Upgrade to Premium', 'pie-forms' ),
				'upgrade_url'           		=> apply_filters( 'pie_forms_upgrade_url', 'https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder' ),
				'addons_upgrade_title'         	=> esc_html__( 'is part of the Premium Plan', 'pie-forms' ),
				'addons_upgrade_message'       	=> esc_html__( 'Upgrade to Premium, and unlock all the amazing fields and features.', 'pie-forms' ),
				'addons_upgrade_button'        	=> esc_html__( 'Upgrade to Premium', 'pie-forms' ),
				'addons_upgrade_url'           	=> apply_filters( 'pie_forms_upgrade_url', 'https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder' ),
				'addons_payment_upgrade_title'         => esc_html__( 'is available in Paypal Donation Addon', 'pie-forms' ),
				'addons_payment_upgrade_message'       => esc_html__( 'Get Paypal Donation addon for free.', 'pie-forms' ),
				'addons_payment_upgrade_button'        => esc_html__( 'Get Now', 'pie-forms' ),	
				'addons_quiz_upgrade_title'         		=> esc_html__( 'is part of Quiz Addon', 'pie-forms' ),
				'addons_quiz_upgrade_message'       		=> esc_html__( 'Get the Quiz Addon, and unlock all the amazing Quiz features.', 'pie-forms' ),
				'addons_quiz_upgrade_button'        		=> esc_html__( 'Get Addon', 'pie-forms' ),
				'addons_quiz_upgrade_url'           		=> apply_filters( 'pie_forms_upgrade_url', 'https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder' ),
				
			)
		);

		//=====================ADDNEW=========================


		if( isset($_GET[ 'page' ]) && $_GET['page'] == 'pie-addnew') {

				wp_register_script( 'Isotope', Pie_Forms::$url . 'assets/js/lib/isotope.pkgd.min.js', array(), $this->ver );
				wp_register_script( 'JqueryConfirm', Pie_Forms::$url . 'assets/js/lib/jquery-confirm.min.js', array(), $this->ver );
				wp_register_script( 'AddNewJS', Pie_Forms::$url . 'assets/js/addnew.js', array(), $this->ver );
				

				wp_enqueue_script( 'Isotope' );
				wp_enqueue_script( 'JqueryConfirm' );
				wp_enqueue_script( 'AddNewJS' );
				
				// ADD NEW LOCALIZED SCRIPT - AJAX
				wp_localize_script( 'AddNewJS', 'pf_ajax_object',
						array( 
							'ajax_url' 					   => admin_url( 'admin-ajax.php' ),
							'nonce' 					   => wp_create_nonce('ajax-nonce'),
							'upgrade_url'                  => apply_filters( 'pie_forms_upgrade_url', 'https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formtemplates' ),
							'upgrade_button'               => esc_html__( 'Upgrade to Premium', 'pie-forms' ),
							'upgrade_message'              => esc_html__( 'Upgrade to Premium, and unlock all the amazing templates and features.
							', 'pie-forms' ),
							'upgrade_title'                => esc_html__( 'Template is part of the Premium Plan', 'pie-forms' ), 
							'addons_quiz_upgrade_title'         		=> esc_html__( 'Template is part of our Quiz Addon', 'pie-forms' ),
							'addons_quiz_upgrade_message'       		=> esc_html__( 'Get this Quiz Addon and you can unlock all the amazing Quiz features for your website', 'pie-forms' ),
							'addons_quiz_upgrade_button'        		=> esc_html__( 'Get Addon', 'pie-forms' ),
							'addons_quiz_upgrade_url'           		=> apply_filters( 'pie_forms_upgrade_url', 'https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder' ),
							
							) 
				);
		}

	//=====================FONTAWESOME=========================
	if(  isset($_GET[ 'page' ]) && $_GET['page'] == 'pf-aboutus' || isset($_GET[ 'page' ]) && $_GET['page'] == 'pie-forms' ) {
		wp_register_script( 'fontawesome', Pie_Forms::$url . 'assets/js/lib/fontawesome.min.js', array(), $this->ver, true);
        wp_enqueue_script( 'fontawesome');
	}
	
	//=====================EDITOR=========================	
	wp_register_script( 'PFEditor', Pie_Forms::$url . 'assets/js/gutenberg/editor.js', array(), $this->ver );
	wp_enqueue_script( 'PFEditor' );
	wp_enqueue_script( 'SlickJS' );
	
	}
}