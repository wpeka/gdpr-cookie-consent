<?php
/**
 * Analytics Core functions.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

/*
Templates / Views.
--------------------------------------------------------------------------------------------
*/
if ( ! function_exists( 'as_get_template_path' ) ) {
	/**
	 * Get analytics template path
	 *
	 * @param string $path template path.
	 *  @return string
	 */
	function as_get_template_path( $path ) {
		return WP_STAT__DIR_TEMPLATES . '/' . trim( $path, '/' );
	}

	/**
	 * Include template.
	 *
	 * @param string $path Path.
	 * @param null   $params Parameters.
	 */
	function as_include_template( $path, &$params = null ) {
		$VARS = &$params;
		include as_get_template_path( $path );
	}

	/**
	 * Include once.
	 *
	 * @param string $path Path.
	 * @param null   $params Parameters.
	 */
	function as_include_once_template( $path, &$params = null ) {
		$VARS = &$params;
		include_once as_get_template_path( $path );
	}

	/**
	 * Require template.
	 *
	 * @param string $path Path.
	 * @param array  $params Parameters.
	 */
	function as_require_template( $path, &$params = null ) {
		$VARS = &$params;
		require as_get_template_path( $path );
	}

	/**
	 * Require once.
	 *
	 * @param string $path Path.
	 * @param array  $params Parameters.
	 */
	function as_require_once_template( $path, &$params = null ) {
		$VARS = &$params;
		require_once as_get_template_path( $path );
	}

	/**
	 * Get Template.
	 *
	 * @param string $path Path.
	 * @param array  $params Parameters.
	 * @return string
	 */
	function as_get_template( $path, &$params = null ) {
		ob_start();

		$VARS = &$params;
		require as_get_template_path( $path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'as_request_get' ) ) {
	/**
	 * A helper method to fetch GET/POST user input.
	 *
	 * @author CyberChimps
	 *
	 * @param string      $key Key.
	 * @param mixed       $def Defined.
	 * @param string|bool $type Type.
	 *
	 * @return mixed
	 */
	function as_request_get( $key, $def = false, $type = false ) {
		if ( is_string( $type ) ) {
			$type = strtolower( $type );
		}

		/**
		 *  Helper method to fetch GET/POST user input.
		 */
		switch ( $type ) {
			case 'post':
				$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : $def;
				break;
			case 'get':
				$value = isset( $_GET[ $key ] ) ? $_GET[ $key ] : $def;
				break;
			default:
				$value = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : $def;
				break;
		}

		return $value;
	}
}

if ( ! function_exists( 'as_request_get_bool' ) ) {
	/**
	 * Fetch GET/POST user boolean input
	 *
	 * @author CyberChimps
	 *
	 * @param string $key Key.
	 * @param bool   $def Defined.
	 *
	 * @return bool|mixed
	 */
	function as_request_get_bool( $key, $def = false ) {
		$val = as_request_get( $key, null );

		if ( is_null( $val ) ) {
			return $def;
		}

		if ( is_bool( $val ) ) {
			return $val;
		} elseif ( is_numeric( $val ) ) {
			if ( 1 === $val ) {
				return true;
			} elseif ( 0 === $val ) {
				return false;
			}
		} elseif ( is_string( $val ) ) {
			$val = strtolower( $val );

			if ( 'true' === $val ) {
				return true;
			} elseif ( 'false' === $val ) {
				return false;
			}
		}

		return $def;
	}
}

if ( ! function_exists( 'as_get_raw_referer' ) ) {
	/**
	 * Retrieves unvalidated referer from '_wp_http_referer'
	 *
	 * @since 1.0.0
	 *
	 * @return string|false Referer URL on success, false on failure.
	 */
	function as_get_raw_referer() {
		if ( function_exists( 'wp_get_raw_referer' ) ) {
			return wp_get_raw_referer();
		}
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
			return wp_unslash( $_REQUEST['_wp_http_referer'] );
		} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
			return wp_unslash( $_SERVER['HTTP_REFERER'] );
		}

		return false;
	}
}

if ( ! function_exists( 'as_asset_url' ) ) {
	/**
	 * Generates an absolute URL to the given path. This function ensures that the URL will be correct whether the asset
	 * is inside a plugin's folder or a theme's folder.
	 *
	 * Examples:
	 * 1. "themes" folder
	 *    Path: C:/xampp/htdocs/fswp/wp-content/themes/twentytwelve/analytics/assets/css/admin/common.css
	 *    URL: http://awp:8080/wp-content/themes/twentytwelve/analytics/assets/css/admin/common.css
	 *
	 * 2. "plugins" folder
	 *    Path: C:/xampp/htdocs/fswp/wp-content/plugins/rating-widget-premium/analytics/assets/css/admin/common.css
	 *    URL: http://awp:8080/wp-content/plugins/rating-widget-premium/analytics/assets/css/admin/common.css
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param  string $asset_abs_path Asset's absolute path.
	 *
	 * @return string Asset's URL.
	 */
	function as_asset_url( $asset_abs_path ) {
		$wp_content_dir = as_normalize_path( WP_CONTENT_DIR );
		$asset_abs_path = as_normalize_path( $asset_abs_path );

		if ( 0 === strpos( $asset_abs_path, $wp_content_dir ) ) {
			// Handle both theme and plugin assets located in the standard directories.
			$asset_rel_path = str_replace( $wp_content_dir, '', $asset_abs_path );
			$asset_url      = content_url( as_normalize_path( $asset_rel_path ) );
		} else {
			$wp_plugins_dir = as_normalize_path( WP_PLUGIN_DIR );
			if ( 0 === strpos( $asset_abs_path, $wp_plugins_dir ) ) {
				// Try to handle plugin assets that may be located in a non-standard plugins directory.
				$asset_rel_path = str_replace( $wp_plugins_dir, '', $asset_abs_path );
				$asset_url      = plugins_url( as_normalize_path( $asset_rel_path ) );
			} else {
				// Try to handle theme assets that may be located in a non-standard themes directory.
				$active_theme_stylesheet = get_stylesheet();
				$wp_themes_dir           = as_normalize_path( trailingslashit( get_theme_root( $active_theme_stylesheet ) ) );
				$asset_rel_path          = str_replace( $wp_themes_dir, '', as_normalize_path( $asset_abs_path ) );
				$asset_url               = trailingslashit( get_theme_root_uri( $active_theme_stylesheet ) ) . as_normalize_path( $asset_rel_path );
			}
		}

		return $asset_url;
	}
}

if ( ! function_exists( 'as_enqueue_local_style' ) ) {
	/**
	 * Enqueue Local style.
	 *
	 * @param string $handle Handle.
	 * @param string $path Path.
	 * @param array  $deps Dependencies.
	 * @param bool   $ver Version.
	 * @param string $media Media.
	 */
	function as_enqueue_local_style( $handle, $path, $deps = array(), $ver = false, $media = 'all' ) {
		wp_enqueue_style( $handle, as_asset_url( WP_STAT__DIR_CSS . '/' . trim( $path, '/' ) ), $deps, $ver, $media );
	}
}
