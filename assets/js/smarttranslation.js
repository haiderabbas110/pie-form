(function($) {
    
    var google_translate_api_key          = $( '#pf_google_translate_api_key' ).parents( 'tr' ).eq( 0 ),
        deepl_key_type                    = $( '#pf_deepl_key_type' ).parents( 'tr' ).eq( 0 ),
        deepl_api_key                     = $( '#pf_deepl_api_key' ).parents( 'tr' ).eq( 0 ),
        pf_select_translator              = $( 'input#pf_select_translator:checked' );
     
    // pf_default();
    console.log(pf_select_translator);
    if(pf_select_translator.val() === 'google_translate'){
        pf_google_translate();
    }else if(pf_select_translator.val() === 'deepl'){
        pf_deepl();
    }

    $( 'input#pf_select_translator' ).change(function(){
        pf_default();
        if($(this).val() == 'google_translate'){
            pf_google_translate();
        }else if($(this).val() == 'deepl'){
            pf_deepl();
        }
    });

    function pf_google_translate(){
        google_translate_api_key.show();
        deepl_api_key.hide();
        deepl_key_type.hide();
    }  

    function pf_deepl(){
        google_translate_api_key.hide();
        deepl_api_key.show();
        deepl_key_type.show();
    }

    function pf_default(){
        google_translate_api_key.hide();
        deepl_api_key.hide();
        deepl_key_type.hide();
    }

})( jQuery );