<?php

class PFORM_Blockuser_Emailaddress extends PFORM_Abstracts_Blockuser {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'emailaddress';
        $this->label = esc_html__( 'Block User By Email Address', 'pie-forms' );

        parent::__construct();
    }

    /**
     * Get settings array.
     */
    public function get_settings() {
        $settings = apply_filters(
            'pie_forms_blockuser_emailaddress',
            array(
                array(
                    'title' => esc_html__( 'Note : For every single email address use new line.' , 'pie-forms' ),
                    'type'  => 'title',
                    'id'    => 'block_emailaddress_options',
                ),
                array(
					'type'     => 'checkbox',
                    'title'    => esc_html__( 'Enable Block Email Address', 'pie-forms' ),
                    'desc'     => esc_html__( 'Do not allow email addresses listed below to fill form on my site', 'pie-forms' ),
					'id'       => 'pf_enable_block_emailaddress',
                    'default'  => 'no',
                    'disabled' => 'disabled',
				),
                array(
					'type'          => 'textarea',
                    'title'         => esc_html__( 'Email Addresses', 'pie-forms' ),
					'id'            => 'pf_block_emailaddress',
                    'desc'          =>   __('<strong>Example: </strong><br>some@example.com<br>*@domain.com<br>Give (*) to block user containing that domain' , 'pie-forms'),
					'disabled'       => 'disabled',
				),
                array(
                    'type' => 'sectionend',
                    'id'   => 'block_emailaddress_options',
                ),
            )
        );
        return apply_filters( 'pie_forms_get_settings_' . $this->id, $settings );
    }
}
