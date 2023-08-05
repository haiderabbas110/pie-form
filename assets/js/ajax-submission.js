/* global pf_data */
jQuery( function( $ ) {
	'use strict';

		var form = $( 'form[data-ajax_submission="1"]' );

		form.each( function( i, v ) {
			$( document ).ready( function() {
				var formTuple = $( v ),
					btn = formTuple.find( '.pie-submit' );

				
				btn.on( 'click', function( e ) {
					
					var data = formTuple.serializeArray();
					e.preventDefault();

					// We let the bubbling events in form play itself out.
					formTuple.trigger( 'focusout' ).trigger( 'change' ).trigger( 'submit' );

					var errors = formTuple.find( '.pie-error:visible' );

					if ( errors.length > 0 ) {
						/* $( [document.documentElement, document.body] ).animate({
							scrollTop: errors.last().offset().top - 70
						}, 800 ); */
						return;
					}
					// // Change the text to user defined property.
					// if(formTuple.data( 'process-text-type' ) === 'icon'){
					// 	$( this ).html( formTuple.data( 'process-text' ) );
					// }else{
					// 	$( this ).text( formTuple.data( 'process-text' ) );
					// }
					$( this ).html( formTuple.data( 'process-text' ) );
					// Add action intend for ajax_form_submission endpoint.
					data.push({
						name: 'action',
						value: 'pie_forms_ajax_form_submission'
					});
					data.push({
						name: 'security',
						value: pf_data.pf_ajax_submission
					});
					
					// Fire the ajax request.
					$.ajax({
						url: pf_data.ajax_url,
						type: 'POST',
						data: data
					})
					.done( function ( xhr, textStatus, errorThrown ) {
						
						var redirect_url = ( xhr.data && xhr.data.redirect_url ) ? xhr.data.redirect_url : '';
						var download_url = ( xhr.data && xhr.data.download_url ) ? xhr.data.download_url : '';

						
						if ( redirect_url ) {
							formTuple.trigger( 'reset' );
								window.location = redirect_url;
							return;
						}
						if ( 'success' === xhr.data.response || true === xhr.success ) {
							if(formTuple.attr('data-message_popup') === '1'){
								var message = '<img class="pf-success-check" src="'+pf_data.base_url+'/assets/images/check.png" alt="success-message">';
								 message += xhr.data.message;
								$.dialog({
									title: false,
									content: message ,
									theme: 'material,pieforms-submission-popup',
									keyboardEnabled:true,
								});
							}else{
								formTuple.closest( '.pie-forms' ).html( xhr.data.message ).focus();
							}
							
							formTuple.trigger( 'reset' );
							var submit_button = $('#pie-submit-'+formTuple.data('formid')+'');

							// // Change the text to user defined property.
							// if(formTuple.data( 'process-text-type' ) === 'icon'){
							// 	submit_button.html( submit_button.data( 'submit-text' ) );
							// }else{
							// 	submit_button.text( submit_button.data( 'submit-text' ) );
							// }	
							submit_button.html( formTuple.data( 'process-text' ) );
							if($('.pf-success-msg').length > 0){
								var removeHeaderHeight = $('header').height();
									$( [document.documentElement, document.body] ).animate({
										scrollTop: $('.pf-success-msg').offset().top - removeHeaderHeight
									}, 800 );
								
								}

							if ( download_url ) {
								const link = document.createElement('a');
								link.href = download_url;
								// link.download = 'file';
								link.dispatchEvent(new MouseEvent('click'));
							}

						} else {
							var	form_id = formTuple.data( 'formid' ),
								error   =  pf_data.error,
								err     =  JSON.parse( errorThrown.responseText );

								if ( 'string' === typeof err.data.message ) {
									error =  err.data.message;
								}

								formTuple.closest( '.pie-forms' ).find( '.pie-forms-notice' ).remove();
								formTuple.closest( '.pie-forms' ).prepend( '<div class="pie-forms-notice pie-forms-notice--error" role="alert">'+ error  +'</div>' ).focus();


							btn.attr( 'disabled', false ).html( pf_data.submit );
						}
					})
					.fail( function () {
						btn.attr( 'disabled', false ).html( pf_data.submit );
						formTuple.trigger( 'focusout' ).trigger( 'change' );
						formTuple.closest( '.pie-forms' ).find( '.pie-forms-notice' ).remove();
						formTuple.closest( '.pie-forms' ).prepend( '<div class="pie-forms-notice pie-forms-notice--error" role="alert">'+ pf_data.error  +'</div>' ).focus();
					})
					.always( function( xhr ) {
						var redirect_url = ( xhr.data && xhr.data.redirect_url ) ? xhr.data.redirect_url : '';
						if ( ! redirect_url && $( '.pie-forms-notice' ).length ) {
							$( [document.documentElement, document.body] ).animate({
								scrollTop: $( '.pie-forms-notice' ).offset().top
							}, 800 );
						}
					});
				});
			});
		});

});
