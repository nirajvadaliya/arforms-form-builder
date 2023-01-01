<?php

if( version_compare( $arflitenewdbversion, '1.3', '<') ){
	$nextEvent = strtotime( '+1 week' );

	wp_schedule_single_event( $nextEvent, 'arflite_display_ratenow_popup' );
}

if( version_compare( $arflitenewdbversion, '1.5.1', '<') ){
	global $wpdb, $ARFLiteMdlDb;

	$wpdb->query("ALTER TABLE `".$ARFLiteMdlDb->forms."` ADD `arflite_update_form` TINYINT(1) NOT NULL DEFAULT '0'");

	$get_form_ids = $wpdb->get_results( $wpdb->prepare( "SELECT form_id FROM `" . $ARFLiteMdlDb->fields . "` WHERE ( type = %s OR type = %s OR type = %s ) GROUP BY form_id", 'select', 'checkbox', 'radio' ) );

	if( !empty( $get_form_ids ) ){
		foreach( $get_form_ids as $form_data ){
			$form_id = $form_data->form_id;

			$wpdb->update(
				$ARFLiteMdlDb->forms,
				array(
					'arflite_update_form' => 1
				),
				array(
					'id' => $form_id
				)
			);
		}
	}
}

if( version_compare( $arflitenewdbversion, '1.5.2', '<') ){
	global $wpdb, $ARFLiteMdlDb;

	$wpdb->update(
		$ARFLiteMdlDb->forms,
		array(
			'arflite_update_form' => 1
		),
		array(
			'is_template' => 0
		)
	);
}

update_option( 'arflite_db_version', '1.5.3' );

delete_transient( 'arforms_form_builder_addon_page_notice' );

global $arflitenewdbversion;
$arflitenewdbversion = '1.5.3';

update_option('arflite_new_version_installed', intval(1) );
update_option('arflite_update_date_' . $arflitenewdbversion, current_time( 'mysql'  ) );
