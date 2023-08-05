<?php

class PFORM_Integration_Divi_Module extends ET_Builder_Module {
    
    /**
     * Module slug.
     *
     * @var string
     */
    public $slug = 'pieforms_selector';

    /**
     * VB support.
     *
     * @var string
     */
    public $vb_support = 'on';

    /**
     * Init module.
     */
    public function init() {

        $this->name = esc_html__( 'Pie Forms', 'pie-forms' );
    }

    /**
     * Get list of settings.
     *
     * @return array
     */
    public function get_fields() {

        $forms    = Pie_Forms()->core()->pform_get_all_forms();
		$forms    = array_map(
			function ( $form ) {

				return htmlspecialchars_decode( $form, ENT_QUOTES );
			},
			$forms
		);
		$forms[0] = esc_html__( 'Select form', 'pie-forms' );

        return [
            'form_id'    => [
                'label'           => esc_html__( 'Form', 'pie-forms' ),
                'type'            => 'select',
                'option_category' => 'basic_option',
                'toggle_slug'     => 'main_content',
                'options'         => $forms
            ],
            'show_title' => [
                'label'           => esc_html__( 'Show Title', 'pie-forms' ),
                'type'            => 'yes_no_button',
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
                'type'            => 'yes_no_button',
                'toggle_slug'     => 'main_content',
                'options'         => [
                    'off' => esc_html__( 'Off', 'pie-forms' ),
                    'on'  => esc_html__( 'On', 'pie-forms' ),
                ],
            ],
        ];
    }
    
    /**
	 * Disable advanced fields configuration.
	 *
	 * @since 1.6.3
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {

		return [
			'link_options' => false,
			'text'         => false,
			'background'   => false,
			'borders'      => false,
			'box_shadow'   => false,
			'button'       => false,
			'filters'      => false,
			'fonts'        => false,
		];
	}

    /**
     * Render module on the frontend.
     *
     * @param array  $attrs       List of unprocessed attributes.
     * @param string $content     Content being processed.
     * @param string $render_slug Slug of module that is used for rendering output.
     *
     * @return string
     */
    public function render( $attrs, $content = null, $render_slug ) {

        if ( empty( $this->props['form_id'] ) ) {
            return '';
        }

        return do_shortcode(
            sprintf(
                '[pie_form id="%1$s" title="%2$s" description="%3$s"]',
                absint( $this->props['form_id'] ),
                (bool) apply_filters( 'pieforms_divi_builder_form_title', ! empty( $this->props['show_title'] ) && 'on' === $this->props['show_title'], absint( $this->props['form_id'] ) ),
                (bool) apply_filters( 'pieforms_divi_builder_form_desc', ! empty( $this->props['show_desc'] ) && 'on' === $this->props['show_desc'], absint( $this->props['form_id'] ) )
            )
        );
    }
}
    