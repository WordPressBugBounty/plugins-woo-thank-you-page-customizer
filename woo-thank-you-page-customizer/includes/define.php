<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WTYPC_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woo-thank-you-page-customizer' . DIRECTORY_SEPARATOR );
define( 'WTYPC_ADMIN', WTYPC_DIR . 'admin' . DIRECTORY_SEPARATOR );
define( 'WTYPC_FRONTEND', WTYPC_DIR . 'frontend' . DIRECTORY_SEPARATOR );
define( 'WTYPC_LANGUAGES', WTYPC_DIR . 'languages' . DIRECTORY_SEPARATOR );
define( 'WTYPC_INCLUDES', WTYPC_DIR . 'includes' . DIRECTORY_SEPARATOR );
define( 'WTYPC_TEMPLATES', WTYPC_DIR . 'templates' . DIRECTORY_SEPARATOR );
$wtypc_plugin_url = plugins_url( 'woo-thank-you-page-customizer' );
$wtypc_plugin_url = str_replace( '/includes', '', $wtypc_plugin_url );
define( 'WTYPC_CSS', $wtypc_plugin_url . '/css/' );
define( 'WTYPC_CSS_DIR', WTYPC_DIR . 'css' . DIRECTORY_SEPARATOR );
define( 'WTYPC_JS', $wtypc_plugin_url . '/js/' );
define( 'WTYPC_JS_DIR', WTYPC_DIR . 'js' . DIRECTORY_SEPARATOR );
define( 'WTYPC_IMAGES', $wtypc_plugin_url . '/images/' );
define( 'WTYPC_MARKERS', WTYPC_IMAGES . '/markers/' );

if ( ! defined( 'VI_WOO_THANK_YOU_PAGE_DIR' ) ) {
	define( 'VI_WOO_THANK_YOU_PAGE_DIR', WTYPC_DIR ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_ADMIN', WTYPC_ADMIN ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_FRONTEND', WTYPC_FRONTEND ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_LANGUAGES', WTYPC_LANGUAGES ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_INCLUDES', WTYPC_INCLUDES ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_TEMPLATES', WTYPC_TEMPLATES ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_CSS', WTYPC_CSS ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_CSS_DIR', WTYPC_CSS_DIR ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_JS', WTYPC_JS ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_JS_DIR', WTYPC_JS_DIR ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_IMAGES', WTYPC_IMAGES ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	define( 'VI_WOO_THANK_YOU_PAGE_MARKERS', WTYPC_MARKERS ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
}

/*Include functions file*/
if ( is_file( WTYPC_INCLUDES . 'functions.php' ) ) {
	require_once WTYPC_INCLUDES . 'functions.php';
}
if ( is_file( WTYPC_INCLUDES . 'support.php' ) ) {
	require_once WTYPC_INCLUDES . 'support.php';
}
if ( is_file( WTYPC_INCLUDES . 'data.php' ) ) {
	require_once WTYPC_INCLUDES . 'data.php';
}
if ( is_file( WTYPC_INCLUDES . 'class-wtypc-functions.php' ) ) {
	require_once WTYPC_INCLUDES . 'class-wtypc-functions.php';
}
if ( is_file( WTYPC_INCLUDES . 'custom-controls.php' ) ) {
	require_once WTYPC_INCLUDES . 'custom-controls.php';
}
wtypc_include_folder( WTYPC_ADMIN, 'WTYPC_Admin_' );
wtypc_include_folder( WTYPC_FRONTEND, 'WTYPC_Frontend_' );

if ( class_exists( 'WTYPC_DATA', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_DATA', false ) ) {
	class_alias( 'WTYPC_DATA', 'VI_WOO_THANK_YOU_PAGE_DATA' );
}
if ( class_exists( 'WTYPC_Functions', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Functions', false ) ) {
	class_alias( 'WTYPC_Functions', 'VI_WOO_THANK_YOU_PAGE_Functions' );
}
if ( class_exists( 'WTYPC_Admin_Admin', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Admin_Admin', false ) ) {
	class_alias( 'WTYPC_Admin_Admin', 'VI_WOO_THANK_YOU_PAGE_Admin_Admin' );
}
if ( class_exists( 'WTYPC_Admin_Design', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Admin_Design', false ) ) {
	class_alias( 'WTYPC_Admin_Design', 'VI_WOO_THANK_YOU_PAGE_Admin_Design' );
}
if ( class_exists( 'WTYPC_Admin_Settings', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Admin_Settings', false ) ) {
	class_alias( 'WTYPC_Admin_Settings', 'VI_WOO_THANK_YOU_PAGE_Admin_Settings' );
}
if ( class_exists( 'WTYPC_Frontend_Frontend', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Frontend_Frontend', false ) ) {
	class_alias( 'WTYPC_Frontend_Frontend', 'VI_WOO_THANK_YOU_PAGE_Frontend_Frontend' );
}
if ( class_exists( 'WTYPC_Frontend_Account', false ) && ! class_exists( 'VI_WOO_THANK_YOU_PAGE_Frontend_Account', false ) ) {
	class_alias( 'WTYPC_Frontend_Account', 'VI_WOO_THANK_YOU_PAGE_Frontend_Account' );
}
