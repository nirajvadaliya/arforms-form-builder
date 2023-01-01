<?php
	global $current_user, $arfliteformhelper,$arflite_installed_field_types,$arfliterecordcontroller,$arfliteformcontroller;
	$browser_info = $arfliterecordcontroller->arflitegetBrowser( $_SERVER['HTTP_USER_AGENT'] );
	$allowed_html = arflite_retrieve_attrs_for_wp_kses();
	@ini_set( 'max_execution_time', 0 );

	global $arfliteformcontroller;
?>

<div class="wrap arfforms_page arf_imortexport arf_imortexport_page_wrap">

	<div class="top_bar">
		<span class="h2"><?php echo  __( 'Import / Export Forms', 'arforms-form-builder' ) ; ?></span>
	</div>
	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div class="inside">
				<div class="frm_settings_form ">
					<?php
					if ( isset( $_REQUEST['arf_import_btn'] ) && current_user_can( 'arfchangesettings' ) ) {
						@ini_set( 'max_execution_time', 0 );

						$upload_dir    = ARFLITE_UPLOAD_DIR  . '/css/';
						$main_css_dir  = ARFLITE_UPLOAD_DIR . '/maincss/';


						$xml = html_entity_decode( base64_decode( sanitize_text_field( $_REQUEST['arf_import_textarea'] ) ) );

						$outside_fields = apply_filters( 'arflite_installed_fields_outside', $arflite_installed_field_types );

						libxml_use_internal_errors( true );

						$xml = simplexml_load_string( $xml );

						if ( $xml === false ) {
							$xml = base64_decode( sanitize_text_field( $_REQUEST['arf_import_textarea'] ) );

							$outside_fields = apply_filters( 'arflite_installed_fields_outside', $arflite_installed_field_types );

							libxml_use_internal_errors( true );

							$xml = simplexml_load_string( $xml );
						}

						$invalid_file_ext = array( "php", "php3", "php4", "php5", "pl", "py", "jsp", "asp", "exe", "cgi" );
                        $valid_file_ext = array("jpg", "png", "gif", "jpeg", "svg", "webp");

                        if( !isset( $_REQUEST['arflite_import_form_nonce'] ) || ( isset( $_REQUEST['arflite_import_form_nonce'] ) && !wp_verify_nonce( $_REQUEST['arflite_import_form_nonce'], 'arflite_import_form' ) ) ){

	                        ?>
	                        	<div id="error_message" class="arf_error_message" data-id="arflite_import_export_error_msg">
									<div class="message_descripiton">
										<div class="arffloatmargin" id=""><?php echo  __( 'Sorry, You are not an authorized person to perform this action.', 'arforms-form-builder' ) ; ?></div>
										<div class="message_svg_icon">
											<svg class="arfheightwidth14"><path fill-rule="evenodd" clip-rule="evenodd" fill="#ffffff" d="M10.702,10.909L6.453,6.66l-4.249,4.249L1.143,9.848l4.249-4.249L1.154,1.361l1.062-1.061l4.237,4.237l4.238-4.237l1.061,1.061L7.513,5.599l4.249,4.249L10.702,10.909z"></path></svg>
										</div>
									</div>
								</div>
	                        <?php

                        } else {

							global $arflitefield, $arfliteform, $ARFLiteMdlDb, $wpdb, $WP_Filesystem, $arflitemainhelper, $arflitefieldhelper, $arfliteformhelper, $arflitesettingcontroller, $arfliterecordmeta, $arflite_db_record, $arflitesettings;
							if ( isset( $xml->arformslite ) ) {

								$ik = 0;
								foreach ( $xml->children() as $formxml ) {
									foreach ( $formxml->children() as $key_main => $val_main ) {
										$attr                         = $val_main->attributes();
										$old_form_id                  = $attr['id'];
										$submit_bg_img_fnm            = '';
										$arfmainform_bg_img_fnm       = '';
										$arfmainform_bg_hover_img_fnm = '';

										$submit_bg_img             = trim( $val_main->submit_bg_img );
										$arfmainform_bg_img        = trim( $val_main->arfmainform_bg_img );
										$submit_hover_bg_img       = trim( $val_main->submit_hover_bg_img );
										$xml_arf_version           = trim( $val_main->arf_db_version );
										$exported_site_uploads_dir = trim( $val_main->exported_site_uploads_dir );

										$imageupload_dir = ARFLITE_UPLOAD_DIR . '/';

										$imageupload_url = ARFLITE_UPLOAD_URL . '/';

										if ( $submit_bg_img != '' ) {
											$submit_bg_img_filenm = basename( $submit_bg_img );

											$submit_bg_img_fnm = time() . '_' . $ik . '_' . $submit_bg_img_filenm;
											$ik++;

											$submit_btn_img_ext = explode( '.', $submit_bg_img );
	                                        $file_ext = end( $submit_btn_img_ext );
	                                        if( !in_array( $file_ext, $invalid_file_ext ) && in_array( $file_ext, $valid_file_ext ) ){
												if ( !$arfliteformcontroller->arflite_upload_file_function( $submit_bg_img, $imageupload_dir . $submit_bg_img_fnm ) ) {
													$submit_bg_img_fnm = '';
												}
											}
										}
										
										if ( $arfmainform_bg_img != '' ) {
											$arfmainform_bg_img_filenm = basename( $arfmainform_bg_img );

											$arfmainform_bg_img_fnm = time() . '_' . $ik . '_' . $arfmainform_bg_img_filenm;
											$ik++;


                                            $arflitefilecontroller = new arflitefilecontroller( $arfmainform_bg_img, true );

                                            $arflitefilecontroller->check_cap = true;
                                            $arflitefilecontroller->capabilities = array( 'arfchangesettings' );

                                            $arflitefilecontroller->check_nonce = true;
                                            $arflitefilecontroller->nonce_action = 'arflite_import_form';
                                            $arflitefilecontroller->nonce_data = isset( $_REQUEST['arflite_import_form_nonce'] ) ? sanitize_text_field( $_REQUEST['arflite_import_form_nonce'] ) : '';

                                            $arflitefilecontroller->check_only_image = true;

                                            $destination = $imageupload_dir . $arfmainform_bg_img_fnm;

                                            if( ! $arflitefilecontroller->arflite_process_upload( $destination ) ){
                                                $arfmainform_bg_img_fnm = '';
                                            }
										}
										if ( $submit_hover_bg_img != '' ) {
											$submit_hover_bg_img_filenm = basename( $submit_hover_bg_img );


											$arfmainform_bg_hover_img_fnm = time() . '_' . $ik . '_' . $submit_hover_bg_img_filenm;
											$ik++;

											$arflitefilecontroller = new arflitefilecontroller( $submit_hover_bg_img, true );

                                            $arflitefilecontroller->check_cap = true;
                                            $arflitefilecontroller->capabilities = array( 'arfchangesettings' );

                                            $arflitefilecontroller->check_nonce = true;
                                            $arflitefilecontroller->nonce_action = 'arflite_import_form';
                                            $arflitefilecontroller->nonce_data = isset( $_REQUEST['arflite_import_form_nonce'] ) ? sanitize_text_field( $_REQUEST['arflite_import_form_nonce'] ) : '';

                                            $arflitefilecontroller->check_only_image = true;

                                            $destination = $imageupload_dir . $arfmainform_bg_hover_img_fnm;

                                            if( ! $arflitefilecontroller->arflite_process_upload( $destination ) ){
                                                $arfmainform_bg_hover_img_fnm = '';
                                            }

										}
										
										$val                          = '';
										$old_field_orders             = $new_field_order = array();
										$old_field_resize_width       = $new_field_resize_width = array();
										$old_field_order_type         = $new_field_order_type = array();
										foreach ( $val_main->general_options->children() as $key => $val ) {
											if ( $key == 'options' ) {
												$options_arr = '';
												$options_key = '';
												$options_val = '';
												unset( $option_arr_new );
												$option_string = '';

												$options_arr = arflite_json_decode( trim( $val ), true );

												if ( ! is_array( $options_arr ) ) {
													$options_arr = json_decode( $options_arr, true );
												}


												foreach ( $options_arr as $options_key => $options_val ) {
													if ( ! is_array( $options_val ) ) {
														$options_val = str_replace( '[ENTERKEY]', '<br>', $options_val );
														$options_val = str_replace( '[AND]', '&', $options_val );
													}

													if ( $options_key == 'before_html' ) {
														$option_arr_new[ $options_key ] = $arfliteformhelper->arflite_get_default_html( 'before' );
													} elseif ( $options_key == 'ar_email_subject' ) {
														 $_SESSION['ar_email_subject_org']  = $options_val;
														$option_arr_new[ $options_key ]   = $options_val;
													} elseif ( $options_key == 'ar_email_message' ) {
														 $_SESSION['ar_email_message_org']  = $options_val;
														$option_arr_new[ $options_key ]   = $options_val;
													} elseif ( $options_key == 'ar_admin_email_message' ) {
														 $_SESSION['ar_admin_email_message_org']  = $options_val;
														$option_arr_new[ $options_key ]  = $options_val;
													} elseif ( $options_key == 'ar_email_to' ) {
														 $_SESSION['ar_email_to_org']     = $options_val;
														$option_arr_new[ $options_key ] = $options_val;
													} elseif ( $options_key == 'ar_admin_from_email' ) {
														 $_SESSION['ar_admin_from_email']  = $options_val;
														$option_arr_new[ $options_key ]  = $options_val;
													} elseif ( $options_key == 'ar_user_from_email' ) {
														 $_SESSION['ar_user_from_email']  = $options_val;
														$option_arr_new[ $options_key ] = $options_val;
													} elseif ( $options_key == 'ar_admin_from_name' ) {
														 $_SESSION['arf_admin_from_name']  = $options_val;
														$option_arr_new[ $options_key ]  = $options_val;
													} elseif ( $options_key == 'admin_email_subject' ) {
														 $_SESSION['admin_email_subject']  = $options_val;
														$option_arr_new[ $options_key ]  = $options_val;
													} elseif ( $options_key == 'reply_to' ) {
														 $_SESSION['reply_to']   = $options_val;
														$option_arr_new[ $options_key ] = $options_val;
													} elseif ( $options_key == 'arf_pre_dup_field' ) {
														 $_SESSION['arf_pre_dup_field']  = $options_val;
														$option_arr_new[ $options_key ] = $options_val;
													} elseif ( $options_key == 'arf_field_order' ) {
														$old_field_orders               = json_decode( $options_val, true );
														$option_arr_new[ $options_key ] = $options_val;
													} elseif ( $options_key == 'arf_field_resize_width' ) {
														$option_arr_new[ $options_key ] = $options_val;
														$old_field_resize_width         = json_decode( $options_val, true );
													} else {
														$option_arr_new[ $options_key ] = $options_val;
													}
												}
												$option_string = maybe_serialize( $option_arr_new );

												$general_option[ $key ] = $option_string;

												$general_op = $option_string;
											} elseif ( $key == 'form_css' ) {
												$form_css_arr = arflite_json_decode( trim( $val ), true );

												if ( ! isset( $form_css_arr['prefix_suffix_bg_color'] ) || $form_css_arr['prefix_suffix_bg_color'] == '' ) {
													$form_css_arr['prefix_suffix_bg_color'] = '#e7e8ec';
												}

												if ( ! isset( $form_css_arr['prefix_suffix_icon_color'] ) || $form_css_arr['prefix_suffix_icon_color'] == '' ) {
													$form_css_arr['prefix_suffix_icon_color'] = '#808080';
												}

												if ( ! isset( $form_css_arr['arfsubmitboxxoffsetsetting'] ) || $form_css_arr['arfsubmitboxxoffsetsetting'] == '' ) {
													$form_css_arr['arfsubmitboxxoffsetsetting'] = '1';
												}

												if ( ! isset( $form_css_arr['arfsubmitboxyoffsetsetting'] ) || $form_css_arr['arfsubmitboxyoffsetsetting'] == '' ) {
													$form_css_arr['arfsubmitboxyoffsetsetting'] = '2';
												}

												if ( ! isset( $form_css_arr['arfsubmitboxblursetting'] ) || $form_css_arr['arfsubmitboxblursetting'] == '' ) {
													$form_css_arr['arfsubmitboxblursetting'] = '3';
												}

												if ( ! isset( $form_css_arr['arfsubmitboxshadowsetting'] ) || $form_css_arr['arfsubmitboxshadowsetting'] == '' ) {
													$form_css_arr['arfsubmitboxshadowsetting'] = '0';
												}

												foreach ( $form_css_arr as $form_css_key => $form_css_val ) {
													if ( $form_css_key == 'submit_bg_img' ) {
														if ( $submit_bg_img_fnm == '' ) {
															$form_css_arr_new['submit_bg_img']    = '';
															$form_css_arr_new_db['submit_bg_img'] = '';
														} else {


															$form_css_arr_new['submit_bg_img']    = $imageupload_url . $submit_bg_img_fnm;
															$form_css_arr_new_db['submit_bg_img'] = $imageupload_url . $submit_bg_img_fnm;
														}
													} elseif ( $form_css_key == 'arfmainform_bg_img' ) {
														if ( $arfmainform_bg_img_fnm == '' ) {
															$form_css_arr_new[ $form_css_key ]    = '';
															$form_css_arr_new_db[ $form_css_key ] = '';
														} else {

															$form_css_arr_new[ $form_css_key ]    = $imageupload_url . $arfmainform_bg_img_fnm;
															$form_css_arr_new_db[ $form_css_key ] = $imageupload_url . $arfmainform_bg_img_fnm;
														}
													} elseif ( $form_css_key == 'submit_hover_bg_img' ) {
														if ( $arfmainform_bg_hover_img_fnm == '' ) {
															$form_css_arr_new[ $form_css_key ]    = '';
															$form_css_arr_new_db[ $form_css_key ] = '';
														} else {

															$form_css_arr_new[ $form_css_key ]    = $imageupload_url . $arfmainform_bg_hover_img_fnm;
															$form_css_arr_new_db[ $form_css_key ] = $imageupload_url . $arfmainform_bg_hover_img_fnm;
														}
													} elseif ( $form_css_key == 'arf_checked_checkbox_icon' || $form_css_key == 'arf_checked_radio_icon' ) {
														$form_css_arr_new[ $form_css_key ]    = $arflitemainhelper->arflite_update_fa_font_class( $form_css_val );
														$form_css_arr_new_db[ $form_css_key ] = $arflitemainhelper->arflite_update_fa_font_class( $form_css_val );
													} else {
														$form_css_arr_new[ $form_css_key ]    = $form_css_val;
														$form_css_arr_new_db[ $form_css_key ] = $form_css_val;
													}
												}

												$final_val                      = maybe_serialize( $form_css_arr_new );
												$final_val_db                   = maybe_serialize( $form_css_arr_new_db );
												$general_option[ $key ]         = $final_val;
												$general_option[ $key . '_db' ] = $final_val_db;
											} else {
												$general_option[ $key ] = trim( $val );
											}
										}
										
										$general_option['is_importform'] = 'Yes';
										
										$general_option['form_key'] = '';
										unset( $general_option['id'] );
										$form_id = $arfliteform->arflitecreate( $general_option );

										$cssoptions = $general_option['form_css'];

										$cssoptions_db = $general_option['form_css_db'];


										$type_array    = array();
										$content_array = array();
										$value_array   = array();
										$new_id_array  = array();
										$allfieldstype = array();
										$allfieldsarr  = array();
										$i             = 0;

										$is_checkbox_img_enable = 0;
										$is_radio_img_enable 	= 0;
										$is_prefix_suffix_enable = false;

										foreach ( $val_main->fields->children() as $key_fields => $val_fields ) {

											if ( ! in_array( $val_fields->type, $outside_fields ) ) {
												continue;
											}

											$fields_option = array();

											foreach ( $val_fields as $key_field => $val_field ) {

												if ( $key_field == 'form_id' ) {
													$fields_option[ $key_field ] = $form_id;
												} elseif ( $key_field == 'field_key' ) {
													
                                            } else if ($key_field == 'options' && ( $val_fields->type == 'radio' || $val_fields->type == 'checkbox' ) ) {

													if ( ! is_array( $val_field ) ) {

														$temp_radio_val = stripslashes( trim( $val_field ) );
														$temp_radio_val = rtrim( $temp_radio_val, '"' );
														$temp_radio_val = ltrim( $temp_radio_val, '"' );

														$val_field_radio = json_decode( trim( $temp_radio_val ), true );
														if ( json_last_error() != JSON_ERROR_NONE ) {
															$val_field_radio = maybe_unserialize( trim( $val_field ) );
														}
													}

													if ( is_array( $val_field_radio ) ) {
														foreach ( $val_field_radio as $key => $value ) {
															$image_path = '';
															if ( is_array( $value ) ) {
																if ( isset( $value['label_image'] ) && $value['label_image'] != '' ) {
																	$image_path = $value['label_image'];

																	$arflitefilecontroller = new arflitefilecontroller( $image_path, true );

	                                                                $arflitefilecontroller->check_cap = true;
	                                                                $arflitefilecontroller->capabilities = array( 'arfchangesettings' );

	                                                                $arflitefilecontroller->check_nonce = true;
	                                                                $arflitefilecontroller->nonce_action = 'arflite_import_form';
	                                                                $arflitefilecontroller->nonce_data = isset( $_REQUEST['arflite_import_form_nonce'] ) ? sanitize_text_field( $_REQUEST['arflite_import_form_nonce'] ) : '';

	                                                                $arflitefilecontroller->check_only_image = true;

	                                                                $destination = $imageupload_dir . $key . '_' . basename($image_path);

	                                                                if( ! $arflitefilecontroller->arflite_process_upload( $destination ) ){
	                                                                    $val_field_radio[$key]['label_image'] = '';
	                                                                } else {
	                                                                    $val_field_radio[$key]['label_image'] = $imageupload_url . $key . '_' . basename( $image_path );

	                                                                    if( $val_fields->type == 'radio' ){
	                                                                    	$is_radio_img_enable 	= true;
	                                                                    }elseif( $val_fields->type == 'checkbox' ){
	                                                                    	$is_checkbox_img_enable = true;
	                                                                    }
	                                                                }
																}
															}
														}
													}

													$fields_option[ $key_field ] = json_encode( $val_field_radio );
												} else {

													if ( $key_field == 'field_options' ) {

														$fields_option[ $key_field ] = trim( json_encode( arflite_json_decode( trim( $val_field ), true ) ) );

														$fields_option[ $key_field ] = str_replace( '[ENTERKEY]', '<br>', $fields_option[ $key_field ] );

													} elseif ( 'options' == $key_field ) {
														$temp_val                    = stripslashes( trim( $val_field ) );
														$temp_val                    = rtrim( $temp_val, '"' );
														$temp_val                    = ltrim( $temp_val, '"' );
														$fields_option[ $key_field ] = $temp_val;
													} else {
														$fields_option[ $key_field ] = trim( $val_field );
													}
												}
												$all_field_data = '';
												$field_name     = '';

												if ( isset( $fields_option['field_options'] ) ) {
													$all_field_data = arflite_json_decode( $fields_option['field_options'] );

													if ( isset( $all_field_data->name ) ) {
														$field_name           = str_replace( '[ENTERKEY]', ' ', $all_field_data->name );
														$all_field_data->name = $field_name;
													}
													$fields_option['field_options'] = trim( json_encode( $all_field_data ) );

												}
												if ( $key_field == 'field_options' ) {
													$arf_field_options = arflite_json_decode( trim( $fields_option[$key_field] ), true );

													if ( isset( $arf_field_options['arf_prefix_icon'] ) && $arf_field_options['arf_prefix_icon'] != '' ) {
														$arf_field_options['arf_prefix_icon'] = $arflitemainhelper->arflite_update_fa_font_class( $arf_field_options['arf_prefix_icon'] );

														$is_prefix_suffix_enable = true;
													}

													if ( isset( $arf_field_options['arf_suffix_icon'] ) && $arf_field_options['arf_suffix_icon'] != '' ) {
														$arf_field_options['arf_suffix_icon'] = $arflitemainhelper->arflite_update_fa_font_class( $arf_field_options['arf_suffix_icon'] );

														$is_prefix_suffix_enable = true;
													}

													if($val_fields->type == 'phone' && 1 == $arf_field_options['phonetype']){
														$is_prefix_suffix_enable = true;
													}
													$fields_option[ $key_field ] = trim( json_encode( $arf_field_options ) );
												}
											}

											$res_field_id                = $fields_option['id'];
											$type_array[ $res_field_id ] = $fields_option['type'];


											$new_field_id = $arflitefield->arflitecreate( $fields_option, true, true, $res_field_id );
											if ( $val_fields->type != 'html' ) {
												$new_id_array[ $i ]['old_id'] = $res_field_id;
												$new_id_array[ $i ]['new_id'] = $new_field_id;
												$new_id_array[ $i ]['name']   = $fields_option['name'];
												$new_id_array[ $i ]['type']   = $fields_option['type'];
											}
                                        if ($fields_option['type'] == 'html' ||  $fields_option['type'] == 'select' ) {
												$value_array                                    = json_decode( $fields_option['field_options'], true );
												$content_array[ $new_field_id ]['html_content'] = str_replace( '[ENTERKEY]', "\n", $value_array['description'] );
											}
											if ( $fields_option['type'] != 'hidden' ) {
												if ( isset( $old_field_orders[ $res_field_id ] ) ) {
													$new_field_order[ $new_field_id ]      = $old_field_orders[ $res_field_id ];
													$old_field_order_type[ $res_field_id ] = $fields_option['type'];
													$new_field_order_type[ $new_field_id ] = $fields_option['type'];
												}
											}

											$ar_email_subject = isset( $ar_email_subject ) ? $ar_email_subject : '';
											if ( $ar_email_subject == '' ) {
												$ar_email_subject = esc_html( $_SESSION['ar_email_subject_org'] );
											} else {
												$ar_email_subject = $ar_email_subject;
											}

											$ar_email_subject = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_email_subject );
											$ar_email_subject = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_email_subject, $res_field_id, $new_field_id );

											$ar_email_message = isset( $ar_email_message ) ? $ar_email_message : '';
											if ( $ar_email_message == '' ) {
												$ar_email_message = isset(  $_SESSION['ar_email_message_org']  ) ? wp_kses( $_SESSION['ar_email_message_org'], $allowed_html ) : '';
											} else {
												$ar_email_message = $ar_email_message;
											}

											$ar_email_message = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_email_message );
											$ar_email_message = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_email_message, $res_field_id, $new_field_id );

											$arf_pre_dup_field = isset( $arf_pre_dup_field ) ? $arf_pre_dup_field : '';
											if ( $arf_pre_dup_field == '' ) {
												$arf_pre_dup_field = isset( $_SESSION['arf_pre_dup_field']  ) ? esc_html( $_SESSION['arf_pre_dup_field'] ) : '';
											} else {
												$arf_pre_dup_field = $arf_pre_dup_field;
											}

											$arf_pre_dup_field = str_replace( $res_field_id, $new_field_id, $arf_pre_dup_field );


											$ar_admin_email_message = isset( $ar_admin_email_message ) ? $ar_admin_email_message : '';
											if ( $ar_admin_email_message == '' ) {
												$ar_admin_email_message = isset(  $_SESSION['ar_admin_email_message_org']  ) ? wp_kses( $_SESSION['ar_admin_email_message_org'], $allowed_html ): '';
											} else {
												$ar_admin_email_message = $ar_admin_email_message;
											}
											$ar_admin_email_message = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_admin_email_message );
											$ar_admin_email_message = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_admin_email_message, $res_field_id, $new_field_id );


											$ar_admin_from_name = isset( $ar_admin_from_name ) ? $ar_admin_from_name : '';
											if ( $ar_admin_from_name == '' ) {
												$ar_admin_from_name = isset( $_SESSION['arf_admin_from_name']  ) ? esc_html( $_SESSION['arf_admin_from_name'] ) : '';
											} else {
												$ar_admin_from_name = $ar_admin_from_name;
											}
											$ar_admin_from_name = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_admin_from_name );
											$ar_admin_from_name = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_admin_from_name, $res_field_id, $new_field_id );

											$admin_email_subject = isset( $admin_email_subject ) ? $admin_email_subject : '';
											if ( $admin_email_subject == '' ) {
												$admin_email_subject = isset(  $_SESSION['admin_email_subject']  ) ? esc_html( $_SESSION['admin_email_subject'] ): '';
											} else {
												$admin_email_subject = $admin_email_subject;
											}
											$admin_email_subject = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $admin_email_subject );
											$admin_email_subject = $arfliteformhelper->arflite_replace_field_shortcode_import( $admin_email_subject, $res_field_id, $new_field_id );


											$reply_to = isset( $reply_to ) ? $reply_to : '';
											if ( $reply_to == '' ) {
												$reply_to = isset(  $_SESSION['reply_to']  ) ? esc_html( $_SESSION['reply_to'] ) : '';
											} else {
												$reply_to = $reply_to;
											}
											$reply_to = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $reply_to );
											$reply_to = $arfliteformhelper->arflite_replace_field_shortcode_import( $reply_to, $res_field_id, $new_field_id );

											$ar_email_to = isset( $ar_email_to ) ? $ar_email_to : '';
											if ( $ar_email_to == '' ) {
												$ar_email_to = isset(  $_SESSION['ar_email_to_org']  ) ? esc_html( $_SESSION['ar_email_to_org'] ) : '';
											} else {
												$ar_email_to = $ar_email_to;
											}

											$ar_admin_from_email = isset( $ar_admin_from_email ) ? $ar_admin_from_email : '';
											if ( $ar_admin_from_email == '' ) {
												$ar_admin_from_email = isset(  $_SESSION['ar_admin_from_email']  ) ? esc_html( $_SESSION['ar_admin_from_email'] ) : '';
											} else {
												$ar_admin_from_email = $ar_admin_from_email;
											}

											$ar_admin_from_email = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_admin_from_email );
											$ar_admin_from_email = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_admin_from_email, $res_field_id, $new_field_id );

											$ar_user_from_email = isset( $ar_user_from_email ) ? $ar_user_from_email : '';
											if ( $ar_user_from_email == '' ) {
												$ar_user_from_email = isset( $_SESSION['ar_user_from_email'] ) ? esc_html( $_SESSION['ar_user_from_email'] ) : '';
											} else {
												$ar_user_from_email = $ar_user_from_email;
											}

											$ar_user_from_email = str_replace( '[' . $res_field_id . ']', '[' . $new_field_id . ']', $ar_user_from_email );
											$ar_user_from_email = $arfliteformhelper->arflite_replace_field_shortcode_import( $ar_user_from_email, $res_field_id, $new_field_id );

											unset( $field_values );
											$i++;
										}

										$result_diff = array_diff( $old_field_orders, $new_field_order );
										foreach ( $result_diff as $key => $value ) {
											$new_field_order[ $key ] = $value;
										}


										$result_type_diff = array_diff( $old_field_order_type, $new_field_order_type );
										foreach ( $result_type_diff as $key => $value ) {
											$new_field_order_type[ $key ] = $value;
										}
										$final_field_order       = array();
										$new_temp_field          = array();
										foreach ( $new_field_order as $key => $value ) {
											if ( strpos( $key, '_confirm' ) !== false ) {

												$field_ext_extract                         = explode( '_', $key );
												$old_value                                 = $old_field_orders[ $field_ext_extract[0] ];
												$new_id                                    = array_search( $old_value, $new_field_order );
												$final_field_order[ $new_id . '_confirm' ] = $value;
												$fleld_data_confirm                        = $wpdb->get_results( $wpdb->prepare( 'SELECT field_options FROM ' . $ARFLiteMdlDb->fields . ' WHERE id=%d', $new_id ) );
												$fleld_data_confirm_options                = json_decode( $fleld_data_confirm[0]->field_options, 1 );
												if ( $fleld_data_confirm_options['type'] == 'email' ) {
													$new_temp_field[ 'confirm_email_' . $new_id ]                        = array();
													$new_temp_field[ 'confirm_email_' . $new_id ]['key']                 = $fleld_data_confirm_options['key'];
													$new_temp_field[ 'confirm_email_' . $new_id ]['order']               = $value;
													$new_temp_field[ 'confirm_email_' . $new_id ]['parent_field_id']     = $new_id;
													$new_temp_field[ 'confirm_email_' . $new_id ]['confirm_inner_class'] = $fleld_data_confirm_options['confirm_email_inner_classes'];


												}
											} else {
												$final_field_order[ $key ] = $value;
											}
										}


										$getForm = $wpdb->get_results( $wpdb->prepare( 'SELECT options FROM `' . $ARFLiteMdlDb->forms . '` WHERE id = %d', $form_id ) );
										$formOpt = maybe_unserialize( $getForm[0]->options );

										$newOpt = maybe_unserialize( $general_option['options'] );

										$newOpt['arf_field_order']              = json_encode( $final_field_order );

										$general_option['options'] = maybe_serialize( $newOpt );

										$new_values = array();



										foreach ( maybe_unserialize( $cssoptions ) as $k => $v ) {
											if ( ( preg_match( '/color/', $k ) || in_array( $k, array( 'arferrorbgsetting', 'arferrorbordersetting', 'arferrortextsetting' ) ) ) && ! in_array( $k, array( 'arfcheckradiocolor' ) ) ) {
												$new_values[ $k ] = str_replace( '#', '', $v );
											} else {
												$new_values[ $k ] = $v;
											}
										}
										$new_values1 = maybe_serialize( $new_values );


										if ( ! empty( $new_values ) ) {
											$query_results = $wpdb->query( $wpdb->prepare( 'update ' . $ARFLiteMdlDb->forms . " set form_css = '%s' where id = '%d'", $cssoptions_db, $form_id ) );

											$use_saved = $saving = true;
											$arfssl    = ( is_ssl() ) ? 1 : 0;

											$loaded_field = $type_array;
											
											$filename  = ARFLITE_FORMPATH . '/core/arflite_css_create_main.php';

											$target_path   = ARFLITE_UPLOAD_DIR . '/maincss';

											$css = $warn = '/* WARNING: Any changes made to this file will be lost when your ARForms lite settings are updated */';

											$css .= "\n";
											if ( ob_get_length() ) {
												ob_end_flush();
											}

											ob_start();

											include $filename;

											$css .= ob_get_contents();

											ob_end_clean();



											$css     .= "\n " . $warn;
											$css_file = $target_path . '/maincss_' . $form_id . '.css';

											$css = str_replace( '##', '#', $css );
											if ( ! file_exists( $css_file ) ) {

												WP_Filesystem();
												global $wp_filesystem;
												$wp_filesystem->put_contents( $css_file, $css, 0777 );
											} elseif ( is_writable( $css_file ) ) {

												WP_Filesystem();
												global $wp_filesystem;
												$wp_filesystem->put_contents( $css_file, $css, 0777 );
											} else {
												$error =  __( 'File Not writable', 'arforms-form-builder' ) ;
											}

											$filename1 = ARFLITE_FORMPATH . '/core/arflite_css_create_materialize.php';

											$target_path1  = ARFLITE_UPLOAD_DIR . '/maincss';

											$css1 = $warn1 = '/* WARNING: Any changes made to this file will be lost when your ARForms lite settings are updated */';

											$css1 .= "\n";
											if ( ob_get_length() ) {
												ob_end_flush();
											}

											ob_start();

											include $filename1;

											$css1 .= ob_get_contents();

											ob_end_clean();



											$css1     .= "\n " . $warn1;
											$css_file1 = $target_path1 . '/maincss_materialize_' . $form_id . '.css';

											$css1 = str_replace( '##', '#', $css1 );
											if ( ! file_exists( $css_file1 ) ) {

												WP_Filesystem();
												global $wp_filesystem;
												$wp_filesystem->put_contents( $css_file1, $css1, 0777 );
											} elseif ( is_writable( $css_file1 ) ) {

												WP_Filesystem();
												global $wp_filesystem;
												$wp_filesystem->put_contents( $css_file1, $css1, 0777 );
											} else {
												$error =  __( 'File Not writable', 'arforms-form-builder' ) ;
											}
										} else {

											$query_results = true;
										}
										
										ob_start();

										$wpdb->update(
											$ARFLiteMdlDb->forms,
											array(
												'options'     => $general_option['options'],
												'temp_fields' => maybe_serialize( $new_temp_field ),
											),
											array( 'id' => $form_id )
										);

										$sel_rec = $wpdb->prepare( 'select options from ' . $ARFLiteMdlDb->forms . ' where id = %d', $form_id );

										$res_rec = $wpdb->get_results( $sel_rec, 'ARRAY_A' );

										$opt                     = $res_rec[0]['options'];
										$arf_formfield_other_css = $option_arr_new['arf_form_other_css'];
										foreach ( $new_id_array as $id_info_arr ) {
											$arf_formfield_other_css = stripslashes( str_replace( $id_info_arr['old_id'], $id_info_arr['new_id'], $arf_formfield_other_css ) );

											if ( $ar_email_to == $id_info_arr['old_id'] ) {
												$ar_email_to = $id_info_arr['new_id'];
											}
										}
										$arf_form_other_css = stripslashes( str_replace( $old_form_id, $form_id, $arf_formfield_other_css ) );
										$form_custom_css    = stripslashes( str_replace( $old_form_id, $form_id, $val_main->form_custom_css ) );

										$form_custom_css = str_replace( '[REPLACE_SITE_URL]', site_url(), $form_custom_css );

										$form_custom_css = str_replace( '[ENTERKEY]', '<br>', $form_custom_css );

										$option_arr_new = maybe_unserialize( $opt );

										$option_arr_new['form_custom_css'] = $form_custom_css;

										$option_arr_new['arf_form_other_css'] = $arf_form_other_css;

										$option_arr_new['ar_email_subject'] = isset( $ar_email_subject ) ? $ar_email_subject : '';

										$option_arr_new['ar_email_message'] = isset( $ar_email_message ) ? $ar_email_message : '';

										$option_arr_new['ar_admin_email_message'] = isset( $ar_admin_email_message ) ? $ar_admin_email_message : '';

										$option_arr_new['ar_email_to'] = isset( $ar_email_to ) ? $ar_email_to : '';

										$option_arr_new['ar_admin_from_email'] = isset( $ar_admin_from_email ) ? $ar_admin_from_email : '';

										$option_arr_new['ar_user_from_email'] = isset( $ar_user_from_email ) ? $ar_user_from_email : '';

										$option_arr_new['ar_admin_from_name'] = isset( $ar_admin_from_name ) ? $ar_admin_from_name : '';

										$option_arr_new['admin_email_subject'] = isset( $admin_email_subject ) ? $admin_email_subject : '';

										$option_arr_new['arf_pre_dup_field'] = isset( $arf_pre_dup_field ) ? $arf_pre_dup_field : '';

                                    $option_arr_new['reply_to'] = !empty( $reply_to ) ? $reply_to : '';

										if ( $val_main->site_url != site_url() ) {
											$option_arr_new['success_action'] = isset( $option_arr_new['success_action'] ) ? $option_arr_new['success_action'] : '';
											if ( $option_arr_new['success_action'] == 'page' ) {
												$option_arr_new['success_action'] = 'message';
											}
										}


										$option_arr_new = maybe_serialize( $option_arr_new );

										$wpdb->update( $ARFLiteMdlDb->forms, array( 'options' => $option_arr_new ), array( 'id' => $form_id ) );
										$frm_id = $form_id;
										global $wpdb, $ARFLiteMdlDb;

										if ( isset( $val_main->form_entries ) && count( $val_main->form_entries->children() ) > 0 ) {

											include_once ARFLITE_FORMPATH . '/js/filedrag/simple_image.php';
											global $user_ID, $wpdb;
											$entry_values            = array();
											$entry_values_new        = array();
											$vls                     = array();
											$entry_values['form_id'] = $frm_id;
											if ( $user_ID ) {
												$entry_values['user_id'] = $user_ID;
											}

											foreach ( $val_main->form_entries->children() as $key_fields => $val_fields ) {
												$entry_values['entry_key'] = $arflitemainhelper->arflite_get_unique_key( '', $ARFLiteMdlDb->entries, 'entry_key' );

												foreach ( $val_fields as $key_field => $val_field ) {


													$field_nm = str_replace( '_ARF_', ' ', (string) $val_field['field_label'] );
													$field_nm = str_replace( '_ARF_SLASH_', '/', $field_nm );

													if ( $field_nm == 'Browser' ) {
														$entry_values['browser_info'] = (string) $val_field;
													} elseif ( $field_nm == 'Country' ) {
														$entry_values['country'] = (string) $val_field;
													} elseif ( $field_nm == 'Created Date' ) {
														$entry_values['created_date'] = (string) $val_field;
													} elseif ( $field_nm == 'IP Address' ) {
														$entry_values['ip_address'] = (string) $val_field;
													} elseif ( $field_nm == 'Submit Type' ) {

														$vls['form_display_type'] = (string) trim( $val_field );
													} else {
														$field_data = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $ARFLiteMdlDb->fields . ' WHERE form_id = %d', $frm_id ) );
														foreach ( $field_data as $k => $v ) {

															if ( $v->name == $field_nm ) {
																$field_type  = $val_field->attributes();
																$entry_value = array();
															
																if ( strtolower( $field_type ) == 'checkbox' ) {
																	$values                                  = explode( '^|^', (string) $val_field );
																	$entry_values_new['item_meta'][ $v->id ] = array_map( 'trim', $values );
																} else {
																	$entry_values_new['item_meta'][ $v->id ] = (string) trim( $val_field );
																}
															}
														}
													}
													$referrerinfo                 = $arflitemainhelper->arflite_get_referer_info();
													$entry_values['browser_info'] = isset( $entry_values['browser_info'] ) ? $entry_values['browser_info'] : '';
													$entry_values['description']  = maybe_serialize(
														array(
															'browser'  => $entry_values['browser_info'],
															'referrer' => $referrerinfo,
														)
													);
												}

												$create_entry = true;
												if ( $create_entry ) {
													$query_results = $wpdb->insert( $ARFLiteMdlDb->entries, $entry_values );
												}
												if ( isset( $query_results ) && $query_results ) {
													$entry_id = $wpdb->insert_id;
													global $arflitesavedentries;
													$arflitesavedentries[] = (int) $entry_id;
													if ( isset( $vls['form_display_type'] ) && $vls['form_display_type'] != '' ) {
														global $wpdb;
														$arf_meta_insert = array(
															'entry_value' => sanitize_text_field( $vls['form_display_type'] ),
															'field_id' => intval( 0 ),
															'entry_id' => intval( $entry_id ),
															'created_date' => current_time( 'mysql' ),
														);
														$wpdb->insert( $wpdb->prefix . 'arf_entry_values', $arf_meta_insert, array( '%s', '%d', '%d', '%s' ) );

													}

													if ( isset( $entry_values_new['item_meta'] ) ) {
														$arfliterecordmeta->arflite_update_entry_metas( $entry_id, $entry_values_new['item_meta'] );
													}
												}
											}
										}
									}
								}
								?>
								<div id="success_message" class="arf_success_message" data-id="arflite_import_export_success_msg">
									<div class="message_descripiton">
										<div class="arffloatmargin"><?php echo  __( 'Form is imported successfully.', 'arforms-form-builder' ) ; ?></div>
										<div class="message_svg_icon">
											<svg class="arfheightwidth14"><path fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M6.075,14.407l-5.852-5.84l1.616-1.613l4.394,4.385L17.181,0.411
																								l1.616,1.613L6.392,14.407H6.075z"></path></svg>
										</div>
									</div>
								</div>
								<?php
							} else {
								?>
								<div id="error_message" class="arf_error_message" data-id="arflite_import_export_error_msg">
									<div class="message_descripiton">
										<div class="arffloatmargin" id=""><?php echo  __( 'File is not proper.', 'arforms-form-builder' ) ; ?></div>
										<div class="message_svg_icon">
											<svg class="arfheightwidth14"><path fill-rule="evenodd" clip-rule="evenodd" fill="#ffffff" d="M10.702,10.909L6.453,6.66l-4.249,4.249L1.143,9.848l4.249-4.249L1.154,1.361l1.062-1.061l4.237,4.237l4.238-4.237l1.061,1.061L7.513,5.599l4.249,4.249L10.702,10.909z"></path></svg>
										</div>
									</div>
								</div>

								<?php
							}
						}
					}
					?>




					<div class="arflite-clear-float"></div>
					<div class="modal-body arfexportformwrap">

						<div class="opt_export_div">
							<label class="opt_export_lbl"><span></span>
								<span class="lbltitle"><?php echo __( 'Export Form(s)', 'arforms-form-builder' ); ?>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<?php echo  __( 'Entries', 'arforms-form-builder' ) ; ?></span>
							</label>

							<span class="arf_helptip_container">
								<a href="https://www.arformsplugin.com/documentation/import-and-export-forms-with-arforms/" target="_blank" title="" class="fas fa-life-ring arf_adminhelp_icon">
									<svg width="30px" height="30px" viewBox="0 0 26 32" class="arfsvgposition arfhelptip tipso_style" data-tipso="Help" title="Help">
									<?php echo ARFLITE_LIFEBOUY_ICON; ?>
									</svg>

								</a>
							</span>
						</div>

						<div class="exportformseprater"></div>

						<div class="export_opt_part" id="export_opt_part">
							<?php $plugin_url_list = plugin_dir_url( __FILE__ ); ?>

							<form id="exportForm" onSubmit="return arflite_check_import_form_selected();" method="post">
								<input type="hidden" value="<?php echo site_url() . '/index.php?plugin=ARFormslite'; ?>" name="arflitescripturl_cus" id="arflitescripturl_cus" />
								<div id="export_forms" class="export_forms" >
									<div class="export_options" id="export_options">
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_submit_action arf_custom_radio" name="arflite_opt_export" id="arflite_opt_export_form" value="arflite_opt_export_form" checked="checked" />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arflite_opt_export_form"><?php echo  __( 'Form(s) Only', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_submit_action arf_custom_radio" name="arflite_opt_export" id="arflite_opt_export_entries" value="arflite_opt_export_entries" />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arflite_opt_export_entries"><?php echo  __( 'Entries Only', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper" style="<?php echo ( ! is_rtl() ) ? 'width:60%;' : ''; ?>">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_submit_action arf_custom_radio" name="arflite_opt_export" id="arflite_opt_export_both" value="arflite_opt_export_both" />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arflite_opt_export_both"><?php echo  __( 'Forms + Entries', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
									</div>

									<table class="form-table">
										
										<tr>
											<td colspan="2">
												<span class="lblsubtitle selection_msg lblnotetitle notelitespan">
													<?php echo __( 'Please Select Form', 'arforms-form-builder' ); ?>
												</span>

												<div class="arf_importselform">

													<?php $arfliteformhelper->arflite_forms_dropdown_new( 'frm_add_form_id', '', 'Select form', '', '', 'mutliple', 1, 1, 'arf_import_export_dropdown' ); ?>
												</div>
												<div id="arf_xml_select_form_error"><?php echo __( 'Please Select Form', 'arforms-form-builder' ); ?></div>
											</td>
										</tr>

										<tr class="display_form_entry_separator display-none-cls">
											<td colspan="2">
												<span class="lblsubtitle arfcsv-file-seprater">
													<?php echo  __( 'CSV File Separator', 'arforms-form-builder' ) ; ?>
												</span>

												<div class="arf_radio_wrapper">
													<div class="arf_custom_radio_div" >
														<div class="arf_custom_radio_wrapper">
															<input type="radio" name="arfexportentryseparator" id="arf_comma_separate" class="arf_submit_action arf_custom_radio" value="arf_comma"  <?php checked( get_option( 'arflite_form_entry_separator' ), 'arf_comma' ); ?>/>
															<svg width="18px" height="18px">
															<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
															<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
															</svg>
														</div>
													</div>
													<span>
														<label for="arf_comma_separate"><?php echo  __( 'Comma ( , )', 'arforms-form-builder' ) ; ?></label>
													</span>
												</div>
												<div class="arf_radio_wrapper">
													<div class="arf_custom_radio_div" >
														<div class="arf_custom_radio_wrapper">
															<input type="radio" name="arfexportentryseparator" id="arf_semicolon_separate" class="arf_submit_action arf_custom_radio" value="arf_semicolon" <?php checked( get_option( 'arflite_form_entry_separator' ), 'arf_semicolon' ); ?> />
															<svg width="18px" height="18px">
															<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
															<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
															</svg>
														</div>
													</div>
													<span>
														<label for="arf_semicolon_separate"><?php echo  __( 'Semicolon ( ; )', 'arforms-form-builder' ) ; ?></label>
													</span>
												</div>

												<div class="arf_radio_wrapper">
													<div class="arf_custom_radio_div" >
														<div class="arf_custom_radio_wrapper">
															<input type="radio" name="arfexportentryseparator" id="arf_pipe_separate" class="arf_submit_action arf_custom_radio" value="arf_pipe" <?php checked( get_option( 'arflite_form_entry_separator' ), 'arf_pipe' ); ?>/>
															<svg width="18px" height="18px">
															<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
															<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
															</svg>
														</div>
													</div>
													<span>
														<label for="arf_pipe_separate"><?php echo  __( 'Pipe ( | )', 'arforms-form-builder' ) ; ?></label>
													</span>
												</div>
											</td>

										</tr>
										<br>
										<tr>

											<td colspan="2" class="export-btn-td">
												<input type="hidden" id="arf_export_action" name="s_action" value="arflite_opt_export_form">
												<input name="export_button" type="submit" id="export_button" class="rounded_button arf_btn_dark_blue arfexportbtn" value="<?php echo  __( 'Export', 'arforms-form-builder' ) ; ?>">
											</td>
										</tr>
									</table>
							</form>

						</div>
						<br />
						<div class="import-export-seprater"></div>
						<br />
						<div class="arfimport-form-title">
							<div class="opt_import_div">
								<label class="arfimport-form-lbl"><span></span>
									<span class="lbltitle"><?php echo __( 'Import Form(s)', 'arforms-form-builder' ); ?></span>
								</label>
								<br /><br />
							</div>

						<div class="import_opt_part" id="import_opt_part">
							<form  action="" method="post" enctype="multipart/form-data" >
								<table class="form-table">
									<tr>
										<td colspan="2"><span class="lblsubtitle arfexporttitlespan"><?php echo  __( 'Exported File Content', 'arforms-form-builder' ); ?></span>

											<textarea id="arf_import_textarea" cols="100" rows="15" name="arf_import_textarea" class="txtmultimodal1 text_area_import_export_page export-form-textarea"></textarea>

											 <div class="arf_tooltip_main" ><img src="<?php echo ARFLITEIMAGESURL; ?>/tooltips-icon.png" alt="?" class="arfhelptip tipso_style arfexport-form-note" title="<?php echo  __( 'Please open your exported file, copy entire content & paste it here.', 'arforms-form-builder' ) ; ?>" data-tipso="<?php echo  __( 'Please open your exported file, copy entire content & paste it here.', 'arforms-form-builder' ) ; ?>"/></div>
											 <div class="arf_import_textarea_error_wrapper">
												<span id="arf_import_content_null" class="arf_importerr"><?php echo  __( 'Please enter content', 'arforms-form-builder' ) ; ?></span>
											 </div>
										</td>
									</tr>
									<tr class="blank-tr">
										<td></td>
										<td></td>
									</tr>
									<tr>
										<td class="arfimportbtntd">
											<input type="hidden" name="arf_xml_file_name" id="arf_xml_file_name" value="" /><input type="hidden" name="arf_import_disable" id="arf_import_disable" value="1" /><input type="hidden" name="arflite_import_form_nonce" value="<?php echo wp_create_nonce('arflite_import_form'); ?>" />

											<input type="submit" id="arf_import_btn" name="arf_import_btn"  class="rounded_button arf_btn_dark_blue arf_importbtn" value="<?php echo  __( 'Import', 'arforms-form-builder' ) ; ?>">&nbsp;&nbsp;<span id="import_loader"><img src="<?php echo ARFLITEURL . '/images/loading_299_1.gif'; ?>" height="15" /></span>

											</td>
										</tr>
									</table>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="documentation_link arflite-clear-float" align="right"><a href="https://www.arformsplugin.com/documentation/1-getting-started-with-arforms/" target="_blank" class="arlinks doc-link-a">
			<?php echo  __( 'Documentation', 'arforms-form-builder' ) ; ?>
		</a>
	</div>
</div>
