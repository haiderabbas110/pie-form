(function( $ ){
	'use strict';

	$( function() {
		// Close modal
		var pfModalClose = function() {
			if ( $('#pf-modal-select-form').length ) {
				$('#pf-modal-select-form').get(0).selectedIndex = 0;
				$('#pf-modal-checkbox-title, #pf-modal-checkbox-description').prop('checked', false);
			}
			$('#pf-modal-backdrop, #pf-modal-wrap').css('display','none');
			$( document.body ).removeClass( 'modal-open' );
		};
		// Open modal when media button is clicked
		$(document).on('click', '.pf-insert-form-button', function(event) {
			event.preventDefault();
			$('#pf-modal-backdrop, #pf-modal-wrap').css('display','block');
			$( document.body ).addClass( 'modal-open' );
		});
		// Close modal on close or cancel links
		$(document).on('click', '#pf-modal-close, #pf-modal-cancel a', function(event) {
			event.preventDefault();
			pfModalClose();
		});
		// Insert shortcode into TinyMCE
		$(document).on('click', '#pf-modal-submit', function(event) {
			event.preventDefault();
			var shortcode;
			shortcode = '[pie_form id="' + $('#pf-modal-select-form').val() + '"';
			if ( $('#pf-modal-checkbox-title').is(':checked') ) {
				shortcode = shortcode+' title="true"';
			}
			if ( $('#pf-modal-checkbox-description').is(':checked') ) {
				shortcode = shortcode+' description="true"';
			}
			shortcode = shortcode+']';
			wp.media.editor.insert(shortcode);
			pfModalClose();
		});
	} );
}(jQuery));
