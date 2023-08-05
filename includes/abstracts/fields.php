<?php
defined( 'ABSPATH' ) || exit;

/**
 * Form fields class.
 */
abstract class PFORM_Abstracts_Fields {

	/**
	 * Field name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Field icon.
	 *
	 * @var mixed
	 */
	public $icon = '';

	/**
	 * Field class.
	 *
	 * @var string
	 */
	public $class = '';

	/**
	 * Form ID.
	 *
	 * @var int|mixed
	 */
	public $form_id;

	/**
	 * Field group.
	 *
	 * @var string
	 */
	public $group = 'general';

	/**
	 * Is available in Pro?
	 *
	 * @var boolean
	 */
	public $is_pro = false;

	/**
	 * Is available in Pro?
	 *
	 * @var boolean
	 */
	public $is_addon = false;

	/**
	 * Placeholder to hold default value(s) for some field types.
	 *
	 * @var mixed
	 */
	public $defaults;

	/**
	 * Array of form data.
	 *
	 * @var array
	 */
	public $form_data;

	/**
	 * Array of field settings.
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->class   = $this->is_pro ? 'upgrade-modal' : ($this->is_addon ? 'upgrade-to-addon' : '');
		$this->form_id = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : false; 

		// Init hooks.
		$this->init_hooks();

		// Hooks.
		add_action( 'pie_forms_builder_fields_options_' . $this->type, array( $this, 'field_options' ) );
		add_action( 'pie_forms_builder_fields_preview_' . $this->type, array( $this, 'field_preview' ) , 10 , 2 );
		add_action( 'wp_ajax_pie_forms_new_field_' . $this->type, array( $this, 'field_new' ) );
		add_action( 'pie_forms_display_field_' . $this->type, array( $this, 'field_display' ), 10, 3 );
		add_action( 'pie_forms_process_validate_' . $this->type, array( $this, 'validate' ), 10, 3 );
		add_action( 'pie_forms_process_format_' . $this->type, array( $this, 'format' ), 10, 4 );
		add_filter( 'pie_forms_field_properties', array( $this, 'field_prefill_value_property' ), 10, 3 );
		add_filter( 'pie_forms_field_exporter_' . $this->type, array( $this, 'field_exporter' ) );
	}

	/**
	 * Hook in tabs.
	 */
	public function init_hooks() {}

	/**
	 * Prefill field value with either fallback or dynamic data.
	 */
	public function field_prefill_value_property( $properties, $field, $form_data ) {
		// Process only for current field.
		if ( $this->type !== $field['type'] ) {
			return $properties;
		}

		// Set the form data, so we can reuse it later, even on front-end.
		$this->form_data = $form_data;

		return $properties;
	}

	/**
	 * Get the form fields after they are initialized.
	 *
	 * @return array of options
	 */
	public function get_field_settings() {
		return apply_filters( 'pie_forms_get_field_settings_' . $this->type, $this->settings );
	}

	/**
	 * Output form fields options.
	 */
	public function field_options( $field ) {
		$settings = $this->get_field_settings();

		foreach ( $settings as $option_key => $option ) {
			$this->field_option(
				$option_key,
				$field,
				array(
					'markup' => 'open',
				)
			);

			if ( ! empty( $option['field_options'] ) ) {
				foreach ( $option['field_options'] as $option_name ) {
					$this->field_option( $option_name, $field );
				}
			}

			$this->field_option(
				$option_key,
				$field,
				array(
					'markup' => 'close',
				)
			);
		}
	}

	/**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {}

	/**
	 * Helper function to create field option elements.
	 */
	public function field_element( $option, $field, $args = array(), $echo = true ) {
		$id     = (string) $field['id'];
		$class  = ! empty( $args['class'] ) && is_string( $args['class'] ) ? esc_attr( $args['class'] ) : '';
		$readonly  = ! empty( $args['readonly'] ) && is_string( $args['readonly'] ) ? esc_attr( $args['readonly'] ) : '';
		$disabled  = ! empty( $args['disabled'] ) && is_string( $args['disabled'] ) ? esc_attr( $args['disabled'] ) : '';
		
		$slug   = ! empty( $args['slug'] ) ? sanitize_title( $args['slug'] ) : '';
		$data   = '';
		$output = '';

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $val ) {
				if ( is_array( $val ) ) {
					$val = wp_json_encode( $val );
				}
				$data .= ' data-' . $key . '=\'' . $val . '\'';
			}
		}

		// BW compat for number attrs.
		if ( ! empty( $args['min'] ) ) {
			$args['attrs']['min'] = esc_attr( $args['min'] );
			unset( $args['min'] );
		}
		if ( ! empty( $args['max'] ) ) {
			$args['attrs']['max'] = esc_attr( $args['max'] );
			unset( $args['min'] );
		}
		if ( ! empty( $args['required'] ) && $args['required'] ) {
			$args['attrs']['required'] = 'required';
			unset( $args['required'] );
		}

		if ( ! empty( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $arg_key => $val ) {
				if ( is_array( $val ) ) {
					$val = wp_json_encode( $val );
				}
				$data .= $arg_key . '=\'' . $val . '\'';
			}
		}

		switch ( $option ) {

			// Row.
			case 'row':
				$output = sprintf( '<div class="pie-form-field-wrapper pie-form-field-wrapper-%s %s" id="pie-form-field-wrapper-%s-%s" data-field-id="%s" %s>%s</div>', $slug, $class, $id, $slug, $id, $data, $args['content'] );
				break;

			// Icon.
			case 'icon':
				$element_tooltip = isset( $args['tooltip'] ) ? $args['tooltip'] : 'Edit Label';
				$icon            = isset( $args['icon'] ) ? $args['icon'] : 'dashicons-edit';
				$output         .= sprintf( ' <i class="dashicons %s pie-forms-icon %s" title="%s" %s></i>', esc_attr( $icon ), $class, esc_attr( $element_tooltip ), $data );
				break;

			// Notice.
			case 'notice':
				$notice          = isset( $args['notice'] ) ? $args['notice'] : '';
				$starting_key    = isset( $args['starting_key'] ) ? $args['starting_key'] : '';
				$output         .= sprintf( '<p class="pie-builder-notice"><strong>%s </strong>%s</p>', esc_attr( $starting_key ),  $notice  );
				break;

			// Label.
			case 'label':
				$output = sprintf( '<label for="pie-forms-field-option-%s-%s" class="%s" %s>%s', $id, $slug, $class, $data, esc_html( $args['value'] ) );
				if ( isset( $args['tooltip'] ) && ! empty( $args['tooltip'] ) ) {
					$output .= ' ' . sprintf( '<i class="dashicons dashicons-editor-help pie-forms-help-tooltip"><span class="tooltip-hover">%s</span></i>', esc_attr( $args['tooltip'] ) );
				}
				if ( isset( $args['after_tooltip'] ) && ! empty( $args['after_tooltip'] ) ) {
					$output .= $args['after_tooltip'];
				}
				$output .= '</label>';
				break;

			// Text input.
			case 'text':
				$type        = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';
				$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
				$readonly = ! empty( $args['readonly'] ) ? esc_attr( $args['readonly'] ) : '';
				$before      = ! empty( $args['before'] ) ? '<span class="before-input">' . esc_html( $args['before'] ) . '</span>' : '';
				if ( ! empty( $before ) ) {
					$class .= ' has-before';
				}

				$output = sprintf( '%s<input type="%s" class="widefat %s" %s id="pie-forms-field-option-%s-%s" name="form_fields[%s][%s]" value="%s" placeholder="%s" %s>', $before, $type, $class, $readonly , $id, $slug, $id, $slug, esc_attr( $args['value'] ), $placeholder, $data );
				break;
			// Text input.
			case 'number':
				$type        = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'number';
				$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
				$readonly = ! empty( $args['readonly'] ) ? esc_attr( $args['readonly'] ) : '';
				$before      = ! empty( $args['before'] ) ? '<span class="before-input">' . esc_html( $args['before'] ) . '</span>' : '';
				if ( ! empty( $before ) ) {
					$class .= ' has-before';
				}

				$output = sprintf( '%s<input type="%s" class="widefat %s" %s id="pie-forms-field-option-%s-%s" min="0" name="form_fields[%s][%s]" value="%s" placeholder="%s" %s>', $before, $type, $class, $readonly , $id, $slug, $id, $slug, esc_attr( $args['value'] ), $placeholder, $data );
				break;

			// Textarea.
			case 'textarea':
				$rows   = ! empty( $args['rows'] ) ? (int) $args['rows'] : '3';
				$output = sprintf( '<textarea class="widefat %s" id="pie-forms-field-option-%s-%s" name="form_fields[%s][%s]" rows="%s" %s>%s</textarea>', $class, $id, $slug, $id, $slug, $rows, $data, $args['value'] );
				break;

			// Checkbox.
			case 'checkbox':
				
				$checked = checked( '1', $args['value'], false );
				$disabled = ! empty( $args['disabled'] ) ? esc_attr( $args['disabled'] ) : '';
				$output  = sprintf( '<input type="checkbox" class="widefat %s" id="pie-forms-field-option-%s-%s" name="form_fields[%s][%s]" value="1" %s %s %s>', $class, $id, $slug, $id, $slug, $checked, $data , $disabled);
				$output .= sprintf( '<label for="pie-forms-field-option-%s-%s" class="inline">%s', $id, $slug, $args['desc'] );
				if ( isset( $args['tooltip'] ) && ! empty( $args['tooltip'] ) ) {
					$output .= ' ' . sprintf( '<i class="dashicons dashicons-editor-help pie-forms-help-tooltip"><span class="tooltip-hover">%s</span></i>', esc_attr( $args['tooltip'] ) );
				}
				$output .= '</label>';
				break;

			// Toggle.
			case 'toggle':
				$checked = checked( '1', $args['value'], false );
				$icon    = $args['value'] ? 'fa-toggle-on' : 'fa-toggle-off';
				$cls     = $args['value'] ? 'pie-forms-on' : 'pie-forms-off';
				$status  = $args['value'] ? __( 'On', 'pie-forms' ) : __( 'Off', 'pie-forms' );
				$output  = sprintf( '<span class="pie-forms-toggle-icon %s"><i class="fa %s" aria-hidden="true"></i> <span class="pie-forms-toggle-icon-label">%s</span>', $cls, $icon, $status );
				$output .= sprintf( '<input type="checkbox" class="widefat %s" id="pie-forms-field-option-%s-%s" name="form_fields[%s][%s]" value="1" %s %s></span>', $class, $id, $slug, $id, $slug, $checked, $data );
				break;

			// Select.
			case 'select':
				$options = $args['options'];
				$value   = isset( $args['value'] ) ? $args['value'] : '';
				$output  = sprintf( '<select class="widefat %s" id="pie-forms-field-option-%s-%s" name="form_fields[%s][%s]" %s>', $class, $id, $slug, $id, $slug, $data );
				foreach ( $options as $key => $option ) {
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), $option );
				}
				$output .= '</select>';
				break;

			// Radio.
			case 'radio':
				$options = $args['options'];
				$default = isset( $args['default'] ) ? $args['default'] : '';
				$output  = '<label>' . $args['desc'];

				if ( isset( $args['tooltip'] ) && ! empty( $args['tooltip'] ) ) {
					$output .= ' ' . sprintf( '<i class="dashicons dashicons-editor-help pie-forms-help-tooltip" title="%s"></i></label>', esc_attr( $args['tooltip'] ) );
				} else {
					$output .= '</label>';
				}
				$output .= '<ul>';

				foreach ( $options as $key => $option ) {
					$output .= '<li>';
					$output .= sprintf( '<label><input type="radio" class="widefat %s" id="pie-forms-field-option-%s-%s-%s" value="%s" name="form_fields[%s][%s]" %s %s>%s</label>', $class, $id, $slug, $key, $key, $id, $slug, $data, checked( $key, $default, false ), $option );
					$output .= '</li>';
				}
				$output .= '</ul>';
				break;
		}

		if ( $echo ) {
			echo wp_kses($output, Pie_Forms()->core()->pform_get_allowed_tags()); 
		} else {
			return $output;
		}
	}

	/**
	 * Helper function to create common field options that are used frequently.
	 */
	public function field_option( $option, $field, $args = array(), $echo = true ) {
		$output = '';

		switch ( $option ) {
			/**
			 * Basic Fields.
			 */

			/*
			 * Basic Options markup.
			 */
			case 'basic-options':
				$markup = ! empty( $args['markup'] ) ? $args['markup'] : 'open';
				$class  = ! empty( $args['class'] ) ? esc_html( $args['class'] ) : '';
				if ( 'open' === $markup ) {
					
					$output  = sprintf( '<div class="pie-forms-field-option-group pie-forms-field-option-group-basic open" id="pie-forms-field-option-basic-%s">', $field['id'] );
					
					$output .= sprintf( '<a href="#" class="pie-forms-field-option-group-toggle">%s <i class="handlediv"></i></a>', __( 'Basic Options', 'pie-forms' ) );

					$output .= sprintf( '<div class="pie-forms-field-option-group-inner %s">', $class );
					
				} else {
					$output = '</div></div>';
				}
				break;

			/*
			 * Field Label.
			 */
			case 'label':
				$value   = ! empty( $field['label'] ) ? esc_attr( $field['label'] ) : '';
				$tooltip = esc_html__( 'Enter text for the form field label. Field labels are recommended and can be hidden in the Advanced Settings.', 'pie-forms' );
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'label',
						'value'   => esc_html__( 'Label', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'label',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'label',
						'content' => $output,
					),
					false
				);
				break;

			/*
			 * Field Meta.
			 */
			case 'meta':
				$value   = ! empty( $field['meta-key'] ) ? esc_attr( $field['meta-key'] ) : Pie_Forms()->core()->pform_get_meta_key_field_option( $field );
				

				$tooltip = esc_html__( 'Keys to be stored in database', 'pie-forms' );
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'meta-key',
						'value'   => esc_html__( 'Meta Key', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'meta-key',
						'class' => 'pf-input-meta-key',
						'value' => $value,
						'readonly' => 'readonly',
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'meta-key',
						'content' => $output,
					),
					false
				);
				break;

			/*
			 * Field Description.
			 */
			case 'description':
				$value   = ! empty( $field['description'] ) ? esc_attr( $field['description'] ) : '';
				$tooltip = esc_html__( 'Enter text for the form field description.', 'pie-forms' );
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'description',
						'value'   => esc_html__( 'Description', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'textarea',
					$field,
					array(
						'slug'  => 'description',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'description',
						'content' => $output,
					),
					false
				);
				break;

			/*
			 * Field Required toggle.
			 */
			case 'required':
				$default = ! empty( $args['default'] ) ? $args['default'] : '0';
				$value   = isset( $field['required'] ) ? $field['required'] : $default;
				$tooltip = esc_html__( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.', 'pie-forms' );
				$output  = $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'required',
						'value'   => $value,
						'desc'    => esc_html__( 'Required', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'required',
						'content' => $output,
					),
					false
				);
				break;

			/*
			 * Required Field Message.
			 */
			case 'required_field_message':
			
				$required_validation = get_option( 'pf_required_validation' );
				if ( in_array( $field['type'], array( 'number', 'email', 'url', 'phone' ), true ) ) {
					$required_validation = get_option( 'pf_' . $field['type'] . '_validation' );
				}


				$value   = isset( $field['required-field-message'] ) ? esc_attr( $field['required-field-message'] ) : esc_attr( $required_validation );
				$tooltip = esc_html__( 'Enter a message to show for this field if it\'s required.', 'pie-forms' );
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'required-field-message',
						'value'   => esc_html__( 'Required Field Message', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'required-field-message',
						'value' => esc_html($value),
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'required-field-message',
						'class'   => isset( $field['required'] ) || $field['type'] === 'gdpr' ? '' : 'hidden',
						'content' => $output,
					),
					false
				);
			
			break;
			
			/*
			 * Choices.
			 */
			case 'choices':
				$class      = array();
				$label      = ! empty( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Choices', 'pie-forms' );
				$choices    = ! empty( $field['choices'] ) ? $field['choices'] : $this->defaults;
				$input_type = in_array( $field['type'], array( 'radio', 'payment-multiple' ), true ) ? 'radio' : 'checkbox';

				if ( ! empty( $field['show_values'] ) ) {
					$class[] = 'show-values';
				}

				// Field label.
				$field_label = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'choices',
						'value'         => $label,
						'tooltip'       => esc_html__( 'Add choices for the form field.', 'pie-forms' ),
						'after_tooltip' => '',
					)
				);

				// Field contents.
				$field_content = sprintf(
					'<ul data-next-id="%s" class="pf-choices-list %s" data-field-id="%s" data-field-type="%s">',
					max( array_keys( $choices ) ) + 1,
					Pie_Forms()->core()->pform_sanitize_classes( $class, true ),
					$field['id'],
					$this->type
				);
				foreach ( $choices as $key => $choice ) {
					$default = ! empty( $choice['default'] ) ? $choice['default'] : '';
					$name    = sprintf( 'form_fields[%s][choices][%s]', $field['id'], $key );

					if(! isset($choice['cal'])){
						$choice['cal'] = '';
					}

					$field_content .= sprintf( '<li data-key="%1$d">', absint( $key ) );
					$field_content .= '<span class="sort"><i class="dashicons dashicons-move"></i></span>';
					$field_content .= sprintf( '<input type="%1$s" name="%2$s[default]" class="default" value="1" %3$s>', $input_type, $name, checked( '1', $default, false ) );
					$field_content .= '<div class="pf-choice-list-input">';
					$field_content .= sprintf( '<input type="text" name="%1$s[label]" value="%2$s" class="label" data-key="%3$s">', $name, esc_attr( $choice['label'] ), absint( $key ) );
					$field_content .= sprintf( '<input type="number" name="%1$s[value]" value="%2$s" class="value">', $name, esc_attr( $choice['value'] ) );
					
					if( is_plugin_active('pie-forms-for-wp-quiz/pie-forms-for-wp-quiz.php') ){
						$field_content .= sprintf( '<input type="number" name="%1$s[cal]" value="%2$s" class="cal" data-key="%3$s" placeholder="Input this Answer marks">', $name,esc_attr($choice['cal']), absint( $key ) );
					}

					$field_content .= '</div>';
					$field_content .= '<a class="add" href="#"><i class="dashicons dashicons-plus-alt"></i></a>';
					$field_content .= '<a class="remove" href="#"><i class="dashicons dashicons-dismiss"></i></a>';
					$field_content .= '</li>';
				}
				$field_content .= '</ul>';

				// Final field output.
				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'choices',
						'content' => $field_label . $field_content,
					),
					false
				);
				break;
			/*
			 * gdpr agreement checkbox.
			 */
			case 'gdpr_agreement_checkbox':
				
				$value   = 'I consent to having this website store my submitted information so they can respond to my inquiry.';
				$tooltip = esc_html__( 'Customize the message to display for the GDPR field on the form.', 'pie-forms' );
				$label      = ! empty( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Agreement', 'pie-forms' );

				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'gdpr-checkbox',
						'value'   => $label ,
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'gdpr-checkbox',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'gdpr-checkbox',
						'content' => $output,
					),
					false
				);
				break;
			/*
			 * Choices.
			 */
			case 'radio':
				$class      = array();
				$label      = ! empty( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Choices', 'pie-forms' );
				$choices    = ! empty( $field['choices'] ) ? $field['choices'] : $this->defaults;
				$input_type = in_array( $field['type'], array( 'radio' ), true ) ? 'radio' : 'radio';

				if ( ! empty( $field['show_values'] ) ) {
					$class[] = 'show-values';
				}
				if ( ! empty( $field['choices_images'] ) ) {
					$class[] = 'show-images';
				}

				// Field label.
				$field_label = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'choices',
						'value'         => $label,
						'tooltip'       => esc_html__( 'Add choices for the form field.', 'pie-forms' ),
						'after_tooltip' => '',
					)
				);

				// Field contents.
				$field_content = sprintf(
					'<ul data-next-id="%s" class="pf-choices-list %s" data-field-id="%s" data-field-type="%s">',
					max( array_keys( $choices ) ) + 1,
					Pie_Forms()->core()->pform_sanitize_classes( $class, true ),
					$field['id'],
					$this->type
				);
				foreach ( $choices as $key => $choice ) {
					$default = ! empty( $choice['default'] ) ? $choice['default'] : '';
					$name    = sprintf( 'form_fields[%s][choices][%s]', $field['id'], $key );

					if(! isset($choice['cal'])){
						$choice['cal'] = '';
					}

					$field_content .= sprintf( '<li data-key="%1$d">', absint( $key ) );
					$field_content .= '<span class="sort"><i class="dashicons dashicons-move"></i></span>';
					$field_content .= sprintf( '<input type="%1$s" name="%2$s[default]" class="default" value="1" %3$s>', $input_type, $name, checked( '1', $default, false ) );
					$field_content .= '<div class="pf-choice-list-input">';
					$field_content .= sprintf( '<input type="text" name="%1$s[label]" value="%2$s" class="label" data-key="%3$s">', $name, esc_attr( $choice['label'] ), absint( $key ) );
					$field_content .= sprintf( '<input type="text" name="%1$s[value]" value="%2$s" class="value">', $name, esc_attr( $choice['value'] ) );
					
					if( is_plugin_active('pie-forms-for-wp-quiz/pie-forms-for-wp-quiz.php') ){
						$field_content .= sprintf( '<input type="number" name="%1$s[cal]" value="%2$s" class="cal" data-key="%3$s" placeholder="Input this Answer marks">', $name,esc_attr($choice['cal']), absint( $key ) );
					}
					
					$field_content .= '</div>';
					$field_content .= '<a class="add" href="#"><i class="dashicons dashicons-plus-alt"></i></a>';
					$field_content .= '<a class="remove" href="#"><i class="dashicons dashicons-dismiss"></i></a>';
					$field_content .= '</li>';
				}
				$field_content .= '</ul>';

				// Final field output.
				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'choices',
						'content' => $field_label . $field_content,
					),
					false
				);
				break;
			
			/*
			 * Choices Images.
			 */
			case 'choices_images':

				$url = "https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder";
				$field_content = $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'choices_images',
						'value'   => isset( $field['choices_images'] ) ? '1' : '0',
						'desc'    => esc_html__( 'Use image choices', 'pie-forms' ),
						'tooltip' => esc_html__( 'Check this option to enable using images with the choices.', 'pie-forms' ),
						'disabled' => 'disabled',
					),
					false
				);
				$field_content .= sprintf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					sprintf(__( 'This option is only available in the <a href=%s target="_blank">Premium Plan</a>', 'pie-forms' ), esc_html($url))
				);
				// Final field output.
				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'choices_images',
						'content' => $field_content,
					),
					false
				);
				break;
			/**
			 * Advanced Fields.
			 */

			/*
			 * Default value.
			 */
			case 'default_value':
				$value   = ! empty( $field['default_value'] ) ? esc_attr( $field['default_value'] ) : '';
				$tooltip = esc_html__( 'Enter text for the default form field value.', 'pie-forms' );
				$toggle  = '';
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'default_value',
						'value'         => esc_html__( 'Default Value', 'pie-forms' ),
						'tooltip'       => $tooltip,
						'after_tooltip' => $toggle,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'default_value',
						'value' => $value,
					),
					false
				);

				// Smart tag for default value.
				$exclude_fields = array( 'rating', 'number', 'range-slider' );

				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'default_value',
						'content' => $output,
						'class'   => in_array( $field['type'], $exclude_fields, true ) ? '' : 'pf_smart_tag',
					),
					false
				);
				break;

			/*
			 * Advanced Options markup.
			 */
			case 'advanced-options':
				$markup = ! empty( $args['markup'] ) ? $args['markup'] : 'open';
				if ( 'open' === $markup ) {
					$override = apply_filters( 'pie_forms_advanced_options_override', false );
					$override = ! empty( $override ) ? 'style="display:' . $override . ';"' : '';
					$output   = sprintf( '<div class="pie-forms-field-option-group pie-forms-field-option-group-advanced pie-forms-hide closed" id="pie-forms-field-option-advanced-%s" %s>', $field['id'], $override );
					$output  .= sprintf( '<a href="#" class="pie-forms-field-option-group-toggle">%s<i class="handlediv"></i></a>', __( 'Advanced Options', 'pie-forms' ) );
					$output  .= '<div class="pie-forms-field-option-group-inner">';
				} else {
					$output = '</div></div>';
				}
				break;

			/*
			 * Placeholder.
			 */
			case 'placeholder':
				$value   = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
				$tooltip = esc_html__( 'Enter text for the form field placeholder.', 'pie-forms' );
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'placeholder',
						'value'   => esc_html__( 'Placeholder Text', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'placeholder',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'placeholder',
						'content' => $output,
					),
					false
				);
				break;

			/*
			 * CSS classes.
			 */
			case 'css':
				$toggle  = '';
				$tooltip = esc_html__( 'Enter CSS class names for this field container. Multiple class names should be separated with spaces.', 'pie-forms' );
				$value   = ! empty( $field['css'] ) ? esc_attr( $field['css'] ) : '';

				// Build output.
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'css',
						'value'         => esc_html__( 'CSS Classes', 'pie-forms' ),
						'tooltip'       => $tooltip,
						'after_tooltip' => $toggle,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'css',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'css',
						'content' => $output,
					),
					false
				);
				break;


			/*
			 * Advance Validation.
			 */
			case 'validation_rule':
				$value   = ! empty( $field['validation_rule'] ) ? esc_attr( $field['validation_rule'] ) : '';
				$tooltip = esc_html__( 'Select validation type.', 'pie-forms' );
				$options = array(
					'please_select'       			=> esc_html__( 'Please select type', 'pie-forms' ),
					'alpha_only'       	=> esc_html__( 'Alphabet only with space', 'pie-forms' ),
					'custom_regex'		=> esc_html__( 'Custom Regex', 'pie-forms' ),
				);

				// Build output.
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'validation_rule',
						'value'   => esc_html__( 'Advance Validation', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'select',
					$field,
					array(
						'slug'    => 'validation_rule',
						'value'   => $value,
						'options' => $options,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'validation_rule',
						'content' => $output,
					),
					false
				);
				break;
			/*
			 * Custom Regex Field.
			 */
			case 'custom_regex':
				$toggle  = '';
				$tooltip = esc_html__( 'Enter your desired custom regex.', 'pie-forms' );
				$value   = ! empty( $field['custom_regex'] ) ? esc_attr( $field['custom_regex'] ) : '';

				// Build output.
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'custom_regex',
						'value'         => esc_html__( 'Custom Regex', 'pie-forms' ),
						'tooltip'       => $tooltip,
						'after_tooltip' => $toggle,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'custom_regex',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'custom_regex',
						'content' => $output,
					),
					false
				);
				break;	
			/*
			 * Custom Regex Field.
			 */
			case 'custom_validation_message':
				$toggle  = '';
				$tooltip = esc_html__( 'Customize the validation message.', 'pie-forms' );
				$value   = ! empty( $field['custom_validation_message'] ) ? esc_attr( $field['custom_validation_message'] ) : '';

				// Build output.
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'          => 'custom_validation_message',
						'value'         => esc_html__( 'Custom Validation Message', 'pie-forms' ),
						'tooltip'       => $tooltip,
						'after_tooltip' => $toggle,
					),
					false
				);
				$output .= $this->field_element(
					'text',
					$field,
					array(
						'slug'  => 'custom_validation_message',
						'value' => $value,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'custom_validation_message',
						'content' => $output,
					),
					false
				);
				break;	
			/*
			 * Select Search.
			 */
			case 'is_search':
				$value   = isset( $field['is_search'] ) ? $field['is_search'] : '0';
				$tooltip = esc_html__( 'Adds search filter to the dropdown.', 'pie-forms' );

				// Build output.
				$output = $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'is_search',
						'value'   => $value,
						'desc'    => esc_html__( 'Add Search filter', 'pie-forms' ),
						'tooltip' => $tooltip,
						'class'   => 'checkbox-search',
					),
					false
				);
				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'is_search',
						'content' => $output,
					),
					false
				);
			break;
			/*
			 * Hide Label.
			 */
			case 'label_hide':
				$value   = isset( $field['label_hide'] ) ? $field['label_hide'] : '0';
				$tooltip = esc_html__( 'Check this option to hide the form field label.', 'pie-forms' );

				// Build output.
				$output = $this->field_element(
					'checkbox',
					$field,
					array(
						'slug'    => 'label_hide',
						'value'   => $value,
						'desc'    => esc_html__( 'Hide Label', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'label_hide',
						'content' => $output,
					),
					false
				);
				break;
			
			/*
			 * Input columns.
			 */
			case 'input_columns':
				$value   = ! empty( $field['input_columns'] ) ? esc_attr( $field['input_columns'] ) : '';
				$tooltip = esc_html__( 'Select the column layout for displaying field choices.', 'pie-forms' );
				$options = array(
					''       => esc_html__( 'One Column', 'pie-forms' ),
					'2'      => esc_html__( 'Two Columns', 'pie-forms' ),
					'3'      => esc_html__( 'Three Columns', 'pie-forms' ),
					'inline' => esc_html__( 'Inline', 'pie-forms' ),
				);

				// Build output.
				$output  = $this->field_element(
					'label',
					$field,
					array(
						'slug'    => 'input_columns',
						'value'   => esc_html__( 'Layout', 'pie-forms' ),
						'tooltip' => $tooltip,
					),
					false
				);
				$output .= $this->field_element(
					'select',
					$field,
					array(
						'slug'    => 'input_columns',
						'value'   => $value,
						'options' => $options,
					),
					false
				);
				$output  = $this->field_element(
					'row',
					$field,
					array(
						'slug'    => 'input_columns',
						'content' => $output,
					),
					false
				);
				break;
			/*
			 * Conditional: Logic.
			 */
			case 'conditional-logic':
				$markup = ! empty( $args['markup'] ) ? $args['markup'] : 'open';
				if ( 'open' === $markup ) {
					$override = apply_filters( 'pie_forms_conditional_logic_override', false );
					$override = ! empty( $override ) ? 'style="display:' . $override . ';"' : '';
					
					$output   = sprintf( '<div class="pie-forms-field-option-group pie-forms-field-option-group-conditional pie-forms-hide closed" id="pie-forms-field-option-conditional-%s" %s>', $field['id'], $override );
					
					$output  .= sprintf( '<a href="#" class="pie-forms-field-option-group-toggle">%s<i class="handlediv"></i></a>', __( 'Conditional Logic', 'pie-forms' ) );
					
					$output  .= '<div class="pie-forms-field-option-group-inner">';
				} else {
					$output = '</div></div>';
				}
				break;
				
			case 'upgrade-to-pro':
				$output = '<div class="upgrade-to-pro">';		
					$output .= '<p>This feature is only available in our Premium Plan. <a href=
					"https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=formbuilder" target="_blank"> Upgrade to Premium </a>and enjoy all the amazing features. </p>';		
				$output .= '</div>';		
				break;
			/*
			 * Default.
			 */
			default:
				if ( is_callable( array( $this, $option ) ) ) {
					$this->{$option}( $field );
				}
				do_action( 'pie_forms_field_options_' . $option, $this, $field, $args );
				break;

		}

		if ( $echo ) {
			if ( in_array( $option, array( 'basic-options', 'advanced-options' ), true ) ) {
				if ( 'open' === $markup ) {
					do_action( "pie_forms_field_options_before_{$option}", $field, $this );
				}

				if ( 'close' === $markup ) {
					do_action( "pie_forms_field_options_bottom_{$option}", $field, $this );
				}

				echo $output; 

				if ( 'open' === $markup ) {
					do_action( "pie_forms_field_options_top_{$option}", $field, $this );
				}

				if ( 'close' === $markup ) {
					do_action( "pie_forms_field_options_after_{$option}", $field, $this );
				}
			} else {
				echo wp_kses($output, Pie_Forms()->core()->pform_get_allowed_tags()); 
			}
		} else {
			return $output;
		}
	}

	/**
	 * Helper function to create common field options that are used frequently
	 * in the field preview.
	 */
	public function field_preview_option( $option, $field, $args = array(), $echo = true ) {
		$output = '';
		$class  = ! empty( $args['class'] ) ? Pie_Forms()->core()->pform_sanitize_classes( $args['class'] ) : '';
		
		$form_fields =  Pie_Forms()->core()->get_form_fields($this->form_id);

		$hide_all_label = ( isset($form_fields['settings']['hide_all_label'])  && $form_fields['settings']['hide_all_label'] == 1 ) ? "hide-label" : "";
	
		switch ( $option ) {
			case 'label':
				$label  = isset( $field['label'] ) && ! empty( $field['label'] ) ? $field['label'] : '';
				$output = sprintf( '<label class="label-title %s '.$hide_all_label .'"><span class="text">%s</span><span class="required">*</span></label>', $class, $label );
				break;

			case 'description':
				$description = isset( $field['description'] ) && ! empty( $field['description'] ) ? esc_html($field['description']) : '';
				$description = false !== strpos( $class, 'nl2br' ) ? nl2br( $description ) : $description;
				$position 	 = isset($form_fields['settings']['field_description_postion'])  && $form_fields['settings']['field_description_postion'] == 'before' ? "position-before" : "" ;
				$output      = sprintf( '<div class="description %s %s">%s</div>', $class,$position, $description );
				// var_dump($field);
				break;

			case 'choices':
				$values         = ! empty( $field['choices'] ) ? $field['choices'] : $this->defaults;
				$choices_fields = array( 'select', 'radio', 'checkbox', 'multiselect', 'payment-multiple' );

				// Notify if choices source is currently empty.
				if ( empty( $values ) ) {
					$values = array(
						'label' => esc_html__( ' - ', 'pie-forms' ),
					);
				}

				// Build output.
				if ( ! in_array( $field['type'], $choices_fields, true ) ) {
					break;
				}

				switch ( $field['type'] ) {
					case 'checkbox':
						$type = 'checkbox';
						break;

					case 'select':
						$type = 'select';
						break;

					case 'multiselect':
						$type = 'multiselect';
						break;
					
					default:
						$type = 'radio';
						break;
				}

				$list_class     = array( 'widefat', 'primary-input' );
				

				if ( 'select' === $type || 'multiselect' === $type ) {
					$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
					$output      = sprintf( '<select class="%s" disabled>', Pie_Forms()->core()->pform_sanitize_classes( $list_class, true ) );

					// Optional placeholder.
					if ( ! empty( $placeholder ) ) {
						$output .= sprintf( '<option value="" class="placeholder">%s</option>', esc_html( $placeholder ) );
					}

					// Build the select options (even though user can only see 1st option).
					foreach ( $values as $value ) {
						$default  = isset( $value['default'] ) ? (bool) $value['default'] : false;
						$selected = ! empty( $placeholder ) ? '' : selected( true, $default, false );
						$output  .= sprintf( '<option %s>%s</option>', $selected, esc_html( $value['label'] ) );
					}

					$output .= '</select>';

					$is_multiselect = ('multiselect' === $type ? '<span class="multiselect-values"></span>' : '' );
					$output .= $is_multiselect;
				} else {
					$output = sprintf( '<ul class="%s">', Pie_Forms()->core()->pform_sanitize_classes( $list_class, true ) );

					// Individual checkbox/radio options.
					foreach ( $values as $value ) {
						$default     = isset( $value['default'] ) ? $value['default'] : '';
						$selected    = checked( '1', $default, false );
						$placeholder = '';
						$item_class  = array();

						if ( ! empty( $value['default'] ) ) {
							$item_class[] = 'pie-forms-selected';
						}

						$output .= sprintf( '<li class="%s">', Pie_Forms()->core()->pform_sanitize_classes( $item_class, true ) );
						
							$output .= sprintf( '<input type="%s" %s disabled>%s', $type, $selected, $value['label'] );
						

						$output .= '</li>';
					}

					$output .= '</ul>';
				}
				break;
		}

		if ( $echo ) {
			echo wp_kses($output, Pie_Forms()->core()->pform_get_allowed_tags()); 
		} else {
			return $output;
		}
	}

	/**
	 * Create a new field in the admin AJAX editor.
	 */
	public function field_new() {

		check_ajax_referer( 'pieforms_new_field_nonce', 'security' );
		if ( ! isset( $_POST['form_id'] ) || empty( $_POST['form_id'] ) ) {
			die( esc_html__( 'No form ID found', 'pie-forms' ) );
		}

		// Check for field type to add.
		if ( ! isset( $_POST['field_type'] ) || empty( $_POST['field_type'] ) ) {
			die( esc_html__( 'No field type found', 'pie-forms' ) );
		}

		// Grab field data.
		$field_args     = ! empty( $_POST['defaults'] ) ? (array)sanitize_post(wp_unslash( $_POST['defaults'] )) : array(); 
		$field_type     = sanitize_text_field( wp_unslash( $_POST['field_type'] ) ); 
		$field_id       = Pie_Forms()->core()->field_unique_key( sanitize_key(wp_unslash( $_POST['form_id'] )) ); 
		$field          = array(
			'id'          => $field_id,
			'type'        => $field_type,
			'label'       => $this->name,
			'description' => '',
		);
		$field          = wp_parse_args( $field_args, $field );
		$field          = apply_filters( 'pie_forms_field_new_default', $field );
		$field_required = apply_filters( 'pie_forms_field_new_required', '', $field );
		$field_class    = apply_filters( 'pie_forms_field_new_class', '', $field );

		// Field types that default to required.
		if ( ! empty( $field_required ) ) {
			$field_required    = 'required';
			$field['required'] = '1';
		}

		// Build Preview.
		ob_start();
		$this->field_preview( $field , $this->form_data);
		$preview      = sprintf( '<div class="edit pie-forms-field pie-forms-field-%s %s %s" id="pie-forms-field-%s" data-field-id="%s" data-field-type="%s">', $field_type, $field_required, $field_class, $field['id'], $field['id'], $field_type );
			$preview .= sprintf( '<div class="edit-delete-wrapper">' );
			$preview .= sprintf( '<a href="#" class="pie-forms-field-duplicate" title="%s"><span class="duplicate"></span></a>', __( 'Duplicate Field', 'pie-forms' ) );
			$preview .= sprintf( '<a href="javascript:;" class="pie-forms-field-delete" title="%s"><span class="delete"></span></a>', __( 'Delete Field', 'pie-forms' ) );
			$preview .= sprintf( '<a href="#" class="pie-forms-field-setting" title="%s"><span class="edit"></span></a>', __( 'Settings', 'pie-forms' ) );
			$preview .= sprintf( '</div>' );
			$preview .= ob_get_clean();
		$preview     .= '</div>';

		// Build Options.
		$options      = sprintf( '<div class="pie-forms-field-option pie-forms-field-option-%s" id="pie-forms-field-option-%s" data-field-id="%s">', esc_attr( $field['type'] ), $field['id'], $field['id'] );
			$options .= sprintf( '<input type="hidden" name="form_fields[%s][id]" value="%s" class="pie-forms-field-option-hidden-id">', $field['id'], $field['id'] );
			$options .= sprintf( '<input type="hidden" name="form_fields[%s][type]" value="%s" class="pie-forms-field-option-hidden-type">', $field['id'], esc_attr( $field['type'] ) );
			ob_start();
			$this->field_options( $field );
			$options .= ob_get_clean();
		$options     .= '</div>';

		$form_field_array = explode( '-', $field_id );
		$field_id_int     = absint( $form_field_array[ count( $form_field_array ) - 1 ] );

		// Prepare to return compiled results.
		wp_send_json_success(
			array(
				'form_id'       => (int) sanitize_key(trim($_POST['form_id'])),
				'field'         => $field,
				'preview'       => $preview,
				'options'       => $options,
				'form_field_id' => ( $field_id_int + 1 ),
			)
		);
	}

	/**
	 * Field display on the form front-end.
	 */
	public function field_display( $field, $field_atts, $form_data ) {}

	/**
	 * Display field input errors if present.
	 */
	public function field_display_error( $key, $field ) {
		// Need an error.
		if ( empty( $field['properties']['error']['value'][ $key ] ) ) {
			return;
		}

		printf(
			'<label class="pie-forms-error pf-error" for="%s">%s</label>',
			esc_attr( $field['properties']['inputs'][ $key ]['id'] ),
			esc_html( $field['properties']['error']['value'][ $key ] )
		);
	}

	/**
	 * Display field input sublabel if present.
	 */
	public function field_display_sublabel( $key, $position, $field ) {
		// Need a sublabel value.
		if ( empty( $field['properties']['inputs'][ $key ]['sublabel']['value'] ) ) {
			return;
		}

		$pos    = ! empty( $field['properties']['inputs'][ $key ]['sublabel']['position'] ) ? $field['properties']['inputs'][ $key ]['sublabel']['position'] : 'after';
		$hidden = ! empty( $field['properties']['inputs'][ $key ]['sublabel']['hidden'] ) ? 'pie-forms-sublabel-hide' : '';

		if ( $pos !== $position ) {
			return;
		}

	}

	/**
	 * Validates field on form submit.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {
		$field_type         = isset( $form_data['form_fields'][ $field_id ]['type'] ) ? esc_html($form_data['form_fields'][ $field_id ]['type']) : '';

		$required_field     = isset( $form_data['form_fields'][ $field_id ]['required'] ) && !empty($form_data['form_fields'][ $field_id ]['required']) ? esc_html($form_data['form_fields'][ $field_id ]['required']) : false;

		// GDPR field should always be required.
		if( $field_type === "gdpr" ){
			$required_field	= true;	
		}
		
		$conditional_status = isset( $form_data['form_fields'][ $field_id ]['conditional_logic_status'] ) ? absint($form_data['form_fields'][ $field_id ]['conditional_logic_status']) : 0;
		
		$validation_rule = isset($form_data['form_fields'][$field_id]['validation_rule']) ? esc_html($form_data['form_fields'][$field_id]['validation_rule']): "";
		
		$custom_validation_message = isset($form_data['form_fields'][$field_id]['custom_validation_message']) ? esc_html($form_data['form_fields'][$field_id]['custom_validation_message']): "Please enter a valid value";
		
		// If there is set custom regex else default rules set
		if(isset($form_data['form_fields'][$field_id]['custom_regex']) && !empty($form_data['form_fields'][$field_id]['custom_regex']) && $validation_rule === 'custom_regex'){
			$custom_regex  = $form_data['form_fields'][$field_id]['custom_regex'];
		}else{
			$custom_regex = $validation_rule;
		}	
		
		// Basic required check - If field is marked as required, check for entry data.
		if ( false !== $required_field && '1' !== $conditional_status && ( empty( $field_submit ) && '0' !== $field_submit ) ) {
			Pie_Forms()->task->errors[ $form_data['id'] ][ $field_id ] = Pie_Forms()->core()->pform_get_required_label();
		}
		if(!empty($validation_rule)){
			if(Pie_Forms()->core()->get_define_regex(sanitize_key($_POST['pie_forms']['form_fields'][ $field_id ]),$custom_regex))
				{
					$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( $custom_validation_message, 'pie-forms' ) );
				}
			
		}
		// Type validations.
		switch ( $field_type ) {
			case 'phone':
				$custom_regex = "phone";
				if ( !empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) && Pie_Forms()->core()->get_define_regex(sanitize_key($_POST['pie_forms']['form_fields'][ $field_id ]),$custom_regex) ) { 
					$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'Please enter a valid phone number', 'pie-forms' ) );

				
				}
				break;
			case 'url':
				if ( ! empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) && filter_var( $field_submit, FILTER_VALIDATE_URL ) === false ) { 
					$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'Please enter a valid url', 'pie-forms' ) );
				}
				break;
			case 'email':
				if ( is_array( $field_submit ) ) {
					$value = ! empty( $field_submit['primary'] ) ? esc_html($field_submit['primary']) : '';
				} else {
					$value = ! empty( $field_submit ) ? esc_html($field_submit) : '';
				}
				if ( ! empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) && ! is_email( $value ) ) { 
					$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'Please enter a valid email address', 'pie-forms' ) );
				}
				break;
			case 'number':
				if ( ! empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) && ! is_numeric( $field_submit ) ) { 
					$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'Please enter a valid number', 'pie-forms' ) );
				}
				break;
			case 'textarea':

				$limit_enabled 	= 	isset($form_data['form_fields'][$field_id]["limit_enabled"]) ? absint($form_data['form_fields'][$field_id]["limit_enabled"]) : '';
				$limit_mode 	= 	isset($form_data['form_fields'][$field_id]["limit_mode"]) ? $form_data['form_fields'][$field_id]["limit_mode"] : '';
				$limit_count 	= 	isset($form_data['form_fields'][$field_id]["limit_count"]) ? $form_data['form_fields'][$field_id]["limit_count"] : '';
				
				if ( ! empty( $_POST['pie_forms']['form_fields'][ $field_id ] ) ) { 
					if($limit_enabled){
						if($limit_mode === "words"){
							$value_length = str_word_count($field_submit);
							if($value_length > $limit_count){
								$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'You have exceeded the maximum number words', 'pie-forms' ) );
							}
						}else{
							$value_length = strlen($field_submit);
							if($value_length > $limit_count){
								$validation_text = get_option( 'pf_' . $field_type . '_validation', esc_html__( 'You have exceeded the maximum number character', 'pie-forms' ) );
							}
						}
					}
				}
		}

		if ( isset( $validation_text ) ) {
			Pie_Forms()->task->errors[ $form_data['id'] ][ $field_id ] = apply_filters( 'pie_forms_type_validation', esc_html($validation_text) );
			update_option( 'pf_validation_error', 'yes' );
		}
		///var_dump($validation_text);

	}

	/**
	 * Formats and sanitizes field.
	 */
	public function format( $field_id, $field_submit, $form_data, $meta_key ) {
		if ( is_array( $field_submit ) ) {
			$field_submit = array_filter( $field_submit );
			$field_submit = implode( "\r\n", $field_submit );
		}

		$name = ! empty( $form_data['form_fields'][ $field_id ]['label'] ) ? make_clickable( $form_data['form_fields'][ $field_id ]['label'] ) : '';

		// Sanitize but keep line breaks.
		$value = Pie_Forms()->core()->pform_sanitize_textarea_field( $field_submit );

		Pie_Forms()->task->form_fields[ $field_id ] = array(
			'name'     => esc_html($name),
			'value'    => esc_html($value),
			'id'       => absint($field_id),
			'type'     => $this->type,
			'meta_key' => $meta_key,
		);
	}

	public function field_exporter( $field ) {
		$export = array();

		switch ( $this->type ) {
			case 'radio':
				$value  = esc_html($field['value']);
				$export = array(
					'label' => ! empty( $field['name'] ) ? esc_html($field['name']) : '',
					'value' => ! empty( $value ) ? $value : false,
				);
			break;

			case 'checkbox':
				case 'multiselect':
					$value = array();
	
					if ( count( $field['value'] ) ) {
						foreach ( $field['value']['label'] as $key => $choice ) {
	
							if ( ! empty( $choice ) ) {
								$value[ $key ] = $choice;
							}
						}
					}
					$export = array(
						'label' => ! empty( $field['value']['name'] ) ? esc_html($field['value']['name']) : "",
						'value' => is_array( $value ) ? implode( '<br>', array_values( $value ) ) : false,
					);
			break;

			default:
				$export = array(
					'label' => ! empty( $field['name'] ) ? esc_html($field['name']) : "",
					'value' => ! empty( $field['value'] ) ? (is_array( $field['value'] ) ? $this->implode_recursive( $field['value'] ) : $field['value'] ): false,
				);
		}
		return $export;
	}

	/**
	 * Field with limit.
	 */
	protected function field_is_limit( $field ) {
		if ( in_array( $field['type'], array( 'text', 'textarea' ), true ) ) {
			return isset( $field['limit_enabled'] ) && ! empty( $field['limit_count'] );
		}
	}
	
}