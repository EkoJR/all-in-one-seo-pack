<?php
/**
 * Compatibility Class for the Gutenberg editor.
 *
 * @package All_in_One_SEO_Pack
 *
 * @since 3.2.8
 */


class AIOSEOP_Compat_Gutenberg extends All_in_One_SEO_Pack_Compatible {

	/**
	 * Returns flag indicating if WPML is present.
	 *
	 * @since 3.2.8
	 *
	 * @return bool
	 */
	public function exists() {
		global $wp_version;

		// Gutenberg only exists in 5.0 or above.
		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Declares compatibility hooks.
	 *
	 * @since 3.2.8
	 */
	public function hooks() {
		// Fix for Chrome 77.
		if ( false !== stripos( $_SERVER['HTTP_USER_AGENT'], 'Chrome/77.' ) ) {
			add_action( 'admin_head', array( $this, 'override_gutenberg_css_class' ) );
		}
	}

	/**
	 * Change height of a specific Gutenberg CSS class.
	 *
	 * @see https://github.com/WordPress/gutenberg/issues/17406
	 * @link https://github.com/semperfiwebdesign/all-in-one-seo-pack/issues/2914
	 *
	 * @since 3.2.8
	 *
	 * @return void
	 */
	public function override_gutenberg_css_class() {
		global $wp_version;

		// CSS class renamed from 'editor' to 'block-editor' in WP v5.2.
		if ( version_compare( $wp_version, '5.2', '<' ) ) {
			$this->override_gutenberg_css_class_helper( 'editor-writing-flow' );
		} else {
			$this->override_gutenberg_css_class_helper( 'block-editor-writing-flow' );
		}
	}

	/**
	 * Overrides a Gutenberg CSS class using inline CSS. Helper method of gutenberg_fix_metabox().
	 *
	 * @since 3.2.8
	 *
	 * @param string $class_name
	 * @return void
	 */
	protected function override_gutenberg_css_class_helper( $class_name ) {
		echo '<style>.' . $class_name . ' { height: auto; }</style>';
	}
}
