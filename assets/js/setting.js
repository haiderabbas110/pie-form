(function($) {

    // Show/hide based on reCAPTCHA type.
    $( 'select#pf_recaptcha_type' ).change( function() {
        var recaptcha_v2_site_key             = $( '#pf_recaptcha_v2_site_key' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v2_secret_key           = $( '#pf_recaptcha_v2_secret_key' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v2_invisible_site_key   = $( '#pf_recaptcha_v2_invisible_site_key' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v2_invisible_secret_key = $( '#pf_recaptcha_v2_invisible_secret_key' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v2_invisible            = $( '#pf_recaptcha_v2_invisible' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v3_site_key             = $( '#pf_recaptcha_v3_site_key' ).parents( 'tr' ).eq( 0 ),
            recaptcha_v3_secret_key           = $( '#pf_recaptcha_v3_secret_key' ).parents( 'tr' ).eq( 0 );
        
        //if ( $( this ).is( ':checked' ) ) {
            if ( 'v2' === $( this ).val() ) {
                if( $( '#pf_recaptcha_v2_invisible' ).is(':checked') ) {
                    recaptcha_v2_site_key.hide();
                    recaptcha_v2_secret_key.hide();
                    recaptcha_v2_invisible_site_key.show();
                    recaptcha_v2_invisible_secret_key.show();
                } else {
                    recaptcha_v2_invisible_site_key.hide();
                    recaptcha_v2_invisible_secret_key.hide();
                    recaptcha_v2_site_key.show();
                    recaptcha_v2_secret_key.show();
                }
                recaptcha_v2_invisible.show();
                recaptcha_v3_site_key.hide();
                recaptcha_v3_secret_key.hide();
            } else {
                recaptcha_v2_site_key.hide();
                recaptcha_v2_secret_key.hide();
                recaptcha_v2_invisible.hide();
                recaptcha_v2_invisible_site_key.hide();
                recaptcha_v2_invisible_secret_key.hide();
                recaptcha_v3_site_key.show();
                recaptcha_v3_secret_key.show();
            }
        //}
    }).change();

    $( 'select#pf_recaptcha_v2_invisible' ).change( function() {
        if ( $( this ).is( ':checked' ) ) {
            $( '#pf_recaptcha_v2_site_key' ).parents( 'tr' ).eq( 0 ).hide();
            $( '#pf_recaptcha_v2_secret_key' ).parents( 'tr' ).eq( 0 ).hide();
            $( '#pf_recaptcha_v2_invisible_site_key' ).parents( 'tr' ).eq( 0 ).show();
            $( '#pf_recaptcha_v2_invisible_secret_key' ).parents( 'tr' ).eq( 0 ).show();
        } else {
            $( '#pf_recaptcha_v2_site_key' ).parents( 'tr' ).eq( 0 ).show();
            $( '#pf_recaptcha_v2_secret_key' ).parents( 'tr' ).eq( 0 ).show();
            $( '#pf_recaptcha_v2_invisible_site_key' ).parents( 'tr' ).eq( 0 ).hide();
            $( '#pf_recaptcha_v2_invisible_secret_key' ).parents( 'tr' ).eq( 0 ).hide();
        }
    });
    $('.forminp-activate').each(function(){
        var addon_name = $(this).attr('data-addon-name');
         $(this).find('.pie-forms-save-'+addon_name+'-button' ).on('click', function(event){
            
            event.preventDefault();
            
            var val= $(this).val(),
                email_address = $('#pieforms_addon_'+addon_name+'_licence_email').val()
                licence_key = $('#pieforms_addon_'+addon_name+'_licence_key').val()
            

            $.ajax({
                url: pform_admin.ajax_url,
                type:'POST',
                data:{
                    action: "pie_forms_addons_"+addon_name+"_licence",
                    licence_key: licence_key,
                    email_address: email_address,
                    security: pform_admin.nonce
                },
                beforeSend: function() {
                    $('.pie-forms-save-'+addon_name+'-button').html( '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>' );
                },
                success: function(res){
                    $('.pie-forms-settings').find('.form-table').before('<div class="notice pie-message">'+res+'</div>');
                    setTimeout(function(){
                        location.reload(); 
                   }, 1000); 
                }
            })
            
        })
    });
    // Upload Header Image
    $( document ).on( 'click', '.forminp-image button.pform-upld-header-img', function( event ) {
        event.preventDefault();

        var el = $( this ), media_frame;

        if ( media_frame ) {
            media_frame.open();
            return;
        }

        var $setting = $( el ).closest( '.forminp-image' );

        media_frame = wp.media.frames.pieforms_media_frame = wp.media({
            className: 'media-frame pie-forms-media-frame',
            frame: 'select',
            multiple: false,
            title: pform_admin.upload_image_title,
            library: {
                type: 'image'
            },
            button: {
                text: pform_admin.upload_image_button
            }
        });

        media_frame.on( 'select', function(){
            // Grab our attachment selection and construct a JSON representation of the model.
            var media_attachment = media_frame.state().get( 'selection' ).first().toJSON();
            // Send the attachment URL to our custom input field via jQuery.
            el.prev().val( media_attachment.url );
            $setting.find( 'img' ).remove();
            $setting.prepend( '<img src="'+media_attachment.url+'">' );
        });

        // Now that everything has been set, let's open up the frame.
        media_frame.open();
    });
    // Remove Uploaded Header Image
    $( document ).on( 'click', '.forminp-image button.pform-rmv-header-img', function( event ) {
        event.preventDefault();

        var el = $( this );

        var $setting = $( el ).closest( '.forminp-image' );

        el.prev().prev().val('');
        $setting.find( 'img' ).remove();
    });
    // Init color pickers via minicolors.js.
	$( '.pie-forms-color-picker' ).minicolors();
})( jQuery );

