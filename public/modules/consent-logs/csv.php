<?php
/**
 * The CSV functionality for consent logs of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

// No need for the template engine.
define( 'WP_USE_THEMES', false );

// find the base path.
define( 'BASE_PATH', find_wordpress_base_path() . '/' );

// Load WordPress Core.
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

/**
 * Convert an array to a CSV file and trigger a download.
 *
 * @param array  $array     The array to be converted to CSV.
 * @param string $filename  Optional. The filename for the downloaded CSV. Defaults to 'export.csv'.
 * @param string $delimiter Optional. The CSV field delimiter. Defaults to ';'.
 *
 * @return void
 */
function array_to_csv_download(
	$array,
	$filename = 'export.csv',
	$delimiter = ';'
) {
	header( 'Content-Type: application/csv;charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
	// fix ö ë etc character encoding issues:.
	echo "\xEF\xBB\xBF"; // UTF-8 BOM.
	// open the "output" stream.
	// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq.
	$f = fopen( 'php://output', 'w' );

	foreach ( $array as $line ) {
		fputcsv( $f, $line, $delimiter );
	}
}

$file_title = 'consent-logs-export-' . date( 'j' ) . ' ' . __( date( 'F' ) ) . ' ' . date( 'Y' );
array_to_csv_download( export_array(), $file_title . '.csv' );

/**
 * Get consent log requests based on provided arguments.
 *
 * @param array $args {
 *     Optional. Arguments for retrieving consent log requests.
 *
 *     @type int $number Number of consent log requests to retrieve. Default is 10.
 *     @type int $offset Offset for pagination. Default is 0.
 * }
 *
 * @global WP_Post $post WordPress post object.
 *
 * @return array An array containing consent log request data.
 */
function get_requests( $args ) {

	global $post;
	$number = isset( $args['number'] ) ? intval( $args['number'] ) : 10;
	$offset = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;

	$post_args = array(
		'post_type'      => 'wplconsentlogs',
		'posts_per_page' => -1,
		'offset'         => $offset,
		'post_status'    => 'publish', // Retrieve all posts.
		'orderby'        => 'ID',
		'order'          => 'DESC',
		'meta_query'     => array(),
	);

	$custom_posts     = get_posts( $post_args );
	$all_consent_data = array(); // Initialize the $data array.

	if ( ! is_multisite() ) {
		foreach ( $custom_posts as $post ) {

			setup_postdata( $post ); // Setup post data for each post.

			$post_id = $post->ID;

			// Fetch specific values from post meta using keys.
			$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip', true );

			$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country', true );

			if ( empty( $wplconsentlogs_country ) ) {
				$wplconsentlogs_country = 'Unknown';
			}

			// date.
			$content_post = get_post( $post_id );

			$time_utc      = $content_post->post_date_gmt;
			$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
			$tz_string     = wp_timezone_string();
			$timezone      = new DateTimeZone( $tz_string );
			$local_time    = date( 'd', $utc_timestamp ) . '/' . date( 'm', $utc_timestamp ) . '/' . date( 'Y', $utc_timestamp );

			if ( $content_post ) {
				$wplconsentlogs_dates = $local_time;
			}

			// consent status.

			$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details', true );

			$wplconsentlogs_details = 'wpl_viewed_cookie : ' . $wplconsentlogs_details['wpl_viewed_cookie'] . $wplconsentlogs_details['wpl_user_preference'];

			// all data for table.
			$all_consent_data[] = array(
				'ID'                    => $post_id,
				'wplconsentlogsip'      => $wplconsentlogs_ip,
				'wplconsentlogsdates'   => $wplconsentlogs_dates,
				'wplconsentlogsdetails' => $wplconsentlogs_details,
			);
		}
	} else {
		foreach ( $custom_posts as $post ) {

			setup_postdata( $post ); // Setup post data for each post.

			$post_id = $post->ID;

			// Fetch specific values from post meta using keys.
			$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip_cf', true );

			$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country_cf', true );

			if ( empty( $wplconsentlogs_country ) ) {
				$wplconsentlogs_country = 'Unknown';
			}

			// date.
			$content_post = get_post( $post_id );

			$time_utc      = $content_post->post_date_gmt;
			$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
			$tz_string     = wp_timezone_string();
			$timezone      = new DateTimeZone( $tz_string );
			$local_time    = date( 'd', $utc_timestamp ) . '/' . date( 'm', $utc_timestamp ) . '/' . date( 'Y', $utc_timestamp );

			if ( $content_post ) {
				$wplconsentlogs_dates = $local_time;
			}

			// consent status.

			$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details_cf', true );

			$wplconsentlogs_details = 'wpl_viewed_cookie : ' . $wplconsentlogs_details['wpl_viewed_cookie'] . $wplconsentlogs_details['wpl_user_preference'];

			// all data for table.
			$all_consent_data[] = array(
				'ID'                    => $post_id,
				'wplconsentlogsip'      => $wplconsentlogs_ip,
				'wplconsentlogsdates'   => $wplconsentlogs_dates,
				'wplconsentlogsdetails' => $wplconsentlogs_details,
			);
		}
	}

	return $all_consent_data;
}

/**
 * Export consent log data as an array for further processing or display.
 *
 * @return array An array containing consent log data for export.
 */
function export_array() {

	$requests = get_requests(
		array(
			'orderby' => 'ID',
			'order'   => 'DESC',
		)
	);

	$output   = array();
	$output[] = array(
		__( 'IP Address', 'gdpr-cookie-consent' ),
		__( 'Visited Date', 'gdpr-cookie-consent' ),
		__( 'Consent Log Details', 'gdpr-cookie-consent' ),
	);

	foreach ( $requests as $request ) {
		$output[] = array( $request['wplconsentlogsip'], $request['wplconsentlogsdates'], $request['wplconsentlogsdetails'] );
	}

	return $output;
}

/**
 * Find the base path of the WordPress installation.
 *
 * This function searches for the WordPress installation path starting from the current directory
 * and traverses up the directory structure until it finds the wp-config.php file. It then checks
 * if the wp-load.php file exists in the same directory or any subdirectory, and returns the path
 * where it is found.
 *
 * @return string|false The path to the WordPress installation or false if not found.
 */
function find_wordpress_base_path() {
	$path = __DIR__;

	do {
		// it is possible to check for other files here.
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
