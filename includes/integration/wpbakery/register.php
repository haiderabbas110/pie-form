<?php

class PFORM_Integration_WPbakery_Register extends WPBakeryShortCode {
    function __construct(){

        // add_action( 'admin_init', array( $this, 'mapping' ) );
        vc_add_shortcode_param( 'search_select',array($this, 'search_select') );
        add_action( 'wp_loaded', array( $this, 'mapping' ) );
        add_shortcode('pieforms_for_wpbakery',array($this,'shortcode_html'));

    }
    function search_select( $param, $value ) {
        $param_line = '';
        $param_line .= '<select name="'. esc_attr( $param['param_name'] ).'" class="vc-search-select wpb_vc_param_value wpb-input wpb-select'. esc_attr( $param['param_name'] ).' '. esc_attr($param['type']).'">';

        foreach ( $param['value'] as $val => $text_val ) {

                $text_val = __($text_val, "js_composer");
                $selected = '';

                if(!is_array($value)) {
                    $param_value_arr = explode(',',$value);
                } else {
                    $param_value_arr = $value;
                }

                if ($value!=='' && in_array($val, $param_value_arr)) {
                    $selected = ' selected="selected"';
                }
                $param_line .= '<option value="'.esc_attr($val).'"'.esc_html($selected).'>'.esc_html($text_val).'</option>';
            }
        $param_line .= '</select>';
        $param_line .= '<script>
            jQuery(document).ready(function() {
                jQuery(".vc-search-select").select2();
            });
        </script>';
                
        return  $param_line;
    }
    function mapping(){

        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }
        $forms_id = [];
        // if (is_plugin_active('pie-forms-for-wp/pie-forms-for-wp.php') || is_plugin_active('pie-forms-for-wp-premium/pie-forms-for-wp-premium.php') ) {
            $forms = Pie_Forms()->core()->pform_get_all_forms();
            foreach($forms as $key => $value){
                $forms_id[$key] = $value;
            }
        // }
        // Map the block with vc_map()
        vc_map(
            array(
                'name'          => __('Pie Forms', 'pie-forms'),
                'base'          => 'pieforms_for_wpbakery',
                'description'   => __('The Easiest Drag and Drop WordPress Form Plugin', 'pie-forms'),
                'category'      => __('Pie Forms', 'pie-forms'),
                'icon'          => Pie_Forms::$url . 'assets/images/wpbakery/pie-forms-element.png',
                'params'        => array(
                    array(
                        "type"          => "search_select",
                        "heading"       => esc_html__("Forms", 'pie-forms'),
                        "param_name"    => "form_id",
                        "value"         =>  absint($forms_id),
                    ),
                ),
            )
        );
    }

    function shortcode_html($atts, $content = null){

        extract( shortcode_atts( array(
            'form_id'   =>  '',
        ), $atts ) );
 
        $output = '<div>';
            $output .= do_shortcode("[pie_form id='".absint($form_id)."']");
        $output .= '</div>';
        return $output;
    }
}
