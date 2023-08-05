<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_AllForms extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $page_title = 'Pie Forms Builder';
    
    public $menu_title = 'All Forms';

    public $menu_slug = 'pie-forms';

    public $priority = 1;

    public function __construct()
    {
        parent::__construct();
    }

    // public function get_page_title()
    // {
    //     return esc_html__( 'All Forms', 'pie-forms' );
    // }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        // This section intentionally left blank.
        Pie_Forms::template( 'related-features.php');
    }
}
