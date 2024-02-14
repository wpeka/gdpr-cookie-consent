<?php
/**
 * The script blocker frontend functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      2.6
 *
 * @package    Wpl_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The frontend-specific functionality for script blocker.
 *
 * @package    Wpl_Cookie_Consent
 * @subpackage Wpl_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Script_Blocker_Frontend extends Gdpr_Cookie_Consent_Script_Blocker {

	/**
	 * Buffer type for content rendering.
	 *
	 * @since 2.6
	 * @var int $buffer_type Buffer type.
	 */
	public $buffer_type = '1';

	/**
	 * Gdpr_Cookie_Consent_Script_Blocker_Frontend constructor.
	 */
	public function __construct() {
		$disable_blocker = get_option( 'wpl_bypass_script_blocker' );
		if ( is_admin() || isset( $_GET['preview'] ) || $disable_blocker || ! class_exists( 'Gdpr_Cookie_Consent' ) ) { // phpcs:ignore input var ok, CSRF ok, sanitization ok.
			return;
		}
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		if ( true === $the_options['is_on'] && true === $the_options['is_script_blocker_on'] ) {
			if ( true === $the_options['is_eu_on'] ) {
				require WPL_PLUGIN_PATH . 'public/modules/geo-ip/class-wpl-cookie-consent-geo-ip.php';
				$geoip = new Wpl_Cookie_Consent_Geo_Ip();
				if ( ! $geoip->wpl_is_eu_country() ) {
					return;
				}
			}
			// Block scripts.
			add_action( 'template_redirect', array( $this, 'wpl_cookie_consent_template_redirect' ), 9999 );
		} else {
			return;
		}

	}

	/**
	 * Blocks scripts.
	 *
	 * @since 2.6
	 */
	public function wpl_cookie_consent_template_redirect() {
		ob_start();
		if ( '1' === $this->buffer_type ) {
			ob_start( array( $this, 'wpl_cookie_consent_custom_buffer' ) );
		}
	}

	/**
	 * Match the whitelisted url in scripts.
	 *
	 */
	public function wpl_check_for_script_match($whitelist_urls, $html_Data) {
	     // Convert $html_Data to lowercase for case-insensitive comparison
		 $html_Data_lower = strtolower($html_Data);

		 // Loop through each element in $whitelist_urls
		 foreach ($whitelist_urls as $key => $value) {
			 // If the element is an array, skip it
			 if (is_array($value)) {
				 continue;
			 }

			 // Check if the value exists in $html_Data (case-insensitive)
			 $value_lower = strtolower($value);
			 if (strpos($html_Data_lower, $value_lower) !== false) {
				 return true; // Return true if a match is found
			 }
		 }

		 return false; // Return false if no match is found
	}

	/**
	 * Returns custom buffer along with blocking changes.
	 *
	 * @since 2.6
	 * @param string $buffer String buffer.
	 * @return mixed|string
	 */
	public function wpl_cookie_consent_custom_buffer( $buffer = '' ) {
		$script_patterns = $this->wpl_get_script_patterns();
		$script_lists    = $this->get_cookie_scripts();
		$script_lists    = isset( $script_lists['data'] ) ? $script_lists['data'] : array();
		if ( isset( $script_lists ) && ! empty( $script_lists ) ) {
			foreach ( $script_lists as $key => $value ) {
				$category = $this->get_category_by_id( $value['script_category'] );
				foreach ( $category as $k => $v ) {
					$category_slug = $v->gdpr_cookie_category_slug;
					$category_name = $v->gdpr_cookie_category_name;
				}
				$script_key    = $value['script_key'];
				$script_status = $value['script_status'];
				if ( isset( $script_patterns[ $script_key ] ) && 'necessary' !== $category_slug ) {
					if ( '1' === $script_status ) {
						$script_patterns[ $script_key ]['block_scripts'] = true;
						$script_patterns[ $script_key ]['category_slug'] = $category_slug;
						$script_patterns[ $script_key ]['category_name'] = $category_name;
						$script_patterns[ $script_key ]['placeholder']   = sprintf(
							/* Translators: %s category name. */
							__( 'Accept %s cookies to view the content.', 'gdpr-cookie-consent' ),
							"<a class='wpl_manage_current_consent'>" . esc_html( $category_name ) . '</a>'
						);
					} else {
						unset( $script_patterns[ $script_key ] );
					}
				} else {
					unset( $script_patterns[ $script_key ] );
				}
			}
		}

		foreach ( $script_patterns as $key => $value ) {
			$script_patterns[ $key ]['check'] = true;
		}

		$buffer = $this->wpl_before_automate( $buffer );
		$parts  = $this->wpl_get_parts( $buffer );
		if ( $parts ) {
			foreach ( $script_patterns as $key => $data ) {
				if ( $data['check'] ) {
					if ( $data['internal_cb'] ) {
						$callback = array( $this, $data['callback'] );
					}
				} else {
					continue;
				}
				$parts = call_user_func_array( $callback, array( $key, $data, $parts ) );
			}
			$buffer = $parts['head'] . $parts['split'] . $parts['body'];
		}
		$buffer = $this->wpl_after_automate( $buffer );
		return $buffer;
	}

	/**
	 * Fetch the scripts to be whitelisted.
	 *
	 */
	public function wpl_whitelisted_scripts(  ) {

		$whitelisted_script_tags = array();
		$scripts = get_option("wpl_options_custom-scripts");
		if ( is_array($scripts) && isset($scripts['whitelist_script']) && is_array($scripts['whitelist_script']) ) {
			$custom_whitelisted_script_tags = array_filter( $scripts['whitelist_script'], function($script) {
				return $script['enable'] == 1;
			});

			//flatten array
			$flat = array();
			foreach ( $custom_whitelisted_script_tags as $data ) {
				$flat = array_merge($flat, $data['urls']);
			}

			$whitelisted_script_tags = array_merge($flat, $whitelisted_script_tags );
		}
		return $whitelisted_script_tags;
	}

	/**
	 * Returns parts of the buffer string.
	 *
	 * @since 2.6
	 * @param String $buffer Buffer.
	 * @return array|bool
	 * @throws \RuntimeException Run time exception.
	 */
	public function wpl_get_parts( $buffer ) {
		$parts   = array(
			'head'  => '',
			'body'  => '',
			'split' => '',
		);
		$pattern = '#\</head\>[^<]*\<body[^\>]*?\>#';
		if ( preg_match( $pattern, $buffer, $m ) ) {
			$splitted = preg_split( $pattern, $buffer );
			if ( 2 !== count( $splitted ) ) {
				throw new RuntimeException( 'Could not split content in <head> and <body> parts.' );
			}
			$parts['head']  = $splitted[0];
			$parts['body']  = $splitted[1];
			$parts['split'] = $m[0];
			unset( $splitted );
			return $parts;
		}
		return false;
	}

	/**
	 * Before automate block scripts.
	 *
	 * @since 2.6
	 * @param String $buffer Buffer.
	 * @return string
	 */
	public function wpl_before_automate( $buffer ) {
		$text_arr                       = wp_html_split( $buffer );
		$regex_patterns                 = $this->wpl_get_regex_patterns();
		$_regex_pattern_script_tag_open = $regex_patterns['_regexScriptTagOpen'];
		$changed                        = false;
		$replace_pairs                  = array(
			"\r\n" => '_RNL_',
			"\n"   => '_NL_',
			'<'    => '_LT_',
		);

		foreach ( $replace_pairs as $needle => $replace ) {
			foreach ( $text_arr as $i => $html ) {
				if ( preg_match( "#^$_regex_pattern_script_tag_open#", $text_arr[ $i ], $m ) ) {
					if ( false !== strpos( $text_arr[ $i + 1 ], $needle ) ) {
						$textarr[ $i + 1 ] = str_replace( $needle, $replace, $text_arr[ $i + 1 ] );
						$changed           = true;
					}
					if ( '<' === $needle && $needle === $text_arr[ $i + 2 ][0] && '</script>' !== $text_arr[ $i + 2 ] ) {
						$text_arr[ $i + 2 ] = preg_replace( '#\<(?!/script\>)#', $replace, $text_arr[ $i + 2 ] );
					}
				}
			}
		}
		if ( $changed ) {
			$buffer = implode( $text_arr );
		}
		unset( $text_arr );
		return $buffer;
	}

	/**
	 * After automate block scripts.
	 *
	 * @since 2.6
	 * @param String $buffer Buffer.
	 * @return mixed
	 */
	public function wpl_after_automate( $buffer ) {
		return str_replace( array( '_RNL_', '_NL_', '_LT_' ), array( "\r\n", "\n", '<' ), $buffer );
	}

	/**
	 * Automate block scripts.
	 *
	 * @since 2.6
	 * @param null  $type Type.
	 * @param array $data Data.
	 * @param array $parts Parts.
	 * @return array
	 */
	public function wpl_automate_default( $type = null, $data = array(), $parts = array() ) {
		$patterns      = array();
		$has_src       = $data['has_src'];
		$has_js        = $data['has_js'];
		$has_js_needle = $data['has_js_needle'];
		$has_url       = $data['has_url'];
		$has_html_elem = $data['has_html_elem'];

		$regex_patterns = $this->wpl_get_regex_patterns();

		if ( $has_url ) {
			$url              = $data['url'];
			$url_tmpl_pattern = $regex_patterns['_regexParts']['-lookbehindLinkImg'] . 'https?://(?:[www\.]{4})?%s';
			foreach ( (array) $url as $u ) {
				$url         = $this->wpl_get_url_without_schema_subdomain( $u );
				$url         = str_replace( '*', $regex_patterns['_regexParts']['randomChars'], $url );
				$escaped_url = $this->wpl_escape_regex_chars( $url );
				$pattern     = sprintf( $url_tmpl_pattern, $escaped_url );
				$patterns[]  = $pattern;
			}
		}

		if ( $has_src ) {
			$s = $data['src'];
			foreach ( (array) $s as $src ) {
				$clean_url   = $this->wpl_get_clean_url( $src, true );
				$subdomain   = ( '' !== $clean_url && '.' === $clean_url[0] ) ? '[^.]+?' : '';
				$escaped_url = $this->wpl_escape_regex_chars( $clean_url );
				$pattern     = sprintf( $regex_patterns['_regexIframe'], $subdomain, $escaped_url );
				$patterns[]  = $pattern;
			}
		}

		if ( $has_js ) {
			$js = $data['js'];
			foreach ( (array) $js as $script ) {
				$has_plugin_url    = false;
				$clean_url         = $this->wpl_get_clean_url( $script, true );
				$allowed_locations = array(
					'plugin' => 'wp-content/plugins',
					'theme'  => 'wp-content/themes',
				);
				if ( '' !== $clean_url && ! empty( $allowed_locations ) && preg_match( '#^' . join( '|', $allowed_locations ) . '#', $clean_url ) ) {
					$has_plugin_url = true;
					$url_begin      = trailingslashit( $this->wpl_get_clean_url( home_url( add_query_arg( null, null ) ) ) );

				} elseif ( '' !== $clean_url && '.' === $clean_url[0] ) {
					$url_begin = '[^.]+?';
				} else {
					$url_begin = '';
				}
				$escaped_url = $this->wpl_escape_regex_chars( $clean_url );
				if ( $has_plugin_url ) {
					$url_begin = $this->wpl_escape_regex_chars( $url_begin );
				}
				$pattern    = sprintf( $regex_patterns['_regexScriptSrc'], $url_begin, $escaped_url );
				$patterns[] = $pattern;
			}
		}

		if ( $has_js_needle ) {
			$js_needle = $data['js_needle'];
			foreach ( (array) $js_needle as $needle ) {
				$escaped    = $this->wpl_escape_regex_chars( $needle );
				$pattern    = sprintf( $regex_patterns['_regexScriptHasNeedle'], $escaped );
				$patterns[] = $pattern;
			}
		}

		if ( $has_html_elem ) {
			$data_count = count( $data['html_elem'] );
			for ( $j = 0; $j < $data_count; $j++ ) {
				$html_elem_attr       = explode( ':', $data['html_elem'][ $j ]['attr'] );
				$html_elem_name       = $this->wpl_escape_regex_chars( $data['html_elem'][ $j ]['name'] );
				$html_elem_attr_name  = $this->wpl_escape_regex_chars( $html_elem_attr[0] );
				$html_elem_attr_value = $this->wpl_escape_regex_chars( $html_elem_attr[1] );
				$prefix               = '';
				if ( ( 'src' === $html_elem_attr_name ) || ( 'data' === $html_elem_attr_name && 'object' === $html_elem_name ) ) {
					$prefix = $regex_patterns['_regexParts']['srcSchemeWww'];
				}
				if ( ( 'img' === $html_elem_name ) || ( 'embed' === $html_elem_name ) ) {
					$patterns[] = sprintf( $regex_patterns['_regexHtmlElemWithAttrTypeA'], $html_elem_name, $html_elem_attr_name, $prefix, $html_elem_attr_value );
				} else {
					$patterns[] = sprintf( $regex_patterns['_regexHtmlElemWithAttr'], $html_elem_name, $html_elem_attr_name, $prefix, $html_elem_attr_value, $html_elem_name, $html_elem_name );
				}
			}
		}
		return $this->wpl_prepare_script( $patterns, '', $type, $parts, $data );
	}

	/**
	 * Prepare blocking script.
	 *
	 * @since 2.6
	 * @param string $patterns Patterns.
	 * @param string $modifiers Modifiers.
	 * @param null   $type Type.
	 * @param array  $parts Parts.
	 * @param array  $data Data.
	 * @return array
	 * @throws \InvalidArgumentException Invalid argument exception.
	 * @throws \RuntimeException Run time exception.
	 */
	public function wpl_prepare_script( $patterns = '', $modifiers = '', $type = null, $parts = array(), $data = array() ) {
		$wrap_pattern = '#%s#' . $modifiers;
		$pattern      = array();
		foreach ( $patterns as $ptrn ) {
			$pattern[] = sprintf( $wrap_pattern, $ptrn );
		}
		if ( ! isset( $parts['head'] ) || ! isset( $parts['body'] ) ) {
			throw new InvalidArgumentException( 'Parts array is not valid for ' . $type . ': head or body entry not found.' );
		}
		$parts['head'] = $this->wpl_script_replace_callback( $parts['head'], $pattern, $data, 'head' );
		if ( null === $parts['head'] ) {
			throw new RuntimeException( 'An error occurred calling preg_replace_callback() context head.' );
		}
		$wrap_pattern = '#%s#' . $modifiers;
		$pattern      = array();
		foreach ( $patterns as $ptrn ) {
			$pattern[] = sprintf( $wrap_pattern, $ptrn );
		}
		$parts['body'] = $this->wpl_script_replace_callback( $parts['body'], $pattern, $data, 'body' );

		if ( null === $parts['body'] ) {
			throw new RuntimeException( 'An error occurred calling preg_replace_callback() context body.' );
		}

		return $parts;
	}

	/**
	 * Replace callback for blocking scripts.
	 *
	 * @since 2.6
	 * @param string $html HTML.
	 * @param Array  $pattern Pattern.
	 * @param Array  $data Data.
	 * @param string $elm_position Element position head or body.
	 * @return null|string|string[]
	 */
	public function wpl_script_replace_callback( $html, $pattern, $data, $elm_position = 'head' ) {
		return preg_replace_callback(
			$pattern,
			function( $matches ) use ( $data, $elm_position ) {
				$placeholder          = '';
				$script_category_slug = $data['category_slug'];
				$script_label         = $data['label'];
				$script_load_on_start = $data['block_scripts'];
				$script_type          = 'text/plain';
				$match                = $matches[0];

				$whitelist_urls = $this->wpl_whitelisted_scripts();

				$result = $this->wpl_check_for_script_match($whitelist_urls, $match);

				if ($result) {
					// if whitelist script matches then bypass.
					return $match;
				} else {
					if ( isset( $data['placeholder'] ) ) {
						$placeholder = $data['placeholder'];
					}
					$wpl_replace = 'data-wpl-class="wpl-blocker-script" data-wpl-label="' . $script_label . '"  data-wpl-script-type="' . $script_category_slug . '" data-wpl-block="' . $script_load_on_start . '" data-wpl-element-position="' . $elm_position . '"';
					if ( ( preg_match( '/<iframe.*(src=\"(.*)\").*>.*<\/iframe>/', $match, $element_match ) ) || ( preg_match( '/<object.*(src=\"(.*)\").*>.*<\/object >/', $match, $element_match ) ) || ( preg_match( '/<embed.*(src=\"(.*)\").*>/', $match, $element_match ) ) || ( preg_match( '/<img.*(src=\"(.*)\").*>/', $match, $element_match ) ) ) {
						$element_src        = $element_match[1];
						$element_modded_src = str_replace( 'src=', $wpl_replace . ' data-wpl-placeholder="' . $placeholder . '" data-wpl-src=', $element_src );
						$match              = str_replace( $element_src, $element_modded_src, $match );

					} else {
						if ( preg_match( '/type=/', $match ) ) {
							preg_match( "/(type=[\"|']text\/javascript[\"|']).*>(.*)/im", $match, $output_array );
							if ( ! empty( $output_array ) ) {
								$match = str_replace( $output_array[1], 'type="' . $script_type . '" ' . $wpl_replace, $match );
							}
							if ( 'Matomo Analytics' === $script_label ) {
								$match = str_replace( '<script', '<script type="' . $script_type . '" ' . $wpl_replace, $match );
							}
						} else {
							$match = str_replace( '<script', '<script type="' . $script_type . '" ' . $wpl_replace, $match );

						}
					}
					return $match;
				}
			},
			$html
		);
	}
}
new Gdpr_Cookie_Consent_Script_Blocker_Frontend();
