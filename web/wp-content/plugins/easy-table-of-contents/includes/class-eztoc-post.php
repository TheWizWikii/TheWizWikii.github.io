<?php

use function Easy_Plugins\Table_Of_Contents\Cord\br2;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ezTOC_Post {

	/**
	 * @since 2.0
	 * @var int
	 */
	private $queriedObjectID;

	/**
	 * @since 2.0
	 * @var WP_Post
	 */
	private $post;

	/**
	 * @since 2.0
	 * @var false|string
	 */
	private $permalink;

	/**
	 * The post content broken into pages by user inserting `<!--nextpage-->` into the post content.
	 * @see ezTOC_Post::extractPages()
	 * @since 2.0
	 * @var array
	 */
	private $pages = array();

	/**
	 * The user defined heading levels to be included in the TOC.
	 * @see ezTOC_Post::getHeadingLevels()
	 * @since 2.0
	 * @var array
	 */
	private $headingLevels = array();

	/**
	 * Array of nodes that are excluded by class/id selector.
	 * @since 2.0
	 * @var string[]
	 */
	private $excludedNodes = array();

	/**
	 * Keeps a track of used anchors for collision detecting.
	 * @see ezTOC_Post::generateHeadingIDFromTitle()
	 * @since 2.0
	 * @var array
	 */
	private $collision_collector = array();

	/**
	 * @var bool
	 */
	private $hasTOCItems = false;

	/**
	 * ezTOC_Post constructor.
	 *
	 * @since 2.0
	 *
	 * @param WP_Post $post
	 * @param bool    $apply_content_filter Whether or not to apply the `the_content` filter on the post content.
	 */
	public function __construct( WP_Post $post, $apply_content_filter = true ) {

		$this->post            = $post;
		$this->permalink       = get_permalink( $post );
		$this->queriedObjectID = get_queried_object_id();

        $apply_content_filter  = $this->apply_filter_status( $apply_content_filter );

        if ( $apply_content_filter ) {

            $this->applyContentFilter()->process();
        } else {

            $this->process();
        }
    }

	/**
	 * apply_filter_status function
	 *
	 * @since 2.0.51
	 * @access private
	 * @param bool $apply_content_filter
	 * @return bool
	 */
	private function apply_filter_status( $apply_content_filter )
    {

		/**
		 * ez_toc_apply_filter_status Apply filter
		 * for any plugin which conflict 
		 * in easy toc plugin
		 * @since 2.0.51
		 */
        $plugins = apply_filters(
            'ez_toc_apply_filter_status',
            array(
                'booster-extension/booster-extension.php',
                'divi-bodycommerce/divi-bodyshop-woocommerce.php',
                'social-pug/index.php',
				'fusion-builder/fusion-builder.php',
				'modern-footnotes/modern-footnotes.php',
				'yet-another-stars-rating-premium/yet-another-stars-rating.php'
            )
        );

        foreach ( $plugins as $value ) {
            if ( in_array( $value, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                $apply_content_filter = false;
            }
        }

		$apply_content_filter = apply_filters('ez_toc_apply_filter_status_manually', $apply_content_filter);
		global $eztoc_disable_the_content;
	    if($eztoc_disable_the_content){
			$apply_content_filter = false;
			$eztoc_disable_the_content = false;
	    }
        return $apply_content_filter;
    }

	/**
	 * @access public
	 * @since  2.0
	 *
	 * @param $id
	 *
	 * @return ezTOC_Post|null
	 */
	public static function get( $id ) {

		$post = get_post( $id );

		if ( ! $post instanceof WP_Post ) {

			return null;
		}
                
		return new static( $post );
	}

	/**
	 * Process post content for headings.
	 *
	 * This must be run after object init or after @see ezTOC_Post::applyContentFilter().
	 *
	 * @since  2.0
	 *
	 * @return static
	 */
	private function process() {

		$this->processPages();

		return $this;
	}

	/**
	 * Apply `the_content` filter to the post content.
	 *
	 * @since  2.0
	 *
	 * @return static
	 */
	private function applyContentFilter() {

		/*
		 * Parses dynamic blocks out of post_content and re-renders them for gutenberg blocks.
		 */		
		if(function_exists('do_blocks')){
			$this->post->post_content = do_blocks($this->post->post_content);
		}else{
			$this->post->post_content = $this->post->post_content;
		}
		
		if( defined('EASY_TOC_AMP_VERSION') && function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() ){
			$ampforwp_pagebuilder_enable = get_post_meta(get_the_ID(),'ampforwp_page_builder_enable', true);
			if($ampforwp_pagebuilder_enable=='yes' && function_exists('ampforwp_eztoc_PageBuilder_content')){
				$this->post->post_content = ampforwp_eztoc_PageBuilder_content();
			}
		}

		add_filter( 'strip_shortcodes_tagnames', array( __CLASS__, 'stripShortcodes' ), 10, 2 );

		/*
		 * Ensure the ezTOC content filter is not applied when running `the_content` filter.
		 */
		remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );

		$enable_memory_fix = ezTOC_Option::get('enable_memory_fix');
		if ( $enable_memory_fix ) {
		/*
		 * Strip the shortcodes but retain their inner content for processing TOC.
		 * This issues happens with builder themes which adds shortcodes for sections , rows and columns etc
		 * This is required to prevent an Infinite loop when the `the_content` filter is applied and the post content contains the ezTOC shortcode.
		 * 
		 * @see https://github.com/ahmedkaludi/Easy-Table-of-Contents/issues/749
		*/
		$this->post->post_content = $this->stripShortcodesButKeepContent($this->post->post_content);
		
		}

		$this->post->post_content = apply_filters( 'the_content', strip_shortcodes( $this->post->post_content ) );

		add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );  // increased  priority to fix other plugin filter overwriting our changes

		remove_filter( 'strip_shortcodes_tagnames', array( __CLASS__, 'stripShortcodes' ) );

		return $this;
	}

	/**
	 * Callback for the `strip_shortcodes_tagnames` filter.
	 *
	 * Strip the shortcodes so their content is no processed for headings.
	 *
	 * @see ezTOC_Post::applyContentFilter()
	 *
	 * @since 2.0
	 *
	 * @param array  $tags_to_remove Array of shortcode tags to remove.
	 * @param string $content        Content shortcodes are being removed from.
	 *
	 * @return array
	 */
	public static function stripShortcodes( $tags_to_remove, $content ) {

		/*
		 * Ensure the ezTOC shortcodes are not processed when applying `the_content` filter
		 * otherwise an infinite loop may occur.
		 */
		$tags_to_remove = apply_filters(
			'ez_toc_strip_shortcodes_tagnames',
			array(
				'ez-toc',
				'ez-toc-widget-sticky',
				apply_filters( 'ez_toc_shortcode', 'toc' ),
			),
			$content
		);

		return $tags_to_remove;
	}

	/**
	 * This is a work around for theme's and plugins
	 * which break the WordPress global $wp_query var by unsetting it
	 * or overwriting it which breaks the method call
	 * that `get_query_var()` uses to return the query variable.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return int
	 */
	protected function getCurrentPage() {

		global $wp_query;

		// Check to see if the global `$wp_query` var is an instance of WP_Query and that the get() method is callable.
		// If it is then when can simply use the get_query_var() function.
		if ( $wp_query instanceof WP_Query && is_callable( array( $wp_query, 'get' ) ) ) {

			$page =  get_query_var( 'page', 1 );

			return 1 > $page ? 1 : $page;

			// If a theme or plugin broke the global `$wp_query` var, check to see if the $var was parsed and saved in $GLOBALS['wp_query']->query_vars.
		} elseif ( isset( $GLOBALS['wp_query']->query_vars[ 'page' ] ) ) {

			return $GLOBALS['wp_query']->query_vars[ 'page' ];

			// We should not reach this, but if we do, lets check the original parsed query vars in $GLOBALS['wp_the_query']->query_vars.
		} elseif ( isset( $GLOBALS['wp_the_query']->query_vars[ 'page' ] ) ) {

			return $GLOBALS['wp_the_query']->query_vars[ 'page' ];

			// Ok, if all else fails, check the $_REQUEST super global.
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason : Nonce verification is not required here.
		} elseif ( isset( $_REQUEST[ 'page' ] ) ) {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason : Nonce verification is not required here.
			return $_REQUEST[ 'page' ];
		}

		// Finally, return the $default if it was supplied.
		return 1;
	}

	/**
	 * Get the number of page the post has.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return int
	 */
	protected function getNumberOfPages() {

		return count( $this->pages );
	}

	/**
	 * Whether or not the post has multiple pages.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return bool
	 */
	protected function isMultipage() {

		return 1 < $this->getNumberOfPages();
	}

	/**
	 * Parse the post content and headings.
	 *
	 * @access private
	 * @since  2.0
	 */
	private function processPages() {

		$content = apply_filters( 'ez_toc_modify_process_page_content', $this->post->post_content );
		
		// Fix for wordpress category pages showing wrong toc if they have description
		if(is_category()){
			$cat_from_query=get_query_var( 'cat', null ); 
			if($cat_from_query){
				$category = get_category($cat_from_query);
				if(is_object($category) && property_exists($category,'description') && !empty($category->description)){
					$content = $category->description;
				}
			}
		}

		if(is_tax() || is_tag()){
			global $wp_query;
			$tax = $wp_query->get_queried_object();
			if(is_object($tax)){
				$content = apply_filters('ez_toc_modify_taxonomy_content',$tax->description,$tax->term_id);
			}
		}

		if(function_exists('is_product_category') && is_product_category()){
			$term_object = get_queried_object();			
			if(!empty($term_object->description)){
				$content     = $term_object->description;
			}						
		}		

		if ( in_array( 'js_composer_salient/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$eztoc_post_id=get_the_ID();
			$eztoc_post_meta = get_option( 'ez-toc-post-meta-content',false);
			if(!empty($eztoc_post_meta) && !empty($eztoc_post_id) && isset($eztoc_post_meta[$eztoc_post_id])){
				if ( empty( $content ) ) {
					$content = $eztoc_post_meta[$eztoc_post_id];
				} else {
					$content .= $eztoc_post_meta[$eztoc_post_id];
				}
		}
		} else if ( ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) && false != get_option( 'ez-toc-post-content-core-level' ) ) {
                    $content = get_option( 'ez-toc-post-content-core-level' );
		} else {
                       
                }

		$pages = array();

		$split = preg_split( '/<!--nextpage-->/msuU', $content );

		$page = $first_page = 1;
		$totalHeadings = [];
		if ( is_array( $split ) ) {


			foreach ( $split as $content ) {

				$this->extractExcludedNodes( $page, $content );

				$totalHeadings[] = array(
					'headings' => $this->extractHeadings( $content, $page ),
					'content'  => $content,
				);

				$page++;
			}

		}
		$pages[$first_page] = $totalHeadings;

		$this->pages = $pages;
	}

	/**
	 * Get the post's parse content and headings.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array
	 */
	public function getPages() {

		return $this->pages;
	}

	/**
	 * Extract nodes that heading are to be excluded.
	 *
	 * @since 2.0
	 *
	 * @param int    $page
	 * @param string $content
	 */
	private function extractExcludedNodes( $page, $content ) {

		if ( ! class_exists( 'TagFilter' ) ) {

                        if(phpversion() <= 5.6)
                            require_once( EZ_TOC_PATH . '/includes/vendor/ultimate-web-scraper/tag_filter56.php' );
                        else
                            require_once( EZ_TOC_PATH . '/includes/vendor/ultimate-web-scraper/tag_filter.php' );
		}

		$tagFilterOptions = TagFilter::GetHTMLOptions();

		// Set custom TagFilter options.
		$tagFilterOptions['charset'] = get_option( 'blog_charset' );

		$html = TagFilter::Explode( $content, $tagFilterOptions );

		/**
		 * @since 2.0
		 *
		 * @param $selectors array  Array of classes/id selector to exclude from TOC.
		 * @param $content   string Post content.
		 */
		$selectors = apply_filters( 'ez_toc_exclude_by_selector', array( '.ez-toc-exclude-headings' ), $content );
		$selectors = ! is_array( $selectors ) ? [] : $selectors; // In case we get string instead of array
		$nodes = $html->Find( implode( ',', $selectors ) );
		if(isset($nodes['ids'])){
			foreach ( $nodes['ids'] as $id ) {

				array_push( $this->excludedNodes, $html->Implode( $id, $tagFilterOptions ) );
			}
		}

		/**
		 * TagFilter::Implode() writes br tags as `<br>` while WP normalizes to `<br />`.
		 * Normalize `$eligibleContent` to match WP.
		 *
		 * @see wpautop()
		 */
	}

	/**
	 * Extract the posts content for headings.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param string $content
	 *
	 * @return array
	 */
	private function extractHeadings( $content, $page = 1 ) {

		$matches = array();

		if ( in_array( 'elementor/elementor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) || function_exists( 'koyfin_setup' )) {
                    $content = apply_filters( 'ez_toc_extract_headings_content', $content );           
                } else {
                    $content = apply_filters( 'ez_toc_extract_headings_content', wptexturize( $content ) );
                }

                /**
                * Lasso Product Compatibility
                * @since 2.0.46
                */
                $regEx = apply_filters( 'ez_toc_regex_filteration', '/(<h([1-6]{1})[^>]*>)(.*)<\/h\2>/msuU' );
                
		// get all headings
		// the html spec allows for a maximum of 6 heading depths
		if ( preg_match_all( $regEx, $content, $matches, PREG_SET_ORDER ) ) {

			$minimum = absint( ezTOC_Option::get( 'start' ) );

			$this->removeHeadingsFromExcludedNodes( $matches );
			$this->removeHeadings( $matches );
			$this->excludeHeadings( $matches );
			$this->removeEmptyHeadings( $matches );

			if ( count( $matches ) >= $minimum ) {

				$this->alternateHeadings( $matches );
				$this->headingIDs( $matches );
				$this->addPage( $matches, $page );
				$this->hasTOCItems = true;

			} else {

				return array();
			}

		}

		return array_values( $matches ); // Rest the array index.
	}

	/**
	 * addPage function
	 *
	 * @access private
	 * @since 2.0.50
	 * @param array|null|false $matches
	 * @param int $page
	 * @return void
	 */
	private function addPage( &$matches, $page )
	{
		foreach ( $matches as $i => $match ) {
			$matches[ $i ][ 'page' ] = $page;
		}
		return $matches;
	}
	/**
	 * Whether or not the string is in one of the excluded nodes.
	 *
	 * @since 2.0
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	private function inExcludedNode( $string ) {

		foreach ( $this->excludedNodes as $node ) {

			if ( empty( $node ) || empty( $string ) ) {

				return false;
			}

			if ( false !== strpos( $node, $string ) ) {

				return true;
			}
		}

		return false;
	}

	/**
	 * Remove headings that are in excluded nodes.
	 *
	 * @since 2.0
	 *
	 * @param array $matches
	 *
	 * @return array
	 */
	private function removeHeadingsFromExcludedNodes( &$matches ) {

		foreach ( $matches as $i => $match ) {
			
			$match[3] = apply_filters( 'ez_toc_filter_headings_from_exclude_nodes', $match[3]);

			if ( $this->inExcludedNode( "{$match[3]}</h$match[2]>" ) ) {

				unset( $matches[ $i ] );
			}
		}

		return $matches;
	}

	/**
	 * Get the heading levels to be included in the TOC.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @return array
	 */
	private function getHeadingLevels() {

		$levels = get_post_meta( $this->post->ID, '_ez-toc-heading-levels', true );

		if ( ! is_array( $levels ) ) {

			$levels = array();
		}

		if ( empty( $levels ) ) {

			$levels = ezTOC_Option::get( 'heading_levels', array() );
		}

		$this->headingLevels = $levels;

		return $this->headingLevels;
	}

	/**
	 * Remove the heading levels as defined by user settings from the TOC heading matches.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function removeHeadings( &$matches ) {

		$levels = $this->getHeadingLevels();

		if ( count( $levels ) != 6 ) {

			$new_matches = array();

			foreach ( $matches as $i => $match ) {

				if ( in_array( $matches[ $i ][2], $levels ) ) {

					$new_matches[ $i ] = $matches[ $i ];
				}
			}

			$matches = $new_matches;
		}

		return $matches;
	}

	/**
	 * Exclude the heading, by title, as defined by the user settings from the TOC matches.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array  $matches The headings from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function excludeHeadings( &$matches ) {

		$exclude = get_post_meta( $this->post->ID, '_ez-toc-exclude', true );

		if ( empty( $exclude ) ) {

			$exclude = ezTOC_Option::get( 'exclude' );
		}

		if ( $exclude ) {

			$excluded_headings = explode( '|', $exclude );
			$excluded_count    = count( $excluded_headings );

			if ( $excluded_count > 0 ) {

				for ( $j = 0; $j < $excluded_count; $j++ ) {

					$excluded_headings[ $j ] = preg_quote( $excluded_headings[ $j ] );

					// escape some regular expression characters
					// others: http://www.php.net/manual/en/regexp.reference.meta.php
					$excluded_headings[ $j ] = str_replace(
						array( '\*', '/', '%' ),
						array( '.*', '\/', '\%' ),
						trim( $excluded_headings[ $j ] )
					);
				}

				$new_matches = array();

				foreach ( $matches as $i => $match ) {

					$found = false;

					$against = html_entity_decode(
                                                ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) ? wp_strip_all_tags( str_replace( array( "\r", "\n" ), ' ', $matches[ $i ][0] ) ) : wptexturize(wp_strip_all_tags( str_replace( array( "\r", "\n" ), ' ', $matches[ $i ][0] ) ) ),
						ENT_NOQUOTES,
						get_option( 'blog_charset' )
					);

					for ( $j = 0; $j < $excluded_count; $j++ ) {

						// Since WP manipulates the post content it is required that the excluded header and
						// the actual header be manipulated similarly so a match can be made.
						$pattern = html_entity_decode(
							( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) ? $excluded_headings[ $j ] : wptexturize($excluded_headings[ $j ]),
							ENT_NOQUOTES,
							get_option( 'blog_charset' )
						);
						$against = trim($against); 
						if ( preg_match( '/^' . $pattern . '$/imU', $against ) ) {

							$found = true;
							break;
						}
					}

					if ( ! $found ) {

						$new_matches[ $i ] = $matches[ $i ];
					}
				}

					$matches = $new_matches;
			}
		}

		return $matches;
	}

	/**
	 * Return the alternate headings added by the user, saved in the post meta.
	 *
	 * The result is an associative array where the `key` is the original post heading
	 * and the `value` is the alternate heading.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @return array
	 */
	private function getAlternateHeadings() {

		$alternates = array();
		$value      = get_post_meta( $this->post->ID, '_ez-toc-alttext', true );

		if ( $value ) {

			$headings = preg_split( '/\r\n|[\r\n]/', $value );
			$count    = count( $headings );

			if ( $headings ) {

				for ( $k = 0; $k < $count; $k++ ) {

					$heading = explode( '|', $headings[ $k ] );

					/**
					 * @link https://wordpress.org/support/topic/undefined-offset-1-home-blog-public-wp-content-plugins-easy-table-of-contents/
					 */
					if ( ! is_array( $heading) ||
					     ! array_key_exists( 0, $heading ) ||
					     ! array_key_exists( 1, $heading )
					) {
						continue;
					}

					if ( 0 < strlen( $heading[0] ) && 0 < strlen( $heading[1] ) ) {

						$alternates[ $heading[0] ] = $heading[1];
					}
				}

			}

		}

		return $alternates;
	}

	/**
	 * Add the alternate headings to the array.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function alternateHeadings( &$matches ) {

		$alt_headings = $this->getAlternateHeadings();

		if ( 0 < count( $alt_headings ) ) {

			foreach ( $matches as $i => $match ) {

				foreach ( $alt_headings as $original_heading => $alt_heading ) {

					// Cleanup and texturize so alt heading can match heading in post content.
                                        if ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
                                            $original_heading = trim( $original_heading );
                                        }else {
                                            $original_heading = wptexturize( trim( $original_heading ) );
                                        }
					// Deal with special characters such as non-breakable space.
					$original_heading = str_replace(
						array( "\xc2\xa0" ),
						array( ' ' ),
						$original_heading
					);

					// Escape for regular expression.
					$original_heading = preg_quote( $original_heading );

					// Escape for regular expression some other characters: http://www.php.net/manual/en/regexp.reference.meta.php
					$original_heading = str_replace(
						array( '\*', '/', '%' ),
						array( '.*', '\/', '\%' ),
						$original_heading
					);

					// Cleanup subject so alt heading can match heading in post content.
					$subject = wp_strip_all_tags( $matches[ $i ][0] );

					// Deal with special characters such as non-breakable space.
					$subject = str_replace(
						array( "\xc2\xa0" ),
						array( ' ' ),
						$subject
					);

					if ( preg_match( '/^' . $original_heading . '$/imU', $subject ) ) {

						$matches[ $i ]['alternate'] = $alt_heading;
					}
				}
			}
		}

		return $matches;
	}

	/**
	 * Add the heading `id` to the array.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return mixed
	 */
	private function headingIDs( &$matches ) {

		foreach ( $matches as $i => $match ) {

			$matches[ $i ]['id'] = $this->generateHeadingIDFromTitle( $matches[ $i ][0] );
		}

		return $matches;
	}

	/**
	 * Create unique heading ID from heading string.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param string $heading
	 *
	 * @return bool|string
	 */
	private function generateHeadingIDFromTitle( $heading ) {

		$return = false;

		if ( $heading ) {
			$heading = apply_filters( 'ez_toc_url_anchor_target_before', $heading );
			// WP entity encodes the post content.
			$return = html_entity_decode( $heading, ENT_QUOTES, get_option( 'blog_charset' ) );
			$return = br2( $return, ' ' );
			$return = trim( wp_strip_all_tags( $return ) );

			// Convert accented characters to ASCII.
			$return = remove_accents( $return );

			// replace newlines with spaces (eg when headings are split over multiple lines)
			$return = str_replace( array( "\r", "\n", "\n\r", "\r\n" ), ' ', $return );

			// Remove `&amp;` and `&nbsp;` NOTE: in order to strip "hidden" `&nbsp;`,
			// title needs to be converted to HTML entities.
			// @link https://stackoverflow.com/a/21801444/5351316
			$return = htmlentities2( $return );
			$return = str_replace( array( '&amp;', '&nbsp;'), ' ', $return );
			$return = str_replace( array( '&shy;' ),'', $return );					// removed silent hypen 
			$return = html_entity_decode( $return, ENT_QUOTES, get_option( 'blog_charset' ) );

			// remove non alphanumeric chars
			$return = preg_replace( '/[\x00-\x1F\x7F]*/u', '', $return );

			//for procesing shortcode in headings
			$return = apply_filters('ez_toc_table_heading_title_anchor',$return);
			// Reserved Characters.
			// * ' ( ) ; : @ & = + $ , / ? # [ ]
			$return = str_replace(
				array( '*', '\'', '(', ')', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']' ),
				'',
				$return
			);

			// Unsafe Characters.
			// % { } | \ ^ ~ [ ] `
			$return = str_replace(
				array( '%', '{', '}', '|', '\\', '^', '~', '[', ']', '`' ),
				'',
				$return
			);

			// Special Characters.
			// $ - _ . + ! * ' ( ) ,
			// Special case for Apostrophes (’) which is causing TOC link to break in Block themes and CM Tooltip Glossary plugin #556
			$return = str_replace(
				array( '$', '.', '+', '!', '*', '\'', '(', ')', ',', '’' ),
				'',
				$return
			);

			// Dashes
			// Special Characters.
			// - (minus) - (dash) â€“ (en dash) â€” (em dash)
			$return = str_replace(
				array( '-', '-', 'â€“', 'â€”' ),
				'-',
				$return
			);

			// Curley quotes.
			// â€˜ (curly single open quote) â€™ (curly single close quote) â€œ (curly double open quote) â€ (curly double close quote)
			$return = str_replace(
				array( 'â€˜', 'â€™', 'â€œ', 'â€' ),
				'',
				$return
			);

			// AMP/Caching plugins seems to break URL with the following characters, so lets replace them.
			$return = str_replace( array( ':' ), '_', $return );

			// Convert space characters to an `_` (underscore).
			$return = preg_replace( '/\s+/', '_', $return );

			// Replace multiple `-` (hyphen) with a single `-` (hyphen).
			$return = preg_replace( '/-+/', '-', $return );

			// Replace multiple `_` (underscore) with a single `_` (underscore).
			$return = preg_replace( '/_+/', '_', $return );

			// Remove trailing `-` (hyphen) and `_` (underscore).
			$return = rtrim( $return, '-_' );

			/*
			 * Encode URI based on ECMA-262.
			 *
			 * Only required to support the jQuery smoothScroll library.
			 *
			 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURI#Description
			 * @link https://stackoverflow.com/a/19858404/5351316
			 */
			$return = preg_replace_callback(
				"{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i",
				function( $m ) {

					return sprintf( '%%%02X', ord( $m[0] ) );
				},
				$return
			);

			// lowercase everything?
			if ( ezTOC_Option::get( 'lowercase' ) ) {

				$return = strtolower( $return );
			}

			// if blank, then prepend with the fragment prefix
			// blank anchors normally appear on sites that don't use the latin charset
			//@since  2.0.59
			if ( !$return || true == ezTOC_Option::get( 'all_fragment_prefix' ) ) {
				$return = ( ezTOC_Option::get( 'fragment_prefix' ) ) ? ezTOC_Option::get( 'fragment_prefix' ) : '_';
			}

			// hyphenate?
			if ( ezTOC_Option::get( 'hyphenate' ) ) {

				$return = str_replace( '_', '-', $return );
				$return = preg_replace( '/-+/', '-', $return );
			}
		}

		if ( array_key_exists( $return, $this->collision_collector ) ) {

			$this->collision_collector[ $return ]++;
			$return .= '-' . $this->collision_collector[ $return ];

		} else {

			$this->collision_collector[ $return ] = 1;
		}

		return apply_filters( 'ez_toc_url_anchor_target', $return, $heading );
	}

	/**
	 * Remove any empty headings from the TOC.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function removeEmptyHeadings( &$matches ) {

		$new_matches = array();
		foreach ( $matches as $i => $match ) {

			if ( trim( wp_strip_all_tags( $matches[ $i ][0] ) ) != false ) {

				$new_matches[ $i ] = $matches[ $i ];
			}
		}


			$matches = $new_matches;

		return $matches;
	}

	/**
	 * Whether or not the post has TOC items.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool
	 */
	public function hasTOCItems() {

		return $this->hasTOCItems;
	}

	/**
	 * Get the headings of the current page of the post.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|null $page
	 *
	 * @return array
	 */
	public function getHeadings( $page = null ) {

		$headings = array();

		if ( is_null( $page ) ) {

			$page = $this->getCurrentPage();
		}

		if ( !empty( $this->pages ) || isset( $this->pages[ $page ] ) ) {

			$matches = $this->getHeadingsfromPageContents( $page );

			foreach ( $matches as $i => $match ) {

                $headings[] = str_replace(
                    array(
                        $matches[ $i ][1],                // start of heading
                        '</h' . $matches[ $i ][2] . '>'   // end of heading
                    ),
                    array(
                        '>',
                        '</h' . $matches[ $i ][2] . '>'
                    ),
                   apply_filters('ez_toc_content_heading_title',$matches[ $i ][0])
                );

			}
		}

		return $headings;
	}
	/**
	 * Get the heading title id.
	 *
	 * @access public
	 * @since  2.0.58
	 *
	 * @param int|null $page
	 *
	 * @return array
	 */
	public function getTocTitleId( $page = null ) {
		$nav_data = array();
		if ( is_null( $page ) ) {
			$page = $this->getCurrentPage();
		}
		if ( !empty( $this->pages ) || isset( $this->pages[ $page ] ) ) {
			$matches = $this->getHeadingsfromPageContents( $page );
			foreach ( $matches as $i => $match ) {
				$nav_data[$i]['title'] = wp_strip_all_tags( $matches[ $i ][0] );
				$nav_data[ $i ]['id'] = strtolower(str_replace( '_', '-', $matches[ $i ]['id'] ));
			}
		}
		return $nav_data;
	}

	/**
	 * Get the heading with in page anchors of the current page of the post.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|null $page
	 *
	 * @return array
	 */
	public function getHeadingsWithAnchors( $page = null ) {

		$headings = array();

		if ( is_null( $page ) ) {

			$page = $this->getCurrentPage();
		}

		if ( !empty( $this->pages ) || isset( $this->pages[ $page ] ) ) {

			$matches = $this->getHeadingsfromPageContents( $page );
			foreach ( $matches as $i => $match ) {

				$anchor     = $matches[ $i ]['id'];

				$headings[] = str_replace(
					array(
						$matches[ $i ][1],                // start of heading
						'</h' . $matches[ $i ][2] . '>'   // end of heading
					),
					array(
						'><span class="ez-toc-section" id="' . $anchor . '"></span>',
						'<span class="ez-toc-section-end"></span></h' . $matches[ $i ][2] . '>'
					),
					apply_filters('ez_toc_content_heading_title_anchor',$matches[ $i ][0])
				);
			}
		}

		return $headings;
	}

	/**
	 * Parse the post content and headings.
	 * only use when filter "ez_toc_modify_process_page_content" is not fetching correct content
	 * mostly in case of custom post types.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function setContent($content){
		
		$pages = array();
		$split = preg_split( '/<!--nextpage-->/msuU', $content );

		$page = $first_page = 1;
		$totalHeadings = [];
		if ( is_array( $split ) ) {


			foreach ( $split as $content ) {

				$this->extractExcludedNodes( $page, $content );

				$totalHeadings[] = array(
					'headings' => $this->extractHeadings( $content, $page ),
					'content'  => $content,
				);

				$page++;
			}

		}
		$pages[$first_page] = $totalHeadings;

		$this->pages = $pages;
	}

	/**
	 * getHeadingsfromPageContents function
	 *
	 * @access private
	 * @since 2.0.50
	 * @param int $page
	 * @return array|null
	 */
	private function getHeadingsfromPageContents( $page = 1 )
	{
		$headings = [];
		$first_page = 1;
		foreach( $this->pages[ $first_page ] as $attributes ) 
		{
			if( isset($attributes['headings'][0]['page'])  && $page == $attributes['headings'][0]['page'] ) 
			{
				foreach( $attributes['headings'] as $heading ) 
				{
					array_push( $headings, $heading );
				}
			}
		}
		
		return $headings;
	} 

	/**
	 * createTOCParent function
	 *
	 * @param string $prefix
	 * @return void|mixed|string|null
	 */
	private function createTOCParent( $prefix = "ez-toc", $toc_more = array() )
	{
		$html = ''; 
		$first_page = 1;
		$headings = array();
		foreach ( $this->pages[ $first_page ] as $attribute )
		{
			$headings = array_merge( $headings, $attribute[ 'headings' ] );
		}

		if( !empty( $headings ) )
		{
			$html .= $this->createTOC( $first_page, $headings, $prefix, $toc_more );
		}

		return $html;
	}
	/**
	 * Get the post TOC list.
	 *
	 * @access public
	 * @param string $prefix
	 * @since  2.0
	 *
	 * @return string
	 */
	public function getTOCList($prefix = "ez-toc", $options = []) {
		
		$html = '';

		$toc_more = isset($options['view_more']) ? array( 'view_more' => $options['view_more'] )  : array();

		if(isset($options['hierarchy'])){
			$toc_more['hierarchy'] = true;
		}elseif(isset($options['no_hierarchy'])){
			$toc_more['no_hierarchy'] = true;
		}

		if(isset($options['collapse_hd'])){
			$toc_more['collapse_hd'] = true;
		}elseif(isset($options['no_collapse_hd'])){
			$toc_more['no_collapse_hd'] = true;
		}

		if ( $this->hasTOCItems ) {
			
			$html = $this->createTOCParent($prefix, $toc_more);
			$visiblityClass = '';
			if( ezTOC_Option::get( 'visibility_hide_by_default' ) && 'js' == ezTOC_Option::get( 'toc_loading' ) &&  ezTOC_Option::get( 'visibility' ))
			{
				$visiblityClass = "eztoc-toggle-hide-by-default";
			}
			if( get_post_meta( $this->post->ID, '_ez-toc-visibility_hide_by_default', true ) && 'js' == ezTOC_Option::get( 'toc_loading' ) && ezTOC_Option::get( 'visibility' ))
			{
				$visiblityClass = "eztoc-toggle-hide-by-default";
			}
			if(is_array($options) && key_exists( 'visibility_hide_by_default', $options ) && $options['visibility_hide_by_default'] == true && 'js' == ezTOC_Option::get( 'toc_loading' ) && ezTOC_Option::get( 'visibility' )){
				$visiblityClass = "eztoc-toggle-hide-by-default";
			}elseif(is_array($options) && key_exists( 'visibility_show_by_default', $options ) && $options['visibility_show_by_default'] == true && 'js' == ezTOC_Option::get( 'toc_loading' ) && ezTOC_Option::get( 'visibility' )){
				$visiblityClass = "";
			}elseif(is_array($options) && key_exists( 'visibility_hide_by_default', $options ) && $options['visibility_hide_by_default'] == false){
				$visiblityClass = "";
			}
			$html  = apply_filters('ez_toc_add_custom_links',$html);
			$html  = "<ul class='{$prefix}-list {$prefix}-list-level-1 $visiblityClass' >" . $html . "</ul>";
		}

		return $html;
	}

	/**
	 * Get the post Sticky Toggle TOC content block.
	 *
	 * @access public
	 * @return string
	 * @since  2.0.32
	 *
	 */
	public function getStickyToggleTOC() {
		$classSticky = array( 'ez-toc-sticky-v' . str_replace( '.', '_', ezTOC::VERSION ) );
		$htmlSticky  = '';
		if ( $this->hasTOCItems() ) {
			$classSticky[] = 'counter-flat';
			if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'ltr' ) {
                $classSticky[] = 'ez-toc-sticky-toggle-counter';
            }
            if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'rtl' ) {
                $classSticky[] = 'ez-toc-sticky-toggle-counter-rtl';
            }



			$classSticky = array_filter( $classSticky );
			$classSticky = array_map( 'trim', $classSticky );
			$classSticky = array_map( 'sanitize_html_class', $classSticky );


            $ezTocStickyToggleDirection = 'ez-toc-sticky-toggle-direction';

			if ( ezTOC_Option::get( 'show_heading_text' ) ) {
				$toc_title = apply_filters('ez_toc_sticky_title', ezTOC_Option::get( 'heading_text' ));
				$toc_title_tag = ezTOC_Option::get( 'heading_text_tag' );
				$toc_title_tag = $toc_title_tag?$toc_title_tag:'p';
				if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== false ) {
					$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
				}
				if ( strpos( $toc_title, '%PAGE_NAME%' ) !== false ) {
					$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
				}
					$htmlSticky .= '<div class="ez-toc-sticky-title-container">' . PHP_EOL;
					
				switch($toc_title_tag){
					case 'div':
						$htmlSticky .= '<div class="ez-toc-sticky-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )) . '</div>' . PHP_EOL;
					break;
					case 'label':
						$htmlSticky .= '<label class="ez-toc-sticky-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )) . '</label>' . PHP_EOL;
					break;
					case 'span':
						$htmlSticky .= '<span class="ez-toc-sticky-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )) . '</span>' . PHP_EOL;
					break;
					default:
						$htmlSticky .= '<p class="ez-toc-sticky-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )) . '</p>' . PHP_EOL;
					break;
				}	
					$htmlSticky .= '<a class="ez-toc-close-icon" href="#" onclick="ezTOC_hideBar(event)" aria-label="×"><span aria-hidden="true">×</span></a>' . PHP_EOL;
					$htmlSticky .= '</div>' . PHP_EOL;
			} else {
				$htmlSticky .= '<div class="ez-toc-sticky-title-container">' . PHP_EOL;
				$htmlSticky .= '<a class="ez-toc-close-icon" href="#" onclick="ezTOC_hideBar(event)" aria-label="Close"><span aria-hidden="true">×</span></a>' . PHP_EOL;
				$htmlSticky .= '</div>' . PHP_EOL;
			}
			$htmlSticky  .= '<div id="ez-toc-sticky-container" class="ez-toc-sticky-container ' . implode( ' ', $classSticky ) . '">' . PHP_EOL;
			ob_start();
			do_action( 'ez_toc_sticky_toggle_before' );
			$htmlSticky .= ob_get_clean();
			$htmlSticky .= "<nav class='$ezTocStickyToggleDirection'>" . $this->getTOCList( "ez-toc-sticky" ) . "</nav>";
			ob_start();
			do_action( 'ez_toc_sticky_toggle_after' );
			$htmlSticky .= ob_get_clean();
			$htmlSticky .= '</div>' . PHP_EOL;						
		}
		return $htmlSticky;
	}

	/**
	 * Get the post TOC content block.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string
	 */
	public function getTOC($options = []) {

		$class = array( 'ez-toc-v' . str_replace( '.', '_', ezTOC::VERSION ) );
		$html  = '';

		if ( $this->hasTOCItems() ) {
			$wrapping_class_add = "";
			if(ezTOC_Option::get( 'toc_wrapping' )){
				$wrapping_class_add='-text';
			}

			$toc_align = get_post_meta( get_the_ID(), '_ez-toc-alignment', true );

			if ( !$toc_align || empty( $toc_align ) || $toc_align == 'none' ) {
				$toc_align = ezTOC_Option::get( 'wrapping' );
			}

			// wrapping css classes
			switch ( $toc_align ) {

				case 'left':
					$class[] = 'ez-toc-wrap-left'.esc_attr($wrapping_class_add);
					break;

				case 'right':
					$class[] = 'ez-toc-wrap-right'.esc_attr($wrapping_class_add);
					break;
				case 'center':
					$class[] = 'ez-toc-wrap-center';
					break;						
				case 'none':					
				default:
					// do nothing
			}

	        $show_counter = (isset($options['no_counter']) && $options['no_counter'] == true ) ? false : true;

	        $post_hide_counter = get_post_meta( get_the_ID(), '_ez-toc-hide_counter', true );

	        if($post_hide_counter){
	        	$show_counter = false;
	        }

	        if( $show_counter ){
	        	$hierarchical = ezTOC_Option::get( 'show_hierarchy' );
	        	if(isset($options['hierarchy'])){
	        		$hierarchical = true;
	        	}elseif(isset($options['no_hierarchy'])){
	        		$hierarchical = false;
	        	}

	            if ( $hierarchical ) {
	            	$class[] = 'counter-hierarchy';
	            } else {
	            	$class[] = 'counter-flat';
	            }
	            if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'ltr' ) {
	                $class[] = 'ez-toc-counter';
	            }
	            if( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) == 'rtl' ) {
	                $class[] = 'ez-toc-counter-rtl';
	            }
	        }

			// colour themes
			switch ( ezTOC_Option::get( 'theme' ) ) {

				case 'light-blue':
					$class[] = 'ez-toc-light-blue';
					break;

				case 'white':
					$class[] = 'ez-toc-white';
					break;

				case 'black':
					$class[] = 'ez-toc-black';
					break;

				case 'transparent':
					$class[] = 'ez-toc-transparent';
					break;

				case 'grey':
					$class[] = 'ez-toc-grey';
					break;

				case 'custom':
					$class[] = 'ez-toc-custom';
					break;
			}

			$custom_classes = ezTOC_Option::get( 'css_container_class', '' );			


            $class[] = 'ez-toc-container-direction';
			
			if ( 0 < strlen( $custom_classes ) ) {

				$custom_classes = explode( ' ', $custom_classes );
				$custom_classes = apply_filters( 'ez_toc_container_class', $custom_classes, $this );

				if ( is_array( $custom_classes ) ) {

					$class = array_merge( $class, $custom_classes );
				}
			}

			$class = array_filter( $class );
			$class = array_map( 'trim', $class );
			$class = array_map( 'sanitize_html_class', $class );

			$html .= '<div id="ez-toc-container" class="' . implode( ' ', $class ) . '">' . PHP_EOL;
                        
            if( ezTOC_Option::get( 'toc_loading' ) == 'js' ){
				$html .= $this->get_js_based_toc_heading($options);
			}else{
				$html .= $this->get_css_based_toc_heading($options);
			}            

			ob_start();
			do_action( 'ez_toc_before' );
			$html .= ob_get_clean();

			$html .= '<nav>' . $this->getTOCList('ez-toc', $options) . '</nav>';

			ob_start();
			do_action( 'ez_toc_after' );
			$html .= ob_get_clean();

			$html .= '</div>' . PHP_EOL;
			
		}

		return apply_filters('eztoc_autoinsert_final_toc_html',$html);
	}

	private function get_js_based_toc_heading($options){

		$html = '';						
		$html .= '<div class="ez-toc-title-container">' . PHP_EOL;
		$header_label = '';
		$show_header_text = ezTOC_Option::get( 'show_heading_text' );
		if(isset($options['label'])){
			$show_header_text = true;
		}elseif(isset($options['no_label'])){
			$show_header_text = false;
		}
		$read_time = array();
		if(isset($options['read_time'])){
			$read_time['read_time'] = $options['read_time'];
		}
	if ( $show_header_text ) {

		$toc_title = get_post_meta( get_the_ID(), '_ez-toc-header-label', true );

		if ( !$toc_title || empty( $toc_title ) ) {
			$toc_title = ezTOC_Option::get( 'heading_text' );
		}

		$toc_title_tag = ezTOC_Option::get( 'heading_text_tag' );
		$toc_title_tag = $toc_title_tag?$toc_title_tag:'p';

		if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== false ) {

			$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
		}

		if ( strpos( $toc_title, '%PAGE_NAME%' ) !== false ) {

			$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
		}
		if(isset($options['header_label'])){
			$toc_title = $options['header_label'];
		}
		$headerTextToggleClass = '';
		$headerTextToggleStyle = '';
		
		if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {
			$headerTextToggleClass = 'ez-toc-toggle';
			$headerTextToggleStyle = 'style="cursor: pointer"';
		}
		switch($toc_title_tag){
		
			case 'div':
				$header_label = '<div class="ez-toc-title ' . $headerTextToggleClass .'" ' . $headerTextToggleStyle . '>' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</div>' . PHP_EOL;
			break;
			case 'label':
				$header_label = '<label class="ez-toc-title ' . $headerTextToggleClass .'" ' . $headerTextToggleStyle . '>' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</label>' . PHP_EOL;
			break;
			case 'span':
				$header_label = '<span class="ez-toc-title ' . $headerTextToggleClass .'" ' . $headerTextToggleStyle . '>' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</span>' . PHP_EOL;
			break;
			default:
				$header_label = '<p class="ez-toc-title ' . $headerTextToggleClass .'" ' . $headerTextToggleStyle . '>' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</p>' . PHP_EOL;
			break;}
	
		$html .= $header_label;
													
	} 
	$html .= '<span class="ez-toc-title-toggle">';

	$label_below_html = '';
	$show_toggle_view = ezTOC_Option::get( 'visibility' );
	if(isset($options['toggle']) && $options['toggle'] == true){
		$show_toggle_view = true;
	}elseif(isset($options['no_toggle']) && $options['no_toggle'] == true){
		$show_toggle_view = false;
	}
	if ( $show_toggle_view ) {
								
		$icon = ezTOC::getTOCToggleIcon();
		if( function_exists( 'ez_toc_pro_activation_link' ) ) {
				$icon = apply_filters('ez_toc_modify_icon',$icon);
				$label_below_html = apply_filters('ez_toc_label_below_html',$label_below_html, $read_time);
		}							   
		$html .= '<a href="#" class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle" aria-label="Toggle Table of Content"><span class="ez-toc-js-icon-con">'.$icon.'</span></a>';
		 
	}
			$html .= '</span>';
			$html .= '</div>' . PHP_EOL;
			$html .= $label_below_html;
				
		return $html;
	}


	//css based heaing function
	private function get_css_based_toc_heading($options){

		$html = '';	
		$header_label = '';
		$show_header_text = true;
		if(isset($options['no_label']) && $options['no_label'] == true){
			$show_header_text = false;
		}
	if ( $show_header_text && ezTOC_Option::get( 'show_heading_text' ) ) {

		$toc_title = ezTOC_Option::get( 'heading_text' );
		$toc_title_tag = ezTOC_Option::get( 'heading_text_tag' );
		$toc_title_tag = $toc_title_tag?$toc_title_tag:'p';
		if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== false ) {

			$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
		}

		if ( strpos( $toc_title, '%PAGE_NAME%' ) !== false ) {

			$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
		}
					
		if(isset($options['header_label'])){
			$toc_title = $options['header_label'];
		}
		switch($toc_title_tag){
			case 'div':
				$header_label = '<div class="ez-toc-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</div>' . PHP_EOL;
			break;
			case 'label':
				$header_label = '<label class="ez-toc-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</label>' . PHP_EOL;
			break;
			case 'span':
				$header_label = '<span class="ez-toc-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</span>' . PHP_EOL;
			break;
			default:
				$header_label = '<p class="ez-toc-title">' . sprintf( '%s', htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' )). '</p>' . PHP_EOL;
			break;
		}
		if (!ezTOC_Option::get( 'visibility' ) ) {
			$html .='<div class="ez-toc-title-container">'.$header_label.'</div>';
		}															
	} 
	

	$show_toggle_view = true;
	if(isset($options['no_toggle']) && $options['no_toggle'] == true){
		$show_toggle_view = false;
	}

	if ( $show_toggle_view && ezTOC_Option::get( 'visibility' ) ) {
			$cssIconID = uniqid();
			
			$inputCheckboxExludeStyle = "";
			if ( ezTOC_Option::get( 'exclude_css' ) ) {
				$inputCheckboxExludeStyle = "style='display:none'";
			}
			$toggle_view='';
			if(ezTOC_Option::get('visibility_hide_by_default')==true){
					$toggle_view= "checked";
			}
			if( true == get_post_meta( $this->post->ID, '_ez-toc-visibility_hide_by_default', true ) ){
					$toggle_view= "checked";
			}
			if( $options !== null && !empty( $options ) && is_array( $options ) && key_exists( 'visibility_hide_by_default', $options ) && true == $options['visibility_hide_by_default'] ) {
					$toggle_view= "checked";
			}
			if( $options !== null && !empty( $options ) && is_array( $options ) && key_exists( 'visibility_hide_by_default', $options ) && false == $options['visibility_hide_by_default'] ) {
				$toggle_view= '';
		    }
			$toc_icon = ezTOC::getTOCToggleIcon();
		    $label_below_html = '';
		    $read_time = array();
		    if(isset($options['read_time']) && $options['read_time'] != ''){
		    	$read_time['read_time'] = $options['read_time'];
		    }
			if( function_exists( 'ez_toc_pro_activation_link' ) ) {
				$toc_icon = apply_filters('ez_toc_modify_icon',$toc_icon);
				$label_below_html = apply_filters('ez_toc_label_below_html',$label_below_html, $read_time);
		     }				
			if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {		
				$html .= '<label for="ez-toc-cssicon-toggle-item-' . $cssIconID . '" class="ez-toc-cssicon-toggle-label">' .$header_label. $toc_icon . '</label>'.$label_below_html.'<input type="checkbox" ' . $inputCheckboxExludeStyle . ' id="ez-toc-cssicon-toggle-item-' . $cssIconID . '" '.$toggle_view.' />';
			}else{
				if(function_exists('ez_toc_pro_inline_css_func')){
					$html .= '<div class="ez-toc-cssicon-toggle-label">'.$header_label.'<label for="ez-toc-cssicon-toggle-item-' . $cssIconID . '">' . $toc_icon . '</label></div>'.$label_below_html.'<input type="checkbox" ' . $inputCheckboxExludeStyle . ' id="ez-toc-cssicon-toggle-item-' . $cssIconID . '" '.$toggle_view.' aria-label="Toggle" />';
				}else{
					$html .= $header_label.'<label for="ez-toc-cssicon-toggle-item-' . $cssIconID . '" class="ez-toc-cssicon-toggle-label">' . $toc_icon . '</label>'.$label_below_html.'<input type="checkbox" ' . $inputCheckboxExludeStyle . ' id="ez-toc-cssicon-toggle-item-' . $cssIconID . '" '.$toggle_view.' aria-label="Toggle" />';
				}
				
				
			}
					
		}
		return $html;
	}
        
	/**
	 * Displays the post's TOC.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function toc() {
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in getTOC()
		echo $this->getTOC();
	}

	/**
	 * Generate the TOC list items for a given page within a post.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param int   $page    The page of the post to create the TOC items for.
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return string The HTML list of TOC items.
	 */
	private function createTOC( $page, $matches, $prefix = "ez-toc", $toc_more = array() ) {

		// Whether or not the TOC should be built flat or hierarchical.
		$hierarchical = ezTOC_Option::get( 'show_hierarchy' );

		if(isset($toc_more['hierarchy'])){
			$hierarchical = true;
		}elseif(isset($toc_more['no_hierarchy'])){
			$hierarchical = false;
		}

		$html = $toc_type = $collapse_status = '';

		if(isset($toc_more['collapse_hd'])){
			$collapse_status = true;
		}elseif(isset($toc_more['no_collapse_hd'])){
			$collapse_status = false;
		}

		$count_matches = is_array($matches) ? count($matches) : '';

		$toc_type = ezTOC_Option::get( 'toc_loading' );

		if ( $hierarchical ) {

			//To not show view more in Hierarchy
			unset($toc_more['view_more']);

			$current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
			$numbered_items     = array();
			$numbered_items_min = null;

			// find the minimum heading to establish our baseline
			foreach ( $matches as $i => $match ) {
				if ( $current_depth > $matches[ $i ][2] ) {
					$current_depth = (int) $matches[ $i ][2];
				}
			}

			$numbered_items[ $current_depth ] = 0;
			$numbered_items_min               = $current_depth;

			foreach ( $matches as $i => $match ) {

				$level = $matches[ $i ][2];
				$count = $i + 1;

				if ( $current_depth == (int) $matches[ $i ][2] ) {

					$html .= "<li class='{$prefix}-page-" . $page . " {$prefix}-heading-level-" . $current_depth . "'>";
				}

				// start lists
				if ( $current_depth != (int) $matches[ $i ][2] ) {

					for ( $current_depth; $current_depth < (int) $matches[ $i ][2]; $current_depth++ ) {

						$numbered_items[ $current_depth + 1 ] = 0;
						//Hide Level 4 Headings
						$sub_active = '';
						if($level > 3){
							$sub_active = apply_filters('ez_toc_hierarchy_js_add_attr', $sub_active, $collapse_status);
						}
						$html .= "<ul class='{$prefix}-list-level-" . $level . "' ".$sub_active."><li class='{$prefix}-heading-level-" . $level . "'>";
					}
				}

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				//check for line break
				if(!ezTOC_Option::get( 'prsrv_line_brk' )){
					$title = br2( $title, ' ' );
				}
				$title = wp_strip_all_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

				$html .= $this->createTOCItemAnchor( $matches[ $i ]['page'], $matches[ $i ]['id'], $title, $count );

				// end lists
				if ( $i != count( $matches ) - 1 ) {

					if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {

						for ( $current_depth; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth-- ) {

							$html .= '</li></ul>';
							$numbered_items[ $current_depth ] = 0;
						}
					}

					if ( $current_depth == (int) $matches[ $i + 1 ][2] ) {

						$html .= '</li>';
					}

				} else {

					// this is the last item, make sure we close off all tags
					for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {

						$html .= '</li>';

						if ( $current_depth != $numbered_items_min ) {
							$html .= '</ul>';
						}
					}
				}
			}

		} else {
			if(isset($toc_more['view_more']) && $toc_more['view_more']>0){
				//No. of Headings
				$no_of_headings = $toc_more['view_more'];
				if(is_array($matches)){
					foreach ( $matches as $i => $match ) {
						$count = $i + 1;
						$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
						$title = wp_strip_all_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );
						if($count <= $no_of_headings){
							$html .= "<li class='{$prefix}-page-" . $page . "'>";
							$html .= $this->createTOCItemAnchor( $matches[ $i ]['page'], $matches[ $i ]['id'], $title, $count );
							$html .= '</li>';
						}else{
							$detect = '';
							$is_more_last = false;
							if('css' == $toc_type && $i == $no_of_headings && function_exists('ez_toc_non_amp') && ez_toc_non_amp()){
								$html .= '</ul><input type="checkbox" id="ez-toc-more-toggle-css"/><ul class="ez-toc-more-wrp" style="--start: '.$i.'">';
							}
							if($i == count($matches)-1){
								$detect = 'm-last';
								$is_more_last = true;
							}
							$html .= "<li class='{$prefix}-page-" . $page . " ez-toc-more-link " . $detect . "'>";
							$html .= $this->createTOCItemAnchor( $matches[ $i ]['page'], $matches[ $i ]['id'], $title, $count );
							$html .= '</li>';
							if($is_more_last && 'css' == $toc_type && function_exists('ez_toc_non_amp') && ez_toc_non_amp()){
								$html .= '</ul>';
							}
						}
					}
				}
			}else{
				if(is_array($matches)){
					foreach ( $matches as $i => $match ) {
						$count = $i + 1;
						$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
						$title = wp_strip_all_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );
						$html .= "<li class='{$prefix}-page-" . $page . "'>";
						$html .= $this->createTOCItemAnchor( $matches[ $i ]['page'], $matches[ $i ]['id'], $title, $count );
						$html .= '</li>';
					}
				}
			}
		}

		$html = apply_filters('ez_toc_pro_html_modifier', $html, $toc_more, $count_matches, $toc_type);

		return do_shortcode($html);
	}

	/**
	 * @access private
	 * @since  2.0
	 *
	 * @param int    $page
	 * @param string $id
	 * @param string $title
	 * @param int    $count
	 *
	 * @return string
	 */
	private function createTOCItemAnchor( $page, $id, $title, $count ) {
		if (ezTOC_Option::get( 'remove_special_chars_from_title' )) {
			$title = str_replace(':', '', $title);
		}
		
		$anch_name = 'href';
		if(ezTOC_Option::get( 'toc_loading' ) == 'js' && ezTOC_Option::get( 'smooth_scroll' ) && ezTOC_Option::get( 'avoid_anch_jump' )){
			$anch_name = 'href="#" data-href';
		}

		return sprintf(
			'<a class="ez-toc-link ez-toc-heading-' . $count . '" '.$anch_name.'="%1$s" title="%2$s">%3$s</a>',
			esc_url( $this->createTOCItemURL( $id, $page ) ),
			esc_attr( wp_strip_all_tags( $title ) ),
			$title
		);
	}

	/**
	 * @access private
	 * @since  2.0
	 *
	 * @param string $id
	 * @param int    $page
	 *
	 * @return string
	 */
	private function createTOCItemURL( $id, $page ) {

		$current_post = $this->post->ID === $this->queriedObjectID;
		$current_page = $this->getCurrentPage();

		$anch_url = $this->permalink;

		//Ajax Load more 
		//@since 2.0.61
		if(ezTOC_Option::get( 'ajax_load_more' ) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			$anch_url = $_SERVER['HTTP_REFERER'];
		}

		if ( $page === $current_page && $current_post ) {

			return (ezTOC_Option::get( 'add_request_uri' ) ? $_SERVER['REQUEST_URI'] : '') . '#' . $id;

		} elseif ( 1 === $page ) {
			// Fix for wrong links on TOC on Wordpress category page
			if(is_category() || is_tax() || is_tag() || (function_exists('is_product_category') && is_product_category())){
				return  '#' . $id;
			}
			return trailingslashit( $anch_url ) . '#' . $id;

		}

		return trailingslashit( $anch_url ) . $page . '/#' . $id;
	}

	/**
	 * Strip Shortcodes but keeping its content.
	 *
	 * @access private
	 * @since  2.0.67
	 *
	 * @param string $content The post content.
	 *
	 * @return string The post content without shortcodes.
	 */
	private function stripShortcodesButKeepContent($content) {
		// Regex pattern to match the specific shortcodes
		$shortcodes = apply_filters('ez_toc_strip_shortcodes_with_inner_content',[]);
		if(!empty($shortcodes) && is_array($shortcodes)){
			
		$pattern = '/\[('.implode('|',$shortcodes).')(?:\s[^\]]*)?\](.*?)\[\/\1\]|\[('.implode('|',$shortcodes).')(?:\s[^\]]*)?\/?\]/s';
	
		// Function to recursively strip shortcodes
		while (preg_match($pattern, $content)) {
			$content = preg_replace_callback($pattern, function($matches) {
				if (isset($matches[2])) {
					return $matches[2]; // Keep content inside shortcode
				}
	   
				return ''; // Remove self-closing shortcode
			}, $content);
		}
		
		}
		return $content;
	}
}