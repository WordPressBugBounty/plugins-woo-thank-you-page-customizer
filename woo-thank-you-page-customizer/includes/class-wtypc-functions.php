<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WTYPC_Functions {
	public static $params;

	/**
	 * WTYPC_Functions constructor.
	 * Init setting
	 */
	public function __construct() {
		self::$params = new WTYPC_DATA();
	}

	public static function email_style( $css ) {
		$css .= '.woo-thank-you-page-customizer-coupon-input{line-height:46px;display:block;text-align: center;font-size: 24px;width: 100%;height: 46px;vertical-align: middle;margin: 0;color:' . self::$params->get_params( 'coupon_code_color' ) . ';background-color:' . self::$params->get_params( 'coupon_code_bg_color' ) . ';border-width:' . self::$params->get_params( 'coupon_code_border_width' ) . 'px;border-style:' . self::$params->get_params( 'coupon_code_border_style' ) . ';border-color:' . self::$params->get_params( 'coupon_code_border_color' ) . ';}';

		return $css;
	}

	public static function send_email( $user_email, $coupon_code, $coupon_date_expires = '', $last_valid_date = '', $coupon_amount = '', $shortcodes = array(), $return = false ) {
		$headers             = "Content-Type: text/html\r\n";
		$content             = stripslashes( self::$params->get_params( 'coupon_email_content' ) );
		$subject             = stripslashes( self::$params->get_params( 'coupon_email_subject' ) );
		$heading             = stripslashes( self::$params->get_params( 'coupon_email_heading' ) );
		$coupon_code_style_1 = '<div class="woo-thank-you-page-customizer-coupon-input">' . $coupon_code . '</div>';
		$content             = str_replace( '{coupon_code_style_1}', $coupon_code_style_1, $content );
		$content             = str_replace( array(
			'{coupon_code}',
			'{coupon_date_expires}',
			'{last_valid_date}',
			'{coupon_amount}',
		), array(
			$coupon_code,
			$coupon_date_expires,
			$last_valid_date,
			$coupon_amount,
		), $content );
		$subject             = str_replace( array(
			'{coupon_code}',
			'{coupon_date_expires}',
			'{last_valid_date}',
			'{coupon_amount}'
		), array( $coupon_code, $coupon_date_expires, $last_valid_date, $coupon_amount ), $subject );
		$heading             = str_replace( array(
			'{coupon_code}',
			'{coupon_date_expires}',
			'{last_valid_date}',
			'{coupon_amount}'
		), array( $coupon_code, $coupon_date_expires, $last_valid_date, $coupon_amount ), $heading );
		if ( is_array( $shortcodes ) && count( $shortcodes ) ) {
			foreach ( $shortcodes as $key => $value ) {
				$content = str_replace( '{' . $key . '}', $value, $content );
				$subject = str_replace( '{' . $key . '}', $value, $subject );
				$heading = str_replace( '{' . $key . '}', $value, $heading );
			}
		}
		add_filter( 'woocommerce_email_styles', array( __CLASS__, 'email_style' ) );
		$mailer  = WC()->mailer();
		$email   = new WC_Email();
		$content = $email->style_inline( $mailer->wrap_message( $heading, $content ) );

		add_filter( 'viwec_disable_woocommerce_email_inline_style', '__return_false' );
		$send    = $email->send( $user_email, $subject, $content, $headers, array() );
		remove_filter( 'woocommerce_email_styles', array( __CLASS__, 'email_style' ) );
		if ( $return ) {
			return $send;
		}
	}
	public static function woocommerce_valid_order_statuses_for_order_again( $order_status ) {
		self::$params = new WTYPC_DATA();
		$status = self::$params->get_params( 'order_status' );
		if ( is_array( $status ) && count( $status ) ) {
			$order_status = array();
			foreach ( $status as $key => $value ) {
				$order_status[] = str_replace( 'wc-', '', $value );
			}
		}

		return $order_status;
	}

	/**
	 * Render Order again button on thank you page.
	 *
	 * WooCommerce's woocommerce_order_again_button() returns early on
	 * is_order_received_page(), so it never outputs there.
	 *
	 * @param WC_Order|false $order Order object.
	 */
	public static function render_order_again_button( $order ) {
		$statuses_for_reordering = apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) );
		if ( ! $order || ! $order->has_status( $statuses_for_reordering ) || ! is_user_logged_in() ) {
			return;
		}

		$wp_button_class = '';
		if ( function_exists( 'wc_wp_theme_get_element_class_name' ) && wc_wp_theme_get_element_class_name( 'button' ) ) {
			$wp_button_class = ' ' . wc_wp_theme_get_element_class_name( 'button' );
		}

		wc_get_template(
			'order/order-again.php',
			array(
				'order'           => $order,
				'wp_button_class' => $wp_button_class,
				'order_again_url' => wp_nonce_url( add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ),
			)
		);
	}
}

if ( ! class_exists( 'WTYPC_F_FUNCTIONS', false ) ) {
	class_alias( 'WTYPC_Functions', 'WTYPC_F_FUNCTIONS' );
}
