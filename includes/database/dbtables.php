<?php if ( ! defined( 'ABSPATH' ) ) exit;
class PFORM_Database_DbTables
{
    protected $db = array();

    /**
     * Constructor method 
     */
    public function __construct()
    {
        $this->db[ 'forms' ]          = new PFORM_Database_Form();
        $this->db[ 'fields' ]         = new PFORM_Database_Fields();
        $this->db[ 'entries' ]        = new PFORM_Database_Entries();
        $this->db[ 'entrymeta' ]      = new PFORM_Database_EntryMeta();
    }
    
    
    /**
     * Function to run each db table on the stack.
     */
    public function run()
    {
        foreach( $this->db as $database ){
            $database->_run();
        }
    }

}
