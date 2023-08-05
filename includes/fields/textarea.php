<?php
defined( 'ABSPATH' ) || exit;

class PFORM_Fields_Textarea extends PFORM_Abstracts_Fields 
{

	/**
	 * Constructor.
	 */
	public function __construct() 
	{
		$this->name     = esc_html__( 'Text Area', 'pie-forms' );
		$this->type     = 'textarea';
		$this->icon     = 'textarea';
		$this->order    = 40;
		$this->group    = 'basic';
		$this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'label',
					'meta',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'size',
					'placeholder',
					'label_hide',
					'limit_length',
					//'default_value',
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
	 * Limit length field option.
	 *
	 * @param array $field Field settings.
	 */
	public function limit_length( $field ) {
		// Limit length.
		$args = array(
			'slug'    => 'limit_enabled',
			'content' => $this->field_element(
				'checkbox',
				$field,
				array(
					'slug'    => 'limit_enabled',
					'value'   => isset( $field['limit_enabled'] ),
					'desc'    => esc_html__( 'Limit Length', 'pie-forms' ),
					'tooltip' => esc_html__( 'Check this option to limit text length by characters or words count.', 'pie-forms' ),
				),
				false
			),
		);
		$this->field_element( 'row', $field, $args );

		// Limit controls.
		$count = $this->field_element(
			'text',
			$field,
			array(
				'type'  => 'number',
				'class' => 'small-text',
				'slug'  => 'limit_count',
				'attrs' => array(
					'min'     => 1,
					'step'    => 1,
					'pattern' => '[0-9]',
				),
				'value' => ! empty( $field['limit_count'] ) ? absint( $field['limit_count'] ) : 1,
			),
			false
		);

		$mode = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'limit_mode',
				'class'   => 'limit-select',
				'value'   => ! empty( $field['limit_mode'] ) ? esc_attr( $field['limit_mode'] ) : 'characters',
				'options' => array(
					'characters' => esc_html__( 'Characters', 'pie-forms' ),
					'words'      => esc_html__( 'Words Count', 'pie-forms' ),
				),
			),
			false
		);
		$args = array(
			'slug'    => 'limit_controls',
			'class'   => ! isset( $field['limit_enabled'] ) ? 'pie-forms-hidden' : '',
			'content' => $count . $mode,
		);
		$this->field_element( 'row', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<textarea placeholder="' . esc_attr( $placeholder ) . '" class="widefat" disabled></textarea>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {
		// Define data.
		$value   = '';
		$primary = $field['properties']['inputs']['primary'];

		if ( ! empty( $primary['attr']['value'] ) ) {
			$value = $primary['attr']['value'];
			unset( $primary['attr']['value'] );

		}

		// Limit length.
		if ( isset( $field['limit_enabled'] ) ) {
			$limit_count = isset( $field['limit_count'] ) ? absint( $field['limit_count'] ) : 0;
			$limit_mode  = isset( $field['limit_mode'] ) ? sanitize_key( $field['limit_mode'] ) : 'characters';

			$primary['data']['form-id']  = $form_data['id'];
			$primary['data']['field-id'] = $field['id'];

			if ( 'characters' === $limit_mode ) {
				$primary['class'][]            = 'pie-forms-limit-characters-enabled';
				$primary['attr']['maxlength']  = $limit_count;
				$primary['data']['text-limit'] = $limit_count;
			} else {
				$primary['class'][]            = 'pie-forms-limit-words-enabled';
				$primary['data']['text-limit'] = $limit_count;
			}
		}

		// Primary field.
		printf(
			'<textarea %s %s>%s</textarea>',
			Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			esc_attr( $primary['required'] ),
			$value 
		); 
	}
}