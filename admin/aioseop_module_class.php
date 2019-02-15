<?php
/**
 * All in One SEO Pack Module class
 *
 * @package All-in-One-SEO-Pack
 * @version 2.3.12.2
 */

if ( ! class_exists( 'All_in_One_SEO_Pack_Module' ) ) {

	// TODO Class name is not valid; consider All_In_One_SEO_Pack_Module instead.
	// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid
	/**
	 * The module base class; handles settings, options, menus, metaboxes, etc.
	 */
	abstract class All_in_One_SEO_Pack_Module {
		// phpcs:enable
		/**
		 * Class Instance
		 *
		 * (Unused) The class instance typically used in a singleton design.
		 *
		 * @since ?
		 * @var null
		 */
		public static $instance = null;

		/**
		 * Plugin Name
		 *
		 * @since ?
		 * @var string
		 */
		protected $plugin_name;

		// TODO investigate merging $name & $menu_name together. Only 'General Settings' is in $menu_name.
		/**
		 * Module Name
		 *
		 * Human Readable name for modules
		 *
		 * @since ?
		 * @var string
		 */
		protected $name;

		/**
		 * Menu Name
		 *
		 * @since ?
		 * @var string
		 */
		protected $menu_name;

		/**
		 * (Module) Prefix
		 *
		 * The module's slug.
		 *
		 * @since ?
		 * @var string
		 */
		protected $prefix;

		/**
		 * (Module) File Path
		 *
		 * @since ?
		 * @var string
		 */
		protected $file;

		/**
		 * Options
		 *
		 * Plugin Options
		 *
		 * @since ?
		 * @var array
		 */
		protected $options;

		/**
		 * Option Name
		 *
		 * The WP_Option name.
		 *
		 * @since ?
		 * @var string
		 */
		protected $option_name;

		/**
		 * Default Option
		 *
		 * The default values for $options.
		 *
		 * @since ?
		 * @var array
		 */
		protected $default_options;

		/**
		 * (Module) Help Text
		 *
		 * @since ?
		 * @var array
		 */
		protected $help_text = array();

		/**
		 * Help Anchors
		 *
		 * @since ?
		 * @var array
		 */
		protected $help_anchors = array();

		/**
		 * (Display) Locations
		 *
		 * Organize settings into settings pages with a menu items and/or metaboxes on post types edit screen.
		 *
		 * @since ?
		 * @var array
		 */
		protected $locations = null;

		/**
		 * (Display) Layout
		 *
		 * The layout settings used during output. Organize settings on a settings page into multiple, separate metaboxes.
		 *
		 * @since ?
		 * @var array
		 */
		protected $layout = null;

		/**
		 * Tab (Settings)
		 *
		 * Organize layouts on a settings page into multiple, separate tabs.
		 *
		 * @since ?
		 * @var array
		 */
		protected $tabs = null;

		/**
		 * Current Tab
		 *
		 * The current tab.
		 *
		 * @since ?
		 * @var string
		 */
		protected $current_tab = null;

		/**
		 * PageHook
		 *
		 * The current page hook.
		 *
		 * @since ?
		 * @var string
		 */
		protected $pagehook = null;

		/**
		 * Store Option
		 *
		 * @since ?
		 * @var bool
		 */
		protected $store_option = false;

		/**
		 * Parent Option
		 *
		 * The WP_Option name.
		 *
		 * @since ?
		 * @var string
		 */
		protected $parent_option = 'aioseop_options';

		/**
		 * Post Metaboxes
		 *
		 * @since ?
		 * @var array
		 */
		protected $post_metaboxes = array();

		/**
		 * Tabbed Metaboxes
		 *
		 * @since ?
		 * @var bool
		 */
		protected $tabbed_metaboxes = true;

		/**
		 * Credentials
		 *
		 * Used for the WP Filesystem.
		 *
		 * @since ?
		 * @var bool
		 */
		protected $credentials = false;

		/**
		 * Scritp Data
		 *
		 * Used for paasing data to JavaScript.
		 *
		 * @since ?
		 * @var array
		 */
		protected $script_data = null;

		/**
		 * Plugin Path
		 *
		 * @since ?
		 * @var array|null
		 */
		protected $plugin_path = null;

		/**
		 * Pointers
		 *
		 * @since ?
		 * @var array
		 */
		protected $pointers = array();

		/**
		 * Form
		 *
		 * @since ?
		 * @var string
		 */
		protected $form = 'dofollow';

		/**
		 * Handles calls to display_settings_page_{$location}, does error checking.
		 *
		 * @since ?
		 *
		 * @throws Exception Standard error.
		 * @throws BadMethodCallException Undefined method or if some arguments are missing.
		 *
		 * @param string $name      The method being called.
		 * @param array  $arguments Params passed to the method.
		 */
		public function __call( $name, $arguments ) {
			if ( 0 === $this->strpos( $name, 'display_settings_page_' ) ) {
				// TODO Remove "return", since no value is returned from `$this->display_settings_page()`.
				return $this->display_settings_page( $this->substr( $name, 22 ) );
			}

			/* Translators: The method being called in the current instance */
			$error = sprintf( __( "Method %s doesn't exist", 'all-in-one-seo-pack' ), $name );
			if ( class_exists( 'BadMethodCallException' ) ) {
				throw new BadMethodCallException( $error );
			}
			throw new Exception( $error );
		}

		/**
		 * All_in_One_SEO_Pack_Module constructor.
		 *
		 * @since ?
		 */
		public function __construct() {
			if ( empty( $this->file ) ) {
				$this->file = __FILE__;
			}

			$this->plugin_name = AIOSEOP_PLUGIN_NAME;

			$this->plugin_path               = array();
			$this->plugin_path['basename']   = plugin_basename( $this->file );
			$this->plugin_path['dirname']    = dirname( $this->plugin_path['basename'] );
			$this->plugin_path['url']        = plugin_dir_url( $this->file );
			$this->plugin_path['images_url'] = $this->plugin_path['url'] . 'images';

			$this->script_data['plugin_path'] = $this->plugin_path;
		}

		/**
		 * Get Class Option
		 *
		 * Get options for module, stored individually or together.
		 *
		 * @since ?
		 *
		 * @return array|bool
		 */
		public function get_class_option() {
			$option_name = $this->get_option_name();
			if ( $this->store_option || $option_name === $this->parent_option ) {
				return get_option( $option_name );
			} else {
				$option = get_option( $this->parent_option );
				if ( isset( $option['modules'] ) && isset( $option['modules'][ $option_name ] ) ) {
					return $option['modules'][ $option_name ];
				}
			}

			return false;
		}

		/**
		 * Update Class Option
		 *
		 * Update options for module, stored individually or together.
		 *
		 * @since ?
		 *
		 * @param array $option_data AIOSEOP options from wp_options.
		 * @param bool  $option_name ?.
		 * @return bool
		 */
		public function update_class_option( $option_data, $option_name = false ) {
			if ( false === $option_name ) {
				$option_name = $this->get_option_name();
			}
			if ( $this->store_option || $option_name === $this->parent_option ) {
				return update_option( $option_name, $option_data );
			} else {
				$option = get_option( $this->parent_option );
				if ( ! isset( $option['modules'] ) ) {
					$option['modules'] = array();
				}
				$option['modules'][ $option_name ] = $option_data;

				return update_option( $this->parent_option, $option );
			}
		}

		/**
		 * Delete Class Option
		 *
		 * Delete options for module, stored individually or together.
		 *
		 * @since ?
		 *
		 * @param bool $delete Determines whether to delete the Db options.
		 * @return bool
		 */
		public function delete_class_option( $delete = false ) {
			$option_name = $this->get_option_name();
			if ( $this->store_option || $delete ) {
				delete_option( $option_name );
			} else {
				$option = get_option( $this->parent_option );
				if ( isset( $option['modules'] ) && isset( $option['modules'][ $option_name ] ) ) {
					unset( $option['modules'][ $option_name ] );

					return update_option( $this->parent_option, $option );
				}
			}

			return false;
		}

		/**
		 * Get the option name with prefix.
		 */
		public function get_option_name() {
			if ( ! isset( $this->option_name ) || empty( $this->option_name ) ) {
				$this->option_name = $this->prefix . 'options';
			}

			return $this->option_name;
		}

		/**
		 * Option Isset
		 *
		 * Convenience function to see if an option is set.
		 *
		 * @since ?
		 *
		 * @param string $option   The option slug.
		 * @param null   $location ?.
		 * @return bool
		 */
		public function option_isset( $option, $location = null ) {
			$prefix = $this->get_prefix( $location );
			$opt    = $prefix . $option;

			return ( isset( $this->options[ $opt ] ) && $this->options[ $opt ] );
		}

		/**
		 * Conver Case
		 *
		 * Case conversion; handle non UTF-8 encodings and fallback.
		 *
		 * @since ?
		 *
		 * @param string $str  The string to modify.
		 * @param string $mode Type of modification. upper|lower.
		 * @return string
		 */
		public function convert_case( $str, $mode = 'upper' ) {
			static $charset = null;
			if ( null === $charset ) {
				$charset = get_bloginfo( 'charset' );
			}
			$str = (string) $str;
			if ( 'title' === $mode ) {
				if ( function_exists( 'mb_convert_case' ) ) {
					return mb_convert_case( $str, MB_CASE_TITLE, $charset );
				} else {
					return ucwords( $str );
				}
			}

			if ( 'UTF-8' === $charset ) {
				global $utf8_tables;

				include_once AIOSEOP_PLUGIN_DIR . 'inc/aioseop_UTF8.php';
				if ( is_array( $utf8_tables ) ) {
					if ( 'upper' === $mode ) {
						return strtr( $str, $utf8_tables['strtoupper'] );
					}
					if ( 'lower' === $mode ) {
						return strtr( $str, $utf8_tables['strtolower'] );
					}
				}
			}

			if ( 'upper' === $mode ) {
				if ( function_exists( 'mb_strtoupper' ) ) {
					return mb_strtoupper( $str, $charset );
				} else {
					return strtoupper( $str );
				}
			}

			if ( 'lower' === $mode ) {
				if ( function_exists( 'mb_strtolower' ) ) {
					return mb_strtolower( $str, $charset );
				} else {
					return strtolower( $str );
				}
			}

			return $str;
		}

		/**
		 * String to Lowercase
		 *
		 * Compatible with mb_strtolower(), an UTF-8 friendly replacement for strtolower().
		 *
		 * @since ?
		 *
		 * @param string $str The string to modify.
		 * @return string
		 */
		public function strtolower( $str ) {
			return $this->convert_case( $str, 'lower' );
		}

		/**
		 * String to Uppercase
		 *
		 * Compatible with mb_strtoupper(), an UTF-8 friendly replacement for strtoupper().
		 *
		 * @since ?
		 *
		 * @param string $str The string to modify.
		 * @return string
		 */
		public function strtoupper( $str ) {
			return $this->convert_case( $str, 'upper' );
		}

		/**
		 * (Title) Uppercase Words
		 *
		 * Compatible with mb_convert_case(), an UTF-8 friendly replacement for ucwords().
		 *
		 * @since ?
		 *
		 * @param string $str The string to modify.
		 * @return string
		 */
		public function ucwords( $str ) {
			return $this->convert_case( $str, 'title' );
		}

		/**
		 * PHP sublen|mb_sublen
		 *
		 * Wrapper for strlen() - uses mb_strlen() if possible.
		 *
		 * @since ?
		 *
		 * @param string $string The string to modify.
		 * @return int
		 */
		public function strlen( $string ) {
			if ( function_exists( 'mb_strlen' ) ) {
				return mb_strlen( $string );
			}

			return strlen( $string );
		}

		/**
		 * PHP substr|mb_substr
		 *
		 * Wrapper for substr() - uses mb_substr() if possible.
		 *
		 * @since ?
		 *
		 * @link http://php.net/manual/en/function.substr.php
		 * @link http://php.net/manual/en/function.mb-substr.php
		 *
		 * @param string $string The input string. Must be one character or longer.
		 * @param int    $start  Position to start on the string.
		 * @param int    $length End position.
		 * @return mixed
		 */
		public function substr( $string, $start = 0, $length = 2147483647 ) {
			$args = func_get_args();
			if ( function_exists( 'mb_substr' ) ) {
				return call_user_func_array( 'mb_substr', $args );
			}

			return call_user_func_array( 'substr', $args );
		}

		/**
		 * PHP strpos|mb_strpos
		 *
		 * Wrapper for strpos() - uses mb_strpos() if possible.
		 *
		 * @since ?
		 *
		 * @link http://php.net/manual/en/function.strpos.php
		 * @link http://php.net/manual/en/function.mb-strpos.php
		 *
		 * @param string $haystack The string to search in.
		 * @param string $needle   Should either be explicitly cast to string, or an explicit call to chr().
		 * @param int    $offset   Optional. Number of characters counted from the beginning of the string.
		 * @return bool|int
		 */
		public function strpos( $haystack, $needle, $offset = 0 ) {
			if ( function_exists( 'mb_strpos' ) ) {
				return mb_strpos( $haystack, $needle, $offset );
			}

			return strpos( $haystack, $needle, $offset );
		}

		/**
		 * PHP strrpos|mb_strrpos
		 *
		 * Wrapper for strrpos() - uses mb_strrpos() if possible.
		 *
		 * @since ?
		 *
		 * @link http://php.net/manual/en/function.strpos.php
		 * @link http://php.net/manual/en/function.mb-strpos.php
		 *
		 * @param string $haystack The string to search in.
		 * @param string $needle   Should either be explicitly cast to string, or an explicit call to chr().
		 * @param int    $offset   Optional. Number of characters counted from the beginning of the string.
		 * @return bool|int
		 */
		public function strrpos( $haystack, $needle, $offset = 0 ) {
			if ( function_exists( 'mb_strrpos' ) ) {
				return mb_strrpos( $haystack, $needle, $offset );
			}

			return strrpos( $haystack, $needle, $offset );
		}

		/**
		 * HTML String to Array
		 *
		 * Convert html string to php array - useful to get a serializable value.
		 *
		 * @since ?
		 *
		 * @param string $html_str The HTML content.
		 * @return array
		 */
		public function html_string_to_array( $html_str ) {
			if ( ! class_exists( 'DOMDocument' ) ) {
				return array();
			} else {
				$doc = new DOMDocument();
				$doc->loadXML( $html_str );

				return $this->domnode_to_array( $doc->documentElement ); // @phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		/**
		 * Domnode to Array
		 *
		 * @since ?
		 *
		 * @param DOMElement $node DOMDocument element/node.
		 * @return array|string
		 */
		public function domnode_to_array( $node ) {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			switch ( $node->nodeType ) {
				case XML_CDATA_SECTION_NODE:
				case XML_TEXT_NODE:
					return trim( $node->textContent );
				case XML_ELEMENT_NODE:
					$output = array();
					for ( $i = 0, $m = $node->childNodes->length; $i < $m; $i ++ ) {
						$child = $node->childNodes->item( $i );
						$v     = $this->domnode_to_array( $child );
						if ( isset( $child->tagName ) ) {
							$t = $child->tagName;
							if ( ! isset( $output[ $t ] ) ) {
								$output[ $t ] = array();
							}
							if ( is_array( $output ) ) {
								$output[ $t ][] = $v;
							}
						} elseif ( $v || '0' === $v ) {
							$output = (string) $v;
						}
					}
					if ( $node->attributes->length && ! is_array( $output ) ) {
						// Has attributes but isn't an array.
						$output = array( '@content' => $output );
					} //Change output into an array.
					if ( is_array( $output ) ) {
						if ( $node->attributes->length ) {
							$a = array();
							foreach ( $node->attributes as $attr_name => $attr_node ) {
								$a[ $attr_name ] = (string) $attr_node->value;
							}
							$output['@attributes'] = $a;
						}
						foreach ( $output as $t => $v ) {
							// TODO Change to strict comparisons.
							if ( is_array( $v ) && 1 == count( $v ) && '@attributes' != $t ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
								$output[ $t ] = $v[0];
							}
						}
					}
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( empty( $output ) ) {
				return '';
			}

			return $output;
		}

		/**
		 * Apply Custom Field Fields
		 *
		 * Adds support for using %cf_(name of field)% for using custom fields / Advanced Custom Fields
		 * in titles / descriptions etc.
		 *
		 * @since ?
		 *
		 * @param string $format The title, description, keywords, etc..
		 * @return mixed
		 */
		public function apply_cf_fields( $format ) {
			return preg_replace_callback( '/%cf_([^%]*?)%/', array( $this, 'cf_field_replace' ), $format );
		}

		/**
		 * Custom Field Replace
		 *
		 * Callback function for applying custom fields.
		 *
		 * @since ?
		 *
		 * @link http://php.net/manual/en/function.preg-replace-callback.php
		 *
		 * @param array $matches Stores the matches found.
		 * @return bool|mixed|string
		 */
		public function cf_field_replace( $matches ) {
			$result = '';
			if ( ! empty( $matches ) ) {
				if ( ! empty( $matches[1] ) ) {
					if ( function_exists( 'get_field' ) ) {
						$result = get_field( $matches[1] );
					}
					if ( empty( $result ) ) {
						global $post;
						if ( ! empty( $post ) ) {
							$result = get_post_meta( $post->ID, $matches[1], true );
						}
					}
				} else {
					$result = $matches[0];
				}
			}
			// TODO Investigate changing to `wp_strip_all_tags()`.
			$result = strip_tags( $result ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags

			return $result;
		}

		/**
		 * Get Child Blogs
		 *
		 * Returns child blogs of parent in a multisite.
		 *
		 * @since ?
		 */
		public function get_child_blogs() {
			global $wpdb, $blog_id;
			$site_id = $wpdb->siteid;
			if ( is_multisite() ) {
				if ( (int) $site_id !== (int) $blog_id ) {
					return false;
				}

				// TODO Usage of a direct database call is discouraged.
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
				// TODO Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete().
				// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
				// TODO Use placeholders and $wpdb->prepare(); found interpolated variable $blog_id.
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				return $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = {$blog_id} AND site_id != blog_id" );
				// @phpcs:enable
			}

			return false;
		}

		/**
		 * Is AIOSEOP Active on Blog
		 *
		 * Checks if the plugin is active on a given blog by blogid on a multisite.
		 *
		 * @since ?
		 *
		 * @param bool $bid The Blog ID.
		 * @return bool
		 */
		public function is_aioseop_active_on_blog( $bid = false ) {
			global $blog_id;
			// TODO Use strict comparisons.
			if ( empty( $bid ) || ( $bid == $blog_id ) || ! is_multisite() ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				return true;
			}
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active_for_network( AIOSEOP_PLUGIN_BASENAME ) ) {
				return true;
			}

			// TODO Not using strict comparison for in_array; supply true for third argument.
			return in_array( AIOSEOP_PLUGIN_BASENAME, (array) get_blog_option( $bid, 'active_plugins', array() ) ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		}

		/**
		 * Quote List for Regex
		 *
		 * @since ?
		 *
		 * @param array  $list The botlist or referlist.
		 * @param string $quote Optional. Delimeter.
		 * @return string
		 */
		public function quote_list_for_regex( $list, $quote = '/' ) {
			$regex = '';
			$cont  = 0;
			foreach ( $list as $l ) {
				$trim_l = trim( $l );
				if ( ! empty( $trim_l ) ) {
					if ( $cont ) {
						$regex .= '|';
					}
					$cont   = 1;
					$regex .= preg_quote( trim( $l ), $quote );
				}
			}

			return $regex;
		}

		/**
		 * Is Good Bot
		 *
		 * Original code thanks to Sean M. Brown.
		 *
		 * @since ?
		 *
		 * @link http://smbrown.wordpress.com/2009/04/29/verify-googlebot-forward-reverse-dns/
		 *
		 * @return bool
		 */
		public function is_good_bot() {
			$botlist = array(
				'Yahoo! Slurp' => 'crawl.yahoo.net',
				'googlebot'    => '.googlebot.com',
				'msnbot'       => 'search.msn.com',
			);
			$botlist = apply_filters( $this->prefix . 'botlist', $botlist );
			if ( ! empty( $botlist ) ) {
				if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
					return false;
				}
				// TODO Detected usage of a non-sanitized input variable: $_SERVER.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// TODO Detected usage of a non-validated input variable: $_SERVER.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				// TODO Missing wp_unslash() before sanitization.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$ua  = $_SERVER['HTTP_USER_AGENT'];
				$uas = $this->quote_list_for_regex( $botlist );
				if ( preg_match( '/' . $uas . '/i', $ua ) ) {
					$ip             = $_SERVER['REMOTE_ADDR'];
					$hostname       = gethostbyaddr( $ip );
					$ip_by_hostname = gethostbyname( $hostname );
					if ( $ip_by_hostname === $ip ) {
						$hosts = array_values( $botlist );
						foreach ( $hosts as $k => $h ) {
							$hosts[ $k ] = preg_quote( $h, null ) . '$';
						}
						$hosts = join( '|', $hosts );
						if ( preg_match( '/' . $hosts . '/i', $hostname ) ) {
							return true;
						}
					}
				}
				// phpcs:enable

				return false;
			}

			return false;
		}

		/**
		 * Default Bad Bots
		 *
		 * @since ?
		 *
		 * @return array
		 */
		public function default_bad_bots() {
			$botlist = array(
				'Abonti',
				'aggregator',
				'AhrefsBot',
				'asterias',
				'BDCbot',
				'BLEXBot',
				'BuiltBotTough',
				'Bullseye',
				'BunnySlippers',
				'ca-crawler',
				'CCBot',
				'Cegbfeieh',
				'CheeseBot',
				'CherryPicker',
				'CopyRightCheck',
				'cosmos',
				'Crescent',
				'discobot',
				'DittoSpyder',
				'DotBot',
				'Download Ninja',
				'EasouSpider',
				'EmailCollector',
				'EmailSiphon',
				'EmailWolf',
				'EroCrawler',
				'Exabot',
				'ExtractorPro',
				'Fasterfox',
				'FeedBooster',
				'Foobot',
				'Genieo',
				'grub-client',
				'Harvest',
				'hloader',
				'httplib',
				'HTTrack',
				'humanlinks',
				'ieautodiscovery',
				'InfoNaviRobot',
				'IstellaBot',
				'Java/1.',
				'JennyBot',
				'k2spider',
				'Kenjin Spider',
				'Keyword Density/0.9',
				'larbin',
				'LexiBot',
				'libWeb',
				'libwww',
				'LinkextractorPro',
				'linko',
				'LinkScan/8.1a Unix',
				'LinkWalker',
				'LNSpiderguy',
				'lwp-trivial',
				'magpie',
				'Mata Hari',
				'MaxPointCrawler',
				'MegaIndex',
				'Microsoft URL Control',
				'MIIxpc',
				'Mippin',
				'Missigua Locator',
				'Mister PiX',
				'MJ12bot',
				'moget',
				'MSIECrawler',
				'NetAnts',
				'NICErsPRO',
				'Niki-Bot',
				'NPBot',
				'Nutch',
				'Offline Explorer',
				'Openfind',
				'panscient.com',
				'PHP/5.{',
				'ProPowerBot/2.14',
				'ProWebWalker',
				'Python-urllib',
				'QueryN Metasearch',
				'RepoMonkey',
				'SISTRIX',
				'sitecheck.Internetseer.com',
				'SiteSnagger',
				'SnapPreviewBot',
				'Sogou',
				'SpankBot',
				'spanner',
				'spbot',
				'Spinn3r',
				'suzuran',
				'Szukacz/1.4',
				'Teleport',
				'Telesoft',
				'The Intraformant',
				'TheNomad',
				'TightTwatBot',
				'Titan',
				'toCrawl/UrlDispatcher',
				'True_Robot',
				'turingos',
				'TurnitinBot',
				'UbiCrawler',
				'UnisterBot',
				'URLy Warning',
				'VCI',
				'WBSearchBot',
				'Web Downloader/6.9',
				'Web Image Collector',
				'WebAuto',
				'WebBandit',
				'WebCopier',
				'WebEnhancer',
				'WebmasterWorldForumBot',
				'WebReaper',
				'WebSauger',
				'Website Quester',
				'Webster Pro',
				'WebStripper',
				'WebZip',
				'Wotbox',
				'wsr-agent',
				'WWW-Collector-E',
				'Xenu',
				'Zao',
				'Zeus',
				'ZyBORG',
				'coccoc',
				'Incutio',
				'lmspider',
				'memoryBot',
				'serf',
				'Unknown',
				'uptime files',
			);

			return $botlist;
		}

		/**
		 * Is Bad Bot
		 *
		 * @since ?
		 *
		 * @return bool
		 */
		public function is_bad_bot() {
			$botlist = $this->default_bad_bots();
			$botlist = apply_filters( $this->prefix . 'badbotlist', $botlist );
			if ( ! empty( $botlist ) ) {
				if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
					return false;
				}
				// TODO Detected usage of a non-sanitized input variable: $_SERVER.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// TODO Missing wp_unslash() before sanitization.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$ua  = $_SERVER['HTTP_USER_AGENT'];
				$uas = $this->quote_list_for_regex( $botlist );
				// phpcs:enable
				if ( preg_match( '/' . $uas . '/i', $ua ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Default Bad Referers
		 *
		 * @since ?
		 *
		 * @todo Move to All_in_One_SEO_Pack_Bad_Robots class.
		 *
		 * @return array
		 */
		public function default_bad_referers() {
			$referlist = array(
				'semalt.com',
				'kambasoft.com',
				'savetubevideo.com',
				'buttons-for-website.com',
				'sharebutton.net',
				'soundfrost.org',
				'srecorder.com',
				'softomix.com',
				'softomix.net',
				'myprintscreen.com',
				'joinandplay.me',
				'fbfreegifts.com',
				'openmediasoft.com',
				'zazagames.org',
				'extener.org',
				'openfrost.com',
				'openfrost.net',
				'googlsucks.com',
				'best-seo-offer.com',
				'buttons-for-your-website.com',
				'www.Get-Free-Traffic-Now.com',
				'best-seo-solution.com',
				'buy-cheap-online.info',
				'site3.free-share-buttons.com',
				'webmaster-traffic.com',
			);

			return $referlist;
		}

		/**
		 * Is Bad Referer
		 *
		 * @since ?
		 *
		 * @todo Move to All_in_One_SEO_Pack_Bad_Robots class.
		 *
		 * @return bool
		 */
		public function is_bad_referer() {
			$referlist = $this->default_bad_referers();
			$referlist = apply_filters( $this->prefix . 'badreferlist', $referlist );

			if ( ! empty( $referlist ) && ! empty( $_SERVER ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
				// TODO Detected usage of a non-sanitized input variable: $_SERVER.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// TODO Missing wp_unslash() before sanitization.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$ref   = $_SERVER['HTTP_REFERER'];
				$regex = $this->quote_list_for_regex( $referlist );
				// phpcs:enable
				if ( preg_match( '/' . $regex . '/i', $ref ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Allow Bot
		 *
		 * @since ?
		 *
		 * @return mixed|void
		 */
		public function allow_bot() {
			$allow_bot = true;
			if ( ( ! $this->is_good_bot() ) && $this->is_bad_bot() && ! is_user_logged_in() ) {
				$allow_bot = false;
			}

			// TODO Remove "return", since no value is returned from `$this->display_settings_page()`.
			return apply_filters( $this->prefix . 'allow_bot', $allow_bot );
		}

		/**
		 * Display Tabs
		 *
		 * Displays tabs for tabbed locations on a settings page.
		 *
		 * @since ?
		 *
		 * @param string $location ?.
		 */
		public function display_tabs( $location ) {
			// TODO BUG? There is a variable that has not been initialized.
			if ( ( null !== $location ) && isset( $locations[ $location ]['tabs'] ) ) {
				// TODO Investigate/Remove undefined varible.
				$tabs = $locations['location']['tabs'];
			} else {
				$tabs = $this->tabs;
			}
			if ( ! empty( $tabs ) ) {
				?>
				<div class="aioseop_tabs_div"><label class="aioseop_head_nav">
						<?php
						foreach ( $tabs as $k => $v ) {
							?>
							<a class="aioseop_head_nav_tab aioseop_head_nav_
							<?php
							if ( $this->current_tab !== $k ) {
								echo 'in';
							}
							// TODO All output should be run through an escaping function.
							// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							active"
								href="<?php echo esc_url( add_query_arg( 'tab', $k ) ); ?>"><?php echo $v['name']; ?></a>
							<?php
							// phpcs:enable
						}
						?>
					</label></div>
				<?php
			}
		}

		/**
		 * Get Object Labels
		 *
		 * @since ?
		 *
		 * @param array $post_objs A list of post names,taxonomies, or objects.
		 * @return array
		 */
		public function get_object_labels( $post_objs ) {
			$pt         = array_keys( $post_objs );
			$post_types = array();
			foreach ( $pt as $p ) {
				if ( ! empty( $post_objs[ $p ]->label ) ) {
					$post_types[ $p ] = $post_objs[ $p ]->label;
				} else {
					$post_types[ $p ] = $p;
				}
			}

			return $post_types;
		}

		/**
		 * Get Term Labels
		 *
		 * @since ?
		 *
		 * @param array $post_objs List of categories.
		 * @return array
		 */
		public function get_term_labels( $post_objs ) {
			$post_types = array();
			foreach ( $post_objs as $p ) {
				if ( ! empty( $p->name ) ) {
					$post_types[ $p->term_id ] = $p->name;
				}
			}

			return $post_types;
		}

		/**
		 * Get Post Type Titles
		 *
		 * @since ?
		 *
		 * @param array $args Query args for get_post_types().
		 * @return array
		 */
		public function get_post_type_titles( $args = array() ) {
			return $this->get_object_labels( get_post_types( $args, 'objects' ) );
		}

		/**
		 * Get Taxonomy Titles
		 *
		 * @since ?
		 *
		 * @param array $args Query args for get_taxonomies().
		 * @return array
		 */
		public function get_taxonomy_titles( $args = array() ) {
			return $this->get_object_labels( get_taxonomies( $args, 'objects' ) );
		}

		/**
		 * Get Category Title
		 *
		 * @since ?
		 *
		 * @param array $args Query args for get_categories().
		 * @return array
		 */
		public function get_category_titles( $args = array() ) {
			return $this->get_term_labels( get_categories( $args ) );
		}

		/**
		 * Post Data Export
		 *
		 * Helper function for exporting settings on post data.
		 *
		 * @since ?
		 *
		 * @param string $prefix Optional. Custom Field prefix.
		 * @param array  $query  Optional. Query args.
		 * @return string
		 */
		public function post_data_export( $prefix = '_aioseop', $query = array( 'posts_per_page' => - 1 ) ) {
			$buf         = '';
			$posts_query = new WP_Query( $query );
			while ( $posts_query->have_posts() ) {
				$posts_query->the_post();
				global $post;
				$guid               = $post->guid;
				$type               = $post->post_type;
				$title              = $post->post_title;
				$date               = $post->post_date;
				$data               = '';
				$post_custom_fields = get_post_custom( $post->ID );
				$has_data           = null;

				if ( is_array( $post_custom_fields ) ) {
					foreach ( $post_custom_fields as $field_name => $field ) {
						if ( ( $this->strpos( $field_name, $prefix ) === 0 ) && $field[0] ) {
							$has_data = true;

							$data .= $field_name . " = '" . $field[0] . "'\n";
						}
					}
				}
				if ( ! empty( $data ) ) {
					$has_data = true;
				}

				if ( null !== $has_data ) {
					$post_info  = "\n[post_data]\n\n";
					$post_info .= "post_title = '" . $title . "'\n";
					$post_info .= "post_guid = '" . $guid . "'\n";
					$post_info .= "post_date = '" . $date . "'\n";
					$post_info .= "post_type = '" . $type . "'\n";
					if ( $data ) {
						$buf .= $post_info . $data . "\n";
					}
				}
			}
			wp_reset_postdata();

			return $buf;
		}

		/**
		 * Settings Export
		 *
		 * Handles exporting settings data for a module.
		 *
		 * @since 2.4.13 Fixed bug on empty options.
		 *
		 * @param string $buf The buffer.
		 * @return string
		 */
		public function settings_export( $buf ) {
			global $aiosp;
			$post_types       = apply_filters( 'aioseop_export_settings_exporter_post_types', null );
			$has_data         = null;
			$general_settings = null;
			$exporter_choices = apply_filters( 'aioseop_export_settings_exporter_choices', '' );
			// TODO Processing form data without nonce verification.
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			// TODO Detected usage of a non-sanitized input variable: $_SERVER.
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// TODO Missing wp_unslash() before sanitization.
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			if ( ! empty( $_REQUEST['aiosp_importer_exporter_export_choices'] ) ) {
				$exporter_choices = $_REQUEST['aiosp_importer_exporter_export_choices'];
			}
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			if ( ! empty( $exporter_choices ) && is_array( $exporter_choices ) ) {
				foreach ( $exporter_choices as $ex ) {
					if ( 1 === intval( $ex ) ) {
						$general_settings = true;
					}
					if ( 2 === intval( $ex ) && isset( $_REQUEST['aiosp_importer_exporter_export_post_types'] ) ) {
						// TODO Detected usage of a non-sanitized input variable: $_SERVER.
						// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						// TODO Missing wp_unslash() before sanitization.
						// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
						$post_types = $_REQUEST['aiosp_importer_exporter_export_post_types'];
						// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					}
				}
			}
			// phpcs:enable

			if ( ( null !== $post_types ) && ( $this === $aiosp ) ) {
				$buf .= $this->post_data_export(
					'_aioseop',
					array(
						'posts_per_page' => - 1,
						'post_type'      => $post_types,
						'post_status'    => array(
							'publish',
							'pending',
							'draft',
							'future',
							'private',
							'inherit',
						),
					)
				);
			}

			/* Add all active settings to settings file */
			$name    = $this->get_option_name();
			$options = $this->get_class_option();
			if ( ! empty( $options ) && null !== $general_settings ) {
				$buf .= "\n[$name]\n\n";
				foreach ( $options as $key => $value ) {
					if ( ( $name === $this->parent_option ) && ( 'modules' === $key ) ) {
						continue;
					} // don't re-export all module settings -- pdb
					if ( is_array( $value ) ) {
						// TODO Serialized data has known vulnerability problems with Object Injection.
						// JSON is generally a better approach for serializing data. See https://www.owasp.org/index.php/PHP_Object_Injection.
						// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
						$value = "'" . str_replace(
							array( "'", "\n", "\r" ),
							array(
								"\'",
								"\n",
								"\r",
							),
							trim( serialize( $value ) )
						) . "'";
						// phpcs:enable
					} else {
						// TODO  var_export() found. Debug code should not normally be used in production.
						// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
						$value = str_replace(
							array(
								"\n",
								"\r",
							),
							array(
								'\n',
								'\r',
							),
							trim( var_export( $value, true ) )
						);
						// phpcs:enable
					}
					$buf .= "$key = $value\n";
				}
			}

			return $buf;
		}

		/**
		 * Menu Order
		 *
		 * Order for adding the menus for the aioseop_modules_add_menus hook.
		 *
		 * @since ?
		 */
		public function menu_order() {
			return 10;
		}

		/**
		 * Output Error
		 *
		 * Print a basic error message.
		 *
		 * @since ?
		 *
		 * @param string $error The error message to display.
		 * @return bool
		 */
		public function output_error( $error ) {
			// TODO All output should be run through an escaping function.
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "<div class='aioseop_module error'>$error</div>";
			// phpcs:enable

			return false;
		}

		/**
		 * String Get CSV
		 *
		 * Backwards compatibility - see http://php.net/manual/en/function.str-getcsv.php
		 *
		 * @since ?
		 *
		 * @param string $input     ?.
		 * @param string $delimiter ?.
		 * @param string $enclosure ?.
		 * @param string $escape    ?.
		 *
		 * @return array
		 */
		public function str_getcsv( $input, $delimiter = ',', $enclosure = '"', $escape = '\\' ) {
			// TODO File operations should use WP_Filesystem methods instead of direct PHP filesystem calls.
			// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fopen
			$fp = fopen( 'php://memory', 'r+' );
			fputs( $fp, $input );
			rewind( $fp );
			$data = fgetcsv( $fp, null, $delimiter, $enclosure ); // $escape only got added in 5.3.0
			// TODO File operations should use WP_Filesystem methods instead of direct PHP filesystem calls.
			// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fclose
			fclose( $fp );
			// phpcs:enable

			return $data;
		}

		/**
		 * CSV to Array
		 *
		 * Helper function to convert csv in key/value pair format to an associative array.
		 *
		 * @since ?
		 *
		 * @deprecated Appears to be unused.
		 *
		 * @param string $csv CSV content.
		 * @return array
		 */
		public function csv_to_array( $csv ) {
			$args = array();
			if ( ! function_exists( 'str_getcsv' ) ) {
				$v = $this->str_getcsv( $csv );
			} else {
				$v = str_getcsv( $csv );
			}
			$size = count( $v );
			if ( is_array( $v ) && isset( $v[0] ) && $size >= 2 ) {
				for ( $i = 0; $i < $size; $i += 2 ) {
					$args[ $v[ $i ] ] = $v[ $i + 1 ];
				}
			}

			return $args;
		}

		/**
		 * Use WP Filesystem
		 *
		 * Allow modules to use WP Filesystem if available and desired, fall back to PHP filesystem access otherwise.
		 *
		 * @since ?
		 *
		 * @todo Change $form_fields to be an array|null.
		 *
		 * @param string $method      (Optional) Chosen type of filesystem.
		 * @param bool   $form_fields (Optional) Extra POST fields to be checked for inclusion in the post.
		 * @param string $url         (Optional) The URL to post the form to.
		 * @param bool   $error       (Optional) Whether the current request has failed to connect.
		 * @return bool
		 */
		public function use_wp_filesystem( $method = '', $form_fields = false, $url = '', $error = false ) {
			if ( empty( $method ) ) {
				$this->credentials = request_filesystem_credentials( $url );
			} else {
				// TODO $form_fields needs to be array|null.
				$this->credentials = request_filesystem_credentials( $url, $method, $error, false, $form_fields );
			}

			return $this->credentials;
		}

		/**
		 * Get Filesystem Object
		 *
		 * Wrapper function to get filesystem object.
		 *
		 * @since ?
		 */
		public function get_filesystem_object() {
			$cred = get_transient( 'aioseop_fs_credentials' );
			if ( ! empty( $cred ) ) {
				$this->credentials = $cred;
			}

			if ( function_exists( 'WP_Filesystem' ) && WP_Filesystem( $this->credentials ) ) {
				global $wp_filesystem;

				return $wp_filesystem;
			} else {
				require_once ABSPATH . 'wp-admin/includes/template.php';
				require_once ABSPATH . 'wp-admin/includes/screen.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';

				if ( ! WP_Filesystem( $this->credentials ) ) {
					$this->use_wp_filesystem();
				}

				if ( ! empty( $this->credentials ) ) {
					set_transient( 'aioseop_fs_credentials', $this->credentials, 10800 );
				}
				global $wp_filesystem;
				if ( is_object( $wp_filesystem ) ) {
					return $wp_filesystem;
				}
			}

			return false;
		}

		/**
		 * File Exists
		 *
		 * See if a file exists using WP Filesystem.
		 *
		 * @since ?
		 *
		 * @uses WP_Filesystem_Direct::exists()
		 * @link https://developer.wordpress.org/reference/classes/wp_filesystem_direct/exists/
		 *
		 * @param string $filename The name of the file.
		 * @return bool
		 */
		public function file_exists( $filename ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				return $wpfs->exists( $filename );
			}

			return $wpfs;
		}

		/**
		 * Is File
		 *
		 * See if the directory entry is a file using WP Filesystem.
		 *
		 * @since ?
		 *
		 * @uses WP_Filesystem_Direct::is_file()
		 * @link https://developer.wordpress.org/reference/classes/wp_filesystem_direct/is_file/
		 *
		 * @param string $filename File path.
		 * @return bool
		 */
		public function is_file( $filename ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				return $wpfs->is_file( $filename );
			}

			return $wpfs;
		}

		/**
		 * Scan Directory
		 *
		 * List files in a directory using WP Filesystem.
		 *
		 * @since ?
		 *
		 * @uses WP_Filesystem_Direct::dirlist()
		 * @link https://developer.wordpress.org/reference/classes/wp_filesystem_direct/dirlist/
		 *
		 * @param string $path File path.
		 * @return array|bool
		 */
		public function scandir( $path ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				$dirlist = $wpfs->dirlist( $path );
				if ( empty( $dirlist ) ) {
					return $dirlist;
				}

				return array_keys( $dirlist );
			}

			return $wpfs;
		}

		/**
		 * Load File
		 *
		 * Load a file through WP Filesystem; implement basic support for offset and maxlen.
		 *
		 * @todo Remove unused params.
		 *
		 * @since ?
		 *
		 * @uses WP_Filesystem_Direct::get_contents()
		 * @link https://developer.wordpress.org/reference/classes/wp_filesystem_direct/get_contents/
		 *
		 * @param string $filename         Name of the file to read.
		 * @param bool   $use_include_path ?.
		 * @param null   $context          ?.
		 * @param int    $offset           Amount to offset substr.
		 * @param int    $maxlen           Max length of substr.
		 *
		 * @return bool|mixed
		 */
		public function load_file( $filename, $use_include_path = false, $context = null, $offset = - 1, $maxlen = - 1 ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				if ( ! $wpfs->exists( $filename ) ) {
					return false;
				}
				if ( ( $offset > 0 ) || ( $maxlen >= 0 ) ) {
					if ( 0 === $maxlen ) {
						return '';
					}
					if ( $offset < 0 ) {
						$offset = 0;
					}
					$file = $wpfs->get_contents( $filename );
					if ( ! is_string( $file ) || empty( $file ) ) {
						return $file;
					}
					if ( $maxlen < 0 ) {
						return $this->substr( $file, $offset );
					} else {
						return $this->substr( $file, $offset, $maxlen );
					}
				} else {
					return $wpfs->get_contents( $filename );
				}
			}

			return false;
		}

		/**
		 * Save File
		 *
		 * Save a file through WP Filesystem.
		 *
		 * @since ?
		 *
		 * @param string $filename Name of file to search and read.
		 * @param null   $contents ?.
		 * @return bool
		 */
		public function save_file( $filename, $contents ) {
			/* Translators: Name of file. */
			$failed_str = sprintf( __( "Failed to write file %s!\n", 'all-in-one-seo-pack' ), $filename );
			/* Translators: Name of file. */
			$readonly_str = sprintf( __( "File %s isn't writable!\n", 'all-in-one-seo-pack' ), $filename );
			$wpfs         = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				$file_exists = $wpfs->exists( $filename );
				if ( ! $file_exists || $wpfs->is_writable( $filename ) ) {
					if ( $wpfs->put_contents( $filename, $contents ) === false ) {
						return $this->output_error( $failed_str );
					}
				} else {
					return $this->output_error( $readonly_str );
				}

				return true;
			}

			return false;
		}

		/**
		 * Delete a file through WP Filesystem.
		 *
		 * @since ?
		 *
		 * @param string $filename Name of file to search and delete.
		 * @return bool
		 */
		public function delete_file( $filename ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				if ( $wpfs->exists( $filename ) ) {
					if ( $wpfs->delete( $filename ) === false ) {
						/* Translators: Name of file. */
						$this->output_error( sprintf( __( "Failed to delete file %s!\n", 'all-in-one-seo-pack' ), $filename ) );
					} else {
						return true;
					}
				} else {
					/* Translators: Name of file. */
					$this->output_error( sprintf( __( "File %s doesn't exist!\n", 'all-in-one-seo-pack' ), $filename ) );
				}
			}

			return false;
		}

		/**
		 * Rename file
		 *
		 * Rename a file through WP Filesystem.
		 *
		 * @since ?
		 *
		 * @param string $filename Name of file to search and rename/move.
		 * @param string $newname  New name of file.
		 * @return bool
		 */
		public function rename_file( $filename, $newname ) {
			$wpfs = $this->get_filesystem_object();
			if ( is_object( $wpfs ) ) {
				$file_exists    = $wpfs->exists( $filename );
				$newfile_exists = $wpfs->exists( $newname );
				if ( $file_exists && ! $newfile_exists ) {
					if ( $wpfs->move( $filename, $newname ) === false ) {
						/* Translators: Name of file. */
						$this->output_error( sprintf( __( "Failed to rename file %s!\n", 'all-in-one-seo-pack' ), $filename ) );
					} else {
						return true;
					}
				} else {
					if ( ! $file_exists ) {
						/* Translators: Name of file. */
						$this->output_error( sprintf( __( "File %s doesn't exist!\n", 'all-in-one-seo-pack' ), $filename ) );
					} elseif ( $newfile_exists ) {
						/* Translators: Name of file. */
						$this->output_error( sprintf( __( "File %s already exists!\n", 'all-in-one-seo-pack' ), $newname ) );
					}
				}
			}

			return false;
		}

		/**
		 * Load Files
		 *
		 * Load multiple files.
		 *
		 * @since ?
		 *
		 * @param array  $options AIOSEOP options.
		 * @param array  $opts    ?.
		 * @param string $prefix  AIOSEOP Module prefix.
		 * @return mixed
		 */
		public function load_files( $options, $opts, $prefix ) {
			foreach ( $opts as $opt => $file ) {
				$opt      = $prefix . $opt;
				$file     = ABSPATH . $file;
				$contents = $this->load_file( $file );
				if ( false !== $contents ) {
					$options[ $opt ] = $contents;
				}
			}

			return $options;
		}

		/**
		 * Save Files
		 *
		 * Save multiple files.
		 *
		 * @since ?
		 *
		 * @param array  $opts   ?.
		 * @param string $prefix AIOSEOP Module prefix.
		 */
		public function save_files( $opts, $prefix ) {
			foreach ( $opts as $opt => $file ) {
				$opt = $prefix . $opt;
				// TODO Processing form data without nonce verification.
				// phpcs:disable WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST[ $opt ] ) ) {
					// TODO Detected usage of a non-sanitized input variable: $_POST.
					// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					// TODO Missing wp_unslash() before sanitization.
					// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					$output = stripslashes_deep( $_POST[ $opt ] );
					$file   = ABSPATH . $file;
					$this->save_file( $file, $output );
				}
				// phpcs:enable
			}
		}

		/**
		 * Delete Files
		 *
		 * Delete multiple files.
		 *
		 * @since ?
		 *
		 * @param array $opts ?.
		 */
		public function delete_files( $opts ) {
			foreach ( $opts as $opt => $file ) {
				$file = ABSPATH . $file;
				$this->delete_file( $file );
			}
		}

		/**
		 * Get All Images by Type
		 *
		 * Returns available social seo images.
		 *
		 * @since 2.4 #1079 Fixes array_flip warning on opengraph module.
		 *
		 * @param array  $options Plugin/module options.
		 * @param object $p       Post.
		 * @return array
		 */
		public function get_all_images_by_type( $options = null, $p = null ) {
			$img = array();
			if ( empty( $img ) ) {
				$size = apply_filters( 'post_thumbnail_size', 'large' );

				global $aioseop_options, $wp_query, $aioseop_opengraph;

				if ( null === $p ) {
					global $post;
				} else {
					// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
					// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
					$post = $p;
					// phpcs:enable
				}

				$count = 1;

				if ( ! empty( $post ) ) {
					if ( ! is_object( $post ) ) {
						// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
						// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
						$post = get_post( $post );
						// phpcs:enable
					}
					if ( is_object( $post ) && function_exists( 'get_post_thumbnail_id' ) ) {
						if ( 'attachment' === $post->post_type ) {
							$post_thumbnail_id = $post->ID;
						} else {
							$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
						}
						if ( ! empty( $post_thumbnail_id ) ) {
							$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );
							if ( is_array( $image ) ) {
								$img[] = array(
									'type' => 'featured',
									'id'   => $post_thumbnail_id,
									'link' => $image[0],
								);
							}
						}
					}

					$post_id = $post->ID;
					$p       = $post;
					$w       = $wp_query;

					$meta_key = '';
					if ( is_array( $options ) && isset( $options['meta_key'] ) ) {
						$meta_key = $options['meta_key'];
					}

					if ( ! empty( $meta_key ) && ! empty( $post ) ) {
						$image = $this->get_the_image_by_meta_key(
							array(
								'post_id'  => $post->ID,
								// TODO Detected usage of meta_key, possible slow query.
								// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
								'meta_key' => explode( ',', $meta_key ),
								// phpcs:enable
							)
						);
						if ( ! empty( $image ) ) {
							$img[] = array(
								'type' => 'meta_key',
								'id'   => $meta_key,
								'link' => $image,
							);
						}
					}

					// TODO Found: !=. Use strict comparisons (=== or !==).
					// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
					if ( '' != ! $post->post_modified_gmt ) {
						// TODO Overriding WordPress globals is prohibited. Found assignment to $wp_query.
						// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
						$wp_query = new WP_Query(
							array(
								'p'         => $post_id,
								'post_type' => $post->post_type,
							)
						);
					}
					// phpcs:enable
					if ( 'page' === $post->post_type ) {
						$wp_query->is_page = true;
					} elseif ( 'attachment' === $post->post_type ) {
						$wp_query->is_attachment = true;
					} else {
						$wp_query->is_single = true;
					}
					if ( 'page' === get_option( 'show_on_front' ) && intval( get_option( 'page_for_posts' ) ) === $post->ID ) {
						$wp_query->is_home = true;
					}
					$args['options']['type']   = 'html';
					$args['options']['nowrap'] = false;
					$args['options']['save']   = false;
					$wp_query->queried_object  = $post;

					$attachments = get_children(
						array(
							'post_parent'    => $post->ID,
							'post_status'    => 'inherit',
							'post_type'      => 'attachment',
							'post_mime_type' => 'image',
							'order'          => 'ASC',
							'orderby'        => 'menu_order ID',
						)
					);
					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $id => $attachment ) {
							$image = wp_get_attachment_image_src( $id, $size );
							if ( is_array( $image ) ) {
								$img[] = array(
									'type' => 'attachment',
									'id'   => $id,
									'link' => $image[0],
								);
							}
						}
					}
					$matches = array();
					preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $post->ID ), $matches );
					if ( isset( $matches ) && ! empty( $matches[1] ) && ! empty( $matches[1][0] ) ) {
						foreach ( $matches[1] as $i => $m ) {
							$img[] = array(
								'type' => 'post_content',
								'id'   => 'post' . ( $count++ ),
								'link' => $m,
							);
						}
					}
					wp_reset_postdata();
					// TODO Overriding WordPress globals is prohibited. Found assignment to $wp_query.
					// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
					$wp_query = $w;
					$post     = $p;
					// phpcs:enable
				}
			}

			return $img;
		}

		/**
		 * Get All Images
		 *
		 * @since ?
		 *
		 * @param array|null $options Settings from AIOSEOP options.
		 * @param null       $p       ?.
		 * @return array
		 */
		public function get_all_images( $options = null, $p = null ) {
			$img    = $this->get_all_images_by_type( $options, $p );
			$legacy = array();
			foreach ( $img as $k => $v ) {
				$v['link'] = set_url_scheme( $v['link'] );
				if ( 'featured' === $v['type'] ) {
					$legacy[ $v['link'] ] = 1;
				} else {
					$legacy[ $v['link'] ] = $v['id'];
				}
			}

			return $legacy;
		}

		/**
		 * Thanks to Justin Tadlock for the original get-the-image code - http://themehybrid.com/plugins/get-the-image **
		 *
		 * @param null $options Settings.
		 * @param null $p       Post object. Appears unused.
		 *
		 * @return bool|mixed|string
		 */
		public function get_the_image( $options = null, $p = null ) {

			if ( null === $p ) {
				global $post;
			} else {
				// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
				// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
				$post = $p;
				// phpcs:enable
			}

			$meta_key = '';
			if ( is_array( $options ) && isset( $options['meta_key'] ) ) {
				$meta_key = $options['meta_key'];
			}

			if ( ! empty( $meta_key ) && ! empty( $post ) ) {
				$meta_key = explode( ',', $meta_key );
				$image    = $this->get_the_image_by_meta_key(
					array(
						'post_id'  => $post->ID,
						// TODO Detected usage of meta_key, possible slow query.
						// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_key' => $meta_key,
						// phpcs:enable
					)
				);
			}
			if ( empty( $image ) ) {
				$image = $this->get_the_image_by_post_thumbnail( $post );
			}
			if ( empty( $image ) ) {
				$image = $this->get_the_image_by_attachment( $post );
			}
			if ( empty( $image ) ) {
				$image = $this->get_the_image_by_scan( $post );
			}
			if ( empty( $image ) ) {
				$image = $this->get_the_image_by_default( $post );
			}

			return $image;
		}

		/**
		 * Get the Image by Default
		 *
		 * @since ?
		 *
		 * @param WP_Post|null $p Post object.
		 * @return string
		 */
		public function get_the_image_by_default( $p = null ) {
			return '';
		}

		/**
		 * Get the Image by Meta Key
		 *
		 * @since ?
		 *
		 * @param array $args Post query args.
		 * @return bool|mixed
		 */
		public function get_the_image_by_meta_key( $args = array() ) {

			/* If $meta_key is not an array. */
			if ( ! is_array( $args['meta_key'] ) ) {
				// TODO Detected usage of meta_key, possible slow query.
				// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['meta_key'] = array( $args['meta_key'] );
				// phpcs:enable
			}

			/* Loop through each of the given meta keys. */
			foreach ( $args['meta_key'] as $meta_key ) {
				/* Get the image URL by the current meta key in the loop. */
				$image = get_post_meta( $args['post_id'], $meta_key, true );
				/* If a custom key value has been given for one of the keys, return the image URL. */
				if ( ! empty( $image ) ) {
					return $image;
				}
			}

			return false;
		}

		/**
		 * Get the Image by Post Thumbnail
		 *
		 * @since 2.4.13 Fixes when content is taxonomy.
		 *
		 * @param WP_Post|null $p Post object.
		 * @return bool
		 */
		public function get_the_image_by_post_thumbnail( $p = null ) {

			if ( null === $p ) {
				global $post;
			} else {
				// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
				// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
				$post = $p;
				// phpcs:enable
			}

			if ( is_category() || is_tag() || is_tax() ) {
				return false;
			}

			$post_thumbnail_id = null;
			if ( function_exists( 'get_post_thumbnail_id' ) ) {
				$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
			}

			if ( empty( $post_thumbnail_id ) ) {
				return false;
			}

			// Check if someone is using built-in WP filter.
			$size  = apply_filters( 'aioseop_thumbnail_size', apply_filters( 'post_thumbnail_size', 'large' ) );
			$image = wp_get_attachment_image_src( $post_thumbnail_id, $size );

			return $image[0];
		}

		/**
		 * Get the Image by Attachment
		 *
		 * @since ?
		 *
		 * @param null $p ?.
		 * @return bool
		 */
		public function get_the_image_by_attachment( $p = null ) {

			if ( null === $p ) {
				global $post;
			} else {
				// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
				// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
				$post = $p;
				// phpcs:enable
			}

			$attachments = get_children(
				array(
					'post_parent'    => $post->ID,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => 'ASC',
					'orderby'        => 'menu_order ID',
				)
			);

			if ( empty( $attachments ) && 'attachment' === get_post_type( $post->ID ) ) {
				$size  = apply_filters( 'aioseop_attachment_size', 'large' );
				$image = wp_get_attachment_image_src( $post->ID, $size );
			}

			/* If no attachments or image is found, return false. */
			if ( empty( $attachments ) && empty( $image ) ) {
				return false;
			}

			/* Set the default iterator to 0. */
			$i = 0;

			/* Loop through each attachment. Once the $order_of_image (default is '1') is reached, break the loop. */
			foreach ( $attachments as $id => $attachment ) {
				if ( 1 === ++$i ) {
					$size  = apply_filters( 'aioseop_attachment_size', 'large' );
					$image = wp_get_attachment_image_src( $id, $size );
					// TODO strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead.
					// phpcs:disable WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
					$alt = trim( strip_tags( get_post_field( 'post_excerpt', $id ) ) );
					// phpcs:enable
					break;
				}
			}

			/* Return the image URL. */

			return $image[0];

		}

		/**
		 * Get the Image by Scan
		 *
		 * @since ?
		 *
		 * @param WP_Post|null $p Post Object.
		 * @return bool
		 */
		public function get_the_image_by_scan( $p = null ) {
			if ( null === $p ) {
				global $post;
			} else {
				// TODO Overriding WordPress globals is prohibited. Found assignment to $post.
				// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
				$post = $p;
				// phpcs:enable
			}

			/* Search the post's content for the <img /> tag and get its URL. */
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $post->ID ), $matches );

			/* If there is a match for the image, return its URL. */
			if ( isset( $matches ) && ! empty( $matches[1][0] ) ) {
				return $matches[1][0];
			}

			return false;
		}


		/**
		 * Help Text Helper
		 *
		 * @since ?
		 *
		 * @param array  $default_options AIOSEOP option.
		 * @param array  $options         Defaul AIOSEOP option.
		 * @param string $help_link       URL link to documentation.
		 */
		public function help_text_helper( &$default_options, $options, $help_link = '' ) {
			foreach ( $options as $o ) {
				$ht = '';
				if ( ! empty( $this->help_text[ $o ] ) ) {
					$ht = $this->help_text[ $o ];
				} elseif ( ! empty( $default_options[ $o ]['help_text'] ) ) {
					$ht = $default_options[ $o ]['help_text'];
				}
				if ( $ht && ! is_array( $ht ) ) {
					$ha = '';
					$hl = $help_link;
					if ( 0 === strpos( $o, 'ga_' ) ) {
						// special case -- pdb.
						$hl = 'https://semperplugins.com/documentation/advanced-google-analytics-settings/';
					}
					if ( ! empty( $this->help_anchors[ $o ] ) ) {
						$ha = $this->help_anchors[ $o ];
					}
					$pos = strrpos( $hl, '#' );
					if ( ! empty( $ha ) && $pos ) {
						$hl = substr( $hl, 0, $pos );
					}
					if ( ! empty( $ha ) && ( 'h' === $ha[0] ) ) {
						$hl = '';
					}
					if ( ! empty( $ha ) || ! isset( $this->help_anchors[ $o ] ) ) {
						$ht .= "<br /><a href='" . $hl . $ha . "' target='_blank'>" . __( 'Click here for documentation on this setting', 'all-in-one-seo-pack' ) . '</a>';
					}
					$default_options[ $o ]['help_text'] = $ht;
				}
			}
		}

		/**
		 * Add Help Text Links
		 *
		 * @since ?
		 */
		public function add_help_text_links() {
			if ( ! empty( $this->help_text ) ) {
				foreach ( $this->layout as $k => $v ) {
					$this->help_text_helper( $this->default_options, $v['options'], $v['help_link'] );
				}
				if ( ! empty( $this->locations ) ) {
					foreach ( $this->locations as $k => $v ) {
						if ( ! empty( $v['default_options'] ) && ! empty( $v['options'] ) ) {
							$this->help_text_helper( $this->locations[ $k ]['default_options'], $v['options'], $v['help_link'] );
						}
					}
				}
			}
		}

		/**
		 * Load scripts and styles for metaboxes.
		 * edit-tags exists only for pre 4.5 support... remove when we drop 4.5 support.
		 * Also, that check and others should be pulled out into their own functions.
		 *
		 * @todo is it possible to migrate this to \All_in_One_SEO_Pack_Module::add_page_hooks? Or refactor? Both function about the same.
		 *
		 * @since 2.4.14 Added term as screen base.
		 */
		public function enqueue_metabox_scripts() {
			$screen = '';
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}
			$bail = false;
			if ( empty( $screen ) ) {
				$bail = true;
			}
			if ( true !== $bail ) {
				if ( ( 'post' !== $screen->base ) && ( 'term' !== $screen->base ) && ( 'edit-tags' !== $screen->base ) && ( 'toplevel_page_shopp-products' !== $screen->base ) ) {
					$bail = true;
				}
			}
			$prefix = $this->get_prefix();
			$bail   = apply_filters( $prefix . 'bail_on_enqueue', $bail, $screen );
			if ( $bail ) {
				return;
			}
			$this->form = 'post';
			if ( 'term' === $screen->base || 'edit-tags' === $screen->base ) {
				$this->form = 'edittag';
			}
			if ( 'toplevel_page_shopp-products' === $screen->base ) {
				$this->form = 'product';
			}
			$this->form = apply_filters( $prefix . 'set_form_on_enqueue', $this->form, $screen );
			foreach ( $this->locations as $k => $v ) {
				if ( 'metabox' === $v['type'] && isset( $v['display'] ) && ! empty( $v['display'] ) ) {
					$enqueue_scripts = false;
					$enqueue_scripts =
						(
							(
								( 'toplevel_page_shopp-products' === $screen->base )
								&& in_array( 'shopp_product', $v['display'], true )
							)
						)
						|| in_array( $screen->post_type, $v['display'], true )
						|| 'edit-category' === $screen->base
						|| 'edit-post_tag' === $screen->base
						|| 'term' === $screen->base;
					$enqueue_scripts = apply_filters( $prefix . 'enqueue_metabox_scripts', $enqueue_scripts, $screen, $v );
					if ( $enqueue_scripts ) {
						add_filter( 'aioseop_localize_script_data', array( $this, 'localize_script_data' ) );
						add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20 );
						add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 20 );
					}
				}
			}
		}

		/**
		 * Load styles for module.
		 *
		 * Add hook in \All_in_One_SEO_Pack_Module::enqueue_metabox_scripts - Bails adding hook if not on target valid screen.
		 * Add hook in \All_in_One_SEO_Pack_Module::add_page_hooks - Function itself is hooked based on the screen_id/page.
		 *
		 * @since 2.9
		 *
		 * @see 'admin_enqueue_scripts' hook
		 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
		 *
		 * @param string $hook_suffix Current WP Screen hook suffix.
		 */
		public function admin_enqueue_styles( $hook_suffix ) {
			wp_enqueue_style( 'thickbox' );
			if ( ! empty( $this->pointers ) ) {
				wp_enqueue_style( 'wp-pointer' );
			}
			wp_enqueue_style( 'aioseop-module-style', AIOSEOP_PLUGIN_URL . 'css/modules/aioseop_module.css', array(), AIOSEOP_VERSION );
			if ( function_exists( 'is_rtl' ) && is_rtl() ) {
				wp_enqueue_style( 'aioseop-module-style-rtl', AIOSEOP_PLUGIN_URL . 'css/modules/aioseop_module-rtl.css', array( 'aioseop-module-style' ), AIOSEOP_VERSION );
			}
		}

		/**
		 * Admin Enqueue Scripts
		 *
		 * Hook function to enqueue scripts and localize data to scripts.
		 *
		 * Add hook in \All_in_One_SEO_Pack_Module::enqueue_metabox_scripts - Bails adding hook if not on target valid screen.
		 * Add hook in \All_in_One_SEO_Pack_Module::add_page_hooks - Function itself is hooked based on the screen_id/page.
		 *
		 * @since ?
		 * @since 2.3.12.3 Add missing wp_enqueue_media.
		 * @since 2.9 Switch to admin_enqueue_scripts; both the hook and function name.
		 *
		 * @see 'admin_enqueue_scripts' hook
		 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
		 * @global WP_Post $post Used to set the post ID in wp_enqueue_media().
		 *
		 * @param string $hook_suffix Current WP Screen hook suffix.
		 */
		public function admin_enqueue_scripts( $hook_suffix ) {
			wp_enqueue_script( 'sack' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

			if ( ! empty( $this->pointers ) ) {
				// TODO In footer ($in_footer) is not set explicitly wp_enqueue_script;
				// It is recommended to load scripts in the footer. Please set this value to `true`
				// to load it in the footer, or explicitly `false` if it should be loaded in the header.
				// phpcs:disable WordPress.WP.EnqueuedResourceParameters.NotInFooter
				wp_enqueue_script(
					'wp-pointer',
					false,
					array( 'jquery' ),
					AIOSEOP_VERSION
				);
				// phpcs:enable
			}

			global $post;
			if ( ! empty( $post->ID ) ) {
				wp_enqueue_media( array( 'post' => $post->ID ) );
			} else {
				wp_enqueue_media();
			}

			// AIOSEOP Script enqueue.
			// TODO In footer ($in_footer) is not set explicitly wp_enqueue_script;
			// It is recommended to load scripts in the footer. Please set this value to `true`
			// to load it in the footer, or explicitly `false` if it should be loaded in the header.
			// phpcs:disable WordPress.WP.EnqueuedResourceParameters.NotInFooter
			wp_enqueue_script(
				'aioseop-module-script',
				AIOSEOP_PLUGIN_URL . 'js/modules/aioseop_module.js',
				array(),
				AIOSEOP_VERSION
			);
			// phpcs:enable

			// Localize aiosp_data in JS.
			if ( ! empty( $this->script_data ) ) {
				aioseop_localize_script_data();
			}
		}

		/**
		 * Localize Script Data
		 *
		 * @since ?
		 *
		 * @param array|string $data JS data to localize.
		 * @return array
		 */
		public function localize_script_data( $data ) {
			if ( ! is_array( $data ) ) {
				$data = array( 0 => $data );
			}
			if ( empty( $this->script_data ) ) {
				$this->script_data = array();
			}
			if ( ! empty( $this->pointers ) ) {
				$this->script_data['pointers'] = $this->pointers;
			}
			if ( empty( $data[0]['condshow'] ) ) {
				$data[0]['condshow'] = array();
			}
			if ( empty( $this->script_data['condshow'] ) ) {
				$this->script_data['condshow'] = array();
			}
			$condshow            = $this->script_data['condshow'];
			$data[0]['condshow'] = array_merge( $data[0]['condshow'], $condshow );
			unset( $this->script_data['condshow'] );
			$data[0]                       = array_merge( $this->script_data, $data[0] );
			$this->script_data['condshow'] = $condshow;

			return $data;
		}

		/**
		 * Settings Page Init
		 *
		 * Override this to run code at the beginning of the settings page.
		 *
		 * @since ?
		 */
		public function settings_page_init() {

		}

		/**
		 * Filter Pointers
		 *
		 * Filter out admin pointers that have already been clicked.
		 *
		 * @since ?
		 */
		public function filter_pointers() {
			if ( ! empty( $this->pointers ) ) {
				$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
				foreach ( $dismissed as $d ) {
					if ( isset( $this->pointers[ $d ] ) ) {
						unset( $this->pointers[ $d ] );
					}
				}
			}
		}

		/**
		 * Add Page Hooks
		 *
		 * Add basic hooks when on the module's page.
		 *
		 * @since ?
		 */
		public function add_page_hooks() {
			$hookname = current_filter();
			if ( $this->strpos( $hookname, 'load-' ) === 0 ) {
				$this->pagehook = $this->substr( $hookname, 5 );
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
			add_filter( 'aioseop_localize_script_data', array( $this, 'localize_script_data' ) );
			add_action( $this->prefix . 'settings_header', array( $this, 'display_tabs' ) );
		}

		/**
		 * Get Admin Links
		 *
		 * @since ?
		 *
		 * @return array
		 */
		public function get_admin_links() {
			if ( ! empty( $this->menu_name ) ) {
				$name = $this->menu_name;
			} else {
				$name = $this->name;
			}

			$hookname = plugin_basename( $this->file );

			$links = array();
			$url   = '';
			if ( function_exists( 'menu_page_url' ) ) {
				$url = menu_page_url( $hookname, 0 );
			}
			if ( empty( $url ) ) {
				$url = esc_url( admin_url( 'admin.php?page=' . $hookname ) );
			}

			if ( null === $this->locations ) {
				array_unshift(
					$links,
					array(
						'parent' => AIOSEOP_PLUGIN_DIRNAME,
						'title'  => $name,
						'id'     => $hookname,
						'href'   => $url,
						'order'  => $this->menu_order(),
					)
				);
			} else {
				foreach ( $this->locations as $k => $v ) {
					if ( 'settings' === $v['type'] ) {
						if ( 'default' === $k ) {
							array_unshift(
								$links,
								array(
									'parent' => AIOSEOP_PLUGIN_DIRNAME,
									'title'  => $name,
									'id'     => $hookname,
									'href'   => $url,
									'order'  => $this->menu_order(),
								)
							);
						} else {
							if ( ! empty( $v['menu_name'] ) ) {
								$name = $v['menu_name'];
							} else {
								$name = $v['name'];
							}
							array_unshift(
								$links,
								array(
									'parent' => AIOSEOP_PLUGIN_DIRNAME,
									'title'  => $name,
									'id'     => $this->get_prefix( $k ) . $k,
									'href'   => esc_url( admin_url( 'admin.php?page=' . $this->get_prefix( $k ) . $k ) ),
									'order'  => $this->menu_order(),
								)
							);
						}
					}
				}
			}

			return $links;
		}

		/**
		 * Add Admin Bar Submenu
		 *
		 * @since ?
		 *
		 * @global $aioseop_admin_menu
		 * @global $wp_admin_bar
		 */
		public function add_admin_bar_submenu() {
			global $aioseop_admin_menu, $wp_admin_bar;

			if ( $aioseop_admin_menu ) {
				$links = $this->get_admin_links();
				if ( ! empty( $links ) ) {
					foreach ( $links as $l ) {
						$wp_admin_bar->add_menu( $l );
					}
				}
			}
		}

		/**
		 * Filter Return Metaboxes
		 *
		 * Collect metabox data together for tabbed metaboxes.
		 *
		 * @since ?
		 *
		 * @param string array $args ?.
		 * @return array
		 */
		public function filter_return_metaboxes( $args ) {
			return array_merge( $args, $this->post_metaboxes );
		}

		/**
		 * Add Menu
		 *
		 * Add submenu for module, call page hooks, set up metaboxes.
		 *
		 * @since ?
		 *
		 * @param string $parent_slug The slug name for the parent menu (or the file name of a standard WordPress admin page).
		 * @return bool
		 */
		public function add_menu( $parent_slug ) {
			if ( ! empty( $this->menu_name ) ) {
				$name = $this->menu_name;
			} else {
				$name = $this->name;
			}
			if ( null === $this->locations ) {
				$hookname = add_submenu_page(
					$parent_slug,
					$name,
					$name,
					apply_filters( 'manage_aiosp', 'aiosp_manage_seo' ),
					plugin_basename( $this->file ),
					array(
						$this,
						'display_settings_page',
					)
				);
				add_action( "load-{$hookname}", array( $this, 'add_page_hooks' ) );

				return true;
			}
			foreach ( $this->locations as $k => $v ) {
				if ( 'settings' === $v['type'] ) {
					if ( 'default' === $k ) {
						if ( ! empty( $this->menu_name ) ) {
							$name = $this->menu_name;
						} else {
							$name = $this->name;
						}
						$hookname = add_submenu_page(
							$parent_slug,
							$name,
							$name,
							apply_filters( 'manage_aiosp', 'aiosp_manage_seo' ),
							plugin_basename( $this->file ),
							array(
								$this,
								'display_settings_page',
							)
						);
					} else {
						if ( ! empty( $v['menu_name'] ) ) {
							$name = $v['menu_name'];
						} else {
							$name = $v['name'];
						}
						$hookname = add_submenu_page(
							$parent_slug,
							$name,
							$name,
							apply_filters( 'manage_aiosp', 'aiosp_manage_seo' ),
							$this->get_prefix( $k ) . $k,
							array(
								$this,
								"display_settings_page_$k",
							)
						);
					}
					add_action( "load-{$hookname}", array( $this, 'add_page_hooks' ) );
				} elseif ( 'metabox' === $v['type'] ) {
					// hack -- make sure this runs anyhow, for now -- pdb.
					$this->setting_options( $k );
					$this->toggle_save_post_hooks( true );
					if ( isset( $v['display'] ) && ! empty( $v['display'] ) ) {
						add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_metabox_scripts' ), 5 );
						if ( $this->tabbed_metaboxes ) {
							add_filter( 'aioseop_add_post_metabox', array( $this, 'filter_return_metaboxes' ) );
						}
						foreach ( $v['display'] as $posttype ) {
							$v['location'] = $k;
							$v['posttype'] = $posttype;

							if ( post_type_exists( $posttype ) ) {
								// Metabox priority/context on edit post screen.
								$v['context']  = apply_filters( 'aioseop_post_metabox_context', 'normal' );
								$v['priority'] = apply_filters( 'aioseop_post_metabox_priority', 'high' );
							}
							if ( false !== strpos( $posttype, 'edit-' ) ) {
								// Metabox priority/context on edit taxonomy screen.
								$v['context']  = 'advanced';
								$v['priority'] = 'default';
							}

							// Metabox priority for everything else.
							if ( ! isset( $v['context'] ) ) {
								$v['context'] = 'advanced';
							}
							if ( ! isset( $v['priority'] ) ) {
								$v['priority'] = 'default';
							}

							if ( $this->tabbed_metaboxes ) {
								$this->post_metaboxes[] = array(
									'id'            => $v['prefix'] . $k,
									'title'         => $v['name'],
									'callback'      => array( $this, 'display_metabox' ),
									'post_type'     => $posttype,
									'context'       => $v['context'],
									'priority'      => $v['priority'],
									'callback_args' => $v,
								);
							} else {
								$title = $v['name'];
								if ( $title !== $this->plugin_name ) {
									$title = $this->plugin_name . ' - ' . $title;
								}
								if ( ! empty( $v['help_link'] ) ) {
									// TODO Investigate/Remove undefined varible.
									$title .= "<a class='aioseop_help_text_link aioseop_meta_box_help' target='_blank' href='" . $lopts['help_link'] . "'><span>" . __( 'Help', 'all-in-one-seo-pack' ) . '</span></a>';
								}
								add_meta_box(
									$v['prefix'] . $k,
									$title,
									array(
										$this,
										'display_metabox',
									),
									$posttype,
									$v['context'],
									$v['priority'],
									$v
								);
							}
						}
					}
				}
			}

			// TODO Fix Missing return statement.
		}

		/**
		 * Toggle Save Post Hooks
		 *
		 * Adds or removes hooks that could be called while editing a post.
		 *
		 * @todo Review if all these hooks are really required (save_post should be enough vs. edit_post and publish_post).
		 *
		 * @since ?
		 *
		 * @param boolean $add ?.
		 */
		private function toggle_save_post_hooks( $add ) {
			if ( $add ) {
				add_action( 'edit_post', array( $this, 'save_post_data' ) );
				add_action( 'publish_post', array( $this, 'save_post_data' ) );
				add_action( 'add_attachment', array( $this, 'save_post_data' ) );
				add_action( 'edit_attachment', array( $this, 'save_post_data' ) );
				add_action( 'save_post', array( $this, 'save_post_data' ) );
				add_action( 'edit_page_form', array( $this, 'save_post_data' ) );
			} else {
				remove_action( 'edit_post', array( $this, 'save_post_data' ) );
				remove_action( 'publish_post', array( $this, 'save_post_data' ) );
				remove_action( 'add_attachment', array( $this, 'save_post_data' ) );
				remove_action( 'edit_attachment', array( $this, 'save_post_data' ) );
				remove_action( 'save_post', array( $this, 'save_post_data' ) );
				remove_action( 'edit_page_form', array( $this, 'save_post_data' ) );
			}
		}

		/**
		 * Save Post Data
		 *
		 * Update postmeta for metabox.
		 *
		 * @since ?
		 *
		 * @param int $post_id Post ID.
		 */
		public function save_post_data( $post_id ) {
			$this->toggle_save_post_hooks( false );
			if ( null !== $this->locations ) {
				foreach ( $this->locations as $k => $v ) {
					if ( isset( $v['type'] ) && ( 'metabox' === $v['type'] ) ) {
						$opts    = $this->default_options( $k );
						$options = array();
						foreach ( $opts as $l => $o ) {
							// TODO Processing form data without nonce verification.
							// phpcs:disable WordPress.Security.NonceVerification.Missing
							if ( isset( $_POST[ $l ] ) ) {
								// TODO Detected usage of a non-sanitized input variable: $_POST.
								// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								// TODO Missing wp_unslash() before sanitization.
								// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
								$options[ $l ] = stripslashes_deep( $_POST[ $l ] );
								$options[ $l ] = esc_attr( $options[ $l ] );
							}
							// phpcs:enable
						}
						$prefix  = $this->get_prefix( $k );
						$options = apply_filters( $prefix . 'filter_metabox_options', $options, $k, $post_id );
						update_post_meta( $post_id, '_' . $prefix . $k, $options );
					}
				}
			}

			$this->toggle_save_post_hooks( true );
		}

		/**
		 * Do Multi Input
		 *
		 * Outputs radio buttons, checkboxes, selects, multiselects, handles groups.
		 *
		 * @since ?
		 *
		 * @param array $args Function args/params.
		 * @return string
		 */
		public function do_multi_input( $args ) {
			$options = $args['options'];
			$value   = $args['value'];
			$name    = $args['name'];
			$attr    = $args['attr'];

			$buf1 = '';
			$type = $options['type'];

			$strings = array(
				'block'     => "<select name='$name' $attr>%s\n</select>\n",
				'group'     => "\t<optgroup label='%s'>\n%s\t</optgroup>\n",
				'item'      => "\t<option %s value='%s'>%s</option>\n",
				'item_args' => array( 'sel', 'v', 'subopt' ),
				'selected'  => 'selected ',
			);

			if ( ( 'radio' === $type ) || ( 'checkbox' === $type ) ) {
				$strings = array(
					'block'     => "%s\n",
					'group'     => "\t<b>%s</b><br>\n%s\n",
					'item'      => "\t<label class='aioseop_option_setting_label'><input type='$type' %s name='%s' value='%s' %s> %s</label>\n",
					'item_args' => array( 'sel', 'name', 'v', 'attr', 'subopt' ),
					'selected'  => 'checked ',
				);
			}

			$setsel = $strings['selected'];
			if ( isset( $options['initial_options'] ) && is_array( $options['initial_options'] ) ) {
				foreach ( $options['initial_options'] as $l => $option ) {
					// TODO strip_tags() is discouraged. Use the more comprehensive wp_strip_all_tags() instead.
					// phpcs:disable WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
					$option_check = strip_tags( is_array( $option ) ? implode( ' ', $option ) : $option );
					// phpcs:enable
					if ( empty( $l ) && empty( $option_check ) ) {
						continue;
					}
					$is_group = is_array( $option );
					if ( ! $is_group ) {
						$option = array( $l => $option );
					}
					$buf2 = '';
					foreach ( $option as $v => $subopt ) {
						$sel    = '';
						$is_arr = is_array( $value );
						if ( is_string( $v ) || is_string( $value ) ) {
							if ( is_string( $value ) ) {
								$cmp = ! strcmp( $v, $value );
							} else {
								$cmp = ! strcmp( $v, '' );
							}
							// phpcs:disable Squiz.PHP.CommentedOutCode.Found
							// $cmp = !strcmp( (string)$v, (string)$value ); // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
							// phpcs:enable
						} else {
							// Intended as a loose comparator.
							$cmp = ( $value == $v ); // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
						}
						if ( ( ! $is_arr && $cmp ) || ( $is_arr && in_array( $v, $value, true ) ) ) {
							$sel = $setsel;
						}
						$item_arr = array();
						foreach ( $strings['item_args'] as $arg ) {
							$item_arr[] = $$arg;
						}
						$buf2 .= vsprintf( $strings['item'], $item_arr );
					}
					if ( $is_group ) {
						$buf1 .= sprintf( $strings['group'], $l, $buf2 );
					} else {
						$buf1 .= $buf2;
					}
				}
				$buf1 = sprintf( $strings['block'], $buf1 );
			}

			return $buf1;
		}

		/**
		 * Get Option HTML
		 *
		 * Outputs a setting item for settings pages and metaboxes.
		 *
		 * @since ?
		 *
		 * @param array $args The function arguments.
		 * @return string
		 */
		public function get_option_html( $args ) {
			static $n = 0;

			$options = $args['options'];
			$value   = $args['value'];
			$name    = $args['name'];
			$attr    = $args['attr'];
			$prefix  = isset( $args['prefix'] ) ? $args['prefix'] : '';

			if ( 'custom' === $options['type'] ) {
				return apply_filters( "{$prefix}output_option", '', $args );
			}

			$needle_array = array(
				'multiselect',
				'select',
				'multicheckbox',
				'radio',
				'checkbox',
				'textarea',
				'text',
				'submit',
				'hidden',
				'date',
			);
			if ( in_array( $options['type'], $needle_array, true ) && is_string( $value ) ) {
				$value = esc_attr( $value );
			}
			$buf    = '';
			$onload = '';
			if ( ! empty( $options['count'] ) ) {
				$n ++;
				$classes  = isset( $options['class'] ) ? $options['class'] : '';
				$classes .= ' aioseop_count_chars';

				$attr .= " class='{$classes}' data-length-field='{$prefix}length$n'";
			}
			if ( isset( $opts['id'] ) ) {
				// TODO Investigate/Remove undefined varible.
				$attr .= " id=\"{$opts['id']}\" ";
			}
			if ( isset( $options['required'] ) && true === $options['required'] ) {
				$attr .= ' required';
			}
			switch ( $options['type'] ) {
				case 'multiselect':
					$attr .= ' MULTIPLE';

					$name = "{$name}[]";

					$args['attr'] = $attr;
					$args['name'] = $name;

					// fall through.
				case 'select':
					$buf .= $this->do_multi_input( $args );
					break;
				case 'multicheckbox':
					$name = "{$name}[]";

					$args['name']            = $name;
					$args['options']['type'] = 'checkbox';

					$options['type'] = 'checkbox';

					// fall through.
				case 'radio':
					$buf .= $this->do_multi_input( $args );
					break;
				case 'checkbox':
					if ( $value ) {
						$attr .= ' CHECKED';
					}
					$buf .= "<input name='$name' type='{$options['type']}' $attr>\n";
					break;
				case 'textarea':
					// #1363: prevent characters like ampersand in title and description (in social meta module) from getting changed to &amp;
					if ( in_array( $name, array( 'aiosp_opengraph_hometitle', 'aiosp_opengraph_description' ), true ) ) {
						$value = htmlspecialchars_decode( $value, ENT_QUOTES );
					}
					$buf .= "<textarea name='$name' $attr>$value</textarea>";
					break;
				case 'image':
					$buf .= '<input class="aioseop_upload_image_checker" type="hidden" name="' . $name . '_checker" value="0">' .
							"<input class='aioseop_upload_image_button button-primary' type='button' value='";
					$buf .= __( 'Upload Image', 'all-in-one-seo-pack' );
					$buf .= "' style='float:left;' />" .
							"<input class='aioseop_upload_image_label' name='" . esc_attr( $name ) . "' type='text' " . esc_html( $attr ) . " value='" . esc_attr( $value ) . "' size=57 style='float:left;clear:left;'>\n";
					break;
				case 'html':
					$buf .= wp_kses( $value, wp_kses_allowed_html( 'post' ) );
					break;
				case 'esc_html':
					$buf .= '<pre>' . esc_html( $value ) . "</pre>\n";
					break;
				case 'date':
					// firefox and IE < 11 do not have support for HTML5 date, so we will fall back to the datepicker.
					wp_enqueue_script( 'jquery-ui-datepicker' );
					// fall through.
				default:
					$buf .= "<input name='" . esc_attr( $name ) . "' type='" . esc_attr( $options['type'] ) . "' " . wp_kses( $attr, wp_kses_allowed_html( 'data' ) ) . " value='" . esc_attr( $value ) . "'>\n";
					break;
			}
			if ( ! empty( $options['count'] ) ) {
				$size = 60;
				if ( isset( $options['size'] ) ) {
					$size = $options['size'];
				} elseif ( isset( $options['rows'] ) && isset( $options['cols'] ) ) {
					$size = $options['rows'] * $options['cols'];
				}
				if ( isset( $options['count_desc'] ) ) {
					$count_desc = $options['count_desc'];
				} else {
					/* Translators: %1$s is the max count. %2$s is the option/settings name. */
					$count_desc = __( ' characters. Most search engines use a maximum of %1$s chars for the %2$s.', 'all-in-one-seo-pack' );
				}
				$buf .= "<br /><input readonly tabindex='-1' type='text' name='{$prefix}length$n' size='3' maxlength='3' style='width:53px;height:23px;margin:0px;padding:0px 0px 0px 10px;' value='" . $this->strlen( $value ) . "' />"
						. sprintf( $count_desc, $size, trim( $this->strtolower( $options['name'] ), ':' ) );
				if ( ! empty( $onload ) ) {
					$buf .= "<script>jQuery( document ).ready(function() { {$onload} });</script>";
				}
			}

			return $buf;
		}

		const DISPLAY_HELP_START   = '<a class="aioseop_help_text_link" style="cursor:pointer;" title="%s" onclick="toggleVisibility(\'%s_tip\');"><label class="aioseop_label textinput">%s</label></a>';
		const DISPLAY_HELP_END     = '<div class="aioseop_help_text_div" style="display:none" id="%s_tip"><label class="aioseop_help_text">%s</label></div>';
		const DISPLAY_LABEL_FORMAT = '<span class="aioseop_option_label" style="text-align:%s;vertical-align:top;">%s</span>';
		const DISPLAY_TOP_LABEL    = "</div>\n<div class='aioseop_input aioseop_top_label'>\n";
		const DISPLAY_ROW_TEMPLATE = '<div class="aioseop_wrapper%s" id="%s_wrapper"><div class="aioseop_input">%s<span class="aioseop_option_input"><div class="aioseop_option_div" %s>%s</div>%s</span><p style="clear:left"></p></div></div>';

		/**
		 * Get Option Row
		 *
		 * Format a row for an option on a settings page.
		 *
		 * @since ?
		 *
		 * @param string $name Option/setting's name.
		 * @param array  $opts Funstion's options.
		 * @param array  $args Params for `All_in_One_SEO_Pack_Module::get_option_html()`.
		 *
		 * @return string
		 */
		public function get_option_row( $name, $opts, $args ) {
			$label_text  = '';
			$input_attr  = '';
			$help_text_2 = '';
			$id_attr     = '';

			$align = 'right';
			if ( 'top' === $opts['label'] ) {
				$align = 'left';
			}
			if ( isset( $opts['id'] ) ) {
				$id_attr .= " id=\"{$opts['id']}_div\" ";
			}
			if ( 'none' !== $opts['label'] ) {
				if ( isset( $opts['help_text'] ) ) {
					$help_text   = sprintf( self::DISPLAY_HELP_START, __( 'Click for Help!', 'all-in-one-seo-pack' ), $name, $opts['name'] );
					$help_text_2 = sprintf( self::DISPLAY_HELP_END, $name, $opts['help_text'] );
				} else {
					$help_text = $opts['name'];
				}
				$label_text = sprintf( self::DISPLAY_LABEL_FORMAT, $align, $help_text );
			} else {
				$input_attr .= ' aioseop_no_label ';
			}
			if ( 'top' === $opts['label'] ) {
				$label_text .= self::DISPLAY_TOP_LABEL;
			}
			$input_attr .= " aioseop_{$opts['type']}_type";

			return sprintf( self::DISPLAY_ROW_TEMPLATE, $input_attr, $name, $label_text, $id_attr, $this->get_option_html( $args ), $help_text_2 );
		}

		/**
		 * Display options for settings pages and metaboxes, allows for filtering settings, custom display options.
		 *
		 * @since ?
		 *
		 * @param null $location  Option's location.
		 * @param null $meta_args With metabox id, title, callback, and args elements.
		 */
		public function display_options( $location = null, $meta_args = null ) {
			static $location_settings = array();

			$defaults  = null;
			$prefix    = $this->get_prefix( $location );
			$help_link = '';
			if ( is_array( $meta_args['args'] ) && ! empty( $meta_args['args']['default_options'] ) ) {
				$defaults = $meta_args['args']['default_options'];
			}
			if ( ! empty( $meta_args['callback_args'] ) && ! empty( $meta_args['callback_args']['help_link'] ) ) {
				$help_link = $meta_args['callback_args']['help_link'];
			}
			if ( ! empty( $help_link ) ) {
				// TODO All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$help_link'.
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				// TODO All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'.
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo "<a class='aioseop_help_text_link aioseop_meta_box_help' target='_blank' href='" . $help_link . "'><span>" . __( 'Help', 'all-in-one-seo-pack' ) . '</span></a>';
				// phpcs:enable
			}

			if ( ! isset( $location_settings[ $prefix ] ) ) {
				$current_options                                 = apply_filters( "{$this->prefix}display_options", $this->get_current_options( array(), $location, $defaults ), $location );
				$settings                                        = apply_filters( "{$this->prefix}display_settings", $this->setting_options( $location, $defaults ), $location, $current_options );
				$current_options                                 = apply_filters( "{$this->prefix}override_options", $current_options, $location, $settings );
				$location_settings[ $prefix ]['current_options'] = $current_options;
				$location_settings[ $prefix ]['settings']        = $settings;
			} else {
				$current_options = $location_settings[ $prefix ]['current_options'];
				$settings        = $location_settings[ $prefix ]['settings'];
			}
			// phpcs:ignore // $opts["snippet"]["default"] = sprintf( $opts["snippet"]["default"], "foo", "bar", "moby" );
			$container = "<div class='aioseop aioseop_options {$this->prefix}settings'>";
			if ( is_array( $meta_args['args'] ) && ! empty( $meta_args['args']['options'] ) ) {
				$args     = array();
				$arg_keys = array();
				foreach ( $meta_args['args']['options'] as $a ) {
					if ( ! empty( $location ) ) {
						$key = $prefix . $location . '_' . $a;
						if ( ! isset( $settings[ $key ] ) ) {
							$key = $a;
						}
					} else {
						$key = $prefix . $a;
					}
					if ( isset( $settings[ $key ] ) ) {
						$arg_keys[ $key ] = 1;
					} elseif ( isset( $settings[ $a ] ) ) {
						$arg_keys[ $a ] = 1;
					}
				}
				$setting_keys = array_keys( $settings );
				foreach ( $setting_keys as $s ) {
					if ( ! empty( $arg_keys[ $s ] ) ) {
						$args[ $s ] = $settings[ $s ];
					}
				}
			} else {
				$args = $settings;
			}
			foreach ( $args as $name => $opts ) {
				$attr_list = array( 'class', 'style', 'readonly', 'disabled', 'size', 'placeholder' );
				if ( 'textarea' === $opts['type'] ) {
					$attr_list = array_merge( $attr_list, array( 'rows', 'cols' ) );
				}
				$attr = '';
				foreach ( $attr_list as $a ) {
					if ( isset( $opts[ $a ] ) ) {
						$attr .= ' ' . $a . '="' . esc_attr( $opts[ $a ] ) . '" ';
					}
				}
				$opt = '';
				if ( isset( $current_options[ $name ] ) ) {
					$opt = $current_options[ $name ];
				}
				// TODO Found: ==. Use strict comparisons (=== or !==).
				// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
				if ( 'none' === $opts['label'] && 'submit' == $opts['type'] && false == $opts['save'] ) {
					$opt = $opts['name'];
				}
				// TODO Found: ==. Use strict comparisons (=== or !==).
				// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison:
				if ( 'html' === $opts['type'] && empty( $opt ) && false == $opts['save'] ) {
					$opt = $opts['default'];
				}
				// phpcs:enable

				$args = array(
					'name'    => $name,
					'options' => $opts,
					'attr'    => $attr,
					'value'   => $opt,
					'prefix'  => $prefix,
				);

				if ( ! empty( $opts['nowrap'] ) ) {
					// TODO All output should be run through an escaping function.
					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->get_option_html( $args );
					// phpcs:enable
				} else {
					if ( $container ) {
						// TODO All output should be run through an escaping function.
						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $container;
						// phpcs:enable
						$container = '';
					}
					// TODO All output should be run through an escaping function.
					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $this->get_option_row( $name, $opts, $args );
					// phpcs:enable
				}
			}
			if ( ! $container ) {
				echo '</div>';
			}
		}

		/**
		 * Sanitize Domain
		 *
		 * @since ?
		 *
		 * @param string $domain The domain address.
		 * @return mixed|string
		 */
		public function sanitize_domain( $domain ) {
			$domain = trim( $domain );
			$domain = $this->strtolower( $domain );
			if ( $this->strpos( $domain, 'http://' ) === 0 ) {
				$domain = $this->substr( $domain, 7 );
			} elseif ( $this->strpos( $domain, 'https://' ) === 0 ) {
				$domain = $this->substr( $domain, 8 );
			}
			$domain = untrailingslashit( $domain );

			return $domain;
		}

		/**
		 * Sanitize options
		 *
		 * @since ?
		 *
		 * @param array|null $location Setting's location.
		 */
		public function sanitize_options( $location = null ) {
			foreach ( $this->setting_options( $location ) as $k => $v ) {
				if ( isset( $this->options[ $k ] ) ) {
					if ( ! empty( $v['sanitize'] ) ) {
						$type = $v['sanitize'];
					} else {
						$type = $v['type'];
					}
					switch ( $type ) {
						case 'multiselect':
							// fall through.
						case 'multicheckbox':
							$this->options[ $k ] = urlencode_deep( $this->options[ $k ] );
							break;
						case 'textarea':
							// #1363: prevent characters like ampersand in title and description (in social meta module) from getting changed to &amp;
							if ( ! ( 'opengraph' === $location && in_array( $k, array( 'aiosp_opengraph_hometitle', 'aiosp_opengraph_description' ), true ) ) ) {
								$this->options[ $k ] = wp_kses_post( $this->options[ $k ] );
							}
							$this->options[ $k ] = htmlspecialchars( $this->options[ $k ], ENT_QUOTES );
							break;
						case 'filename':
							$this->options[ $k ] = sanitize_file_name( $this->options[ $k ] );
							break;
						case 'url':
							// fall through.
						case 'text':
							$this->options[ $k ] = wp_kses_post( $this->options[ $k ] );
							// fall through.
						case 'checkbox':
							// fall through.
						case 'radio':
							// fall through.
						case 'select':
							// fall through.
						default:
							if ( ! is_array( $this->options[ $k ] ) ) {
								$this->options[ $k ] = esc_attr( $this->options[ $k ] );
							}
					}
				}
			}
		}

		/**
		 * Display metaboxes with display_options()
		 *
		 * @since ?
		 *
		 * @param WP_Post $post    The current post.
		 * @param array   $metabox With metabox id, title, callback, and args elements.
		 */
		public function display_metabox( $post, $metabox ) {
			$this->display_options( $metabox['args']['location'], $metabox );
		}

		/**
		 * Handle resetting options to defaults.
		 *
		 * @since ?
		 *
		 * @param array|null $location ?.
		 * @param bool       $delete   ?Whether to delete class module options.
		 */
		public function reset_options( $location = null, $delete = false ) {
			if ( true === $delete ) {
				$this->delete_class_option( $delete );
				$this->options = array();
			}
			$default_options = $this->default_options( $location );
			foreach ( $default_options as $k => $v ) {
				$this->options[ $k ] = $v;
			}
			$this->update_class_option( $this->options );
		}

		/**
		 * Handle Settings Updates
		 *
		 * Handle option resetting and updating.
		 *
		 * @since ?
		 *
		 * @param null $location ?.
		 * @return mixed|string|void
		 */
		public function handle_settings_updates( $location = null ) {
			$message = '';
			// TODO Processing form data without nonce verification.
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if (
					isset( $_POST['action'] )
					&& 'aiosp_update_module' === $_POST['action']
					&& (
						isset( $_POST['Submit_Default'] )
						|| isset( $_POST['Submit_All_Default'] )
						|| ! empty( $_POST['Submit'] )
					)
			) {
				// TODO Detected usage of a non-sanitized input variable: $_POST.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// TODO Detected usage of a non-validated input variable: $_POST.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				// TODO Missing wp_unslash() before sanitization.
				// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$nonce = $_POST['nonce-aioseop'];
				// phpcs:enable
				if ( ! wp_verify_nonce( $nonce, 'aioseop-nonce' ) ) {
					// TODO All output should be run through an escaping function.
					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					die( __( 'Security Check - If you receive this in error, log out and back in to WordPress', 'all-in-one-seo-pack' ) );
					// phpcs:enable
				}
				if ( isset( $_POST['Submit_Default'] ) || isset( $_POST['Submit_All_Default'] ) ) {
					$message = __( 'Options Reset.', 'all-in-one-seo-pack' );
					if ( isset( $_POST['Submit_All_Default'] ) ) {
						$this->reset_options( $location, true );
						do_action( 'aioseop_options_reset' );
					} else {
						$this->reset_options( $location );
					}
				}
				if ( ! empty( $_POST['Submit'] ) ) {
					$message         = __( 'All in One SEO Options Updated.', 'all-in-one-seo-pack' );
					$default_options = $this->default_options( $location );
					foreach ( $default_options as $k => $v ) {
						if ( isset( $_POST[ $k ] ) ) {
							// TODO Detected usage of a non-sanitized input variable: $_POST.
							// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							// TODO Missing wp_unslash() before sanitization.
							// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
							$this->options[ $k ] = stripslashes_deep( $_POST[ $k ] );
							// phpcs:enable
						} else {
							$this->options[ $k ] = '';
						}
					}
					$this->sanitize_options( $location );
					$this->options = apply_filters( $this->prefix . 'update_options', $this->options, $location );
					$this->update_class_option( $this->options );
					wp_cache_flush();
				}
				do_action( $this->prefix . 'settings_update', $this->options, $location );
			}
			// phpcs:enable

			return $message;
		}

		/**
		 * Display Settings Page
		 *
		 * Update / reset settings, printing options, sanitizing, posting back.
		 *
		 * @since ?
		 *
		 * @param null $location ?.
		 */
		public function display_settings_page( $location = null ) {
			// TODO Use strict comparisons (=== or !==).
			//phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
			if ( ! empty( $location ) ) {
				// phpcs:enable
				$location_info = $this->locations[ $location ];
			}
			$name = null;
			if ( $location && isset( $location_info['name'] ) ) {
				// TODO Investigate/Remove undefined varible.
				$name = $location_info['name'];
			}
			if ( ! $name ) {
				$name = $this->name;
			}
			$message = $this->handle_settings_updates( $location );
			$this->settings_page_init();

			// TODO All output should be run through an escaping function.
			//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<div class="wrap <?php echo get_class( $this ); ?>">
				<?php
				ob_start();
				do_action( $this->prefix . 'settings_header_errors', $location );
				$errors = ob_get_clean();
				echo $errors;
				?>
				<div id="aioseop_settings_header">
					<?php
					if ( ! empty( $message ) && empty( $errors ) ) {
						echo "<div id=\"message\" class=\"updated fade\"><p>$message</p></div>";
					}
					?>
					<div id="icon-aioseop" class="icon32"><br></div>
					<h2><?php echo $name; ?></h2>
					<div id="dropmessage" class="updated" style="display:none;"></div>
				</div>
				<?php
				do_action( 'aioseop_global_settings_header', $location );
				do_action( $this->prefix . 'settings_header', $location );
				?>
				<form id="aiosp_settings_form" name="dofollow" enctype="multipart/form-data" action="" method="post">
					<div id="aioseop_top_button">
						<div id="aiosp_ajax_settings_message"></div>
						<?php

						$submit_options = array(
							'action'         => array(
								'type'  => 'hidden',
								'value' => 'aiosp_update_module',
							),
							'module'         => array(
								'type'  => 'hidden',
								'value' => get_class( $this ),
							),
							'location'       => array(
								'type'  => 'hidden',
								'value' => $location,
							),
							'nonce-aioseop'  => array(
								'type'  => 'hidden',
								'value' => wp_create_nonce( 'aioseop-nonce' ),
							),
							'page_options'   => array(
								'type'  => 'hidden',
								'value' => 'aiosp_home_description',
							),
							'Submit'         => array(
								'type'  => 'submit',
								'class' => 'button-primary',
								'value' => __( 'Update Options', 'all-in-one-seo-pack' ) . ' &raquo;',
							),
							'Submit_Default' => array(
								'type'  => 'submit',
								'class' => 'button-secondary',
								/* Translators: The module/class. */
								'value' => sprintf( __( 'Reset %s Settings to Defaults', 'all-in-one-seo-pack' ), $name ) . ' &raquo;',
							),
						);
						$submit_options = apply_filters( "{$this->prefix}submit_options", $submit_options, $location );
						foreach ( $submit_options as $k => $s ) {
							if ( 'submit' === $s['type'] && 'Submit' !== $k ) {
								continue;
							}
							$class = '';
							if ( isset( $s['class'] ) ) {
								$class = " class='{$s['class']}' ";
							}
							echo $this->get_option_html(
								array(
									'name'    => $k,
									'options' => $s,
									'attr'    => $class,
									'value'   => $s['value'],
								)
							);
						}
						?>
					</div>
					<div class="aioseop_options_wrapper aioseop_settings_left">
						<?php
						$opts = $this->get_class_option();
						if ( false !== $opts ) {
							$this->options = $opts;
						}
						if ( is_array( $this->layout ) ) {
							foreach ( $this->layout as $l => $lopts ) {
								if ( ! isset( $lopts['tab'] ) || ( $this->current_tab === $lopts['tab'] ) ) {
									$title = $lopts['name'];
									if ( ! empty( $lopts['help_link'] ) ) {
										$title .= "<a class='aioseop_help_text_link aioseop_meta_box_help' target='_blank' href='" . $lopts['help_link'] . "'><span>" . __( 'Help', 'all-in-one-seo-pack' ) . '</span></a>';
									}
									add_meta_box(
										$this->get_prefix( $location ) . $l . '_metabox',
										$title,
										array(
											$this,
											'display_options',
										),
										"{$this->prefix}settings",
										'advanced',
										'default',
										$lopts
									);
								}
							}
						} else {
							add_meta_box(
								$this->get_prefix( $location ) . 'metabox',
								$name,
								array(
									$this,
									'display_options',
								),
								"{$this->prefix}settings",
								'advanced'
							);
						}
						do_meta_boxes( "{$this->prefix}settings", 'advanced', $location );
						?>
						<p class="submit" style="clear:both;">
							<?php
							foreach ( array( 'action', 'nonce-aioseop', 'page_options' ) as $submit_field ) {
								if ( isset( $submit_field ) ) {
									unset( $submit_field );
								}
							}
							foreach ( $submit_options as $k => $s ) {
								$class = '';
								if ( isset( $s['class'] ) ) {
									$class = " class='{$s['class']}' ";
								}
								echo $this->get_option_html(
									array(
										'name'    => $k,
										'options' => $s,
										'attr'    => $class,
										'value'   => $s['value'],
									)
								);
							}
							?>
						</p>
					</div>
				</form>
				<?php
				do_action( $this->prefix . 'settings_footer', $location );
				do_action( 'aioseop_global_settings_footer', $location );
				?>
			</div>
			<?php
			// phpcs:enable
		}

		/**
		 * Get Prefix
		 *
		 * Get the prefix used for a given location.
		 *
		 * @since ?
		 *
		 * @param array|null $location ?.
		 * @return string
		 */
		public function get_prefix( $location = null ) {
			if ( ( ! empty( $location ) ) && isset( $this->locations[ $location ]['prefix'] ) ) {
				return $this->locations[ $location ]['prefix'];
			}

			return $this->prefix;
		}

		/**
		 * Setting Options
		 *
		 * Sets up initial settings.
		 *
		 * @since ?
		 *
		 * @param array|null $location ?.
		 * @param array|null $defaults ?.
		 *
		 * @return array
		 */
		public function setting_options( $location = null, $defaults = null ) {
			if ( empty( $defaults ) ) {
				$defaults = $this->default_options;
			}
			$prefix = $this->get_prefix( $location );
			$opts   = array();
			if ( empty( $location ) || ( isset( $this->locations[ $location ] ) && null === $this->locations[ $location ]['options'] ) ) {
				$options = $defaults;
			} else {
				$options = array();
				$prefix  = "{$prefix}{$location}_";
				if ( ! empty( $this->locations[ $location ]['default_options'] ) ) {
					$options = $this->locations[ $location ]['default_options'];
				}
				foreach ( $this->locations[ $location ]['options'] as $opt ) {
					if ( isset( $defaults[ $opt ] ) ) {
						$options[ $opt ] = $defaults[ $opt ];
					}
				}
			}
			if ( ! $prefix ) {
				$prefix = $this->prefix;
			}
			if ( ! empty( $options ) ) {
				foreach ( $options as $k => $v ) {
					if ( ! isset( $v['name'] ) ) {
						$v['name'] = $this->ucwords( strtr( $k, '_', ' ' ) );
					}
					if ( ! isset( $v['type'] ) ) {
						$v['type'] = 'checkbox';
					}
					if ( ! isset( $v['default'] ) ) {
						$v['default'] = null;
					}
					if ( ! isset( $v['initial_options'] ) ) {
						$v['initial_options'] = $v['default'];
					}
					if ( 'custom' === $v['type'] && ( ! isset( $v['nowrap'] ) ) ) {
						$v['nowrap'] = true;
					} elseif ( ! isset( $v['nowrap'] ) ) {
						$v['nowrap'] = null;
					}
					if ( isset( $v['condshow'] ) ) {
						if ( ! is_array( $this->script_data ) ) {
							$this->script_data = array();
						}
						if ( ! isset( $this->script_data['condshow'] ) ) {
							$this->script_data['condshow'] = array();
						}
						$this->script_data['condshow'][ $prefix . $k ] = $v['condshow'];
					}
					if ( 'submit' === $v['type'] ) {
						if ( ! isset( $v['save'] ) ) {
							$v['save'] = false;
						}
						if ( ! isset( $v['label'] ) ) {
							$v['label'] = 'none';
						}
						if ( ! isset( $v['prefix'] ) ) {
							$v['prefix'] = false;
						}
					} else {
						if ( ! isset( $v['label'] ) ) {
							$v['label'] = null;
						}
					}
					if ( 'hidden' === $v['type'] ) {
						if ( ! isset( $v['label'] ) ) {
							$v['label'] = 'none';
						}
						if ( ! isset( $v['prefix'] ) ) {
							$v['prefix'] = false;
						}
					}
					if ( ( 'text' === $v['type'] ) && ( ! isset( $v['size'] ) ) ) {
						$v['size'] = 57;
					}
					if ( 'textarea' === $v['type'] ) {
						if ( ! isset( $v['cols'] ) ) {
							$v['cols'] = 57;
						}
						if ( ! isset( $v['rows'] ) ) {
							$v['rows'] = 2;
						}
					}
					if ( ! isset( $v['save'] ) ) {
						$v['save'] = true;
					}
					if ( ! isset( $v['prefix'] ) ) {
						$v['prefix'] = true;
					}
					if ( $v['prefix'] ) {
						$opts[ $prefix . $k ] = $v;
					} else {
						$opts[ $k ] = $v;
					}
				}
			}

			return $opts;
		}

		/**
		 * Generates just the default option names and values
		 *
		 * @since 2.4.13 Applies filter before final return.
		 *
		 * @param array|null $location ?.
		 * @param array|null $defaults ?.
		 *
		 * @return array
		 */
		public function default_options( $location = null, $defaults = null ) {
			$prefix  = $this->get_prefix( $location );
			$options = $this->setting_options( $location, $defaults );
			$opts    = array();
			foreach ( $options as $k => $v ) {
				if ( $v['save'] ) {
					$opts[ $k ] = $v['default'];
				}
			}
			return apply_filters( $prefix . 'default_options', $opts, $location );
		}

		/**
		 * Gets the current options stored for a given location.
		 *
		 * @since 2.4.14 Added taxonomy options.
		 *
		 * @param array       $opts     ?.
		 * @param string|null $location ?.
		 * @param null        $defaults ?.
		 * @param null        $post     ?.
		 *
		 * @return array
		 */
		public function get_current_options( $opts = array(), $location = null, $defaults = null, $post = null ) {
			$prefix   = $this->get_prefix( $location );
			$get_opts = '';
			if ( empty( $location ) ) {
				$type = 'settings';
			} else {
				$type = $this->locations[ $location ]['type'];
			}
			if ( 'settings' === $type ) {
				$get_opts = $this->get_class_option();
			} elseif ( 'metabox' === $type ) {
				if ( null === $post ) {
					global $post;
				}

				// TODO Processing form data without nonce verification.
				// phpcs:disable WordPress.Security.NonceVerification.Recommended
				if ( ( isset( $_GET['taxonomy'] ) && isset( $_GET['tag_ID'] ) ) || is_category() || is_tag() || is_tax() ) {
					$term_id = isset( $_GET['tag_ID'] ) ? (int) $_GET['tag_ID'] : 0;
					// phpcs:enable
					$term_id = $term_id ? $term_id : get_queried_object()->term_id;
					if ( AIOSEOPPRO ) {
						$get_opts = AIO_ProGeneral::getprotax( $get_opts );
						$get_opts = get_term_meta( $term_id, '_' . $prefix . $location, true );
					}
				} elseif ( isset( $post ) ) {
					$get_opts = get_post_meta( $post->ID, '_' . $prefix . $location, true );
				}
			}

			if ( is_home() && ! is_front_page() ) {
				// If we're on the non-front page blog page, WP doesn't really know its post meta data so we need to get that manually for social meta.
				$get_opts = get_post_meta( get_option( 'page_for_posts' ), '_' . $prefix . $location, true );
			}

			$defs = $this->default_options( $location, $defaults );
			if ( empty( $get_opts ) ) {
				$get_opts = $defs;
			} else {
				$get_opts = wp_parse_args( $get_opts, $defs );
			}
			$opts = wp_parse_args( $opts, $get_opts );

			return $opts;
		}

		/**
		 * Update Option
		 *
		 * Updates the options array in the module; loads saved settings with get_option() or uses defaults.
		 *
		 * @since ?
		 *
		 * @param array $opts     ?.
		 * @param null  $location ?.
		 * @param null  $defaults ?.
		 */
		public function update_options( $opts = array(), $location = null, $defaults = null ) {
			if ( null === $location ) {
				$type = 'settings';
			} else {
				// TODO Investigate/Remove undefined varible.
				$type = $this->locations[ $location ][ $type ];
			}
			$get_opts = false;
			if ( 'settings' === $type ) {
				$get_opts = $this->get_class_option();
			}
			if ( false === $get_opts ) {
				$get_opts = $this->default_options( $location, $defaults );
			} else {
				$this->setting_options( $location, $defaults );
			}
			// hack -- make sure this runs anyhow, for now -- pdb.
			$this->options = wp_parse_args( $opts, $get_opts );
		}
	}
}
