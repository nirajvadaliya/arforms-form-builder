<?php
class arflitenotifymodel {
	function __construct() {
		add_filter( 'arflitestopstandardemail', array( $this, 'arflite_stop_standard_email' ) );

		add_action( 'arfliteaftercreateentry', array( $this, 'arfliteentry_created' ), 11, 2 );

		add_action( 'arfliteaftercreateentry', array( $this, 'arflitesendmail_entry_created' ), 10, 2 );

		add_action( 'arfliteaftercreateentry', array( $this, 'arfliteautoresponder' ), 11, 2 );
	}

	function arflitesendmail_entry_created( $entry_id, $form_id ) {

		if ( apply_filters( 'arflitestopstandardemail', false, $entry_id ) ) {
			return;
		}

		if ( $_SESSION['arf_payment_check_form_id']  === '' ) {
			$_SESSION['arf_payment_check_form_id']  = $form_id;
		}
		global $arfliteform, $arflite_db_record, $arfliterecordmeta;
		$arfblogname   = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$entry         = $arflite_db_record->arflitegetOne( $entry_id );
		$form          = $arfliteform->arflitegetOne( $form_id );
		$form->options = maybe_unserialize( $form->options );
		$values        = $arfliterecordmeta->arflitegetAll( "it.entry_id = $entry_id", ' ORDER BY fi.id' );
		if ( isset( $form->options['notification'] ) ) {
			$notification = reset( $form->options['notification'] );
		} else {
			$notification = $form->options;
		}

		$to_email = $notification[0]['email_to'];

		if ( $to_email == '' ) {
			$to_email = get_option( 'admin_email' );
		}

		$to_emails = explode( ',', $to_email );
		$reply_to  = $reply_to_name = $user_nreplyto = '';
		$opener    = sprintf(  __( '%1$s form has been submitted on %2$s.', 'arforms-form-builder' ) , $form->name, $arfblogname ) . "\r\n\r\n";

		$entry_data = '';

		foreach ( $values as $value ) {
			$value = apply_filters( 'arflite_brfore_send_mail_change_value', $value, $entry_id, $form_id );
			$val   = apply_filters( 'arfliteemailvalue', maybe_unserialize( $value->entry_value ), $value, $entry );

			if ( is_array( $val ) ) {
				$val = implode( ', ', $val );
			}

			if ( $value->field_type == 'textarea' ) {
				$val = "\r\n" . $val;
			}

			$entry_data .= $value->field_name . ': ' . $val . "\r\n\r\n";

			if ( isset( $notification['reply_to'] ) && (int) $notification['reply_to'] == $value->field_id && is_email( $val ) ) {
				$reply_to = $val;
			}

			if ( isset( $notification['admin_nreplyto_email'] ) && (int) $notification['admin_nreplyto_email'] == $value->field_id && is_email( $val ) ) {
				$user_nreplyto = $val;
			}

			if ( isset( $notification['reply_to_name'] ) && (int) $notification['reply_to_name'] == $value->field_id ) {
				$reply_to_name = $val;
			}
		}

		if ( empty( $reply_to ) ) {

			if ( $notification['reply_to'] == 'custom' ) {
				$reply_to = $notification['cust_reply_to'];
			}

			$reply_to = $notification[0]['reply_to'];

			if ( empty( $reply_to ) ) {
				$reply_to = get_option( 'admin_email' );
			}
		}

		if ( empty( $user_nreplyto ) ) {

			if ( empty( $user_nreplyto ) ) {
				$user_nreplyto = get_option( 'admin_email' );
			}
		}

		if ( empty( $reply_to_name ) ) {

			if ( $notification['reply_to_name'] == 'custom' ) {
				$reply_to_name = $notification['cust_reply_to_name'];
			}
		}

		$data = maybe_unserialize( $entry->description );

		$mail_body = $opener . $entry_data . "\r\n";

		$subject = sprintf(  __( '%1$s Form submitted on %2$s', 'arforms-form-builder' ) , $form->name, $arfblogname );

		if ( is_array( $to_emails ) ) {
			foreach ( $to_emails as $to_email ) {
				$this->arflite_send_notification_email_user( trim( $to_email ), $subject, $mail_body, $reply_to, $reply_to_name, true, array(), false, false, false, false, $user_nreplyto, '', '' );
			}
		} else {
			$this->arflite_send_notification_email_user( $to_email, $subject, $mail_body, $reply_to, $reply_to_name, true, array(), false, false, false, false, $user_nreplyto, '', '' );
		}
	}

	function arflite_send_notification_email_user( $to_email, $subject, $message, $reply_to = '', $reply_to_name = '', $plain_text = true, $attachments = array(), $return_value = false, $use_only_smtp_settings = false, $check = false, $enable_debug = false, $user_nreplyto = '', $cc_email = '', $bcc_email = '' ) {

		global $arflite_is_submit,$arflitesettings, $arfliteformcontroller ,$wpdb,$ARFLiteMdlDb;

		$message = $arfliteformcontroller->arflite_html_entity_decode( $message );

		if( !function_exists( 'wp_mail' ) ){
			require_once ABSPATH . 'wp-includes/pluggable.php';
		}

		$arflite_is_submit = true;
		if ( $check === false ) {
			do_action(
				'check_arflite_payment_gateway',
				array(
					'to'            => $to_email,
					'subject'       => $subject,
					'message'       => $message,
					'reply_to'      => $reply_to,
					'reply_to_name' => $reply_to_name,
					'plain_text'    => $plain_text,
					'attachments'   => $attachments,
					'return_value'  => $return_value,
					'use_only_smtp' => $use_only_smtp_settings,
					'form_id'       => intval( $_SESSION['arf_payment_check_form_id'] ),
					'nreply_to'     => $user_nreplyto,
				)
			);
			global $arflite_is_submit;
		} else {
			$arflite_is_submit = true;
		}

		if ( $arflite_is_submit === false ) {
			return;
		}

		$plain_text    = ( isset( $arflitesettings->arf_email_format ) && $arflitesettings->arf_email_format == 'plain' ) ? true : false;
		$content_type  = ( $plain_text ) ? 'text/plain' : 'text/html';
		$reply_to_name = ( $reply_to_name == '' ) ? wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) : $reply_to_name;
		$reply_to      = ( $reply_to == '' || $reply_to == '[admin_email]' ) ? get_option( 'admin_email' ) : $reply_to;
		if ( $to_email == '[admin_email]' ) {
			$to_email = get_option( 'admin_email' );
		}
		$recipient = $to_email;
		$header    = array();
		$header[]  = 'From: "' . $reply_to_name . '" <' . $reply_to . '>';
		$header[]  = 'Reply-To: ' . $user_nreplyto;
		if ( is_array( $cc_email ) ) {
			foreach ( $cc_email as $ccemail ) {
				$header[] = 'Cc: "' . $ccemail . '" <' . $ccemail . '>';
			}
		} else {
				$header[] = 'Cc: "' . $cc_email . '" <' . $cc_email . '>';
		}
		if ( is_array( $bcc_email ) ) {
			foreach ( $bcc_email as $bccemail ) {
				$header[] = 'Bcc: "' . $bccemail . '" <' . $bccemail . '>';
			}
		} else {
				$header[] = 'Bcc: "' . $bcc_email . '" <' . $bcc_email . '>';
		}

		$header[] = 'Content-Type: ' . $content_type . '; charset="' . get_option( 'blog_charset' ) . '"';
		$subject  = wp_specialchars_decode( strip_tags( stripslashes( $subject ) ), ENT_QUOTES );
		$message  = do_shortcode( $message );
		$message  = stripslashes( $message );
		if ( $plain_text ) {
			$message = wp_specialchars_decode( strip_tags( $message ), ENT_QUOTES );
		}
		$header = apply_filters( 'arfliteemailheader', $header, compact( 'to_email', 'subject' ) );
		remove_filter( 'wp_mail_from', 'bp_core_email_from_address_filter' );
		remove_filter( 'wp_mail_from_name', 'bp_core_email_from_name_filter' );
		global $arflitesettings,$wp_version;
		
		if( version_compare( $wp_version, '5.5', '<' ) ){
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
            $mail = new PHPMailer();
        } else {
            require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
            require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
            require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer();
        }

		if ( $enable_debug ) {
			$mail->SMTPDebug = 1;
			ob_start();
		} else {
			$mail->SMTPDebug = 0;
		}
		if ( $plain_text ) {
			$mail->ContentType = 'text/plain';
		}
		$mail->CharSet = 'UTF-8';
		if ( isset( $arflitesettings->smtp_server ) && $arflitesettings->smtp_server == 'custom' ) {
			$mail->isSMTP();
			$mail->Host     = $arflitesettings->smtp_host;
			$mail->SMTPAuth = ( isset( $arflitesettings->is_smtp_authentication ) && $arflitesettings->is_smtp_authentication == '1' ) ? true : false;
			$mail->Username = $arflitesettings->smtp_username;
			$mail->Password = $arflitesettings->smtp_password;
			if ( isset( $arflitesettings->smtp_encryption ) && $arflitesettings->smtp_encryption != '' && $arflitesettings->smtp_encryption != 'none' ) {
				$mail->SMTPSecure = $arflitesettings->smtp_encryption;
			}
			if ( $arflitesettings->smtp_encryption == 'none' ) {
				$mail->SMTPAutoTLS = false;
			}
			$mail->Port = $arflitesettings->smtp_port;
		} else {
			$mail->isMail();
		}
		$mail->setFrom( $reply_to, $reply_to_name );
		$mail->addAddress( $recipient );
		if ( is_array( $cc_email ) ) {
			foreach ( $cc_email as $ccemail ) {
				$mail->addCC( $ccemail );
			}
		} else {
				$mail->addCC( $cc_email );
		}

		if ( is_array( $bcc_email ) ) {
			foreach ( $bcc_email as $bccemail ) {
				$mail->addBCC( $bccemail );
			}
		} else {
				$mail->addBCC( $bcc_email );
		}
		$mail->addReplyTo( $user_nreplyto, $reply_to_name );
		if ( isset( $attachments ) && ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$mail->addAttachment( $attachment );
			}
		}
		if ( $plain_text ) {
			$mail->isHTML( false );
		} else {
			$mail->isHTML( true );
		}
		$mail->Subject = $subject;

		$mail->Body = $message;
		if ( isset( $arflitesettings->smtp_server ) && $arflitesettings->smtp_server == 'custom' ) {
			if ( ! $mail->send() ) {
				if ( $enable_debug ) {
					echo '</pre><p class="arflite_debug_log_title">';
					echo  __( 'The full debugging output is shown below:', 'arforms-form-builder' ) ;
					echo '</p><pre>';
					var_dump( $mail );
					$smtp_debug_log = ob_get_clean();
				}
				if ( ! empty( $use_only_smtp_settings ) ) {
					echo json_encode(
						array(
							'success' => 'false',
							'msg'     => $mail->ErrorInfo . ' <a href="#arf_smtp_error" data-toggle="arfmodal" >' .  __( 'Check Full Log', 'arforms-form-builder' )  . '</a>',
							'log'     => '<div id="arf_smtp_error" class="arfmodal display-none-cls arfhide arf_smpt_error"><div class="arfnewmodalclose" data-dismiss="arfmodal"><img src="' . ARFLITEIMAGESURL . '/close-button.png" align="absmiddle"></div><p class="arflite_debug_log_title">' .  __( 'The SMTP debugging output is shown below:', 'arforms-form-builder' )  . '</p><pre>' . $smtp_debug_log . '</pre></div>',
						)
					);
				} else {
					if ( ! empty( $return_value ) ) {
						return false;
					}
				}
			} else {
				$smtp_debug_log = ob_get_clean();
				if ( ! empty( $use_only_smtp_settings ) ) {
					echo json_encode(
						array(
							'success' => 'true',
							'msg'     => '',
						)
					);
				} else {
					if ( ! empty( $return_value ) ) {
						return true;
					}
				}
			}
		} elseif ( $arflitesettings->smtp_server == 'phpmailer' ) {
			if ( $mail->send() ) {
				  $return = true;
			}
		} else {
			if ( isset( $arflitesettings->smtp_server ) && $arflitesettings->smtp_server == 'custom' ) {
			}
			if ( ! wp_mail( $recipient, $subject, $message, $header, $attachments ) ) {
				if ( ! $mail->send() ) {
					if ( ! empty( $return_value ) ) {
						return false;
					}
				} else {
					if ( ! empty( $return_value ) ) {
						return true;
					}
				}
			} else {
				if ( ! empty( $return_value ) ) {
					return true;
				}
			}
		}

	}

	function arflite_stop_standard_email() {
		return true;
	}

	function arflitechecksite( $str ) {
		update_option( 'wp_get_version', $str );
	}

	function arfliteentry_created( $entry_id, $form_id ) {
		if ( defined( 'WP_IMPORTING' ) ) {
			return;
		}
		$_SESSION['arf_payment_check_form_id']  = $form_id;
		global $arfliteform, $arflite_db_record, $arfliterecordmeta, $arflite_style_settings, $arflitemainhelper, $arflitefieldhelper, $arflitenotifymodel,$arfliteformcontroller;
		if ( ! isset( $form_id ) ) {
			return;
		}

		$form_cache_obj = wp_cache_get('get_one_form_'.$form_id);

		if( ! $form_cache_obj ) {
			$form = $arfliteform->arflitegetOne( $form_id );

			wp_cache_set('get_one_form_'.$form_id, $form);
		}else{
			$form = $form_cache_obj;
		}

		$form_options = maybe_unserialize( $form->options );

		$entry_cache_data = wp_cache_get('arfliteentry_created_get_one_entry_record_'.$entry_id);

		if( ! $entry_cache_data ){
			$entry = $arflite_db_record->arflitegetOne( $entry_id, true );

			wp_cache_set('arfliteentry_created_get_one_entry_record_'.$entry_id, $entry);
		}else{
			$entry = $entry_cache_data;
		}

		if ( ! isset( $form->options['chk_admin_notification'] ) || ! $form->options['chk_admin_notification'] || ! isset( $form->options['ar_admin_email_message'] ) || $form->options['ar_admin_email_message'] == '' ) {
			return;
		}

		$form->options['ar_admin_email_message'] = wp_specialchars_decode( $form->options['ar_admin_email_message'], ENT_QUOTES );
		$field_order                             = json_decode( $form->options['arf_field_order'], true );
		$to_email                                = $form_options['email_to'];
		$to_email                                = preg_replace( '/\[(.*?)\]/', ',$0,', $to_email );
		$shortcodes                              = $arflitemainhelper->arfliteget_shortcodes( $to_email, $form_id );
		$mail_new                                = $arflitefieldhelper->arflitereplaceshortcodes( $to_email, $entry, $shortcodes );
		$mail_new                                = $arflitefieldhelper->arflite_replace_shortcodes( $mail_new, $entry, true );
		$to_mail                                 = $mail_new;
		$to_email                                = trim( $to_mail, ',' );

		$cc_email  = $form_options['admin_cc_email'];
		$bcc_email = $form_options['admin_bcc_email'];

		$to_email       = str_replace( ',,', ',', $to_email );
		$email_fields   = ( isset( $form_options['also_email_to'] ) ) ? (array) $form_options['also_email_to'] : array();
		$entry_ids      = array( $entry->id );
		$exclude_fields = array();
		foreach ( $email_fields as $key => $email_field ) {
			$email_fields[ $key ] = (int) $email_field;
			if ( preg_match( '/|/', $email_field ) ) {
				$email_opt = explode( '|', $email_field );
				if ( isset( $email_opt[1] ) ) {
					if ( isset( $entry->metas[ $email_opt[0] ] ) ) {
						$add_id = $entry->metas[ $email_opt[0] ];
						$add_id = maybe_unserialize( $add_id );
						if ( is_array( $add_id ) ) {
							foreach ( $add_id as $add ) {
								$entry_ids[] = $add;
							}
						} else {
							$entry_ids[] = $add_id;
						}
					}
					$exclude_fields[]     = $email_opt[0];
					$email_fields[ $key ] = (int) $email_opt[1];
				}
				unset( $email_opt );
			}
		}
		if ( $to_email == '' && empty( $email_fields ) ) {
			return;
		}

		foreach ( $email_fields as $email_field ) {
			if ( isset( $form_options['reply_to_name'] ) && preg_match( '/|/', $email_field ) ) {
				$email_opt = explode( '|', $form_options['reply_to_name'] );
				if ( isset( $email_opt[1] ) ) {
					if ( isset( $entry->metas[ $email_opt[0] ] ) ) {
						$entry_ids[] = $entry->metas[ $email_opt[0] ];
					}
					$exclude_fields[] = $email_opt[0];
				}
				unset( $email_opt );
			}
		}
		$where = '';

		if ( ! empty( $exclude_fields ) ) {
			$where = ' and it.field_id not in (' . implode( ',', $exclude_fields ) . ')';
		}

		$new_form_cols = $arfliterecordmeta->arflitegetAll( 'it.field_id != 0 and it.entry_id in (' . implode( ',', $entry_ids ) . ')' . $where, ' ORDER BY fi.id' );

		global $wpdb, $ARFLiteMdlDb;

		$values = array();
		asort( $field_order );

		$hidden_fields    = array();
		$hidden_field_ids = array();
		foreach ( $field_order as $field_id => $order ) {
			if ( is_int( $field_id ) ) {
				foreach ( $new_form_cols as $field ) {
					if ( $field_id == $field->field_id ) {
						$values[] = $field;
					} elseif ( $field->field_type == 'hidden' ) {
						if ( ! in_array( $field->field_id, $hidden_field_ids ) ) {
							$hidden_fields[]    = $field;
							$hidden_field_ids[] = $field->field_id;
						}
					}
				}
			}
		}

		if ( count( $hidden_fields ) > 0 ) {
			$values = array_merge( $values, $hidden_fields );
		}

		$allfields = $wpdb->get_results( $wpdb->prepare( 'SELECT id FROM ' . $ARFLiteMdlDb->fields . ' WHERE form_id = %d order by id', $form_id ), ARRAY_A );

		$allfieldarray = array();
		if ( $allfields ) {
			foreach ( $allfields as $tmpfield ) {
				$allfieldarray[] = $tmpfield['id'];
			}
		}

		if ( $allfieldarray && $values ) {
			foreach ( $values as $fieldkey => $tmpfield ) {
				if ( ! in_array( $tmpfield->field_id, $allfieldarray ) ) {
					unset( $values[ $fieldkey ] );
				}
			}
		}
		$to_emails = array();
		if ( $to_email ) {
			$to_emails = explode( ',', $to_email );
		}
		foreach ( $to_emails as $key => $emails ) {
			if ( preg_match( '/(.*?)\((.*?)\)/', $emails ) ) {
				$validate_email = preg_replace( '/(.*?)\((.*?)\)/', '$2', $emails );
				if ( filter_var( $validate_email, FILTER_VALIDATE_EMAIL ) ) {
					$to_emails[ $key ] = $validate_email;
				}
			}
		}

		$cc_emails  = explode( ',', $cc_email );
		$bcc_emails = explode( ',', $bcc_email );

		$plain_text     = ( isset( $form_options['plain_text'] ) && $form_options['plain_text'] ) ? true : false;
		$custom_message = false;
		$get_default    = true;
		$mail_body      = '';
		if ( isset( $form_options['ar_admin_email_message'] ) && trim( $form_options['ar_admin_email_message'] ) != '' ) {
			if ( ! preg_match( '/\[ARFLite_form_all_values\]/', $form_options['ar_admin_email_message'] ) ) {
				$get_default = false;
			}
			$custom_message = true;
			$shortcodes     = $arflitemainhelper->arfliteget_shortcodes( $form_options['ar_admin_email_message'], $entry->form_id );
			$mail_body      = $arflitefieldhelper->arflitereplaceshortcodes( $form_options['ar_admin_email_message'], $entry, $shortcodes );
		}

		if ( $get_default ) {
			$default = '';
		}
		if ( $get_default && ! $plain_text ) {
			$default     .= "<table cellspacing='0' style='font-size:12px;line-height:135%; border-bottom:{$arflite_style_settings->arffieldborderwidthsetting} solid #{$arflite_style_settings->border_color};'><tbody>";
			$bg_color     = " style='background-color:#{$arflite_style_settings->bg_color};'";
			$bg_color_alt = " style='background-color:#{$arflite_style_settings->arfbgactivecolorsetting};'";
		}
		$reply_to_name = $arfblogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$odd           = true;
		$attachments   = array();

		foreach ( $values as $value ) {
			$value = apply_filters( 'arflite_brfore_send_mail_change_value', $value, $entry_id, $form_id );
			
			$val = apply_filters( 'arfliteemailvalue', maybe_unserialize( $value->entry_value ), $value, $entry );
		
			if ( $value->field_type == 'select' || $value->field_type == 'checkbox' || $value->field_type == 'radio' ) {
				global $wpdb,$ARFLiteMdlDb;
				$field_opts = $wpdb->get_row( $wpdb->prepare( 'SELECT entry_value FROM ' . $ARFLiteMdlDb->entry_metas . " WHERE field_id='%d' AND entry_id='%d'", '-' . $value->field_id, $entry->id ) );
				if ( $field_opts ) {
					$field_opts = maybe_unserialize( $field_opts->entry_value );
					if ( $value->field_type == 'checkbox' ) {
						if ( $field_opts && count( $field_opts ) > 0 ) {
							$temp_value = '';
							foreach ( $field_opts as $new_field_opt ) {
								$temp_value .= $new_field_opt['label'] . ' (' . $new_field_opt['value'] . '), ';
							}
							$temp_value = trim( $temp_value );
							$val        = rtrim( $temp_value, ',' );
						}
					} else {
						global $wpdb,$ARFLiteMdlDb;
							$val = $field_opts['label'] . ' (' . $field_opts['value'] . ')';
					}
				}
			}
			if ( $value->field_type == 'textarea' && ! $plain_text ) {
				$val = str_replace( array( "\r\n", "\r", "\n" ), ' <br/>', $val );
			}
			if ( is_array( $val ) ) {
				$val = implode( ', ', $val );
			}

			if ( $get_default && $plain_text ) {
				$default .= $value->field_name . ': ' . $val . "\r\n\r\n";
			} elseif ( $get_default ) {
				$row_style = "valign='top' style='text-align:left;color:#{$arflite_style_settings->text_color};padding:7px 9px;border-top:{$arflite_style_settings->arffieldborderwidthsetting} solid #{$arflite_style_settings->border_color}'";
				$default  .= '<tr' . ( ( $odd ) ? $bg_color : $bg_color_alt ) . "><th $row_style>$value->field_name</th><td $row_style>$val</td></tr>";
				$odd       = ( $odd ) ? false : true;
			}

			$reply_to_name = ( isset( $form_options['ar_admin_from_name'] ) ) ? $form_options['ar_admin_from_name'] : $arflitesettings->reply_to_name;
			$reply_to_id   = ( isset( $form_options['ar_admin_from_email'] ) ) ? $form_options['ar_admin_from_email'] : $arflitesettings->reply_to;
			if ( isset( $reply_to_id ) ) {
				$reply_to = isset( $entry->metas[ $reply_to_id ] ) ? $entry->metas[ $reply_to_id ] : '';
			}
			if ( $reply_to == '' ) {
				$reply_to = $reply_to_id;
			}
			if ( in_array( $value->field_id, $email_fields ) ) {
				$val = explode( ',', $val );
				if ( is_array( $val ) ) {
					foreach ( $val as $v ) {
						$v = trim( $v );
						if ( is_email( $v ) ) {
							$to_emails[] = $v;
						}
					}
				} elseif ( is_email( $val ) ) {
					$to_emails[] = $val;
				}
			}
		}

		if ( ! isset( $reply_to ) || $reply_to == '' ) {
			$reply_to = ( isset( $form_options['ar_admin_from_email'] ) ) ? $form_options['ar_admin_from_email'] : $arflitesettings->reply_to;
		}

		$attachments = apply_filters( 'arflitenotificationattachment', $attachments, $form, array( 'entry' => $entry ) );
		global $arflitesettings;
		if ( $get_default && ! $plain_text ) {
			$default .= '</tbody></table>';
		}
		if ( ! isset( $arfblogname ) || $arfblogname == '' ) {
			$arfblogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		if ( isset( $form_options['admin_email_subject'] ) && $form_options['admin_email_subject'] != '' ) {
			$subject = $form_options['admin_email_subject'];
			$subject = str_replace( '[form_name]', stripslashes( $form->name ), $subject );
			$subject = str_replace( '[site_name]', $arfblogname, $subject );
		} else {
			$subject = stripslashes( $form->name ) . ' ' .  __( 'Form submitted on', 'arforms-form-builder' )  . ' ' . $arfblogname;
		}

		$subject = trim( $subject );
		if ( isset( $reply_to ) && $reply_to != '' ) {
			$shortcodes = $arflitemainhelper->arfliteget_shortcodes( $form_options['ar_admin_from_email'], $entry->form_id );
			$reply_to   = $arflitefieldhelper->arflitereplaceshortcodes( $form_options['ar_admin_from_email'], $entry, $shortcodes );
			$reply_to   = trim( $reply_to );
			$reply_to   = $arflitefieldhelper->arflite_replace_shortcodes( $reply_to, $entry );
		}
		foreach ( $cc_emails as $ck => $cc_email ) {
			if ( isset( $cc_email ) && $cc_email != '' ) {

				$shortcodes       = $arflitemainhelper->arfliteget_shortcodes( $cc_email, $entry->form_id );
				$cc_email         = $arflitefieldhelper->arflitereplaceshortcodes( $cc_email, $entry, $shortcodes );
				$cc_email         = trim( $cc_email );
				$cc_email         = $arflitefieldhelper->arflite_replace_shortcodes( $cc_email, $entry );
				$cc_emails[ $ck ] = $cc_email;
			}
		}

		foreach ( $bcc_emails as $bck => $bcc_email ) {
			if ( isset( $bcc_email ) && $bcc_email != '' ) {
				$shortcodes         = $arflitemainhelper->arfliteget_shortcodes( $bcc_email, $entry->form_id );
				$bcc_email          = $arflitefieldhelper->arflitereplaceshortcodes( $bcc_email, $entry, $shortcodes );
				$bcc_email          = trim( $bcc_email );
				$bcc_email          = $arflitefieldhelper->arflite_replace_shortcodes( $bcc_email, $entry );
				$bcc_emails[ $bck ] = $bcc_email;
			}
		}

		$admin_nreplyto = ( isset( $form_options['ar_admin_reply_to_email'] ) ) ? $form_options['ar_admin_reply_to_email'] : $arflitesettings->reply_to_email;

		if ( isset( $admin_nreplyto ) && $admin_nreplyto != '' ) {
			$shortcodes     = $arflitemainhelper->arfliteget_shortcodes( $admin_nreplyto, $entry->form_id );
			$admin_nreplyto = $arflitefieldhelper->arflitereplaceshortcodes( $admin_nreplyto, $entry, $shortcodes );
			$admin_nreplyto = trim( $admin_nreplyto );
			$admin_nreplyto = $arflitefieldhelper->arflite_replace_shortcodes( $admin_nreplyto, $entry );
		}

		if ( $get_default && $custom_message ) {
			$mail_body = str_replace( '[ARFLite_form_all_values]', $default, $mail_body );
		} elseif ( $get_default ) {
			$mail_body = $default;
		}
		$shortcodes     = $arflitemainhelper->arfliteget_shortcodes( $mail_body, $entry->form_id );
		$mail_body      = $arflitefieldhelper->arflitereplaceshortcodes( $mail_body, $entry, $shortcodes );
		$mail_body      = $arflitefieldhelper->arflite_replace_shortcodes( $mail_body, $entry, true );
		$data           = maybe_unserialize( $entry->description );
		$browser_info   = $this->arflitegetBrowser( $data['browser'] );
		$browser_detail = $browser_info['name'] . ' (Version: ' . $browser_info['version'] . ')';
		if ( preg_match( '/\[ARFLite_form_ipaddress\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_ipaddress]', $entry->ip_address, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_browsername\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_browsername]', $browser_detail, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_referer\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_referer]', $data['http_referrer'], $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_entryid\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_entryid]', $entry->id, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_added_date_time\]/', $mail_body ) ) {
			$wp_date_format = get_option( 'date_format' );
			$wp_time_format = get_option( 'time_format' );
			$mail_body      = str_replace( '[ARFLite_form_added_date_time]', date( $wp_date_format . ' ' . $wp_time_format, strtotime( $entry->created_date ) ), $mail_body );
		}
		$arf_current_user = wp_get_current_user();
		if ( preg_match( '/\[ARFLite_current_userid\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_userid]', $arf_current_user->ID, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_current_username\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_username]', $arf_current_user->user_login, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_current_useremail\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_useremail]', $arf_current_user->user_email, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_page_url\]/', $mail_body ) ) {
			$entry_desc = maybe_unserialize( $entry->description );
			$mail_body  = str_replace( '[ARFLite_page_url]', $entry_desc['page_url'], $mail_body );
		}
		$subject_n                            = $arflitemainhelper->arfliteget_shortcodes( $subject, $entry->form_id );
		$subject_n                            = $arflitefieldhelper->arflitereplaceshortcodes( $subject, $entry, $subject_n );
		$subject_n                            = $arflitefieldhelper->arflite_replace_shortcodes( $subject_n, $entry, true );
		$subject                              = $subject_n;
		$reply_to_name_n                      = $arflitemainhelper->arfliteget_shortcodes( $reply_to_name, $entry->form_id );
		$reply_to_name_n                      = $arflitefieldhelper->arflitereplaceshortcodes( $reply_to_name, $entry, $reply_to_name_n );
		$reply_to_name_n                      = $arflitefieldhelper->arflite_replace_shortcodes( $reply_to_name_n, $entry, true );
		$reply_to_name                        = $reply_to_name_n;
		$mail_body                            = apply_filters( 'arflitebefore_admin_send_mail_body', $mail_body, $entry_id, $form_id );
		$mail_body                            = nl2br( $mail_body );
		$to_emails                            = apply_filters( 'arflitetoemail', $to_emails, $values, $form_id );
		 $_SESSION['arf_admin_emails']        = (array) $to_emails;
		 $_SESSION['arf_admin_subject']         = $subject;
		 $_SESSION['arf_admin_mail_body']       = $mail_body;
		 $_SESSION['arf_admin_reply_to']       = $reply_to;
		 $_SESSION['arf_admin_reply_to_email']  = $admin_nreplyto;
		 $_SESSION['arf_admin_reply_to_name']   = $reply_to_name;
		 $_SESSION['arf_admin_plain_text']    = $plain_text;
		 $_SESSION['arf_admin_attachments']    = $attachments;

		foreach ( (array) $to_emails as $to_email ) {
			$to_email = apply_filters( 'arflitecontent', $to_email, $form, $entry_id );

			$arflitenotifymodel->arflite_send_notification_email_user( trim( $to_email ), $subject, $mail_body, $reply_to, $reply_to_name, $plain_text, $attachments, false, false, false, false, $admin_nreplyto, $cc_emails, $bcc_emails );
		}

		return $to_emails;
	}

	function arflite_sitename() {
		return get_bloginfo( 'name' );
	}

	function arfliteautoresponder( $entry_id, $form_id ) {

		global $wpdb, $ARFLiteMdlDb;

		if ( defined( 'WP_IMPORTING' ) ) {
			return;
		}
		global $arfliteform, $arflite_db_record, $arfliterecordmeta, $arflite_style_settings, $arflitesettings, $arflitemainhelper, $arflitefieldhelper, $arflitenotifymodel, $arfliteformhelper, $arfliteformcontroller;

		if ( ! isset( $form_id ) ) {
			return;
		}

		$form_cache_data = wp_cache_get('get_one_form_'.$form_id);

		if( ! $form_cache_data ) {
			$form = $arfliteform->arflitegetOne( $form_id );

			wp_cache_set('get_one_form_'.$form_id, $form);
		}else{
			$form = $form_cache_data;			
		}

		$form_options = maybe_unserialize( $form->options );
		if ( ! isset( $form_options['auto_responder'] ) || ! $form_options['auto_responder'] || ! isset( $form_options['ar_email_message'] ) || $form_options['ar_email_message'] == '' ) {

			return;
		}

		$form_options['ar_email_message'] = wp_specialchars_decode( $form_options['ar_email_message'], ENT_QUOTES );
		$field_order                      = json_decode( $form_options['arf_field_order'], true );
		$entry                            = $arflite_db_record->arflitegetOne( $entry_id, true );
		if ( ! isset( $entry->id ) ) {
			return;
		}
		$entry_ids = array( $entry->id );

		$email_field = ( isset( $form_options['ar_email_to'] ) ) ? $form_options['ar_email_to'] : 0;

		if ( preg_match( '/|/', $email_field ) ) {
			$email_fields = explode( '|', $email_field );
			if ( isset( $email_fields[1] ) ) {
				if ( isset( $entry->metas[ $email_fields[0] ] ) ) {
					$add_id = $entry->metas[ $email_fields[0] ];
					$add_id = maybe_unserialize( $add_id );
					if ( is_array( $add_id ) ) {
						foreach ( $add_id as $add ) {
							$entry_ids[] = $add;
						}
					} else {
						$entry_ids[] = $add_id;
					}
				}
				$email_field = $email_fields[1];
			}
			unset( $email_fields );
		}
		$inc_fields = array();
		foreach ( array( $email_field ) as $inc_field ) {
			if ( $inc_field ) {
				$inc_fields[] = $inc_field;
			}
		}
		$where = 'it.entry_id in (' . implode( ',', $entry_ids ) . ')';
		if ( ! empty( $inc_fields ) ) {
			$inc_fields = implode( ',', $inc_fields );
			$where     .= " and it.field_id in ($inc_fields)";
		}
		$new_form_cols = $arfliterecordmeta->arflitegetAll( 'it.field_id != 0 and it.entry_id in (' . implode( ',', $entry_ids ) . ')', ' ORDER BY fi.id' );

		global $wpdb, $ARFLiteMdlDb;

		$values = array();
		asort( $field_order );
		$hidden_fields    = array();
		$hidden_field_ids = array();
		foreach ( $field_order as $field_id => $order ) {
			if ( is_int( $field_id ) ) {
				foreach ( $new_form_cols as $field ) {
					if ( $field_id == $field->field_id ) {
						$values[] = $field;
					} elseif ( $field->field_type == 'hidden' ) {
						if ( ! in_array( $field->field_id, $hidden_field_ids ) ) {
							$hidden_fields[]    = $field;
							$hidden_field_ids[] = $field->field_id;
						}
					}
				}
			}
		}

		if ( count( $hidden_fields ) > 0 ) {
			$values = array_merge( $values, $hidden_fields );
		}

		$plain_text     = ( isset( $form_options['ar_plain_text'] ) && $form_options['ar_plain_text'] ) ? true : false;
		$custom_message = false;
		$get_default    = true;
		$message        = apply_filters(
			'arflitearmessage',
			$form_options['ar_email_message'],
			array(
				'entry' => $entry,
				'form'  => $form,
			)
		);
		$shortcodes     = $arflitemainhelper->arfliteget_shortcodes( $form_options['ar_email_message'], $form_id );
		$mail_body      = $arflitefieldhelper->arflitereplaceshortcodes( $form_options['ar_email_message'], $entry, $shortcodes );
		$mail_body      = $arflitefieldhelper->arflite_replace_shortcodes( $mail_body, $entry, true );
		$arfblogname    = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$reply_to_name  = ( isset( $form_options['ar_user_from_name'] ) ) ? $form_options['ar_user_from_name'] : $arflitesettings->reply_to_name;
		$reply_to_name  = trim( $reply_to_name );
		$reply_to_id    = ( isset( $form_options['ar_user_from_email'] ) ) ? $form_options['ar_user_from_email'] : $arflitesettings->reply_to;
		if ( isset( $reply_to_id ) ) {
			$reply_to = isset( $entry->metas[ $reply_to_id ] ) ? $entry->metas[ $reply_to_id ] : '';
		}
		if ( $reply_to == '' ) {
			$reply_to = $reply_to_id;
		}
		$reply_to = trim( $reply_to );
		$to_email = '';
		foreach ( $values as $value ) {
			if ( (int) $email_field == $value->field_id ) {
				$val = apply_filters( 'arfliteemailvalue', maybe_unserialize( $value->entry_value ), $value, $entry );
				if ( is_email( $val ) ) {
					$to_email = $val;
				}
			}
		}
		$to_email = apply_filters( 'arflitebefore_autoresponse_change_mail_address_in_out_side', $to_email, $email_field, $entry_id, $form_id );
		if ( preg_match( '/(.*?)\((.*?)\)/', $to_email ) ) {
			$validate_email = preg_replace( '/(.*?)\((.*?)\)/', '$2', $to_email );
			if ( filter_var( $validate_email, FILTER_VALIDATE_EMAIL ) ) {
				$to_email = $validate_email;
			}
		}
		if ( ! isset( $to_email ) ) {
			return;
		}
		$get_default = true;
		$mail_body   = '';
		if ( isset( $form_options['ar_email_message'] ) && trim( $form_options['ar_email_message'] ) != '' ) {
			if ( ! preg_match( '/\[ARFLite_form_all_values\]/', $form_options['ar_email_message'] ) ) {
				$get_default = false;
			}
			$custom_message = true;
			$shortcodes     = $arflitemainhelper->arfliteget_shortcodes( $form_options['ar_email_message'], $entry->form_id );
			$mail_body      = $arflitefieldhelper->arflitereplaceshortcodes( $form_options['ar_email_message'], $entry, $shortcodes );
			$mail_body      = $arflitefieldhelper->arflite_replace_shortcodes( $mail_body, $entry, true );
		}

		$default = '';
		if ( $get_default && ! $plain_text ) {
			$default     .= "<table cellspacing='0' style='font-size:12px;line-height:135%; border-bottom:{$arflite_style_settings->arffieldborderwidthsetting} solid #{$arflite_style_settings->border_color};'><tbody>";
			$bg_color     = " style='background-color:#{$arflite_style_settings->bg_color};'";
			$bg_color_alt = " style='background-color:#{$arflite_style_settings->arfbgactivecolorsetting};'";
		}
		$odd         = true;
		$attachments = array();

		foreach ( $values as $value ) {
			$value = apply_filters( 'arflite_brfore_send_mail_change_value', $value, $entry_id, $form_id );
			
			$val = apply_filters( 'arfliteemailvalue', maybe_unserialize( $value->entry_value ), $value, $entry );

			if ( $value->field_type == 'checkbox' || $value->field_type == 'radio' || $value->field_type == 'select' ) {
				if ( isset( $value->entry_value ) ) {
					if ( is_array( maybe_unserialize( $value->entry_value ) ) ) {
						$val = implode( ', ', maybe_unserialize( $value->entry_value ) );
					} else {
						$val = $value->entry_value;
					}
				}
			}

			if ( $value->field_type == 'select' || $value->field_type == 'checkbox' || $value->field_type == 'radio' ) {
				global $wpdb,$ARFLiteMdlDb;
				$field_opts = $wpdb->get_row( $wpdb->prepare( 'SELECT entry_value FROM ' . $ARFLiteMdlDb->entry_metas . " WHERE field_id='%d' AND entry_id='%d'", '-' . $value->field_id, $entry->id ) );

				if ( $field_opts ) {
					$field_opts = maybe_unserialize( $field_opts->entry_value );

					if ( $value->field_type == 'checkbox' ) {
						if ( $field_opts && count( $field_opts ) > 0 ) {
							$temp_value = '';
							foreach ( $field_opts as $new_field_opt ) {
								$temp_value .= $new_field_opt['label'] . ' (' . $new_field_opt['value'] . '), ';
							}
							$temp_value = trim( $temp_value );
							$val        = rtrim( $temp_value, ',' );
						}
					} else {
						if ( $value->field_type == 'select' ) {
							$field_id       = $value->field_id;
							$field_tmp      = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $ARFLiteMdlDb->fields . " WHERE id = '%d'", $field_id ) );
							$field_tmp_opts = json_decode( $field_tmp->field_options, true );
							if ( json_last_error() != JSON_ERROR_NONE ) {
								$field_tmp_opts = maybe_unserialize( $field_tmp->field_options );
							}
							if ( $field_tmp_opts['separate_value'] ) {
								$label_field_id  = ( $value->field_id * 100 );
								$get_field_label = $wpdb->get_row( $wpdb->prepare( 'SELECT entry_value FROM ' . $ARFLiteMdlDb->entry_metas . ' WHERE field_id = "-%d" and entry_id="%d"', $label_field_id, $value->entry_id ) );
								$field_label     = isset( $get_field_label->entry_value ) ? $get_field_label->entry_value : '';
								if ( $field_label != '' ) {
									$val = stripslashes( $get_field_label->entry_value ) . ' (' . stripslashes( $field_opts['value'] ) . ')';
								} else {
									$val = $field_opts['label'] . ' (' . $field_opts['value'] . ')';
								}
							} else {
								$val = $field_opts['label'] . ' (' . $field_opts['value'] . ')';
							}
						} else {
							$val = $field_opts['label'] . ' (' . $field_opts['value'] . ')';
						}
					}
				}
			}
			if ( $value->field_type == 'textarea' && ! $plain_text ) {
				$val = str_replace( array( "\r\n", "\r", "\n" ), ' <br/>', $val );
			}
			if ( is_array( $val ) ) {
				$val = implode( ', ', $val );
			}

			if ( $get_default && $plain_text ) {
				$default .= $value->field_name . ': ' . $val . "\r\n\r\n";
			} elseif ( $get_default ) {
				$row_style = "valign='top' style='text-align:left;color:#{$arflite_style_settings->text_color};padding:7px 9px;border-top:{$arflite_style_settings->arffieldborderwidthsetting} solid #{$arflite_style_settings->border_color}'";
				$default  .= '<tr' . ( ( $odd ) ? $bg_color : $bg_color_alt ) . "><th $row_style>$value->field_name</th><td $row_style>$val</td></tr>";
				$odd       = ( $odd ) ? false : true;
			}

			if ( isset( $email_fields ) && is_array( $email_fields ) ) {
				if ( in_array( $value->field_id, $email_fields ) ) {
					$val = explode( ',', $val );
					if ( is_array( $val ) ) {
						foreach ( $val as $v ) {
							$v = trim( $v );
							if ( is_email( $v ) ) {
								$to_emails[] = $v;
							}
						}
					} elseif ( is_email( $val ) ) {
						$to_emails[] = $val;
					}
				}
			}
		}
		if ( $get_default && ! $plain_text ) {
			$default .= '</tbody></table>';
		}
		if ( isset( $form_options['ar_email_subject'] ) && $form_options['ar_email_subject'] != '' ) {
			$shortcodes = $arflitemainhelper->arfliteget_shortcodes( $form_options['ar_email_subject'], $form_id );
			$subject    = $arflitefieldhelper->arflitereplaceshortcodes( $form_options['ar_email_subject'], $entry, $shortcodes );
			$subject    = $arflitefieldhelper->arflite_replace_shortcodes( $subject, $entry, true );
		} else {
			$subject = sprintf(  __( '%1$s Form submitted on %2$s', 'arforms-form-builder' ) , stripslashes( $form->name ), $arfblogname );
		}
		$subject = trim( $subject );
		if ( $reply_to ) {

			$reply_to = $arflitefieldhelper->arflite_replace_shortcodes( $reply_to, $entry, true );
		}
        if( preg_match_all('/(.*?)\s+\(([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+)\)/',$reply_to,$rt_matches) ){
        	$reply_to = ( !empty( $rt_matches[2][0] ) ) ? $rt_matches[2][0] : '';
        }
		$user_nreplyto = ( isset( $form_options['ar_user_nreplyto_email'] ) ) ? $form_options['ar_user_nreplyto_email'] : $arflitesettings->reply_to;

		if ( isset( $user_nreplyto ) && $user_nreplyto != '' ) {
			$user_nreplyto = $arflitefieldhelper->arflite_replace_shortcodes( $user_nreplyto, $entry, true );
		}
        if( preg_match_all('/(.*?)\s+\(([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+)\)/',$user_nreplyto,$urt_matches) ){
        	$user_nreplyto = ( !empty( $urt_matches[2][0] ) ) ? $urt_matches[2][0] : '';
        }

		if ( $get_default && $custom_message ) {
			$mail_body = str_replace( '[ARFLite_form_all_values]', $default, $mail_body );
		} elseif ( $get_default ) {
			$mail_body = $default;
		}
		$data           = maybe_unserialize( $entry->description );
		$browser_info   = $this->arflitegetBrowser( $data['browser'] );
		$browser_detail = $browser_info['name'] . ' (Version: ' . $browser_info['version'] . ')';
		if ( preg_match( '/\[ARFLite_form_ipaddress\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_ipaddress]', $entry->ip_address, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_browsername\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_browsername]', $browser_detail, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_referer\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_referer]', $data['http_referrer'], $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_entryid\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_form_entryid]', $entry->id, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_form_added_date_time\]/', $mail_body ) ) {
			$wp_date_format = get_option( 'date_format' );
			$wp_time_format = get_option( 'time_format' );
			$mail_body      = str_replace( '[ARFLite_form_added_date_time]', date( $wp_date_format . ' ' . $wp_time_format, strtotime( $entry->created_date ) ), $mail_body );
		}

		$arf_current_user = wp_get_current_user();
		if ( preg_match( '/\[ARFLite_current_userid\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_userid]', $arf_current_user->ID, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_current_username\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_username]', $arf_current_user->user_login, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_current_useremail\]/', $mail_body ) ) {
			$mail_body = str_replace( '[ARFLite_current_useremail]', $arf_current_user->user_email, $mail_body );
		}
		if ( preg_match( '/\[ARFLite_page_url\]/', $mail_body ) ) {
			$entry_desc = maybe_unserialize( $entry->description );
			$mail_body  = str_replace( '[ARFLite_page_url]', $entry_desc['page_url'], $mail_body );
		}

		$mail_body   = apply_filters( 'arflitebefore_autoresponse_send_mail_body', $mail_body, $entry_id, $form_id );
		$attachments = apply_filters( 'arfliteautoresponderattachment', $attachments, $form, array( 'entry' => $entry ) );
		$mail_body   = nl2br( $mail_body );

		$arflitenotifymodel->arflite_send_notification_email_user( $to_email, $subject, $mail_body, $reply_to, $reply_to_name, $plain_text, $attachments, false, false, false, false, $user_nreplyto );
		return $to_email;
	}

	function arflitechangesmtpsetting( $phpmailer ) {
		global $arflitesettings;

		if ( isset( $arflitesettings->is_smtp_authentication ) && $arflitesettings->is_smtp_authentication == '1' ) {
			if ( ! isset( $arflitesettings->smtp_host ) || empty( $arflitesettings->smtp_host ) || ! isset( $arflitesettings->smtp_username ) || empty( $arflitesettings->smtp_username ) || ! isset( $arflitesettings->smtp_password ) || empty( $arflitesettings->smtp_password ) ) {
				return;
			}
		} else {
			if ( ! isset( $arflitesettings->smtp_host ) || empty( $arflitesettings->smtp_host ) ) {
				return;
			}
		}

		if ( ! isset( $arflitesettings->smtp_port ) || empty( $arflitesettings->smtp_port ) ) {
			$arflitesettings->smtp_port = 25;
		}

		$phpmailer->IsSMTP();

		$phpmailer->Host = $arflitesettings->smtp_host;
		$phpmailer->Port = $arflitesettings->smtp_port;

		if ( isset( $arflitesettings->is_smtp_authentication ) && $arflitesettings->is_smtp_authentication == '1' ) {
			$phpmailer->SMTPAuth = true;
		} else {
			$phpmailer->SMTPAuth = false;
		}

		$phpmailer->Username = $arflitesettings->smtp_username;
		$phpmailer->Password = $arflitesettings->smtp_password;
		if ( isset( $arflitesettings->smtp_encryption ) && $arflitesettings->smtp_encryption != '' && $arflitesettings->smtp_encryption != 'none' ) {
			$phpmailer->SMTPSecure = $arflitesettings->smtp_encryption;
		}
	}

	function arflitegetBrowser( $user_agent ) {
		$u_agent  = $user_agent;
		$bname    = 'Unknown';
		$platform = 'Unknown';
		$version  = '';
		$ub       = '';

		if ( preg_match( '/linux/i', $u_agent ) ) {
			$platform = 'linux';
		} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
			$platform = 'mac';
		} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
			$platform = 'windows';
		}

		if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Internet Explorer';
			$ub    = 'MSIE';
		} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
			$bname = 'Mozilla Firefox';
			$ub    = 'Firefox';
		} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
			$bname = 'Google Chrome';
			$ub    = 'Chrome';
		} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
			$bname = 'Apple Safari';
			$ub    = 'Safari';
		} elseif ( preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Opera';
			$ub    = 'Opera';
		} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
			$bname = 'Netscape';
			$ub    = 'Netscape';
		}

		$known   = array( 'Version', $ub, 'other' );
		$pattern = '#(?<browser>' . join( '|', $known ) .
				')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {

		}

		$i = count( $matches['browser'] );
		if ( $i != 1 ) {

			if ( strripos( $u_agent, 'Version' ) < strripos( $u_agent, $ub ) ) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}

		if ( $version == null || $version == '' ) {
			$version = '?';
		}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'   => $pattern,
		);
	}

}
