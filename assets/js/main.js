(function($) {
    // Toggle form status.
    $( document ).on( 'change', '.pie-forms-toggle-form input', function(e) {
        e.stopPropagation();
        $.post( pf_main_data.ajax_url, {
            action: 'pie_forms_enabled_form',
            security: pf_main_data.pf_enabled_form,
            form_id: $( this ).data( 'form_id' ),
            enabled: $( this ).prop( 'checked' ) ? 1 : 0
        });
    });
})( jQuery );