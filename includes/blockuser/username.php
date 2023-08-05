<?php

class PFORM_Blockuser_Username extends PFORM_Abstracts_Blockuser {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'username';
        $this->label = esc_html__( 'Block User By Username', 'pie-forms' );

        parent::__construct();
    }

    /**
     * Get settings array.
     */
    public function get_settings() {
        $settings = apply_filters(
            'pie_forms_blockuser_username',
            array(
                array(
                    'title' => esc_html__( 'Note : For every single username use new line.', 'pie-forms'),
                    'type'  => 'title',
                    'id'    => 'blockusername_options',
                ),
                array(
					'type'     => 'checkbox',
                    'title'    => esc_html__( 'Enable Block User Name', 'pie-forms' ),
                    'desc'     => esc_html__( 'Do not allow users listed below to fill form on my      site', 'pie-forms' ),
					'id'       => 'pf_enable_block_username',
                    'default'  => 'no',
                    'disabled'       => 'disabled',
				),
                array(
					'type'     => 'textarea',
                    'title'    => esc_html__( 'Usernames ', 'pie-forms' ),
					'id'       => 'pf_block_username',
                    'desc'     => __('<strong>Example:</strong><br>johnny<br>downey<br>Give (*) to block user containing that username' , 'pie-forms'),
                    'disabled'       => 'disabled',
				),
                array(
                    'type' => 'sectionend',
                    'id'   => 'blockusername_options',
                ),
            )
        );
        return apply_filters( 'pie_forms_get_settings_' . $this->id, $settings );
    }
}
