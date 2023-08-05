<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Core_Functions
{   

    public function __construct()
    {

	}

	/**
	 * Get all PieForms screen ids.
	 *
	 * @return array
	 */
	function pform_get_screen_ids() {
		$pf_screen_id = sanitize_title( esc_html__( 'Pie Forms', 'pie-forms' ) );
		$screen_ids    = array(
			'dashboard_page_pf-welcome',
			'toplevel_page_' . $pf_screen_id,
			$pf_screen_id . '_page_pf-builder',
			$pf_screen_id . '_page_pf-entries',
			$pf_screen_id . '_page_pf-settings',
			$pf_screen_id . '_page_pf-tools',
			$pf_screen_id . '_page_pf-addons',
			$pf_screen_id . '_page_pf-email-templates',
		);

		return apply_filters( 'pie_forms_screen_ids', $screen_ids );
	}


	/**
	 * Get a builder fields type's name.
	 */
	public function get_fields_group( $type = '' ) {
		$types = $this->get_fields_groups();
		return isset( $types[ $type ] ) ? $types[ $type ] : '';
	}

	/**
	 * Get builder fields groups.
	 *
	 * @return array
	 */
	public function get_fields_groups() {
		return (array) apply_filters(
			'pie_forms_builder_fields_groups',
			array(
				'basic'  => __( 'Basic Fields', 'pie-forms' ),
				'advanced' => __( 'Advanced Fields', 'pie-forms' ),
				'payment'  => __( 'Payment Fields', 'pie-forms' ),
				'survey'   => __( 'Survey Fields', 'pie-forms' ),
			)
		);
	}

	/**
	 * Get array of available languages
	 *
	 * @return array
	 */
	function pforms_payment_currencies(){
		$payment_currencies	= array(
			'USD'  => esc_html__( 'US dollar ($)', 'pie-forms' ),
			'EUR'  => esc_html__( 'Euro (€)', 'pie-forms' ),
			'GBP'  => esc_html__( 'British Pounds (£)', 'pie-forms' ),
			'CAD'  => esc_html__( 'Canadian dollars (Can$)', 'pie-forms' ),
		);
		return (array) apply_filters( 'pforms_payment_currencies_filter', $payment_currencies );
	}

	/**
     * Get supported currencies.
     *
     * @return array
     */
    function pform_get_currencies() {

        $currencies = array(
            'USD' => array(
                'name'                => esc_html__( 'U.S. Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'GBP' => array(
                'name'                => esc_html__( 'Pound Sterling', 'pie-forms' ),
                'symbol'              => '&pound;',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'EUR' => array(
                'name'                => esc_html__( 'Euro', 'pie-forms' ),
                'symbol'              => '&euro;',
                'symbol_pos'          => 'right',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'AUD' => array(
                'name'                => esc_html__( 'Australian Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'BRL' => array(
                'name'                => esc_html__( 'Brazilian Real', 'pie-forms' ),
                'symbol'              => 'R$',
                'symbol_pos'          => 'left',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'CAD' => array(
                'name'                => esc_html__( 'Canadian Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'CZK' => array(
                'name'                => esc_html__( 'Czech Koruna', 'pie-forms' ),
                'symbol'              => '&#75;&#269;',
                'symbol_pos'          => 'right',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'DKK' => array(
                'name'                => esc_html__( 'Danish Krone', 'pie-forms' ),
                'symbol'              => 'kr.',
                'symbol_pos'          => 'right',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'HKD' => array(
                'name'                => esc_html__( 'Hong Kong Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'HUF' => array(
                'name'                => esc_html__( 'Hungarian Forint', 'pie-forms' ),
                'symbol'              => 'Ft',
                'symbol_pos'          => 'right',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'ILS' => array(
                'name'                => esc_html__( 'Israeli New Sheqel', 'pie-forms' ),
                'symbol'              => '&#8362;',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'MYR' => array(
                'name'                => esc_html__( 'Malaysian Ringgit', 'pie-forms' ),
                'symbol'              => '&#82;&#77;',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'MXN' => array(
                'name'                => esc_html__( 'Mexican Peso', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'NOK' => array(
                'name'                => esc_html__( 'Norwegian Krone', 'pie-forms' ),
                'symbol'              => 'Kr',
                'symbol_pos'          => 'left',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'NZD' => array(
                'name'                => esc_html__( 'New Zealand Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'PHP' => array(
                'name'                => esc_html__( 'Philippine Peso', 'pie-forms' ),
                'symbol'              => 'Php',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'PLN' => array(
                'name'                => esc_html__( 'Polish Zloty', 'pie-forms' ),
                'symbol'              => '&#122;&#322;',
                'symbol_pos'          => 'left',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'RUB' => array(
                'name'                => esc_html__( 'Russian Ruble', 'pie-forms' ),
                'symbol'              => 'pyб',
                'symbol_pos'          => 'right',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'SGD' => array(
                'name'                => esc_html__( 'Singapore Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'ZAR' => array(
                'name'                => esc_html__( 'South African Rand', 'pie-forms' ),
                'symbol'              => 'R',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'SEK' => array(
                'name'                => esc_html__( 'Swedish Krona', 'pie-forms' ),
                'symbol'              => 'Kr',
                'symbol_pos'          => 'right',
                'thousands_separator' => '.',
                'decimal_separator'   => ',',
                'decimals'            => 2,
            ),
            'CHF' => array(
                'name'                => esc_html__( 'Swiss Franc', 'pie-forms' ),
                'symbol'              => 'CHF',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'TWD' => array(
                'name'                => esc_html__( 'Taiwan New Dollar', 'pie-forms' ),
                'symbol'              => '$',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
            'THB' => array(
                'name'                => esc_html__( 'Thai Baht', 'pie-forms' ),
                'symbol'              => '฿',
                'symbol_pos'          => 'left',
                'thousands_separator' => ',',
                'decimal_separator'   => '.',
                'decimals'            => 2,
            ),
        );

        return apply_filters( 'pforms_payment_currencies_filter', $currencies );
    }
	/**
     * Get payment total amount from entry.
     *
     * @since 1.0.0
     *
     * @param array $fields
     *
     * @return float
     */
    function pforms_get_total_payment( $fields ) {

        $fields = $this->pforms_get_payment_items( $fields );
        $total  = 0;

        if ( empty( $fields ) ) {
            return false;
        }
        foreach ( $fields as $field ) {
	
            if ( ! empty( $field['amount'] ) ) {
                $amount = $this->pform_sanitize_amount( $field['amount'] );
                $total  = $total + $amount;
            }
        }

        return $this->pform_sanitize_amount( $total );
    }

    /**
     * Get payment fields in an entry.
     *
     * @since 1.0.0
     *
     * @param array $fields
     *
     * @return array|bool False if no fields provided, otherwise array.
     */
    function pforms_get_payment_items( $fields = array() ) {
		if ( empty( $fields ) ) {
			return false;
        }
		
        $payment_fields = $this->pform_payment_fields();
		
        foreach ( $fields as $id => $field ) {

            if (
                empty( $field['type'] ) ||
                ! in_array( $field['type'], $payment_fields, true ) ||
                empty( $field['amount'] ) ||
                empty( (float) $field['amount'] )
            ) {
                // Remove all non-payment fields as well as payment fields with no amount.
                unset( $fields[ $id ] );
            }
        }
	
        return $fields;
    }

	/**
     * Return recognized payment field types.
     *
     * @return array
     */
    function pform_payment_fields() {

        $fields = array( 'payment-single', 'payment-multiple' );

        return apply_filters( 'pform_payment_fields', $fields );
    }

 	/**
     * Get payment fields in an entry.
     *
     * @param array $fields
     *
     * @return array|bool False if no fields provided, otherwise array.
     */
    function pform_get_payment_items( $fields = array() ) {

        if ( empty( $fields ) ) {
            return false;
        }

        $payment_fields = $this->pform_payment_fields();
        
        foreach ( $fields as $id => $field ) {
            if (
                empty( $field['type'] ) ||
                ! in_array( $field['type'], $payment_fields, true ) ||
                empty( $field['amount'] ) ||
                $field['amount'] == $this->pform_sanitize_amount( '0', $field['currency'] )
            ) {
                // Remove all non-payment fields as well as payment fields with no amount.
                unset( $fields[ $id ] );
            }
        }

        return $fields;
    }
	/**
     * Return a nicely formatted amount.
     *
     * @param string $amount
     * @param bool   $symbol
     * @param string $currency
     *
     * @return string $amount Newly formatted amount or Price Not Available
     */
    function pform_format_amount( $amount, $symbol = false, $currency = '' ) {

        if ( empty( $currency ) ) {
            // get selected currency to work
            // $currency =  $this->pform_get_currencies();
            // $currency = $currency['USD'];
            $currency = 'USD';
        }
        
        $currency      = strtoupper( $currency );
        $currencies    = $this->pform_get_currencies();
        $thousands_sep = $currencies[ $currency ]['thousands_separator'];
        $decimal_sep   = $currencies[ $currency ]['decimal_separator'];

        // Format the amount.
        if ( $decimal_sep === ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
            $whole  = substr( $amount, 0, $sep_found );
            $part   = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
            $amount = $whole . '.' . $part;
        }

        // Strip , from the amount (if set as the thousands separator).
        if ( $thousands_sep === ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
            $amount = floatval( str_replace( ',', '', $amount ) );
        }

        if ( empty( $amount ) ) {
            $amount = 0;
        }

        $decimals = apply_filters( 'pform_sanitize_amount_decimals', 2, $amount );
        $number   = number_format( (float) $amount, $decimals, '.', $thousands_sep );

        if ( $symbol ) {
            $symbol_padding = apply_filters( 'pform_currency_symbol_padding', ' ' );
            if ( 'right' === $currencies[ $currency ]['symbol_pos'] ) {
                $number = $number . $symbol_padding . $currencies[ $currency ]['symbol'];
            } else {
                $number = $currencies[ $currency ]['symbol'] . $symbol_padding . $number;
            }
        }

        return $number;
    }

	/**
     * Sanitize Amount.
     *
     * Return a sanitized amount by stripping out thousands separators.
     * @link https://github.com/easydigitaldownloads/easy-digital-downloads/blob/master/includes/formatting.php#L24
     *
     * @param string $amount
     * @param string $currency
     *
     * @return string $amount
     */
    function pform_sanitize_amount( $amount, $currency = '' ) {

		if ( empty( $currency ) ) {
			$currency = 'USD';
		}
		$currency      = strtoupper( $currency );
		$currencies    = $this->pform_get_currencies();
		$thousands_sep = $currencies[ $currency ]['thousands_separator'];
		$decimal_sep   = $currencies[ $currency ]['decimal_separator'];
		$is_negative   = false;
		
		// Sanitize the amount.
		if ( $decimal_sep === ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
			if ( ( $thousands_sep === '.' || $thousands_sep === ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
				$amount = str_replace( $thousands_sep, '', $amount );
			} elseif ( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
				$amount = str_replace( '.', '', $amount );
			}
			$amount = str_replace( $decimal_sep, '.', $amount );
		} elseif ( $thousands_sep === ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		}		

		if ( is_numeric($amount) && $amount < 0 ) {
			$is_negative = true;
		}
	
		$amount   = preg_replace( '/[^0-9\.]/', '', $amount );
		$decimals = apply_filters( 'pform_sanitize_amount_decimals', 2, $amount );
		$amount   = number_format( (double) $amount, $decimals, '.', '' );
		if ( $is_negative ) {
			$amount *= - 1;
		}
		return $amount;
	}

	/**
	 * Format the payment price
	 *
	 * @param [float] $price
	 * @param [string] $cur
	 * @return string
	 */
	function pforms_display_amount($price, $cur){
		$symbol = "$";
		if( $cur == 'EUR' ):
			$symbol = "&euro;";		
		elseif($cur == 'GBP'):
			$symbol = "&pound;";		
		elseif($cur == 'CAD'):
			$symbol = "Can$";				
		endif;
			$formatted_price	= '<span class="currency">'.$symbol."</span><span class='price'>".number_format($price, 2).'</span>';
		return apply_filters('pforms_display_amount_filter', $formatted_price);
	}


	public function field_unique_key( $form_id ) {
		
		if ( empty( $form_id ) ) {
			return false;
		}
		

		$form_id++;

		$field_id = $this->pform_get_random_string() . '-' . $form_id;

		return $field_id;
	}

	/**
		* Generate random string.
	*/
	function pform_get_random_string( $length = 10 ) {
		$string         = '';
		$code_alphabet  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$code_alphabet .= 'abcdefghijklmnopqrstuvwxyz';
		$code_alphabet .= '0123456789';
		$max            = strlen( $code_alphabet );
		for ( $i = 0; $i < $length; $i ++ ) {
			$string .= $code_alphabet[ $this->pform_crypto_rand_secure( 0, $max - 1 ) ];
		}

		return $string;
	}

	/**
	 * Crypto rand secure.
	 */
	function pform_crypto_rand_secure( $min, $max ) {
		$range = $max - $min;
		if ( $range < 1 ) {
			return $min;
		} // not so random...
		$log    = ceil( log( $range, 2 ) );
		$bytes  = (int) ( $log / 8 ) + 1; // Length in bytes.
		$bits   = (int) $log + 1; // Length in bits.
		$filter = (int) ( 1 << $bits ) - 1; // Set all lower bits to 1.
		do {
			$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
			$rnd = $rnd & $filter; // Discard irrelevant bits.
		} while ( $rnd > $range );

		return $min + $rnd;
	}

	/**
	 * Performs json_decode and unslash.

	 */
	public function pform_decode( $data ) {
		if ( ! $data || empty( $data ) ) {
			return false;
		}

		return wp_unslash(json_decode( $data, true ));
	}

	/**
	 * Performs json_encode and wp_slash.
	 */
	function pform_encode( $data = false ) {
		if ( empty( $data ) ) {
			return false;
		}

		return wp_slash( wp_json_encode( $data ) );
	}

	//GET FORM FIELD DATA ONLY
    public function get_form_fields($form_id){
		
		$form_data = Pie_Forms()->form()->get( absint( $form_id ));
        $data = array_shift($form_data);
        $data_unslesh = isset($data->form_data) ? $this->pform_decode( wp_unslash($data->form_data) ) : '';
        
        return $data_unslesh;
    }

	function pform_flatten_array( $value = array() ) {
		$return = array();
		array_walk_recursive( $value, function( $a ) use ( &$return ) { $return[] = $a; } ); // @codingStandardsIgnoreLine.
		return $return;
	}

	function pie_forms_panel_field( $option, $panel, $field, $form_data, $label, $args = array(), $echo = true ) {
		// Required params.
		if ( empty( $option ) || empty( $panel ) || empty( $field ) ) {
			return '';
		}

		// Setup basic vars.
		$panel       = esc_attr( $panel );
		$field       = esc_attr( $field );
		$panel_id    = $this->sanitize_html_class( $panel );
		$parent      = ! empty( $args['parent'] ) ? esc_attr( $args['parent'] ) : '';
		$label       = ! empty( $label ) ? $label : '';
		$class       = ! empty( $args['class'] ) ? esc_attr( $args['class'] ) : '';
		$input_class = ! empty( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : '';
		$default     = isset( $args['default'] ) ? $args['default'] : '';
		$tinymce     = isset( $args['tinymce'] ) ? $args['tinymce'] : '';
		$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
		$data_attr   = '';
		$output      = '';

		// Check if we should store values in a parent array.
	if ( ! empty( $parent ) ) {
		$field_name = sprintf( '%s[%s][%s]', $parent, $panel, $field );
		$value      = ( isset( $form_data[ $parent ][ $panel ][ $field ] ) && !empty( $form_data[ $parent ][ $panel ][ $field ] ) ) ? $form_data[ $parent ][ $panel ][ $field ] : $default;
	}else{
		$field_name = sprintf( '%s[%s]', $panel, $field );
		$value      = ( isset( $form_data[ $panel ][ $field ] ) && !empty( $form_data[ $panel ][ $field ] ) ) ? 
		$form_data[ $panel ][ $field ] : $default;
	} 
		// Check for data attributes.
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $val ) {
				if ( is_array( $val ) ) {
					$val = wp_json_encode( $val );
				}
				$data_attr .= ' data-' . $key . '=\'' . $val . '\'';
			}
		}

		$case = '';
		$field_case = apply_filters( 'pie_forms_builder_settings_fields_case' , $case );

		// Determine what field type to output.
		switch ( $option ) {

			// Text input.
			case 'text':
				$type   = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';
				$output = sprintf(
					'<input type="%s" id="pie-forms-panel-field-%s-%s" name="%s" value="%s" placeholder="%s" class="widefat %s" %s>',
					$type,
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					esc_attr( $value ),
					$placeholder,
					$input_class,
					$data_attr
				);
				break;
				case 'imagepicker':

					$value = isset($value["image"]) ? $value["image"] : $value;
					$output .= '<div class="pie-forms-attachment-media-view">';
						$output .= sprintf( '<input type="hidden" class="source" name="%s[image]" value="%s">', $field_name,  esc_url_raw($value)  );
						$output .= sprintf( '<button type="button" class="upload-button button-add-media"%s>%s</button>', ! empty( $value ) ? ' style="display:none;"' : '', esc_html__( 'Upload Image', 'pie-forms' ) );
						$output .= '<div class="thumbnail thumbnail-image">';
						if ( ! empty( $value ) ) {
							$output .= sprintf( '<img class="attachment-thumb" src="%1$s">', esc_url_raw( $value ) );
						}
						$output .= '</div>';			
					$output .= sprintf( '<div class="actions"%s>', empty( $value ) ? ' style="display:none;"' : '' );
					$output .= sprintf( '<button type="button" class="button remove-button">%1$s</button>', esc_html__( 'Remove', 'pie-forms' ) );
					$output .= sprintf( '<button type="button" class="button upload-button">%1$s</button>', esc_html__( 'Change image', 'pie-forms' ) );
					$output .= '</div>';
					$output .= '</div>';
					break;

									
			// Number input.
			case 'number':
				$type   = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'number';
				$output = sprintf(
					'<input type="%s" id="pie-forms-panel-field-%s-%s" min="1" name="%s" value="%s" placeholder="%s" class="widefat %s" %s>',
					$type,
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					esc_attr( $value ),
					$placeholder,
					$input_class,
					$data_attr
				);
				break;
			// Textarea.
			case 'textarea':
				$rows   = ! empty( $args['rows'] ) ? (int) $args['rows'] : '3';
				$output = sprintf(
					'<textarea id="pie-forms-panel-field-%s-%s" name="%s" rows="%d" placeholder="%s" class="widefat %s" %s>%s</textarea>',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					$rows,
					$placeholder,
					$input_class,
					$data_attr,
					esc_textarea( $value )
				);
				break;
			// Icon Picker.
			case 'iconpicker':
				$output = sprintf(
					'<button type="button" id="pie-forms-select-icon-%s-%s" data-iconpicker-input="#pie-forms-panel-field-%s-%s" data-iconpicker-preview="#pie-forms-iconpreview-%s-%s" class="select-icon-button">Select Icon</button>',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field )
				);
				$output .= sprintf( '<div class="icon-preview" data-toggle="tooltip" title="" data-original-title="Preview of selected Icon">
					<span id="pie-forms-iconpreview-%s-%s" class="%s"></span>
					</div>',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					esc_attr( $value )
				);
				$output .= sprintf( '<input type="hidden" id="pie-forms-panel-field-%s-%s" name="%s" value="%s">',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					esc_attr($value)
				);
				break; 	
			// TinyMCE.
			case 'tinymce':
				$arguments                  = wp_parse_args(
					$tinymce,
					array(
						'media_buttons' => false,
						'tinymce'       => false,
					)
				);
				$arguments['textarea_name'] = $field_name;
				$arguments['teeny']         = true;
				$id                         = 'pie-forms-panel-field-' . $this->sanitize_html_class( $panel_id ) . '-' . $this->sanitize_html_class( $field );
				$id                         = str_replace( '-', '_', $id );
				ob_start();
				wp_editor( $value, $id, $arguments );
				$output = ob_get_clean();
				break;

			// Checkbox.
			case 'checkbox':
				$checked   = checked( '1', $value, false );
				$checkbox  = sprintf(
					'<input type="hidden" name="%s" value="0" class="widefat %s" %s %s>',
					$field_name,
					$input_class,
					$checked,
					$data_attr
				);
				$checkbox .= sprintf(
					'<input type="checkbox" id="pie-forms-panel-field-%s-%s" name="%s" value="1" class="%s" %s %s>',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					$input_class,
					$checked,
					$data_attr
				);
				$output    = sprintf(
					'<label for="pie-forms-panel-field-%s-%s" class="inline">%s',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$checkbox . $label
				);
				if ( ! empty( $args['tooltip'] ) ) {
					$output .= sprintf( ' <i class="dashicons dashicons-editor-help pie-forms-help-tooltip"><span class="tooltip-hover">%s</span></i>', esc_attr( $args['tooltip'] ) );
				}
				$output .= '</label>';
				break;

			// Radio.
			case 'radio':
				$options = $args['options'];
				$x       = 1;
				$output  = '';
				foreach ( $options as $key => $item ) {
					if ( empty( $item['label'] ) ) {
						continue;
					}
					$checked = checked( $key, $value, false );
					$output .= sprintf(
						'<span class="row"><input type="radio" id="pie-forms-panel-field-%s-%s-%d" name="%s" value="%s" class="widefat %s" %s %s>',
						$this->sanitize_html_class( $panel_id ),
						$this->sanitize_html_class( $field ),
						$x,
						$field_name,
						$key,
						$input_class,
						$checked,
						$data_attr
					);
					$output .= sprintf(
						'<label for="pie-forms-panel-field-%s-%s-%d" class="inline">%s',
						$this->sanitize_html_class( $panel_id ),
						$this->sanitize_html_class( $field ),
						$x,
						$item['label']
					);
					if ( ! empty( $item['tooltip'] ) ) {
						$output .= sprintf( ' <i class="dashicons dashicons-editor-help pie-forms-help-tooltip"><span class="tooltip-hover">%s</span></i>', esc_attr( $item['tooltip'] ) );
					}
					$output .= '</label></span>';
					$x ++;
				}
				break;

			// Select.
			case 'select':
				if ( empty( $args['options'] ) && empty( $args['field_map'] ) ) {
					return '';
				}

				if ( ! empty( $args['field_map'] ) ) {
					$options          = array();
					$available_fields = pf_get_form_fields( $form_data, $args['field_map'] );
					if ( ! empty( $available_fields ) ) {
						foreach ( $available_fields as $id => $available_field ) {
							$lbl            = ! empty( $available_field['label'] ) ? esc_attr( $available_field['label'] ) : esc_html__( 'Field #', 'pie-forms' ) . $id;
							$options[ $id ] = $lbl;
						}
					}
					$input_class .= ' pie-forms-field-map-select';
					$data_attr   .= ' data-field-map-allowed="' . implode( ' ', $args['field_map'] ) . '"';
					if ( ! empty( $placeholder ) ) {
						$data_attr .= ' data-field-map-placeholder="' . esc_attr( $placeholder ) . '"';
					}
				} else {
					$options = $args['options'];
				}

				$output = sprintf(
					'<select id="pie-forms-panel-field-%s-%s" name="%s" class="widefat %s" %s>',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					$input_class,
					$data_attr
				);

				if ( ! empty( $placeholder ) ) {
					$output .= '<option value="">' . $placeholder . '</option>';
				}

				foreach ( $options as $key => $item ) {
					if( $field == 'currency' && $parent == 'pf_payments' ){
						$item = $item['name'] . ' (' . $item['symbol'] . ')';
					}
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), $item );
				}

				$output .= '</select>';
				break;

			//Multi Select.
			case 'multiselect':
				if ( empty( $args['options'] ) && empty( $args['field_map'] ) ) {
					return '';
				}

				$options = $args['options'];
			

				$output = sprintf(
					'<select id="pie-forms-panel-field-%s-%s" name="%s[]" class="widefat %s" %s multiple >',
					$this->sanitize_html_class( $panel_id ),
					$this->sanitize_html_class( $field ),
					$field_name,
					$input_class,
					$data_attr
				);

			  	if ( ! empty( $placeholder ) ) {
					$output .= '<option value="">' . $placeholder . '</option>';
				}
				foreach ( $options as $key => $item ) {
					
					$_selected = !empty($value) && in_array($key,$value) ? 'selected' : '';
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $_selected , $item );
				}

				$output .= '</select>';
			break;

				
			case $field_case:
				$output = apply_filters( "pie_forms_builder_settings_fields_{$field_case}", $panel_id, $field, $field_name, $placeholder, $input_class, $data_attr, $value );
				break;
		}

		$smarttags_class = ! empty( $args['smarttags'] ) ? 'pf_smart_tag' : '';

		// Put the pieces together....
		$field_open  = sprintf(
			'<div id="pie-forms-panel-field-%s-%s-wrap" class="pie-forms-panel-field %s %s %s">',
			$this->sanitize_html_class( $panel_id ),
			$this->sanitize_html_class( $field ),
			$class,
			$smarttags_class,
			'pie-forms-panel-field-' . $this->sanitize_html_class( $option )
		);
		$field_open .= ! empty( $args['before'] ) ? $args['before'] : '';
		if ( ! in_array( $option, array( 'checkbox' ), true ) && ! empty( $label ) ) {
			$field_label = sprintf(
				'<label for="pie-forms-panel-field-%s-%s">%s',
				$this->sanitize_html_class( $panel_id ),
				$this->sanitize_html_class( $field ),
				$label
			);
			if ( ! empty( $args['tooltip'] ) ) {
				$field_label .= sprintf( ' <i class="dashicons dashicons-editor-help pie-forms-help-tooltip"><span class="tooltip-hover">%s</span></i>', esc_attr( $args['tooltip'] ) );
			}
			if ( ! empty( $args['after_tooltip'] ) ) {
				$field_label .= $args['after_tooltip'];
			}
			if ( ! empty( $args['smarttags'] ) ) {
				$smart_tag = '';

				$type        = ! empty( $args['smarttags']['type'] ) ? esc_attr( $args['smarttags']['type'] ) : 'form_fields';
				$form_fields = ! empty( $args['smarttags']['form_fields'] ) ? esc_attr( $args['smarttags']['form_fields'] ) : '';
				$smart_tag .= '<a href="#" class="pf-toggle-smart-tag-display" data-type="' . $type . '" data-fields="' . $form_fields . '"><span class="dashicons dashicons-shortcode"></span></a>';

				$smart_tag .= '<div class="pf-smart-tag-lists ScrollBar " style="display: none">';
				$smart_tag .= '<div class="smart-tag-title">';
				$smart_tag .= esc_html__( 'Available Fields', 'pie-forms' );
				$smart_tag .= '</div><ul class="pf-fields"></ul>';
				if ( 'all' === $type || 'other' === $type ) {
					$smart_tag .= '<div class="smart-tag-title other-tag-title">';
					$smart_tag .= esc_html__( 'Others', 'pie-forms' );
					$smart_tag .= '</div><ul class="pf-others"></ul>';
				}
				$smart_tag .= '</div>';
			} else {
				$smart_tag = '';
			}

			$field_label .= '</label>';
		} else {
			$field_label = '';
			$smart_tag   = '';
		}
		$field_close  		 = ! empty( $args['after'] ) ? $args['after'] : '';
		$field_close 		.= '</div>';
		$smart_tag_output    = '<div class="pie-form-smart-tag-parent">'.$output . $smart_tag.'</div>';
		$output 			 = $field_open . $field_label . $smart_tag_output  . $field_close;

		if ( $echo ) {
			echo wp_kses( $output, Pie_Forms()->core()->pform_get_allowed_html_tags( 'builder' ) );

		} else {
			return $output;
		}
	}


	/**
	 * Pieforms KSES.
	 *
	 * @param string $context Context.
	 */
	function pform_get_allowed_html_tags( $context = '' ) {
		$post_tags = wp_kses_allowed_html( 'post' );
		if ( 'builder' === $context ) {
			$builder_tags = get_transient( 'pf-builder-tags-list' );
			
			if ( ! empty( $builder_tags ) ) {
				return $builder_tags;
			}		

			$template_data 	= Pie_forms::$dir.'/assets/allowed_tags/allowed_tags.json';
			$handle 		= fopen($template_data, "r");
			$raw_templates 	= fread($handle, filesize($template_data));
			$allowed_tags   = json_decode(  $raw_templates , true);

			if ( ! empty( $allowed_tags ) ) {
				foreach ( $allowed_tags as $tag => $args ) {
					if ( array_key_exists( $tag, $post_tags ) ) {
						foreach ( $args as $arg => $value ) {
							if ( ! array_key_exists( $arg, $post_tags[ $tag ] ) ) {
								$post_tags[ $tag ][ $arg ] = true;
							}
						}
					} else {
						$post_tags[ $tag ] = $args;
					}
				}
				set_transient( 'pf-builder-tags-list', $post_tags, DAY_IN_SECONDS );
			}

			return $post_tags;
		}

		return wp_parse_args(
			$post_tags,
			array(
				'input'    => array(
					'type'  => true,
					'name'  => true,
					'value' => true,
				),
				'select'   => array(
					'name' => true,
					'id'   => true,
				),
				'option'   => array(
					'value'    => true,
					'selected' => true,
				),
				'textarea' => array(
					'style' => true,
				),
			)
		);
	}
	function sanitize_html_class( $class, $fallback = '' ) {
		// Strip out any %-encoded octets.
		$sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $class );

		// Limit to A-Z, a-z, 0-9, '_', '-'.
		$sanitized = preg_replace( '/[^A-Za-z0-9_-]/', '', $sanitized );

		if ( '' == $sanitized && $fallback ) {
			return sanitize_html_class( $fallback );
		}
		/**
		 * Filters a sanitized HTML class string.
		 */
		return apply_filters( 'sanitize_html_class', $sanitized, $class, $fallback );
	}

	function pie_html_attributes( $id = '', $class = array(), $datas = array(), $atts = array(), $echo = false ) {
		$id    = trim( $id );
		$parts = array();

		if ( ! empty( $id ) ) {
			$id = sanitize_html_class( $id );
			if ( ! empty( $id ) ) {
				$parts[] = 'id="' . $id . '"';
			}
		}

		if ( ! empty( $class ) ) {
			$class = $this->pform_sanitize_classes( $class, true );
			if ( ! empty( $class ) ) {
				$parts[] = 'class="' . $class . '"';
			}
		}

		if ( ! empty( $datas ) ) {
			foreach ( $datas as $data => $val ) {
				$parts[] = 'data-' . sanitize_html_class( $data ) . '="' . esc_attr( $val ) . '"';
			}
		}

		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $val ) {
				if ( '0' === $val || ! empty( $val ) ) {
					$parts[] = sanitize_html_class( $att ) . '="' . esc_attr( $val ) . '"';
				}
			}
		}

		$output = implode( ' ', $parts );

		if ( $echo ) {
			echo wp_kses(trim( $output ), Pie_Forms()->core()->pform_get_allowed_tags()); 
		} else {
			return trim( $output );
		}
	}

	
/**
 * Array combine.
 */
function pform_sanitize_array_combine( $array ) {
	if ( empty( $array ) || ! is_array( $array ) ) {
		return $array;
	}

	return array_map( 'sanitize_text_field', $array );
}

	function pform_sanitize_classes( $classes, $convert = false ) {
		$css   = array();
		$array = is_array( $classes );

		if ( ! empty( $classes ) ) {
			if ( ! $array ) {
				$classes = explode( ' ', trim( $classes ) );
			}
			foreach ( $classes as $class ) {
				$css[] = sanitize_html_class( $class );
			}
		}

		if ( $array ) {
			return $convert ? implode( ' ', $css ) : $css;
		} else {
			return $convert ? $css : implode( ' ', $css );
		}
	}

	function pie_string_translation( $form_id, $field_id, $value, $suffix = '' ) {
		$context = isset( $form_id ) ? 'pie_forms_' . absint( $form_id ) : 0;
		$name    = isset( $field_id ) ? $field_id : '';

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( $context, $name, $value );
		}

		if ( function_exists( 'icl_t' ) ) {
			$value = icl_t( $context, $name, $value );
		}

		return $value;
	}

	/**
	 * Get random meta-key for field option.
	 */
	function pform_get_meta_key_field_option( $field ) {
		$random_number = rand( pow( 10, 3 ), pow( 10, 4 ) - 1 ); 
		return strtolower( str_replace( array( ' ', '/_' ), array( '_', '' ), $field['label'] ) ) . '_' . $random_number;
	}

	/**
	 * Checks if field exists within the form.
	 */
	function pie_is_field_exists( $form_id, $field ) {
				
		$form_obj  		= Pie_Forms()->form()->get( $form_id );

		$form_shift     = array_shift($form_obj);
		$form_data = ! empty( $form_shift->form_data ) ? $this->pform_decode( wp_unslash($form_shift->form_data) ) : '';
		
		if ( ! empty( $form_data['form_fields'] ) ) {
			foreach ( $form_data['form_fields'] as $form_field ) {
				if ( $field === $form_field['type'] ) {
					return true;
				}
			}
		}

		return false;
	}

	function pform_decode_string( $string ) {
		if ( ! is_string( $string ) ) {
			return $string;
		}
	
		return wp_kses_decode_entities( html_entity_decode( $string, ENT_QUOTES ) );
	}

	function pform_sanitize_textarea_field( $string ) {
		if ( empty( $string ) || ! is_string( $string ) ) {
			return $string;
		}
	
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$string = sanitize_textarea_field( $string );
		} else {
			$string = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $string ) ) );
		}
	
		return $string;
	}

	//EMAIL TEMPLATE INCLUDE
	function pform_get_template( $template_name) {
		
		$template = $this->pform_locate_template($template_name);
	
		$action_args = array(
			'template_name' => $template_name,
			'located'       => $template,
			
		);
	
		include $action_args['located'];
			
	}

	function pform_locate_template( $template_name ) {
		$template_path = Pie_Forms::$dir.'includes/templates/'.$template_name;
		return  $template_path;
	}

	//TEMPLATE JSON
	function templateJson(){
		//$templatejson = "https://raw.githubusercontent.com/haidersayani/Pie-Form/master/Template-All/Form.json";
		$templatejson = Pie_Forms::$dir.'includes/templates/forms.json';
		return $templatejson;
	}

	/**
	 * Get the required label text, with a filter.
	 */
	function pform_get_required_label() {
		return apply_filters( 'pie_forms_required_label', esc_html__( 'This field is required.', 'pie-forms' ) );
	}

	/**
	 * Define Regex.
	 */	
	function get_define_regex($value,$key){
		$regex = '';
		switch ( $key ) {
			case 'alpha_only':
				$regex = "^[a-zA-Z ]*$";
				break;
			case 'phone':
				$regex = "^(?=.*[0-9])[- +()0-9]+$";
				break;
		}

		if ( !preg_match('/'.$regex.'/', $value) )
			{
				return true;
				
			}
	}

	/**
	 * Get all forms.
	 */
	function pform_get_all_forms( $skip_disabled_entries = false ) {
		$forms    = array();
		
		$form_ids = Pie_Forms()->form()->get_result();
		if(!empty($form_ids)){
			
			foreach ($form_ids as $key => $value) {
				$form_id 		= $value->id;	
				$status 		= $value->post_status;
				$form_data 		= $this->get_form_fields($form_id);
		
				if ( ( isset( $form_data['form_enabled'] ) && '1' != $form_data['form_enabled']) ){
					continue;
				}
				if(isset( $status ) && 'published' !== $status ){
					continue;
				}
				$forms[ $form_id ] = $form_data['settings']['form_title'];
				
				
			}

		}
		
		return $forms;
	}

	public function get_pro_form_field_types() {
		$_available_fields = array();
		$this_field = pie_forms()->form_fields();
		if ( count( $this_field ) > 0 ) {
			foreach ( array_values( $this_field ) as $form_field ) {
				foreach ( $form_field as $field ) {
					if ( $field->is_pro ) {
						$_available_fields[] = $field->type;
					}
				}
			}
		}

		return $_available_fields;
	}
	/**
	 * Addons Plugin Data
	 */
	public function get_addons_plugin_data( $plugin, $details, $all_plugins ) {

		if ( array_key_exists( $plugin, $all_plugins ) ) {
			if ( is_plugin_active( $plugin ) ) {
				// Status text/status.
				$plugin_data['status_class'] = 'status-active';
				$plugin_data['status_text']  = esc_html__( 'Active', 'pie-forms' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary disabled';
				$plugin_data['action_text']  = esc_html__( 'Deactivate', 'pie-forms' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			} else {
				// Status text/status.
				$plugin_data['status_class'] = 'status-inactive';
				$plugin_data['status_text']  = esc_html__( 'In-Active', 'pie-forms' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary';
				$plugin_data['action_text']  = esc_html__( 'Activate', 'pie-forms' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			}
		} 
		else {
			$plugin_data = [];
			
			// Doesn't exist, install.
			// Status text/status.
			$plugin_data['status_class'] = 'status-download';
			$plugin_data['status_text'] = esc_html__( '', 'pie-forms' );
			// Button text/status.
			$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-primary';
			$plugin_data['action_text']  = esc_html__( 'Learn More', 'pie-forms' );
		}
	
		$plugin_data['details'] = $details;
	
		return $plugin_data;
	}
	/**
	 * About Us Plugin Data
	 */
	public function get_about_plugin_data( $plugin, $details, $all_plugins ) {

		if ( array_key_exists( $plugin, $all_plugins ) ) {
			if ( is_plugin_active( $plugin ) ) {
				// Status text/status.
				$plugin_data['status_class'] = 'status-active';
				$plugin_data['status_text']  = esc_html__( 'Active', 'pie-forms' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary disabled';
				$plugin_data['action_text']  = esc_html__( 'Activated', 'pie-forms' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			} else {
				// Status text/status.
				$plugin_data['status_class'] = 'status-inactive';
				$plugin_data['status_text']  = esc_html__( 'Inactive', 'pie-forms' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary';
				$plugin_data['action_text']  = esc_html__( 'Activate', 'pie-forms' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			}
		} else {
			// Doesn't exist, install.
			// Status text/status.
			$plugin_data['status_class'] = 'status-download';
			if ( isset( $details['act'] ) && 'go-to-url' === $details['act'] ) {
				$plugin_data['status_class'] = 'status-go-to-url';
			}
			$plugin_data['status_text'] = esc_html__( 'Not Installed', 'pie-forms' );
			// Button text/status.
			$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-primary';
			$plugin_data['action_text']  = esc_html__( 'Install Plugin', 'pie-forms' );
			$plugin_data['plugin_src']   = esc_url( $details['url'] );
		}
	
		$plugin_data['details'] = $details;
	
		return $plugin_data;
	}

	/**
	 * Return URL to form preview page.
	 *
	 * @return string
	 */
	function pieforms_get_form_preview_url( $form_id ) {

		$url = add_query_arg(
			array(
				'form_id'     		=> absint( $form_id ),
				'pf_preview'  		=> 'true',
			),
			home_url()
		);

		return $url;
	}
		/**
	 * Get Recaptcha languages.
	 *
	 * @return array
	 */
	function pform_captcha_languages() {
		$captcha_languages = array(
			'ar' => esc_html__( 'Arabic', 'pie-forms' ),
			'aF' => esc_html__( 'Afrikaans', 'pie-forms' ),
			'am' => esc_html__( 'Amharic', 'pie-forms' ),
			'hy' => esc_html__( 'Armenian', 'pie-forms' ),
			'az' => esc_html__( 'Azerbaijani', 'pie-forms' ),
			'eu' => esc_html__( 'Basque', 'pie-forms' ),
			'bn' => esc_html__( 'Bengali', 'pie-forms' ),
			'bg' => esc_html__( 'Bulgarian', 'pie-forms' ),
			'ca' => esc_html__( 'Catalan', 'pie-forms' ),
			'zh-HK' => esc_html__( 'Chinese (Hong Kong)', 'pie-forms' ),
			'zh-CN' => esc_html__( 'Chinese (Simplified)', 'pie-forms' ),
			'zh-TW' => esc_html__( 'Chinese (Traditional)', 'pie-forms' ),
			'hr' => esc_html__( 'Croatian', 'pie-forms' ),
			'cs' => esc_html__( 'Czech', 'pie-forms' ),
			'da' => esc_html__( 'Danish', 'pie-forms' ),
			'nl' => esc_html__( 'Dutch *', 'pie-forms' ),
			'en-GB' => esc_html__( 'English (UK)', 'pie-forms' ),
			'en' => esc_html__( 'English (US) *', 'pie-forms' ),
			'et' => esc_html__( 'Estonian', 'pie-forms' ),
			'fil' => esc_html__( 'Filipino', 'pie-forms' ),
			'fi' => esc_html__( 'Finnish', 'pie-forms' ),
			'fr' => esc_html__( 'French *', 'pie-forms' ),
			'fr-CA' => esc_html__( 'French (Canadian)', 'pie-forms' ),
			'gl' => esc_html__( 'Galician', 'pie-forms' ),
			'ka' => esc_html__( 'Georgian', 'pie-forms' ),
			'de' => esc_html__( 'German *', 'pie-forms' ),
			'de-AT' => esc_html__( 'German (Austria)', 'pie-forms' ),
			'de-CH' => esc_html__( 'German (Switzerland)', 'pie-forms' ),
			'el' => esc_html__( 'Greek', 'pie-forms' ),
			'gu' => esc_html__( 'Gujarati', 'pie-forms' ),
			'iw' => esc_html__( 'Hebrew', 'pie-forms' ),
			'hi' => esc_html__( 'Hindi', 'pie-forms' ),
			'hu' => esc_html__( 'Hungarain', 'pie-forms' ),
			'is' => esc_html__( 'Icelandic', 'pie-forms' ),
			'id' => esc_html__( 'Indonesian', 'pie-forms' ),
			'it' => esc_html__( 'Italian *', 'pie-forms' ),
			'ja' => esc_html__( 'Japanese', 'pie-forms' ),
			'kn' => esc_html__( 'Kannada', 'pie-forms' ),
			'ko' => esc_html__( 'Korean', 'pie-forms' ),
			'lo' => esc_html__( 'Laothian', 'pie-forms' ),
			'lv' => esc_html__( 'Latvian', 'pie-forms' ),
			'it' => esc_html__( 'Lithuanian', 'pie-forms' ),
			'ms' => esc_html__( 'Malay', 'pie-forms' ),
			'ml' => esc_html__( 'Malayalam', 'pie-forms' ),
			'mr' => esc_html__( 'Marathi', 'pie-forms' ),
			'mn' => esc_html__( 'Mongolian', 'pie-forms' ),
			'no' => esc_html__( 'Norwegian', 'pie-forms' ),
			'fa' => esc_html__( 'Persian', 'pie-forms' ),
			'pl' => esc_html__( 'Polish', 'pie-forms' ),
			'pt' => esc_html__( 'Portuguese *', 'pie-forms' ),
			'pt-BR' => esc_html__( 'Portuguese (Brazil)', 'pie-forms' ),
			'pt-PT' => esc_html__( 'Portuguese (Portugal)', 'pie-forms' ),
			'ro' => esc_html__( 'Romanian', 'pie-forms' ),
			'ru' => esc_html__( 'Russian', 'pie-forms' ),
			'sr' => esc_html__( 'Serbian', 'pie-forms' ),
			'si' => esc_html__( 'Sinhalese', 'pie-forms' ),
			'sk' => esc_html__( 'Slovak', 'pie-forms' ),
			'sl' => esc_html__( 'Slovenian', 'pie-forms' ),
			'es' => esc_html__( 'Spanish *', 'pie-forms' ),
			'es-419' => esc_html__( 'Spanish (Latin America)', 'pie-forms' ),
			'sw' => esc_html__( 'Swahili', 'pie-forms' ),
			'sv' => esc_html__( 'Swedish', 'pie-forms' ),
			'ta' => esc_html__( 'Tamil', 'pie-forms' ),
			'te' => esc_html__( 'Telugu', 'pie-forms' ),
			'th' => esc_html__( 'Thai', 'pie-forms' ),
			'tr' => esc_html__( 'Turkish', 'pie-forms' ),
			'uk' => esc_html__( 'Ukrainian', 'pie-forms' ),
			'ur' => esc_html__( 'Urdu', 'pie-forms' ),
			'vi' => esc_html__( 'Vietnamese', 'pie-forms' ),
			'zu' => esc_html__( 'Zulu', 'pie-forms' ),

		);
		return (array) apply_filters( 'pie_forms_captcha_languages', $captcha_languages );
	}

	/**
	 * Sanitize hex color.
	 *
	 * @param string $color
	 *
	 * @return string
	 */
	function pform_sanitize_hex_color( $color ) {
	
		if ( empty( $color ) ) {
			return '';
		}
	
		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
	
		return '';
	}
	/**
	 * Check Addon Exist.
	 *
	 * @return string
	 */
	function pform_check_addon_exist( $addon ) {
		if( !function_exists('is_plugin_active') ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$active = is_plugin_active($addon);
		return $active;
	}

	/**
	 * Check if any payment addon is active
	 *
	 * @return boolean
	 */
	function pforms_check_payment_addon_active(){
		if( !function_exists('is_plugin_active') ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$active = (is_plugin_active('pie-forms-for-wp-paypal-donation/pie-forms-for-wp-paypal-donation.php') );
		return $active;
	}
	
	/**
	 * Return information about pages if the form has multiple pages.
	 *
	 * @param mixed $form
	 *
	 * @return mixed false or an array
	 */
	function pform_get_pagebreak_details( $form = false ) {
		$is_addon_activated = get_option('pieforms_manager_addon_multipart_activated');

		if(!Pie_Forms()->core()->pform_check_addon_exist('pie-forms-for-wp-multipart-forms/pie-forms-for-wp-multipart-forms.php') || $is_addon_activated == "Deactivated"){
			return false;
		}

		$form_data = '';
		$details   = array();
		$pages     = 1;
		
		if ( is_object( $form ) && ! empty( $form->post_content ) ) {
			$form_data = Pie_forms()->core()->pform_decode( $form->post_content );
		} elseif ( is_array( $form ) ) {
			$form_data = $form;
		}
		
		if ( empty( $form_data['form_fields'] ) ) {
			return false;
		}

		foreach ( $form_data['form_fields'] as $field ) {
			if ( 'multipart' === $field['type'] ) {
				if ( empty( $field['position'] ) ) {
					$pages ++;
					$details['total']   = $pages;
					$details['pages'][] = $field;
				} elseif ( 'top' === $field['position'] ) {
					$details['top'] = $field;
				} elseif ( 'bottom' === $field['position'] ) {
					$details['bottom'] = $field;
				}
			}
		}

		if ( ! empty( $details ) ) {
			if ( empty( $details['top'] ) ) {
				$details['top'] = array();
			}
			if ( empty( $details['bottom'] ) ) {
				$details['bottom'] = array();
			}
			$details['current'] = 1;

			return $details;
		}

		return false;
	}

	/**
	 * Create index.html file in the specified directory if it doesn't exist.
	 *
	 * @param string $path Path to the directory.
	 *
	 * @return int|false Number of bytes that were written to the file, or false on failure.
	 */
	function pform_create_index_html_file( $path ) {

		if ( ! is_dir( $path ) || is_link( $path ) ) {
			return false;
		}

		$index_file = wp_normalize_path( trailingslashit( $path ) . 'index.html' );

		// Do nothing if index.html exists in the directory.
		if ( file_exists( $index_file ) ) {
			return false;
		}

		// Create empty index.html.
		return file_put_contents( $index_file, '' );
	}

	/**
	 * Create .htaccess file in the Pie Forms upload directory.
	 *
	 * @return bool True on write success, false on failure.
	 */
	function pform_create_upload_dir_htaccess_file() {

		if ( ! apply_filters( 'pform_create_upload_dir_htaccess_file', true ) ) {
			return false;
		}

		$upload_dir = $this->pform_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			return false;
		}

		$htaccess_file = wp_normalize_path( trailingslashit( $upload_dir['path'] ) . '.htaccess' );

		if ( file_exists( $htaccess_file ) ) {
			@unlink( $htaccess_file ); 
		}

		if ( ! function_exists( 'insert_with_markers' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		$contents = apply_filters(
			'pform_create_upload_dir_htaccess_file_content',
			'# Disable PHP and Python scripts parsing.
			<Files *>
			SetHandler none
			SetHandler default-handler
			RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
			RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
			</Files>
			<IfModule mod_php5.c>
			php_flag engine off
			</IfModule>
			<IfModule mod_php7.c>
			php_flag engine off
			</IfModule>
			<IfModule headers_module>
			Header set X-Robots-Tag "noindex"
			</IfModule>'
		);

		return insert_with_markers( $htaccess_file, 'Pieforms', $contents );
	}

	/**
	 * Get Pieforms upload root path (e.g. /wp-content/uploads/pieforms).
	 *
	 * @return array Pieforms upload root path (no trailing slash).
	 */
	function pform_upload_dir() {

		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			return [ 'error' => $upload_dir['error'] ];
		}

		$pieforms_upload_root = trailingslashit( realpath( $upload_dir['basedir'] ) ) . 'pieforms';

		// Add filter to allow redefine store directory.
		$custom_uploads_root = apply_filters( 'pieforms_upload_root', $pieforms_upload_root );
		if ( wp_is_writable( $custom_uploads_root ) ) {
			$pieforms_upload_root = $custom_uploads_root;
		}

		return [
			'path'  => $pieforms_upload_root,
			'url'   => trailingslashit( $upload_dir['baseurl'] ) . 'pieforms',
			'error' => false,
		];
	}

	/**
	 * Convert a file size provided, such as "2M", to bytes.
	 *
	 * @link http://stackoverflow.com/a/22500394
	 *
	 * @param bool $bytes
	 *
	 * @return mixed
	 */
	function pform_max_upload( $bytes = false ) {

		$max = wp_max_upload_size();
		if ( $bytes ) {
			return $max;
		}

		return size_format( $max );
	}

	/**
	 * Convert a file size provided, such as "2M", to bytes.
	 *
	 * @link http://stackoverflow.com/a/22500394
	 *
	 * @param string $size
	 *
	 * @return int
	 */
	function pform_size_to_bytes( $size ) {

		if ( is_numeric( $size ) ) {
			return $size;
		}

		$suffix = substr( $size, - 1 );
		$value  = substr( $size, 0, - 1 );

		switch ( strtoupper( $suffix ) ) {
			case 'P':
				$value *= 1024;
			case 'T':
				$value *= 1024;
			case 'G':
				$value *= 1024;
			case 'M':
				$value *= 1024;
			case 'K':
				$value *= 1024;
				break;
		}

		return $value;
	}

	/**
	 * Update an existing entry record in the database.
	 *
	 * @param int|string $row_id Row ID for the record being updated.
	 * @param array      $data   Optional. Array of columns and associated data to update. Default empty array.
	 * @param string     $where  Optional. Column to match against in the WHERE clause. If empty, $primary_key
	 *                           will be used. Default empty.
	 * @param string     $type   Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 *
	 * @return bool False if the record could not be updated, true otherwise.
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {
		
		global $wpdb;
		
		// Row ID must be a positive integer.
		$row_id = absint( $row_id );
		
		if ( empty( $row_id ) ) {
			return false;
		}

		do_action( 'pform_pre_update_' . $type, $data );

		$fields_data = [ 'fields' => wp_json_encode($data) ];
		
		if ( false === $wpdb->update( $wpdb->prefix . 'pf_entries', $fields_data, [ 'id' => $row_id ] ) ) {
			return false;
		}

		do_action( 'pform_post_update_' . $type, $data );

		return true;
	}

	/**
	 * Chain monad, useful for chaining certain array or string related functions.
	 *
	 * @param mixed $value Any data.
	 *
	 * @return PFORM_Core_Helper_Chain
	 */
	function pform_chain( $value ) {

		return PFORM_Core_Helper_Chain::of( $value );
	}
	
	/**
	 * Check if PForm Free version is activated
	 *
	 *
	 * @return bool
	 */
	public function pform_version_activated() {
		
		return is_plugin_active('pie-forms-for-wp/pie-forms-for-wp.php');
	}

	/**
	 * Get the list of allowed tags, used in pair with wp_kses() function.
	 * This allows getting rid of all potentially harmful HTML tags and attributes.
	 *
	 * @return array Allowed Tags.
	 */
	function pform_get_allowed_tags() {

		static $allowed_tags;

		if ( ! empty( $allowed_tags ) ) {
			return $allowed_tags;
		}

		$atts = [ 'align', 'class', 'type', 'id', 'for', 'style', 'src', 'rel', 'href', 'target', 'value', 'width', 'height', 'data-*', 'placeholder', 'name', 'method', 'action', 'min', 'max', 'enctype', 'checked', 'selected', 'disabled', 'alt', 'onclick', 'autocomplete', 'onsubmit', 'multiple', 'label', 'tabindex'];
		$tags = [ 'label', 'form', 'style', 'input','button', 'strong', 'small', 'table', 'span', 'abbr', 'code', 'pre', 'div', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'li', 'em', 'hr', 'br', 'th', 'tr', 'td', 'p', 'a', 'b', 'i', 'select', 'option', 'textarea', 's', 'optgroup' ];
		
		$allowed_atts = array_fill_keys( $atts, true );
		$allowed_tags = array_fill_keys( $tags, $allowed_atts );
		
		return $allowed_tags;
	}



	//Payment fields Work

	function pie_forms_payment_single_field_frontend($field, $field_atts, $form_data){

		if(! empty($form_data['pf_payments']['authorizedotnet']['enable_authorizedotnet_payment']) ){
			$currency = isset($form_data['pf_payments']['authorizedotnet']['currency']) ?  $form_data['pf_payments']['authorizedotnet']['currency'] : "" ;
		}
		elseif(! empty($form_data['paypal_donation']['enable_paypal_donation']) || ! empty($form_data['pf_payments']['paypal_standard']['enable_paypal_payment']) ){
			$currency = !isset($form_data['pf_payments']['paypal_standard']['currency']) ? ( isset($form_data['paypal_donation']['currency'] ) ? $form_data['paypal_donation']['currency'] : 'USD' ) : $form_data['pf_payments']['paypal_standard']['currency'];
		}
		elseif(! empty($form_data['pf_payments']['stripe']['enable_stripe_payment']) ){
			$currency = isset($form_data['pf_payments']['stripe']['currency']) ?  $form_data['pf_payments']['stripe']['currency'] : "" ;
		}
		else{
			$currency = "";
		}
		// Define data.
		$primary 	= $field['properties']['inputs']['primary'];

		if( $field['price_type'] == 'user_defined'){
			printf(
				"<input type='number' %s %s min='0'>",
				Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
				esc_attr( 1 )
			);
		} else {
			echo Pie_Forms()->core()->pform_format_amount($field['pf_price'], true, $currency);
			printf(
				"<input type='hidden' %s %s %s>",
				Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
				esc_attr( $primary['required'] ),
				'value="'.$field['pf_price'].'"'
			);
		}

		echo '<p style="margin-bottom: 0px;">Payment Methods*</p>';

		$checked_pay_input = true;

		foreach ($form_data['pf_payments'] as $key => $payment) {
		if (!empty($payment['enable_' . (($key == 'paypal_standard') ? 'paypal' : $key) . '_payment'])) {
			
			// Check if payment method is 'authorizedotnet' and if form has 'checkout' field
			if($key == 'authorizedotnet'){
			$has_checkout_field = false;
			foreach($form_data['form_fields'] as $field_check){
				if($field_check['type'] == 'checkout'){
				$has_checkout_field = true;
				break;
				}
			}
			if($has_checkout_field){
				echo '<br><input type="radio" name="payment" value="' . $key . '"';
				if ($checked_pay_input) {
				echo ' checked';
				$checked_pay_input = false;
				}
				echo '>' . ucwords(($key == 'paypal_standard') ? 'PayPal' : (($key == 'authorizedotnet') ? 'Authorize.net' : $key));
			}
			continue;
			}

			echo '<br><input type="radio" name="payment" value="' . $key . '"';
			if ($checked_pay_input) {
			echo ' checked';
			$checked_pay_input = false;
			}
			echo '>' . ucwords(($key == 'paypal_standard') ? 'PayPal' : (($key == 'authorizedotnet') ? 'Authorize.net' : $key));
		}
		}

		if (!empty($form_data['paypal_donation']['enable_paypal_donation'])) {
		echo '<br><input type="radio" name="payment" value="paypaldonation"';
		if ($checked_pay_input) {
			echo ' checked';
		}
		echo '>PayPal Donation';
		}
		
	}

	function pie_forms_payment_single_builder_insider($field, $form_data,  $field_class  ){

		if(! empty($form_data['pf_payments']['authorizedotnet']['enable_authorizedotnet_payment']) ){
			$currency = isset($form_data['pf_payments']['authorizedotnet']['currency']) ?  $form_data['pf_payments']['authorizedotnet']['currency'] : "" ;
		}
		elseif(! empty($form_data['paypal_donation']['enable_paypal_donation']) || ! empty($form_data['pf_payments']['paypal_standard']['enable_paypal_payment']) ){
			$currency = !isset($form_data['pf_payments']['paypal_standard']['currency']) ? ( isset($form_data['paypal_donation']['currency'] ) ? $form_data['paypal_donation']['currency'] : 'USD' ) : $form_data['pf_payments']['paypal_standard']['currency'];
		}
		elseif(! empty($form_data['pf_payments']['stripe']['enable_stripe_payment']) ){
			$currency = isset($form_data['pf_payments']['stripe']['currency']) ?  $form_data['pf_payments']['stripe']['currency'] : "" ;
		}
		else{
			$currency = "";
		}

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$field_class->field_preview_option( 'label', $field );

		// Primary input.		
		$hidden = (empty($field['price_type']) || $field['price_type'] == 'pre_defined') ? 'hidden' : '';
		echo '<input type="text" placeholder="' . esc_attr( $placeholder ) . '" class="widefat pf-price-field '.$hidden.'" disabled>';
		
		$hide = ($field['price_type'] == 'user_defined') ? 'hidden' : '';

		echo '<div class="price-container widefat '.$hide.'">' . Pie_Forms()->core()->pform_format_amount($field['pf_price'], true, $currency) . '</div>';

		// Description.
		$field_class->field_preview_option( 'description', $field );
	}

	function pie_forms_payment_multiple_field_builder_insider($field, $form_data,  $field_class  ){
		// Label.
		$field_class->field_preview_option( 'label', $field );

		// Choices.
		$field_class->field_preview_option( 'choices', $field );

		// Description.
		$field_class->field_preview_option( 'description', $field );	
	}

	function pie_forms_payment_multiple_field_frontend($field, $field_atts, $form_data){

		// Define data.
		$container = $field['properties']['input_container'];
		$choices   = $field['properties']['inputs'];

		printf(
			'<ul %s>',
			Pie_Forms()->core()->pie_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);

			foreach ( $choices as $key => $choice ) {

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


				if(! empty($form_data['pf_payments']['authorizedotnet']['enable_authorizedotnet_payment']) ){
					$currency = isset($form_data['pf_payments']['authorizedotnet']['currency']) ?  $form_data['pf_payments']['authorizedotnet']['currency'] : "" ;
				}
				elseif(! empty($form_data['paypal_donation']['enable_paypal_donation']) || ! empty($form_data['pf_payments']['paypal_standard']['enable_paypal_payment']) ){
					$currency = !isset($form_data['pf_payments']['paypal_standard']['currency']) ? ( isset($form_data['paypal_donation']['currency'] ) ? $form_data['paypal_donation']['currency'] : 'USD' ) : $form_data['pf_payments']['paypal_standard']['currency'];
				}
				elseif(! empty($form_data['pf_payments']['stripe']['enable_stripe_payment']) ){
					$currency = isset($form_data['pf_payments']['stripe']['currency']) ?  $form_data['pf_payments']['stripe']['currency'] : "" ;
				}
				else{
					$currency = "";
				}



				$label = isset( $choice['label']['text'] ) ? $choice['label']['text'] : '';
				/* translators: %s - Choice item number. */
				$label  = $label !== '' ? $label : sprintf( esc_html__( 'Item %s', 'pie-forms' ), $key );
				$label .= ! empty( $field['show_price_after_labels'] ) && isset( $choice['data']['amount'] ) ? ' - ' . Pie_Forms()->core()->pform_format_amount( Pie_Forms()->core()->pform_sanitize_amount($choice['data']['amount'], $currency) , true, $currency ) : '';

				printf(
					'<li %s>',
					Pie_Forms()->core()->pie_html_attributes( $choice['container']['id'], $choice['container']['class'], $choice['container']['data'], $choice['container']['attr'] )
				);

					if ( empty( $field['dynamic_choices'] ) && ! empty( $field['choices_images'] ) ) {
						// Make image choices keyboard-accessible.
						$choice['label']['attr']['tabindex'] = 0;

						// Image choices.
						printf(
							'<label %s>',
							Pie_Forms()->core()->pie_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] )
						);

							if ( ! empty( $choice['image'] ) ) {
								printf(
									'<span class="pie-forms-image-choices-image"><img src="%s" alt="%s"%s></span>',
									esc_url( $choice['image'] ),
									esc_attr( $choice['label']['text'] ),
									! empty( $choice['label']['text'] ) ? ' title="' . esc_attr( $choice['label']['text'] ) . '"' : ''
								);
							}

							echo '<br>';
							printf(
								'<input type="radio" %s %s %s>',
								Pie_Forms()->core()->pie_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ),
								esc_attr( $choice['required'] ),
								checked( '1', $choice['default'], false )
							);

							echo '<span class="pie-forms-image-choices-label">' . wp_kses_post( $label ) . '</span>';

						echo '</label>';

					} else {
						// Normal display.
						printf(
							'<input type="radio" %s %s %s>',
							Pie_Forms()->core()->pie_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ),
							esc_attr( $choice['required'] ),
							checked( '1', $choice['default'], false )
						);

						printf(
							'<label %s>%s</label>',
							Pie_Forms()->core()->pie_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] ),
							wp_kses_post( $label )
						); // WPCS: XSS ok.
					}
				echo '</li>';
			}
		echo '</ul>';



		echo '<p style="margin-bottom: 0px;">Payment Methods*</p>';

		$checked_pay_input = true;

		foreach ($form_data['pf_payments'] as $key => $payment) {
		if (!empty($payment['enable_' . (($key == 'paypal_standard') ? 'paypal' : $key) . '_payment'])) {
			
			// Check if payment method is 'authorizedotnet' and if form has 'checkout' field
			if($key == 'authorizedotnet'){
			$has_checkout_field = false;
			foreach($form_data['form_fields'] as $field_check){
				if($field_check['type'] == 'checkout'){
				$has_checkout_field = true;
				break;
				}
			}
			if($has_checkout_field){
				echo '<br><input type="radio" name="payment" value="' . $key . '"';
				if ($checked_pay_input) {
				echo ' checked';
				$checked_pay_input = false;
				}
				echo '>' . ucwords(($key == 'paypal_standard') ? 'PayPal' : (($key == 'authorizedotnet') ? 'Authorize.net' : $key));
			}
			continue;
			}

			echo '<br><input type="radio" name="payment" value="' . $key . '"';
			if ($checked_pay_input) {
			echo ' checked';
			$checked_pay_input = false;
			}
			echo '>' . ucwords(($key == 'paypal_standard') ? 'PayPal' : (($key == 'authorizedotnet') ? 'Authorize.net' : $key));
		}
		}

		if (!empty($form_data['paypal_donation']['enable_paypal_donation'])) {
		echo '<br><input type="radio" name="payment" value="paypaldonation"';
		if ($checked_pay_input) {
			echo ' checked';
		}
		echo '>PayPal Donation';
		}
	}

	
}