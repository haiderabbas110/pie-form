(function($) {

$(window).on('load',function(){
      /**
       * Setup actions.
       */
      var pf_setup_actions = {
        init: function() {
          // Template actions.
          $( document.body ).on( 'click', '.element-item.premium .upgrade-modal', this.message_upgrade );
          $( document.body ).on( 'click', '.element-item.quiz .upgrade-modal', this.quiz_addon_message_upgrade );
      
        },
        message_upgrade: function( e ) {
          var templateName = $( this ).parent().parent().parent().data('template');
    
          e.preventDefault();
    
          $.alert( {
            title: templateName + ' ' + pf_ajax_object.upgrade_title,
            theme: 'jconfirm-modern',
            icon: 'dashicons dashicons-lock',
            content: pf_ajax_object.upgrade_message,
            type: 'red',
            boxWidth: '565px',
            buttons: {
              confirm: {
                text: pf_ajax_object.upgrade_button,
                btnClass: 'btn-confirm',
                keys: ['enter'],
                action: function () {
                  window.open( pf_ajax_object.upgrade_url, '_blank' );
                }
              },
              cancel: {
                text: pf_ajax_object.i18n_ok
              }
            }
          } );
        },
        quiz_addon_message_upgrade: function( e ) {
          var templateName = $( this ).parent().parent().parent().data('template');
          console.log(pf_ajax_object);
          e.preventDefault();
    
          $.alert( {
            title: templateName + ' ' + pf_ajax_object.addons_quiz_upgrade_title,
            theme: 'jconfirm-modern',
            icon: 'dashicons dashicons-lock',
            content: pf_ajax_object.addons_quiz_upgrade_message,
            type: 'red',
            boxWidth: '565px',
            buttons: {
              confirm: {
                text: pf_ajax_object.addons_quiz_upgrade_button,
                btnClass: 'btn-confirm',
                keys: ['enter'],
                action: function () {
                  window.open( pf_ajax_object.addons_quiz_upgrade_url, '_blank' );
                }
              },
              cancel: {
                text: pf_ajax_object.i18n_ok
              }
            }
          } );
        }
      }
      pf_setup_actions.init();

      var $grid = $('.element-grid').isotope({
        itemSelector: '.element-item',
        layoutMode: 'fitRows',
        columnWidth: 300,
        isFitWidth: true
      });
      // filter functions
      var filterFns = {
        // show if number is greater than 50
        numberGreaterThan50: function() {
          var number = $(this).find('.number').text();
          return parseInt( number, 10 ) > 50;
        },
        // show if name ends with -ium
        ium: function() {
          var name = $(this).find('.name').text();
          return name.match( /ium$/ );
        }
      };
      // bind filter button click
      $('.filters-button-group').on( 'click', 'button', function() {
        var filterValue = $( this ).attr('data-filter');
        // use filterFn if matches value
        filterValue = filterFns[ filterValue ] || filterValue;
        $grid.isotope({ filter: filterValue });
      });
      // change is-checked class on buttons
      $('.button-group').each( function( i, buttonGroup ) {
        var $buttonGroup = $( buttonGroup );
        $buttonGroup.on( 'click', 'button', function() {
          $buttonGroup.find('.is-checked').removeClass('is-checked');
          $( this ).addClass('is-checked');
        });
      });

});

//JCONFIRM ALERT
$( document ).on( 'click', '.element-item.free .getting-start ,#quiz_active .getting-start', function(e) {
    var template  = $(this).parents('.element-item').data('template');
      $.confirm({
        title: 'Create Form',
        boxWidth: '500px',
        useBootstrap: false,
        content: '' +
        '<form action="post" class="pie-form-addnew">' +
          '<div class="form-group">' +
          '<input type="text" placeholder="Enter a form name" class="name form-control" required />' +
          '</div>' +
        '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-red',
                action: function () {
                    var name = this.$content.find('.name').val();
                    if(!name){
                        $.alert('provide a valid name');
                        return false;
                    }

                    //ADD NEW AJAX
                    $.ajax({
                      type : "POST",
                      url : pf_ajax_object.ajax_url,
                      data : {action: "pie_forms_create_form",form_name: name,form_template:template, nonce:pf_ajax_object.nonce },
                      dataType : "json",
                      success: function ( form_id ) {  
                        
                        location.href = "?page=pie-forms&form_id="+form_id;
                      }
                   });

                }
            },
            cancel: function () {
                //close
            },
        },
        onContentReady: function () {
            // bind to events
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                // if the user submits the form by pressing enter in the field.
                e.preventDefault();
                jc.$$formSubmit.trigger('click'); // reference the button and click it
            });
        }
    });
});
  
})( jQuery );
