<?php
/*
Plugin Name: Pie Forms - Basic
Description: The Intelligent and Easy WordPress Form Builder Dynamic forms, Built-in Ready to use Templates, Advanced field options, Anti-spam features, and more!
Version: 1.4.16
Author: Pie Forms
Author URI: https://www.pieforms.com
Text Domain: pie-forms
Domain Path: /languages/
*/

// Define PF_PLUGIN_FILE.
if ( ! defined( 'PF_PLUGIN_FILE' ) ) {
	define( 'PF_PLUGIN_FILE', __FILE__ );
}

//to change in future

if(file_exists(plugin_dir_path( __FILE__ ).'includes/templates/welcome.php')) require_once(plugin_dir_path( __FILE__ ).'includes/templates/welcome.php');

register_activation_hook(__FILE__, 'deactivate_premium');

/**
  * Activation
 */
function deactivate_premium() {

    // Add transient to trigger redirect to the Welcome screen.
	set_transient( 'pieforms_activation_redirect', true, 300 );
    
	add_option( 'pie_forms_activated_time', time(), '', false );

    if(is_plugin_active('pie-forms-for-wp-premium/pie-forms-for-wp-premium.php') )
	{  
		deactivate_plugins('pie-forms-for-wp-premium/pie-forms-for-wp-premium.php');				
    }
}

if( !class_exists('Pie_Forms')){
final class Pie_Forms
    {

        const VERSION = '1.4.16';
        
        const DB_VERSION = '1.2';

        private static $instance;

        /**
         * Plugin Directory
         *
         * @var string $dir
         */
        public static $dir = '';
        
        /**
         * Plugin URL
         *
         * @var string $url
         */
        public static $url = '';

        /**
         * Admin Menus
         *
         * @var array
         */
        public $menus = array();
        
        public $builder = array();
        
        
        public $task;
        
        public $db = array();

        /**
         * AJAX Controllers
         *
         * @var array
         */
        public $ajax = array();

        /**
         * Form Fields
         *
         * @var array
         */
        public $fields = array();
        public $core = array();
        public $form_fields = array();

        /**
         * Form Actions
         *
         * @var array
         */
        public $actions = array();

        /**
         * Merge Tags
         *
         * @var array
         */
        public $merge_tags = array();

        /**
         * Metaboxes
         *
         * @var array
         */
        public $metaboxes = array();

        /**
         * Model Factory
         *
         * @var object
         */
        public $factory = '';

        /**
         * Logger
         *
         * @var string
         */
        protected $_logger = '';

        /**
         * Dispatcher
         *
         * @var string
         */
        protected $_dispatcher = '';

        /**
         * @var PFORM_Session
         */
        public $session = null;

        /**
         * @var PFORM_Tracking
         */
        public $tracking;

        /**
         * Plugin Settings
         */
        protected $settings = array();

        protected $requests = array();

        protected $processes = array();

        /**
		 * Paid returns true, free (Lite) returns false.
		 *
		 * @var bool
		 */
		public $pro = false;

       /**
         * PieForms Constructor.
        */
        public function __construct() {
           
            
        }


        public static function instance()
        {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Pie_Forms ) ) {
                self::$instance = new Pie_Forms;
                self::$dir = plugin_dir_path( __FILE__ );
                self::$url = plugin_dir_url( __FILE__ );
               
                /*
                * Register our autoloader
                */
                spl_autoload_register( array( self::$instance, 'autoloader' ) );
                
                /*
                * Admin Menus
                */
                self::$instance->menus[ 'main' ]                = new PFORM_Admin_Menus_Main();
                self::$instance->menus[ 'form_id' ]             = new PFORM_Admin_Menus_Builder();
                self::$instance->menus[ 'all_forms' ]           = new PFORM_Admin_Menus_AllForms();
                self::$instance->menus[ 'add_new' ]             = new PFORM_Admin_Menus_AddNew();
                self::$instance->menus[ 'settings' ]            = new PFORM_Admin_Menus_Settings();
                self::$instance->menus[ 'marketing' ]           = new PFORM_Admin_Menus_Marketing();
                self::$instance->menus[ 'entries' ]             = new PFORM_Admin_Menus_Entries();
                self::$instance->menus[ 'tools' ]               = new PFORM_Admin_Menus_Tools();
                self::$instance->menus[ 'blockuser' ]           = new PFORM_Admin_Menus_Blockuser();
                self::$instance->menus[ 'aboutus' ]             = new PFORM_Admin_Menus_Aboutus();
                self::$instance->menus[ 'addons' ]              = new PFORM_Admin_Menus_Addons();
                self::$instance->menus[ 'smarttranslation' ]    = new PFORM_Admin_Menus_Smarttranslation();
                self::$instance->ajax                           = new PFORM_Core_Ajax();
                self::$instance->task                           = new PFORM_Form_Task();
                
                
                
                register_activation_hook( __FILE__, array( self::$instance, 'activation' ) );
                register_deactivation_hook( __FILE__, array( self::$instance, 'deactivate' ) );
                
                /*
                * Builders
                */

                  // Stop all if Divi is not enabled
                    // if(class_exists('ET_Builder_Module')){
                        self::$instance->divi[ 'register' ]             = new PFORM_Integration_Divi_Register();
                    // }

                    // Stop all if Elementor is not enabled
                    if (class_exists( '\Elementor\Plugin' ) ) {
                        self::$instance->elementor[ 'register' ]        = new PFORM_Integration_Elementor_Register();
                    }
                    
                    // Stop all if VC is not enabled
                    if ( defined( 'WPB_VC_VERSION' ) ) {
                        self::$instance->wpbakery[ 'register' ]     = new PFORM_Integration_WPbakery_Register();
                    }

                    if ( class_exists( 'FLBuilder' ) ) {
                        self::$instance->beaver[ 'register' ]     = new PFORM_Integration_Beaver_Register();
                    }
                    
                add_action( 'plugins_loaded', array( self::$instance, 'plugins_loaded' ) );
                
                
               
                $shortcode                          = new PFORM_Shortcodes_Form();
                $display                            = new PFORM_Shortcodes_Display();                    
                $block                              = new PFORM_Shortcodes_Block();
                $PFORM_Settings_reCAPTCHA           = new PFORM_Settings_reCAPTCHA();
                $PFORM_Settings_Validation          = new PFORM_Settings_Validation();
                $PFORM_Settings_General             = new PFORM_Settings_General();
                $PFORM_Settings_Email               = new PFORM_Settings_Email();
                $PFORM_Settings_Licence             = new PFORM_Settings_Licence();
                $PFORM_Settings_Integrations        = new PFORM_Settings_Integrations();
                $PFORM_Tools_Environment            = new PFORM_Tools_Environment();
                $PFORM_Tools_PluginsThemes          = new PFORM_Tools_PluginsThemes();
                $PFORM_Tools_Import                 = new PFORM_Tools_Import();
                $PFORM_Blockuser_Username           = new PFORM_Blockuser_Username();
                $PFORM_Blockuser_Emailaddress       = new PFORM_Blockuser_Emailaddress();
                $PFORM_Blockuser_IPaddress          = new PFORM_Blockuser_IPaddress();
                $PFORM_Aboutus_Aboutus              = new PFORM_Aboutus_Aboutus();
                $tag                                = new PFORM_Email_Tags();
                $PFORM_Admin_Editor                 = new PFORM_Admin_Editor();
                $PFORM_db_Models_FormsEntries       = new PFORM_Database_Models_FormsEntries();
                $PFORM_Core_Install                 = new PFORM_Core_Install();
                $Import_Cf7                         = new PFORM_Admin_Importers_Contactform7();
                // $PFORM_Elementor_Elementor           = new PFORM_Elementor_Elementor();
                $PFORM_Admin_Adminmenubar           = new PFORM_Admin_Adminmenubar();

                // Updates of add-ons
                $PFORM_Api_Apikey                   = new PFORM_Api_Apikey();
                $PFORM_Api_Passwords                = new PFORM_Api_Passwords();
                
                //ENQUEUE
                $PFORM_Enqueue_Assets               = new PFORM_Enqueue_Assets();
                
                //NOTICES
                $PFORM_Notices_Notices              = new PFORM_Notices_Notices();

                add_action( 'init', array( 'PFORM_Shortcodes_Display', 'init' ), 0 );
                add_action( 'init', array( 'PFORM_Form_Preview', 'init' ) );

                add_filter( 'plugin_action_links', array(self::$instance,'add_action_links'),10,2 );
                
                add_action('admin_init' , array(self::$instance, 'pf_admin_notification') ,1);
            }
            
            return self::$instance;
        }

        /**
         * Activation
         */
        public function activation() {

            //create PIEFORMS tables 
            $database = new PFORM_Database_DbTables();
            $database->run();
        }
        
        /**
         * Dectivation
         */
        
        public function deactivate() {
            
        }

            /**
             * Admin Notifications
             */
            public function pf_admin_notification(){
                add_action( 'pie_forms_notice_in_header', array( $this, 'pf_gopro_notice' ), 1000 );

                if( is_admin() && current_user_can('administrator') )
                {
                    add_action( 'in_admin_header', array( $this, 'pieforms_admin_header' ), 1 );
                  
                }
                add_action( 'admin_notices', array( $this, 'pf_notices' ), 1 );
            }
            
        /**
         * Get the plugin url.
         */
        public function plugin_url() {
            return untrailingslashit( plugins_url( '/', PF_PLUGIN_FILE ) );
        }

        public function form(  )
        {
            $forms = new PFORM_Database_Models_Forms;
            return $forms;

        }

        public function templateView(){
            $PFORM_Admin_Template = new PFORM_Admin_Template;
            return $PFORM_Admin_Template;
        }

        public function core(  ){
            $core = new PFORM_Core_Functions();
            
            return $core;
        }

        /**
         * Plugin Action Links
         */
        
        function add_action_links( $links, $file ) 
        {
                if ( $file != plugin_basename( __FILE__ ))
                return $links;
        
            $action_links = array();
            $action_links[] = '<a style="color:#13ad11;font-weight:700;" class="go-pro" href="https://pieforms.com/plan-and-pricing/">'.(__("Go Premium","pie-forms")).'</a>';
            $action_links[] = '<a href="'. get_admin_url(null, 'admin.php?page=pf-settings') .'">'.(__("Settings","pie-forms")).'</a>';
            $action_links[] = '<a href="'. get_admin_url(null, 'admin.php?page=pf-aboutus') .'">'.(__("About Us","pie-forms")).'</a>';
            return array_merge( $action_links, $links );
        }

        /**
         * Load Classes from Directory
         *
         * @param string $prefix
         * @return array
         */
        private static function load_classes( $prefix = '' )
        {
            $return = array();
            
            $subdirectory = str_replace( '_', DIRECTORY_SEPARATOR, str_replace( 'PFORM_', '', $prefix ) );
            $lower_case_subdir = strtolower($subdirectory);
            $directory = 'includes/' . $lower_case_subdir;
            foreach (scandir( self::$dir . $directory ) as $path) {
                
                $path = explode( DIRECTORY_SEPARATOR, str_replace( self::$dir, '', $path ) );
                $filename = str_replace( '.php', '', end( $path ) );
                $class_name = 'PFORM_' . $prefix . '_' . $filename;
                
                if( ! class_exists( $class_name ) ) continue;
                
                $return[] = new $class_name;
            }

            return $return;
        }
       
        /**
         * Autoloader
         *
         * Autoload Pie Forms classes
         *
         * @param $class_name
         */
        public function autoloader( $class_name )
        {
            if( class_exists( $class_name ) ) return;

            /* Pie Forms Prefix */
            if (false !== strpos($class_name, 'PFORM_')) {
                $class_name = str_replace('PFORM_', '', $class_name);
                $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
                $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
                $class_lower = strtolower($class_file);
                if (file_exists($classes_dir . $class_lower)) {
                    require_once $classes_dir . $class_lower;
                }

            }

        }


        public function plugins_loaded()
        {
            self::$instance->builder['field']                        = new PFORM_Admin_Builder_Field();
            self::$instance->builder['setting']                      = new PFORM_Admin_Builder_Settings();

            /*
             * Field Class Registration
             */
            self::$instance->fields = apply_filters( 'PFORM_forms_register_fields', self::load_classes( 'Fields' ) );
            
            // Get sort order.
            $order_end = 999;
            foreach ( self::$instance->fields as $field ) {
               
                if ( isset( $field->order ) && is_numeric( $field->order ) ) {
                    // Add in position.
                    self::$instance->form_fields[ $field->group ][ $field->order ] = $field;
                } else {
                    // Add to end of the array.
                    self::$instance->form_fields[ $field->group ][ $order_end ] = $field;
                    $order_end++;
                }
               
                ksort( self::$instance->form_fields[ $field->group ] );
                
            }

            load_plugin_textdomain( 'pie-forms', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
         
        }

        /**
         * Get entry instance.
         */
        public function entry( )
        {
            $entries = new PFORM_Database_Models_FormsEntries;
            return $entries;

        }

        //Reverse the Starting Two Elements

        public function moveElement(&$array) {
            $Temparray = array_slice($array, 0, 2);
            $Temparray = array_reverse($Temparray);
            $array = array_slice($array, 2);
            return array_merge($Temparray, $array );
        }
        
        public function form_fields() {
            $_available_fields = array();
            if ( count( self::$instance->form_fields ) > 0 ) {
                foreach ( self::$instance->form_fields as $group => $field ) {
                    $_available_fields[ $group ] = $field;
                }
            }
                return $this->moveElement($_available_fields);
        }

        /*
         * STATIC METHODS
         */

        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template( $file_name = '', array $data = array(), $return = FALSE )
        {
            if( ! $file_name ) return FALSE;

            extract( $data );

            $path = self::$dir . 'includes/templates/' . $file_name;
            if( ! file_exists( $path ) ) return FALSE;

            if( $return ) 
                return wp_safe_remote_get( $path );
            include $path;
        }

         /* Admin Notices */

        public function pf_notices(){
            echo apply_filters('addon_notices' ,true);
        }

         /* End Admin Notices */

         public function pf_gopro_notice() {
             ?>
                <div class="pie-notice-pro is-dismissible"> 
                <p>
                    <img src="<?php echo esc_url(self::$url."assets/images/pie_gift_icon.png"); ?>" />
                    <?php esc_html_e( 'Automate form entries through powerful features and addons. ' , 'pieforms'); ?><a href='https://pieforms.com/plan-and-pricing/?utm_source=adminarea&utm_medium=gopro&utm_campaign=viewplan' target="_blank"> Get 20% off on all plans.</a><?php esc_html_e(' Use code "automate" at checkout. Limited time offer.', 'pie-forms' ); ?>
                </p>
                        <button style="top: 28px;" type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
                    </div>
             <script  type="text/javascript">
                             jQuery('.pie-notice-pro .notice-dismiss').on( 'click.dismiss-notice', function( event ) {
                             event.preventDefault();
                             jQuery('.pie-notice-pro').fadeTo( 100, 0, function() {
                                 jQuery('.pie-notice-pro').slideUp( 100, function() {
                                     jQuery('.pie-notice-pro').remove();
                                 });
                             });
                         });
             </script>
             <?php
         }
            public function pieforms_admin_header() 
            {		
                // Omit header from Welcome activation screen.
                if ( isset($_REQUEST['page']) && 'pieforms-getting-started' === $_REQUEST['page'] ) 
                {
                    return;
                }
                
                if ( empty( $_REQUEST['page'] ) || ( (strpos( $_REQUEST['page'], 'pie-' ) === false) && (strpos( $_REQUEST['page'], 'pf' ) === false) )) {
                    return false;
                }
                
                do_action( 'pieforms_admin_header_before' );
                ?>
                <div id="pf-header-temp"></div>
                <div id="pf-header" class="pf-header">
                    <div class="pf-notice">
                        <?php do_action( 'pie_forms_notice_in_header'); ?>
                    </div>
                </div>
                <?php
                do_action( 'pieforms_admin_header_after' );
            }

    } // End Class Pie_Forms

    /**
     * The main function responsible for returning The Highlander Pie_Forms
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * Example: <?php $Pie = Pie_Forms(); ?>
     *
     * @return Pie_Forms Highlander Instance
     */
} 

if( !function_exists( 'Pie_Forms' ) ) {
    function Pie_Forms()
    {
        return Pie_Forms::instance();
    }   
} 
Pie_Forms();