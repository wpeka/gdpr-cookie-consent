<?php
// No need for the template engine
define( 'WP_USE_THEMES', false );

// find the base path
define( 'BASE_PATH', find_wordpress_base_path() . '/' );

// Load WordPress Core
if ( ! file_exists( BASE_PATH . 'wp-load.php' ) ) {
	die( 'WordPress not installed here' );
}
require_once BASE_PATH . 'wp-load.php';
require_once ABSPATH . 'wp-includes/class-phpass.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if ( isset( $_GET['nonce'] ) ) {
	$nonce = $_GET['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'wpl_csv_nonce' ) ) {
		die( '1 invalid command' );
	}
} else {
	die( '2 invalid command' );
}

// if ( ! wpl_user_can_manage() ) {
// die( "no permission here, invalid command" );
// }

function array_to_csv_download(
	$array,
	$filename = 'export.csv',
	$delimiter = ';'
) {
	header( 'Content-Type: application/csv;charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
	// fix ö ë etc character encoding issues:
	echo "\xEF\xBB\xBF"; // UTF-8 BOM
	// open the "output" stream
	// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
	$f = fopen( 'php://output', 'w' );

	foreach ( $array as $line ) {
		fputcsv( $f, $line, $delimiter );
	}
}

$file_title = 'wpl-export-' . date( 'j' ) . ' ' . __( date( 'F' ) ) . ' ' . date( 'Y' );
array_to_csv_download( export_array(), $file_title . '.csv' );

function get_requests( $args ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wpl_data_req';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) {
		// Table doesn't exist yet, return empty result set or handle as needed
		return array();
	}
	$sql = "SELECT * from {$wpdb->prefix}wpl_data_req WHERE 1=1 ";
	if ( isset( $args['email'] ) && ! empty( $args['email'] ) && is_email( $args['email'] ) ) {
		$sql .= " AND email like '" . '%' . sanitize_email( $args['email'] ) . '%' . "'";
	}

	if ( isset( $args['name'] ) && ! empty( $args['name'] ) ) {
		$sql .= " AND name like '%" . sanitize_text_field( $args['name'] ) . "%'";
	}

	if ( isset( $args['resolved'] ) ) {
		$sql .= ' AND resolved = ' . intval( $args['resolved'] );
	}

	$sql .= ' ORDER BY ' . sanitize_title( $args['orderby'] ) . ' ' . sanitize_title( $args['order'] );
	if ( isset( $args['number'] ) ) {
		$sql .= ' LIMIT ' . intval( $args['number'] ) . ' OFFSET ' . intval( $args['offset'] );
	}

	return $wpdb->get_results( $sql );
}

function wpl_localize_date( $unix_time ) {

	$formatted_date    = date( get_option( 'date_format' ), $unix_time );
	$month             = date( 'F', $unix_time ); // june
	$month_localized   = __( $month ); // juni
	$date              = str_replace( $month, $month_localized, $formatted_date );
	$weekday           = date( 'l', $unix_time ); // wednesday
	$weekday_localized = __( $weekday ); // woensdag
	$date              = str_replace( $weekday, $weekday_localized, $date );
	return $date;
}

function wpl_sprintf() {

	$args       = func_get_args();
	$count      = substr_count( $args[0], '%s' );
	$args_count = count( $args ) - 1;
	if ( $args_count === $count ) {
		return call_user_func_array( 'sprintf', $args );
	} else {
		$output = $args[0];
		if ( is_admin() ) {
			$output .= 'Translation error';
		}
		return $output;
	}
}

function export_array() {

	$requests = get_requests(
		array(
			'orderby' => 'ID',
			'order'   => 'DESC',
		)
	);

	$output   = array();
	$output[] = array(
		__( 'Name', 'gdpr-cookie-consent' ),
		__( 'Email', 'gdpr-cookie-consent' ),
		__( 'Resolved', 'gdpr-cookie-consent' ),
		__( 'Data request', 'gdpr-cookie-consent' ),
		__( 'Date', 'gdpr-cookie-consent' ),
	);

	foreach ( $requests as $request ) {
		$datarequest = '';
		$options     = Gdpr_Cookie_Consent_Admin::wpl_data_reqs_options();
		foreach ( $options as $fieldname => $label ) {
			if ( $request->{$fieldname} == 1 ) {
				$datarequest = $label['short'];
			}
		}
		$time = date( get_option( 'time_format' ), $request->request_date );
		$date = wpl_localize_date( $request->request_date );
		// Translators: Placeholder %1$s represents the date, %2$s represents the time.
		$date_time_format = __( '%1$s at %2$s', 'gdpr-cookie-consent' );
		$date             = sprintf( $date_time_format, $date, $time );
		$output[]         = array( $request->name, $request->email, $request->resolved, $datarequest, $date );
	}

	return $output;
}

function find_wordpress_base_path() {
	$path = __DIR__;

	do {
		// it is possible to check for other files here
		if ( file_exists( $path . '/wp-config.php' ) ) {
			// check if the wp-load.php file exists here. If not, we assume it's in a subdir.
			if ( file_exists( $path . '/wp-load.php' ) ) {
				return $path;
			} else {
				// wp not in this directory. Look in each folder to see if it's there.
				if ( file_exists( $path ) && $handle = opendir( $path ) ) {
					while ( false !== ( $file = readdir( $handle ) ) ) {
						if ( $file != '.' && $file != '..' ) {
							$file = $path . '/' . $file;
							if ( is_dir( $file ) && file_exists( $file . '/wp-load.php' ) ) {
								$path = $file;
								break;
							}
						}
					}
					closedir( $handle );
				}
			}

			return $path;
		}
	} while ( $path = realpath( "$path/.." ) );

	return false;
}
