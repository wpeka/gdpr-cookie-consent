<?php
/**
 * The script blocker frontend functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Wpl_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The frontend-specific functionality for script blocker.
 *
 * Every page is rewritten the same way for every visitor, whatever their
 * consent or location, so full-page caches stay correct: each element that
 * matches a blocking rule is marked with its category and made inert. The
 * consent core in the browser then releases each element whose category the
 * visitor has granted, on its own, and keeps the rest blocked.
 *
 * - <script>: type="text/plain", data-wpl-category, and data-wpl-type with the
 *   original type (e.g. "module"). Every other attribute is kept.
 * - <iframe>, <img>, <embed>, <object>, <link>: the URL attributes (src,
 *   srcset, data, href and the common lazy-load ones) move to
 *   data-wpl-attr-<name>.
 *
 * @package    Wpl_Cookie_Consent
 * @subpackage Wpl_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Script_Blocker_Frontend extends Gdpr_Cookie_Consent_Script_Blocker {

	/**
	 * Script types the browser executes.
	 *
	 * @var string[]
	 */
	const JS_TYPES = array( '', 'text/javascript', 'application/javascript', 'module', 'text/ecmascript', 'application/ecmascript', 'application/x-javascript', 'text/jsx' );

	/**
	 * URL attributes moved aside on blocked non-script elements, per tag.
	 *
	 * @var array
	 */
	const URL_ATTRIBUTES = array(
		'IFRAME' => array( 'src', 'data-src', 'data-lazy-src' ),
		'IMG'    => array( 'src', 'srcset', 'data-src', 'data-lazy-src', 'data-srcset', 'data-lazy-srcset' ),
		'EMBED'  => array( 'src' ),
		'OBJECT' => array( 'data' ),
		'LINK'   => array( 'href' ),
	);

	/**
	 * <link rel> values that fetch or connect to a third party.
	 *
	 * @var string[]
	 */
	const LINK_RELS = array( 'preload', 'prefetch', 'preconnect', 'dns-prefetch', 'modulepreload', 'prerender' );

	/**
	 * The instance hooked into the page, also used by the consent core config.
	 *
	 * @var Gdpr_Cookie_Consent_Script_Blocker_Frontend|null
	 */
	public static $instance = null;

	/**
	 * Rules for this request.
	 *
	 * @var array|null
	 */
	private $rules = null;

	/**
	 * Whitelisted URL fragments for this request.
	 *
	 * @var array|null
	 */
	private $whitelist = null;

	/**
	 * Gdpr_Cookie_Consent_Script_Blocker_Frontend constructor.
	 */
	public function __construct() {
		self::$instance = $this;
		if ( is_admin() || isset( $_GET['preview'] ) || ! class_exists( 'Gdpr_Cookie_Consent' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true !== $the_options['is_on'] || true !== $the_options['is_script_blocker_on'] ) {
			return;
		}
		// No geo check here: the page is the same for every visitor and the
		// browser decides whether blocking applies to them.
		add_action( 'template_redirect', array( $this, 'wpl_cookie_consent_template_redirect' ), 9999 );
	}

	/**
	 * Start buffering the page.
	 *
	 * @since 3.0.0
	 */
	public function wpl_cookie_consent_template_redirect() {
		if ( self::wpl_is_scan_bypass_active() || is_feed() || is_embed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		ob_start( array( $this, 'wpl_cookie_consent_custom_buffer' ) );
	}

	/**
	 * Returns the buffer with matching elements blocked.
	 *
	 * Any failure returns the page unchanged rather than a blank one.
	 *
	 * @since 3.0.0
	 * @param string $buffer String buffer.
	 * @return string
	 */
	public function wpl_cookie_consent_custom_buffer( $buffer = '' ) {
		if ( ! is_string( $buffer ) || '' === $buffer || false === stripos( $buffer, '<html' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return $buffer;
		}
		try {
			$rewritten = $this->wpl_block_elements( $buffer );
			return ( is_string( $rewritten ) && '' !== $rewritten ) ? $rewritten : $buffer;
		} catch ( \Throwable $e ) {
			return $buffer;
		}
	}

	/**
	 * Walk the document once and block every element a rule matches.
	 *
	 * @since 4.5.0
	 * @param string $html Page HTML.
	 * @return string
	 */
	public function wpl_block_elements( $html ) {
		$rules = $this->wpl_get_rules();
		if ( empty( $rules ) ) {
			return $html;
		}
		$processor     = new WP_HTML_Tag_Processor( $html );
		$can_read_text = method_exists( $processor, 'get_modifiable_text' );

		while ( $processor->next_tag() ) {
			if ( $processor->is_tag_closer() ) {
				continue;
			}
			$tag = $processor->get_tag();
			if ( 'SCRIPT' !== $tag && ! isset( self::URL_ATTRIBUTES[ $tag ] ) ) {
				continue;
			}
			if ( null !== $processor->get_attribute( 'data-wpl-skip' ) || null !== $processor->get_attribute( 'data-wpl-category' ) ) {
				continue;
			}

			if ( 'SCRIPT' === $tag ) {
				$this->wpl_block_script( $processor, $rules, $can_read_text );
			} else {
				$this->wpl_block_url_element( $processor, $tag, $rules );
			}
		}
		return $processor->get_updated_html();
	}

	/**
	 * Block a <script> if its src or (inline) text matches a rule.
	 *
	 * @param WP_HTML_Tag_Processor $processor     Processor positioned on the tag.
	 * @param array                 $rules         Rules.
	 * @param bool                  $can_read_text Whether inline text can be read (WP 6.5+).
	 */
	private function wpl_block_script( $processor, $rules, $can_read_text ) {
		$id = $processor->get_attribute( 'id' );
		// The plugin's own scripts and their localized data.
		if ( is_string( $id ) && ( 0 === strpos( $id, 'gdpr-cookie-consent' ) || 0 === strpos( $id, 'wpl-' ) ) ) {
			return;
		}
		$type = $processor->get_attribute( 'type' );
		$type = is_string( $type ) ? strtolower( trim( $type ) ) : '';
		if ( ! in_array( $type, self::JS_TYPES, true ) ) {
			return;
		}

		$rule = null;
		$src  = $processor->get_attribute( 'src' );
		if ( is_string( $src ) && '' !== $src ) {
			if ( $this->wpl_is_whitelisted( $src ) ) {
				return;
			}
			$rule = $this->wpl_match_url( $src, $rules );
		} elseif ( $can_read_text ) {
			$text = $processor->get_modifiable_text();
			if ( '' !== trim( $text ) && ! $this->wpl_is_whitelisted( $text ) ) {
				$rule = $this->wpl_match_inline( $text, $rules );
			}
		}
		if ( null === $rule ) {
			return;
		}

		$original_type = $processor->get_attribute( 'type' );
		$processor->set_attribute( 'type', 'text/plain' );
		if ( is_string( $original_type ) && '' !== $original_type ) {
			$processor->set_attribute( 'data-wpl-type', $original_type );
		}
		$this->wpl_mark( $processor, $rule );
	}

	/**
	 * Block an <iframe>, <img>, <embed>, <object> or <link> pointing at a rule's URL.
	 *
	 * @param WP_HTML_Tag_Processor $processor Processor positioned on the tag.
	 * @param string                $tag       Tag name.
	 * @param array                 $rules     Rules.
	 */
	private function wpl_block_url_element( $processor, $tag, $rules ) {
		if ( 'LINK' === $tag ) {
			$rel = $processor->get_attribute( 'rel' );
			$rel = is_string( $rel ) ? preg_split( '/\s+/', strtolower( trim( $rel ) ) ) : array();
			if ( ! array_intersect( $rel, self::LINK_RELS ) ) {
				return;
			}
		}

		$rule  = null;
		$found = array();
		foreach ( self::URL_ATTRIBUTES[ $tag ] as $attribute ) {
			$value = $processor->get_attribute( $attribute );
			if ( ! is_string( $value ) || '' === $value ) {
				continue;
			}
			$found[ $attribute ] = $value;
			if ( null === $rule && ! $this->wpl_is_whitelisted( $value ) ) {
				// srcset holds several URLs; matching the whole string is enough.
				$rule = $this->wpl_match_url( $value, $rules );
			}
		}
		if ( null === $rule ) {
			return;
		}

		foreach ( $found as $attribute => $value ) {
			$processor->set_attribute( 'data-wpl-attr-' . $attribute, $value );
			$processor->remove_attribute( $attribute );
		}
		$processor->set_attribute( 'data-wpl-placeholder', $rule['category_name'] );
		$this->wpl_mark( $processor, $rule );
	}

	/**
	 * Add the attributes the consent core releases by.
	 *
	 * @param WP_HTML_Tag_Processor $processor Processor positioned on the tag.
	 * @param array                 $rule      Matched rule.
	 */
	private function wpl_mark( $processor, $rule ) {
		$processor->set_attribute( 'data-wpl-category', $rule['category'] );
		$processor->set_attribute( 'data-wpl-key', $rule['key'] );
		$processor->set_attribute( 'data-wpl-label', $rule['label'] );
	}

	/**
	 * The rule whose URL fragment occurs in the URL, if any.
	 *
	 * The longest matching fragment wins, so gtag/js?id=AW- (ads) beats the
	 * generic gtag/js (Tag Manager). The consent core matches the same way.
	 *
	 * @param string $url   URL.
	 * @param array  $rules Rules.
	 * @return array|null
	 */
	public function wpl_match_url( $url, $rules ) {
		$haystack = self::wpl_normalize_url( $url );
		if ( '' === $haystack ) {
			return null;
		}
		$best   = null;
		$length = 0;
		foreach ( $rules as $rule ) {
			foreach ( $rule['urls'] as $needle ) {
				if ( strlen( $needle ) > $length && false !== strpos( $haystack, $needle ) ) {
					$best   = $rule;
					$length = strlen( $needle );
				}
			}
		}
		if ( null !== $best ) {
			return $best;
		}
		foreach ( $rules as $rule ) {
			foreach ( $rule['regex'] as $regex ) {
				if ( 1 === @preg_match( $regex, $url ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- admin-supplied pattern, already validated.
					return $rule;
				}
			}
		}
		return null;
	}

	/**
	 * The rule one of whose code fragments occurs in an inline script, if any.
	 *
	 * @param string $text  Script text.
	 * @param array  $rules Rules.
	 * @return array|null
	 */
	public function wpl_match_inline( $text, $rules ) {
		$normalized = strtolower( $text );
		foreach ( $rules as $rule ) {
			foreach ( $rule['needles'] as $needle ) {
				if ( false !== strpos( $text, $needle ) ) {
					return $rule;
				}
			}
			// A tracker URL inside an inline loader (e.g. the Meta Pixel snippet).
			foreach ( $rule['urls'] as $needle ) {
				if ( false !== strpos( $normalized, $needle ) ) {
					return $rule;
				}
			}
		}
		return null;
	}

	/**
	 * Lowercase, without scheme, protocol-relative slashes or a leading "www.".
	 *
	 * The consent core normalizes the same way.
	 *
	 * @param string $url URL or URL fragment.
	 * @return string
	 */
	public static function wpl_normalize_url( $url ) {
		$url = strtolower( trim( html_entity_decode( (string) $url, ENT_QUOTES ) ) );
		$url = preg_replace( '#^[a-z][a-z0-9+.-]*:#', '', $url );
		$url = ltrim( (string) $url, '/' );
		if ( 0 === strpos( $url, 'www.' ) ) {
			$url = substr( $url, 4 );
		}
		return (string) $url;
	}

	/**
	 * Whether the URL or script text contains a whitelisted fragment.
	 *
	 * @param string $haystack URL or script text.
	 * @return bool
	 */
	private function wpl_is_whitelisted( $haystack ) {
		if ( null === $this->whitelist ) {
			$this->whitelist = array();
			foreach ( $this->wpl_whitelisted_scripts() as $url ) {
				if ( is_string( $url ) && '' !== trim( $url ) ) {
					$this->whitelist[] = strtolower( trim( $url ) );
				}
			}
		}
		if ( empty( $this->whitelist ) ) {
			return false;
		}
		$haystack = strtolower( $haystack );
		foreach ( $this->whitelist as $url ) {
			if ( false !== strpos( $haystack, $url ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Fetch the scripts to be whitelisted.
	 *
	 * @return string[]
	 */
	public function wpl_whitelisted_scripts() {
		$whitelisted = array();
		$scripts     = get_option( 'wpl_options_custom-scripts' );
		if ( is_array( $scripts ) && isset( $scripts['whitelist_script'] ) && is_array( $scripts['whitelist_script'] ) ) {
			foreach ( $scripts['whitelist_script'] as $entry ) {
				// An entry saved without URLs must not break the page.
				if ( ! is_array( $entry ) || empty( $entry['enable'] ) || empty( $entry['urls'] ) || ! is_array( $entry['urls'] ) ) {
					continue;
				}
				$whitelisted = array_merge( $whitelisted, $entry['urls'] );
			}
		}
		return $whitelisted;
	}

	/**
	 * The blocking rules for this request (cached per request).
	 *
	 * @return array
	 */
	public function wpl_get_rules() {
		if ( null === $this->rules ) {
			$this->rules = $this->wpl_build_rules();
		}
		return $this->rules;
	}
}
new Gdpr_Cookie_Consent_Script_Blocker_Frontend();
