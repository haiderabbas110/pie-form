<?php

/**
 * WooCommerce API Manager API Key Class
 * Updates for add-ons
 *
 * @package Update API Manager/Key Handler
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) 2011-2013, Todd Lahman LLC
 * @since 1.1.1
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class PFORM_Api_Apikey {

	// API Key URL
	public function create_software_api_url( $args ) {
        /*
         * STORE URL FOR API
         */
        //$storeUrl = 'https://blue.genetechz.com/store.genetech.co/';
        $storeUrl = 'https://store.genetech.co/';
		$api_url = add_query_arg( 'wc-api', 'am-software-api', $storeUrl );

    //    echo '<pre>';
    //    print_r(http_build_query($args));
    //    echo '</pre>';
    //    die;

		return $api_url . '&' . http_build_query( $args );

	}

	public function activate( $args , $addon = false) {
		$platform = site_url();

        $PFORM_Api_Passwords = new PFORM_Api_Passwords();
		
		if($addon){
			$addon_id = get_option( 'pieforms_manager_'.$addon['is_addon'].'_id' );
			$instance = get_option( 'pieforms_manager_'.$addon['is_addon'].'_instance' );
		
			$defaults = array(
				'request' => 'activation',
				'product_id' => $addon_id,
				'instance' => $instance,
				'platform' => $platform,
				'is_addon' => $addon['is_addon'],
				'is_addon_version' => $addon['is_addon_version']
				);
		}else{
			$product_id = 'PieForms-For-WP';
			$instance   = get_option('pieforms_manager_instance');
			
			$defaults = array(
			'request' => 'activation',
			'product_id' => $product_id,
			'instance' => $instance,
			'platform' => $platform
			);
		}
		
		$args = wp_parse_args( $defaults, $args );
		$target_url = self::create_software_api_url( $args );
        $request = wp_remote_get( $target_url, array('sslverify' => FALSE) );
	
		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}

		$response = wp_remote_retrieve_body( $request );
		
		return $response;
	}

	public function deactivate( $args, $addon = false  ) {
		// instance required

		$platform = site_url();
		if($addon){
			$addon_id = get_option( 'pieforms_manager_'.$addon['is_addon'].'_id' );
			$instance = get_option( 'pieforms_manager_'.$addon['is_addon'].'_instance' );
			
			$defaults = array(
				'request' => 'deactivation',
				'product_id' => $addon_id,
				'instance' => $instance,
				'platform' => $platform,
				'is_addon' => $addon['is_addon'],
				'is_addon_version' => $addon['is_addon_version']
				);
		}else{
			$product_id = 'PieForms-For-WP';
			$instance = get_option( 'pieforms_manager_instance' );

			$defaults = array(
				'request' => 'deactivation',
				'product_id' => $product_id,
				'instance' => $instance,
				'platform' => $platform
				);
		}
		$args = wp_parse_args( $defaults, $args );

		$target_url = self::create_software_api_url( $args );

//        echo '<pre>';
//        print_r($target_url);
//        echo '</pre>';
//        die;

		$request = wp_remote_get( $target_url, array('sslverify' => FALSE)  );



        if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}
//        echo '<pre>';
//		print_r($request);
//        echo '</pre>';
		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

	public function check( $args = array(),$addon = false ) {
		$platform = site_url();
		$_product_type = "";
		if($addon){
			$addon_id = get_option( 'pieforms_manager_'.$addon['is_addon'].'_id' );
			$instance = get_option( 'pieforms_manager_'.$addon['is_addon'].'_instance' );
			
			$defaults = array(
				'request' 		=> 'status',
				'product_id' 	=> $addon_id,
				'instance' 		=> $instance,
				'platform' 		=> $platform,
				'is_addon' 		=> $addon['is_addon'],
				'pro_active' 	=> $args['is_pro_active']
			);
			$_product_type = $addon['is_addon'];
		}else{
		$product_id = get_option( 'pieforms_manager_product_id' );
		$instance 	= get_option( 'pieforms_manager_instance' );

		$defaults = array(
			'request' 		=> 'status',
			'product_id' 	=> $product_id,
			'instance' 		=> $instance,
			'platform' 		=> $platform
			);
			$_product_type = "PR_Premium";
		}

		$args = wp_parse_args( $defaults, $args );
		$target_url = self::create_software_api_url( $args );

		$request = wp_remote_get( $target_url );

		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}


		$response = wp_remote_retrieve_body( $request );
		$this->update_status($response,$_product_type);

		return $response;
	}

	public function update_status($resp,$prod){
		$resp 	= json_decode($resp);
		if( isset( $resp->status_check ) ){
			update_option('pieforms_manager_'.$prod.'_status',$resp->status_check);
		}		
	}

}

// Class is instantiated as an object by other classes on-demand