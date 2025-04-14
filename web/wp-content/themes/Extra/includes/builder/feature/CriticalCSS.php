<?php
/**
 * Extract Critical CSS
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Extract Critical CSS
 */
class ET_Builder_Critical_CSS {
	// Include in Critical CSS the Required Assets (those which don't depends on Content).
	// To force some of the Required Assets in the BTF, check `maybe_defer_global_asset` method.
	const INCLUDE_REQUIRED = true;
	// Used to estimate height for percentage based units like `vh`,`em`, etc.
	const VIEWPORT_HEIGHT = 1000;
	const FONT_HEIGHT     = 16;

	/**
	 * Is Critical CSS Threshold Height.
	 *
	 * @var int
	 */
	protected $_above_the_fold_height;

	/**
	 * Root element.
	 *
	 * @var stdClass
	 */
	protected $_root;

	/**
	 * Modules.
	 *
	 * @var array
	 */
	protected $_modules = [];

	/**
	 * Modules.
	 *
	 * @var stdClass
	 */
	protected $_content;

	/**
	 * Above The Fold Sections.
	 *
	 * @var array
	 */
	protected $_atf_sections = [];

	/**
	 * Builder Style Manager.
	 *
	 * @var array
	 */
	protected $_builder_styles = [];

	/**
	 * Instance of `ET_Builder_Critical_CSS`.
	 *
	 * @var ET_Builder_Critical_CSS
	 */
	private static $_instance;

	/**
	 * ET_Builder_Critical_CSS constructor.
	 */
	public function __construct() {
		global $shortname;

		if ( et_is_builder_plugin_active() ) {
			$options                   = get_option( 'et_pb_builder_options', array() );
			$critical_threshold_height = isset( $options['performance_main_critical_threshold_height'] ) ? $options['performance_main_critical_threshold_height'] : 'Medium';
		} else {
			$critical_threshold_height = et_get_option( $shortname . '_critical_threshold_height', 'Medium' );
		}

		if ( 'High' === $critical_threshold_height ) {
			$this->_above_the_fold_height = 1500;
		} elseif ( 'Medium' === $critical_threshold_height ) {
			$this->_above_the_fold_height = 1000;
		} else {
			$this->_above_the_fold_height = 500;
		}

		add_filter( 'et_builder_critical_css_enabled', '__return_true' );
		// Dynamic CSS content shortcode modules.
		add_filter( 'et_dynamic_assets_modules_atf', [ $this, 'dynamic_assets_modules_atf' ], 10, 2 );
		// Detect when renderining Above The Fold sections.
		add_filter( 'pre_do_shortcode_tag', [ $this, 'check_section_start' ], 99, 4 );
		add_filter( 'do_shortcode_tag', [ $this, 'check_section_end' ], 99, 2 );

		// Analyze Builder style manager.
		add_filter( 'et_builder_module_style_manager', [ $this, 'enable_builder' ] );

		// Dynamic CSS content shortcode.
		add_filter( 'et_global_assets_list', [ $this, 'maybe_defer_global_asset' ], 99 );

		if ( self::INCLUDE_REQUIRED ) {
			add_filter( 'et_dynamic_assets_atf_includes_required', '__return_true' );
		}
	}

	/**
	 * Defer some global assets if threshold is met.
	 *
	 * @param array $assets assets to defer.
	 *
	 * @since 4.10.0
	 *
	 * @return array $assets assets to be deferred.
	 */
	public function maybe_defer_global_asset( $assets ) {
		$defer = [
			'et_divi_footer',
			'et_divi_gutters_footer',
			'et_divi_comments',
		];

		foreach ( $defer as $key ) {
			if ( isset( $assets[ $key ] ) ) {
				$assets[ $key ]['maybe_defer'] = true;
			}
		}

		return $assets;
	}

	/**
	 * Force a PageResource to write its content on file, even when empty
	 *
	 * @param bool  $force Default value.
	 * @param array $resource Critical/Deferred PageResources.
	 *
	 * @since 4.10.0
	 *
	 * @return bool
	 */
	public function force_resource_write( $force, $resource ) {
		$styles = $this->_builder_styles;
		if ( empty( $styles ) ) {
			return $force;
		}

		$forced_slugs = [
			$styles['deferred']->slug,
			$styles['manager']->slug,
		];

		return in_array( $resource->slug, $forced_slugs, true ) ? true : $force;
	}

	/**
	 * Analyze Builder style manager.
	 *
	 * @since 4.10.0
	 *
	 * @param array $styles Style Managers.
	 *
	 * @return array
	 */
	public function enable_builder( $styles ) {
		$this->_builder_styles = $styles;

		// There are cases where external assets generation might be disabled at runtime,
		// ensure Critical CSS and Dynamic Assets use the same logic to avoid side effects.
		if ( ! et_should_generate_dynamic_assets() ) {
			$this->disable();
			return $styles;
		}

		add_filter( 'et_core_page_resource_force_write', [ $this, 'force_resource_write' ], 10, 2 );
		add_filter( 'et_core_page_resource_tag', [ $this, 'builder_style_tag' ], 10, 5 );
		if ( et_builder_is_mod_pagespeed_enabled() ) {
			// PageSpeed filters out `preload` links so we gotta use `prefetch` but
			// Safari doesn't support the latter....
			add_action( 'wp_body_open', [ $this, 'add_safari_prefetch_workaround' ], 1 );
		}

		return $styles;
	}

	/**
	 * Prints deferred Critical CSS stlyesheet.
	 *
	 * @param string $tag stylesheet template.
	 * @param string $slug stylesheet slug.
	 * @param string $scheme stylesheet URL.
	 * @param string $onload stylesheet onload attribute.
	 *
	 * @since 4.10.0
	 *
	 * @return string
	 */
	public function builder_style_tag( $tag, $slug, $scheme, $onload ) {
		$deferred = $this->_builder_styles['deferred'];
		$inlined  = $this->_builder_styles['manager'];

		// reason: Stylsheet needs to be printed on demand.
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		// reason: Snake case requires refactor of PageResource.php.
		// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase
		switch ( $slug ) {
			case $deferred->slug:
				// Don't enqueue empty resources.
				if ( 0 === et_()->WPFS()->size( $deferred->path ) ) {
					return '';
				}

				// Use 'prefetch' when Mod PageSpeed is detected because it removes 'preload' links.
				$rel = et_builder_is_mod_pagespeed_enabled() ? 'prefetch' : 'preload';

				/**
				 * Filter deferred styles rel attribute.
				 *
				 * Mod PageSpeed removes 'preload' links and we attempt to fix that by trying to detect if
				 * the 'x-mod-pagespeed' (Apache) or 'x-page-speed' (Nginx) header is present and if it is,
				 * replace 'preload' with 'prefetch'. However, in some cases, like when the request goes through
				 * a CDN first, we are unable to detect the header. This hook can be used to change the 'rel'
				 * attribute to use 'prefetch' when our et_builder_is_mod_pagespeed_enabled() function fails
				 * to detect Mod PageSpeed.
				 *
				 * With that out of the way, the only reason I wrote this detailed description is to make Fabio proud.
				 *
				 * @since 4.11.3
				 *
				 * @param string $rel
				 */
				$rel = apply_filters( 'et_deferred_styles_rel', $rel );

				// Defer the stylesheet.
				$template = '<link rel="%4$s" as="style" id="%1$s" href="%2$s" onload="this.onload=null;this.rel=\'stylesheet\';%3$s" />';

				return sprintf( $template, $slug, $scheme, $onload, $rel );
			case $inlined->slug:
				// Inline the stylesheet.
				$template = "<style id=\"et-critical-inline-css\">%1\$s</style>\n";
				$content  = et_()->WPFS()->get_contents( $inlined->path );
				return sprintf( $template, $content );
		}
		// phpcs:enable

		return $tag;
	}

	/**
	 * Safari doesn't support `prefetch`......
	 *
	 * @since 4.10.7
	 *
	 * @return void
	 */
	public function add_safari_prefetch_workaround() {
		// .... so we turn it into `preload` using JS.
		?>
		<script type="application/javascript">
			(function() {
				var relList = document.createElement('link').relList;
				if (!!(relList && relList.supports && relList.supports('prefetch'))) {
					// Browser supports `prefetch`, no workaround needed.
					return;
				}

				var links = document.getElementsByTagName('link');
				for (var i = 0; i < links.length; i++) {
					var link = links[i];
					if ('prefetch' === link.rel) {
						link.rel = 'preload';
					}
				}
			})();
		</script>
		<?php
	}

	/**
	 * Add `set_style` filter when rendering an ATF section.
	 *
	 * @since 4.10.0
	 *
	 * @param false|string $value Short-circuit return value. Either false or the value to replace the shortcode with.
	 * @param string       $tag   Shortcode name.
	 * @param array|string $attr  Shortcode attributes array or empty string.
	 * @param array        $m     Regular expression match array.
	 *
	 * @return false|string
	 */
	public function check_section_start( $value, $tag, $attr, $m ) {
		if ( 'et_pb_section' !== $tag ) {
			return $value;
		}

		$attrs  = $m[3];
		$action = 'et_builder_set_style';
		$filter = [ $this, 'set_style' ];
		$active = has_filter( $action, $filter );

		if ( ! empty( $this->_atf_sections[ $attrs ] ) ) {
			$this->_atf_sections[ $attrs ]--;

			if ( ! $active ) {
				add_filter( $action, [ $this, 'set_style' ], 10 );
			}
		}

		return $value;
	}

	/**
	 * Remove `set_style` filter after rendering an ATF section.
	 *
	 * @since 4.10.0
	 *
	 * @param string $output Shortcode output.
	 * @param string $tag    Shortcode name.
	 *
	 * @return string
	 */
	public function check_section_end( $output, $tag ) {
		static $section = 0;

		if ( 'et_pb_section' !== $tag ) {
			return $output;
		}

		$action = 'et_builder_set_style';
		$filter = [ $this, 'set_style' ];

		if ( has_filter( $action, $filter ) ) {
			remove_filter( $action, $filter, 10 );
		}

		return $output;
	}

	/**
	 * Filter used to analize content coming from Dynamic Access Class.
	 *
	 * @param array  $value Default shortcodes list (empty).
	 * @param string $content TB/Post Content.
	 *
	 * @since 4.10.0
	 *
	 * @return array List of ATF shortcodes.
	 */
	public function dynamic_assets_modules_atf( $value, $content = '' ) {
		if ( empty( $content ) ) {
			return $value;
		}

		$modules = $this->extract( $content );

		// Dynamic CSS content shortcode.
		add_filter( 'et_dynamic_assets_content', [ $this, 'dynamic_assets_content' ] );

		return $modules;

	}

	/**
	 * Returns splitted (ATF/BFT) Content.
	 *
	 * @since 4.10.0
	 *
	 * @return stdClass
	 */
	public function dynamic_assets_content() {
		return $this->_content;
	}

	/**
	 * While the filter is applied, any rendered style will be considered critical.
	 *
	 * @param array $style Style.
	 *
	 * @since 4.10.0
	 *
	 * @return array
	 */
	public function set_style( $style ) {
		$style['critical'] = true;
		return $style;
	}

	/**
	 * Parse Content into shortcodes.
	 *
	 * @param string $content TB/Post Content.
	 *
	 * @since 4.10.0
	 *
	 * @return array|boolean
	 */
	public static function parse_shortcode( $content ) {
		static $regex;

		if ( false === strpos( $content, '[' ) ) {
			return false;
		}

		if ( empty( $regex ) ) {
			$regex = '/' . get_shortcode_regex() . '/';

			// Add missing child shortcodes (because dynamically added).
			$existing   = 'et_pb_pricing_tables';
			$shortcodes = [
				$existing,
				'et_pb_pricing_item',
			];

			$regex = str_replace( $existing, join( '|', $shortcodes ), $regex );
		}

		preg_match_all( $regex, $content, $matches, PREG_SET_ORDER );

		return $matches;
	}

	/**
	 * Estimates height to split Content in ATF/BTF.
	 *
	 * @param string $content TB/Post Content.
	 *
	 * @since 4.10.0
	 *
	 * @return array List of ATF shortcodes.
	 */
	public function extract( $content ) {
		// Create root object when needed.
		if ( empty( $this->_root ) ) {
			$this->_root = (object) [
				'tag'    => 'root',
				'height' => 0,
			];
		}

		if ( $this->_root->height >= $this->_above_the_fold_height ) {
			// Do nothing when root already exists and its height >= treshold.
			return [];
		}

		$shortcodes = self::parse_shortcode( $content );

		if ( ! is_array( $shortcodes ) ) {
			return [];
		}

		$shortcodes        = array_reverse( $shortcodes );
		$is_above_the_fold = true;
		$root              = $this->_root;
		$root->count       = count( $shortcodes );
		$stack             = [ $root ];
		$parent            = end( $stack );
		$tags              = [];
		$atf_content       = '';
		$btf_content       = '';

		$structure_slugs = [
			'et_pb_section',
			'et_pb_row',
			'et_pb_row_inner',
			'et_pb_column',
			'et_pb_column_inner',
		];

		$section           = '';
		$section_shortcode = '';

		// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		while ( $is_above_the_fold && $shortcode = array_pop( $shortcodes ) ) {
			list( $raw,, $tag, $attrs,, $content ) = $shortcode;

			$tags[]   = $tag;
			$children = self::parse_shortcode( $content );
			$element  = (object) [
				'tag'      => $tag,
				'children' => [],
				'height'   => 0,
				'margin'   => 0,
				'padding'  => 0,
				'attrs'    => [],
			];

			switch ( $tag ) {
				case 'et_pb_pricing_table':
					$lines   = array_filter( explode( "\n", str_replace( array( '<p>', '</p>', '<br />' ), "\n", $content ) ) );
					$content = '';

					foreach ( $lines as $line ) {
						$content .= sprintf( '[et_pb_pricing_item]%s[/et_pb_pricing_item]', trim( $line ) );
					}

					$children = self::parse_shortcode( $content );
					break;
				case 'et_pb_section':
					$section           = $attrs;
					$section_shortcode = $raw;
					break;
			}

			$props = shortcode_parse_atts( $attrs );

			if ( isset( $props['custom_margin'] ) ) {
				$margin          = self::get_margin_padding_height( $props['custom_margin'] );
				$element->margin = $margin;
				if ( $margin > 0 ) {
					$element->height += $margin;
					$element->attrs[] = 'margin:' . $props['custom_margin'] . "-> $margin";
				}
			}

			if ( isset( $props['custom_padding'] ) ) {
				$padding          = self::get_margin_padding_height( $props['custom_padding'] );
				$element->padding = $padding;
				if ( $padding > 0 ) {
					$element->height += $padding;
					$element->attrs[] = 'padding:' . $props['custom_padding'] . "-> $padding";
				}
			}

			if ( false !== $children ) {
				// Non empty structure element.
				$element->count = count( $children );
				$stack[]        = $element;
				$shortcodes     = array_merge( $shortcodes, array_reverse( $children ) );
			} else {
				// Only add default content height for modules, not empty structure.
				if ( ! in_array( $tag, $structure_slugs, true ) ) {
					$element->height += 100;
				}
				do {
					$parent = end( $stack );

					switch ( $element->tag ) {
						case 'et_pb_column':
						case 'et_pb_column_inner':
							// Do nothing.
							break;
						case 'et_pb_row':
						case 'et_pb_row_inner':
							// Row height is determined by its tallest column.
							$max = 0;

							foreach ( $element->children as $column ) {
								$max = max( $max, $column->height );
							}

							$element->height += $max;
							$parent->height  += $element->height;
							break;
						case 'et_pb_section':
							// Update Above The Fold Sections.
							if ( isset( $this->_atf_sections[ $section ] ) ) {
								$this->_atf_sections[ $section ]++;
							} else {
								$this->_atf_sections[ $section ] = 1;
							}

							$atf_content  .= $section_shortcode;
							$root->height += $element->height;

							if ( $root->height >= $this->_above_the_fold_height ) {
								$is_above_the_fold = false;
							}
							break;
						default:
							$parent->height += $element->height;
					}

					$parent->children[] = $element;

					if ( 0 !== --$parent->count ) {
						break;
					}

					$element = $parent;
					array_pop( $stack );
					if ( empty( $stack ) ) {
						break;
					}
				} while ( $is_above_the_fold && 0 !== --$parent->count );
			}
		}

		foreach ( $shortcodes as $shortcode ) {
			$btf_content .= $shortcode[0];
		}

		$tags           = array_unique( $tags );
		$this->_modules = array_unique( array_merge( $this->_modules, $tags ) );
		$this->_content = (object) [
			'atf' => $atf_content,
			'btf' => $btf_content,
		];

		return $tags;
	}

	/**
	 * Calculate margin and padding.
	 *
	 * @param string $value Margin and padding values.
	 *
	 * @since 4.10.0
	 *
	 * @return int margin/padding height value.
	 */
	public static function get_margin_padding_height( $value ) {
		$values = explode( '|', $value );

		if ( empty( $values ) ) {
			return;
		}

		// Only top/bottom values are needed.
		$values = array_map( 'trim', [ $values[0], $values[2] ] );
		$total  = 0;

		foreach ( $values as $value ) {
			if ( '' === $value ) {
				continue;
			}

			$unit = et_pb_get_value_unit( $value );

			// Remove the unit, if present.
			if ( false !== strpos( $value, $unit ) ) {
				$value = substr( $value, 0, -strlen( $unit ) );
			}

			$value = (int) $value;

			switch ( $unit ) {
				case 'rem':
				case 'em':
					$value *= self::FONT_HEIGHT;
					break;
				case 'vh':
					$value = ( $value * self::VIEWPORT_HEIGHT ) / 100;
					break;
				case 'px':
					break;
				default:
					$value = 0;
			}

			$total += $value;
		}

		return $total;
	}

	/**
	 * Disable Critical CSS.
	 *
	 * @since 4.12.0
	 *
	 * @return void
	 */
	public function disable() {
		remove_filter( 'et_builder_critical_css_enabled', '__return_true' );
		remove_filter( 'et_dynamic_assets_modules_atf', [ $this, 'dynamic_assets_modules_atf' ] );
		remove_filter( 'pre_do_shortcode_tag', [ $this, 'check_section_start' ] );
		remove_filter( 'do_shortcode_tag', [ $this, 'check_section_end' ] );
		remove_filter( 'et_builder_module_style_manager', [ $this, 'enable_builder' ] );
		remove_filter( 'et_global_assets_list', [ $this, 'maybe_defer_global_asset' ] );
	}

	/**
	 * Get the class instance.
	 *
	 * @since 4.10.0
	 *
	 * @return ET_Builder_Critical_CSS
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

ET_Builder_Critical_CSS::instance();
