<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Entries extends PFORM_Abstracts_Submenu
{
    public $parent_slug = 'pie-forms';

    public $menu_slug = 'pf-entries';

    public $priority = 11;

    protected $_prefix = 'pie_forms';

    public function __construct()
    {
        parent::__construct();


    }

    public function get_page_title()
    {
        return esc_html__( 'Entries', 'pie-forms' );
    }

    public function get_capability()
    {
        return $this->capability;
    }

    public function display()
    {
        
        //self::output();
         
        if( $_GET['page'] == $this->menu_slug) {
        
            ?>
    
                <div class="wrap">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Entries', 'pie-forms' ); ?></h1>
                    <?php
                        $table_data      = new PFORM_Admin_EntriesTable();
                    ?>
                    <div class="upgrade-to-pro-banner"><a href="https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=entries" target="_blank"><img src="<?php echo esc_url( plugins_url( '../assets/images/builder/upgrade-to-premium.jpg', dirname(__DIR__) ));  ?>" alt="upgrade-to-premium"></a></div>
                </div>            

                <?php
                
                wp_enqueue_style('TableGrid', Pie_Forms::$url . 'assets/css/admin-table.css', array(), Pie_Forms::VERSION );
            }
            Pie_Forms::template( 'related-features.php');

    }

    public static function output() {
       
    }
    
} // End Class PFORM_Admin_Settings
