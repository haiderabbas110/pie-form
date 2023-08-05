<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_AddNew extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    //public $page_title = '';

    public $menu_slug = 'pie-addnew';

	public $priority = 11;

    protected $_prefix = 'pie_forms';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_page_title()
    {
        return esc_html__( 'New Form', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        Pie_Forms::template( 'add-new.php' );
        Pie_Forms::template( 'related-features.php');
		

    }

} // End Class PFORM_Admin_Addnew
