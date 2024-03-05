<?php
/**
 * The script blocker ajax functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The admin-specific ajax functionality for script blocker.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Script_Blocker_Ajax extends Gdpr_Cookie_Consent_Script_Blocker {

	/**
	 * Gdpr_Cookie_Consent_Script_Blocker_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wpl_script_blocker', array( $this, 'ajax_script_blocker' ) );
	}

	/**
	 * Main ajax hook for processing request.
	 *
	 * @since 3.0.0
	 */
	public function ajax_script_blocker() {
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to handle your request.', 'gdpr-cookie-consent' ),
		);
		if ( isset( $_POST['wpl_script_action'] ) ) {
			check_admin_referer( 'wpl_script_blocker', 'security' );
			$wpl_script_action = sanitize_text_field( wp_unslash( $_POST['wpl_script_action'] ) );
			$allowed_actions   = array( 'update_script_status', 'update_script_category', 'update_script_blocker' );
			if ( in_array( $wpl_script_action, $allowed_actions, true ) && method_exists( $this, $wpl_script_action ) ) {
				$out = $this->{$wpl_script_action}();
			}
		}
		echo wp_json_encode( $out );
		exit();
	}

	/**
	 * Ajax processing for updating script status.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function update_script_status() {
		global $wpdb;
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to update status', 'gdpr-cookie-consent' ),
		);
		if ( isset( $_POST['id'] ) ) {
			check_admin_referer( 'wpl_script_blocker', 'security' );
			$status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '0';
			$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
			if ( ! empty( $id ) ) {
				$update_status = $wpdb->update( $wpdb->prefix . $this->main_table, array( 'script_status' => $status ), array( 'id' => $id ) ); // db call ok; no-cache ok.
				if ( $update_status >= 1 ) {
					$out['response'] = true;
					$out['message']  = __( 'Status updated successfully', 'gdpr-cookie-consent' );
				}
			}
		}
		return $out;
	}

	/**
	 * Ajax processing for updating script category.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function update_script_category() {
		global $wpdb;
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to update category', 'gdpr-cookie-consent' ),
		);
		if ( isset( $_POST['id'] ) ) {
			check_admin_referer( 'wpl_script_blocker', 'security' );
			$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '5';
			$id       = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
			if ( ! empty( $id ) ) {
				$update_status = $wpdb->update( $wpdb->prefix . $this->main_table, array( 'script_category' => $category ), array( 'id' => $id ) ); // db call ok; no-cache ok.
				if ( $update_status >= 1 ) {
					$out['response'] = true;
					$out['message']  = __( 'Category updated successfully', 'gdpr-cookie-consent' );
				}
			}
		}
		return $out;
	}

	/**
	 * Ajax processing for updating script blocker.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function update_script_blocker() {

		$out = array(
			'response' => false,
			'message'  => __( 'Unable to update script blocker', 'gdpr-cookie-consent' ),
		);
		if ( isset( $_POST['script_blocker_status'] ) ) {
			check_admin_referer( 'wpl_script_blocker', 'security' );
			$script_blocker_status = isset( $_POST['script_blocker_status'] ) ? sanitize_text_field( wp_unslash( $_POST['script_blocker_status'] ) ) : 'false';
			$the_options           = Gdpr_Cookie_Consent::gdpr_get_settings();
			if ( 'true' === $script_blocker_status || true === $script_blocker_status ) {
				$the_options['is_script_blocker_on'] = true;
			} elseif ( 'false' === $script_blocker_status || false === $script_blocker_status ) {
				$the_options['is_script_blocker_on'] = false;
			}
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			$out['response'] = true;
			$out['message']  = __( 'Script blocker updated successfully', 'gdpr-cookie-consent' );
		}
		return $out;
	}
}
new Gdpr_Cookie_Consent_Script_Blocker_Ajax();
