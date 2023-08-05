<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Rating extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name   = esc_html__( 'Rating', 'pie-forms' );
		$this->type   = 'rating';
		$this->icon   = 'rating';
		$this->order  = 230;
		$this->group  = 'advanced';
		$this->is_pro = true;

		parent::__construct();
	}

}
