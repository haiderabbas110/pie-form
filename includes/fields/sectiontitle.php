<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_SectionTitle extends PFORM_Abstracts_Fields
{   

    public function __construct()
    {
        $this->name     = esc_html__( 'Section Title', 'pie-forms' );
		$this->type     = 'sectionTitle';
		$this->icon     = 'sectionTitle';
		$this->order    = 150;
        $this->group    = 'advanced';
        // $this->is_pro   = false;
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'css',
				),
			),
		);
		parent::__construct();
        
    }

    /**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data) {
		// Label.
		$this->field_preview_option( 'label', $field );
		// Description.
		$this->field_preview_option( 'description', $field );
	}
}