<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Addons extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $menu_slug = 'pf-addons';

    public $priority = 16;

    protected $_prefix = 'pie_forms';

    public function get_page_title()
    {
        return  esc_html__( 'Addons', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        wp_enqueue_style('AddonsPage', Pie_Forms::$url . 'assets/css/addons.css', array(), Pie_Forms::VERSION );
        wp_register_script( 'AddonsPageJS', Pie_Forms::$url . 'assets/js/addons.js', array(), Pie_Forms::VERSION );
        wp_enqueue_script( 'AddonsPageJS' );
        wp_localize_script( 'AddonsPageJS', 'pf_addon', array(
            'nonce_addons'            => wp_create_nonce( 'pf_addons_nonce' ),
            'ajax_url'                => admin_url( 'admin-ajax.php' ),
        ) );
        wp_enqueue_style( 'fontawesomev515', Pie_Forms::$url . 'assets/css/lib/fontawesome.min.css', array(), Pie_Forms::VERSION );
        wp_enqueue_script( 'fontawesome');
        Pie_Forms::template( 'addons.php' );
        Pie_Forms::template( 'related-features.php');
    }

} // End Class PFORM_Admin_Menus_Addons