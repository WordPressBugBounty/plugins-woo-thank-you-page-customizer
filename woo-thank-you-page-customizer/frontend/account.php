<?php
/*
Class Name: WTYPC_Frontend_Account
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2018 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WTYPC_Frontend_Account {
	protected $settings;

	public function __construct() {
		$this->settings = new WTYPC_DATA();
		add_action( 'woocommerce_order_status_changed', array( $this, 'revoke_coupon_on_terminal_status' ), 10, 4 );
		if ( $this->settings->get_params( 'my_account_coupon_enable' ) ) {
			add_filter( 'woocommerce_account_orders_columns', array( $this, 'woocommerce_account_orders_columns' ) );
			add_action( 'woocommerce_my_account_my_orders_column_wtypc_coupon', array(
				$this,
				'woocommerce_my_account_my_orders_column_wtypc_coupon'
			) );
		}
	}

	/**
	 * Disable thank-you unique coupon when the order reaches a terminal status.
	 *
	 * @param int            $order_id   Order ID.
	 * @param string         $old_status Previous status slug.
	 * @param string         $new_status New status slug.
	 * @param WC_Order|false $order      Order object.
	 */
	public function revoke_coupon_on_terminal_status( $order_id, $old_status, $new_status, $order ) {
		$terminal = array( 'cancelled', 'refunded', 'failed' );
		if ( ! in_array( $new_status, $terminal, true ) ) {
			return;
		}

		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! $order ) {
			return;
		}

		$should_revoke = apply_filters( 'wtypc_should_revoke_terminal_coupon', true, $order, $new_status, $old_status );
		if ( ! $should_revoke ) {
			return;
		}

		$disabled    = false;
		$coupon_code = $order->get_meta( 'woo_thank_you_page_coupon_code', true );
		if ( $coupon_code ) {
			$coupon = new WC_Coupon( $coupon_code );
			if ( $coupon->get_id() ) {
				$linked_order_id = absint( $coupon->get_meta( 'wtypc_unique_coupon' ) );
				if ( $linked_order_id && $linked_order_id === absint( $order->get_id() ) ) {
					$usage_count = absint( $coupon->get_usage_count() );
					if ( 0 === $usage_count ) {
						// WC treats usage_limit 0 as unlimited; expire unused coupons instead.
						$coupon->set_date_expires( time() - DAY_IN_SECONDS );
					} else {
						$coupon->set_usage_limit( $usage_count );
					}
					$coupon = apply_filters( 'wtypc_revoke_terminal_coupon', $coupon, $order, $new_status );
					$coupon->save();
					$disabled = true;
				}
			}
		}

		$order->update_meta_data( 'woo_thank_you_page_coupon_code', '' );
		$order->save();

		do_action( 'wtypc_revoked_terminal_coupon', $order, $new_status, $disabled, $old_status );
	}

	public function woocommerce_my_account_my_orders_column_wtypc_coupon( $order ) {
		$coupon_code = $order->get_meta( 'woo_thank_you_page_coupon_code', true );
		if ( $coupon_code ) {
			$coupon = new WC_Coupon( $coupon_code );
			$title  = '';
			$css    = '';
			if ( $coupon ) {
				$usage_count  = $coupon->get_usage_count();
				$usage_limit  = $coupon->get_usage_limit();
				$title        .= sprintf(
					/* translators: 1: times used, 2: usage limit */
					esc_html__( 'Used: %1$s/%2$s.', 'woo-thank-you-page-customizer' ),
					esc_html( (string) $usage_count ),
					esc_html( (string) $usage_limit )
				);
				$today        = strtotime( 'today' );
				$date_expires = $coupon->get_date_expires();
				$expires      = esc_html__( 'Never', 'woo-thank-you-page-customizer' );
				$date_format  = wc_date_format();
				if ( $coupon->get_discount_type() == 'percent' ) {
					$coupon_amount = $coupon->get_amount() . '%';
				} else {
					$coupon_amount = $this->wc_price( $coupon->get_amount() );
				}
				if ( $date_expires ) {
					$expires_ts = method_exists( $date_expires, 'getTimestamp' ) ? (int) $date_expires->getTimestamp() : strtotime( (string) $date_expires );
					$expires    = esc_html( $date_expires->date_i18n( $date_format ) );
					if ( $expires_ts <= $today ) {
						$css     = 'color:red;';
						$expires = '<span style="' . esc_attr( $css ) . '">' . $expires . '</span>';
					}
					if ( $usage_count == $usage_limit ) {
						$css = 'color:red;';
					}
				}
				$coupon_info = sprintf(
					/* translators: 1: coupon amount, 2: expiry date */
					__( '(%1$s - Expires: %2$s)', 'woo-thank-you-page-customizer' ),
					esc_html( $coupon_amount ),
					wp_kses_post( $expires )
				);

			}
			?>
            <div title="<?php echo esc_attr( $title ); ?>">
                <span class="woocommerce-thank-you-page-orders-coupon"
                      style="<?php echo esc_attr( $css ); ?>"><?php echo esc_html( strtoupper( $coupon_code ) ); ?></span>
				<?php
				if ( isset( $coupon_info ) ) {
					?>
                    <p><?php echo wp_kses_post( $coupon_info ); ?></p>
					<?php
				}
				?>
            </div>
			<?php
		}
	}

	public function woocommerce_account_orders_columns( $columns ) {
		if ( isset( $columns['order-actions'] ) ) {
			$order_action = $columns['order-actions'];
			unset( $columns['order-actions'] );
			$columns['wtypc_coupon']  = esc_html__( 'Coupon gift', 'woo-thank-you-page-customizer' );
			$columns['order-actions'] = $order_action;
		} else {
			$columns['wtypc_coupon'] = esc_html__( 'Coupon gift', 'woo-thank-you-page-customizer' );

		}

		return $columns;
	}

	public function wc_price( $price, $args = array() ) {
		$args = apply_filters(
			'wc_price_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			wp_parse_args(
				$args,
				array(
					'ex_tax_label'       => false,
					'currency'           => get_option( 'woocommerce_currency' ),
					'decimal_separator'  => get_option( 'woocommerce_price_decimal_sep' ),
					'thousand_separator' => get_option( 'woocommerce_price_thousand_sep' ),
					'decimals'           => get_option( 'woocommerce_price_num_decimals', 2 ),
					'price_format'       => get_woocommerce_price_format(),
				)
			)
		);

		$currency           = isset( $args['currency'] ) ? $args['currency'] : get_option( 'woocommerce_currency' );
		$decimal_separator  = isset( $args['decimal_separator'] ) ? $args['decimal_separator'] : '.';
		$thousand_separator = isset( $args['thousand_separator'] ) ? $args['thousand_separator'] : ',';
		$decimals           = isset( $args['decimals'] ) ? (int) $args['decimals'] : 2;
		$currency_pos       = get_option( 'woocommerce_currency_pos' );
		$price_format       = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left':
				$price_format = '%1$s%2$s';
				break;
			case 'right':
				$price_format = '%2$s%1$s';
				break;
			case 'left_space':
				$price_format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space':
				$price_format = '%2$s&nbsp;%1$s';
				break;
		}

		$negative = $price < 0;
		$price    = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * - 1 : $price ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$price    = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, $currency, $price );

		return $formatted_price;
	}

}
