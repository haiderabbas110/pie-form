<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_Signature extends PFORM_Abstracts_Fields
{   

    public function __construct()
    {
        $this->name     = esc_html__( 'Signature', 'pie-forms' );
		$this->type     = 'signature';
		$this->icon     = 'signature';
		$this->order    = 170;
        $this->group    = 'advanced';
        $this->is_pro   = true;

        parent::__construct();
    }
}