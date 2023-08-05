<?php

class PFORM_Blockuser_IPaddress extends PFORM_Abstracts_Blockuser {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'ipaddress';
        $this->label = esc_html__( 'Block User By IP Address', 'pie-forms' );

        parent::__construct();
    }

    /**
     * Get settings array.
     */
    public function get_settings() {
        $settings = apply_filters(
            'pie_forms_blockuser_ipaddress',
            array(
                array(
                    'title' => esc_html__('Note : Enter each IP address on a new line.' , 'pie-forms' ),
                    'type'  => 'title',
                    'id'    => 'block_ipaddress_options',
                ),
                array(
					'type'     => 'checkbox',
                    'title'    => esc_html__( 'Enable Block IP Address', 'pie-forms' ),
                    'desc'     => esc_html__( 'Do not allow IP Addresses listed below to fill form on my      site', 'pie-forms' ),
					'id'       => 'pf_enable_block_ipaddress',
                    'default'  => 'no',
                    'disabled'       => 'disabled',
				),
                array(
					'type'          => 'textarea',
                    'title'         => esc_html__( 'IP Addresses ', 'pie-forms' ),
					'id'            => 'pf_block_ipaddress',
                    'desc'          =>   __('<strong>Example:</strong><br>192.168.1.1<br>192.168.2.0-24 ', 'pie-forms' ),
                    'disabled'       => 'disabled',
				),
                array(
                    'type' => 'sectionend',
                    'id'   => 'block_ipaddress_options',
                ),
            )
        );
        return apply_filters( 'pie_forms_get_settings_' . $this->id, $settings );
    }

}
