<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Updates for add-ons
*/

class PFORM_Api_Menu{

	//private $Api_Manager_Key;
	private $pieforms_manager_key_class;

	// Load admin menu
	public function __construct() {

		$this->pieforms_manager_key_class = new PFORM_Api_Apikey();

		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	// Register settings
	public function load_settings() {
		global $pieforms_manager;

		register_setting( 'api_manager_pieform', 'api_manager_pieform', array( $this, 'validate_options' ) );
		
		// API Key
		add_settings_section( 'api_key', __( 'License Information', $pieforms_manager->pieforms_text_domain ), array( $this, 'wc_am_api_key_text' ), 'api_manager_example_dashboard' );
		add_settings_field( 'api_key', __( 'License Key', $pieforms_manager->pieforms_text_domain ), array( $this, 'wc_am_api_key_field' ), 'api_manager_example_dashboard', 'api_key' );
		add_settings_field( 'api_email', __( 'License email', $pieforms_manager->pieforms_text_domain ), array( $this, 'wc_am_api_email_field' ), 'api_manager_example_dashboard', 'api_key' );

		// Activation settings
		register_setting( 'pieforms_deactivate_example_checkbox', 'pieforms_deactivate_example_checkbox', array( $this, 'wc_am_license_key_deactivation' ) );
        register_setting( 'pieforms_deactivate_example_checkbox_uninstall', 'pieforms_deactivate_example_checkbox_uninstall', array( $this, 'wc_am_license_key_deactivation_on_uninstalling' ) );
        add_settings_section( 'deactivate_button', __( 'Plugin License Deactivation', $pieforms_manager->pieforms_text_domain ), array( $this, 'wc_am_deactivate_text' ), 'api_manager_example_deactivation' );
		add_settings_field( 'deactivate_button', __( 'Deactivate Plugin License', $pieforms_manager->pieforms_text_domain ), array( $this, 'wc_am_deactivate_textarea' ), 'api_manager_example_deactivation', 'deactivate_button' );

	}

	// Outputs API License text field
	public function wc_am_api_key_field() {
		global $pieforms_manager;

		$options = get_option( 'api_manager_pieform' );
		$api_key = $options['api_key'];
		echo "<input id='api_key' name='pieforms_manager_example[api_key]' size='25' type='text' value='". esc_attr($api_key) ."' />";
		if ( !empty( $options['api_key'] ) ) {
			echo "<span class='icon-pos'><img src='" . esc_url($pieforms_manager->plugin_url()) . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . esc_url($pieforms_manager->plugin_url()) . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Outputs API License email text field
	public function wc_am_api_email_field() {
		global $pieforms_manager;

		$options = get_option( 'api_manager_pieform' );
		$activation_email = $options['activation_email'];
		echo "<input id='activation_email' name='pieforms_manager_example[activation_email]' size='25' type='text' value='". esc_attr($activation_email) ."' />";
		if ( !empty( $options['activation_email'] ) ) {
			echo "<span class='icon-pos'><img src='" . esc_url ($pieforms_manager->plugin_url()) . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . esc_url ($pieforms_manager->plugin_url()) . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {
		global $pieforms_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();

		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_pieform' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );
		
		$activation_status = get_option( 'pieforms_licence_manager_activated' );
		$checkbox_status = get_option( 'pieforms_deactivate_example_checkbox' );

		$current_api_key = $this->get_key();
		
			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {
			    /**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key && !empty($current_api_key) )
					$this->replace_license_key( $current_api_key );
				
				
				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
					);

				$activate_results = $this->pieforms_manager_key_class->activate( $args );

				$activate_results = json_decode($activate_results['body'], true);
				if ( $activate_results['activated'] == true ) {//activate_text
					
					$_POST['success'] = __("Plugin activated","pie-forms");
					update_option( 'pieforms_licence_manager_activated', 'Activated' );
					update_option( 'pieforms_deactivate_example_checkbox', 'off' );
					$old_option = get_option( 'api_manager_pieform' );
					$old_option['api_key'] = trim( $input['api_key'] );
					$old_option['activation_email'] = trim( $input['activation_email'] );
					update_option( 'api_manager_pieform', $old_option );

                    $single_options = array(
                        'pieforms_manager_product_id' 			        => 'PieForms-For-WP',
                        'pieforms_manager_instance' 				    => $activate_results['instance'],
                        'pieforms_manager_example_deactivate_checkbox' => 'on',
                        'pieforms_licence_manager_activated' 				    => 'Activated',
                    );

					
                    foreach ( $single_options as $key => $value ) {
                        update_option( $key, $value );
                    }

					@header("location:".($this->pieforms_get_current_url()) );
				}
					
				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("pieforms_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-forms"));
				}
				
				/* if ( isset( $activate_results['code'] ) ) {
                    $_POST['error'] = ($activate_results['error']);
                    $options['activation_email'] = '';
                    $options['api_key'] = '';
                    update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
                   
				} */

				if ( isset( $activate_results['code'] ) ) {
					
					if( !isset($activate_results['additional info']) )
					{
						$activate_results['additional info'] = "";	
					}
					
					switch ( $activate_results['code'] ) {
						case '100'://api_email_text
							$errors->add("pieforms_license_error",__($activate_results['error'].". ".$activate_results['additional info'],"pie-forms"));
							$options['activation_email'] = '';
							$options['api_key'] = '';
							update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '101'://api_key_text
							$errors->add("pieforms_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"pie-forms"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '102'://api_key_purchase_incomplete_text
							$errors->add("pieforms_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"pie-forms"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '103'://api_key_exceeded_text
								$errors->add("pieforms_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"pie-forms"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '104'://api_key_not_activated_text
								$errors->add("pieforms_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"pie-forms"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '105'://api_key_invalid_text
								$errors->add("pieforms_license_error",__($activate_results['error'],"pie-forms"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
						case '106'://sub_not_active_text
								$errors->add("pieforms_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-forms"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
						break;
					}

				}

			} // End Plugin Activation
		return $options;
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_addon_options( $input ) {

		global $pieforms_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_pieform' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );
		$api_addon = array('is_addon'=>trim($input['api_addon']), 'is_addon_version'=>trim($input['api_addon_version']));
		$activation_status = get_option( 'pieforms_licence_manager_activated' );
		$current_api_key = $this->get_key();
		
		// Should match the settings_fields() value
			if ( ( $activation_status == 'Deactivated' && $api_key != '' && $api_email != '' && $current_api_key == $api_key ) || $input['is_pieforms_pro_inactive'] === true   ) {
				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key && !empty($current_api_key) )
					$this->replace_license_key( $current_api_key );
				
				
				$args = array(
							'email' 					=> $api_email,
							'licence_key' 				=> $api_key,
							'is_pieforms_pro_inactive' 	=> $input['is_pieforms_pro_inactive']
						);
				
				$activate_results = $this->pieforms_manager_key_class->activate( $args, $api_addon );
				
				$activate_results = json_decode($activate_results, true);
				
				if ( $activate_results['activated'] == true ) {//activate_text

					$errors->add("pieforms_license_error",__('Addon Activated',"pie-forms"));

					update_option( 'pieforms_manager_'.$api_addon['is_addon'].'_activated', 'Activated' );
				
					if( $input['is_pieforms_pro_inactive'] == true )
					{
						update_option( 'pieforms_'.$api_addon['is_addon'].'_licence_key', $api_key );
						update_option( 'pieforms_'.$api_addon['is_addon'].'_licence_email', $api_email );
					}					
				
				}
			
				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("pieforms_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-forms"));
				}
				
				if ( isset( $activate_results['code'] ) ) {
					$activate_results['additional info'] = isset($activate_results['additional info']) ? $activate_results['additional info'] : '';

						switch ( $activate_results['code'] ) {
							case '100':
								$errors->add("pieforms_license_error",__($activate_results['error'].". ".$activate_results['additional info'],"pie-forms"));
								
							break;
							case '101':
								$errors->add("pieforms_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"pie-forms"));
								
							break;
							case '102':
								
								$errors->add("pieforms_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"pie-forms"));
								
							break;
							case '103':
									$errors->add("pieforms_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"pie-forms"));
									
							break;
							case '104':
									$errors->add("pieforms_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"pie-forms"));
									
							break;
							case '105':
									$errors->add("pieforms_license_error",__($activate_results['error'],"pie-forms"));
									
							break;
							case '106':
									$errors->add("pieforms_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-forms"));
									
							break;
							case '107':
									$errors->add("pieforms_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-forms"));
									
							break;
						}
				}

			} // End Plugin Activation	
		return $options;
	}
	
	public function get_key() {
		$wc_am_options = get_option('api_manager_pieform');
		$api_key = isset($wc_am_options['api_key']) ? $wc_am_options['api_key'] : '';

		return $api_key;
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {
		global $pieforms_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		$default_options = get_option( 'api_manager_pieform' );
		
		$api_email = $default_options['activation_email'];
		
		$args = array(
			'email' => $api_email,
			'licence_key' => $current_api_key,
			);
		
		$reset = $this->pieforms_manager_key_class->deactivate( $args ); // reset license key activation
		
		if ( $reset == true )
			return true;

		return $errors->add("pieforms_license_error",__('The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.',"pie-forms"));
		
	}

	// Deactivates the license key to allow key to be used on another blog
	public function wc_am_license_key_deactivation( $input, $addon = false ) {
		
		global $pieforms_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		$activation_status = get_option( 'pieforms_licence_manager_activated' );
		$default_options = get_option( 'api_manager_pieform' );
		$api_email = $default_options['activation_email'];
		$api_key = $default_options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		$options = ( $input == 'on' ? 'on' : 'off' );
		$is_pro_inactive = isset($addon['is_pieforms_pro_inactive']) ? $addon['is_pieforms_pro_inactive'] : false;
		if ( ( $options == 'on' && $activation_status == 'Activated' && $api_key != '' && $api_email != '' ||$is_pro_inactive === true  )) {
			
			if($addon){
				
				if( $is_pro_inactive == true )
				{
					$api_key	= get_option( 'pieforms_'.$addon['is_addon'].'_licence_key' );	
					$api_email	= get_option( 'pieforms_'.$addon['is_addon'].'_licence_email' );					
					
					$args = array(
									'email' 					=> $api_email,
									'licence_key' 				=> $api_key,
									'is_pieforms_pro_inactive'	=> $is_pro_inactive
								);
				}
				$reset 	= $this->pieforms_manager_key_class->deactivate( $args, $addon ); // reset addon license key activation	
				
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {

					update_option( 'pieforms_manager_'.$addon['is_addon'].'_activated', 'Deactivated' );
					
					//if( $is_pro_inactive == true ) {
						delete_option( 'pieforms_'.$addon['is_addon'].'_licence_key' );	
						delete_option( 'pieforms_'.$addon['is_addon'].'_licence_email' );					
					//}
					
					$errors->add("pieforms_license_error",__('Addon license deactivated.',"pie-forms"));
					
					return $options;
				}else{
					$errors->add("pieforms_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-forms"));
				}

			}else{

				$reset = $this->pieforms_manager_key_class->deactivate( $args ); // reset license key activation
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {
					$update = array(
						'api_key' => '',
						'activation_email' => ''
					);
	
					$merge_options = array_merge( $default_options, $update );
					update_option( 'api_manager_pieform', $merge_options );
					update_option( 'pieforms_licence_manager_activated', 'Deactivated' );
	
					$single_options = array(
						'pieforms_manager_product_id' 			        => 'PieForms-For-WP',
						'pieforms_manager_example_deactivate_checkbox' => 'off',
						'pieforms_licence_manager_activated' 				    => 'Deactivated',
					);
	
	
					foreach ( $single_options as $key => $value ) {
						update_option( $key, $value );
					}
	
					# settings changes on license deactivation
					$errors->add("pieforms_license_error",__('Plugin license deactivated.',"pie-forms"));
	
					@header("location:".($this->pieforms_get_current_url()) );
					
					return $options;
					
	
				}else{
					$errors->add("pieforms_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-forms"));
				}
			}
		}

	}

    // Deactivates the license key to allow key on uninstalling plugin
    public function wc_am_license_key_deactivation_on_uninstalling( $input) {

        global $pieforms_manager,$errors;
        if(empty($errors))
            $errors = new WP_Error();
        $activation_status = get_option( 'pieforms_licence_manager_activated' );
        $default_options = get_option( 'api_manager_pieform' );
        $api_email = $default_options['activation_email'];
        $api_key = $default_options['api_key'];

        $args = array(
            'email' => $api_email,
            'licence_key' => $api_key,
        );

        $options = ( $input == 'on' ? 'on' : 'off' );

        if ( ( $options == 'on' && $activation_status == 'Activated' && $api_key != '' && $api_email != '' )) {

            $reset = $this->pieforms_manager_key_class->deactivate( $args ); // reset license key activation
            $reset_result = json_decode($reset, true);

            if ( $reset_result['deactivated'] == true ) {
                $update = array(
                    'api_key' => '',
                    'activation_email' => ''
                );


                $merge_options = array_merge( $default_options, $update );
                update_option( 'api_manager_pieform', $merge_options );
                update_option( 'pieforms_licence_manager_activated', 'Deactivated' );

                # settings changes on license deactivation
                $errors->add("pieforms_license_error",__('Plugin license deactivated.',"pie-forms"));

            }else{
                $errors->add("pieforms_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-forms"));
            }

            foreach ( array(
                          'pieforms_manager_product_id',
                          'pieforms_manager_instance',
                          'pieforms_manager_example_deactivate_checkbox',
                          'pieforms_licence_manager_activated',
                          'api_manager_pieform',
                          'pieforms_deactivate_example_checkbox',
                      ) as $option) {
                delete_option( $option );
            }
            return "Deleted";
        } else {
            foreach ( array(
                          'pieforms_manager_product_id',
                          'pieforms_manager_instance',
                          'pieforms_manager_example_deactivate_checkbox',
                          'pieforms_licence_manager_activated',
                          'api_manager_pieform',
                          'pieforms_deactivate_example_checkbox',
                      ) as $option) {
                delete_option( $option );
            }
            return $options;
        }

    }


    public function wc_am_deactivate_text() {
	}

    function pieforms_get_current_url($query_string = "") {
        $current_url  = 'http';
        $server_https = isset($_SERVER["HTTPS"]) ? sanitize_text_field(wp_unslash($_SERVER["HTTPS"])) : "";
        $server_name  = sanitize_text_field($_SERVER["SERVER_NAME"]);
        $server_port  = sanitize_text_field($_SERVER["SERVER_PORT"]);
        $request_uri  = esc_url_raw($_SERVER["REQUEST_URI"]);
        if ($server_https == "on")
            $current_url .= "s";
        $current_url .= "://";
        if ($server_port != "80")
            $current_url .= $server_name . ":" . $server_port . $request_uri;
        else
            $current_url .= $server_name . $request_uri;

        if(!empty($query_string))
            return $this->pieforms_modify_custom_url($current_url,$query_string);

        return $current_url;
    }

        function pieforms_modify_custom_url($url,$query_string=false){
            return static_pieforms_modify_custom_url($url,$query_string);
        }
    static function static_pieforms_modify_custom_url($get_url,$query_string=false){
        $get_url = trim($get_url);
        if(!$get_url) return false;

        if(strpos($get_url,"?"))
            $url = $get_url."&".$query_string;
        else
            $url = $get_url."?".$query_string;

        return $url;
    }

	public function wc_am_deactivate_textarea() {
		global $pieforms_manager;
		$activation_status = get_option( 'pieforms_deactivate_example_checkbox' );
		?>
		<input type="checkbox" id="pieforms_deactivate_example_checkbox" name="pieforms_deactivate_example_checkbox" value="on" <?php checked( $activation_status, 'on' ); ?> />
		<span class="description"><?php _e( 'Deactivates plugin license so it can be used on another blog.', $pieforms_manager->pieforms_text_domain ); ?></span>
		<?php
	}

}
//$api_manager_example_menu = new Piereg_API_Manager_Example_MENU();