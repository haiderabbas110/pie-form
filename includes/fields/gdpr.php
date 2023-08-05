<?php
defined( 'ABSPATH' ) || exit;
if(get_option('pf_gdpr_options') === 'yes'){
    class PFORM_Fields_GDPR extends PFORM_Abstracts_Fields {
    
        /**
         * Constructor.
         */
        public function __construct() {
            
            
            $this->name     = esc_html__( 'GDPR Agreement', 'pie-forms' );
            $this->type     = 'gdpr';
            $this->icon     = 'gdpr';
            $this->order    = 100;
            $this->group    = 'advanced';
            $this->defaults = array(
                    1 => array(
                        'label'   => esc_html__( 'I consent to having this website store my submitted information so they can respond to my inquiry.', 'pie-forms' ),
                        'value'   => '',
                        'image'   => '',
                        'default' => '',
                    ),
                );
            $this->settings = array(
                'basic-options'    => array(
                    'field_options' => array(
                        'label',
                        'meta',
                        'description',
                        'gdpr_agreement_checkbox',
                        // 'required',
                        'required_field_message',
                    ),
                ),
                'advanced-options' => array(
                    'field_options' => array(
                        'show_values',
                        'label_hide',
                        'css',
                    ),
                ),
            );
    
            parent::__construct();
        }
    
         /**
         * Field preview inside the builder.
         */
        public function field_preview( $field , $form_data ) {
            $label = empty($field["gdpr-checkbox"]) ? 'I consent to having this website store my submitted information so they can respond to my inquiry.' : $field["gdpr-checkbox"];
            // Label.
            $this->field_preview_option( 'label', $field );
            
            // Primary input.
            echo '<input type="checkbox" class="widefat"  disabled>';
            echo '<label class="pie-gdpr-label">'.esc_html($label).'</label>';

            // Description.
            $this->field_preview_option( 'description', $field );
        }
        /**
         * Field display on the form front-end.
         */
        public function field_display( $field, $field_atts, $form_data ) {
            // Define data.
            $primary 				= $field['properties']['inputs']['primary'];
            $label = empty($field["gdpr-checkbox"]) ? 'I consent to having this website store my submitted information so they can respond to my inquiry.' : $field["gdpr-checkbox"];
            // Primary field.
            printf(
                '<div class="pie-field-gdpr-consent"><input type="checkbox" %s %s>',
                Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
                esc_attr( 'required' )
            );
            echo '<label class="pie-gdpr-agreement-label" for="'.esc_attr($primary['id']).'">'.esc_html($label).'</label></div>';
        }
    }
}
