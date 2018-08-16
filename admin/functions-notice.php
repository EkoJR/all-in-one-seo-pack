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
	 * Set Notice for Disabled Public Blog
	 *
	 * Admin Notice when "Discourage search engines from indexing this site" is
	 * enabled in Settings > Reading.
	 *
	 * @since 2.4.2
	 *
	 * @global AIOSEOP_Notices $aioseop_notices
	 *
	 * @param boolean $update Updates the notice with new content and configurations.
	 * @param boolean $reset  Notice are re-initiated.
	 */
	function aioseop_notice_set_blog_public_disabled( $update = false, $reset = false ) {
		global $aioseop_notices;

		$notice = aioseop_notice_blog_visibility();

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
	 * Notice - Blog Visibility
	 *
	 * Displays when blog disables search engines from indexing.
	 *
	 * @since 2.4.2
	 *
	 * @return array
	 */
	function aioseop_notice_blog_visibility() {
		global $wp_version;
		$text = ( version_compare( $wp_version, '3.5.0', '>=' ) || function_exists( 'set_url_scheme' ) ) ? __( 'Reading Settings', 'all-in-one-seo-pack' ) : __( 'Privacy Settings', 'all-in-one-seo-pack' );

		return array(
			'slug'           => 'blog_public_disabled',
			'delay_time'     => 0,
			'message'        => sprintf( __( 'Warning: You\'re blocking access to search engines. You can change this in %1$s to toggle your blog visibility.', 'all-in-one-seo-pack' ), $text ),
			'delay_options'  => array(),
			'class'          => 'notice-error',
			'target'         => 'site',
			'screens'        => array(),
			'action_options' => array(
				array(
					'time'    => 0,
					'text'    => $text,
					'link'    => ( version_compare( $wp_version, '3.5.0', '>=' ) || function_exists( 'set_url_scheme' ) ) ? admin_url( 'options-reading.php' )              : admin_url( 'options-privacy.php' ),
					'dismiss' => false,
					'class'   => 'button-secondary',
				),
				array(
					'time'    => 604800,
					'text'    => __( 'Delay notice for a week.', 'all-in-one-seo-pack' ),
					'link'    => '',
					'dismiss' => false,
					'class'   => '',
				),
			),
		);
	}

	/**
	 * Disable Notice for Disabled Public Blog
	 *
	 * @since 2.4.2
	 *
	 * @global AIOSEOP_Notices $aioseop_notices
	 */
	function aioseop_notice_disable_blog_public_disabled() {
		global $aioseop_notices;
		$aioseop_notices->deactivate_notice( 'blog_public_disabled' );
	}
}
