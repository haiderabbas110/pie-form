if (typeof (jQuery) != 'undefined') {
    
    jQuery.noConflict(); // Reverts '$' variable back to other JS libraries
    
    "use strict";
    var pf_addons = jQuery.noConflict();

    pf_addons(document).ready(function($) {
        // Toggle an addon state.
        $( document ).on( 'click', '.sib-product button', function( event ) {
    
            event.preventDefault();
            if ( $( this ).hasClass( 'disabled' ) ) {
                return false;
            }
    
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
                statusText = 'Inactive';
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
                    cssClass += ' button button-secondary disabled'; 
                }
                statusText = 'Activate';
                buttonText = 'Deactivate';
                if ( pluginType === 'addon' ) {
                    buttonText = '<i class="fa fa-toggle-on" aria-hidden="true"></i>' + buttonText;
                    errorText  = '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>' + 'Activate';
                } else if ( pluginType === 'plugin' ) {
                    buttonText = 'Activated';
                    errorText  = 'Activate';
                }
    
            } else if ( $btn.hasClass( 'status-download' ) ) {
                // Install & Activate.
                action   = 'pie_forms_install_addon';
                cssClass = 'status-active';
                if ( pluginType === 'plugin' ) {
                    cssClass += ' button disabled';
                }
                statusText = 'Activate';
                buttonText = 'Activated';
                errorText  = '<i class="fa fa-cloud-download" aria-hidden="true"></i>';
                if ( pluginType === 'addon' ) {
                    buttonText = '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>' + 'Deactivate';
                    errorText += 'Install Addon';
                }
    
            } else {
                return;
            }
    
            var data = {
                action  : action,
                plugin  : plugin,
                type    : pluginType,
                security: pf_about.nonce_install
            };
            $.post( ajaxurl, data, function( res ) {
                if ( res.success ) {
                    if ( 'install_addon' === action ) {
                        $btn.attr( 'data-plugin', res.data.basename );
                        successText = res.data.msg;
                        if ( ! res.data.is_activated ) {
                            statusText = 'Inactive';
                            buttonText = 'plugin' === pluginType ? 'Activate' : '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>' + 'Activate';
                            cssClass   = 'plugin' === pluginType ? 'status-inactive button button-secondary' : 'status-inactive';
                        }
                    } else {
                        successText = res.data;
                    }
                    $addon.find( 'product-action' ).append( '<div class="msg success">' + successText + '</div>' );
                    $addon.find( 'span.status-label' )
                            .removeClass( 'status-active status-inactive status-download' )
                            .addClass( cssClass )
                            .removeClass( 'button button-primary button-secondary disabled' )
                            .text( statusText );
                    $btn
                        .removeClass( 'status-active status-inactive status-download' )
                        .removeClass( 'button button-primary button-secondary disabled' )
                        .addClass( cssClass ).html( buttonText );
                } else {
                    console.log(res)
                    if ( 'object' === typeof res.data ) {
                        if ( pluginType === 'addon' ) {
                            $addon.find( 'product-action' ).append( '<div class="msg error">' + 'Could not install addon. Please download from pagebuilderaddons.com and install manually.' + '</div>' );
                        } else {
                            $addon.find( 'product-action' ).append( '<div class="msg error">' + 'Could not install a plugin. Please download from WordPress.org and install manually.' + '</div>' );
                        }
                    } else {
                        $addon.find( 'product-action' ).append( '<div class="msg error">'+res.data+'</div>' );
                    }
    
                    if ( 'install_addon' === action && 'plugin' === pluginType ) {
                        $btn.addClass( 'status-go-to-url' ).removeClass( 'status-download' );
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