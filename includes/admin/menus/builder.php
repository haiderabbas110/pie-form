<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Builder extends PFORM_Abstracts_Submenu
{   

    public $menu_slug = 'pie-forms';

    public $priority = 9002;

    public function __construct()
    {
        parent::__construct();

        add_action( 'admin_body_class', array( $this, 'body_class' ) );
    }

    public function body_class( $classes )
    {
        // Add class for the builder.
        if( isset( $_GET['page'] ) && isset( $_GET[ 'form_id' ] ) && $_GET['page'] == $this->menu_slug ) {
            $classes = "$classes pie-forms-builder";
        }

        return $classes;
    }

    public function display()
    {
        if( isset($_GET[ 'form_id' ]) && $_GET['page'] == $this->menu_slug) {
            Pie_Forms::template( 'form-builder.php' );
            
        }

    }


} // End Class NF_Admin_Settings
