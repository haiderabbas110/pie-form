<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Core_Install {


	public function __construct()
    {
		$this->init();
	}
	
	public static function init() {
		
		add_action( 'init', array( __CLASS__, 'upgrade' ), 5 );
		add_action( 'init', array( __CLASS__, 'new_install' ), 5 );
	}

	/**
	 * Check PieForms version and run the updater is required.
	*/
	public static function upgrade() {
		
		//update db version
		if ( version_compare( get_option( 'pie_forms_db_version' ), Pie_Forms::DB_VERSION, '<' ) ) {
			
			$database = new PFORM_Database_DbTables();
            $database->run();
			
			update_option("pie_forms_db_version", Pie_Forms::DB_VERSION);

			
		}
		
		//update plugin version
		if ( version_compare( get_option( 'pie_forms_version' ), Pie_Forms::VERSION, '<' ) ) {
			update_option("pie_forms_version", Pie_Forms::VERSION);
		}
		
	}

	//If New Install

	public static function new_install() {
		if( !get_option('pie_forms_version') ){

			update_option("pie_forms_version", Pie_Forms::VERSION);
		}

		if( !get_option('pie_forms_db_version') ){

			update_option("pie_forms_db_version", Pie_Forms::DB_VERSION);
		}
	}
	
}