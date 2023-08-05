<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Tools extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $menu_slug = 'pf-tools';

    public $priority = 12;

    protected $_prefix = 'pie_forms';

    public function get_page_title()
    {
        return esc_html__( 'Tools', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        Pie_Forms::template( 'tools.php' );
        Pie_Forms::template( 'related-features.php');
        wp_enqueue_style('ToolsPage', Pie_Forms::$url . 'assets/css/tools.css', array(), Pie_Forms::VERSION );

    }

} // End Class PFORM_Admin_Tools
