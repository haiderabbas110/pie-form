<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Tools_Import.
 */
class PFORM_Tools_Import extends PFORM_Abstracts_Tools {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'import';
		$this->label = esc_html__( 'Import', 'pie-forms' );
		parent::__construct();
		
	}

}