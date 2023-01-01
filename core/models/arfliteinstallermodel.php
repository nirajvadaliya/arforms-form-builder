<?php

class arfliteinstallermodel {

	var $fields;
	var $forms;
	var $entries;
	var $entry_metas;
	var $autoresponder;
	var $ar;
	var $view;

	function __construct() {
		global $wpdb,$blog_id;

		if ( $blog_id && IS_WPMU ) {
			$prefix = $wpdb->get_blog_prefix( $blog_id );

			$this->fields      = "{$prefix}arflite_fields";
			$this->forms       = "{$prefix}arflite_forms";
			$this->entries     = "{$prefix}arflite_entries";
			$this->entry_metas = "{$prefix}arflite_entry_values";

		} else {
			$this->fields      = $wpdb->prefix . 'arflite_fields';
			$this->forms       = $wpdb->prefix . 'arflite_forms';
			$this->entries     = $wpdb->prefix . 'arflite_entries';
			$this->entry_metas = $wpdb->prefix . 'arflite_entry_values';
		}

	}

	function arfliteupgrade( $old_db_version = false ) {

		global $wpdb, $arflitedbversion;

		$old_db_version = (float) $old_db_version;

		if ( ! $old_db_version ) {
			$old_db_version = get_option( 'arflite_db_version' );
		}

		if ( $arflitedbversion != $old_db_version ) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$charset_collate = '';

			if ( $wpdb->has_cap( 'collation' ) ) {

				if ( ! empty( $wpdb->charset ) ) {
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				}

				if ( ! empty( $wpdb->collate ) ) {
					$charset_collate .= " COLLATE $wpdb->collate";
				}
			}

			$sql = "CREATE TABLE IF NOT EXISTS {$this->fields} (

                id int(11) NOT NULL auto_increment,

                field_key varchar(25) default NULL,

                name text default NULL,

                type varchar(50) default NULL,

                options longtext default NULL,

                required int(1) default NULL,

                field_options longtext default NULL,

                form_id int(11) default NULL,

                created_date datetime NOT NULL,

                option_order text default NULL,

                PRIMARY KEY  (id),

                KEY form_id (form_id),

                UNIQUE KEY field_key (field_key)


              ) {$charset_collate};";

			dbDelta( $sql );

			if ( $wpdb->last_error !== '' ) {
				update_option( 'ARF_ERROR_' . time() . rand(), 'ERROR===>' . htmlspecialchars( $wpdb->last_result, ENT_QUOTES ) . 'QUERY===>' . htmlspecialchars( $wpdb->last_query, ENT_QUOTES ) ); }

			$sql = "CREATE TABLE IF NOT EXISTS {$this->forms} (

                id int(11) NOT NULL auto_increment,

                form_key varchar(25) default NULL,

                name varchar(255) default NULL,

                description text default NULL,

                is_template boolean default 0,

                status varchar(25) default NULL,

                options longtext default NULL,

                created_date datetime NOT NULL,

        		columns_list text default NULL,

        		form_css longtext default NULL,

                temp_fields longtext default NULL,

				arflite_update_form tinyint(1) default 0,

                PRIMARY KEY  (id),

                UNIQUE KEY form_key (form_key)

              ) {$charset_collate};";

				dbDelta( $sql );

			if ( $wpdb->last_error !== '' ) {
				update_option( 'ARF_ERROR_' . time() . rand(), 'ERROR===>' . htmlspecialchars( $wpdb->last_result, ENT_QUOTES ) . 'QUERY===>' . htmlspecialchars( $wpdb->last_query, ENT_QUOTES ) ); }

			$sql = "CREATE TABLE IF NOT EXISTS {$this->entries} (

                id int(11) NOT NULL auto_increment,

                entry_key varchar(25) default NULL,

                name varchar(255) default NULL,

                description text default NULL,

                ip_address varchar(255) default NULL,

		        country varchar(255) default NULL,

                browser_info text default NULL,

                form_id int(11) default NULL,

                attachment_id int(11) default NULL,

                user_id int(11) default NULL,

                created_date datetime NOT NULL,

                PRIMARY KEY  (id),

                KEY form_id (form_id),

                KEY attachment_id (attachment_id),

                KEY user_id (user_id),

                UNIQUE KEY entry_key (entry_key)

              ) {$charset_collate};";

			dbDelta( $sql );
			if ( $wpdb->last_error !== '' ) {
				update_option( 'ARF_ERROR_' . time() . rand(), 'ERROR===>' . htmlspecialchars( $wpdb->last_result, ENT_QUOTES ) . 'QUERY===>' . htmlspecialchars( $wpdb->last_query, ENT_QUOTES ) ); }

			$sql = "CREATE TABLE IF NOT EXISTS {$this->entry_metas} (

                id int(11) NOT NULL auto_increment,

                entry_value longtext default NULL,

                field_id int(11) NOT NULL,

                entry_id int(11) NOT NULL,

                created_date datetime NOT NULL,

                PRIMARY KEY  (id),

                KEY field_id (field_id),

                KEY entry_id (entry_id)

              ) {$charset_collate};";

					dbDelta( $sql );
			if ( $wpdb->last_error !== '' ) {
				update_option( 'ARF_ERROR_' . time() . rand(), 'ERROR===>' . htmlspecialchars( $wpdb->last_result, ENT_QUOTES ) . 'QUERY===>' . htmlspecialchars( $wpdb->last_query, ENT_QUOTES ) ); }

			update_option( 'arflite_db_version', $arflitedbversion );

			$target_path = ARFLITE_UPLOAD_DIR;

			wp_mkdir_p( $target_path );

			$target_path .= '/maincss';

			wp_mkdir_p( $target_path );

			global $arflitesettings;
			$arflitesettings = get_transient( 'arflite_options' );

			if ( ! is_object( $arflitesettings ) ) {
				if ( $arflitesettings ) {
					$arflitesettings = maybe_unserialize( maybe_serialize( $arflitesettings ) );
				} else {
					$arflitesettings = get_option( 'arflite_options' );

					if ( ! is_object( $arflitesettings ) ) {
						if ( $arflitesettings ) {
							$arflitesettings = maybe_unserialize( maybe_serialize( $arflitesettings ) );
						} else {
							$arflitesettings = new arflitesettingmodel();
						}
						update_option( 'arflite_options', $arflitesettings );
						set_transient( 'arflite_options', $arflitesettings );
					}
				}
			}

			$arflitesettings->arflite_set_default_options();

			global $arflite_style_settings, $arflitemaincontroller;

			$arflite_style_settings = get_transient( 'arfalite_options' );
			if ( ! is_object( $arflite_style_settings ) ) {
				if ( $arflite_style_settings ) {
					$arflite_style_settings = maybe_unserialize( maybe_serialize( $arflite_style_settings ) );
				} else {
					$arflite_style_settings = get_option( 'arfalite_options' );
					if ( ! is_object( $arflite_style_settings ) ) {
						if ( $arflite_style_settings ) {
							$arflite_style_settings = maybe_unserialize( maybe_serialize( $arflite_style_settings ) );
						} else {
							$arflite_style_settings = new arflitestylemodel();
						}
						update_option( 'arfalite_options', $arflite_style_settings );
						set_transient( 'arfalite_options', $arflite_style_settings );
					}
				}
			}
			$arflite_style_settings = get_option( 'arfalite_options' );
			if ( ! is_object( $arflite_style_settings ) ) {
				if ( $arflite_style_settings ) {
					$arflite_style_settings = maybe_unserialize( maybe_serialize( $arflite_style_settings ) );
				} else {
					$arflite_style_settings = new arflitestylemodel();
				}
				update_option( 'arfalite_options', $arflite_style_settings );
			}

			$arflite_style_settings->arflite_set_default_options();
			$arflite_style_settings->arflitestore();

			if ( ! is_admin() && $arflitesettings->jquery_css ) {
				$arflitedatepickerloaded = true;
			}

			global $wpdb;
			$wpdb->query( "ALTER TABLE {$this->forms} AUTO_INCREMENT = 100" );
			if ( $wpdb->last_error !== '' ) {
				update_option( 'ARF_ERROR_' . time() . rand(), 'ERROR===>' . htmlspecialchars( $wpdb->last_result, ENT_QUOTES ) . 'QUERY===>' . htmlspecialchars( $wpdb->last_query, ENT_QUOTES ) ); }

			$arflitemaincontroller->arflite_getwpversion();

			update_option( 'arflite_form_entry_separator', sanitize_text_field( 'arf_comma' ) );

			update_option( 'arflite_plugin_activated', 1 );
		}

		do_action( 'arfliteafterinstall' );
	}

	function arflite_get_count( $table, $args = array() ) {

		global $wpdb, $ARFLiteMdlDb;

		extract( $ARFLiteMdlDb->arflite_get_where_clause_and_values( $args ) );

		$query = "SELECT COUNT(*) FROM {$table}{$where}";

		$query = $wpdb->prepare( $query, $values );

		return $wpdb->get_var( $query );
	}

	function arflite_get_where_clause_and_values( $args ) {

		$where = '';

		$values = array();

		if ( is_array( $args ) ) {

			foreach ( $args as $key => $value ) {

				$where .= ( ! empty( $where ) ) ? ' AND' : ' WHERE';

				$where .= " {$key}=";

				$where .= ( is_numeric( $value ) ) ? '%d' : '%s';

				$values[] = $value;
			}
		}

		return compact( 'where', 'values' );
	}

	function arfliteget_var( $table, $args = array(), $field = 'id', $order_by = '' ) {

		global $wpdb, $ARFLiteMdlDb;

		extract( $ARFLiteMdlDb->arflite_get_where_clause_and_values( $args ) );

		if ( ! empty( $order_by ) ) {
			$order_by = " ORDER BY {$order_by}";
		}

		$query = $wpdb->prepare( "SELECT {$field} FROM {$table}{$where}{$order_by} LIMIT 1", $values );

		return $wpdb->get_var( $query );
	}

	function arfliteget_col( $table, $args = array(), $field = 'id', $order_by = '' ) {

		global $wpdb, $ARFLiteMdlDb;

		extract( $ARFLiteMdlDb->arflite_get_where_clause_and_values( $args ) );

		if ( ! empty( $order_by ) ) {
			$order_by = " ORDER BY {$order_by}";
		}

		$query = $wpdb->prepare( "SELECT {$field} FROM {$table}{$where}{$order_by}", $values );

		return $wpdb->get_col( $query );
	}

	function arflite_get_one_record( $table, $args = array(), $fields = '*', $order_by = '' ) {

		global $wpdb, $ARFLiteMdlDb;

		extract( $ARFLiteMdlDb->arflite_get_where_clause_and_values( $args ) );

		if ( ! empty( $order_by ) ) {
			$order_by = " ORDER BY {$order_by}";
		}

		$query = "SELECT {$fields} FROM {$table}{$where} {$order_by} LIMIT 1";

		$query = $wpdb->prepare( $query, $values );

		return $wpdb->get_row( $query );
	}

	function arflite_get_records( $table, $args = array(), $order_by = '', $limit = '', $fields = '*' ) {

		global $wpdb, $ARFLiteMdlDb;

		extract( $ARFLiteMdlDb->arflite_get_where_clause_and_values( $args ) );

		if ( ! empty( $order_by ) ) {
			$order_by = " ORDER BY {$order_by}";
		}

		if ( ! empty( $limit ) ) {
			$limit = " LIMIT {$limit}";
		}

		$query = "SELECT {$fields} FROM {$table}{$where}{$order_by}{$limit}";

		$query = $wpdb->prepare( $query, $values );

		return $wpdb->get_results( $query );
	}

	function arflite_assign_rand_value( $num ) {

		switch ( $num ) {
			case '1':
				$rand_value = 'a';
				break;
			case '2':
				$rand_value = 'b';
				break;
			case '3':
				$rand_value = 'c';
				break;
			case '4':
				$rand_value = 'd';
				break;
			case '5':
				$rand_value = 'e';
				break;
			case '6':
				$rand_value = 'f';
				break;
			case '7':
				$rand_value = 'g';
				break;
			case '8':
				$rand_value = 'h';
				break;
			case '9':
				$rand_value = 'i';
				break;
			case '10':
				$rand_value = 'j';
				break;
			case '11':
				$rand_value = 'k';
				break;
			case '12':
				$rand_value = 'l';
				break;
			case '13':
				$rand_value = 'm';
				break;
			case '14':
				$rand_value = 'n';
				break;
			case '15':
				$rand_value = 'o';
				break;
			case '16':
				$rand_value = 'p';
				break;
			case '17':
				$rand_value = 'q';
				break;
			case '18':
				$rand_value = 'r';
				break;
			case '19':
				$rand_value = 's';
				break;
			case '20':
				$rand_value = 't';
				break;
			case '21':
				$rand_value = 'u';
				break;
			case '22':
				$rand_value = 'v';
				break;
			case '23':
				$rand_value = 'w';
				break;
			case '24':
				$rand_value = 'x';
				break;
			case '25':
				$rand_value = 'y';
				break;
			case '26':
				$rand_value = 'z';
				break;
			case '27':
				$rand_value = '0';
				break;
			case '28':
				$rand_value = '1';
				break;
			case '29':
				$rand_value = '2';
				break;
			case '30':
				$rand_value = '3';
				break;
			case '31':
				$rand_value = '4';
				break;
			case '32':
				$rand_value = '5';
				break;
			case '33':
				$rand_value = '6';
				break;
			case '34':
				$rand_value = '7';
				break;
			case '35':
				$rand_value = '8';
				break;
			case '36':
				$rand_value = '9';
				break;
		}
		return $rand_value;
	}

	function arflite_get_rand_alphanumeric( $length ) {
		global $ARFLiteMdlDb;
		if ( $length > 0 ) {
			$rand_id = '';
			for ( $i = 1; $i <= $length; $i++ ) {
				mt_srand( (float) microtime() * 1000000 );
				$num      = mt_rand( 1, 36 );
				$rand_id .= $ARFLiteMdlDb->arflite_assign_rand_value( $num );
			}
		}
		return $rand_id;
	}
}
