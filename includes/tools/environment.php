<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Tools_Environment.
 */
class PFORM_Tools_Environment extends PFORM_Abstracts_Tools {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'environment';
		$this->label = esc_html__( 'Environment', 'pie-forms' );
		parent::__construct();
		
	}

}