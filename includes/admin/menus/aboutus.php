<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Aboutus extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $menu_slug = 'pf-aboutus';

    public $priority = 15;

    protected $_prefix = 'pie_forms';

    public function get_page_title()
    {
        return __( 'About Us', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        wp_enqueue_style('AboutPage', Pie_Forms::$url . 'assets/css/aboutus.css', array(), Pie_Forms::VERSION );
        wp_register_script( 'AboutPage', Pie_Forms::$url . 'assets/js/aboutus.js', array(), Pie_Forms::VERSION );
        wp_enqueue_script( 'AboutPage' );
        wp_localize_script( 'AboutPage', 'pf_about', array(
            'nonce_install'           => wp_create_nonce( 'pf_install_nonce' ),
            'ajax_url'                => admin_url( 'admin-ajax.php' ),
        ) );
        
        Pie_Forms::template( 'aboutus.php' );
        Pie_Forms::template( 'related-features.php');
    }

} // End Class PFORM_Admin_Menus_Aboutus
