<?php
/**
 * Class GDPR Cookie Consent Api file.
 *
 * @package Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class GDPR_Cookie_Consent_Api.
 */
class GDPR_Cookie_Consent_Api extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'gdpr/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 10 );
	}

	/**
	 * Register the routes for app.
	 *
	 * @return void
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'create_items_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';
		$object = new GDPR_Cookie_Consent_Settings();
		$data   = $object->get();
		return rest_ensure_response( $data );
	}

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_items_permissions_check( $request ) {

		$permission_check = false;
		$token            = $request->get_param( 'token' );
		$request_platform = $request->get_param( 'platform' );

		if ( isset( $token ) && 'wordpress' === $request_platform ) {
			return true;
		} else {
			return new WP_Error( 'rest_forbidden', esc_html__( 'Invalid Authorization.', 'gdpr-cookie-consent' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return $permission_check;
	}

}
