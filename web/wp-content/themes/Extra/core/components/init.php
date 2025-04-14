<?php
// phpcs:disable Generic.WhiteSpace.ScopeIndent -- our preference is to not indent the whole inner function in this scenario.
if ( ! function_exists( 'et_core_init' ) ) :
/**
 * {@see 'plugins_loaded' (9999999) Must run after cache plugins have been loaded.}
 */
function et_core_init() {
	ET_Core_API_Spam_Providers::instance();
	ET_Core_Cache_Directory::instance();
	ET_Core_PageResource::startup();
	ET_Core_CompatibilityWarning::instance();

	if ( defined( 'ET_CORE_UPDATED' ) ) {
		global $wp_rewrite;
		add_action( 'shutdown', array( $wp_rewrite, 'flush_rules' ) );

		update_option( 'et_core_page_resource_remove_all', true );
	}

	$cache_dir = ET_Core_PageResource::get_cache_directory();

	if ( file_exists( $cache_dir . '/DONOTCACHEPAGE' ) ) {
		! defined( 'DONOTCACHEPAGE' ) ? define( 'DONOTCACHEPAGE', true ) : '';
		@unlink( $cache_dir . '/DONOTCACHEPAGE' );
	}

	if ( get_option( 'et_core_page_resource_remove_all' ) ) {
		ET_Core_PageResource::remove_static_resources( 'all', 'all', true );
	}
}
endif;

if ( ! function_exists( 'et_core_site_has_builder' ) ) :
/**
 * Check is `et_core_site_has_builder` allowed.
 * We can clear cache managed by 3rd party plugins only
 * if Divi, Extra, or the Divi Builder plugin
 * is active when the core was called.
 *
 * @return boolean
 */
function et_core_site_has_builder() {
	global $shortname;

	$core_path                     = get_transient( 'et_core_path' );
	$is_divi_builder_plugin_active = false;

	if ( ! empty( $core_path ) && false !== strpos( $core_path, '/divi-builder/' ) && function_exists('is_plugin_active') ) {
		$is_divi_builder_plugin_active = is_plugin_active( 'divi-builder/divi-builder.php' );
	}

	if( $is_divi_builder_plugin_active || in_array( $shortname, array( 'divi', 'extra' ) ) ) {
		return true;
	}

	return false;
}
endif;

if ( ! function_exists( 'et_core_clear_wp_cache' ) ):
function et_core_clear_wp_cache( $post_id = '' ) {
	if ( ( ! wp_doing_cron() && ! et_core_security_check_passed( 'edit_posts' ) ) || ! et_core_site_has_builder() ) {
		return;
	}

	try {
		// Cache Plugins
		// Comet Cache
		if ( is_callable( 'comet_cache::clear' ) ) {
			comet_cache::clear();
		}

		// WP Rocket
		if ( function_exists( 'rocket_clean_post' ) ) {
			if ( '' !== $post_id ) {
				rocket_clean_post( $post_id );
			} else if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}

		// W3 Total Cache
		if ( has_action( 'w3tc_flush_post' ) ) {
			'' !== $post_id ? do_action( 'w3tc_flush_post', $post_id ) : do_action( 'w3tc_flush_posts' );
		}

		// WP Super Cache
		if ( function_exists( 'wp_cache_debug' ) && defined( 'WPCACHEHOME' ) ) {
			include_once WPCACHEHOME . 'wp-cache-phase1.php';
			include_once WPCACHEHOME . 'wp-cache-phase2.php';

			if ( '' !== $post_id && function_exists( 'clear_post_supercache' ) ) {
				clear_post_supercache( $post_id );
			} else if ( '' === $post_id && function_exists( 'wp_cache_clear_cache_on_menu' ) ) {
				wp_cache_clear_cache_on_menu();
			}
		}

		// WP Fastest Cache
		if ( isset( $GLOBALS['wp_fastest_cache'] ) ) {
			if ( '' !== $post_id && method_exists( $GLOBALS['wp_fastest_cache'], 'singleDeleteCache' ) ) {
				$GLOBALS['wp_fastest_cache']->singleDeleteCache( $post_id );
			} else if ( '' === $post_id && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
				$GLOBALS['wp_fastest_cache']->deleteCache();
			}
		}

		// Hummingbird
		if ( has_action( 'wphb_clear_page_cache' ) ) {
			'' !== $post_id ? do_action( 'wphb_clear_page_cache', $post_id ) : do_action( 'wphb_clear_page_cache' );
		}

		// WordPress Cache Enabler
		if ( has_action( 'ce_clear_cache' ) ) {
			'' !== $post_id ? do_action( 'ce_clear_post_cache', $post_id ) : do_action( 'ce_clear_cache' );
		}

		// LiteSpeed Cache v3.0+.
		if ( '' !== $post_id && has_action( 'litespeed_purge_post' ) ) {
			do_action( 'litespeed_purge_post', $post_id );
		} elseif ( '' === $post_id && has_action( 'litespeed_purge_all' ) ) {
			do_action( 'litespeed_purge_all' );
		}

		// LiteSpeed Cache v1.1.3 until v3.0.
		if ( '' !== $post_id && function_exists( 'litespeed_purge_single_post' ) ) {
			litespeed_purge_single_post( $post_id );
		} elseif ( '' === $post_id && is_callable( 'LiteSpeed_Cache_API::purge_all' ) ) {
			LiteSpeed_Cache_API::purge_all();
		} elseif ( is_callable( 'LiteSpeed_Cache::get_instance' ) ) {
			// LiteSpeed Cache v1.1.3 below. LiteSpeed_Cache still exist on v2.9.9.2, but no
			// longer exist on v3.0. Keep it here as backward compatibility for lower version.
			$litespeed = LiteSpeed_Cache::get_instance();

			if ( '' !== $post_id && method_exists( $litespeed, 'purge_post' ) ) {
				$litespeed->purge_post( $post_id );
			} else if ( '' === $post_id && method_exists( $litespeed, 'purge_all' ) ) {
				$litespeed->purge_all();
			}
		}

		// Hyper Cache
		if ( class_exists( 'HyperCache' ) && isset( HyperCache::$instance ) ) {
			if ( '' !== $post_id && method_exists( HyperCache::$instance, 'clean_post' ) ) {
				HyperCache::$instance->clean_post( $post_id );
			} else if ( '' === $post_id && method_exists( HyperCache::$instance, 'clean' ) ) {
				HyperCache::$instance->clean_post( $post_id );
			}
		}

		// Hosting Provider Caching
		// Pantheon Advanced Page Cache
		$pantheon_clear     = 'pantheon_wp_clear_edge_keys';
		$pantheon_clear_all = 'pantheon_wp_clear_edge_all';
		if ( function_exists( $pantheon_clear ) || function_exists( $pantheon_clear_all ) ) {
			if ( '' !== $post_id && function_exists( $pantheon_clear ) ) {
				pantheon_wp_clear_edge_keys( array( "post-{$post_id}" ) );
			} else if ( '' === $post_id && function_exists( $pantheon_clear_all ) ) {
				pantheon_wp_clear_edge_all();
			}
		}

		// Siteground
		if ( isset( $GLOBALS['sg_cachepress_supercacher'] ) ) {
			global $sg_cachepress_supercacher;

			if ( is_object( $sg_cachepress_supercacher ) && method_exists( $sg_cachepress_supercacher, 'purge_cache' ) ) {
				$sg_cachepress_supercacher->purge_cache( true );
			}

		} else if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}

		// WP Engine
		if ( class_exists( 'WpeCommon' ) ) {
			is_callable( 'WpeCommon::purge_memcached' ) ? WpeCommon::purge_memcached() : '';
			is_callable( 'WpeCommon::clear_maxcdn_cache' ) ? WpeCommon::clear_maxcdn_cache() : '';
			is_callable( 'WpeCommon::purge_varnish_cache' ) ? WpeCommon::purge_varnish_cache() : '';

			if ( is_callable( 'WpeCommon::instance' ) && $instance = WpeCommon::instance() ) {
				method_exists( $instance, 'purge_object_cache' ) ? $instance->purge_object_cache() : '';
			}
		}

		// Bluehost
		if ( class_exists( 'Endurance_Page_Cache' ) ) {
			wp_doing_ajax() ? ET_Core_LIB_BluehostCache::get_instance()->clear( $post_id ) : do_action( 'epc_purge' );
		}

		// Pressable.
		if ( isset( $GLOBALS['batcache'] ) && is_object( $GLOBALS['batcache'] ) ) {
			wp_cache_flush();
		}

		// Cloudways - Breeze.
		if ( class_exists( 'Breeze_Admin' ) ) {
			$breeze_admin = new Breeze_Admin();
			$breeze_admin->breeze_clear_all_cache();
		}

		// Kinsta.
		if ( class_exists( '\Kinsta\Cache' ) && isset( $GLOBALS['kinsta_cache'] ) && is_object( $GLOBALS['kinsta_cache'] ) ) {
			global $kinsta_cache;

			if ( isset( $kinsta_cache->kinsta_cache_purge ) && method_exists( $kinsta_cache->kinsta_cache_purge, 'purge_complete_caches' ) ) {
				$kinsta_cache->kinsta_cache_purge->purge_complete_caches();
			}
		}

		// GoDaddy.
		if ( class_exists( '\WPaaS\Cache' ) ) {
			global $wpaas_cache_class;

			// Since GD System Plugin 4.51.1 the cache class instance can be accessed
			// with $wpaas_cache_class global. In addition to this, the 'has_ban' method
			// is no longer static. To cover both static and non-static versions we
			// can test if $wpaas_cache_class exists and use the correct type accordingly.
			$has_ban = $wpaas_cache_class ? $wpaas_cache_class->has_ban() : \WPaaS\Cache::has_ban();

			if ( ! $has_ban ) {
				$gd_cache_class = $wpaas_cache_class ? $wpaas_cache_class : '\WPaaS\Cache';

				remove_action( 'shutdown', array( $gd_cache_class, 'purge' ), PHP_INT_MAX );
				add_action( 'shutdown', array( $gd_cache_class, 'ban' ), PHP_INT_MAX );
			}
		}

		// Complimentary Performance Plugins.
		// Autoptimize.
		if ( is_callable( 'autoptimizeCache::clearall' ) ) {
			autoptimizeCache::clearall();
		}

		// WP Optimize.
		if ( class_exists( 'WP_Optimize' ) && defined( 'WPO_PLUGIN_MAIN_PATH' ) ) {
			if ( '' !== $post_id && is_callable( 'WPO_Page_Cache::delete_single_post_cache' ) ) {
				WPO_Page_Cache::delete_single_post_cache( $post_id );
			} elseif ( is_callable( array( 'WP_Optimize', 'get_page_cache' ) ) && is_callable( array( WP_Optimize()->get_page_cache(), 'purge' ) ) ) {
				WP_Optimize()->get_page_cache()->purge();
			}
		}
	} catch( Exception $err ) {
		ET_Core_Logger::error( 'An exception occurred while attempting to clear site cache.' );
	}
}
endif;


if ( ! function_exists( 'et_core_get_nonces' ) ):
/**
 * Returns the nonces for this component group.
 *
 * @return string[]
 */
function et_core_get_nonces() {
	static $nonces = null;

	return $nonces ? $nonces : $nonces = array(
		'clear_page_resources_nonce' => wp_create_nonce( 'clear_page_resources' ),
		'et_core_portability_export' => wp_create_nonce( 'et_core_portability_export' ),
	);
}
endif;


if ( ! function_exists( 'et_core_page_resource_auto_clear' ) ):
function et_core_page_resource_auto_clear() {
	ET_Core_PageResource::remove_static_resources( 'all', 'all' );
}
add_action( 'switch_theme', 'et_core_page_resource_auto_clear' );
add_action( 'activated_plugin', 'et_core_page_resource_auto_clear', 10, 0 );
add_action( 'deactivated_plugin', 'et_core_page_resource_auto_clear', 10, 0 );
endif;


if ( ! function_exists( 'et_core_page_resource_clear' ) ):
/**
 * Ajax handler for clearing cached page resources.
 */
function et_core_page_resource_clear() {
	et_core_security_check( 'manage_options', 'clear_page_resources' );

	if ( empty( $_POST['et_post_id'] ) ) {
		et_core_die();
	}

	$post_id = sanitize_key( $_POST['et_post_id'] );
	$owner   = sanitize_key( $_POST['et_owner'] );

	ET_Core_PageResource::remove_static_resources( $post_id, $owner );
}
add_action( 'wp_ajax_et_core_page_resource_clear', 'et_core_page_resource_clear' );
endif;


if ( ! function_exists( 'et_core_page_resource_get' ) ):
/**
 * Get a page resource instance.
 *
 * @param string     $owner    The owner of the instance (core|divi|builder|bloom|monarch|custom).
 * @param string     $slug     A string that uniquely identifies the resource.
 * @param string|int $post_id  The post id that the resource is associated with or `global`.
 *                             If `null`, the return value of {@link get_the_ID()} will be used.
 * @param string     $type     The resource type (style|script). Default: `style`.
 * @param string     $location Where the resource should be output (head|footer). Default: `head-late`.
 *
 * @return ET_Core_PageResource
 */
function et_core_page_resource_get( $owner, $slug, $post_id = null, $priority = 10, $location = 'head-late', $type = 'style' ) {
	$post_id = $post_id ? $post_id : et_core_page_resource_get_the_ID();
	$_slug   = "et-{$owner}-{$slug}-{$post_id}-cached-inline-{$type}s";

	$all_resources = ET_Core_PageResource::get_resources();

	return isset( $all_resources[ $_slug ] )
		? $all_resources[ $_slug ]
		: new ET_Core_PageResource( $owner, $slug, $post_id, $priority, $location, $type );
}
endif;


if ( ! function_exists( 'et_core_page_resource_get_the_ID' ) ):
function et_core_page_resource_get_the_ID() {
	static $post_id = null;

	if ( is_int( $post_id ) ) {
		return $post_id;
	}

	return $post_id = apply_filters( 'et_core_page_resource_current_post_id', get_the_ID() );
}
endif;


if ( ! function_exists( 'et_core_page_resource_is_singular' ) ):
function et_core_page_resource_is_singular() {
	return apply_filters( 'et_core_page_resource_is_singular', is_singular() );
}
endif;


if ( ! function_exists( 'et_debug' ) ):
function et_debug( $msg, $bt_index = 4, $log_ajax = true ) {
	ET_Core_Logger::debug( $msg, $bt_index, $log_ajax );
}
endif;


if ( ! function_exists( 'et_wrong' ) ):
function et_wrong( $msg, $error = false ) {
	$msg = "You're Doing It Wrong! {$msg}";

	if ( $error ) {
		et_error( $msg );
	} else {
		et_debug( $msg );
	}
}
endif;


if ( ! function_exists( 'et_error' ) ):
function et_error( $msg, $bt_index = 4 ) {
	ET_Core_Logger::error( "[ERROR]: {$msg}", $bt_index );
}
endif;
