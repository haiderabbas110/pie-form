<?php
defined( 'ABSPATH' ) || exit;

	class PFORM_Admin_Settings {

		/**
		 * Setting pages.
		 *
		 * @var array
		 */
		public static $settings = array();

		/**
		 * Error messages.
		 *
		 * @var array
		 */
		public static $errors = array();

		/**
		 * Update messages.
		 *
		 * @var array
		 */
		public static $messages = array();
		/**
		 * Save the settings.
		 */
		public static function save() {
			global $current_tab;
			
			check_admin_referer( 'pie-forms-settings' );

			do_action( 'pie_forms_settings_save_' . $current_tab );
			do_action( 'pie_forms_update_options_' . $current_tab );
			do_action( 'pie_forms_update_options' );

			self::add_message( esc_html__( 'Your settings have been saved.', 'pie-forms' ) );

			do_action( 'pie_forms_settings_saved' );
		}

		/**
		 * Add a message.
		 *
		 * @param string $text Message.
		 */
		public static function add_message( $text ) {
			self::$messages[] = $text;
		}

		/**
		 * Add an error.
		 *
		 * @param string $text Message.
		 */
		public static function add_error( $text ) {
			self::$errors[] = $text;
		}

		/**
		 * Output messages + errors.
		 */
		public static function show_messages() {
			if ( count( self::$errors ) > 0 ) {
				foreach ( self::$errors as $error ) {
					echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
				}
			} elseif ( count( self::$messages ) > 0 ) {
				foreach ( self::$messages as $message ) {
					echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
				}
			}
		}
		
		/**
		 * Get a setting from the settings API.
		 */
		public static function get_option( $option_name, $default = '' ) {
			if ( ! $option_name ) {
				return $default;
			}

			// Array value.
			if ( strstr( $option_name, '[' ) ) {

				parse_str( $option_name, $option_array );

				// Option name is first key.
				$option_name = current( array_keys( $option_array ) );

				// Get value.
				$option_values = get_option( $option_name, '' );

				$key = key( $option_array[ $option_name ] );

				if ( isset( $option_values[ $key ] ) ) {
					$option_value = $option_values[ $key ];
				} else {
					$option_value = null;
				}
			} else {
				// Single value.
				$option_value = get_option( $option_name, null );
			}

			if ( is_array( $option_value ) ) {
				$option_value = array_map( 'stripslashes', $option_value );
			} elseif ( ! is_null( $option_value ) ) {
				$option_value = stripslashes( $option_value );
			}

			return ( null === $option_value ) ? $default : $option_value;
		}

		/**
		 * Output admin fields.
		 */
		public static function output_fields( $options ) {
			foreach ( $options as $value ) {
				if ( ! isset( $value['type'] ) ) {
					continue;
				}
				if ( ! isset( $value['id'] ) ) {
					$value['id'] = '';
				}
				if ( ! isset( $value['title'] ) ) {
					$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
				}
				if ( ! isset( $value['class'] ) ) {
					$value['class'] = '';
				}
				if ( ! isset( $value['css'] ) ) {
					$value['css'] = '';
				}
				if ( ! isset( $value['href'] ) ) {
					$value['href'] = '';
				}
				if ( ! isset( $value['default'] ) ) {
					$value['default'] = '';
				}
				if ( ! isset( $value['desc'] ) ) {
					$value['desc'] = '';
				}
				if ( ! isset( $value['desc_tip'] ) ) {
					$value['desc_tip'] = false;
				}
				if ( ! isset( $value['placeholder'] ) ) {
					$value['placeholder'] = '';
				}
				if ( ! isset( $value['suffix'] ) ) {
					$value['suffix'] = '';
				}
				if ( ! isset( $value['disabled'] ) ) {
					$value['disabled'] = '';
				}
				if ( ! isset( $value['value'] ) ) {
					$value['value'] = self::get_option( $value['id'], $value['default'] );
				}

				// Custom attribute handling.
				$custom_attributes = array();

				if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
					foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
						$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
					}
				}
				// Description handling.
				$field_description = self::get_field_description( $value );
				$description       = $field_description['description'];
				$tooltip_html      = $field_description['tooltip_html'];

				// Switch based on type.
				switch ( $value['type'] ) {

					// Section Titles.
					case 'title':
						if ( ! empty( $value['title'] ) ) {
							echo '<h2 class="'.esc_attr($value['class']).'">' . esc_html( $value['title'] ) . '</h2>';
						}
						if ( ! empty( $value['desc'] ) ) {
							echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
						}
						echo '<table class="form-table">' . "\n\n";
						if ( ! empty( $value['id'] ) ) {
							do_action( 'pie_forms_settings_' . sanitize_title( $value['id'] ) );
						}
						break;
					// Section Titles.
					case 'upgrade':
						if ( isset( $value['is_visible'] ) ) {
							$visibility_class[] = $value['is_visible'] ? 'pie-forms-visible' : 'pie-forms-hidden';
						}

						if ( ! empty( $value['title'] ) ) {
							echo '<a href="'.esc_url($value['href']).'" class="'.esc_attr( implode( ' ', $visibility_class) ).' '.esc_attr($value['class']).'">' . esc_html( $value['title'] ) . '</a>';
						}
						if ( ! empty( $value['desc'] ) ) {
							echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
						}
						if ( ! empty( $value['id'] ) ) {
							do_action( 'pie_forms_settings_' . sanitize_title( $value['id'] ) );
						}
						break;

					// Section Ends.
					case 'sectionend':
						if ( ! empty( $value['id'] ) ) {
							do_action( 'pie_forms_settings_' . sanitize_title( $value['id'] ) . '_end' );
						}
						echo '</table>';
						if ( ! empty( $value['id'] ) ) {
							do_action( 'pie_forms_settings_' . sanitize_title( $value['id'] ) . '_after' );
						}
						break;

					// Standard text inputs and subtypes like 'number'.
					case 'text':
						$option_value     = $value['value'];
						$visibility_class = array();

						if ( isset( $value['is_visible'] ) ) {
							$visibility_class[] = $value['is_visible'] ? 'pie-forms-visible' : 'pie-forms-hidden';
						}

						?><tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> </label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="<?php echo esc_attr( $value['type'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
									/><?php echo esc_html( $value['suffix'] ); ?> <?php echo '</p><i>'.esc_html($tooltip_html).'</i></p>';?>
							</td>
						</tr>
						<?php
						break;
					
						// Select boxes.
					case 'select':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<select
									name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes ));  ?>
									
									>
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<option value="<?php echo esc_attr( $key ); ?>"
											<?php

											if ( is_array( $option_value ) ) {
												selected( in_array( (string) $key, $option_value, true ), true );
											} else {
												selected( $option_value, (string) $key );
											}

											?>
										>
										<?php echo esc_html( $val ); ?></option>
										<?php
									}
									?>
								</select>
							</td>
						</tr>
						<?php
						break;
					
					// Radio inputs.
					case 'radio':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> </label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
								<fieldset>
									<ul class="<?php echo esc_attr( $value['class'] ); ?>">
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<li>
											<label><input
												name="<?php echo esc_attr( $value['id'] ); ?>"
												id="<?php echo esc_attr( $value['id'] ); ?>"
												value="<?php echo esc_attr( $key ); ?>"
												type="radio"
												style="<?php echo esc_attr( $value['css'] ); ?>"
												class="<?php echo esc_attr( $value['class'] ); ?>"
												<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
												<?php checked( $key, $option_value ); ?>
												/> <?php echo esc_html( $val ); ?></label>
										</li>
										<?php
									}
									?>
									</ul>
								</fieldset>
							</td>
						</tr>
						<?php
						break;


					// Checkbox input.
					case 'checkbox':
						$option_value     = $value['value'];
						$visibility_class = array();

						if ( ! isset( $value['hide_if_checked'] ) ) {
							$value['hide_if_checked'] = false;
						}
						if ( ! isset( $value['show_if_checked'] ) ) {
							$value['show_if_checked'] = false;
						}
						if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
							$visibility_class[] = 'hidden_option';
						}
						if ( 'option' === $value['hide_if_checked'] ) {
							$visibility_class[] = 'hide_options_if_checked';
						}
						if ( 'option' === $value['show_if_checked'] ) {
							$visibility_class[] = 'show_options_if_checked';
						}
						if ( isset( $value['is_visible'] ) ) {
							$visibility_class[] = $value['is_visible'] ? 'pie-forms-visible' : 'pie-forms-hidden';
						}

						if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {
							?>
								<tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
									<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
									<td class="forminp forminp-checkbox">
										<fieldset>
							<?php
						} else {
							?>
								<fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
							<?php
						}

						if ( ! empty( $value['title'] ) ) {
							?>
								<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
							<?php
						}

						?>
							<label for="<?php echo esc_attr( $value['id'] ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="checkbox"
									class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
									<?php echo esc_attr( $value['disabled'] ); ?>
									value="1"
									<?php checked( $option_value, 'yes' ); ?>
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
									/> <?php echo esc_html($description); ?>
								</label> <?php echo esc_html($tooltip_html); ?>
						<?php

						if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) {
							?>
										</fieldset>
									</td>
								</tr>
							<?php
						} else {
							?>
								</fieldset>
							<?php
						}
						break;
					// Textarea.
					case 'textarea':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html($tooltip_html);?></label>
								</th>
							<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
							

								<textarea rows="10" cols="70"
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo esc_attr( $value['disabled'] ); ?>
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
									><?php echo esc_textarea( $option_value );  ?></textarea>
									<?php echo wp_kses_post($description);  ?>
							</td>
						</tr>
						<?php
						break;
					case 'button':
						$option_value = $value['value'];

						?>
						<tr valign="top">
						
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				
								<button name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>" 
									<?php echo esc_attr( $value['disabled'] ); ?>
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>>
									<?php echo esc_html( $value['title'] ); ?> 
								</button>
							</td>
						</tr>
						<?php
						break;
					case 'addons_license':

						if ( ! isset( $value['email_address']['id'] ) ) {
							$value['email_address']['id'] = '';
						}
						if ( ! isset( $value['email_address']['title'] ) ) {
							$value['email_address']['title'] = isset( $value['email_address']['name'] ) ? $value['email_address']['name'] : '';
						}
						if ( ! isset( $value['email_address']['class'] ) ) {
							$value['email_address']['class'] = '';
						}
						if ( ! isset( $value['email_address']['css'] ) ) {
							$value['email_address']['css'] = '';
						}
						if ( ! isset( $value['email_address']['default'] ) ) {
							$value['email_address']['default'] = '';
						}
						if ( ! isset( $value['email_address']['desc'] ) ) {
							$value['email_address']['desc'] = '';
						}
						if ( ! isset( $value['email_address']['desc_tip'] ) ) {
							$value['email_address']['desc_tip'] = false;
						}
						if ( ! isset( $value['email_address']['placeholder'] ) ) {
							$value['email_address']['placeholder'] = '';
						}
						if ( ! isset( $value['email_address']['suffix'] ) ) {
							$value['email_address']['suffix'] = '';
						}
						if ( ! isset( $value['email_address']['disabled'] ) ) {
							$value['email_address']['disabled'] = '';
						}
						if ( ! isset( $value['email_address']['value'] ) ) {
							$value['email_address']['value'] = self::get_option( $value['email_address']['id'], $value['email_address']['default'] );
						}
						if ( ! isset( $value['license_key']['id'] ) ) {
							$value['license_key']['id'] = '';
						}
						if ( ! isset( $value['license_key']['title'] ) ) {
							$value['license_key']['title'] = isset( $value['license_key']['name'] ) ? $value['license_key']['name'] : '';
						}
						if ( ! isset( $value['license_key']['class'] ) ) {
							$value['license_key']['class'] = '';
						}
						if ( ! isset( $value['license_key']['css'] ) ) {
							$value['license_key']['css'] = '';
						}
						if ( ! isset( $value['license_key']['default'] ) ) {
							$value['license_key']['default'] = '';
						}
						if ( ! isset( $value['license_key']['desc'] ) ) {
							$value['license_key']['desc'] = '';
						}
						if ( ! isset( $value['license_key']['desc_tip'] ) ) {
							$value['license_key']['desc_tip'] = false;
						}
						if ( ! isset( $value['license_key']['placeholder'] ) ) {
							$value['license_key']['placeholder'] = '';
						}
						if ( ! isset( $value['license_key']['suffix'] ) ) {
							$value['license_key']['suffix'] = '';
						}
						if ( ! isset( $value['license_key']['disabled'] ) ) {
							$value['license_key']['disabled'] = '';
						}
						if ( ! isset( $value['license_key']['value'] ) ) {
							$value['license_key']['value'] = self::get_option( $value['license_key']['id'], $value['license_key']['default'] );
						}
						
						$visibility_class = array();

						if ( isset( $value['is_visible'] ) ) {
							$visibility_class[] = $value['is_visible'] ? 'pie-forms-visible' : 'pie-forms-hidden';
						}

						?><tr valign="top" class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> </label>
							</th>
							<td class="forminp forminp-text" >
							<input
									name="<?php echo esc_attr( $value['email_address']['id'] ); ?>"
									id="<?php echo esc_attr( $value['email_address']['id'] ); ?>"
									type="text"
									style="<?php echo esc_attr( $value['email_address']['css'] ); ?>"
									value="<?php echo esc_attr( $value['email_address']['value'] ); ?>"
									class="<?php echo esc_attr( $value['email_address']['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['email_address']['placeholder'] ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
									/><?php echo esc_html( $value['email_address']['suffix'] ); ?> <?php echo '</p><i>'.esc_html($tooltip_html).'</i></p>'; ?>
							</td>
							<td class="forminp forminp-text">
							<input
									name="<?php echo esc_attr( $value['license_key']['id'] ); ?>"
									id="<?php echo esc_attr( $value['license_key']['id'] ); ?>"
									type="text"
									style="<?php echo esc_attr( $value['license_key']['css'] ); ?>"
									value="<?php echo esc_attr( $value['license_key']['value'] ); ?>"
									class="<?php echo esc_attr( $value['license_key']['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['license_key']['placeholder'] ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
									/><?php echo esc_html( $value['license_key']['suffix'] ); ?> <?php echo '</p><i>'.esc_html($tooltip_html).'</i></p>'; ?>
							</td>
							<td class="forminp forminp-activate" data-addon-name="<?php echo esc_attr( $value['id']); ?>">
								<?php 
								$is_activated = get_option('pieforms_manager_addon_'.$value['id'].'_activated');
								
								if($is_activated === 'Deactivated' || empty($is_activated)){
									$button_text = "Activate";
									$button_value = "activate";
								}else{
									$button_text = "Deactivate";
									$button_value = "deactivate";
								}
								?>	
								<button name="save-<?php echo esc_attr( $value['id']); ?>-addon" class="pie-forms-btn pie-forms-btn-primary pie-form-addon-activate pie-forms-save-<?php echo esc_attr( $value['id']); ?>-button" type="submit" value="<?php esc_attr_e( $button_value, 'pie-forms' ); ?>"><?php _e( $button_text, 'pie-forms' ); ?></button>
							</td>
						</tr>
						<?php
						break;
					// Upload Image
					case 'image':
						$option_value = $value['value'];
						?>
						<tr valign="top">
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html($tooltip_html);?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
								<?php
								if ( ! empty( $option_value ) ) {
								?>
									<img src="<?php echo esc_url_raw( $option_value ); ?>">
								<?php
								}	
								?>
								<input
									type="text"
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
								>
								<button class="pform-btn-upload-grey pform-upld-header-img"><?php echo esc_html( 'Upload Image', 'pie-forms' ); ?></button>
								<button class="pform-btn-upload-grey pform-rmv-header-img"><?php echo esc_html( 'Remove Image', 'pie-forms' ); ?></button>
								<?php echo wp_kses_post($description);  ?>
							</td>
						</tr>
						<?php
						break;
					// Color Picker
					case 'color':
						$option_value = $value['value'];
						?>
						<tr valign="top">
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html($tooltip_html);?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
								<input
									type="text"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									name="<?php echo esc_attr( $value['id'] );?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
								>
							</td>
						</tr>
						<?php
						break;
					case 'tinymce':
						$arguments = array(
											'tinymce'       => false,
									);
						$option_value = $value['value'];
						$arguments['textarea_name'] = $value['id'];
						$arguments['teeny']         = true;
						?>
						<tr valign="top">
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html($tooltip_html);?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
								<?php echo wp_editor( $option_value, $value['id'], $arguments ); ?>
							</td>
						</tr>
						<?php
						break;
						case 'zapier_integration':
							$forms = get_posts(
								array(
									'posts_per_page' => 999,
									/* 'post_type'      => 'pieforms', */
									'meta_query'     => array(
										array(
											'key'     => 'pform_zapier',
											'compare' => 'EXISTS',
										),
									),
								)
							);
							$zap_img_url = Pie_Forms::$url. "assets/images/settings/integrations/{$value['image']}" ;
							?>
							<tr valign="top" >
								<th scope="row" class="titledesc">
								<img class="<?php echo esc_attr( $value['class'] ); ?>" src="<?php echo $zap_img_url; ?>">
								</th>
								<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
									<div>
										<h3>
											<?php echo ucwords( esc_attr( sanitize_title( $value['title'] ) ) ); ?>
										</h3>
									</div>
									<?php 
									$is_activated = get_option('pieforms_manager_addon_zapier_activated');
									if( Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-zapier/pie-forms-for-wp-zapier.php') && $is_activated == "Activated" ): ?>
									<div>
									<p>
											<?php
											printf(
												/* translators: %s - provider name. */
												esc_html__( 'Integrate %s with Pie Forms', 'pie-forms' ),
												$value['title']
											);
											?>
										</p>
										<p>
											<?php
											printf(
												/* translators: %s - API key. */
												esc_html__( 'Your Pie Forms Zapier API key is %s', 'pie-forms' ),
												'<code>' . PFORM_ZAPIER_Holder_Integration::get_apikey() . '</code>'
											);
											?>
										</p>
									</div>
									<?php else: ?>
										<p><?php echo 
										sprintf(
											wp_kses(
												__( '<a href="%s" target="_blank" rel="noopener noreferrer">Click here for documentation on connecting Pie Forms with Zapier.</a>', 'pie-forms' ),
												array(
													'a' => array(
														'href'   => array(),
														'target' => array(),
														'rel'    => array(),
													),
												)
											),
											'https://pieforms.com/documentation/how-to-install-and-use-the-zapier-addon-with-pie-forms/'
										); ?>
										</p>
									<?php endif; ?>
								</td>
							</tr>
							<?php
						break;
					// Default: run an action.
					default:
						do_action( 'pie_forms_admin_field_' . $value['type'], $value );
						break;
				}
			}
		}
		/**
		 * Helper function to get the formatted description and tip HTML for a
		 * given form field. Plugins can call this when implementing their own custom
		 * settings types
		 */
		public static function get_field_description( $value ) {
			$description  = '';
			$tooltip_html = '';

			if ( true === $value['desc_tip'] ) {
				$tooltip_html = $value['desc'];
			} elseif ( ! empty( $value['desc_tip'] ) ) {
				$description  = $value['desc'];
				$tooltip_html = $value['desc_tip'];
			} elseif ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
			}

			if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ), true ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
				$description = wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
			}

			if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ), true ) ) {
				$tooltip_html = '<p class="description">' . esc_html($tooltip_html) . '</p>';
			} elseif ( $tooltip_html ) {
				$tooltip_html = $tooltip_html;
			}

			return array(
				'description'  => $description,
				'tooltip_html' => $tooltip_html,
			);
		}
		/**
		 * Save admin fields.
		*/
		public static function save_fields( $options, $data = null ) {
			if ( is_null( $data ) ) {
				// Since v1.4.7.10
				// As new data types are being added, the data to be saved is not just string anymore
				// $data = filter_var_array($_POST,FILTER_SANITIZE_STRING);
				$data = sanitize_post($_POST);
			}
			if ( empty( $data ) ) {
				return false;
			}
			
			// Options to update will be stored here and saved later.
			$update_options   = array();
			$autoload_options = array();

			// Loop options and get values to save.
			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
					continue;
				}

				// Get posted value.
				if ( strstr( $option['id'], '[' ) ) {
					parse_str( $option['id'], $option_name_array );
					$option_name  = current( array_keys( $option_name_array ) );
					$setting_name = key( $option_name_array[ $option_name ] );
					$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
				} else {
					$option_name  = $option['id'];
					$setting_name = '';
					$raw_value    = isset( $data[ $option['id'] ] ) ? wp_unslash( $data[ $option['id'] ] ) : null;
				}

				// Format the value based on option type.
				switch ( $option['type'] ) {
					case 'checkbox':
						$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
						break;
					case 'textarea':
					case 'tinymce':
						$value = wp_kses_post( trim( $raw_value ) );
						break;
					case 'select':
						$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
						if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
							$value = null;
							break;
						}
						$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
						$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
						break;
					case 'image':
						$value = esc_url_raw( $raw_value );
						break;
					case 'color':
						$value = Pie_Forms()->core()->pform_sanitize_hex_color( $raw_value );
						break;
					default:
						$value = sanitize_text_field($raw_value);
						break;
				}
				
				if ( is_null( $value ) ) {
					continue;
				}

				// Check if option is an array and handle that differently to single values.
				if ( $option_name && $setting_name ) {
					if ( ! isset( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = get_option( $option_name, array() );
					}
					if ( ! is_array( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = array();
					}
					$update_options[ $option_name ][ $setting_name ] = $value;
				} else {
					$update_options[ $option_name ] = $value;
				}

				$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;

				do_action( 'pie_forms_update_option', $option );
			}

			// Save all options in our array.
			foreach ( $update_options as $name => $value ) {
				update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
			}
			do_action('pie_forms_settings_save_field');
			return true;
		}
	}