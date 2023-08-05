<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Checkbox extends PFORM_Abstracts_Fields {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name     = esc_html__( 'Checkboxes', 'pie-forms' );
		$this->type     = 'checkbox';
		$this->icon     = 'checkbox';
		$this->order    = 70;
		$this->group    = 'basic';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'First Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Second Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Third Choice', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
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
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );
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
			'class' => array( 'choices-ul'),
			'data'  => array(),
			'attr'  => array(),
			'id'    => "pf-{$form_id}-field_{$field_id}",
		);

		// Set choice limit.
		$field['choice_limit'] = empty( $field['choice_limit'] ) ? 0 : (int) $field['choice_limit'];
		if ( $field['choice_limit'] > 0 ) {
			$properties['input_container']['data']['choice-limit'] = $field['choice_limit'];
		}

		// Set input properties.
		foreach ( $choices as $key => $choice ) {
			$depth = isset( $choice['depth'] ) ? absint( $choice['depth'] ) : 1;

			// Choice labels should not be left blank, but if they are we provide a basic value.
			$value = isset( $field['show_values'] ) ? $choice['value'] : $choice['label'];
			if ( '' === $value ) {
				if ( 1 === count( $choices ) ) {
					$value = esc_html__( 'Checked', 'pie-forms' );
				} else {
					/* translators: %s - Choice Number. */
					$value = sprintf( esc_html__( 'Choice %s', 'pie-forms' ), $key );
				}
			}

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
					'text'  => Pie_forms()->core()->pie_string_translation( $form_id, $field_id, $choice['label'], '-choice-' . $key ),
				),
				'attr'      => array(
					'name'  => "pie_forms[form_fields][{$field_id}][]",
					'value' => $value,
				),
				'class'     => array( 'input-text' ),
				'data'      => array(),
				'id'        => "pf-{$form_id}-field_{$field_id}_{$key}",
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);

			// Rule for choice limit validator.
			if ( $field['choice_limit'] > 0 ) {
				$properties['inputs'][ $key ]['data']['rule-check-limit'] = 'true';
			}
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

			printf( '<li %s>', Pie_Forms()->core()->pie_html_attributes( $choice['container']['id'], $choice['container']['class'], $choice['container']['data'], $choice['container']['attr'] ) );

			// Checkbox display.
			printf( '<input type="checkbox" %s %s %s>', Pie_Forms()->core()->pie_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ), esc_attr( $choice['required'] ), checked( '1', $choice['default'], false ) );
			printf( '<label %s>%s</label>', Pie_Forms()->core()->pie_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] ), wp_kses_post( $choice['label']['text'] ) );

			echo '</li>';
		}

		echo '</ul>';
	}

	/**
	 * Formats and sanitizes field.
	 */
	public function format( $field_id, $field_submit, $form_data, $meta_key ) {
		$field_submit = (array) $field_submit;
		$field        = $form_data['form_fields'][ $field_id ];
		$name         = make_clickable( $field['label'] );
		$value_raw    = Pie_forms()->core()->pform_sanitize_array_combine( $field_submit );
		$choice_keys  = array();

		$data = array(
			'id'        => $field_id,
			'type'      => $this->type,
			'value'     => array(
				'name' => $name,
				'type' => $this->type,
			),
			'meta_key'  => $meta_key,
			'value_raw' => $value_raw,
		);

	
		$data['value']['label'] = $value_raw;

		// Determine choices keys, this is needed for image choices.
		foreach ( $field_submit as $item ) {
			foreach ( $field['choices'] as $key => $choice ) {
				if ( $item === $choice['label'] ) {
					$choice_keys[] = $key;
					break;
				}
			}
		}

		// Push field details to be saved.
		Pie_Forms()->task->form_fields[ $field_id ] = $data;
	}

}
