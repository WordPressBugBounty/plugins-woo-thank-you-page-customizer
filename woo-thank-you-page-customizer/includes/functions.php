<?php
/**
 * Shared helper functions for Woo Thank You Page Customizer.
 *
 * @package woo-thank-you-page-customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wtypc_include_folder' ) ) {
	/**
	 * Include PHP files in a directory and instantiate matching classes.
	 *
	 * @param string       $path   Directory path.
	 * @param string       $prefix Class name prefix.
	 * @param array|string $ext    File extensions.
	 */
	function wtypc_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( '.' === $sfile || '..' === $sfile ) {
				continue;
			}
			if ( ! is_file( $path . '/' . $sfile ) ) {
				continue;
			}
			$ext_file  = pathinfo( $path . '/' . $sfile );
			$file_name = $ext_file['filename'];
			if ( empty( $ext_file['extension'] ) || ! in_array( $ext_file['extension'], $ext, true ) ) {
				continue;
			}
			$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );
			if ( ! class_exists( $class ) ) {
				require_once $path . $sfile;
				if ( class_exists( $class ) ) {
					new $class();
				}
			}
		}
	}
}

if ( ! function_exists( 'vi_include_folder' ) ) {
	/**
	 * Backward-compatible alias of wtypc_include_folder().
	 *
	 * @param string       $path   Directory path.
	 * @param string       $prefix Class name prefix.
	 * @param array|string $ext    File extensions.
	 */
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
		wtypc_include_folder( $path, $prefix, $ext );
	}
}

if ( ! function_exists( 'woocommerce_version_check' ) ) {
	/**
	 * Check if WooCommerce meets a minimum version.
	 *
	 * @param string $version Minimum version.
	 * @return bool
	 */
	function woocommerce_version_check( $version = '3.0' ) {
		global $woocommerce;

		if ( version_compare( $woocommerce->version, $version, '>=' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wtypc_sanitize_block' ) ) {
	/**
	 * Sanitize customizer block/text JSON payloads.
	 *
	 * @param mixed $var Raw value.
	 * @return mixed
	 */
	function wtypc_sanitize_block( $var ) {
		if ( is_array( $var ) ) {
			return map_deep( $var, 'wp_kses_post' );
		}
		if ( ! is_string( $var ) ) {
			return '';
		}
		$unslashed = wp_unslash( $var );
		$decoded   = json_decode( $unslashed, true );
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
			return wp_json_encode( map_deep( $decoded, 'wp_kses_post' ) );
		}

		return wp_kses_post( $unslashed );
	}
}

if ( ! function_exists( 'wtyp_sanitize_block' ) ) {
	/**
	 * Backward-compatible alias of wtypc_sanitize_block().
	 *
	 * @param mixed $var Raw value.
	 * @return mixed
	 */
	function wtyp_sanitize_block( $var ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
		return wtypc_sanitize_block( $var );
	}
}

if ( ! function_exists( 'wtypc_json_decode' ) ) {
	/**
	 * Decode a JSON string from settings forms into a sanitized array.
	 *
	 * @param mixed $var Raw JSON string or array.
	 * @return array
	 */
	function wtypc_json_decode( $var ) {
		if ( is_array( $var ) ) {
			return map_deep( $var, 'sanitize_text_field' );
		}
		$decoded = json_decode( wp_unslash( (string) $var ), true );
		if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $decoded ) ) {
			return array();
		}

		return map_deep( $decoded, 'sanitize_text_field' );
	}
}

if ( ! function_exists( 'wtyp_json_decode' ) ) {
	/**
	 * Backward-compatible alias of wtypc_json_decode().
	 *
	 * @param mixed $var Raw JSON string or array.
	 * @return array
	 */
	function wtyp_json_decode( $var ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
		return wtypc_json_decode( $var );
	}
}

if ( ! function_exists( 'wtypc_base64_encode' ) ) {
	/**
	 * Base64-encode a value, or each value in an array.
	 *
	 * @param mixed $value Value to encode.
	 * @return mixed
	 */
	function wtypc_base64_encode( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'wtypc_base64_encode', $value );
		}

		return base64_encode( $value );
	}
}
