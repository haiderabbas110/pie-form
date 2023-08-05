<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

/**
 * Delete options from database on uninstall 
 */

$delete_option =  get_option('pf_delete_options');

if($delete_option === 'yes'){
    $all_options = wp_load_alloptions();
    
    foreach ( $all_options as $name => $value ) {
        $options_names = stristr( $name, 'pf_' );
        delete_option($options_names);
    }

    global $wpdb;

    $wpdb->query( "DROP TABLE ".$wpdb->prefix."pf_forms" );
    $wpdb->query( "DROP TABLE ".$wpdb->prefix."pf_fields" );
}