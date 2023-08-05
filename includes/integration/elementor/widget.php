<?php

/**
 * Pieforms widget for Elementor page builder.
 */

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class PFORM_Integration_Elementor_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve shortcode widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {

		return 'pieforms';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve shortcode widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {

		return __( 'pieforms', 'pie-forms' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve shortcode widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {

		return 'icon-pieforms';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {

		return [
			'form',
			'forms',
			'pieforms',
			'contact form',
			'sullie',
			'the dude',
		];
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {

		return [
			'basic',
		];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function _register_controls() { 

		$this->content_controls();
	}

	/**
	 * Register content tab controls.
	 */
	protected function content_controls() {

		$this->start_controls_section(
			'section_form',
			[
				'label' => esc_html__( 'Form', 'pie-forms' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$forms = $this->get_forms();

		if (  count($forms ) < 2 ) {
			$this->add_control(
				'add_form_notice',
				[
					'show_label'      => false,
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => wp_kses(
						__( '<b>You haven\'t created a form yet.</b><br> What are you waiting for?', 'pie-forms' ),
						[
							'b'  => [],
							'br' => [],
						]
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_control(
			'form_id',
			[
				'label'       => esc_html__( 'Form', 'pie-forms' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $forms,
				'default'     => '0',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_display',
			[
				'label'     => esc_html__( 'Display Options', 'pie-forms' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'form_id!' => '0',
				],
			]
		);

		$this->add_control(
			'display_form_name',
			[
				'label'        => esc_html__( 'Form Name', 'pie-forms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pie-forms' ),
				'label_off'    => esc_html__( 'Hide', 'pie-forms' ),
				'return_value' => 'yes',
				'condition'    => [
					'form_id!' => '0',
				],
			]
		);

		$this->add_control(
			'display_form_description',
			[
				'label'        => esc_html__( 'Form Description', 'pie-forms' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pie-forms' ),
				'label_off'    => esc_html__( 'Hide', 'pie-forms' ),
				'separator'    => 'after',
				'return_value' => 'yes',
				'condition'    => [
					'form_id!' => '0',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			$this->render_edit_mode();
		} else {
			$this->render_frontend();
		}
	}

	/**
	 * Render widget output in edit mode.
	 *
	 * @since 1.6.3.1
	 */
	protected function render_edit_mode() {

		$form_id = $this->get_settings_for_display( 'form_id' );
		if ( empty( $form_id ) ) {
		
			$form_image = '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 30 46" role="img" aria-hidden="true" focusable="false"><path fill="currentColor" d="M30,0H6C2.7,0,0,2.7,0,6V30c0,3.3,2.7,6,6,6H30c3.3,0,6-2.7,6-6V6C36,2.7,33.3,0,30,0z M17.2,27.3h-5.1v-3.4h5.1V27.3z M24.1,19.7H12.1v-3.4h11.9V19.7z M26.6,12H12.1V8.6h14.5V12z"/></svg>';
			$forms = $this->get_form_selector_options();
			$forms_block = '<div class="pieforms-elementor pieforms-elementor-form-selector">';
				$forms_block .= $form_image;
				$forms_block .= '<div class="elementor-placeholder">Pie Forms</div>';
			$forms_block .= '</div>';
			
			echo wp_kses($forms_block, Pie_Forms()->core()->pform_get_allowed_tags());
			return;
		}
		// Finally, render selected form.
		$this->render_frontend();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 1.6.3.1
	 */
	protected function render_frontend() {

		// Render selected form.
		echo wp_kses(do_shortcode( $this->render_shortcode() ), Pie_Forms()->core()->pform_get_allowed_tags());
	}

	/**
	 * Render widget as plain content.
	 */
	public function render_plain_content() {

		echo wp_kses($this->render_shortcode(), Pie_Forms()->core()->pform_get_allowed_tags());
	}

	/**
	 * Render shortcode.
	 */
	public function render_shortcode() {

		return sprintf(
			'[pie_form id="%1$d"  title="%2$s" description="%3$s"]',
			absint( $this->get_settings_for_display( 'form_id' ) ),
			sanitize_key( $this->get_settings_for_display( 'display_form_name' ) === 'yes' ? 'true' : 'false' ),
			sanitize_key( $this->get_settings_for_display( 'display_form_description' ) === 'yes' ? 'true' : 'false' )
		);
	}

	/**
	 * Get forms list.
	 *
	 * @returns array Array of forms.
	 */
	public function get_forms() {

		static $forms_list = [];

		if ( empty( $forms_list ) ) {
			$forms = Pie_Forms()->core()->pform_get_all_forms();

			if ( ! empty( $forms ) ) {
				$forms_list[0] = esc_html__( 'Select a form', 'pie-forms' );
				foreach ( $forms as $form_id => $form_title ) {
					$forms_list[ $form_id ] = mb_strlen( $form_title ) > 100 ? mb_substr( $form_title, 0, 97 ) . '...' : esc_html($form_title);
				}
			}
		}

		return $forms_list;
				
	}

	/**
	 * Get form selector options.
	 *
	 * @returns string Rendered options for the select tag.
	 */
	public function get_form_selector_options() {

		$forms   = $this->get_forms();
		$options = '';

		foreach ( $forms as $form_id => $form ) {
			$options .= sprintf(
				'<option value="%d">%s</option>',
				(int) $form_id,
				esc_html( $form )
			);
		}

		return $options;
	}
}
