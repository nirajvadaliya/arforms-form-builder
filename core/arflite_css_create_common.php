<?php

if( !empty( $loaded_field ) ){

    if( in_array('html', $loaded_field) ){
        echo "{$arf_form_cls_prefix} .arfmainformfield .arf_htmlfield_control{";
            echo "color:{$field_label_txt_color};";
            echo "word-wrap: break-word;";
        echo "}";
    }

    if( in_array( 'phone', $loaded_field ) ){
        echo "{$arf_form_cls_prefix} .iti__country-list .iti__country-name{
            font-family:{$arf_input_font_family};
            font-size:{$arf_input_font_size}px;
            {$arf_input_font_style_str}
        }";
        
        echo "{$arf_form_cls_prefix} .arf_field_type_phone ul#country-listbox {
            list-style-type: none !important;
            z-index: 9999;
            padding: 0 !important;
            margin:0;
        }";
        
        echo "{$arf_form_cls_prefix} .arf_active_phone_utils .controls{ z-index:2 !important; }";
    }
}