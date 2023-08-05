if (typeof (jQuery) != 'undefined') {
    
    jQuery.noConflict(); // Reverts '$' variable back to other JS libraries
    
    "use strict";
    var pf_addons = jQuery.noConflict();

    pf_addons(document).ready(function($) {
        // Toggle an addon state.
        if ( $( '.sib-product button').hasClass( 'disabled' ) ) {
            $('.sib-product button').removeClass('disabled');
        }
        $( document ).on( 'click', '.sib-product button', function( event ) {
    
            event.preventDefault();
    
            addonToggle( $( this ) );
        } );
        
        function addonToggle($btn){
            var $addon = $btn.closest( '.sib-product' ),
                plugin = $btn.attr( 'data-plugin' ),
                pluginType = $btn.attr( 'data-type' ),
                action,
                cssClass,
                statusText,
                buttonText,
                errorText,
                successText;
    
            $btn.prop( 'disabled', true ).addClass( 'loading' );
            $btn.html( '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>' );
    
            if ( $btn.hasClass( 'status-active' ) ) {
                // Deactivate.
                action     = 'pie_forms_deactivate_addon';
                cssClass   = 'status-inactive';
                if ( pluginType === 'plugin' ) {
                    cssClass += ' button button-secondary';
                }
                statusText = 'In-Active';
                buttonText = 'Activate';
                errorText  = 'Deactivate';
                if ( pluginType === 'addon' ) {
                    buttonText = '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>' + buttonText;
                    errorText  = '<i class="fa fa-toggle-on" aria-hidden="true"></i>' + errorText;
                }
    
            } else if ( $btn.hasClass( 'status-inactive' ) ) {
                // Activate.
                action     = 'pie_forms_activate_addon';
                cssClass   = 'status-active';
                if ( pluginType === 'plugin' ) {
                    cssClass += ' button button-secondary'; 
                }
                statusText = 'Active';
                buttonText = 'Deactivate';
                if ( pluginType === 'addon' ) {
                    buttonText = '<i class="fa fa-toggle-on" aria-hidden="true"></i>' + buttonText;
                    errorText  = '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>' + 'Activate';
                } else if ( pluginType === 'plugin' ) {
                    buttonText = 'Deactivate';
                    errorText  = 'Activate';
                }
    
            }else{
                return;
            }
    
            var data = {
                action   : action,
                plugin   : plugin,
                type     : pluginType,
                security : pf_addon.nonce_addons
            };
            // console.log(pf_addons.nonce_addons);
            $.post( ajaxurl, data, function( res ) {
                if ( res.success ) {       
                    successText = res.data;
                
                    $addon.find( 'product-action' ).append( '<div class="msg success">' + successText + '</div>' );
                    $addon.find( 'span.status-label' )
                            .removeClass( 'status-active status-inactive status-download' )
                            .addClass( cssClass )
                            .removeClass( 'button button-primary button-secondary ' )
                            .text( statusText );
                    $btn
                        .removeClass( 'status-active status-inactive status-download' )
                        .removeClass( 'button button-primary button-secondary' )
                        .addClass( cssClass ).html( buttonText );
                } else {
                    console.log(res)
                    if ( 'object' === typeof res.data ) {
                       
                        $addon.find( 'product-action' ).append( '<div class="msg error">' + 'Could not install a plugin. Please download from WordPress.org and install manually.' + '</div>' );
                       
                    } else {
                        $addon.find( 'product-action' ).append( '<div class="msg error">'+res.data+'</div>' );
                    }
    
                    $btn.html( errorText );
                }
    
                $btn.prop( 'disabled', false ).removeClass( 'loading' );
    
                // Automatically clear addon messages after 3 seconds.
                setTimeout( function() {
                    $( '.sib-product .msg' ).remove();
                }, 3000 );
    
            }).fail( function( xhr ) {
                console.log( xhr.responseText );
            });
        }
    

    });

    // Declare jQuery Object to $.
    $ = jQuery;
}