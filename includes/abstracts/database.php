<?php if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

abstract class PFORM_Abstracts_Database
{
    public $table_name = '';

    public $charset_collate = '';

    public $flag = '';


    /**
     * @param $table_name (String) The database table name managed by the extension.
     */
    public function __construct( $table_name )
    {
        $this->table_name =  $table_name;
    }

    /**
     * Function to retrieve the full table name of the extension.
     * 
     * @return (String) The full table name, including database prefix.
     * 
     */
    public function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }
   
    /**
     * Function to run our initial migration.
     */
    public function _run()
    {
        // Check the flag
        if( get_option( $this->flag, FALSE ) ) return;

        // Run the migration
        $this->run();

        // Set the Flag
        update_option( $this->flag, TRUE );
    }

    /**
     * Abstract protection of inherited funciton charset_collate.
     */
    public function charset_collate( $use_default = false )
    {
        $response = '';
        global $wpdb;
        // If our mysql version is 5.5.3 or higher...
        if ( version_compare( $wpdb->db_version(), '5.5.3', '>=' ) ) {
            // We can use mb4.
            $response = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        } // Otherwise...
        else {
            // We use standard utf8.
            $response = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
        }
        // If we need to use default...
        if ( $use_default ) {
            // Append that to the response.
            $response = 'DEFAULT ' . $response;
        }
        return $response;
    }

    /**
     * Abstract protection of inherited funciton run.
     */
    protected abstract function run();

    /**
     * Protection of inherited function drop.
     */
    protected function drop()
    {
        // This section intentionally left blank.
    }
}
