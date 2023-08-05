<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_Imageupload extends PFORM_Abstracts_Fields
{   

    public function __construct()
    {
		$this->name     = esc_html__( 'Image Upload', 'pie-forms' );
		$this->type     = 'imageupload';
		$this->icon     = 'image-upload';
		$this->order    = 250;
		$this->group    = 'advanced';
        $this->is_pro   = true;

        parent::__construct();
    }
}