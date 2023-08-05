<?php

class PFORM_Integration_Beaver_Module extends FLBuilderModule {

public function __construct()
{
  parent::__construct(array(
    'name'            => __( 'Pie Forms', 'pie-forms' ),
    'description'     => __( 'A totally awesome forms!', 'pie-forms' ),
    'base'           => __( 'pie Forms', 'pie-forms' ),
    'category'        => __( 'Pie Forms', 'pie-forms' ),
    'dir'             => Pie_Forms::$dir .'/'. 'includes/integration/beaver/',
    'url'             => Pie_Forms::$url .'/', 'includes/integration/beaver/',
    'icon'            => 'icon.svg',
    'editor_export'   => true, // Defaults to true and can be omitted.
    'enabled'         => true, // Defaults to true and can be omitted.
    'partial_refresh' => false, // Defaults to false and can be omitted.
  ));

  
}


    /**
     * Get list of settings.
     *
     * @return array
     */

public static function get_fields() {

  $forms    = Pie_Forms()->core()->pform_get_all_forms();
$forms    = array_map(
function ( $form ) {

  return htmlspecialchars_decode( $form, ENT_QUOTES );
},

  $forms

);
$forms[0] = esc_html__( 'Select form', 'pie-forms' );
return [
  'my-tab-1'      => [
    'title'    =>  __( 'Pie Forms', 'pie-forms' ),
      'sections'      => [
        'my-section-1'  => [
        'title'           => esc_html__( 'Form', 'pie-forms' ),
        'fields' => array(
          'form_id'         => array(
            'type'    => 'select',
            'option_category' => 'basic_option',
            'toggle_slug'     => 'main_content',
            'label'   => __( 'Select form', 'pie-forms' ),
            'options' => $forms,
            ),
          'show_title' => [
            'label'           => esc_html__( 'Show Title', 'pie-forms' ),
            'type'            => 'button-group',
            'option_category' => 'basic_option',
            'toggle_slug'     => 'main_content',
            'options'         => [
                'off' => esc_html__( 'Off', 'pie-forms' ),
                'on'  => esc_html__( 'On', 'pie-forms' ),
            ],
        ],
        'show_desc'  => [
            'label'           => esc_html__( 'Show Description', 'pie-forms' ),
            'option_category' => 'basic_option',
            'type'            => 'button-group',
            'toggle_slug'     => 'main_content',
            'options'         => [
                'off' => esc_html__( 'Off', 'pie-forms' ),
                'on'  => esc_html__( 'On', 'pie-forms' ),
            ],
        ],
        ),
      ],
      
      ]
  ],
];


}


}
FLBuilder::register_module( 'PFORM_Integration_Beaver_Module', PFORM_Integration_Beaver_Module::get_fields());