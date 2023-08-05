jQuery( function( $ ) {
	/**
	 * Upgrade actions.
	 */
	var pf_upgrade_actions = {
		init: function() {
			$( document.body ).on( 'click', '.pie-form-element-box.upgrade-modal', this.field_upgrade );

		},
		field_upgrade: function( e ) {
			e.preventDefault();

			pf_upgrade_actions.upgrade_modal( $( this ).data( 'feature' ) ? $( this ).data( 'feature' ) : $( this ).text() + ' field' );
		},
	
		upgrade_modal: function( feature ) {
			var message = pf_upgrade.upgrade_message.replace( /%name%/g, feature );

			$.alert({
				title: feature + ' ' + pf_upgrade.upgrade_title,
				icon: 'dashicons dashicons-lock',
				content: message,
				type: 'red',
				boxWidth: '565px',
				buttons: {
					confirm: {
						text: pf_upgrade.upgrade_button,
						btnClass: 'btn-confirm',
						keys: ['enter'],
						action: function () {
							window.open( pf_upgrade.upgrade_url, '_blank' );
						}
					},
					cancel: {
						text: pf_upgrade.i18n_ok
					}
				}
            });
        },
	
	};

	pf_upgrade_actions.init();
});
