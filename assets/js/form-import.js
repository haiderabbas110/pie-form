(function($) {
    // CFS7 IMPORT
    /**
         * Imports a single form from the import queue.
         */
    var s = {};
    function importForm() {
        
        var $processSettings = $( '#pieforms-importer-process' ),
            formID           = _.first( s.importQueue ),
            provider         = getQueryString( 'provider' ),
            data             = {
                action:  'pieforms_import_form_' + provider,
                form_id: formID,
                nonce:   pf_import.admin_nonce
            };

        // Trigger AJAX import for this form.
        $.post( pf_import.ajax_url, data, function( res ) {
            if ( res.success ){
                var statusUpdate;

                if ( res.data.error ) {
                    statusUpdate = wp.template( 'pieforms-importer-status-error' );
                } else {
                    statusUpdate = wp.template( 'pieforms-importer-status-update' );
                }
                console.log(statusUpdate);

                
                $processSettings.find( '.status' ).prepend( statusUpdate( res.data ) );
                $processSettings.find( '.status' ).show();
                // Remove this form ID from the queue.
                s.importQueue = _.without( s.importQueue, formID );
                s.imported++;

                if ( _.isEmpty( s.importQueue ) ) {
                    $processSettings.find( '.process-count' ).hide();
                    $processSettings.find( '.forms-completed' ).text( s.imported );
                    $processSettings.find( '.process-completed' ).show();
                } else {
                
                    // Import next form in the queue.
                    $processSettings.find( '.form-current' ).text( s.imported+1 );
                    importForm();
                }
            }
        });
    }

    function getQueryString ( name ) {

        var match = new RegExp( '[?&]' + name + '=([^&]*)' ).exec( window.location.search );
        return match && decodeURIComponent( match[1].replace(/\+/g, ' ') );
    }

    /**
     * Begins the process of importing the forms.
     *
     */
    function importForms( forms ) {
        var $processSettings = $( '#pieforms-importer-process' );

        // Display total number of forms we have to import.
        $processSettings.find( '.form-total' ).text( forms.length );
        $processSettings.find( '.form-current' ).text( '1' );

        // Hide the form select and form analyze sections.
        $( '#pieforms-importer-forms, #pieforms-importer-analyze' ).hide();

        // Show processing status.
        $processSettings.show();
        console.log(s);
        // Create global import queue.
        s.importQueue = forms;
        s.imported    = 0;

        // Import the first form in the queue.
        importForm();
    }

    /**
         * Begins the process of analyzing the forms.
         *
         * This runs for non-Pro installs to check if any of the forms to be
         * imported contain fields
         * not currently available.
         */
        function analyzeForms( forms ) {

            var $processAnalyze = $( '#pieforms-importer-analyze' );

            // Display total number of forms we have to import.
            $processAnalyze.find( '.form-total' ).text( forms.length );
            $processAnalyze.find( '.form-current' ).text( '1' );

            // Hide the form select section.
            $( '#pieforms-importer-forms' ).hide();

            // Show Analyze status.
            $processAnalyze.show();

            // Create global analyze queue.
            s.analyzeQueue   = forms;
            s.analyzed       = 0;
            s.analyzeUpgrade = [];
            s.formIDs        = forms;

            // Analyze the first form in the queue.
            analyzeForm();
        }

        /**
         * Analyze a single form from the queue.
         */
        function analyzeForm() {

            var $analyzeSettings = $( '#pieforms-importer-analyze' ),
                formID           = _.first( s.analyzeQueue ),
                provider         = getQueryString( 'provider' ),
                data             = {
                    action:  'pieforms_import_form_' + provider,
                    analyze: 1,
                    form_id: formID,
                    nonce:   pf_import.admin_nonce
                };

            // Trigger AJAX analyze for this form.
            $.post( pf_import.ajax_url, data, function( res ) {
                if ( res.success ){

                    if ( ! _.isEmpty( res.data.upgrade_plain ) || ! _.isEmpty( res.data.upgrade_omit ) ) {
                        s.analyzeUpgrade.push({
                            name:   res.data.name,
                            fields: _.union( res.data.upgrade_omit, res.data.upgrade_plain )
                        });
                    }

                    // Remove this form ID from the queue.
                    s.analyzeQueue = _.without( s.analyzeQueue, formID );
                    s.analyzed++;

                    if ( _.isEmpty( s.analyzeQueue ) ) {

                        if ( _.isEmpty( s.analyzeUpgrade ) ) {
                            // Continue to import forms as no Pro fields were
                            // found.
                            importForms( s.formIDs );
                        } else {
                            // We found Pro fields, so alert the user.
                            var upgradeDetails = wp.template( 'pieforms-importer-upgrade' );
                            $analyzeSettings.find( '.upgrade' ).append( upgradeDetails( s.analyzeUpgrade ) );
                            $analyzeSettings.find( '.upgrade' ).show();
                            $analyzeSettings.find( '.process-analyze' ).hide();
                        }

                    } else {
                        // Analyze next form in the queue.
                        $analyzeSettings.find( '.form-current' ).text( s.analyzed+1 );
                        analyzeForm();
                    }
                }
            });
        }

    // Run import for a specific provider.
    $( document ).on( 'click', '#pieforms-importer-forms-submit', function( event ) {

        event.preventDefault();

        // Check to confirm user as selected a form.
        if ( $( '#pieforms-importer-forms input:checked' ).length ) {

            var ids = [];
            $( '#pieforms-importer-forms input:checked' ).each( function ( i ) {
                ids[i] = $( this ).val();
            });

            if ( ! pf_import.isPro ) {
                // We need to analyze the forms before starting the
                // actual import.
                analyzeForms( ids );
            } else {
                // Begin the import process.
                importForms( ids );
            }

        } else {

            // User didn't actually select a form so alert them.
            $.alert({
                title: false,
                content: pieforms_admin.importer_forms_required,
                icon: 'fa fa-info-circle',
                type: 'blue',
                buttons: {
                    confirm: {
                        text: pieforms_admin.ok,
                        btnClass: 'btn-confirm',
                        keys: [ 'enter' ],
                    }
                }
            });
        }
    });

    // Continue import after analyzing.
    $( document ).on( 'click', '#pieforms-importer-continue-submit', function( event ) {

        event.preventDefault();

        importForms( s.formIDs );
    });


    $( document ).on( 'change', '.checkbox-multiselect-columns input', function() {

        var $this      = $( this ),
            $parent    = $this.parent(),
            $container = $this.closest( '.checkbox-multiselect-columns' ),
            label      = $parent.text(),
            itemID     = 'check-item-' + $this.val(),
            $item      = $container.find( '#' + itemID );

        if ( $this.prop( 'checked' ) ) {
            $this.parent().addClass( 'checked' );
            if ( ! $item.length ) {
                $container.find('.second-column ul').append( '<li id="'+itemID+'">'+label+'</li>' );
            }
        } else {
            $this.parent().removeClass( 'checked' );
            $container.find( '#' + itemID ).remove();
        }
    });

    $( document ).on( 'click', '.checkbox-multiselect-columns .all', function( event ) {

        event.preventDefault();

        $( this ).closest( '.checkbox-multiselect-columns' ).find( 'input[type=checkbox]' ).prop( 'checked', true ).trigger( 'change' );
        $( this ).remove();
    });
})( jQuery );