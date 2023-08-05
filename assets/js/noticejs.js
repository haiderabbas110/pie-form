(function($) {

    $('#notice').slick({
        dots: false,
        infinite: false,
        arrows : false,
        autoplay : true,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
              dots: true
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
          // You can unslick at a given breakpoint now by adding:
          // settings: "unslick"
          // instead of a settings object
        ]
      });

    jQuery( '.mc_promo_users' ).on( 'click', '.notice-dismiss', function() {
        nonce_mc = jQuery(this).parent().data('mcuser');		
        var data = {
            action: 'dismiss_pf_notice_for_mailchimp',
            nonce: nonce_mc
        };
        jQuery.post( ajaxurl, data );
    });

    jQuery( '.zap_promo_users' ).on( 'click', '.notice-dismiss', function() {
        nonce_zap = jQuery(this).parent().data('zapuser');		
        var data = {
            action: 'dismiss_pf_notice_for_zapier',
            nonce: nonce_zap
        };
        jQuery.post( ajaxurl, data );
    });

    jQuery( '.pp_promo_users' ).on( 'click', '.notice-dismiss', function() {
        nonce_pp = jQuery(this).parent().data('ppuser');		
        var data = {
            action: 'dismiss_pf_notice_for_paypal',
            nonce: nonce_pp
        };
        jQuery.post( ajaxurl, data );
    });

    jQuery( '.st_promo_users' ).on( 'click', '.notice-dismiss', function() {
        nonce_st = jQuery(this).parent().data('stuser');		
        var data = {
            action: 'dismiss_pf_notice_for_stripe',
            nonce: nonce_st
        };
        jQuery.post( ajaxurl, data );
    });

    jQuery( '.smt_promo_users' ).on( 'click', '.notice-dismiss', function() {
        nonce_smt = jQuery(this).parent().data('smtuser');		
        var data = {
            action: 'dismiss_pf_notice_for_smart',
            nonce: nonce_smt
        };
        jQuery.post( ajaxurl, data );
    });

})( jQuery );