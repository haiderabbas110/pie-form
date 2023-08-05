<?php

class PFORM_Integration_Beaver_Register {

	function __construct(){
		$this->hooks();
		
    }

	protected function hooks() {
        
		add_action( 'init', array( $this, 'load_modules' ) );
	}

	public function load_modules() {
		$PFORM_Integration_Beaver_Module = new PFORM_Integration_Beaver_Module();
	}
        
}