<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class PFORM_Admin_Menus_Main extends PFORM_Abstracts_Menu
{
    public $page_title = 'Pie Forms';

    public $menu_slug = 'pie-forms';

    public $icon_url = 'dashicons-text-page';

    public $position = '38';

    public $ver = Pie_Forms::VERSION;
    public $field_type_settings = array();

    public function __construct()
    {
        parent::__construct();

            

    }


    public function get_page_title()
    {
        return esc_html__( 'Pie Forms', 'pie-forms' );
    }

  
    public function display()
    {

        wp_register_script( 'MainJs', Pie_Forms::$url . 'assets/js/main.js', array(),  Pie_Forms::VERSION );
        wp_enqueue_script( 'MainJs' );
        wp_localize_script( 'MainJs', 'pf_main_data', array(
            'pf_enabled_form'         => wp_create_nonce( 'pie_forms_enabled_form' ),
            'ajax_url'                => admin_url( 'admin-ajax.php' ),
        ) );

        
        if( !isset($_GET[ 'form_id' ]) && $_GET['page'] == $this->menu_slug) {
        
            do_action( 'pie_forms_admin_pages_before_content' ); 
        ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?php esc_html_e( 'All Forms', 'pie-forms' ); ?></h1>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=pie-addnew' ) ); ?>" class="page-title-action"><?php esc_html_e( 'New Form', 'pie-forms' ); ?></a>
                <?php
                    $table_data      = new PFORM_Admin_AllFormTable();
                ?>
            </div>            

            <?php
            
            $arguments = array(
                'label'     =>  __( 'Users Per Page', 'pie-forms' ),
                'default'   =>  5,
                'option'    =>  'pie_form_per_page'
            );
            add_screen_option( 'per_page', $arguments );

            wp_enqueue_style('TableGrid', Pie_Forms::$url . 'assets/css/admin-table.css', array(), Pie_Forms::VERSION );

            $images_url = Pie_Forms::$url . 'assets/images/how-to-use/';
            Pie_Forms::template( 'related-features.php');
            Pie_Forms::template( 'recommended-docx.php' , array(
                'docx' => array(
                        array(
                            'image'         => $images_url.esc_html('forms-secure.png' , 'pie-forms'),              
                            'name'          => esc_html('Make Your Forms Secure with Pie Forms' , 'pie-forms'),              
                            'link'          => esc_url( "https://pieforms.com/documentation/how-to-make-your-forms-secure-with-pie-forms/?utm_source=admindashboard&utm_medium=howtosection&utm_campaign=premium" , 'pie-forms'),
                            'description'   => esc_html( "Secure forms are crucial for your website. They do not only protect you from spam but also ensures security for your user’s data." , 'pie-forms')
    
                        ),
                        array(
                            'image'         => $images_url.esc_html('global-settings.png' , 'pie-forms'),              
                            'name'          => esc_html('Global Settings' , 'pie-forms'),              
                            'link'          => esc_url( "https://pieforms.com/documentation/global-settings-2/?utm_source=admindashboard&utm_medium=howtosection&utm_campaign=premium" , 'pie-forms'),
                            'description'   => esc_html( "Pie Forms Global settings can be configured from your Pie Forms Admin Dashboard." , 'pie-forms')
                        ),
                        array(
                            'image'         => $images_url.esc_html('email-settings.png' , 'pie-forms'),              
                            'name'          => esc_html('Email Settings' , 'pie-forms'),              
                            'link'          => esc_url( "https://pieforms.com/documentation/email-settings/?utm_source=admindashboard&utm_medium=howtosection&utm_campaign=premium" , 'pie-forms'),
                            'description'   => esc_html( "A confirmation email sent to your targeted audience to ascertain their online form submission is very essential." , 'pie-forms')     
                        ),
                        array(
                            'image'         => $images_url.esc_html('gdpr-appliance.png' , 'pie-forms'),              
                            'name'          => esc_html('GDPR Compliance' , 'pie-forms'),              
                            'link'          => esc_url( "https://pieforms.com/documentation/how-to-make-your-forms-gdpr-compliant-using-pie-forms/?utm_source=admindashboard&utm_medium=howtosection&utm_campaign=premium" , 'pie-forms'),
                            'description'   => esc_html( "General Data Protection Regulation (GDPR) is a law by the European Union (EU) requiring explicit consent before collecting or storing user’s data." , 'pie-forms')     
                        )    
                    )
                )
            );
        }

    }

  
    public function get_capability()
    {
        return apply_filters( 'pie_forms_admin_parent_menu_capabilities', $this->capability );

    }

   /*  public function screen_option(){
      return true;
    }
 */
}
