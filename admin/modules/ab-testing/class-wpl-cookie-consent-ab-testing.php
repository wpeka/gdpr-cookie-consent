<?php
/**
 * The a/b testing functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 * @package    Wpl_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// use GeoIp2\Database\Reader;
/**
 * The admin-specific functionality for a/b testing.
 *
 * @package    Wpl_Cookie_Consent
 * @subpackage Wpl_Cookie_Consent/admin/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_AB_Testing {
	/**
	 * Display errors.
	 *
	 * @var array $errors Display errors.
	 */
	private static $errors = array();
	/**
	 * Gdpr_Cookie_Consent_Geo_Ip constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_action( 'gdpr_settings_ab_testing_tab', array( $this, 'wp_settings_ab_testing_tab' ) );
		}
	}
	/**
	 * A/B Testing page
	 *
	 * @since 3.0.2
	 */
	public function wp_settings_ab_testing_tab() {
		?>
		<c-tab title="<?php esc_attr_e( 'A/B Testing', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#ab_testing" id="gdpr-cookie-consent-ab-testing">
			<?php
				$pro_is_activated  = get_option( 'wpl_pro_active', false );
				$installed_plugins = get_plugins();
				$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
				$pro_is_activated  = get_option( 'wpl_pro_active', false );
				$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated','' );
				// Require the class file for gdpr cookie consent api framework settings.
				require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';
				$check_for_ab_testing_transient = get_transient( 'gdpr_ab_testing_transient' );
				
				$ab_options = get_option('wpl_ab_options');

				// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
				$this->settings = new GDPR_Cookie_Consent_Settings();
				// Call the is_connected() method from the instantiated object to check if the user is connected.
				$is_user_connected = $this->settings->is_connected();

				$class_for_blur_content = $is_user_connected ? '' : 'gdpr-blur-background'; // Add a class for styling purposes.

				$class_for_card_body_blur_content = $is_user_connected ? '' : 'gdpr-body-blur-background'; // Add a class for styling purposes.

				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
				

				$ab_testing_transient = get_transient('gdpr_ab_testing_transient');
				if ($ab_testing_transient !== false) {
					$current_time = time();
					$creation_time = $ab_testing_transient['creation_time'];
					$remaining_time = ($creation_time + (int)$ab_options['ab_testing_period']*24*60*60) - $current_time;
					if ($remaining_time > 0) {
						// Time remaining
						$days = floor($remaining_time / 86400);
						$hours = floor(($remaining_time % 86400) / 3600);
					} 
				}
				ob_start(); ?> 
				<c-card class="ab_testing-card ">
					<c-card-body class="card-body" >
						<c-row>
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-ab-testing-top"><?php esc_html_e( 'A/B Testing', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable A/B Testing', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="ab_testing_enabled" id="gdpr-cookie-consent-ab-testing-on" variant="3d"  color="success" :checked="ab_testing_enabled" v-on:update:checked="onSwitchABTestingEnable"></c-switch>
									<input type="hidden" name="gcc-ab-testing-enable" v-model="ab_testing_enabled">
								</c-col> 
								<p class="ab-testing-info">If enabled, the plugin will track which cookie banner has the best conversion rate</p>
							</c-row>
							
							<c-row v-show="ab_testing_enabled">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Duration of A/B Testing Period (in Days)', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="col-sm-8">
									<c-input name="ab_testing_period_text_field" v-model="ab_testing_period"></c-input>
								</c-col>
							</c-row>
							<c-row class="statistics" v-show="ab_testing_enabled">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Statistics', 'gdpr-cookie-consent' ); ?></label></c-col>
								<?php if($check_for_ab_testing_transient): ?>
								<p class="ab-testing-timer" >A/B tracking is still in progress, in approximately <?php echo $days;?> days and <?php echo $hours ?> hours the application will automatically enable the best performing banner.</p>
								<?php endif; ?>
								<div id="ab-testing-chart" style="position: relative;">
  									<ab-testing-chart>
									</ab-testing-chart>
									<?php if(!$check_for_ab_testing_transient || ($ab_options['necessary1'] == 0 && $ab_options['marketing1'] == 0 && $ab_options['analytics1'] == 0 && $ab_options['noChoice1'] == 0 && $ab_options['noWarning1'] == 0 && $ab_options['DNT1'] == 0 && $ab_options['necessary2'] == 0 && $ab_options['marketing2'] == 0 && $ab_options['analytics2'] == 0 && $ab_options['noChoice2'] == 0 && $ab_options['noWarning2'] == 0 && $ab_options['DNT2'] == 0)): ?> 
										<div class="ab_testing_graph_overlay">There is nothing to show here yet</div>
									<?php endif; ?>
								</div>
                            
                        </c-row>
					</c-card-body>
				</c-card>
				<?php
				echo ob_get_clean();
			// if ( ! defined( 'ABSPATH' ) ) {
			// 	exit;
			// }
			// 	$response_maxmind = wp_remote_post(
			// 		GDPR_API_URL . 'get_maxmind_data',
			// 		array(
			// 			'body' => array(
			// 				'the_options_enable_safe' => $the_options['enable_safe'],
			// 				'pro_installed'           => $pro_installed,
			// 				'pro_is_activated'        => $pro_is_activated,
			// 				'api_key_activated'       => $api_key_activated,
			// 				'is_user_connected'       => $is_user_connected,
			// 				'class_for_blur_content'  => $class_for_blur_content,
			// 				'class_for_card_body_blur_content' => $class_for_card_body_blur_content,
			// 				'wpl_pro_active'          => $geo_options,
			// 				'enable_geotargeting'     => $geo_options['enable_geotargeting'],
			// 				'enable_safe'             => $the_options['enable_safe'],
			// 				'enable_value2'           => $the_options['enable_safe'] === 'true' ? 'overlay-integration-style' : '',
			// 				'enable_value1'           => $geo_options['enable_geotargeting'] === 'false' ? 'overlay-integration-style__disable' : '',
			// 			),
			// 		)
			// 	);
			// if ( is_wp_error( $response_maxmind ) ) {
			// 	$maxmind_text = '';
			// }

			// 	$response_status = wp_remote_retrieve_response_code( $response_maxmind );

			// if ( 200 === $response_status ) {
			// 	$maxmind_text = json_decode( wp_remote_retrieve_body( $response_maxmind ) );
			// }
			// ?>
			 	<?php
			// 	// The data is coming from the SaaS server, so it is not user-generated.
			// 	echo $maxmind_text; // phpcs:ignore?> 
		</c-tab>
		<?php
	}
		
}