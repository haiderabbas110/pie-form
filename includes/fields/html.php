<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_HTML extends PFORM_Abstracts_Fields
{   

    public function __construct()
    {
        $this->name     = esc_html__( 'HTML / Code Block', 'pie-forms' );
		$this->type     = 'customhtml';
		$this->icon     = 'customhtml';
		$this->order    = 180;
		$this->group    = 'advanced';
        $this->is_pro   = true;

        parent::__construct();
    }
}