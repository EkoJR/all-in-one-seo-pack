<?php
/**
 * Notice Functions for AIOSEOP_Notices
 *
 * @since 2.4.2
 * @package All-in-One-SEO-Pack
 * @subpackage AIOSEOP_Notices
 */

if ( class_exists( 'AIOSEOP_Notices' ) ) {

	/**
	 * Set Notice with WooCommerce Detected on Non-Pro AIOSEOP
	 *
	 * When WC is detected on Non-Pros, and message is displayed to upgrade to
	 * AIOSEOP Pro. "No Thanks" delays for 30 days.
	 *
	 * @since 2.4.2
	 *
	 * @global AIOSEOP_Notices $aioseop_notices
	 *
	 * @param boolean $update Updates the notice with new content and configurations.
	 * @param boolean $reset  Notice are re-initiated.
	 */
	function aioseop_notice_set_woocommerce_detected_on_nonpro( $update = false, $reset = false ) {
		global $aioseop_notices;

		$notice = array(
			'slug'          => 'woocommerce_detected',
			'delay_time'    => 0,
			'message'       => __( 'We\'ve detected you\'re running WooCommerce. Upgrade to All in One SEO Pack Pro for increased SEO compatibility for your products.', 'all-in-one-seo-pack' ),
			'action_options' => array(),
			'class'         => 'notice-info',
			'target'        => 'user',
			'screens'       => array(),
		);
		$notice['action_options'][] = array(
			'time'    => 0,
			'text'    => __( 'Upgrade', 'all-in-one-seo-pack' ),
			'link'    => 'https://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/?loc=woo',
			'dismiss' => false,
			'class'   => 'button-primary',
		);
		$notice['action_options'][] = array(
			'time'    => 2592000,
			'text'    => __( 'No Thanks', 'all-in-one-seo-pack' ),
			'link'    => '',
			'dismiss' => false,
			'class'   => 'button-secondary',
		);

		if ( ! $aioseop_notices->insert_notice( $notice ) ) {
			if ( $update ) {
				$aioseop_notices->update_notice( $notice );
			}
			if ( $reset || ! isset( $aioseop_notices->active_notices[ $notice['slug'] ] ) ) {
				$aioseop_notices->activate_notice( $notice['slug'] );
			}
		}
	}

	/**
	 * Disable Notice for WooCommerce/Upgrade-to-Pro
	 *
	 * @todo Add to Pro version to disable message set by Non-Pro.
	 *
	 * @since 2.4.2
	 *
	 * @global AIOSEOP_Notices $aioseop_notices
	 */
	function aioseop_notice_disable_woocommerce_detected_on_nonpro() {
		global $aioseop_notices;
		$aioseop_notices->deactivate_notice( 'woocommerce_detected' );
	}
}
