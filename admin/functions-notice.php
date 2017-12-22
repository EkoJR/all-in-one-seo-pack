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
    function aioseop_notice_set_sitemap( $update = false, $reset = false ) {
        global $aioseop_notices;

        $notice = array(
            'slug'          => 'sitemap_max_warning',
            'delay_time'    => 0,
            'message'       => __( 'Notice: To avoid problems with your XML Sitemap, we strongly recommend you enable Sitemap Indexes and set the Maximum Posts per Sitemap Page to 1000.', 'all-in-one-seo-pack' ),
            'action_options' => array(),
            'class'         => 'notice-warning',
            'target'        => 'user',
            'screens'       => array(),
        );
        $notice['action_options'][] = array(
            'time'    => 0,
            'text'    => __( 'Sitemap Settings', 'all-in-one-seo-pack' ),
            'link'    => esc_url( get_admin_url( null, 'admin.php?page=' . AIOSEOP_PLUGIN_DIRNAME . '/modules/aioseop_sitemap.php' ) ),
            'dismiss' => false,
            'class'   => 'button-primary',
        );
        $notice['action_options'][] = array(
            //'time'    => 86400,
            'time'    => 30,
            'text'    => __( 'Delay', 'all-in-one-seo-pack' ),
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
     * Disable Notice for Sitemap
     *
     * @since 2.4.2
     *
     * @global AIOSEOP_Notices $aioseop_notices
     */
    function aioseop_notice_disable_sitemap() {
        global $aioseop_notices;
        $aioseop_notices->deactivate_notice( 'woocommerce_detected' );
    }
}
