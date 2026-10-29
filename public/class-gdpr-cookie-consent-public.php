<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Supported languages.
	 *
	 * @var array
	 */
	private $supported_languages = array( 'fr', 'en', 'nl', 'bg', 'cs', 'da', 'de', 'es', 'hr', 'is', 'sl', 'gr', 'hu', 'po', 'pt', 'ab', 'aa', 'af', 'sq', 'am', 'ar', 'hy', 'az', 'eu', 'be', 'bn', 'bs', 'ca', 'co', 'eo', 'fi', 'fy', 'gl', 'ka', 'gu', 'ha', 'he', 'hi', 'ig', 'id', 'ga', 'it', 'ja', 'kn', 'kk', 'ky', 'ko', 'ku', 'lo', 'lv', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mn', 'ne', 'no', 'or', 'ps', 'fa', 'pa', 'ro', 'ru', 'sm', 'gd', 'st', 'sn', 'sd', 'si', 'sk', 'so', 'su', 'sw', 'sv', 'tl', 'tg', 'ta', 'te', 'th', 'tr', 'ug', 'uk', 'ur', 'uz', 'vi', 'cy', 'xh', 'yi', 'yo', 'zu','ceb', 'zh-cn', 'zh-tw', 'et', 'el', 'ht', 'haw', 'iw', 'hmn', 'jw', 'km', 'la', 'lt', 'my', 'pl', 'sr', 'ug' );

	/**
	 * Public module list, Module folder and main file must be same as that of module name.
	 * Please check the `public_modules` method for more details.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $modules Admin module list.
	 */
	private $modules = array();
	/**
	 * Existing modules array.
	 *
	 * @since 1.0
	 * @access public
	 * @var array $existing_modules Existing modules array.
	 */
	public static $existing_modules = array();

	public $chosenBanner = 1;

	public $user_iab_consent;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		if ( ! shortcode_exists( 'wpl_cookie_details' ) ) {
			add_shortcode( 'wpl_cookie_details', array( $this, 'gdprcookieconsent_shortcode_cookie_details' ) );         // a shortcode [wpl_cookie_details].
		}

		add_action( 'init', array( $this, 'init_random_banner' ) );

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			// Before anything else in <head>, including the GTM snippet below.
			add_action( 'wp_head', array( $this, 'gdprcookieconsent_print_consent_core' ), -10000 );
			// Last thing before the template renders.
			add_action( 'template_redirect', array( $this, 'gdprcookieconsent_expire_denied_cookies' ), PHP_INT_MAX );
		}
		if($this->convert_boolean($the_options['is_gcm_on']) === true ){
			add_action('wp_head', array( $this,'insert_custom_consent_script'), -9999);
			add_action('wp_body_open',  array( $this,'insert_custom_consent_script_body'), -9999);
		}
		add_action( 'template_redirect', array( $this, 'gdprcookieconsent_set_auto_mode_cache_flag' ) );
	}

	/**
	 * Print the consent core inline at the top of <head>.
	 *
	 * The core reads the visitor's consent synchronously, sets the Google Consent
	 * Mode defaults (and, for a returning visitor, the update in the same tick),
	 * provides the IAB stubs and owns every consent write. The banner UI stays
	 * deferred in the footer.
	 *
	 * The config holds nothing visitor-specific except the law in auto mode,
	 * where the page is already excluded from full-page caches.
	 *
	 * @since 4.5.0
	 */
	public function gdprcookieconsent_print_consent_core() {
		if ( is_admin() || wp_doing_ajax() || is_feed() ) {
			return;
		}
		$core_file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/js/gdpr-cookie-consent-core' . GDPR_CC_SUFFIX . '.js';
		if ( ! is_readable( $core_file ) ) {
			$core_file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/js/gdpr-cookie-consent-core.js';
		}
		$core = file_get_contents( $core_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local plugin file.
		if ( false === $core ) {
			return;
		}
		$config = $this->gdprcookieconsent_get_consent_core_config();
		$script = 'window.wplConsentConfig = ' . wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES ) . ";\n" . $core;
		wp_print_inline_script_tag( $script, array( 'id' => 'wpl-consent-core' ) );
	}

	/**
	 * Configuration for the consent core.
	 *
	 * @since 4.5.0
	 * @return array
	 */
	public function gdprcookieconsent_get_consent_core_config() {
		static $memo = null;
		if ( null !== $memo ) {
			return $memo;
		}
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		// Manual mode keeps the stored law as is ('both' stays 'both'); auto mode
		// resolves it per visitor, and gdprcookieconsent_set_auto_mode_cache_flag()
		// keeps those pages out of full-page caches.
		$law = isset( $the_options['cookie_usage_for'] ) ? $the_options['cookie_usage_for'] : 'gdpr';
		if ( $this->gdpr_is_auto_mode() ) {
			$law = Gdpr_Cookie_Consent::resolve_law_for_country( $this->gdpr_get_visitor_country() );
		}

		$geo = $this->gdpr_get_geo_targeting_settings( $this->gdpr_get_effective_law( $the_options ), $the_options );

		$categories = array();
		foreach ( Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true ) as $category ) {
			$categories[] = array(
				'slug'      => $category['gdpr_cookie_category_slug'],
				'necessary' => 'necessary' === $category['gdpr_cookie_category_slug'],
			);
		}

		$gcm_defaults = array();
		foreach ( (array) json_decode( $the_options['gcm_defaults'] ?? '[]' ) as $gcm_config ) {
			if ( ! is_object( $gcm_config ) ) {
				continue;
			}
			$region = isset( $gcm_config->region ) ? (string) $gcm_config->region : 'All';
			$entry  = array( 'region' => array() );
			if ( 'All' !== $region ) {
				$entry['region'] = array_values( array_filter( array_map( 'trim', explode( ',', sanitize_text_field( $region ) ) ) ) );
			}
			foreach ( array( 'ad_storage', 'ad_user_data', 'ad_personalization', 'analytics_storage', 'functionality_storage', 'personalization_storage', 'security_storage' ) as $signal ) {
				$entry[ $signal ] = ( isset( $gcm_config->$signal ) && 'granted' === $gcm_config->$signal ) ? 'granted' : 'denied';
			}
			$gcm_defaults[] = $entry;
		}
		// Without a wait the first tags fire on the defaults before the update.
		$wait_for_update = (int) ( $the_options['gcm_wait_for_update_duration'] ?? 0 );
		if ( $wait_for_update < 0 ) {
			$wait_for_update = 500;
		}

		$settings     = new GDPR_Cookie_Consent_Settings();
		$quota_used   = (float) Gdpr_Cookie_Consent_Page_Views::get_monthly_percent( $settings->get_plan() );
		$select_sites = ( true === $the_options['consent_forward'] && is_array( $the_options['select_sites'] ) ) ? array_values( array_map( 'absint', $the_options['select_sites'] ) ) : array();

		$config = array(
			'law'              => $law,
			'consentVersion'   => (int) $the_options['consent_version'],
			'categories'       => $categories,
			/**
			 * Categories a US-law opt-out of sale/sharing (or GPC) denies.
			 *
			 * @since 4.5.0
			 * @param string[] $slugs Category slugs.
			 */
			'optoutCategories' => array_values( (array) apply_filters( 'gdprcookieconsent_us_optout_categories', array( 'marketing' ) ) ),
			// Without worldwide targeting the uncached geo endpoint decides whether
			// the banner (and so blocking) applies to this visitor.
			'geoTargeted'      => ! $geo['worldwide'],
			// The monthly banner-view quota is used up: no banner, so no blocking.
			'disabled'         => $quota_used >= 100,
			'cookie'           => array(
				'name'   => 'wpl_consent',
				'days'   => (int) $the_options['cookie_expiry'] > 0 ? (int) $the_options['cookie_expiry'] : 365,
				/**
				 * Domain for the consent cookie, e.g. '.example.com' to share consent
				 * across subdomains. Empty for a host-only cookie.
				 *
				 * @since 4.5.0
				 * @param string $domain Cookie domain.
				 */
				'domain' => (string) apply_filters( 'gdprcookieconsent_consent_cookie_domain', '' ),
			),
			/**
			 * Keep writing the pre-4.5 consent cookies from the new record, for cached
			 * pages still running the old script and for rollbacks.
			 *
			 * @since 4.5.0
			 * @param bool $mirror Whether to mirror.
			 */
			'mirrorLegacy'     => (bool) apply_filters( 'gdprcookieconsent_mirror_legacy_cookies', true ),
			'dnt'              => $this->convert_boolean( $the_options['do_not_track_on'] ),
			'tcf'              => $this->convert_boolean( $the_options['is_iabtcf_on'] ),
			'reload'           => array(
				'accept'  => $this->convert_boolean( $the_options['accept_reload'] ),
				'decline' => $this->convert_boolean( $the_options['decline_reload'] ),
			),
			'gcm'              => array(
				'on'             => $this->convert_boolean( $the_options['is_gcm_on'] ),
				'defaults'       => $gcm_defaults,
				'waitForUpdate'  => $wait_for_update,
				'adsRedact'      => $this->convert_boolean( $the_options['is_gcm_ads_redact'] ?? false ),
				'urlPassthrough' => $this->convert_boolean( $the_options['is_gcm_url_passthrough'] ?? false ),
			),
			'blocker'          => $this->gdprcookieconsent_get_blocker_config( $the_options ),
			'inventory'        => class_exists( 'Gdpr_Cookie_Consent_Cookie_Inventory' ) ? Gdpr_Cookie_Consent_Cookie_Inventory::inventory() : array(),
			'log'              => array(
				'on'     => $this->convert_boolean( $the_options['logging_on'] ),
				'url'    => esc_url_raw( rest_url( 'gdpr/v2/consent-log' ) ),
				'banner' => (int) $this->chosenBanner,
				'sites'  => $select_sites,
			),
		);

		/**
		 * Filters the consent core configuration.
		 *
		 * @since 4.5.0
		 * @param array $config Configuration.
		 */
		$memo = apply_filters( 'gdprcookieconsent_consent_core_config', $config );
		return $memo;
	}

	/**
	 * Expire the first-party cookies of categories the visitor has not granted.
	 *
	 * JavaScript cannot see HttpOnly cookies, and cookies set by PHP never pass
	 * through the browser's cookie guard, so the server does the same check as
	 * the consent core: it reads the consent record, expires matching cookies
	 * the browser sent, and drops Set-Cookie headers for them that are queued
	 * for this response.
	 *
	 * Only cookies in the inventory are touched, never the plugin's own or
	 * WordPress login cookies. Responses are only changed for requests that
	 * carry a denied cookie, so cached pages are unaffected.
	 *
	 * @since 4.5.0
	 */
	public function gdprcookieconsent_expire_denied_cookies() {
		if ( is_admin() || headers_sent() || wp_doing_ajax() || ! class_exists( 'Gdpr_Cookie_Consent_Cookie_Inventory' ) ) {
			return;
		}
		$denied = $this->gdprcookieconsent_denied_inventory();
		if ( empty( $denied ) ) {
			return;
		}
		$match = function ( $name ) use ( $denied ) {
			if ( Gdpr_Cookie_Consent_Cookie_Inventory::is_protected( $name ) ) {
				return null;
			}
			foreach ( $denied as $item ) {
				if ( 1 === preg_match( $item['regex'], $name ) ) {
					return $item;
				}
			}
			return null;
		};

		// Set-Cookie headers already queued for a denied cookie.
		$queued  = array();
		$dropped = false;
		foreach ( headers_list() as $header ) {
			if ( 0 === stripos( $header, 'Set-Cookie:' ) ) {
				$pair = trim( substr( $header, strlen( 'Set-Cookie:' ) ) );
				$name = trim( substr( $pair, 0, (int) strpos( $pair, '=' ) ) );
				if ( null !== $match( $name ) ) {
					$dropped = true;
					continue;
				}
				$queued[] = $header;
			}
		}
		if ( $dropped ) {
			header_remove( 'Set-Cookie' );
			foreach ( $queued as $header ) {
				header( $header, false );
			}
		}

		// Cookies the browser sent.
		$host    = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		$domains = array( '' );
		if ( $host && false !== strpos( $host, '.' ) && ! filter_var( $host, FILTER_VALIDATE_IP ) ) {
			$parts = explode( '.', $host );
			for ( $i = 0; $i < count( $parts ) - 1; $i++ ) {
				$domains[] = implode( '.', array_slice( $parts, $i ) );
			}
		}
		foreach ( array_keys( $_COOKIE ) as $name ) {
			$name = (string) $name;
			$item = $match( $name );
			if ( null === $item ) {
				continue;
			}
			foreach ( array_unique( array( '/', $item['path'] ) ) as $path ) {
				foreach ( $domains as $domain ) {
					setcookie( $name, '', 1, $path, $domain, is_ssl() );
				}
			}
		}
	}

	/**
	 * Inventory cookies whose category this request's visitor has not granted,
	 * decided the way the consent core decides.
	 *
	 * @since 4.5.0
	 * @return array List of array( 'regex', 'path' ).
	 */
	private function gdprcookieconsent_denied_inventory() {
		$config = $this->gdprcookieconsent_get_consent_core_config();
		if ( ! empty( $config['disabled'] ) || empty( $config['inventory'] ) ) {
			return array();
		}
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( ! empty( $config['geoTargeted'] ) && ! $this->gdpr_should_show_banner_for_law( $this->gdpr_get_effective_law( $the_options ), $the_options, $this->gdpr_is_auto_mode() ) ) {
			return array();
		}

		$record = null;
		if ( isset( $_COOKIE['wpl_consent'] ) ) {
			$raw = json_decode( wp_unslash( $_COOKIE['wpl_consent'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- decoded and validated below.
			if ( is_array( $raw ) && isset( $raw['v'] ) && 1 === (int) $raw['v'] && isset( $raw['cv'] ) && (int) $raw['cv'] === (int) $config['consentVersion'] ) {
				$record = $raw;
			}
		}
		$gpc  = isset( $_SERVER['HTTP_SEC_GPC'] ) && '1' === trim( sanitize_text_field( wp_unslash( $_SERVER['HTTP_SEC_GPC'] ) ) );
		$law  = $config['law'];
		$both = 'both' === $law;
		$us   = $both || in_array( $law, array( 'ccpa', 'us_state_laws' ), true );
		$opt  = $both || ! in_array( $law, array( 'ccpa', 'us_state_laws' ), true );

		$granted = function ( $slug ) use ( $record, $gpc, $us, $opt, $config ) {
			if ( 'necessary' === $slug ) {
				return true;
			}
			if ( $opt && ( ! $record || ! isset( $record['categories'][ $slug ] ) || true !== $record['categories'][ $slug ] ) ) {
				return false;
			}
			if ( $us && in_array( $slug, $config['optoutCategories'], true ) && ( $gpc || ( $record && isset( $record['optout'] ) && true === $record['optout'] ) ) ) {
				return false;
			}
			return true;
		};

		$denied = array();
		foreach ( $config['inventory'] as $item ) {
			if ( 'cookie' === $item['s'] && ! $granted( $item['c'] ) ) {
				$denied[] = array(
					'regex' => Gdpr_Cookie_Consent_Cookie_Inventory::glob_to_regex( $item['p'] ),
					'path'  => $item['path'],
				);
			}
		}
		return $denied;
	}

	/**
	 * The script blocker rules the consent core needs to hold elements added
	 * after the page was rendered: URL fragments and their category.
	 *
	 * @since 4.5.0
	 * @param array $the_options Settings.
	 * @return array
	 */
	private function gdprcookieconsent_get_blocker_config( $the_options ) {
		$config = array(
			'on'          => true === $the_options['is_script_blocker_on'],
			/* translators: %s: cookie category name, rendered as a link that opens the consent banner. */
			'placeholder' => __( 'Accept %s cookies to view the content.', 'gdpr-cookie-consent' ),
			'rules'       => array(),
		);
		if ( ! $config['on'] || ! class_exists( 'Gdpr_Cookie_Consent_Script_Blocker_Frontend' ) || null === Gdpr_Cookie_Consent_Script_Blocker_Frontend::$instance ) {
			return $config;
		}
		foreach ( Gdpr_Cookie_Consent_Script_Blocker_Frontend::$instance->wpl_get_rules() as $rule ) {
			if ( empty( $rule['urls'] ) ) {
				continue;
			}
			$config['rules'][] = array(
				'k' => $rule['key'],
				'c' => $rule['category'],
				'n' => $rule['category_name'],
				'u' => $rule['urls'],
			);
		}
		return $config;
	}

	/**
	 * In auto mode the rendered banner depends on the visitor's country, so a
	 * full-page cache would serve the first visitor's law to everyone.
	 *
	 * The US laws vary the rendered notice by the visitor's *state*, so they need
	 * the same treatment in manual mode — the law is fixed there, the copy is not.
	 *
	 * Setting DONOTCACHEPAGE is honoured by WP Rocket, W3 Total Cache, WP Super
	 * Cache, LiteSpeed Cache and others. If your cache already varies by country
	 * (Cloudflare geo headers, a CDN edge rule), keep caching enabled with:
	 *
	 *     add_filter( 'gdprcookieconsent_auto_mode_disable_page_cache', '__return_false' );
	 *
	 * @since 4.4.0
	 */
	public function gdprcookieconsent_set_auto_mode_cache_flag() {
		if ( is_admin() ) {
			return;
		}

		if ( ! $this->gdpr_is_auto_mode()
			&& 'us_state_laws' !== $this->gdpr_get_effective_law( Gdpr_Cookie_Consent::gdpr_get_settings() ) ) {
			return;
		}

		if ( ! apply_filters( 'gdprcookieconsent_auto_mode_disable_page_cache', true ) ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}

	public function init_random_banner() {
		if ( wp_rand( 0, 1 ) === 0 ) {
			$this->chosenBanner = 2;
		}
	}
	public function convert_boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_null( $value ) ) {
			return false;
		}

		$value = strtolower( trim( ( string ) $value ) );

		if ( in_array( $value, array( '1', 'true' ), true) ) {
			return true;
		}

		if ( in_array( $value, array( '0', 'false', '' ), true ) ) {
			return false;
		}

		return false;
	}
	/* Add defer attribute to scripts */
	public function register_script_with_defer( $handle, $src, $deps = array(), $ver = false, $in_footer = true ) {
		wp_register_script( $handle, $src, $deps, $ver, $in_footer );

		add_filter( 'script_loader_tag', function ( $tag, $h, $s ) use ( $handle ) {
			if ( $h === $handle ) {
				return str_replace( ' src', ' defer src', $tag );
			}
			return $tag;
		}, 10, 3 );
	}
	/* Add defer async attribute to the script */
	public function register_script_with_defer_async( $handle, $src, $deps = array(), $ver = false, $in_footer = true ) {
		wp_register_script( $handle, $src, $deps, $ver, $in_footer );

		add_filter( 'script_loader_tag', function ( $tag, $h, $s ) use ( $handle ) {
			if ( $h === $handle ) {
				return str_replace( ' src', ' defer async src', $tag );
			}
			return $tag;
		}, 10, 3 );
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_Cookie_Consent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_Cookie_Consent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-custom', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public-custom' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-public-variables', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public-variables' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-frontend', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-frontend' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_Cookie_Consent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_Cookie_Consent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$this->register_script_with_defer_async( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.js', array( 'jquery' ), $this->version, true );
		$this->register_script_with_defer( $this->plugin_name.'-tcf', plugin_dir_url( __FILE__ ) . '../admin/js/vue/gdpr-cookie-consent-admin-tcstring.js', array( 'jquery' ), $this->version, true );
		$this->register_script_with_defer( $this->plugin_name . '-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap/bootstrap.bundle.js', array( 'jquery' ), $this->version, true );

		// The __uspapi API is printed in <head> so vendors can call it before the
		// banner script loads. Accept the canonical 'us_state_laws' alongside the
		// legacy 'ccpa'.
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$law         = $this->gdpr_is_auto_mode() ? Gdpr_Cookie_Consent::resolve_law_for_country( $this->gdpr_get_visitor_country() ) : $the_options['cookie_usage_for'];
		if ( true === $the_options['is_on'] && in_array( $law, array( 'ccpa', 'us_state_laws', 'both' ), true ) ) {
			wp_enqueue_script( $this->plugin_name . '-uspapi', plugin_dir_url( __FILE__ ) . 'js/iab/uspapi.js', array(), $this->version, false );
		}
	}

	/**
	 * Print the Google Tag Manager snippet.
	 *
	 * The consent defaults (and the update for returning visitors) are set by the
	 * consent core, printed before this at the top of <head>. Like any GTM
	 * snippet it is held until consent while the script blocker's Google Tag
	 * Manager rule is enabled; disable that rule to load GTM up front under
	 * Consent Mode.
	 */
	public function insert_custom_consent_script() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if(!empty($the_options['gtm_id'])){
			?>
				<!-- Google Tag Manager -->
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer',<?php echo wp_json_encode($the_options['gtm_id']); ?>);</script>
				<!-- End Google Tag Manager -->
			<?php
		}
	}

	public function insert_custom_consent_script_body(){
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if(!empty($the_options['gtm_id'])){
			?>
				<!-- Google Tag Manager (noscript) -->
				<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo rawurlencode($the_options['gtm_id']); ?>"
				height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
				<!-- End Google Tag Manager (noscript) -->
			<?php
		}
	}

	/**
	 * Returns JSON object containing the settings for the main script.
	 *
	 * @param Array $slim_settings Slim settings.
	 * @return mixed
	 */
	public function wplcookieconsent_json_settings( $slim_settings ) {
		$slim_settings['maxmind_integrated'] = get_option( 'wpl_pro_maxmind_integrated', '1' );
		return $slim_settings;
	}



	/**
	 * Returns the list of country codes that constitute the "home region" for a law.
	 */
	private function gdpr_get_law_region_countries( $law ) {
		switch ( $law ) {
			case 'gdpr':
			case 'eprivacy':
				return Gdpr_Cookie_Consent::get_eu_countries();
			case 'uk_gdpr':
				return array( 'GB' );
			case 'us_state_laws':
				return array( 'US' );
			case 'lgpd':
				return array( 'BR' );
			case 'pipeda':
				return array( 'CA' );
			case 'au_app':
				return array( 'AU' );
			case 'sa_pdpl':
				return array( 'SA' );
			default:
				return array();
		}
	}

	private function gdpr_get_geo_targeting_settings( $law, $the_options ) {
		$truthy = function ( $v ) {
			return true === $v || 1 === $v || '1' === $v || 'true' === $v;
		};

		if ( get_option( 'gdpr_geo_targeting_migrated' ) ) {
			return array(
				'worldwide'    => $truthy( $the_options['is_worldwide_on'] ?? false ),
				'region'       => $truthy( $the_options['is_law_region_on'] ?? false ),
				'countries_on' => $truthy( $the_options['is_selectedCountry_on'] ?? false ),
				'countries'    => is_array( $the_options['select_countries'] ?? null ) ? $the_options['select_countries'] : array(),
			);
		}

		// Legacy install: US State Laws (formerly CCPA) used the *_ccpa key family.
		if ( 'us_state_laws' === $law ) {
			return array(
				'worldwide'    => $truthy( $the_options['is_worldwide_on_ccpa'] ?? false ),
				'region'       => $truthy( $the_options['is_ccpa_on'] ?? false ),
				'countries_on' => $truthy( $the_options['is_selectedCountry_on_ccpa'] ?? false ),
				'countries'    => is_array( $the_options['select_countries_ccpa'] ?? null ) ? $the_options['select_countries_ccpa'] : array(),
			);
		}

		return array(
			'worldwide'    => $truthy( $the_options['is_worldwide_on'] ?? false ),
			'region'       => $truthy( $the_options['is_eu_on'] ?? false ),
			'countries_on' => $truthy( $the_options['is_selectedCountry_on'] ?? false ),
			'countries'    => is_array( $the_options['select_countries'] ?? null ) ? $the_options['select_countries'] : array(),
		);
	}

	/**
	 * Resolve the visitor's country once per request.
	 */
	private function gdpr_get_visitor_country() {
		$geo  = $this->gdpr_get_visitor_geo();
		$code = ( is_array( $geo ) && ! empty( $geo['country'] ) ) ? $geo['country'] : '';

		return ( '' !== $code ) ? strtoupper( $code ) : false;
	}

	/**
	 * The visitor's state (ISO-3166-2 subdivision, no country prefix).
	 */
	private function gdpr_get_visitor_state() {
		$geo = $this->gdpr_get_visitor_geo();

		return ( is_array( $geo ) && ! empty( $geo['state'] ) ) ? strtoupper( $geo['state'] ) : '';
	}

	/**
	 * Law-resolution debug payload for the front end.
	 */
	private function gdpr_get_law_debug_data( $the_options ) {
		
		$enabled = ( defined( 'GDPR_LAW_DEBUG' ) && GDPR_LAW_DEBUG )
			|| ( defined( 'WP_DEBUG' ) && WP_DEBUG );

		if ( ! apply_filters( 'gdprcookieconsent_law_debug', $enabled ) ) {
			return false;
		}

		$is_auto = $this->gdpr_is_auto_mode();
		$geo     = $this->gdpr_get_visitor_geo();

		return array(
			'law_selection_mode' => $is_auto ? 'auto' : 'manual',
			'geo_available'      => is_array( $geo ),
			'country'            => is_array( $geo ) ? $geo['country'] : '',
			'state'              => is_array( $geo ) ? $geo['state'] : '',
			'city'               => is_array( $geo ) ? $geo['city'] : '',
			'resolved_law'       => $is_auto
				? Gdpr_Cookie_Consent::resolve_law_for_country( $this->gdpr_get_visitor_country() )
				: $the_options['cookie_usage_for'],
			'rendered_law'       => $the_options['cookie_usage_for'],
			'placeholder_message' => Gdpr_Cookie_Consent::get_law_placeholder_message(
				$the_options['cookie_usage_for'],
				$this->gdpr_get_visitor_state()
			),
		);
	}

	/**
	 * The visitor's full geo record (country, state, city), resolved once.
	 */
	private function gdpr_get_visitor_geo() {
		static $geo = null;

		if ( null !== $geo ) {
			return $geo;
		}

		if ( ! class_exists( 'Gdpr_Cookie_Consent_Geo_Ip' ) ) {
			$geo = false;
			return $geo;
		}

		$geoip = new Gdpr_Cookie_Consent_Geo_Ip();
		$geo   = $geoip->wpl_get_geo_data();

		return $geo;
	}

	/**
	 * Decides whether the banner should be shown to the current visitor for a given law.
	 */
	private function gdpr_should_show_banner_for_law( $law, $the_options, $is_auto_mode = false ) {
		$geo = $this->gdpr_get_geo_targeting_settings( $law, $the_options );

		if ( $geo['worldwide'] ) {
			return true;
		}

		$allowed_countries = array();

		if ( $geo['region'] && ! $is_auto_mode ) {
			$allowed_countries = array_merge( $allowed_countries, $this->gdpr_get_law_region_countries( $law ) );
		}

		if ( $geo['countries_on'] ) {
			$allowed_countries = array_merge( $allowed_countries, $geo['countries'] );
		}

		if ( empty( $allowed_countries ) ) {
			return false;
		}

		$user_country_code = $this->gdpr_get_visitor_country();

		if ( false === $user_country_code ) {
			return true;
		}

		return in_array( $user_country_code, array_map( 'strtoupper', $allowed_countries ), true );
	}

	/**
	 * Returns cookie consent bar status.
	 *
	 * @since 2.0
	 */
	//This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com. The data is licensed under the Creative Commons Attribution-ShareAlike 4.0 International License.
	public function show_cookie_consent_bar() {
		
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$law         = $this->gdpr_get_effective_law( $the_options );
		$is_auto     = $this->gdpr_is_auto_mode();

		$show = $this->gdpr_should_show_banner_for_law( $law, $the_options, $is_auto );

		// `geo_status` is the law-agnostic verdict, `law` the effective law for this
		// visitor (auto mode resolves it per visitor, so the front end must branch
		// on this rather than on the stored cookie_usage_for). `render_law` is kept
		// for consumers that already read it; every law now renders as itself — a
		// law without copy of its own shows a placeholder notice rather than
		// borrowing the GDPR banner — so the two values are identical. `eu_status` /
		// `ccpa_status` are emitted unconditionally for consumers that have not
		// been updated — the minified public bundle and checkEuAndCCPAStatus().
		$return_array = array(
			'geo_status'   => $show ? 'on' : 'off',
			'law'          => $law,
			'render_law'   => $law,
			'auto_mode'    => $is_auto ? 'on' : 'off',
			'eu_status'    => $show ? 'on' : 'off',
			'ccpa_status'  => $show ? 'on' : 'off',
		);

		wp_send_json( $return_array );
		wp_die();
	}

	/**
	 * Whether law selection is in auto ("Detect Automatically") mode.
	 */
	private function gdpr_is_auto_mode() {
		static $is_auto = null;

		if ( null !== $is_auto ) {
			return $is_auto;
		}

		if ( 'auto' !== get_option( 'gdpr_law_selection_mode', 'manual' ) ) {
			$is_auto = false;
			return $is_auto;
		}

		if ( ! get_option( 'gdpr_connected' ) ) {
			$is_auto = false;
			return $is_auto;
		}

		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';
		$settings = new GDPR_Cookie_Consent_Settings();
		$is_auto  = ( 'free' !== $settings->get_plan() );

		return $is_auto;
	}

	/**
	 * The law that applies to the current visitor.
	 */
	private function gdpr_get_effective_law( $the_options ) {
		if ( $this->gdpr_is_auto_mode() ) {
			return Gdpr_Cookie_Consent::resolve_law_for_country( $this->gdpr_get_visitor_country() );
		}

		$law = isset( $the_options['cookie_usage_for'] ) ? $the_options['cookie_usage_for'] : 'gdpr';

		if ( 'ccpa' === $law ) {
			$law = 'us_state_laws';
		}
		if ( 'both' === $law ) {
			$law = 'gdpr';
		}

		return $law;
	}
	/**
	 * Register public modules
	 *
	 * @since 1.0
	 */
	public function public_modules() {
		$initialize_flag     = false;
		$active_flag         = false;
		$non_active_flag     = false;
		$gdpr_public_modules = get_option( 'gdpr_public_modules' );
		if ( false === $gdpr_public_modules ) {
			$gdpr_public_modules = array();
			$initialize_flag     = true;
		}
		foreach ( $this->modules as $module ) {
			$is_active = 1;
			if ( isset( $gdpr_public_modules[ $module ] ) ) {
				$is_active = $gdpr_public_modules[ $module ]; // checking module status.
				if ( 1 === $is_active ) {
					$active_flag = true;
				}
			} else {
				$active_flag                    = true;
				$gdpr_public_modules[ $module ] = 1; // default status is active.
			}
			$module_file = plugin_dir_path( __FILE__ ) . "modules/$module/class-gdpr-cookie-consent-$module.php";
			if ( file_exists( $module_file ) && 1 === $is_active ) {
				self::$existing_modules[] = $module; // this is for module_exits checking.
				require_once $module_file;
			} else {
				$non_active_flag                = true;
				$gdpr_public_modules[ $module ] = 0;
			}
		}
		if ( $initialize_flag || ( $active_flag && $non_active_flag ) ) {
			$out = array();
			foreach ( $gdpr_public_modules as $k => $m ) {
				if ( in_array( $k, $this->modules, true ) ) {
					$out[ $k ] = $m;
				}
			}
			update_option( 'gdpr_public_modules', $out );
		}
	}

	/**
	 * Removes leading # characters from a string.
	 *
	 * @since 1.0
	 * @param string $str String from hash to be removed.
	 *
	 * @return bool|string
	 */
	public static function gdprcookieconsent_remove_hash( $str ) {
		if ( '#' === $str[0] ) {
			$str = substr( $str, 1, strlen( $str ) );
		} else {
			return $str;
		}
		return self::gdprcookieconsent_remove_hash( $str );
	}

	/**
	 * Parse enqueue url for async parameter.
	 *
	 * @since 1.8.5
	 * @param string $url URL.
	 * @return mixed|string
	 */
	public function gdprcookieconsent_clean_async_url( $url ) {
		if ( strpos( $url, '#async' ) === false ) {
			return $url;
		} elseif ( is_admin() ) {
			return str_replace( '#async', '', $url );
		} else {
			return str_replace( '#async', '', $url ) . "' async='async";
		}
	}
	/**
	 * Translator function to convert the public facing side texts
	 *
	 * @param string $text Text .
	 * @param array  $translations Translation.
	 * @param string $target_language Target Language.
	 */
	public function translate_text( $text, $translations, $target_language ) {
		// Assuming $text is the key for the translation in the JSON file.
		if ( isset( $translations[ $text ][ $target_language ] ) ) {
			return $translations[ $text ][ $target_language ];
		} else {
			// Return the original text if no translation is found.
			return $text;
		}
	}

	/**
	 * Translate the fixed banner labels and default cookie categories for a WPML translated view.
	 *
	 * Banner texts set by the admin are already translated by WPML through the option filter,
	 * so only the texts WPML cannot reach are handled here. A value is replaced only while it
	 * is still the stock text, so gettext/WPML translations and admin customisations are kept.
	 *
	 * @param array  $cookie_data   Cookie data passed to the banner templates.
	 * @param string $wpml_language Current WPML language code.
	 * @param string $lang_selected Banner language selected in the plugin settings.
	 * @return array
	 */
	private function gdpr_translate_cookie_data_for_wpml( $cookie_data, $wpml_language, $lang_selected ) {
		$translations = wp_json_file_decode(
			GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/translations/public-translations.json',
			array( 'associative' => true )
		);
		if ( ! is_array( $translations ) || empty( $translations['about'] ) ) {
			return $cookie_data;
		}

		// WPML language codes that differ from the ones used in the translations file.
		$language_map = array(
			'zh-hans' => 'zh-cn',
			'zh-hant' => 'zh-tw',
			'pt-br'   => 'pt',
			'pt-pt'   => 'pt',
			'nb'      => 'no',
		);
		$language     = isset( $language_map[ $wpml_language ] ) ? $language_map[ $wpml_language ] : $wpml_language;
		if ( ! isset( $translations['about'][ $language ] ) ) {
			$language = explode( '-', $language )[0];
		}
		if ( ! isset( $translations['about'][ $language ] ) ) {
			return $cookie_data;
		}

		// Stock texts a value may still hold: English (gettext fallback) and the selected banner language.
		$stock_languages = array_unique( array( 'en', (string) $lang_selected ) );
		$is_stock_text   = static function ( $value, $key ) use ( $translations, $stock_languages ) {
			$value = trim( wp_unslash( (string) $value ) );
			foreach ( $stock_languages as $stock_language ) {
				if ( isset( $translations[ $key ][ $stock_language ] ) && trim( $translations[ $key ][ $stock_language ] ) === $value ) {
					return true;
				}
			}
			return false;
		};

		$label_keys = array( 'about', 'declaration', 'always', 'save_button', 'name', 'domain', 'purpose', 'expiry', 'type', 'cookies_not_found', 'consent_notice' );
		foreach ( $label_keys as $label_key ) {
			// Labels come from __(), so only an untranslated English value is replaced.
			if ( isset( $cookie_data[ $label_key ], $translations[ $label_key ]['en'], $translations[ $label_key ][ $language ] ) && $translations[ $label_key ]['en'] === $cookie_data[ $label_key ] ) {
				$cookie_data[ $label_key ] = $translations[ $label_key ][ $language ];
			}
		}

		// Category slug => key suffix used in the translations file.
		$category_keys = array(
			'necessary'    => 'necessary',
			'analytics'    => 'analytics',
			'marketing'    => 'marketing',
			'preferences'  => 'preference',
			'unclassified' => 'unclassified',
		);
		if ( ! empty( $cookie_data['categories'] ) && is_array( $cookie_data['categories'] ) ) {
			foreach ( $cookie_data['categories'] as $index => $category ) {
				$slug = isset( $category['gdpr_cookie_category_slug'] ) ? $category['gdpr_cookie_category_slug'] : '';
				if ( ! isset( $category_keys[ $slug ] ) ) {
					continue;
				}
				foreach ( array( 'name', 'description' ) as $field ) {
					$field_key       = 'gdpr_cookie_category_' . $field;
					$translation_key = $field_key . '_' . $category_keys[ $slug ];
					if ( isset( $category[ $field_key ], $translations[ $translation_key ][ $language ] ) && $is_stock_text( $category[ $field_key ], $translation_key ) ) {
						$cookie_data['categories'][ $index ][ $field_key ] = $translations[ $translation_key ][ $language ];
					}
				}
			}
		}

		return $cookie_data;
	}


	/**
	 * Outputs the cookie control script in the footer.
	 * This function should be attached to the wp_footer action hook.
	 *
	 * @since 1.0
	 */
	public function gdprcookieconsent_inject_gdpr_script() {

		global $wpdb;
		$chosenBanner = $this->chosenBanner;
		$ab_options = get_option( 'wpl_ab_options' );
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		// Auto mode: the banner body rendered below is chosen from
		// $the_options['cookie_usage_for'], so swap in the law resolved from this
		// visitor's country — unmapped. A law with no copy of its own keeps its own
		// identity here and renders the placeholder notice below instead of
		// borrowing the GDPR message.
		//
		// NOTE: this runs at page-render time, so a full-page cache will serve the
		// first visitor's law to everyone. gdprcookieconsent_set_auto_mode_cache_flag()
		// sets DONOTCACHEPAGE for this reason; a cache that varies by country can
		// disable that via the gdprcookieconsent_auto_mode_disable_page_cache filter.
		if ( $this->gdpr_is_auto_mode() ) {
			$the_options['cookie_usage_for'] = Gdpr_Cookie_Consent::resolve_law_for_country( $this->gdpr_get_visitor_country() );
		}

		// Placeholder copy for the laws still awaiting their own message keys. The
		// US laws pick one of three notices from the visitor's state, so this is
		// per-visitor in manual mode too — see the cache note above. Empty string
		// for every law that has real copy, which leaves the template untouched.
		$law_placeholder_message = Gdpr_Cookie_Consent::get_law_placeholder_message(
			$the_options['cookie_usage_for'],
			$this->gdpr_get_visitor_state()
		);

		if ( true === $the_options['is_on'] ) {
			// IAB TCF: only load the CMP bundle and vendor data when IAB is enabled.
			$is_iabtcf_on = in_array( $the_options['is_iabtcf_on'], array( true, 'true', 1, '1' ), true );
			// Also passed to gdpr_cookies_obj below, so it must exist when IAB is off.
			$iabtcf_consent_data = array();
			if ( $is_iabtcf_on ) {
				wp_enqueue_script( $this->plugin_name. '-tcf' );
				$iabtcf_consent_data = Gdpr_Cookie_Consent::gdpr_get_iabtcf_vendor_consent_data();
				$iabtcf_data = Gdpr_Cookie_Consent::gdpr_get_all_vendors();
				$gacm_data = Gdpr_Cookie_Consent::gdpr_get_gacm_vendors();
				wp_localize_script(
					$this->plugin_name.'-tcf',
					'iabtcf',
					array(
						'consentdata'              => $iabtcf_consent_data,
						'data'					=> $iabtcf_data,		
						'gacm_data'				=> $gacm_data,
						'ajax_url'				=> WP_PLUGIN_URL.'/gdpr-cookie-consent/admin',
						'consent_logging_nonce' => wp_create_nonce( 'wpl_consent_logging_nonce' ),
						'consent_renew_nonce'   => wp_create_nonce( 'wpl_consent_renew_nonce' ),
						'is_gacm_on'			=> $the_options['is_gacm_on'],
						'is_gcm_advertiser_mode'=> $the_options['is_gcm_advertiser_mode'],
						'is_iabtcf_on'			=> $the_options['is_iabtcf_on'],
					)
				);
			}
			
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( $this->plugin_name . '-custom' );
			wp_enqueue_style( $this->plugin_name . '-public-variables' );
			wp_enqueue_style( $this->plugin_name . '-frontend' );
			wp_enqueue_script( $this->plugin_name . '-bootstrap-js' );
			wp_enqueue_script( $this->plugin_name );

			// Fetch Youtube category
			//$total_results = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $value['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
			$selected_script_category = $wpdb->get_var(
			    $wpdb->prepare(
			        "SELECT cat.gdpr_cookie_category_name
			         FROM `{$wpdb->prefix}wpl_cookie_scripts` AS script
			         JOIN `{$wpdb->prefix}gdpr_cookie_scan_categories` AS cat
			           ON script.script_category = cat.id_gdpr_cookie_category
			         WHERE script.script_title = %s",
			        'Youtube Embed'
			    )
			);

			wp_localize_script(
				$this->plugin_name,
				'log_obj',
				array(
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'consent_logging_nonce' => wp_create_nonce( 'wpl_consent_logging_nonce' ),
					'consent_renew_nonce'   => wp_create_nonce( 'wpl_consent_renew_nonce' ),
					'selected_script_category' => $selected_script_category,
				)
			);
			if ( $is_iabtcf_on ) {
				wp_localize_script(
					$this->plugin_name.'-tcf',
					'log_obj',
					array(
						'ajax_url'              => admin_url( 'admin-ajax.php' ),
						'consent_logging_nonce' => wp_create_nonce( 'wpl_consent_logging_nonce' ),
						'consent_renew_nonce'   => wp_create_nonce( 'wpl_consent_renew_nonce' ),
					)
				);
			}
			add_filter( 'clean_url', array( $this, 'gdprcookieconsent_clean_async_url' ) );
			$gdpr_message     = '';
			$ccpa_message     = '';
			$lgpd_message     = '';
			$eprivacy_message = '';
			// Output the HTML in the footer.
			//
			// The template's message blocks use the legacy 'ccpa' vocabulary while the
			// law dropdown stores the canonical 'us_state_laws', and the four newest
			// laws have no content of their own at all. get_content_law() resolves
			// both cases to the law whose copy, cookie categories and settings-modal
			// sections the banner should be built from — without it those installs
			// render a banner with no body and an empty settings popup.
			//
			// Content selection only: $the_options['cookie_usage_for'] itself is left
			// alone because it also feeds container_class and the CSS that targets it,
			// and the visitor is genuinely on that law.
			$content_law = Gdpr_Cookie_Consent::get_content_law( $the_options['cookie_usage_for'] );

			if ( 'eprivacy' === $content_law ) {
				$eprivacy_message               = nl2br( $the_options['notify_message_eprivacy'] );
				$the_options['eprivacy_notify'] = true;
			}
			if ( 'gdpr' === $content_law ) {
				$gdpr_message               = nl2br( $the_options['notify_message'] );
				$the_options['gdpr_notify'] = true;
				$the_options['ccpa_notify']    = false;
			}
			if ( 'ccpa' === $content_law ) {
				$ccpa_message                  = nl2br( $the_options['notify_message_ccpa'] );
				$the_options['ccpa_notify']    = true;
				$the_options['optout_text']    = nl2br( $the_options['optout_text'] );
				$the_options['confirm_button'] = __( 'Confirm', 'gdpr-cookie-consent' );
				$the_options['cancel_button']  = __( 'Cancel', 'gdpr-cookie-consent' );
			}
			if ( 'lgpd' === $content_law ) {
				$lgpd_message               = nl2br( $the_options['notify_message_lgpd'] );
				$the_options['lgpd_notify'] = true;
			}
			if ( 'both' === $content_law ) {
				$gdpr_message                  = nl2br( $the_options['notify_message'] );
				$ccpa_message                  = nl2br( $the_options['notify_message_ccpa'] );
				$the_options['gdpr_notify']    = true;
				$the_options['ccpa_notify']    = true;
				$the_options['optout_text']    = nl2br( $the_options['optout_text'] );
				$the_options['confirm_button'] = __( 'Confirm', 'gdpr-cookie-consent' );
				$the_options['cancel_button']  = __( 'Cancel', 'gdpr-cookie-consent' );
			}
			// The four newest laws render their own banner copy rather than
			// borrowing GDPR's — get_content_law() above still maps them to 'gdpr'
			// for the about-message / cookie-categories / settings-modal sections,
			// which they intentionally share with GDPR.
			$uk_gdpr_message = stripslashes( nl2br( $the_options['notify_message_uk_gdpr'] ) );
			$pdpl_message    = stripslashes( nl2br( $the_options['notify_message_pdpl'] ) );
			$pipeda_message  = stripslashes( nl2br( $the_options['notify_message_pipeda'] ) );
			$app_message     = stripslashes( nl2br( $the_options['notify_message_app'] ) );

			// The three US state-law notices share one settings modal (ccpa_notify
			// above), but the banner text differs by which of the three notice
			// groups the visitor's state belongs to. Manual mode has no per-visitor
			// state to resolve, so it always shows the CCPA/CPRA notice.
			$us_state_law_variant = ( $this->gdpr_is_auto_mode() || ( 'us_state_laws' === $the_options['cookie_usage_for'] && ( $the_options['is_worldwide_on'] === false || $the_options['is_worldwide_on'] === 'false' || $the_options['is_worldwide_on'] === 0 ) ) )
				? Gdpr_Cookie_Consent::resolve_us_state_law_variant( $this->gdpr_get_visitor_state() )
				: 'ccpa';

			$about_message      = stripslashes( nl2br( $the_options['about_message'] ) );
			$about_message_lgpd = stripslashes( nl2br( $the_options['about_message_lgpd'] ) );
			$gcm_about_message  = stripslashes( nl2br( $the_options['gcm_about_message'] ) );
			$gcm_privacy_policy_text  = stripslashes( nl2br( $the_options['gcm_privacy_policy_text'] ) );
			$eprivacy_message   = stripslashes( $eprivacy_message );
			$gdpr_message       = stripslashes( $gdpr_message );
			$ccpa_message       = stripslashes( $ccpa_message );
			$lgpd_message       = stripslashes( $lgpd_message );
			$eprivacy_str       = $eprivacy_message;
			$gdpr_str           = $gdpr_message;
			$ccpa_str           = $ccpa_message;
			$lgpd_str           = $lgpd_message;
			$head               = $the_options['bar_heading_text'];
			$head               = trim( stripslashes( $head ) );
			$head_lgpd          = $the_options['bar_heading_lgpd_text'];
			$head_lgpd          = trim( stripslashes( $head_lgpd ) );
			$template           = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['template_parts'] = $template;
			if ( in_array( $template, array( 'navy_blue_center', 'navy_blue_box', 'navy_blue_square' ), true ) ) {
				$template_parts_background = '#1c2e5a';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FCF5DF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#F4F4F4';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#000000';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#36423f';
			} else {
				$template_parts_background = '#ffffff';
			}
			wp_localize_script( $this->plugin_name, 'background_obj', array( 'background' => $template_parts_background ) );


			// Localizing the values of the bar color, opacity and text color to the public javascript file for adding dynamic css for the cookie setting section.
			wp_localize_script($this->plugin_name, 'cookie_options', [
				'active_law' => $the_options['cookie_usage_for'],
				// for banner where ab testing is disabled.
				'background' => $the_options['background'] ?? '#FFFFFF', // Default background color
				'background1' => $the_options['cookie_bar_color1'] ?? '#FFFFFF',
				'background2' => $the_options['cookie_bar_color2'] ?? '#FFFFFF',
				'background_legislation' => $the_options['multiple_legislation_cookie_bar_color1'] ?? '#FFFFFF', // Default for legislation

				// Opacity values
				'opacity' => $the_options['opacity'] ?? '1.0', // Default full opacity
				'opacity1' => $the_options['cookie_bar_opacity1'] ?? '1.0',
				'opacity2' => $the_options['cookie_bar_opacity2'] ?? '1.0',
				'opacity_legislation' => $the_options['multiple_legislation_cookie_bar_opacity1'] ?? '1.0',

				// Text color values
				'text' => $the_options['text'] ?? '#000000', // Default black text
				'text1' => $the_options['cookie_text_color1'] ?? '#000000',
				'text2' => $the_options['cookie_text_color2'] ?? '#000000',
				'text_legislation' => $the_options['multiple_legislation_cookie_text_color1'] ?? '#000000', // Default for legislation
			]);

			if ( false !== strpos( $template, 'center' ) ) {
				$template = 'center';
			} elseif ( false !== strpos( $template, 'box' ) ) {
				$template = 'box';
			} elseif ( false !== strpos( $template, 'square' ) ) {
				$template = 'square';
			} elseif ( false !== strpos( $template, 'row' ) ) {
				$template = 'row';
			} elseif ( false !== strpos( $template, 'column' ) ) {
				$template = 'column';
			} else {
				$template = 'default';
			}
			$the_options['skin_template']        = 'skins/' . $template . '.php';
			$the_options['container_class']      = $the_options['cookie_usage_for'] . ' gdpr-' . $the_options['cookie_bar_as'] . ' gdpr-' . $template . ' ' . $the_options['template'];

			$current_theme = wp_get_theme();
			if ( isset( $current_theme->template ) ) {
				$the_options['theme_class'] = 'theme-' . $current_theme->template;
			}

			$the_options['eprivacy_str']              = $eprivacy_str;
			$the_options['gdpr_str']                  = $gdpr_str;
			$the_options['ccpa_str']                  = $ccpa_str;
			$the_options['lgpd_str']                  = $lgpd_str;
			$the_options['head']                      = $head;
			$the_options['head_lgpd']                 = $head_lgpd;
			$the_options['version']                   = $this->version;
			$the_options['show_again_container_id']   = $this->gdprcookieconsent_remove_hash( $the_options['show_again_div_id'] );
			$the_options['ccpa_show_again_container_id']   = $this->gdprcookieconsent_remove_hash( $the_options['ccpa_show_again_div_id'] );

			$the_options['container_id']              = $this->gdprcookieconsent_remove_hash( $the_options['notify_div_id'] );
			$the_options['button_accept_action_id']   = $this->gdprcookieconsent_remove_hash( $the_options['button_accept_action'] );
			$the_options['button_readmore_action_id'] = $this->gdprcookieconsent_remove_hash( $the_options['button_readmore_action'] );
			$the_options['button_decline_action_id']  = $this->gdprcookieconsent_remove_hash( $the_options['button_decline_action'] );
			$the_options['button_settings_action_id'] = $this->gdprcookieconsent_remove_hash( $the_options['button_settings_action'] );

			$the_options['backdrop'] = $the_options['popup_overlay'] ? 'static' : 'false';

			$wpl_pro_active = get_option( 'wpl_pro_active' );
			if ( $wpl_pro_active ) {
				$credit_link_href = 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
			} else {
				$credit_link_href = 'https://wordpress.org/plugins/gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
			}
			$credit_link_text = __( 'WPLP Compliance Platform', 'gdpr-cookie-consent' );

			$credit_link = sprintf(
				/* translators: 1: GDPR Cookie Consent Plugin*/
				__( 'Powered by %s', 'gdpr-cookie-consent' ),
				'<a href="' . esc_url( $credit_link_href ) . '" id="cookie_credit_link" rel="nofollow noopener" target="_blank">' . $credit_link_text . '</a>'
			);

			$button_readmore_url_link = '';
			if( isset( $ab_options['ab_testing_enabled'] ) && ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) ) {
				if ( true === $the_options['button_readmore_url_type' . $chosenBanner] ) {
					if ( true === $the_options['button_readmore_wp_page' . $chosenBanner] ) {
						$button_readmore_url_link = get_privacy_policy_url();
					}
					if ( empty( $button_readmore_url_link ) ) {
						if ( '0' !== $the_options['button_readmore_page' . $chosenBanner] ) {
							$button_readmore_url_link = get_page_link( $the_options['button_readmore_page' . $chosenBanner] );
						} else {
							$button_readmore_url_link = '#';
						}
					}
				} else {
					$button_readmore_url_link = $the_options['button_readmore_url' . $chosenBanner];
				}
			} else {
				if ( true === $the_options['button_readmore_url_type'] ) {
					if ( true === $the_options['button_readmore_wp_page'] ) {
						$button_readmore_url_link = get_privacy_policy_url();
					}
					if ( empty( $button_readmore_url_link ) ) {
						if ( '0' !== $the_options['button_readmore_page'] ) {
							$button_readmore_url_link = get_page_link( $the_options['button_readmore_page'] );
						} else {
							$button_readmore_url_link = '#';
						}
					}
				} else {
					$button_readmore_url_link = $the_options['button_readmore_url'];
				}
			}

			$the_options['button_readmore_url_link'] = $button_readmore_url_link;

			$the_options['button_accept_all_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_accept_all_as_button'] ) {
				switch ( $the_options['button_accept_all_button_size'] ) {
					case 'medium':
						$the_options['button_accept_all_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_accept_all_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_accept_all_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_accept_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_accept_as_button'] ) {
				switch ( $the_options['button_accept_button_size'] ) {
					case 'medium':
						$the_options['button_accept_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_accept_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_accept_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_readmore_classes'] = '';
			if ( $the_options['button_readmore_as_button'] ) {
				$the_options['button_readmore_classes'] .= 'gdpr_action_button_link ';
				switch ( $the_options['button_readmore_button_size'] ) {
					case 'medium':
						$the_options['button_readmore_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_readmore_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_readmore_classes'] .= 'btn btn-sm';
						break;
				}
			} else {
				$the_options['button_readmore_classes'] = 'gdpr_link_button';
			}
			$the_options['button_decline_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_decline_as_button'] ) {
				switch ( $the_options['button_decline_button_size'] ) {
					case 'medium':
						$the_options['button_decline_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_decline_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_decline_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_settings_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_settings_as_button'] ) {
				switch ( $the_options['button_settings_button_size'] ) {
					case 'medium':
						$the_options['button_settings_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_settings_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_settings_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_donotsell_classes'] = 'gdpr_action_button gdpr_link_button';
			$the_options['button_confirm_classes']   = 'gdpr_action_button ';
			if ( $the_options['button_accept_as_button'] ) {
				switch ( $the_options['button_confirm_button_size'] ) {
					case 'medium':
						$the_options['button_confirm_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_confirm_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_confirm_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_cancel_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_cancel_as_button'] ) {
				switch ( $the_options['button_cancel_button_size'] ) {
					case 'medium':
						$the_options['button_cancel_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_cancel_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_cancel_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$categories      = Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true );
			$cookies         = $this->get_cookies();
			$categories_data = array();
			// The page is the same for every visitor (full-page caches serve it to
			// all of them), so nothing here depends on the visitor's consent. The
			// settings toggles are ticked from the consent record in the browser.
			$the_options['viewed_cookie'] = '';
			foreach ( $categories as $category ) {
				$total     = 0;
				$temp      = array();
				$json_temp = array();
				foreach ( $cookies as $cookie ) {
					if ( $cookie['category_id'] === $category['id_gdpr_cookie_category'] ) {
						++$total;
						$temp[]                = $cookie;
						$cookie['description'] = str_replace('"', '\"', $cookie['description'] ?? '');
						$json_temp[]           = $cookie;
					}
				}
				$category['data']  = $temp;
				$category['total'] = $total;
				$category['is_ticked'] = false;
				$categories_data[]      = $category;
				$category['data']       = $json_temp;
				$categories_json_data[] = $category;
			}

			
			// On a WPML translated view (e.g. /fr/) WPML provides the banner texts, so neither the
			// selected banner language nor the dynamic (browser) language is applied there.
			$wpml_language      = Gdpr_Cookie_Consent::gdpr_get_wpml_translated_language();
			$is_dynamic_lang_on = isset( $the_options['is_dynamic_lang_on'] ) && true === $this->convert_boolean( $the_options['is_dynamic_lang_on'] );

			// Check for translations if dynamic translation is off, because when it is on code for automatic translation will translate it.
			if ( ( ! $is_dynamic_lang_on || '' !== $wpml_language ) && ( true === $the_options['button_settings_is_on'] || true === $the_options['button_accept_all_is_on'] || true === $the_options['button_accept_is_on'] ) ) {
				$cookie_data                      = array();
				$cookie_data['categories']        = $categories_data;
				$cookie_data['dash_notify_message']               = $about_message;
				$cookie_data['dash_notify_message_lgpd']              = $about_message_lgpd;
				$cookie_data['show_credits']      = $the_options['show_credits'];
				$cookie_data['credits']           = $the_options['show_credits'] ? $credit_link : '';
				$cookie_data['backdrop']          = $the_options['backdrop'];
				$cookie_data['dash_notify_message_eprivacy'] = $the_options['notify_message_eprivacy'];
				$cookie_data['dash_notify_message_lgpd'] = $the_options['notify_message_lgpd'];
				if( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) {
					$cookie_data['dash_button_readmore_text'] = $the_options['button_readmore_text' . $chosenBanner];
				} else {
					$cookie_data['dash_button_readmore_text'] = $the_options['button_readmore_text'];
				}

				$cookie_data['dash_button_accept_text'] = $the_options['button_accept_text'];
				$cookie_data['dash_button_accept_all_text'] = $the_options['button_accept_all_text'];
				$cookie_data['dash_button_decline_text'] = $the_options['button_decline_text'];
				$cookie_data['dash_about_message'] = $the_options['about_message'];
				$cookie_data['dash_gcm_about_message'] = $the_options['gcm_about_message'];
				$cookie_data['dash_gcm_privacy_policy_text'] = $the_options['gcm_privacy_policy_text'];
				$cookie_data['dash_about_message_lgpd'] = $the_options['about_message_lgpd'];
				$cookie_data['dash_notify_message'] = $the_options['notify_message'];
				$cookie_data['dash_button_settings_text'] = $the_options['button_settings_text'];
				$cookie_data['dash_notify_message_ccpa'] = $the_options['notify_message_ccpa'];
				$cookie_data['dash_notify_message_uk_gdpr'] = $the_options['notify_message_uk_gdpr'];
				$cookie_data['dash_notify_message_pdpl'] = $the_options['notify_message_pdpl'];
				$cookie_data['dash_notify_message_pipeda'] = $the_options['notify_message_pipeda'];
				$cookie_data['dash_notify_message_app'] = $the_options['notify_message_app'];
				$cookie_data['dash_notify_message_us_state'] = $the_options[ 'notify_message_' . $us_state_law_variant ];
				$cookie_data['dash_button_donotsell_text'] = $the_options['button_donotsell_text'];
				$cookie_data['dash_button_confirm_text'] = $the_options['button_confirm_text'];
				$cookie_data['dash_button_cancel_text'] = $the_options['button_cancel_text'];
				if( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) {
					$cookie_data['dash_show_again_text'] = $the_options['show_again_text' . $chosenBanner];
					$cookie_data['ccpa_dash_show_again_text'] = $the_options['ccpa_show_again_text' . $chosenBanner];
				} else {
					$cookie_data['dash_show_again_text'] = $the_options['show_again_text'];
					$cookie_data['ccpa_dash_show_again_text'] = $the_options['ccpa_show_again_text'];
				}
				$cookie_data['dash_optout_text'] = $the_options['optout_text'];
				$cookie_data['dash_notify_message_iabtcf'] = $the_options['notify_message'];
				$cookie_data['dash_about_message_iabtcf']  = $the_options['about_message'];
				$cookie_data['about']             = __( 'About Cookies', 'gdpr-cookie-consent' );
				$cookie_data['declaration']       = __( 'Cookie Declaration', 'gdpr-cookie-consent' );
				$cookie_data['always']            = __( 'Always Active', 'gdpr-cookie-consent' );
				$cookie_data['save_button']       = __( 'Save And Accept', 'gdpr-cookie-consent' );
				$cookie_data['name']              = __( 'Name', 'gdpr-cookie-consent' );
				$cookie_data['domain']            = __( 'Domain', 'gdpr-cookie-consent' );
				$cookie_data['purpose']           = __( 'Purpose', 'gdpr-cookie-consent' );
				$cookie_data['expiry']            = __( 'Expiry', 'gdpr-cookie-consent' );
				$cookie_data['type']              = __( 'Type', 'gdpr-cookie-consent' );
				$cookie_data['cookies_not_found'] = __( 'We do not use cookies of this type.', 'gdpr-cookie-consent' );
				$cookie_data['consent_notice']    = __( 'I consent to the use of following cookies:', 'gdpr-cookie-consent' );
				$the_options['cookie_data']       = $cookie_data;

				if ( '' !== $wpml_language ) {
					// The templates read both $cookie_data and $the_options['cookie_data'], so keep them in sync.
					$cookie_data                = $this->gdpr_translate_cookie_data_for_wpml( $cookie_data, $wpml_language, isset( $the_options['lang_selected'] ) ? $the_options['lang_selected'] : '' );
					$the_options['cookie_data'] = $cookie_data;
				} elseif ( isset( $the_options['lang_selected'] ) && in_array( $the_options['lang_selected'], $this->supported_languages, true ) ) {
					// language translation based on the selected language for the public facing.

					// Load and decode translations from JSON file.
					$translations_file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/translations/public-translations.json';

					$translations = wp_json_file_decode(
						$translations_file,
						array( 'associative' => true )
					);

					if ( ! is_array( $translations ) ) {
						$translations = array();
					}
					// Define an array of text keys to translate.
					$text_keys_to_translate = array(
							'about',
							'declaration',
							'always',
							'save_button',
							'name',
							'domain',
							'purpose',
							'expiry',
							'type',
							'cookies_not_found',
							'consent_notice',
						);

					// Determine the target language based on the POST value.
					$target_language = $the_options['lang_selected'];

					// Loop through the text keys and translate them.
					foreach ( $text_keys_to_translate as $text_key ) {
						$translated_text = $this->translate_text( $text_key, $translations, $target_language );

						$cookie_data[ $text_key ] = $translated_text;
					}

					$the_options['cookie_data'] = $cookie_data;
				}
			}

			//detect user's preffered language from browser.
			if(!function_exists('parseLanguageList')){
				function parseLanguageList($languageList) {
					$languages = array();
					$languageRanges = explode(',', trim($languageList));
					foreach ($languageRanges as $languageRange) {
						if (preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($languageRange), $match)) {
							array_push($languages,strtolower($match[1]));
						}
					}
					return $languages;
				}
			}
			
			//code to change the language according to user's preferences
			if ( $is_dynamic_lang_on && '' === $wpml_language ) {
				$accept_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) : '';
				$languages       = parseLanguageList( $accept_language ); // User's preferred languages.
				$cookie_data                      = array();
				$cookie_data['categories']        = $categories_data;
				$cookie_data['dash_notify_message']               = $about_message;
				$cookie_data['dash_notify_message_lgpd']              = $about_message_lgpd;
				$cookie_data['show_credits']      = $the_options['show_credits'];
				$cookie_data['credits']           = $the_options['show_credits'] ? $credit_link : '';
				$cookie_data['backdrop']          = $the_options['backdrop'];
				$cookie_data['dash_notify_message_eprivacy'] = $the_options['notify_message_eprivacy'];
				$cookie_data['dash_notify_message_lgpd'] = $the_options['notify_message_lgpd'];
				$cookie_data['dash_button_readmore_text'] = $the_options['button_readmore_text'];
				$cookie_data['dash_button_accept_text'] = $the_options['button_accept_text'];
				$cookie_data['dash_button_accept_all_text'] = $the_options['button_accept_all_text'];
				$cookie_data['dash_button_decline_text'] = $the_options['button_decline_text'];
				$cookie_data['dash_about_message'] = $the_options['about_message'];
				$cookie_data['dash_gcm_about_message'] = $the_options['gcm_about_message'];
				$cookie_data['dash_gcm_privacy_policy_text'] = $the_options['gcm_privacy_policy_text'];
				$cookie_data['dash_about_message_lgpd'] = $the_options['about_message_lgpd'];
				$cookie_data['dash_notify_message'] = $the_options['notify_message'];
				$cookie_data['dash_button_settings_text'] = $the_options['button_settings_text'];
				$cookie_data['dash_notify_message_ccpa'] = $the_options['notify_message_ccpa'];
				$cookie_data['dash_notify_message_uk_gdpr'] = $the_options['notify_message_uk_gdpr'];
				$cookie_data['dash_notify_message_pdpl'] = $the_options['notify_message_pdpl'];
				$cookie_data['dash_notify_message_pipeda'] = $the_options['notify_message_pipeda'];
				$cookie_data['dash_notify_message_app'] = $the_options['notify_message_app'];
				$cookie_data['dash_notify_message_us_state'] = $the_options[ 'notify_message_' . $us_state_law_variant ];
				$cookie_data['dash_button_donotsell_text'] = $the_options['button_donotsell_text'];
				$cookie_data['dash_button_confirm_text'] = $the_options['button_confirm_text'];
				$cookie_data['dash_button_cancel_text'] = $the_options['button_cancel_text'];
				$cookie_data['dash_show_again_text'] = $the_options['show_again_text'];
				$cookie_data['ccpa_dash_show_again_text'] = $the_options['ccpa_show_again_text'];
				$cookie_data['dash_optout_text'] = $the_options['optout_text'];
				$cookie_data['dash_notify_message_iabtcf'] = $the_options['notify_message'];
				$cookie_data['dash_about_message_iabtcf']  = $the_options['about_message'];
				$cookie_data['about']             = __( 'About Cookies', 'gdpr-cookie-consent' );
				$cookie_data['declaration']       = __( 'Cookie Declaration', 'gdpr-cookie-consent' );
				$cookie_data['always']            = __( 'Always Active', 'gdpr-cookie-consent' );
				$cookie_data['save_button']       = __( 'Save And Accept', 'gdpr-cookie-consent' );
				$cookie_data['name']              = __( 'Name', 'gdpr-cookie-consent' );
				$cookie_data['domain']            = __( 'Domain', 'gdpr-cookie-consent' );
				$cookie_data['purpose']           = __( 'Purpose', 'gdpr-cookie-consent' );
				$cookie_data['expiry']            = __( 'Expiry', 'gdpr-cookie-consent' );
				$cookie_data['type']              = __( 'Type', 'gdpr-cookie-consent' );
				$cookie_data['cookies_not_found'] = __( 'We do not use cookies of this type.', 'gdpr-cookie-consent' );
				$cookie_data['consent_notice']    = __( 'I consent to the use of following cookies:', 'gdpr-cookie-consent' );
				$the_options['cookie_data']       = $cookie_data;

				// language translation based on one of the preferred languages for the public facing.
				// These languages are sorted in way from most preferrd to less preferred, so once we find a language that we provide translation for, we translate and break out of the loop.
				foreach($languages as $value) {
					foreach ($this->supported_languages as $supported_language) {
						$flag = false;
						if (strpos($supported_language, $value) !== false) {
							$flag = true;

							$translations_file = plugin_dir_path(__FILE__) . 'translations/public-translations.json';
							$translations = json_decode(file_get_contents($translations_file), true);

							// Define an array of text keys to translate.
							$text_keys_to_translate = array(
								'about',
								'declaration',
								'always',
								'save_button',
								'name',
								'domain',
								'purpose',
								'expiry',
								'type',
								'cookies_not_found',
								'consent_notice',
								'dash_notify_message_eprivacy',
								'dash_notify_message_lgpd',
								'dash_notify_message_uk_gdpr',
								'dash_notify_message_pdpl',
								'dash_notify_message_pipeda',
								'dash_notify_message_app',
								'dash_notify_message_default_opt_out',
								'dash_notify_message_pure_opt_out',
								'dash_button_readmore_text',
								'dash_button_accept_text',
								'dash_button_accept_all_text',
								'dash_button_decline_text',
								'dash_about_message',
								'dash_about_message_iabtcf',
								'dash_about_message_lgpd',
								'dash_gcm_about_message',
								'dash_gcm_privacy_policy_text',
								'dash_notify_message',
								'dash_notify_message_iabtcf',
								'dash_button_settings_text',
								'dash_notify_message_ccpa',
								'dash_button_donotsell_text',
								'dash_button_confirm_text',
								'dash_button_cancel_text',
								'dash_show_again_text',
								'ccpa_dash_show_again_text',
								'dash_optout_text',
								'gdpr_cookie_category_description_necessary',
								'gdpr_cookie_category_name_necessary',
								'gdpr_cookie_category_description_analytics',
								'gdpr_cookie_category_name_analytics',
								'gdpr_cookie_category_description_marketing',
								'gdpr_cookie_category_description_preference',
								'gdpr_cookie_category_description_unclassified',
								'gdpr_cookie_category_name_marketing',
								'gdpr_cookie_category_name_preference',
								'gdpr_cookie_category_name_unclassified',
							);

							// Determine the target language based on the POST value.
							$target_language = $value;

							// Loop through the text keys and translate them.
							foreach ( $text_keys_to_translate as $text_key ) {
								$translated_text = $this->translate_text( $text_key, $translations, $target_language );
								if ( 'gdpr_cookie_category_description_necessary' === $text_key ) {
									$cookie_data['categories'][0]['gdpr_cookie_category_description'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_description_analytics' === $text_key ) {
									$cookie_data['categories'][2]['gdpr_cookie_category_description'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_description_marketing' === $text_key ) {
									$cookie_data['categories'][1]['gdpr_cookie_category_description'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_description_preference' === $text_key ) {
									$cookie_data['categories'][3]['gdpr_cookie_category_description'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_description_unclassified' === $text_key ) {
									$cookie_data['categories'][4]['gdpr_cookie_category_description'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_name_analytics' === $text_key ) {
									$cookie_data['categories'][2]['gdpr_cookie_category_name'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_name_marketing' === $text_key ) {
									$cookie_data['categories'][1]['gdpr_cookie_category_name'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_name_necessary' === $text_key ) {
									$cookie_data['categories'][0]['gdpr_cookie_category_name'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_name_preference' === $text_key ) {
									$cookie_data['categories'][3]['gdpr_cookie_category_name'] = $translated_text;
								} elseif ( 'gdpr_cookie_category_name_unclassified' === $text_key ) {
									$cookie_data['categories'][4]['gdpr_cookie_category_name'] = $translated_text;
								}else $cookie_data[ $text_key ] = $translated_text;
							}

							$the_options['cookie_data'] = $cookie_data;
							break; 
						}
							
					}
					if($flag) break;
						
				}
			}
			$the_options['credits'] = $the_options['show_credits'] ? $credit_link : '';
			$ab_options    = get_option( 'wpl_ab_options' );
						
			$template_object = json_decode($the_options['selected_template_json'], true);			
			// include plugin_dir_path( __FILE__ ) . 'templates/default.php';
			include_once plugin_dir_path(__FILE__) . 'templates/cookie-notice.php';
			?>
			<style>
				.gdpr_messagebar_detail .category-group .category-item .description-container .group-toggle .checkbox input:checked+label,
				.gdpr_messagebar_detail .category-group .category-item .inner-description-container .group-toggle .checkbox input:checked+label,
				.gdpr_messagebar_detail .category-group .toggle-group .checkbox input:checked+label {
					background: <?php echo ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) ? esc_attr( $the_options['button_accept_all_button_color' . $chosenBanner] ) : esc_attr( $the_options['button_accept_all_button_color'] ); ?> !important;
				}
				.gdpr_messagebar_detail .category-group .toggle-group .checkbox input:checked+label::after {
					background: <?php echo ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) ? esc_attr( $the_options['button_accept_all_link_color' . $chosenBanner] ) : esc_attr( $the_options['button_accept_all_link_color'] ); ?> !important;
				}
			</style>
			<?php

			// make null if consent forward in of.
			$currentid                     = get_current_blog_id();
			$the_options['select_sites']   = is_array( $the_options['select_sites'] ) ? $the_options['select_sites'] : array();
			$the_options['select_sites'][] = $currentid;
			$abTesting = $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true';
	
			$color = $abTesting
				? $the_options['cookie_bar_color' . $chosenBanner]
				: $the_options['background'];

			$opacity = $abTesting
				? $the_options['cookie_bar_opacity' . $chosenBanner]
				: $the_options['opacity'];


			$opacityHex = strtoupper(str_pad(dechex((int) floor($opacity * 255)), 2, '0', STR_PAD_LEFT));
			$finalColor = strtoupper($color . $opacityHex);
			$acceptAllBGColor = $abTesting
				? $the_options[ 'button_accept_all_button_color' . $chosenBanner ]
				: ( $the_options['cookie_usage_for'] === 'both' 
					? $the_options[ 'button_accept_all_button_color1'] 
					: $the_options['button_accept_all_button_color'] );

			if ( $the_options['consent_forward'] !== true ) {
				$the_options['select_sites'] = null;
			}

			$settings = new GDPR_Cookie_Consent_Settings();
			$gdpr_monthly_page_views_percent = Gdpr_Cookie_Consent_Page_Views::get_monthly_percent( $settings->get_plan() );


			global $wpdb;
			$youtube_category = array( 'slug' => 'preferences', 'name' => 'Preferences' );
					
			$youtube_script = $wpdb->get_row(
	    		"SELECT script_category FROM `{$wpdb->prefix}wpl_cookie_scripts`
	    		 WHERE script_key = 'youtube_embed' AND script_status = 1 
	    		 LIMIT 1"
			);
			
			if ( $youtube_script ) {
			    $category = $wpdb->get_row( $wpdb->prepare(
			        "SELECT gdpr_cookie_category_slug, gdpr_cookie_category_name 
			         FROM `{$wpdb->prefix}gdpr_cookie_scan_categories` 
			         WHERE id_gdpr_cookie_category = %d",
			        $youtube_script->script_category
			    ));
			    if ( $category ) {
			        $youtube_category = array(
			            'slug' => $category->gdpr_cookie_category_slug,
			            'name' => $category->gdpr_cookie_category_name,
			        );
			    }
			}

			$cookies_list_data = array(
				'gdpr_cookies_list'                 		=> wp_json_encode( $categories_json_data),
				'gdpr_cookiebar_settings'          		 	=> wp_json_encode( Gdpr_Cookie_Consent::gdpr_get_json_settings() ),
				'iabtcf_consent_data'						=> $iabtcf_consent_data,
				'gdpr_ab_options'							=> get_option('wpl_ab_options'),
				'gdpr_do_not_track'      		    		=> $the_options['do_not_track_on'],
				'ip_anonymization_on'                 		=> $the_options['ip_anonymization_on'],
				'ip_masking_level'                          => $the_options['ip_masking_level'],
				'gdpr_select_pages'       					=> $the_options['select_pages'],
				'gdpr_select_sites'      					=> $the_options['select_sites'],
				'consent_forwarding'      					=> $the_options['consent_forward'],
				'button_revoke_consent_text_color' 			=> $the_options['button_revoke_consent_text_color'],
				'button_revoke_consent_background_color'	=> $the_options['button_revoke_consent_background_color'],
				'button_revoke_consent_text_color1' 		=> $the_options['button_revoke_consent_text_color1'],
				'button_revoke_consent_background_color1'	=> $the_options['button_revoke_consent_background_color1'],
				'button_revoke_consent_text_color2' 		=> $the_options['button_revoke_consent_text_color2'],
				'button_revoke_consent_background_color2'	=> $the_options['button_revoke_consent_background_color2'],
				'ccpa_button_revoke_consent_text_color' 			=> $the_options['ccpa_button_revoke_consent_text_color'],
				'ccpa_button_revoke_consent_background_color'	=> $the_options['ccpa_button_revoke_consent_background_color'],
				'ccpa_button_revoke_consent_text_color1' 		=> $the_options['ccpa_button_revoke_consent_text_color1'],
				'ccpa_button_revoke_consent_background_color1'	=> $the_options['ccpa_button_revoke_consent_background_color1'],
				'ccpa_button_revoke_consent_text_color2' 		=> $the_options['ccpa_button_revoke_consent_text_color2'],
				'ccpa_button_revoke_consent_background_color2'	=> $the_options['ccpa_button_revoke_consent_background_color2'],
				'chosenBanner'								=> $chosenBanner,
				'is_iabtcf_on'                              => $the_options['is_iabtcf_on'],
				'is_gcm_on'									=> $this->convert_boolean($the_options['is_gcm_on']),
				'is_gcm_debug_on'							=> isset($the_options['is_gcm_debug_mode']) ? $this->convert_boolean($the_options['is_gcm_debug_mode']) : false,
				'vendor_data'	                            => $is_iabtcf_on ? $iabtcf_data : array(),
				'cookieSettingsPopupAccentColor'	        => strtoupper(substr($finalColor, 0, -2)) === strtoupper($acceptAllBGColor) ? $the_options['button_accept_all_link_color'] : $acceptAllBGColor,
				'template_parts' 	                        => $the_options['template_parts'],
				'gdpr_monthly_page_views_percent'			=> $gdpr_monthly_page_views_percent,
				'youtube_embed_category'					=> $youtube_category,
				'gdpr_law_debug'							=> $this->gdpr_get_law_debug_data( $the_options ),
			);


			wp_localize_script( $this->plugin_name, 'gdpr_cookies_obj', $cookies_list_data );
		}
	}

	/**
	 * Anonymizes an IP address based on the configured masking level.
	 *
	 * IPv4 masking:
	 *   Level 1 → 192.168.100.xxx
	 *   Level 2 → 192.168.xxx.xxx  (Recommended)
	 *   Level 3 → 192.xxx.xxx.xxx
	 *   Full    → xxx.xxx.xxx.xxx
	 *
	 * @param string $ip    The raw IP address.
	 * @param int|string $level  Masking level: 1, 2, 3, or 'full'.
	 * @return string The anonymized IP address.
	 */
	public function wpl_anonymize_ip( $ip, $level ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			// IPv4
			$parts = explode( '.', $ip );
			switch ( $level ) {
				case 1:
					$parts[3] = 'xxx';
					break;
				case 2:
					$parts[2] = 'xxx';
					$parts[3] = 'xxx';
					break;
				case 3:
					$parts[1] = 'xxx';
					$parts[2] = 'xxx';
					$parts[3] = 'xxx';
					break;
				case 'full':
					$parts = array( 'xxx', 'xxx', 'xxx', 'xxx' );
					break;
				default:
					// Fallback to level 2 (recommended) if unexpected value
					$parts[2] = 'xxx';
					$parts[3] = 'xxx';
					break;
			}
			return implode( '.', $parts );
		}

		// If IP is invalid/unknown, return ip as it is
		return $ip;
	}

	/**
	 * Returns IP address of the user for consent log.
	 *
	 * @since 1.1
	 * @return string
	 *
	 * @phpcs:disable
	 */
	public function wpl_get_user_ip() {
		$ipaddress = 'UNKNOWN';

		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote = filter_var(
				wp_unslash( $_SERVER['REMOTE_ADDR'] ),
				FILTER_VALIDATE_IP
			);

			if ( $remote ) {
				$ipaddress = $remote;
				$trusted_proxies = (array) apply_filters( 'gdpr_cookie_consent_trusted_proxies', array() );

				if (
					! empty( $trusted_proxies )
					&& isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )
					&& $this->wpl_ip_matches( $remote, $trusted_proxies )
				) {
					$ip_list = array_reverse(
						array_map(
							'trim',
							explode(
								',',
								wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] )
							)
						)
					);

					foreach ( $ip_list as $candidate ) {
						$candidate = filter_var( $candidate, FILTER_VALIDATE_IP );

						if ( ! $candidate ) {
							break;
						}

						$ipaddress = $candidate;

						if ( ! $this->wpl_ip_matches( $candidate, $trusted_proxies ) ) {
							break;
						}
					}
				}
			}
		}

		if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		}

		$anonymization_enabled = isset( $the_options['ip_anonymization_on'] )
    		&& ( $the_options['ip_anonymization_on'] === true || $the_options['ip_anonymization_on'] === 1 || $the_options['ip_anonymization_on'] === '1' || $the_options['ip_anonymization_on'] === 'true' );

		if ( $anonymization_enabled && ! empty( $ipaddress ) && $ipaddress !== 'UNKNOWN' ) {
			$level     = isset( $the_options['ip_masking_level'] ) ? $the_options['ip_masking_level'] : 2;
			$ipaddress = $this->wpl_anonymize_ip( $ipaddress, $level );
		}
		return esc_html($ipaddress);
	}
	private function wpl_ip_matches( $ip, array $list ) {
		$ip_bin = inet_pton( $ip );
		if ( false === $ip_bin ) {
			return false;
		}
		foreach ( $list as $entry ) {
			$parts = explode( '/', trim( (string) $entry ), 2 );
			if ( ! filter_var( $parts[0], FILTER_VALIDATE_IP ) ) {
				continue;
			}
			$net   = inet_pton( $parts[0] );
			if ( false === $net || strlen( $net ) !== strlen( $ip_bin ) ) {
				continue;
			}
			$max_bits = strlen( $net ) * 8;
			$bits     = isset( $parts[1] ) ? (int) $parts[1] : $max_bits;
			if ( $bits < 0 || $bits > $max_bits ) {
				continue;
			}
			$bytes = intdiv( $bits, 8 );
			if ( substr( $ip_bin, 0, $bytes ) !== substr( $net, 0, $bytes ) ) {
				continue;
			}
			$rem = $bits % 8;
			if ( 0 === $rem ) {
				return true;
			}
			$mask = ( 0xFF << ( 8 - $rem ) ) & 0xFF;
			if ( ( ord( $ip_bin[ $bytes ] ) & $mask ) === ( ord( $net[ $bytes ] ) & $mask ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns sanitised array.
	 *
	 * @since 2.1.2
	 * @param array $input_array The input array to sanitize.
	 * @return array
	 */
	public function gdpr_cookie_consent_sanitize_decoded_json($input_array = array())
	{
		// Initialize the new array that will hold the sanitize values.
		$return_array = array();

		// Loop through the input and sanitize each of the values.
		foreach ($input_array as $key => $val) {
			$return_array[$key] = sanitize_text_field($val);
		}

		return $return_array;
	}

	/**
	 * Returns scanned and custom cookies.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_cookies()
	{
		$cookies_array = array();
		$cookie_custom = new Gdpr_Cookie_Consent_Cookie_Custom();
		$cookies_array = $cookie_custom->get_cookies();
		$cookies_array = apply_filters('gdprcookieconsent_cookies', $cookies_array);
		return $cookies_array;
	}

	/**
	 * Returns policy data for shortcode wpl_cookie_details.
	 *
	 * @return string|void
	 */
	public function gdprcookieconsent_shortcode_cookie_details()
	{
		if (is_admin()) {
			return;
		}
		$args                = array(
			'numberposts' => -1,
			'post_type'   => 'gdprpolicies',
		);
		$wp_legalpolicy_data = get_posts($args);
		$content             = '';
		if (is_array($wp_legalpolicy_data) && !empty($wp_legalpolicy_data)) {
			$content .= '<p>Our website uses cookies from trusted third-party services to improve functionality, analytics, and advertising. Below is a list of third-party cookies that may be set in your browser:</p>';
			$content .= "<div class='wp_legalpolicy' style='overflow-x:scroll;overflow:auto;'>";
			$content .= '<table style="width:100%;margin:0 auto;border-collapse:collapse;">';
			$content .= '<thead>';
			$content .= '<th>Third Party Companies</th><th>Purpose</th><th>Applicable Privacy/Cookie Policy Link</th>';
			$content .= '</thead>';
			$content .= '<tbody>';
			foreach ($wp_legalpolicy_data as $policypost) {
				$content .= '<tr>';
				$content .= '<td>' . esc_html( $policypost->post_title ) . '</td>';
				$content .= '<td>' . wp_kses_post( $policypost->post_content ) . '</td>';
				$links    = get_post_meta($policypost->ID, '_gdpr_policies_links_editor');
				$content .= '<td>' . ( isset( $links[0] ) ? wp_kses_post( $links[0] ) : '' ) . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody></table></div>';
		}
		return $content;
	}

	/**
	 * Template redirect for header, body and footer scripts.
	 *
	 * The admin's custom scripts are printed on every page, for every visitor,
	 * as normal scripts. The script blocker treats them like any other script
	 * on the page, so the page never depends on the visitor's consent.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_template_redirect() {
		$the_options = GDPR_Cookie_Consent::gdpr_get_settings();
		if ( true !== $the_options['is_on'] ) {
			return;
		}
		add_action( 'wp_head', array( $this, 'gdprcookieconsent_output_header' ) );
		if ( function_exists( 'wp_body_open' ) && did_action( 'wp_body_open' ) === 0 ) {
			add_action( 'wp_body_open', array( $this, 'gdprcookieconsent_output_body' ) );
		}
		add_action( 'wp_footer', array( $this, 'gdprcookieconsent_output_footer' ) );
	}

	/**
	 * Return the custom scripts for pages cached before 4.5, whose script still
	 * asks for them after a click. Current pages print them directly.
	 *
	 * @deprecated 4.5.0
	 */
	public function gdprcookieconsent_inject_sripts_on_consent(){
		$the_options = GDPR_Cookie_Consent::gdpr_get_settings();
		$viewed_cookie = isset($_COOKIE['wpl_viewed_cookie']) ? sanitize_text_field(wp_unslash($_COOKIE['wpl_viewed_cookie'])) : '';
		if($the_options['is_script_blocker_on'] && 'yes' === $viewed_cookie){
			$header_scripts = isset($the_options['header_scripts']) ? "\r\n" . wp_unslash($the_options['header_scripts']) . "\r\n" : '';
			$body_scripts = isset($the_options['body_scripts']) ? "\r\n" . wp_unslash($the_options['body_scripts']) . "\r\n" : '';
			$footer_scripts = isset($the_options['footer_scripts']) ? "\r\n" . wp_unslash($the_options['footer_scripts']) . "\r\n" : '';
			$is_script_dependency_on = ! empty( $the_options['is_script_dependency_on'] );
			$header_dependency = $is_script_dependency_on && isset($the_options['header_dependency']) ? sanitize_text_field($the_options['header_dependency']) : '';
			$footer_dependency = $is_script_dependency_on && isset($the_options['footer_dependency']) ? sanitize_text_field($the_options['footer_dependency']) : '';

			// Return JSON response
			wp_send_json_success([
				'header_scripts' => $header_scripts,
				'body_scripts'   => $body_scripts,
				'footer_scripts' => $footer_scripts,
				'header_dependency' => $header_dependency,
    			'footer_dependency' => $footer_dependency,
			]);
		}
		else{
			wp_send_json_error('Scripts already added');
		}
	}

	/**
	 * Output header scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_header() {
		echo $this->gdprcookieconsent_custom_script_tags( 'header' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in the method.
	}

	/**
	 * Output body scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_body() {
		echo $this->gdprcookieconsent_custom_script_tags( 'body' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in the method.
	}

	/**
	 * Output footer scripts, and the body scripts when the theme has no
	 * wp_body_open.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_footer() {
		if ( ! did_action( 'wp_body_open' ) ) {
			echo $this->gdprcookieconsent_custom_script_tags( 'body' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in the method.
		}
		echo $this->gdprcookieconsent_custom_script_tags( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in the method.
	}

	/**
	 * The <script> elements for one group of custom scripts.
	 *
	 * The admin can paste <script> tags (inline or with src) or bare
	 * JavaScript. Each script keeps its attributes (src, async, defer, id,
	 * type="module" ...) and is printed as a normal script, so the script
	 * blocker treats it like any other script on the page: one matching a
	 * blocking rule waits for that rule's category, the rest run on load.
	 * Other markup in the field is not output.
	 *
	 * With Script Dependency on, data-wpl-after names the groups this group
	 * waits for. The core honours it for scripts the blocker holds.
	 *
	 * @since 4.5.0
	 * @param string $group header, body or footer.
	 * @return string
	 */
	public function gdprcookieconsent_custom_script_tags( $group ) {
		$the_options = GDPR_Cookie_Consent::gdpr_get_settings();
		$code        = isset( $the_options[ $group . '_scripts' ] ) ? trim( wp_unslash( (string) $the_options[ $group . '_scripts' ] ) ) : '';
		if ( '' === $code ) {
			return '';
		}

		$after = array();
		if ( ! empty( $the_options['is_script_dependency_on'] ) && 'false' !== $the_options['is_script_dependency_on'] ) {
			$header_dependency = isset( $the_options['header_dependency'] ) ? (string) $the_options['header_dependency'] : '';
			$footer_dependency = isset( $the_options['footer_dependency'] ) ? (string) $the_options['footer_dependency'] : '';
			// "X waits for: Header Scripts" is stored as header_dependency = X.
			if ( 'header' === $group && 'Header Scripts' === $footer_dependency ) {
				$after[] = 'footer';
			}
			if ( 'body' === $group && 'Body Scripts' === $header_dependency ) {
				$after[] = 'header';
			}
			if ( 'body' === $group && 'Body Scripts' === $footer_dependency ) {
				$after[] = 'footer';
			}
			if ( 'footer' === $group && 'Footer Scripts' === $header_dependency ) {
				$after[] = 'header';
			}
		}

		$out = '';
		foreach ( self::gdprcookieconsent_parse_custom_scripts( $code ) as $script ) {
			$attributes = array( 'data-wpl-group' => $group );
			if ( ! empty( $after ) ) {
				$attributes['data-wpl-after'] = implode( ' ', $after );
			}
			foreach ( $script['attributes'] as $name => $value ) {
				if ( ! isset( $attributes[ $name ] ) && 0 !== strpos( $name, 'data-wpl-' ) && preg_match( '/^[a-z][a-z0-9_:.-]*$/', $name ) ) {
					$attributes[ $name ] = $value;
				}
			}
			$html = '<script';
			foreach ( $attributes as $name => $value ) {
				$html .= ( true === $value ) ? ' ' . $name : ' ' . $name . '="' . esc_attr( $value ) . '"';
			}
			$html .= '>' . str_ireplace( '</script', '<\/script', $script['text'] ) . "</script>\n";
			$out  .= $html;
		}
		return $out;
	}

	/**
	 * Split the custom scripts field into scripts.
	 *
	 * @since 4.5.0
	 * @param string $code Field value.
	 * @return array List of array( 'attributes' => array, 'text' => string ).
	 */
	public static function gdprcookieconsent_parse_custom_scripts( $code ) {
		if ( false === stripos( $code, '<script' ) ) {
			return array(
				array(
					'attributes' => array(),
					'text'       => $code,
				),
			);
		}
		$scripts = array();
		if ( class_exists( 'WP_HTML_Tag_Processor' ) && method_exists( 'WP_HTML_Tag_Processor', 'get_modifiable_text' ) ) {
			$processor = new WP_HTML_Tag_Processor( $code );
			while ( $processor->next_tag( 'script' ) ) {
				$attributes = array();
				foreach ( (array) $processor->get_attribute_names_with_prefix( '' ) as $name ) {
					$attributes[ strtolower( $name ) ] = $processor->get_attribute( $name );
				}
				$scripts[] = array(
					'attributes' => $attributes,
					'text'       => $processor->get_modifiable_text(),
				);
			}
			return $scripts;
		}
		if ( preg_match_all( '#<script\b([^>]*)>(.*?)</script\s*>#is', $code, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$attributes = array();
				if ( preg_match_all( '#([a-zA-Z][a-zA-Z0-9_:.-]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?#', $match[1], $pairs, PREG_SET_ORDER ) ) {
					foreach ( $pairs as $pair ) {
						$value = isset( $pair[2] ) && '' !== $pair[2] ? $pair[2] : ( isset( $pair[3] ) && '' !== $pair[3] ? $pair[3] : ( isset( $pair[4] ) ? $pair[4] : true ) );
						$attributes[ strtolower( $pair[1] ) ] = ( '' === $value ) ? true : html_entity_decode( $value, ENT_QUOTES );
					}
				}
				$scripts[] = array(
					'attributes' => $attributes,
					'text'       => $match[2],
				);
			}
		}
		return $scripts;
	}

}
