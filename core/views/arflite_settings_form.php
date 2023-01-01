<?php
global $current_user, $arfliteformcontroller;

global $arfliteformcontroller;

?>

<div class="wrap arf_setting_page">

	<div class="top_bar">
		<span class="h2"><?php echo  __( 'General Settings', 'arforms-form-builder' ) ; ?></span>
	</div>
	
	<div id="poststuff" class="metabox-holder">

		<div id="post-body">

			<div class="inside arfwhitebackground">

				<div class="formsettings1 arfwhitebackground">

					<div class="setting_tabrow">

						<div class="arftab" id="arftab">
							<?php
								$setting_tab = get_option( 'arflite_current_tab' );
								$setting_tab = ( ! isset( $setting_tab ) || empty( $setting_tab ) ) ? 'general_settings' : $setting_tab;
							?>

							<ul id="arfsettingpagenav" class="arfmainformnavigation">


								<li class="arfsettingpagenavli general_settings 
								<?php
								if ( $setting_tab == 'general_settings' ) {
									echo 'btn_sld';
								} else {
									echo 'tab-unselected';
								}
								?>
								">
									<a href="javascript:arflite_show_form_settimgs('general_settings','autoresponder_settings');"><?php echo  __( 'General Settings', 'arforms-form-builder' ) ; ?></a>
								</li>


								<li class="arfsettingpagenavli autoresponder_settings 
								<?php
								if ( $setting_tab == 'autoresponder_settings' ) {
									echo 'btn_sld';
								} else {
									echo 'tab-unselected';
								}
								?>
								">
									<a href="javascript:arflite_show_form_settimgs('autoresponder_settings','general_settings');"><?php echo  __( 'Email Marketing Tools', 'arforms-form-builder' ) ; ?><span class="arflite_pro_version_notice arflite_pro_notice_with_title">(Premium)</span></a>
								</li>

								<?php foreach ( $sections as $sec_name => $section ) { ?>


									<li><a href="#<?php echo esc_attr($sec_name); ?>_settings"><?php echo ucfirst( $sec_name ); ?></a></li>


								<?php } ?>

							</ul>



						</div>

					</div>



					<form name="frm_settings_form" method="post" enctype="multipart/form-data" class="frm_settings_form" onsubmit="return arflite_global_form_validate();">


						<input type="hidden" name="arfaction" value="process-form" />

						<input type="hidden" name="arfcurrenttab" id="arfcurrenttab" value="<?php echo get_option( 'arflite_current_tab' ); ?>" />

						<?php wp_nonce_field( 'update-options' ); ?>

						<div class="margin-left15">
							<?php
							if ( isset( $message ) && $message != '' ) {
								?>
									<div id="success_message" class="arf_success_message" data-id="arflite_success_msg_setting_forms">
										<div class="message_descripiton">
											<div class="arffloatmargin">
												<?php echo esc_html( $message ); ?>
											</div>
											<div class="message_svg_icon">
												<svg class="arfheightwidth14">
													<path fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M6.075,14.407l-5.852-5.84l1.616-1.613l4.394,4.385L17.181,0.411 l1.616,1.613L6.392,14.407H6.075z"></path>
												</svg>
											</div>
										</div>
									</div>
								<?php
							}


							if ( isset( $errors ) && is_array( $errors ) && count( $errors ) > 0 ) {
								foreach ( $errors as $error ) {
									?>
									<div id="error_message" class="arf_error_message" data-id="arflite_error_msg_setting_forms">
										<div class="message_descripiton">
											<?php echo stripslashes( $error ); ?>
										</div>
									</div>
								<?php } ?>

							<?php } ?>
						</div>

						<div class="arflite-clear-float"></div>

						<div id="general_settings" class="<?php echo ( 'general_settings' != $setting_tab ) ? 'display-none-cls' : 'display-blck-cls'; ?>">
							<table class="form-table">

								<?php

								$hostname = $_SERVER['SERVER_NAME'];


								function is_captcha_act() {
									if ( ! function_exists( 'is_plugin_active' ) ) {
										include_once ABSPATH . 'wp-admin/includes/plugin.php';
									}
									return is_plugin_active( 'arformsgooglecaptcha/arformsgooglecaptcha.php' );
								}
								?>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td class="lbltitle" colspan="2"><?php echo  __( 'reCAPTCHA Configuration', 'arforms-form-builder' ) ; ?>&nbsp;</td>
								</tr>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td colspan="2" style="padding-left:0px; padding-bottom:30px;padding-top:15px;">
										<label class="lblsubtitle"><?php echo stripslashes( __( 'reCAPTCHA requires an API key, consisting of a "site" and a "private" key. You can sign up for a', 'arforms-form-builder' ) ); ?>&nbsp;&nbsp;<a href="https://www.google.com/recaptcha/" target="_blank" class="arlinks"><b><?php echo  __( 'free reCAPTCHA key', 'arforms-form-builder' ) ; ?></b></a>.</label>
									</td>
								</tr>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td class="tdclass email-setting-label-td" width="18%">
										<label class="lblsubtitle"><?php echo  __( 'Site Key', 'arforms-form-builder' ) ; ?></label>
									</td>

									<td>
										<input type="text" name="frm_pubkey" id="frm_pubkey" class="txtmodal1" size="42" value="<?php echo esc_attr( $arflitesettings->pubkey ); ?>" />
									</td>
								</tr>


								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'Secret Key', 'arforms-form-builder' ) ; ?></label>
									</td>

									<td>
										<input type="text" name="frm_privkey" id="frm_privkey" class="txtmodal1" size="42" value="<?php echo esc_attr( $arflitesettings->privkey ); ?>" />
									</td>
								</tr>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'reCAPTCHA Theme', 'arforms-form-builder' ) ; ?></label>
									</td>

									<td class="email-setting-input-td">
										<?php
										$responder_list_option = '';
										$selected_list_id      = '';
										$selected_list_label   = '';

										$captcha_theme = array(
											'light' =>  __( 'Light', 'arforms-form-builder' ) ,
											'dark'  =>  __( 'Dark', 'arforms-form-builder' ) ,
										);

										foreach ( $captcha_theme as $theme_value => $theme_name ) {
											if ( $arflitesettings->re_theme == $theme_value ) {
												$selected_list_id    = esc_attr( $theme_value );
												$selected_list_label = $theme_name;
											}
											$responder_list_option .= '<li class="arf_selectbox_option" data-value="' . esc_attr( $theme_value ) . '" data-label="' . esc_attr($theme_name) . '">' . $theme_name . '</li>';
										}
										?>

										<div class="sltstandard arffloat-none">
											<input id="frm_re_theme" name="frm_re_theme" value="<?php echo esc_attr($selected_list_id); ?>" type="hidden" class="frm-dropdown frm-pages-dropdown">
											<dl class="arf_selectbox width400px" data-name="frm_re_theme" data-id="frm_re_theme">
												<dt><span><?php echo esc_html( $selected_list_label ); ?></span>
												<svg viewBox="0 0 2000 1000" width="15px" height="15px">
												<g fill="#000">
												<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
												</g>
												</svg>
												</dt>
												<dd>
													<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="frm_re_theme">
														<?php echo wp_kses( $responder_list_option, array( 'li' => array( 'class' => array(), 'data-label' => array(), 'data-value' => array() ) ) ) ; ?>
													</ul>
												</dd>
											</dl>
										</div>
									</td>
								</tr>


								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'reCAPTCHA Language', 'arforms-form-builder' ) ; ?></label>
									</td>

									<td class="email-setting-input-td">
										<div class="sltstandard arfrecaptchalang">
											<?php
											$responder_list_option = '';
											$selected_list_id      = 'en';
											$selected_list_label   =  __( 'English (US)', 'arforms-form-builder' ) ;
											$rclang                = array();
											$rclang['en']          =  __( 'English (US)', 'arforms-form-builder' ) ;
											$rclang['ar']          =  __( 'Arabic', 'arforms-form-builder' ) ;
											$rclang['bn']          =  __( 'Bengali', 'arforms-form-builder' ) ;
											$rclang['bg']          =  __( 'Bulgarian', 'arforms-form-builder' ) ;
											$rclang['ca']          =  __( 'Catalan', 'arforms-form-builder' ) ;
											$rclang['zh-CN']       =  __( 'Chinese(Simplified)', 'arforms-form-builder' ) ;
											$rclang['zh-TW']       =  __( 'Chinese(Traditional)', 'arforms-form-builder' ) ;
											$rclang['hr']          =  __( 'Croatian', 'arforms-form-builder' ) ;
											$rclang['cs']          =  __( 'Czech', 'arforms-form-builder' ) ;
											$rclang['da']          =  __( 'Danish', 'arforms-form-builder' ) ;
											$rclang['nl']          =  __( 'Dutch', 'arforms-form-builder' ) ;
											$rclang['en-GB']       =  __( 'English (UK)', 'arforms-form-builder' ) ;
											$rclang['et']          =  __( 'Estonian', 'arforms-form-builder' ) ;
											$rclang['fil']         =  __( 'Filipino', 'arforms-form-builder' ) ;
											$rclang['fi']          =  __( 'Finnish', 'arforms-form-builder' ) ;
											$rclang['fr']          =  __( 'French', 'arforms-form-builder' ) ;
											$rclang['fr-CA']       =  __( 'French (Canadian)', 'arforms-form-builder' ) ;
											$rclang['de']          =  __( 'German', 'arforms-form-builder' ) ;
											$rclang['gu']          =  __( 'Gujarati', 'arforms-form-builder' ) ;
											$rclang['de-AT']       =  __( 'German (Autstria)', 'arforms-form-builder' ) ;
											$rclang['de-CH']       =  __( 'German (Switzerland)', 'arforms-form-builder' ) ;
											$rclang['el']          =  __( 'Greek', 'arforms-form-builder' ) ;
											$rclang['iw']          =  __( 'Hebrew', 'arforms-form-builder' ) ;
											$rclang['hi']          =  __( 'Hindi', 'arforms-form-builder' ) ;
											$rclang['hu']          =  __( 'Hungarian', 'arforms-form-builder' ) ;
											$rclang['id']          =  __( 'Indonesian', 'arforms-form-builder' ) ;
											$rclang['it']          =  __( 'Italian', 'arforms-form-builder' ) ;
											$rclang['ja']          =  __( 'Japanese', 'arforms-form-builder' ) ;
											$rclang['kn']          =  __( 'Kannada', 'arforms-form-builder' ) ;
											$rclang['ko']          =  __( 'Korean', 'arforms-form-builder' ) ;
											$rclang['lv']          =  __( 'Latvian', 'arforms-form-builder' ) ;
											$rclang['lt']          =  __( 'Lithuanian', 'arforms-form-builder' ) ;
											$rclang['ms']          =  __( 'Malay', 'arforms-form-builder' ) ;
											$rclang['ml']          =  __( 'Malayalam', 'arforms-form-builder' ) ;
											$rclang['mr']          =  __( 'Marathi', 'arforms-form-builder' ) ;
											$rclang['no']          =  __( 'Norwegian', 'arforms-form-builder' ) ;
											$rclang['fa']          =  __( 'Persian', 'arforms-form-builder' ) ;
											$rclang['pl']          =  __( 'Polish', 'arforms-form-builder' ) ;
											$rclang['pt']          =  __( 'Portuguese', 'arforms-form-builder' ) ;
											$rclang['pt-BR']       =  __( 'Portuguese (Brazil)', 'arforms-form-builder' ) ;
											$rclang['pt-PT']       =  __( 'Portuguese (Portugal)', 'arforms-form-builder' ) ;
											$rclang['ro']          =  __( 'Romanian', 'arforms-form-builder' ) ;
											$rclang['ru']          =  __( 'Russian', 'arforms-form-builder' ) ;
											$rclang['sr']          =  __( 'Serbian', 'arforms-form-builder' ) ;
											$rclang['sk']          =  __( 'Slovak', 'arforms-form-builder' ) ;
											$rclang['sl']          =  __( 'Slovenian', 'arforms-form-builder' ) ;
											$rclang['es']          =  __( 'Spanish', 'arforms-form-builder' ) ;
											$rclang['es-149']      =  __( 'Spanish (Latin America)', 'arforms-form-builder' ) ;
											$rclang['sv']          =  __( 'Swedish', 'arforms-form-builder' ) ;
											$rclang['ta']          =  __( 'Tamil', 'arforms-form-builder' ) ;
											$rclang['te']          =  __( 'Telugu', 'arforms-form-builder' ) ;
											$rclang['th']          =  __( 'Thai', 'arforms-form-builder' ) ;
											$rclang['tr']          =  __( 'Turkish', 'arforms-form-builder' ) ;
											$rclang['uk']          =  __( 'Ukrainian', 'arforms-form-builder' ) ;
											$rclang['ur']          =  __( 'Urdu', 'arforms-form-builder' ) ;
											$rclang['vi']          =  __( 'Vietnamese', 'arforms-form-builder' ) ;

											foreach ( $rclang as $lang => $lang_name ) {
												if ( $arflitesettings->re_lang == $lang ) {
													$selected_list_id    = esc_attr( $lang );
													$selected_list_label = $lang_name;
												}
												$responder_list_option .= '<li class="arf_selectbox_option" data-value="' . esc_attr( $lang ) . '" data-label="' . esc_attr($lang_name) . '">' . $lang_name . '</li>';
											}
											?>
											<input id="frm_re_lang" name="frm_re_lang" value="<?php echo esc_attr($selected_list_id); ?>" type="hidden" class="frm-dropdown frm-pages-dropdown">
											<dl class="arf_selectbox width400px" data-name="frm_re_lang" data-id="frm_re_lang">
												<dt><span><?php echo esc_html( $selected_list_label ); ?></span>
												<svg viewBox="0 0 2000 1000" width="15px" height="15px">
												<g fill="#000">
												<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
												</g>
												</svg>
												</dt>
												<dd>
													<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="frm_re_lang">
														<?php echo wp_kses( $responder_list_option, array( 'li' => array( 'class' => array(), 'data-value' => array(), 'data-label' => array() ) ) ); ?>
													</ul>
												</dd>
											</dl>
										</div>
									</td>
								</tr>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
						            <td class="tdclass" >
						                <label class="lblsubtitle"><?php echo __('reCAPTCHA Failed Message', 'arforms-form-builder'); ?>&nbsp;&nbsp;<span style="vertical-align:middle" class="arfglobalrequiredfield">*</span></label>
						            </td>
						            
						            <td>
						                <input type="text" class="txtmodal1" value="<?php echo esc_attr($arflitesettings->re_msg) ?>" id="arfvaluerecaptcha" name="frm_recaptcha_value" />
						                <div class="arferrmessage" id="arferrorsubmitvalue" style="display:none;"><?php echo __('This field cannot be blank.', 'arforms-form-builder'); ?></div>
						            </td>
						        </tr>

								<tr class="arfmainformfield" valign="top" style="<?php echo ( !is_captcha_act() ) ? 'display: none;' : 'display: table-row'; ?>">
									<td colspan="2"><div  class="dotted_line dottedline-width96"></div></td>
								</tr>

								<tr class="arfmainformfield">
									<td valign="top" colspan="2" class="lbltitle titleclass"><?php echo  __( 'Default Messages On Form', 'arforms-form-builder' ) ; ?> </td>
								</tr>

								<tr>
									<td class="tdclass default-blnk-msgtd" width="18%">
										<label class="lblsubtitle"><?php echo  __( 'Blank Field', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label> <br/>
									</td>
									<td class="arfmainformfield" >
										<input type="text" id="frm_blank_msg" name="frm_blank_msg" class="txtmodal1 arfgelsetfloatstyle" value="<?php echo esc_attr( $arflitesettings->blank_msg ); ?>"/>

										<div class="arf_tooltip_main arfgelsetfloatstyle"><img alt='' src="<?php echo ARFLITEIMAGESURL; ?>/tooltips-icon.png" alt="?" class="arfhelptip default-msg-toltip" title="<?php echo  __( 'Message will be displayed when required fields is left blank.', 'arforms-form-builder' ) ; ?>"/></div>
										<div class="default-msg_require"></div>
										<div class="arferrmessage display-none-cls" id="arfblankerrmsg"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>

								<tr class="arfmainformfield">

									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'Incorrect Field', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label> <br/>
									</td>

									<td>
										<input type="text" id="arfinvalidmsg" name="frm_invalid_msg" class="txtmodal1 arfgelsetfloatstyle" value="<?php echo esc_attr( $arflitesettings->invalid_msg ); ?>"/>

										<div class="arf_tooltip_main arfgelsetfloatstyle">
											<img alt='' src="<?php echo ARFLITEIMAGESURL; ?>/tooltips-icon.png" alt="?" class="arfhelptip default-msg-toltip" title="<?php echo  __( 'Message will be displayed when incorrect data is inserted of missing.', 'arforms-form-builder' ) ; ?>" />
										</div>
										<div class="default-msg_require"></div>
										<div class="arferrmessage display-none-cls" id="arfinvalidmsg_error">
											<?php
												echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ;
											?>
										</div>
									</td>
								</tr>


								<tr class="arfmainformfield">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'Success Message', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>
									<td>
										<input type="text" id="arfsuccessmsg" name="frm_success_msg" class="txtmodal1 arfgelsetfloatstyle" value="<?php echo esc_attr( $arflitesettings->success_msg ); ?>" />
										<div class="arf_tooltip_main arfgelsetfloatstyle"><img alt='' src="<?php echo ARFLITEIMAGESURL; ?>/tooltips-icon.png" alt="?" class="arfhelptip default-msg-toltip" title="<?php echo  __( 'Default message displayed after form is submitted.', 'arforms-form-builder' ) ; ?>"/></div>
										<div class="arflite-clear-float"></div>
										<div class="arferrmessage display-none-cls" id="arfsuccessmsgerr"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>


								<tr class="arfmainformfield">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo __( 'Submission Failed Message', 'arforms-form-builder' ); ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>
									<td>
										<input type="text" id="arfmessagefailed" name="frm_failed_msg" class="txtmodal1 arfgelsetfloatstyle" value="<?php echo esc_attr( $arflitesettings->failed_msg ); ?>"/>
										<div class="arf_tooltip_main arfgelsetfloatstyle" ><img alt='' src="<?php echo ARFLITEIMAGESURL; ?>/tooltips-icon.png" alt="?" class="arfhelptip default-msg-toltip" title="<?php echo  __( 'Message will be displayed when form is submitted but Duplicate entry exists.', 'arforms-form-builder' ) ; ?>"/></div>
										<div class="arflite-clear-float"></div>
										<div class="arferrmessage display-none-cls" id="arferrormessagefailed"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>


								<tr class="arfmainformfield">
									<td class="tdclass" >
										<label class="lblsubtitle"><?php echo  __( 'Default Submit Button', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>
									<td>
										<input type="text" class="txtmodal1" value="<?php echo esc_attr( $arflitesettings->submit_value ); ?>" id="arfvaluesubmit" name="frm_submit_value" />
										<div class="arferrmessage display-none-cls" id="arferrorsubmitvalue"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>

								<tr class="arfmainformfield" valign="top">
									<td colspan="2"><div class="dotted_line dottedline-width96"></div></td>
								</tr>


								<tr class="arfmainformfield">
									<td valign="top" colspan="2" class="lbltitle titleclass"><?php echo  __( 'Email Settings', 'arforms-form-builder' ) ; ?></td>
								</tr>

								<tr>
									<td class="tdclass email-setting-label-td" valign="top">
										<label class="lblsubtitle"><?php echo  __( 'From/Replyto Name', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>

									<td valign="top" class="email-setting-input-td">
										<input type="text" class="txtmodal1 width400px" id="frm_reply_to_name" name="frm_reply_to_name" value="<?php echo esc_attr($arflitesettings->reply_to_name); ?>">
										<div class="arferrmessage display-none-cls" id="frm_reply_to_name_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>

								<tr>
									<td class="tdclass email-setting-label-td" valign="top">
										<label class="lblsubtitle"><?php echo  __( 'From Email', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>
									<td valign="top " class="email-setting-input-td">
										<input type="text" class="txtmodal1 width400px" id="frm_reply_to" name="frm_reply_to" value="<?php echo esc_attr($arflitesettings->reply_to); ?>">
										<div class="arferrmessage display-none-cls" id="frm_reply_to_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>

								<tr>
									<td class="tdclass email-setting-label-td" valign="top">
										<label class="lblsubtitle"><?php echo  __( 'Reply to Email', 'arforms-form-builder' ) ; ?>&nbsp;&nbsp;<span class="arfglobalrequiredfield default-msg_require">*</span></label>
									</td>
									<td valign="top " class="email-setting-input-td">
										<input type="text" class="txtmodal1 width400px" id="reply_to_email" name="reply_to_email" value="<?php echo esc_attr($arflitesettings->reply_to_email); ?>">
										<div class="arferrmessage display-none-cls" id="frm_reply_to_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
									</td>
								</tr>

								<tr>
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'Send Email SMTP', 'arforms-form-builder' ) ; ?></label> </td>
									<td valign="top" class="email-setting-input-td">
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div">
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_custom_radio arf_submit_action" name="frm_smtp_server" id="arf_wordpress_smtp" value="wordpress" <?php checked( $arflitesettings->smtp_server, 'wordpress' ); ?> onchange="arflitechangesmtpsetting();"  />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arf_wordpress_smtp"><?php echo  __( 'WordPress Server', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div">
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_custom_radio arf_submit_action" name="frm_smtp_server" id="arf_custom_custom" onchange="arflitechangesmtpsetting();" value="custom" <?php checked( $arflitesettings->smtp_server, 'custom' ); ?>  />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arf_custom_custom"><?php echo  __( 'SMTP Server', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div">
												<div class="arf_custom_radio_wrapper">
													<input type="radio" class="arf_custom_radio arf_submit_action" name="frm_smtp_server" id="arf_wordpress_phpmailer" value="phpmailer" <?php checked( $arflitesettings->smtp_server, 'phpmailer' ); ?> onchange="arflitechangesmtpsetting();"  />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arf_wordpress_phpmailer"><?php echo  __( 'PHP Mailer', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
									</td>
								</tr>

								<tr>
									<td class="tdclass email-setting-label-td" valign="top">
										<label class="lblsubtitle"><?php echo  __( 'Email Format', 'arforms-form-builder' ) ; ?></label>
									</td>
									<td valign="top" class="email-setting-input-td">
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" name="arf_email_format" id="arf_email_html" class="arf_submit_action arf_custom_radio" value="html" 
													<?php
													if ( $arflitesettings->arf_email_format == 'html' || $arflitesettings->arf_email_format == '' ) {
														echo 'checked="checked"';
													} else {
														echo '';
													}
													?>
													 />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arf_email_html"><?php echo  __( 'HTML', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>

										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" name="arf_email_format" id="arf_email_plain" class="arf_submit_action arf_custom_radio" value="plain" <?php checked( $arflitesettings->arf_email_format, 'plain' ); ?> />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="arf_email_plain"><?php echo  __( 'Plain Text', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>

									</td>
								</tr>



								<tr class="arfsmptpsettings" <?php echo ( $arflitesettings->smtp_server != 'custom' ) ? 'style="display:none;"' : ''; ?> >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'Authentication', 'arforms-form-builder' ) ; ?></label> </td>
									<td valign="top" class="email-setting-input-td">
										<div class="arf_custom_checkbox_div">
											<div class="arf_custom_checkbox_wrapper">
												<input type="checkbox" class="" onclick="arflite_is_smtp_authentication();" id="is_smtp_authentication" name="is_smtp_authentication" value="1" <?php checked( $arflitesettings->is_smtp_authentication, 1 ); ?>>
												<svg width="18px" height="18px">
												<?php echo ARFLITE_CUSTOM_UNCHECKED_ICON; ?>
												<?php echo ARFLITE_CUSTOM_CHECKED_ICON; ?>
												</svg>
											</div>
											<span class="arf_gerset_checkoption"><label for="is_smtp_authentication"><?php echo __( 'Enable SMTP authentication', 'arforms-form-builder' ); ?></label></span>
										</div>
									</td>
								</tr>


								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo __( 'SMTP Host', 'arforms-form-builder' ) ; ?></label></td>
									<td valign="top" class="email-setting-input-td">
										<input type="text" class="txtmodal1 width400px" id="frm_smtp_host" name="frm_smtp_host" value="<?php echo esc_attr($arflitesettings->smtp_host); ?>">
									</td>
								</tr>

								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'SMTP Port', 'arforms-form-builder' ) ; ?></label></td>
									<td valign="top" class="email-setting-input-td">
										<input onkeyup="arflite_show_test_mail();" type="text" class="txtmodal1 width400px" id="frm_smtp_port" name="frm_smtp_port" value="<?php echo esc_attr($arflitesettings->smtp_port); ?>">
									</td>
								</tr>



								<tr class="arfsmptpsettings arf_authentication_field" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								} else {
									if ( $arflitesettings->is_smtp_authentication != '1' ) {
										echo 'style="display:none;"';
									}
								}
								?>
								 >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo __( 'SMTP Username', 'arforms-form-builder' ); ?></label></td>
									<td valign="top" class="email-setting-input-td">
										<input onkeyup="arflite_show_test_mail();" type="text" class="txtmodal1 width400px" id="frm_smtp_username" name="frm_smtp_username" value="<?php echo esc_attr($arflitesettings->smtp_username); ?>">
									</td>
								</tr>


								<tr class="arfsmptpsettings arf_authentication_field" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								} else {
									if ( $arflitesettings->is_smtp_authentication != '1' ) {
										echo 'style="display:none;"';
									}
								}
								?>
								 >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'SMTP Password', 'arforms-form-builder' ) ; ?></label></td>
									<td valign="top" class="email-setting-input-td">
										<input onkeyup="arflite_show_test_mail();" type="password" class="txtmodal1 width400px" id="frm_smtp_password" name="frm_smtp_password" value="<?php echo esc_attr($arflitesettings->smtp_password); ?>">
									</td>
								</tr>


								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'SMTP Encryption', 'arforms-form-builder' ) ; ?></label></td>
									<td valign="top" class="email-setting-input-td">
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" name="frm_smtp_encryption" id="frm_smtp_encryption_none" class="arf_submit_action arf_custom_radio" value="none" <?php checked( $arflitesettings->smtp_encryption, 'none' ); ?> />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="frm_smtp_encryption_none"><?php echo  __( 'None', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" name="frm_smtp_encryption" id="frm_smtp_encryption_ssl" class="arf_submit_action arf_custom_radio" value="ssl" <?php checked( $arflitesettings->smtp_encryption, 'ssl' ); ?> />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="frm_smtp_encryption_ssl"><?php echo  __( 'SSL', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" name="frm_smtp_encryption" id="frm_smtp_encryption_tls" class="arf_submit_action arf_custom_radio" value="tls" <?php checked( $arflitesettings->smtp_encryption, 'tls' ); ?> />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="frm_smtp_encryption_tls"><?php echo  __( 'TLS', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
									</td>
								</tr>
								<?php
								$smtp_test_mail_style = "disabled='disabled'";
								$smtp_test_main_class = 'arfdisabled';

								if ( $arflitesettings->is_smtp_authentication == '1' ) {
									if ( $arflitesettings->smtp_server == 'custom' && $arflitesettings->smtp_port != '' && $arflitesettings->smtp_host != '' && $arflitesettings->smtp_username != '' && $arflitesettings->smtp_password != '' ) {
										$smtp_test_mail_style = '';
										$smtp_test_main_class = '';
									} else {
										$smtp_test_mail_style = "disabled='disabled'";
										$smtp_test_main_class = 'arfdisabled';
									}
								} else {
									if ( $arflitesettings->smtp_server == 'custom' && $arflitesettings->smtp_port != '' && $arflitesettings->smtp_host != '' ) {
										$smtp_test_mail_style = '';
										$smtp_test_main_class = '';
									} else {
										$smtp_test_mail_style = "disabled='disabled'";
										$smtp_test_main_class = 'arfdisabled';
									}
								}
								?>
								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass testemail-lbl" valign="top">
										<label class="lbltitle">
											<?php echo  __( 'Send Test E-mail', 'arforms-form-builder' ) ; ?>
										</label>
									</td>
									<td valign="top" class="email-setting-input-td">
										<label id="arf_success_test_mail"><?php echo  __( 'Your test mail is successfully sent', 'arforms-form-builder' ) ; ?> </label>
										<label id="arf_error_test_mail"><?php echo  __( 'Your test mail is not sent for some reason, Please check your SMTP setting', 'arforms-form-builder' ) ; ?> </label>
									</td>
								</tr>
								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass testemail-lbl" valign="top">
										<label class="lblsubtitle">
											<?php echo  __( 'To', 'arforms-form-builder' ) ; ?>
										</label>
									</td>
									<td valign="top" class="email-setting-input-td">
										<input type="text" id="sendtestmail_to" name="sendtestmail_to" class="txtmodal1 <?php echo esc_attr($smtp_test_main_class); ?>" value="<?php echo isset( $arflitesettings->smtp_send_test_mail_to ) ? esc_attr($arflitesettings->smtp_send_test_mail_to) : ''; ?>" <?php echo $smtp_test_mail_style; ?> />
									</td>
								</tr>

								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass testemail-lbl" valign="top">
										<label class="lblsubtitle">
											<?php echo  __( 'Message', 'arforms-form-builder' ) ; ?>
										</label>
									</td>
									<td valign="top" class="email-setting-input-td">
										<textarea class="txtmultinew testmailmsg  <?php echo esc_attr($smtp_test_main_class); ?>" name="sendtestmail_msg" <?php echo $smtp_test_mail_style; ?> id="sendtestmail_msg" ><?php echo isset( $arflitesettings->smtp_send_test_mail_msg ) ? esc_attr($arflitesettings->smtp_send_test_mail_msg) : ''; ?></textarea>
									</td>
								</tr>

								<tr class="arfsmptpsettings" 
								<?php
								if ( $arflitesettings->smtp_server != 'custom' ) {
									echo 'style="display:none;"';
								}
								?>
								 >
									<td class="tdclass testemail-lbl" valign="top">
										<label class="lblsubtitle">&nbsp;</label>
									</td>
									<td valign="top" class="email-setting-input-td">
										<input type="button" value="<?php echo  __( 'Send test mail', 'arforms-form-builder' ) ; ?>" class="rounded_button arf_btn_dark_blue send-testmail-input <?php echo $smtp_test_main_class; ?>" id="arf_send_test_mail" <?php echo $smtp_test_mail_style; ?>> <img alt='' src="<?php echo ARFLITEIMAGESURL . '/ajax_loader_gray_32.gif'; ?>" id="arf_send_test_mail_loader" width="16" height="16" /> <span  class="lblnotetitle">(<?php echo  __( 'Test e-mail works only after configure SMTP server settings', 'arforms-form-builder' ) ; ?>)</span>
									</td>
								</tr>
								<tr class="arfmainformfield" valign="top">
									<td colspan="2"><div class="dotted_line dottedline-width96"></div></td>
								</tr>


								<tr class="arfmainformfield">
									<td valign="top" colspan="2" class="lbltitle titleclass"><?php echo  __( 'Other Settings', 'arforms-form-builder' ) ; ?></td>
								</tr>

								<tr>
									<td class="tdclass genenal-setlbl-padding" valign="top"><label class="lblsubtitle"><?php echo __( 'Disable built-in Anti-spam feature in signup forms', 'arforms-form-builder' ); ?></label> </td>
									<td valign="top" class="arfhidden_captcha-td">
										<div class="arf_custom_checkbox_div">
											<div class="arf_custom_checkbox_wrapper">
												<input type="checkbox" name="arfdisablehiddencaptcha" id="arfdisablehiddencaptcha" value="1" <?php checked( $arflitesettings->hidden_captcha, 1 ); ?> />
												<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKED_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKED_ICON; ?>
												</svg>
											</div>
											<span class="arf_gerset_checkoption"><label for="arfdisablehiddencaptcha"><?php echo __( 'Yes', 'arforms-form-builder' ); ?></label></span>
										</div>
									</td>
								</tr>
								<tr>
									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'Form Submission Method', 'arforms-form-builder' ) ; ?></label> </td>

									<td valign="top" class="email-setting-input-td">
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" onchange="arflite_change_form_submission_type(this);" name="arfmainformsubmittype" id="ajax_base_sbmt" class="arf_submit_action arf_custom_radio" value="1" 
													<?php
													if ( $arflitesettings->form_submit_type == 1 ) {
														echo 'checked="checked"';
													} else {
														echo '';
													}
													?>
													 />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="ajax_base_sbmt"><?php echo  __( 'Ajax based submission', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
										<div class="arf_radio_wrapper">
											<div class="arf_custom_radio_div" >
												<div class="arf_custom_radio_wrapper">
													<input type="radio" onchange="arflite_change_form_submission_type(this);" name="arfmainformsubmittype" id="normal_form_sbmt" class="arf_submit_action arf_custom_radio" value="0" 
													<?php
													if ( $arflitesettings->form_submit_type == 0 ) {
														echo 'checked="checked"';}
													?>
													 />
													<svg width="18px" height="18px">
													<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
													<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
													</svg>
												</div>
											</div>
											<span>
												<label for="normal_form_sbmt"><?php echo  __( 'Normal submission', 'arforms-form-builder' ) ; ?></label>
											</span>
										</div>
									</td>
								</tr>

								<tr class="arf_success_message_show_time_wrapper" 
								<?php
								if ( $arflitesettings->form_submit_type == 0 ) {
									echo 'style="display: none"';
								}
								?>
									 >


									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo  __( 'Hide success message after', 'arforms-form-builder' ) ; ?></label> </td>


									<td valign="top" class="email-setting-input-td">
										<?php
										if ( ! ( isset( $arflitesettings->arf_success_message_show_time ) && $arflitesettings->arf_success_message_show_time >= 0 ) ) {
											$arflitesettings->arf_success_message_show_time = 3;
										}
										?>
										<div class="arf_success_message_show_time_inner">
											<input type="text" name="arf_success_message_show_time" onkeydown="arflitevalidatenumber_admin(this, event);" maxlength="3" value="<?php echo esc_attr( $arflitesettings->arf_success_message_show_time ); ?>" id="arf_success_message_show_time" class="arf_success_message_show_time txtmodal1 arf_small_width_txtbox arfcolor"/>
											<?php echo  __( 'seconds', 'arforms-form-builder' )  . '&nbsp;&nbsp;'; ?>
											
											<span class="arf_success_message_show_time_inner">( <?php echo __( 'Note : 0 ( zero ) means it will never hide success message', 'arforms-form-builder' ); ?> )</span>
										
										</div>

									</td>


								</tr>

								<tr class="arfmainformfield" valign="top">
									<td class="tdclass">
										<label class="lblsubtitle"><?php echo  __( 'Decimal separator', 'arforms-form-builder' ) ; ?></label>
										<span class="arflite_pro_version_notice arflite_pro_notice_with_label">(Premium)</span>
									</td>
									<td class="email-setting-input-td">
										<?php
										$responder_list_option = '';
										$selected_list_id      = '.';
										$selected_list_label   =  __( 'Dot (.)', 'arforms-form-builder' ) ;

										foreach ( array(
											'.' =>  __( 'Dot (.)', 'arforms-form-builder' ) ,
											',' =>  __( 'Comma (,)', 'arforms-form-builder' ) ,
											''  =>  __( 'No Separator', 'arforms-form-builder' ) ,
										) as $decimal_value => $decimal_name ) {

											if ( isset( $arflitesettings->decimal_separator ) && $arflitesettings->decimal_separator == $decimal_value ) {
												$selected_list_id    = esc_attr( $decimal_value );
												$selected_list_label = $decimal_name;
											}

											$responder_list_option .= '<li class="arf_selectbox_option" data-value="' . esc_attr( $decimal_value ) . '" data-label="' . esc_attr($decimal_name) . '">' . esc_attr($decimal_name) . '</li>';
											?>
										<?php } ?>

										<div class="sltstandard arffloat-none" >
											<input id="decimal_separator" name="decimal_separator" value="<?php echo esc_attr($selected_list_id); ?>" type="hidden" class="frm-dropdown frm-pages-dropdown">
											<dl class="arf_selectbox decimal_separator_dl" data-name="decimal_separator" data-id="decimal_separator">
												<dt class="arf_disable_selectbox arf_restricted_control"><span><?php echo esc_html( $selected_list_label ); ?></span>
												<svg viewBox="0 0 2000 1000" width="15px" height="15px">
												<g fill="#000">
												<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
												</g>
												</svg>
												</dt>
												<dd>
													<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="decimal_separator">
														<?php echo wp_kses( $responder_list_option, array( 'li' => array( 'class' => array(), 'data-value' => array(), 'data-label' => array() ) ) ); ?>
													</ul>
												</dd>
											</dl>
										</div>
									</td>
								</tr>


								<tr>

									<td class="tdclass font-general-setting" valign="top"><label class="lblsubtitle"><?php echo  __( 'Select character sets for google fonts', 'arforms-form-builder' ) ; ?></label> </td>

									<td valign="top" class="email-setting-input-td">

										<?php
										$arf_character_arr = array(
											'latin'        => 'Latin',
											'latin-ext'    => 'Latin-ext',
											'menu'         => 'Menu',
											'greek'        => 'Greek',
											'greek-ext'    => 'Greek-ext',
											'cyrillic'     => 'Cyrillic',
											'cyrillic-ext' => 'Cyrillic-ext',
											'vietnamese'   => 'Vietnamese',
											'arabic'       => 'Arabic',
											'khmer'        => 'Khmer',
											'lao'          => 'Lao',
											'tamil'        => 'Tamil',
											'bengali'      => 'Bengali',
											'hindi'        => 'Hindi',
											'korean'       => 'Korean',
										);
										?>
										<div class="font-setting-div">
											<span class="font-setting-span">
												<?php $arf_chk_counter = 1; ?>
												<?php
												foreach ( $arf_character_arr as $arf_character => $arf_character_value ) {

													$default_charset = '';
													if ( isset( $arflitesettings->arf_css_character_set ) ) {
														if ( is_object( $arflitesettings->arf_css_character_set ) ) {
															$default_charset = isset( $arflitesettings->arf_css_character_set->$arf_character ) ? $arflitesettings->arf_css_character_set->$arf_character : '';
														} elseif ( is_array( $arflitesettings->arf_css_character_set ) ) {
															$default_charset = ( isset( $arflitesettings->arf_css_character_set[ $arf_character ] ) ) ? $arflitesettings->arf_css_character_set[ $arf_character ] : '';
														} else {
															$default_charset = '';
														}
													}
													?>
													<div class="arf_custom_checkbox_div">
														<div class="arf_custom_checkbox_wrapper">
															<input type="checkbox" id="arf_character_<?php echo esc_attr($arf_character); ?>" name="arf_css_character_set[<?php echo esc_attr($arf_character); ?>]" <?php checked( $default_charset, $arf_character ); ?> value="<?php echo esc_attr($arf_character); ?>" />
															<svg width="18px" height="18px">
															<?php echo ARFLITE_CUSTOM_UNCHECKED_ICON; ?>
															<?php echo ARFLITE_CUSTOM_CHECKED_ICON; ?>
															</svg>
														</div>
														<span class="arf-character-span"><label for="arf_character_<?php echo esc_attr($arf_character); ?>"><?php echo esc_html( $arf_character_value ); ?></label></span>
													</div>
													<?php echo ( $arf_chk_counter % 4 == 0 ) ? '</span><span class="arf_charcounter_span">' : ''; ?>
													<?php $arf_chk_counter++; ?>
												<?php } ?>
											</span>
										</div>
									</td>

								</tr>

								<tr>

									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle arfform-global-css"><?php echo  __( 'Form Global CSS', 'arforms-form-builder' ) ; ?></label></td>

									<td valign="top" class="email-setting-input-td"><div class="arf_gloabal_css_wrapper"><textarea name="arf_global_css" id="arf_global_css" class="txtmultinew"><?php echo stripslashes_deep( get_option( 'arflite_global_css' ) ); ?></textarea></div></td>

								</tr>

								<tr class="arfmainformfield" valign="top">
									<td colspan="2"><div class="dotted_line dottedline-width96"></div></td>
								</tr>
								<tr class="arfmainformfield">
									<td valign="top" colspan="2" class="lbltitle titleclass"><?php echo __( 'Load JS & CSS in all pages', 'arforms-form-builder' ); ?></td>
								</tr>

								<tr class="arfmainformfield" valign="top">

									<td colspan="2" class="load-jscss-labl-wrap">


										<label class="lblsubtitle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo stripslashes(  __( '( Not recommended - If you have any js/css loading issue in your theme, only in that case you should enable this settings )', 'arforms-form-builder' )  ); ?></label>


									</td>

								</tr>



								<tr>



									<td class="tdclass email-setting-label-td" valign="top"><label class="lblsubtitle"><?php echo __( 'Load JS & CSS', 'arforms-form-builder' ); ?></label> </td>
									<td valign="top" class="email-setting-input-td">
										<div class="arf_js_switch_wrapper">
											<input type="checkbox" class="js-switch" name="frm_arfmainformloadjscss" value="1" <?php checked( $arflitesettings->arfmainformloadjscss, 1 ); ?> onchange="arflite_change_load_js_css_wrapper(this);" />
											<span class="arf_js_switch"></span>
										</div>
										<label class="arf_js_switch_label"><span>&nbsp;<?php echo  __( 'Enable', 'arforms-form-builder' ) ; ?></span></label>
									</td>
								</tr>
								<tr class="arf_global_js_css_wrapper_show" 
								<?php
								if ( $arflitesettings->arfmainformloadjscss ) {
									echo 'style="display:table-row;"';
								} else {
									echo 'style="display:none;"';
								}
								?>
									 >


										<td></td>
										<td>
											<div  class="arf_global_js_css_div">
												
													<?php
													$i            = 1;
													$js_css_array = $arfliteformcontroller->arflite_field_wise_js_css();


													foreach ( $js_css_array as $key => $value ) {
														?>
														<div class="arf_custom_checkbox_div arf_load_js_css_option_wrapper" id="arf_load_js_css_option_wrapper">
															<div class="arf_custom_checkbox_wrapper">
																<input type="checkbox" id="arf_all_<?php echo esc_attr($key); ?>" name="arf_load_js_css[]" value="<?php echo esc_attr($key); ?>" 
																											  <?php
																												if ( in_array( $key, $arflitesettings->arf_load_js_css ) ) {
																													echo 'checked="checked"';
																												}
																												?>
																  />
																<svg width="18px" height="18px">
																<?php echo ARFLITE_CUSTOM_UNCHECKED_ICON; ?>
																<?php echo ARFLITE_CUSTOM_CHECKED_ICON; ?>
																</svg>
															</div>
															<span style="<?php echo ( is_rtl() ) ? '' : 'margin-left: 5px;'; ?>"><label for="arf_all_<?php echo esc_attr($key); ?>"><?php echo esc_html( $value['title'] ); ?></label></span>
														</div>
														<?php

														$i++;
													}
													?>
											
											</div>


										</td>

								</tr>
								<?php global $wp_version; ?>
								<?php do_action( 'arflite_outside_global_setting_block', $arflitesettings ); ?>

								<input type="hidden" id="frm_permalinks" name="frm_permalinks" value="0" />

							</table>


						</div>


						<div id="autoresponder_settings"  class="<?php echo ('autoresponder_settings' != $setting_tab) ? 'display-none-cls' : 'display-blck-cls' ?>">

							<span class="fa-life-bouy-span">
								<a href="https://www.arformsplugin.com/documentation/email-marketing-tools-with-form/" target="_blank" title="" class="fas fa-life-bouy arf_adminhelp_icon">
									<svg width="30px" height="30px" viewBox="0 0 26 32" class="arfsvgposition arfhelptip tipso_style" data-tipso="help" title="help">
									<?php echo ARFLITE_LIFEBOUY_ICON; ?>
									</svg>
									
								</a>
							</span>

							<table class="wp-list-table widefat post arflite-email-marketer-tbl2">


								<tr>

									<th class="email-marketer-img-th" width="18%">&nbsp;</th>
									<th class="email-marketer-img-wrapth" colspan="2"><img alt='' src="<?php echo ARFLITEURL; ?>/images/aweber.png" align="absmiddle" /></th>
								</tr>
								<tr>

									<th class="email-marketer-img-th" width="18%">&nbsp;</th>
									<th id="th_aweber" class="arf-email-marketer-radioth">
								<div class="arf_radio_wrapper">
									<div class="arf_custom_radio_div" >
										<div class="arf_custom_radio_wrapper">
											<input type="radio" class="arf_submit_action arf_custom_radio arfemailmarkter-radioinput" checked="checked" id="aweber_1" name="aweber_type" value="1" onclick="arflite_show_api('aweber');"  />
											<svg width="18px" height="18px">
											<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
											<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
											</svg>
										</div>
									</div>
									<span>
										<label for="aweber_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
									</span>
								</div>
						</div>
						<div class="arf_radio_wrapper">
							<div class="arf_custom_radio_div" >
								<div class="arf_custom_radio_wrapper">
									<input type="radio" class="arf_submit_action arf_custom_radio arfemailmarkter-radioinput" id="aweber_2" name="aweber_type" value="0" onclick="arflite_show_web_form('aweber');" />
									<svg width="18px" height="18px">
										<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
										<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
									</svg>
								</div>
							</div>
							<span>
								<label for="aweber_2"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
							</span>
						</div>
				</div>
				<input type="hidden" name="aweber_status" id="aweber_status" />
				</th>
				</tr>
				
				<tr id="aweber_api_tr3">

					<td class="tdclass arfaweberapi_td">&nbsp;</td>
					<td class="arfaweber_auth_btn_td"><button class="rounded_button arf_btn_dark_blue arfaweber_auth_btn arf_restricted_control"  type="button" name="continue"><?php echo  __( 'Authorize', 'arforms-form-builder' ) ; ?></button></td>

				</tr>

				<tr id="aweber_web_form_tr" class="display-none-cls">

					<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from Aweber', 'arforms-form-builder' ) ; ?></label></td>

					<td class="arfpadding-left5px">

						<textarea name="aweber_web_form" id="aweber_web_form" class="txtmultinew"></textarea>

					</td>

				</tr>

					<tr id="aweber_api_tr4" class="display-none-cls">


						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'AWEBER LIST', 'arforms-form-builder' ) ; ?></label></td>


						<td class="aweber_sellist_td">

							<span id="select_aweber">
								<div class="sltstandard arfemail_marketer_list_div">
									
									<input name="responder_list" id="aweber_listid" value="<?php echo esc_attr($selected_list_id); ?>" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="aweber_listid" data-id="aweber_listid">
										<dt>
											<span><?php _e( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
											<svg viewBox="0 0 2000 1000" width="15px" height="15px">
												<g fill="#000">
												<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
												</g>
											</svg>
										</dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls"  data-id="aweber_listid">
											</ul>
										</dd>
										<span id="aweber_loader2"><div class="arf_imageloader"></div></span>
									</dl>
								</div>

							</span>

							<div class="arlinks arfemailmarketer-delref-link-div">
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ) ; ?></a>
								&nbsp;  &nbsp;  &nbsp;  &nbsp;
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>


					</tr>

				<tr>
					<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
				</tr>
				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl">

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th class="email-marketer-img-wrapth" colspan="2"><img alt='' src="<?php echo ARFLITEURL; ?>/images/mailchimp.png" align="absmiddle" /></th>

						</th>

					</tr>

					<tr>
						<th class="email-marketer-img-th">&nbsp;</th>
						<th id="th_mailchimp" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio arfemailmarkter-radioinput" checked="checked" id="mailchimp_1" name="mailchimp_type" value="1"  onclick="arflite_show_api('mailchimp');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="mailchimp_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
						</span>
					</div>
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio arfemailmarkter-radioinput" id="mailchimp_2" name="mailchimp_type" value="0" onclick="arflite_show_web_form('mailchimp');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="mailchimp_2"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>
					</tr>

					<tr id="mailchimp_api_tr1">

						<td class="tdclass arfemail-marketer-credential-lbl" ><label class="lblsubtitle"><?php echo  __( 'API Key', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfemailinputtd"><input type="text" name="mailchimp_api" class="txtmodal1" id="mailchimp_api" size="80" onkeyup="arflite_show_verify_btn('mailchimp');" value="" /> &nbsp; &nbsp;
							<span id="mailchimp_link"><a href="javascript:void(0);" class="arlinks arf_restricted_control"><?php echo  __( 'Verify', 'arforms-form-builder' ) ; ?></a></span>
							<span id="mailchimp_loader" class="display-none-cls"><div class="arf_imageloader arfemailmarketerloaderdiv"></div></span>
							<span id="mailchimp_verify" class="frm_verify_li display-none-cls"><?php echo  __( 'Verified', 'arforms-form-builder' ) ; ?></span>
							<span id="mailchimp_error" class="frm_not_verify_li display-none-cls"><?php echo  __( 'Not Verified', 'arforms-form-builder' ) ; ?></span>
							<input type="hidden" name="mailchimp_status" id="mailchimp_status" value="" />
							<div class="arferrmessage display-none-cls" id="mailchimp_api_error" ><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
						</td>

					</tr>

					<tr id="mailchimp_api_tr2">

						<td class="tdclass arflitelist-id-lbltd"><label class="lblsubtitle"><?php echo  __( 'List ID', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfselect-email-marketer-list-td"><span id="select_mailchimp">
								<div class="sltstandard arfemail_marketer_list_div">
									<?php
									$responder_list_option = '';
									?>
									<input name="mailchimp_listid" id="mailchimp_listid" value="" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="mailchimp_listid" data-id="mailchimp_listid">
										<dt><span><?php echo __( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
										<svg viewBox="0 0 2000 1000" width="15px" height="15px">
										<g fill="#000">
										<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
										</g>
										</svg></dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="mailchimp_listid">
											</ul>
										</dd>
									</dl>
								</div>
							</span>
							<div id="mailchimp_del_link" class="arlinks arfemailmarketer-delref-link-div">
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ) ; ?></a>
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>

					</tr>

					<tr id="mailchimp_web_form_tr" class="display-none-cls">

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from Mailchimp', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfpadding-left5px">

							<textarea name="mailchimp_web_form" id="mailchimp_web_form" class="txtmultinew"></textarea>

						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl" >

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th colspan="2" class="email-marketer-img-wrapth"><img alt='' src="<?php echo ARFLITEURL; ?>/images/getresponse.png" align="absmiddle" /></th>

					</tr>

					<tr>
						<th class="email-marketer-img-th"></th>
						<th id="th_getresponse" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked" id="getresponse_1" name="getresponse_type" value="1" onclick="arflite_show_api('getresponse');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="getresponse_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
						</span>
					</div>

					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" id="getresponse_2" name="getresponse_type" value="0" onclick="arflite_show_web_form('getresponse');"/>
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="getresponse_2"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>
					</tr>

					<tr id="getresponse_api_tr1">


						<td class="tdclass arfemail-marketer-credential-lbl"><label class="lblsubtitle"><?php echo  __( 'API Key', 'arforms-form-builder' ) ; ?></label></td>


						<td class="arfemailinputtd"><input type="text" name="getresponse_api" class="txtmodal1" id="getresponse_api" size="80" onkeyup="arflite_show_verify_btn('getresponse');" value="" /> &nbsp; &nbsp;

							<span id="getresponse_link"><a href="javascript:void(0);" class="arlinks arf_restricted_control"><?php echo  __( 'Verify', 'arforms-form-builder' ) ; ?></a></span>
							<span id="getresponse_loader" class="display-none-cls"><div class="arf_imageloader arfemailmarketerloaderdiv"></div></span>
							<span id="getresponse_verify" class="frm_verify_li display-none-cls"><?php echo  __( 'Verified', 'arforms-form-builder' ) ; ?></span>
							<span id="getresponse_error" class="frm_not_verify_li display-none-cls"><?php echo  __( 'Not Verified', 'arforms-form-builder' ) ; ?></span>
							<input type="hidden" name="getresponse_status" id="getresponse_status" value="" />
							<div class="arferrmessage display-none-cls" id="getresponse_api_error" ><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>


					</tr>


					<tr id="getresponse_api_tr2">


						<td class="tdclass arflitelist-id-lbltd"><label class="lblsubtitle"><?php echo  __( 'Campaign Name', 'arforms-form-builder' ) ; ?></label></td>


						<td class="arfselect-email-marketer-list-td"><span id="select_getresponse">
								<div class="sltstandard arfemail_marketer_list_div">
									<?php
									$responder_list_option = '';
									?>
									<input name="getresponse_listid" id="getresponse_listid" value="" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="getresponse_listid" data-id="getresponse_listid">
										<dt><span><?php echo __( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
										<svg viewBox="0 0 2000 1000" width="15px" height="15px">
										<g fill="#000">
										<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
										</g>
										</svg></dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="getresponse_listid">
											</ul>
										</dd>
									</dl>
								</div></span>


							<div id="getresponse_del_link" class="arlinks arfemailmarketer-delref-link-div">

								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ) ; ?></a>
								&nbsp;  &nbsp;  &nbsp;  &nbsp;
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>


					</tr>

					<tr id="getresponse_web_form_tr" class="display-none-cls">

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from Getresponse', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfpadding-left5px">

							<textarea name="getresponse_web_form" id="getresponse_web_form" class="txtmultinew"></textarea>

						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>

				<table class="wp-list-table widefat post arflite-email-marketer-tbl">


					<tr>

						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th colspan="2" class="email-marketer-img-wrapth"><img alt='' src="<?php echo ARFLITEURL; ?>/images/icontact.png" align="absmiddle" /></th>

					</tr>

					<tr>
						<th class="email-marketer-img-th"></th>
						<th id="th_icontact" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked"  id="icontact_1"  name="icontact_type" value="1"  onclick="arflite_show_api('icontact');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="icontact_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
						</span>
					</div>
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" id="icontact_2"  name="icontact_type" value="0" onclick="arflite_show_web_form('icontact');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="icontact_2"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>

					</tr>

					<tr id="icontact_api_tr1">

						<td class="tdclass arfemail-marketer-credential-lbl"><label class="lblsubtitle"><?php echo  __( 'APP ID', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfemailinputtd"><input type="text" name="icontact_api" class="txtmodal1" id="icontact_api" size="80" onkeyup="arflite_show_verify_btn('icontact');" value="" />
							<div class="arferrmessage display-none-cls" id="icontact_api_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>

					</tr>


					<tr id="icontact_api_tr2">

						<td class="tdclass arf_emilmarkter_list"><label class="lblsubtitle"><?php echo __( 'Username', 'arforms-form-builder' ); ?></label></td>

						<td class="arficontact-username-td"><input type="text" name="icontact_username" class="txtmodal1" id="icontact_username" onkeyup="arflite_show_verify_btn('icontact');" size="80" value="" />
							<div class="arferrmessage display-none-cls" id="icontact_username_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></div></td>

					</tr>

					<tr id="icontact_api_tr3">

						<td class="tdclass arf_emilmarkter_list"><label class="lblsubtitle"><?php echo  __( 'Password', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arficontact-username-td"><input type="password" name="icontact_password" class="txtmodal1" id="icontact_password" onkeyup="arflite_show_verify_btn('icontact');" size="80" value="" />
							<span id="icontact_link" >
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Verify', 'arforms-form-builder' ) ; ?></a>
							</span>
							<span id="icontact_loader" class="display-none-cls">
								<div class="arf_imageloader arfemailmarketerloaderdiv"></div>
							</span>
							<span id="icontact_verify" class="frm_verify_li display-none-cls"><?php echo  __( 'Verified', 'arforms-form-builder' ) ; ?></span>
							<span id="icontact_error" class="frm_not_verify_li display-none-cls"><?php echo  __( 'Not Verified', 'arforms-form-builder' ) ; ?></span>
							<input type="hidden" name="icontact_status" id="icontact_status" value="" />
							<div class="arferrmessage display-none-cls" id="icontact_password_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>
					</tr>

					<tr id="icontact_api_tr4">

						<td class="tdclass arf_emilmarkter_list"><label class="lblsubtitle"><?php echo  __( 'List Name', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfselect-email-marketer-list-td"><span id="select_icontact">
								<div class="sltstandard arfemail_marketer_list_div" >
									<?php
									$responder_list_option = '';
									?>
									<input name="icontact_listname" id="icontact_listname" value="" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="icontact_listname" data-id="icontact_listname">
										<dt><span><?php echo __( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
										<svg viewBox="0 0 2000 1000" width="15px" height="15px">
										<g fill="#000">
										<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
										</g>
										</svg></dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="icontact_listname">
											</ul>
										</dd>
									</dl>
								</div></span>


							<div id="icontact_del_link" class="arlinks arfemailmarketer-delref-link-div">

								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ) ; ?></a>
								&nbsp;  &nbsp;  &nbsp;  &nbsp;
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>


					</tr>

					<tr id="icontact_web_form_tr" class="display-none-cls">

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from Icontact', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfpadding-left5px">

							<textarea name="icontact_web_form" id="icontact_web_form" class="txtmultinew"></textarea>

						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl" >

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th colspan="2" class="email-marketer-img-wrapth"><img alt='' src="<?php echo ARFLITEURL; ?>/images/constant-contact.png" align="absmiddle" /></th>


					</tr>

					<tr>
						<th class="email-marketer-img-th">&nbsp;</th>
						<th id="th_constant" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked" id="constant_contact_1" name="constant_type" value="1" onclick="arflite_show_api('constant');"/>
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="constant_contact_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
						</span>
					</div>
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" id="constant_contact_2" name="constant_type" value="0"  onclick="arflite_show_web_form('constant');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="constant_contact_2"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>
					</tr>

					<tr id="constant_api_tr1">

						<td class="tdclass arfemail-marketer-credential-lbl"><label class="lblsubtitle"><?php echo  __( 'API Key', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfemailinputtd"><input type="text" name="constant_api" class="txtmodal1" onkeyup="arflite_show_verify_btn('constant');" id="constant_api" size="80" value="" />
							<div class="arferrmessage display-none-cls" id="constant_api_error" ><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>
						</tr>

					<tr id="constant_api_tr2">

						<td class="tdclass arf_emilmarkter_list"><label class="lblsubtitle"><?php echo __( 'Access Token', 'arforms-form-builder' ); ?></label></td>

						<td class="arficontact-username-td"><input type="text" name="constant_access_token" onkeyup="arflite_show_verify_btn('constant');" class="txtmodal1" id="constant_access_token" size="80" value="" /> &nbsp; &nbsp;

							<span id="constant_link"><a href="javascript:void(0);" class="arlinks arf_restricted_control"><?php echo  __( 'Verify', 'arforms-form-builder' ) ; ?></a></span>
							<span id="constant_loader" class="display-none-cls" ><div class="arf_imageloader arfemailmarketerloaderdiv"></div></span>
							<span id="constant_verify" class="frm_verify_li display-none-cls"><?php echo  __( 'Verified', 'arforms-form-builder' ) ; ?></span>
							<span id="constant_error" class="frm_not_verify_li display-none-cls"><?php echo  __( 'Not Verified', 'arforms-form-builder' ) ; ?></span>
							<input type="hidden" name="constant_status" id="constant_status" value="" />
							<div class="arferrmessage display-none-cls" id="constant_access_token_error" ><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>

					</tr>

					<tr id="constant_api_tr3">

						<td class="tdclass arf_emilmarkter_list"><label class="lblsubtitle"><?php echo  __( 'List Name', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfselect-email-marketer-list-td"><span id="select_constant">
								<div class="sltstandard arfemail_marketer_list_div">
									<?php
									$responder_list_option = '';
									?>
									<input name="constant_listname" id="constant_listname" value="" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="constant_listname" data-id="constant_listname">
										<dt><span><?php echo __( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
										<svg viewBox="0 0 2000 1000" width="15px" height="15px">
										<g fill="#000">
										<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
										</g>
										</svg></dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="constant_listname">
											</ul>
										</dd>
									</dl>
								</div></span>


							<div id="constant_del_link" class="arlinks arfemailmarketer-delref-link-div">

								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ); ?></a>
								&nbsp;  &nbsp;  &nbsp;  &nbsp;
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>

					</tr>

					<tr id="constant_web_form_tr" class="display-none-cls">

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from Constant Contact', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfpadding-left5px">

							<textarea name="constant_web_form" id="constant_web_form" class="txtmultinew"></textarea>

						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl">

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th class="email-marketer-img-wrapth" colspan="2"><img alt='' src="<?php echo ARFLITEURL; ?>/images/madmimi.png" align="absmiddle" /></th>

						</th>

					</tr>

					<tr>
						<th class="email-marketer-img-th">&nbsp;</th>
						<th id="th_madmimi" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked" id="madmimi_1" name="madmimi_type" value="1" onclick="arflite_show_api('madmimi');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="madmimi_1"><?php echo __( 'Using API', 'arforms-form-builder' ); ?></label>
						</span>
					</div>
					</th>

					</tr>

					<tr id="madmimi_api_tr1" >

						<td class="tdclass arfemail-marketer-credential-lbl"><label class="lblsubtitle"><?php echo  __( 'Email Address', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfemailinputtd"><input type="text" name="madmimi_email" class="txtmodal1" id="madmimi_email" size="80" onkeyup="arflite_show_verify_btn('madmimi');" value="" /> &nbsp; &nbsp;
							<div class="arferrmessage display-none-cls" id="madmimi_email_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div>
							<div class="arferrmessage display-none-cls" id="madmimi_email_not_valid_error"><?php echo  __( 'Please enter valid email address.', 'arforms-form-builder' ) ; ?></div>
						</td>

					</tr>

					<tr id="madmimi_api_tr2">

						<td class="tdclass arfemail-marketer-credential-lbl"><label class="lblsubtitle"><?php echo  __( 'API Key', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfemailinputtd"><input type="text" name="madmimi_api" class="txtmodal1" id="madmimi_api" size="80" onkeyup="arflite_show_verify_btn('madmimi');" /> &nbsp; &nbsp;
							<span id="madmimi_link"><a href="javascript:void(0);" class="arlinks arf_restricted_control"><?php echo  __( 'Verify', 'arforms-form-builder' ) ; ?></a></span>
							<span id="madmimi_loader" class="display-none-cls"><div class="arf_imageloader arfemailmarketerloaderdiv"></div></span>
							<span id="madmimi_verify" class="frm_verify_li display-none-cls"><?php echo  __( 'Verified', 'arforms-form-builder' ) ; ?></span>
							<span id="madmimi_error" class="frm_not_verify_li display-none-cls"><?php echo  __( 'Not Verified', 'arforms-form-builder' ) ; ?></span>
							<input type="hidden" name="madmimi_status" id="madmimi_status"/>
							<div class="arferrmessage display-none-cls" id="madmimi_api_error"><?php echo  __( 'This field cannot be blank.', 'arforms-form-builder' ) ; ?></div></td>

					</tr>


					<tr id="madmimi_api_tr3">

						<td class="tdclass arflitelist-id-lbltd"><label class="lblsubtitle"><?php echo  __( 'List ID', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfselect-email-marketer-list-td"><span id="select_madmimi">
								<div class="sltstandard arfemail_marketer_list_div">
									<?php
									$responder_list_option = '';
									?>
									<input name="madmimi_listid" id="madmimi_listid" value="" type="hidden" class="frm-dropdown frm-pages-dropdown">
									<dl class="arf_selectbox arfemailmar_width400px" data-name="madmimi_listid" data-id="madmimi_listid">
										<dt><span><?php echo __( 'Nothing Selected', 'arforms-form-builder' ); ?></span>
										<svg viewBox="0 0 2000 1000" width="15px" height="15px">
										<g fill="#000">
										<path d="M1024 320q0 -26 -19 -45t-45 -19h-896q-26 0 -45 19t-19 45t19 45l448 448q19 19 45 19t45 -19l448 -448q19 -19 19 -45z"></path>
										</g>
										</svg></dt>
										<dd>
											<ul class="field_dropdown_menu field_dropdown_list_menu display-none-cls" data-id="madmimi_listid">
											</ul>
										</dd>
									</dl>
								</div></span>




							<div id="madmimi_del_link"  class="arlinks arfemailmarketer-delref-link-div">
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Refresh List', 'arforms-form-builder' ) ; ?></a>
								&nbsp;  &nbsp;  &nbsp;  &nbsp;
								<a href="javascript:void(0);" class="arf_restricted_control"><?php echo  __( 'Delete Configuration', 'arforms-form-builder' ) ; ?></a>
							</div>


						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl">

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th class="email-marketer-img-wrapth" colspan="2"><img alt='' src="<?php echo ARFLITEURL; ?>/images/gvo.png" align="absmiddle" /></label></th>

					</tr>

					<tr>
						<th class="email-marketer-img-th"></th>
						<th class="arf-email-marketer-radioth" id="th_gvo">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked" id="gvo_1" name="gvo_type" value="0" onclick="arflite_show_web_form('gvo');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="gvo_1"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>
					</tr>

					<tr id="gvo_web_form_tr">

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from GVO Campaign', 'arforms-form-builder' ) ; ?></label></td>

						<td class="arfpadding-left5px">

							<textarea name="gvo_api" id="gvo_api" class="txtmultinew"></textarea>

						</td>

					</tr>

					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96"></div></td>
					</tr>

				</table>


				<table class="wp-list-table widefat post arflite-email-marketer-tbl3">

					<tr>
						<th class="email-marketer-img-th" width="18%">&nbsp;</th>
						<th class="email-marketer-img-wrapth" colspan="2"><img alt='' src="<?php echo ARFLITEURL; ?>/images/ebizac.png" align="absmiddle" /></th>

					</tr>

					<tr>
						<th class="email-marketer-img-th"></th>
						<th id="th_ebizac" class="arf-email-marketer-radioth">
					<div class="arf_radio_wrapper">
						<div class="arf_custom_radio_div" >
							<div class="arf_custom_radio_wrapper">
								<input type="radio" class="arf_submit_action arf_custom_radio" checked="checked" id="ebizac_1" name="ebizac_type" value="0" onclick="arflite_show_web_form('ebizac');" />
								<svg width="18px" height="18px">
								<?php echo ARFLITE_CUSTOM_UNCHECKEDRADIO_ICON; ?>
								<?php echo ARFLITE_CUSTOM_CHECKEDRADIO_ICON; ?>
								</svg>
							</div>
						</div>
						<span>
							<label for="ebizac_1"><?php echo  __( 'Using Web-form', 'arforms-form-builder' ) ; ?></label>
						</span>
					</div>
					</th>

					</tr>

					<tr id="ebizac_web_form_tr" >

						<td class="tdclass arfwebform-code-emailmarketer"><label class="lblsubtitle"><?php echo  __( 'Webform code from eBizac', 'arforms-form-builder' ) ; ?></label></td>

						<td class="eBizac_textarea-td">
							<textarea name="ebizac_api" id="ebizac_api" class="txtmultinew"></textarea>
						</td>

					</tr>
					<tr>
						<td colspan="2" class="arfpadding-left5px"><div class="dotted_line dottedline-width96" ></div></td>
					</tr>


				</table>

				<?php do_action( 'arflite_autoresponder_global_setting_block' ); ?>


			</div>



			<div id="verification_settings">
							</div>

			<?php
			foreach ( $sections as $sec_name => $section ) {


				if ( isset( $section['class'] ) ) {


					call_user_func( array( $section['class'], $section['function'] ) );
				} else {


					call_user_func( ( isset( $section['function'] ) ? $section['function'] : $section ) );
				}
			}


			$user_roles = $current_user->roles;


			$user_role = array_shift( $user_roles );
			?>

			<br />
			<p class="submit">
				<button class="rounded_button arf_btn_dark_blue general_submit_button gnral-save-changes-btn" type="submit" ><?php echo  __( 'Save Changes', 'arforms-form-builder' ) ; ?></button></p>
			<br />

			</form>
		</div>


	</div>



</div>


</div>

<div class="documentation_link" align="right"><a href="https://www.arformsplugin.com/documentation/1-getting-started-with-arforms/" class="arlinks" style="margin-right:10px;" target="_blank"><?php echo __('Documentation', 'arforms-form-builder'); ?></a></div>

</div>
