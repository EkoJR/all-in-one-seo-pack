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
     * Set Notice with Sitemap Index +1000
     *
     * When there's 1000+ URLs with indexing enabled.
     *
     * @since 2.4.2
     *
     * @global AIOSEOP_Notices $aioseop_notices
     *
     * @param boolean $update Updates the notice with new content and configurations.
     * @param boolean $reset  Notice are re-initiated.
     */
    function aioseop_notice_activate_sitemap_indexes( $update = false, $reset = false ) {
        global $aioseop_notices;

        $notice = aioseop_notice_sitemap_indexes();

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
	 * Notice - Sitemap Indexes
	 *
	 * @since 2.4.2
	 *
	 * @return array
	 */
	function aioseop_notice_sitemap_indexes() {
    	return array(
			'slug'           => 'sitemap_max_warning',
			'delay_time'     => 0,
			'message'        => __( 'Notice: To avoid problems with your XML Sitemap, we strongly recommend you enable Sitemap Indexes and set the Maximum Posts per Sitemap Page to 1000.', 'all-in-one-seo-pack' ),
			'class'          => 'notice-warning',
			'target'         => 'user',
			'screens'        => array(),
			'action_options' => array(
				array(
					'time'    => 0,
					'text'    => __( 'Sitemap Settings', 'all-in-one-seo-pack' ),
					'link'    => esc_url( get_admin_url( null, 'admin.php?page=' . AIOSEOP_PLUGIN_DIRNAME . '/modules/aioseop_sitemap.php' ) ),
					'dismiss' => false,
					'class'   => 'button-primary',
				),
				array(
					'time'    => 86400, // 24 hours.
					//'time'    => 30, // For testing.
					'text'    => __( 'Delay', 'all-in-one-seo-pack' ),
					'link'    => '',
					'dismiss' => false,
					'class'   => 'button-secondary',
				),

			),
		);
	}

    /**
     * Disable Notice for Sitemap
     *
     * @since 2.4.2
     *
     * @global AIOSEOP_Notices $aioseop_notices
     */
    function aioseop_notice_disable_sitemap_indexes() {
        global $aioseop_notices;
        $aioseop_notices->deactivate_notice( 'woocommerce_detected' );
    }
}
