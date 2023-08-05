<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class PFORM_Fields_Multiselect
 */

class PFORM_Fields_Multiselect extends PFORM_Abstracts_Fields{
	
	public function __construct() {
		$this->name     = esc_html__( 'Multiselect Dropdown', 'pie-forms' );
		$this->type     = 'multiselect';
		$this->icon     = 'multiselect';
		$this->order    = 110;
		$this->group    = 'advanced';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'Option 1', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Option 2', 'pie-forms' ),
				'value'   => '',
				'cal'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Option 3', 'pie-forms' ),
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
					'enhanced_select',
					'description',
					'required',
					'required_field_message',
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'size',
					'label_hide',
					'css',
				),
			),
		);

		parent::__construct();
	}

	/**
	 * Hook in tabs.
	 */
	public function init_hooks() {
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );

		// Customize HTML field values.
		add_filter( 'pie_forms_html_field_value', array( $this, 'html_field_value' ), 10, 4 );
	}

	/**
	 * Return images, if any, for HTML supported values.
	 *
	 * @param string $value     Field value.
	 * @param array  $field     Field settings.
	 * @param array  $form_data Form data and settings.
	 * @param string $context   Value display context.
	 *
	 * @return string
	 */
	public function html_field_value( $value, $field, $form_data = array(), $context = '' ) {

		if (isset($field['type']) && $field['type'] == $this->type ) {	
			$value = $field['value']['label'];
			$val = '';
			foreach($value as $key => $values){
				$val .= $values.'<br>';
			}
			return $val;
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
			'class' => array('pie-forms-multiselect'),
			'data'  => array(),
			'attr'  => array(),
			'id'    => "pf-{$form_id}-field_{$field_id}",
			'name'  => "pie_forms[form_fields][{$field_id}][]",
			'required'  => ! empty( $field['required'] ) ? 'required' : '',
		);


		// Set input properties.
		foreach ( $choices as $key => $choice ) {
			$depth = isset( $choice['depth'] ) ? absint( $choice['depth'] ) : 1;

			// Choice labels should not be left blank, but if they are we provide a basic value.
			$value = isset( $field['show_values'] ) ? $choice['value'] : $choice['label'];
		
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
				'class'     => array( ),
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
		
		// enqueue style and script
		wp_enqueue_style('MultiselectCSS', Pie_Forms::$url . 'assets/css/lib/select2.min.css', array(), Pie_Forms::VERSION );	
		wp_register_script( 'MutliselectJS', Pie_Forms::$url . 'assets/js/lib/select2.min.js', array(), Pie_Forms::VERSION );
		wp_enqueue_script( 'MutliselectJS');

		$has_default       = false;

		// Check to see if any of the options have selected by default.
		foreach ( $choices as $choice ) {

			if ( isset( $choice['default'] ) ) {
				$has_default = true;
				break;
			}
		}
		
		// List.
		printf( '<select %s multiple="multiple" name='.$container['name'].' type="multiselect" '.$container['required'].'>', Pie_Forms()->core()->pie_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] ) );

		
		foreach ( $choices as $choice ) {
			if ( empty( $choice['container'] ) ) {
				continue;
			}
		
			// Option display.
			printf( '<option %s %s>%s</option>', Pie_Forms()->core()->pie_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ), selected( '1', $choice['default'], false ), $choice['label']['text'] );
			
		}

		echo '</select>';
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


		// Push field details to be saved.
		Pie_Forms()->task->form_fields[ $field_id ] = $data;
	}
}
