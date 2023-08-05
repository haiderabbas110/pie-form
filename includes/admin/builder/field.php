<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'PFORM_Admin_Builder_Field', false ) ) {
	return new PFORM_Admin_Builder_Field();
}

/**
 * PFORM_Admin_Builder_Field class.
 */
class PFORM_Admin_Builder_Field extends PFORM_Admin_Builder_Page {


	public static $parts = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id      = 'fields';
		$this->label   = __( 'Fields', 'pie-forms' );
		$this->sidebar = true;
		//$this->init_hooks();
		parent::__construct();
	}

	/**
	 * Hook in tabs.
	 */
	public function init_hooks() {
			add_action( 'pie_forms_builder_fields', array( $this, 'output_fields' ) );
			add_action( 'pie_forms_builder_fields_options', array( $this, 'output_fields_options' ) );
			add_action( 'pie_forms_builder_fields_preview', array( $this, 'output_fields_preview' ) );
	}

	/**
	 * Outputs the builder sidebar.
	 */
	public function output_sidebar() {
		?>
			
		    <div class="fields-elements" id="tab-add-fields">
				<div class="search-bar">
						<input type="text" name="search-field" placeholder="Search Fields">
				</div>
				<ul class='add-fields'>
					<?php do_action( 'pie_forms_builder_fields', $this->form ); ?>
				</ul>
			</div>
			<div class='fields-options' id="tab-field-options">
                <div class="pie-forms-field-option-wrapper">
					<?php do_action( 'pie_forms_builder_fields_options', $this->form ); ?>
				</div>
			</div>
			<?php do_action( 'pie_forms_builder_fields_tab_content', $this->form ); ?>
		<?php
	}

	/**
	 * Outputs the builder content.
	 */
	public function output_content() {
		?>	
				
				
				<div class="pie-forms-title-desc">
					<input id= "pf-edit-form-name" type="text" class="pie-forms-form-name pie-forms-name-input" value ="<?php echo isset( $this->form->form_title ) ? esc_html( $this->form->form_title ) : esc_html__( 'Form not found.', 'pie-forms' ); ?>" disabled autocomplete="off" required>
					<span id="edit-form-name" class = "pf-icon pf-edit-icon"></span>
				</div>
				<div class="field-main-wrapper ScrollBar" id="pie-form-element_fields">
					<?php do_action( 'pie_forms_builder_fields_preview', $this->form ); ?>
				</div>
				
		<?php
	}

	/**
	 * Output fields group buttons.
	 */
	public function output_fields() {
		$form_fields = Pie_Forms()->form_fields();
		
		
        if ( ! empty( $form_fields ) ) {
        	//$i = 0;		
          foreach ( $form_fields as $group => $form_field ) {
          	//$active = ($i === 0) ? "active" : "";
            ?>
                <li class="active"><span> <?php echo Pie_Forms()->core()->get_fields_group($group); ?> </span>
                  <ul class="pie-form-fields" id="pie-form-draggable" >
                  <?php foreach ( $form_field as $field ) : ?>
                      <li class="pie-form-element-box <?php echo esc_attr( $field->class ); ?> <?php echo esc_attr($field->type); ?>" data-field-type="<?php echo esc_attr($field->type)?>">
                        <span class="pie-form-icon">
                            <img src="<?php echo esc_url(Pie_Forms::$url) . 'assets/images/builder/'.$field->icon . '.png' ?>" alt="">
                          </span>
                        <span class="pie-form-label"><?php echo esc_html($field->name); ?></span>
                      </li>
                      <?php endforeach; ?>
                  </ul> 
                </li>
          		
          <?php //$i++;

      		}
          }
	}

	/**
	 * Output fields setting options.
	 */
	public function output_fields_options() {
		$fields = isset( $this->form_data['form_fields'] ) ? $this->form_data['form_fields'] : array();
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				
				$field_option_class = apply_filters(
					'pie_forms_builder_field_option_class',
					array(
						'pie-forms-field-option',
						'pie-forms-field-option-' . esc_attr( $field['type'] ),
					),
					$field
				);

				?>
				<div class="<?php echo esc_attr( implode( ' ', $field_option_class ) ); ?>" id="pie-forms-field-option-<?php echo esc_attr( $field['id'] ); ?>" data-field-id="<?php echo esc_attr( $field['id'] ); ?>" >
					<input type="hidden" name="form_fields[<?php echo esc_attr( $field['id'] ); ?>][id]" value="<?php echo esc_attr( $field['id'] ); ?>" class="pie-forms-field-option-hidden-id" />
					<input type="hidden" name="form_fields[<?php echo esc_attr( $field['id'] ); ?>][type]" value="<?php echo esc_attr( $field['type'] ); ?>" class="pie-forms-field-option-hidden-type" />
					<?php do_action( 'pie_forms_builder_fields_options_' . $field['type'], $field ); ?>
				</div>
				<?php
			}
		} else {
			printf( '<p class="no-fields">%s</p>', esc_html__( 'You don\'t have any fields yet.', 'pie-forms' ) );
		}
	}

	/**
	 * Outputs fields preview content.
	 */
	public function output_fields_preview() {
		$form_data = $this->form_data;
		$fields    = isset( $form_data['form_fields'] ) ? $form_data['form_fields'] : array();
		
			if (!empty($fields)) {
				foreach ($fields as $field_id => $value) {
						$this->field_preview( $fields[ $field_id ] , $form_data);
				}
			}
		}


	/**
	 * Single Field preview.
	 */
	public function field_preview( $field ,$form_data) {
	//	$is_pro = pie_forms()->core()->get_pro_form_field_types();
		if ( in_array( $field['type'],  pie_forms()->core()->get_pro_form_field_types(), true ) ) {
			return;
		}
		$css  = ! empty( $field['size'] ) ? 'size-' . esc_attr( $field['size'] ) : '';
		$css .= ! empty( $field['label_hide'] ) && '1' === $field['label_hide'] ? ' label_hide' : '';
		$css .= ! empty( $field['sublabel_hide'] ) && '1' === $field['sublabel_hide'] ? ' sublabel_hide' : '';
		$css .= ! empty( $field['required'] ) && '1' === $field['required'] ? ' required' : '';
		$css .= ! empty( $field['input_columns'] ) && '2' === $field['input_columns'] ? ' pie-forms-list-2-columns' : '';
		$css .= ! empty( $field['input_columns'] ) && '3' === $field['input_columns'] ? ' pie-forms-list-3-columns' : '';
		$css .= ! empty( $field['input_columns'] ) && 'inline' === $field['input_columns'] ? ' pie-forms-list-inline' : '';
		$css  = apply_filters( 'pie_forms_field_preview_class', $css, $field );

		$addon_field_check = apply_filters( 'pie_forms_addon_activated_field', false, $field );

		if( ($field['type'] === "gdpr" && get_option('pf_gdpr_options') !== 'yes') || $addon_field_check ){
			return;
		}
		/* if($field['type'] == 'payment-single' && !Pie_Forms()->core()->pforms_check_payment_addon_active()){						
			return;
		} */
		printf( '<div class="edit pie-forms-field dropper pie-forms-field-%1$s %2$s" id="pie-forms-field-%3$s" data-field-id="%3$s" data-field-type="%4$s">', esc_attr( $field['type'] ), esc_attr( $css ), esc_attr( $field['id'] ), esc_attr( $field['type'] ) );
			printf( '<div class="edit-delete-wrapper">' );
				printf( '<a href="#" class="pie-forms-field-duplicate" title="%s"><span class="duplicate"></span></a>', esc_html__( 'Duplicate', 'pie-forms' ) );
				printf( '<a href="#" class="pie-forms-field-delete" title="%s"><span class="delete"></span></a>', esc_html__( 'Delete', 'pie-forms' ) );
				printf( '<a href="#" class="pie-forms-field-setting" title="%s"><span class="edit"></span></a>', esc_html__( 'Settings', 'pie-forms' ) );
			printf( '</div>' );
				do_action( 'pie_forms_builder_fields_preview_' . $field['type'], $field , $form_data );
		echo '</div>';
	}
}
