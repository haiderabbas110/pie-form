<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Settings extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $menu_slug = 'pf-settings';

    public $priority = 11;

    protected $_prefix = 'pie_forms';

    public function __construct()
    {
        parent::__construct();


    }

    public function get_page_title()
    {
        return esc_html__( 'Global Settings', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        wp_enqueue_style('ColorpickerCss', Pie_Forms::$url . 'assets/css/lib/jquery.minicolors.css', array(), Pie_Forms::VERSION );	
        wp_register_script( 'SettingJS', Pie_Forms::$url . 'assets/js/setting.js', array(), Pie_Forms::VERSION );
        wp_enqueue_script( 'Colorpicker');	
        wp_enqueue_script( 'SettingJS');
        wp_localize_script( 'SettingJS', 'pform_admin',
		array( 
			'upload_image_title'              => esc_html__( 'Upload or Choose Your Image', 'pie-forms' ),
		    'upload_image_button'             => esc_html__( 'Use Image', 'pie-forms' ),
            'nonce'                           => wp_create_nonce( 'pie_forms_settings' ),
            'ajax_url'                        => admin_url( 'admin-ajax.php' ),

			) 
		);	

        wp_enqueue_style('SettingPage', Pie_Forms::$url . 'assets/css/setting.css', array(), Pie_Forms::VERSION );
         
        $this->settings_page_init();

        self::output();

    }

    public static function output() {
        // global $current_section, $current_tab;

        // // Get tabs for the settings page.
        // $tabs = apply_filters( 'pie_forms_settings_tabs_array', array() );
        wp_enqueue_style( 'fontawesomev515', Pie_Forms::$url . 'assets/css/lib/fontawesome.min.css', array(), Pie_Forms::VERSION );
        Pie_Forms::template( 'settings.php' );
        Pie_Forms::template( 'related-features.php');
    }
    	/**
	 * Loads settings page.
	 */
	public function settings_page_init() {
		global $current_tab, $current_section;
		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); 

        $current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); 

		// Save settings if data has been posted.
		if ( apply_filters( '' !== $current_section ? "pie_forms_save_settings_{$current_tab}_{$current_section}" : "pie_forms_save_settings_{$current_tab}", ! empty( $_POST ) ) ) { 
            PFORM_Admin_Settings::save();
            
		}

		// Add any posted messages.
		if ( ! empty( $_GET['pf_error'] ) ) { 
			PFORM_Admin_Settings::add_error( wp_kses_post( wp_unslash( $_GET['pf_error'] ) ) ); 
		}

		if ( ! empty( $_GET['pf_message'] ) ) { 
			PFORM_Admin_Settings::add_message( wp_kses_post( wp_unslash( $_GET['pf_message'] ) ) ); 
        }
        


		do_action( 'pie_forms_settings_page_init' );
	}

} // End Class PFORM_Admin_Settings
