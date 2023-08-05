<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Radio extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Radio Buttons', 'pie-forms' );
		$this->type     = 'radio';
		$this->icon     = 'radio-button';
		$this->order    = 60;
		$this->group    = 'basic';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'First Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'image'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Second Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'image'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Third Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'image'   => '',
				'default' => '',
			),
		);
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'choices',
					'choices_images',
					'description',
					'required',
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
			'conditional-logic' => array(
				'field_options' => array(
					'upgrade-to-pro',
				)
			)
		);

		parent::__construct();
	}

	/**
	 * Hook in tabs.
	 */
	public function init_hooks() {
		add_filter( 'pie_forms_html_field_value', array( $this, 'html_field_value' ), 10, 4 );
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Return images, if any, for HTML supported values.
	 */
	public function html_field_value( $value, $field, $form_data = array(), $context = '' ) {
		if ( is_serialized( $field ) || in_array( $context, array( 'email-html', 'export-pdf' ), true ) ) {
			$field_value = maybe_unserialize( $field );
			$field_type  = isset( $field_value['type'] ) ? sanitize_text_field( $field_value['type'] ) : 'radio';

			if ( $field_type === $this->type ) {
				if (
					'entry-table' !== $context
					&& ! empty( $field_value['label'] )
				) {
					return sprintf(
						'<span style="max-width:200px;display:block;margin:0 0 5px 0;"><img src="%s" style="max-width:100%%;display:block;margin:0;"></span>%s',
						esc_html( $field_value['label'] )
					);
				} elseif ( isset( $field_value['label'] ) ) {
					return esc_html( $field_value['label'] );
				}
			}
		}

		return $value;
	}

	/**
	 * Define additional field properties.
	 */
	public function field_properties( $properties, $field, $form_data ) {
		// Define data.
		$form_id  = absint( $form_data['id'] );
		$field_id = $field['id'];
		$choices  = $field['choices'];

		// Remove primary input.
		unset( $properties['inputs']['primary'] );

		// Set input container (ul) properties.
		$properties['input_container'] = array(
			'class' => array( 'choices-ul' ),
			'data'  => array(),
			'attr'  => array(),
			'id'    => "pf-{$form_id}-field_{$field_id}",
		);

		// Set input properties.
		foreach ( $choices as $key => $choice ) {
			$depth                        = isset( $choice['depth'] ) ? absint( $choice['depth'] ) : 1;
			$properties['inputs'][ $key ] = array(
				'container' => array(
					'attr'  => array(),
					'class' => array( "choice-{$key}", "depth-{$depth}" ),
					'data'  => array(),
					'id'    => '',
				),
				'label'     => array(
					'attr'  => array(
						'for' => "pf-{$form_id}-field_{$field_id}_{$key}",
					),
					'class' => array( 'pie-forms-field-label-inline' ),
					'data'  => array(),
					'id'    => '',
					'text'  => Pie_Forms()->core()->pie_string_translation( $form_id, $field_id, $choice['label'], '-choice-' . $key ),
				),
				'attr'      => array(
					'name'  => "pie_forms[form_fields][{$field_id}]",
					'value' => isset( $field['show_values'] ) ? $choice['value'] : $choice['label'],
				),
				'class'     => array( 'input-text' ),
				'data'      => array(),
				'id'        => "pf-{$form_id}-field_{$field_id}_{$key}",
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);
		}

		// Required class for validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'pf-field-required';
		}

		// Add selected class for choices with defaults.
		foreach ( $properties['inputs'] as $key => $inputs ) {
			if ( ! empty( $inputs['default'] ) ) {
				$properties['inputs'][ $key ]['container']['class'][] = 'pie-forms-selected';
			}
		}

		return $properties;
	}

	/**
	 * Randomize order of choices.
	 */
	public function randomize( $field ) {
		$args = array(
			'slug'    => 'random',
			'content' => $this->field_element(
				'checkbox',
				$field,
				array(
					'slug'    => 'random',
					'value'   => isset( $field['random'] ) ? '1' : '0',
					'desc'    => esc_html__( 'Randomize Choices', 'pie-forms' ),
					'tooltip' => esc_html__( 'Check this option to randomize the order of the choices.', 'pie-forms' ),
				),
				false
			),
		);
		$this->field_element( 'row', $field, $args );
	}

	/**
	 * Show values field option.
	 */
	public function show_values( $field ) {
		// Show Values toggle option. This option will only show if already used or if manually enabled by a filter.
		if ( ! empty( $field['show_values'] ) || apply_filters( 'pie_forms_fields_show_options_setting', false ) ) {
			$args = array(
				'slug'    => 'show_values',
				'content' => $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'show_values',
						'value'   => isset( $field['show_values'] ) ? $field['show_values'] : '0',
						'desc'    => __( 'Show Values', 'pie-forms' ),
						'tooltip' => __( 'Check this to manually set form field values.', 'pie-forms' ),
					),
					false
				),
			);
			$this->field_element( 'row', $field, $args );
		}
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {
		// Label.
		$this->field_preview_option( 'label', $field );

		// Choices.
		$this->field_preview_option( 'choices', $field );

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Define data.
		$container = $field['properties']['input_container'];
		$choices   = $field['properties']['inputs'];

		// List.
		printf( '<ul %s>', Pie_Forms()->core()->pie_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] ) );

		foreach ( $choices as $choice ) {
			if ( empty( $choice['container'] ) ) {
				continue;
			}

			// Conditional logic.
			if ( isset( $choices['primary'] ) ) {
				$choice['attr']['conditional_id'] = $choices['primary']['attr']['conditional_id'];

				if ( isset( $choices['primary']['attr']['conditional_rules'] ) ) {
					$choice['attr']['conditional_rules'] = $choices['primary']['attr']['conditional_rules'];
				}
			}

			printf( '<li %s>', Pie_Forms()->core()->pie_html_attributes( $choice['container']['id'], $choice['container']['class'], $choice['container']['data'], $choice['container']['attr'] ) );

			
				// Normal display.
				printf( '<input type="radio" %s %s %s>', Pie_Forms()->core()->pie_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ), esc_attr( $choice['required'] ), checked( '1', $choice['default'], false ) );
				printf( '<label %s>%s</label>', Pie_Forms()->core()->pie_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] ), wp_kses_post( $choice['label']['text'] ) );
		

			echo '</li>';
		}

		echo '</ul>';
	}


}
