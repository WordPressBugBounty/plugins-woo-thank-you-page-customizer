<?php
/**
 * Plugin Name: Thank You Page Customizer for WooCommerce
 * Plugin URI: https://villatheme.com/extensions/woo-thank-you-page-customizer
 * Description: Craft a stunning thank you page effortlessly with our user-friendly customization tools, offer coupons to customers after purchase.
 * Version: 1.1.5
 * Author: VillaTheme
 * Author URI: https://villatheme.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-thank-you-page-customizer
 * Domain Path: /languages
 * Copyright 2018-2024 VillaTheme.com. All rights reserved.
 * Tested up to: 6.6
 * WC requires at least: 7.0.0
 * WC tested up to: 9.0.2
 * Requires PHP: 7.0
 * Requires Plugins: woocommerce
 **/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VI_WOO_THANK_YOU_PAGE_VERSION', '1.1.5' );
/**
 * Detect plugin. For use on Front End only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class WOO_THANK_YOU_PAGE_CUSTOMIZER
 */
class WOO_THANK_YOU_PAGE_CUSTOMIZER {
	public function __construct() {
		//compatible with 'High-Performance order storage (COT)'
		add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ) );
		if ( is_plugin_active( 'woocommerce-thank-you-page-customizer/woocommerce-thank-you-page-customizer.php' ) ) {
			return;
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-thank-you-page-customizer" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
			require_once $init_file;
		}
		add_action( 'plugins_loaded', function () {
			if ( ! class_exists( 'VillaTheme_Require_Environment' ) ) {
				include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-thank-you-page-customizer" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . 'support.php';
			}

			$environment = new \VillaTheme_Require_Environment( [
					'plugin_name'     => 'Thank You Page Customizer for WooCommerce',
					'php_version'     => '7.0',
					'wp_version'      => '5.0',
					'wc_version'      => '7.0',
					'require_plugins' => [
						[
							'slug' => 'woocommerce',
							'name' => 'WooCommerce',
						],
					]
				]
			);

			if ( $environment->has_error() ) {
				return;
			}
		} );
	}

	public function before_woocommerce_init() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}

}

new WOO_THANK_YOU_PAGE_CUSTOMIZER();