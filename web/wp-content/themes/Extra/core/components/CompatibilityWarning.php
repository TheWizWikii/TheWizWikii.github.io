<?php
/**
 * ET_Core_CompatibilityWarning class file.
 *
 * @class   ET_Core_CompatibilityWarning
 * @package Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ET_Core_CompatibilityWarning' ) ) :

	/**
	 * Plugin & theme compatibility warning system backported from WP 5.5.
	 *
	 * This system is only intended for users who use Divi theme with WP 5.4.2 below and Divi
	 * Builder plugin with WP 5.2 below. There are some areas where it overrides or modifies
	 * the themes & plugins management template to show warnings when the current WP and/or
	 * PHP versions doesn't work with the current and/or upcoming ET plugins/themes.
	 *
	 * A. Update Core
	 *    1. Plugins list section.
	 *    2. Themes list section.
	 *
	 * B. Manage Themes
	 *    On each of incompatible themes, shows warning and disables Activate & Live Preview
	 *    buttons when the theme is not activated yet.
	 *    1. Themes List (tmpl-theme).
	 *    2. Theme Details (tmpl-theme-single).
	 *
	 * C. Theme Customizer
	 *    On each of incompatible themes, shows warning and disables Live Preview button when
	 *    the theme is not activated yet. In addition, disable Publish button for the current
	 *    active theme.
	 *    1. Themes List (tmpl-theme).
	 *    2. Theme Details (tmpl-theme-single).
	 *    3. Publish Button for current active theme.
	 *
	 * D. Plugins List
	 *    1. Plugins List via `after_plugin_row_` action hook.
	 *    2. Plugin activation warning. ET plugins that want to use this warning should
	 *       register `maybe_deactivate_incompatible_plugin()` on their activation hook.
	 *
	 * @since 4.7.0
	 */
	class ET_Core_CompatibilityWarning {

		/**
		 * Class instance.
		 *
		 * @var ET_Core_CompatibilityWarning
		 */
		public static $instance_class;

		/**
		 * WP version where the official warning system is introduced.
		 *
		 * @var string
		 */
		public $supported_wp_version = array(
			'plugin' => '5.3.0',
			'theme'  => '5.5.0',
		);

		/**
		 * Class constructor.
		 */
		public function __construct() {
			global $wp_version;

			// Ensure the system is loaded on lower version of supported version.
			if ( version_compare( $wp_version, $this->supported_wp_version[ ET_CORE_TYPE ], '>=' ) ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// A. Update Core - Overrides plugins and themes updates table body.
			add_action( 'admin_print_footer_scripts-update-core.php', array( $this, 'overrides_update_core_plugins_table_body' ) );
			add_action( 'admin_print_footer_scripts-update-core.php', array( $this, 'overrides_update_core_themes_table_body' ) );

			// B. Manage Themes - Overrides themes list & details templates.
			add_filter( 'wp_prepare_themes_for_js', array( $this, 'set_theme_additional_properties' ) );
			add_action( 'admin_print_footer_scripts-themes.php', array( $this, 'overrides_tmpl_theme' ) );

			// C. Theme Customizer - Overrides themes list & details templates.
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'overrides_tmpl_customize_control_theme_content' ) );

			// D. Plugins - Overrides current plugin list. Default priority is 20.
			add_action( 'load-plugins.php', array( $this, 'overrides_plugins_table_rows' ), 21 );
		}

		/**
		 * Returns instance of the class.
		 *
		 * @since 4.7.0
		 *
		 * @return ET_Core_CompatibilityWarning
		 */
		public static function instance() {
			if ( ! isset( self::$instance_class ) ) {
				self::$instance_class = new self();
			}

			return self::$instance_class;
		}

		/**
		 * Set theme additional properties before it's rendered on Manage Themes page.
		 *
		 * @since 4.7.0
		 *
		 * @param array $prepared_themes List of available themes.
		 *
		 * @return array
		 */
		public function set_theme_additional_properties( $prepared_themes ) {
			// Bail early if the $prepared_themes is empty.
			if ( empty( $prepared_themes ) ) {
				return $prepared_themes;
			}

			// 1. Get available themes update.
			$theme_updates = array();

			if ( current_user_can( 'update_themes' ) ) {
				$updates_themes_transient = get_site_transient( 'update_themes' );

				if ( isset( $updates_themes_transient->response ) ) {
					$theme_updates = $updates_themes_transient->response;
				}
			}

			// 2. Assign compatibility properties.
			$themes = $prepared_themes;

			foreach ( $themes as $theme_slug => $theme_info ) {
				// Ensure style.css file exist.
				$theme_root = get_theme_root( $theme_slug );
				$theme_file = "{$theme_root}/{$theme_slug}/style.css";

				if ( ! file_exists( $theme_file ) ) {
					continue;
				}

				// Get WP & PHP compatibility info.
				$theme_headers = get_file_data(
					$theme_file,
					array(
						'RequiresWP'  => 'Requires at least',
						'RequiresPHP' => 'Requires PHP',
					),
					'theme'
				);

				$require_wp          = et_()->array_get( $theme_headers, 'RequiresWP', null );
				$require_php         = et_()->array_get( $theme_headers, 'RequiresPHP', null );
				$update_requires_wp  = et_()->array_get( $theme_updates, array( $theme_slug, 'requires' ), null );
				$update_requires_php = et_()->array_get( $theme_updates, array( $theme_slug, 'requires_php' ), null );

				$compatibility_properties = array(
					'compatibleWP'   => is_wp_version_compatible( $require_wp ),
					'compatiblePHP'  => is_php_version_compatible( $require_php ),
					'updateResponse' => array(
						'compatibleWP'  => is_wp_version_compatible( $update_requires_wp ),
						'compatiblePHP' => is_php_version_compatible( $update_requires_php ),
					),
				);

				$prepared_themes[ $theme_slug ] = array_merge( $prepared_themes[ $theme_slug ], $compatibility_properties );
			}

			return $prepared_themes;
		}

		/**
		 * Get plugins data for Update Core page.
		 *
		 * The data processing is backported from WP 5.5 with few modification.
		 *
		 * @see {list_plugin_updates()} of WP 5.5
		 *
		 * @since 4.7.0
		 *
		 * @return array
		 */
		public function get_update_core_plugins_data() {
			$plugin_updates   = get_plugin_updates();
			$plugin_processed = array();

			// Bail early if there is no plugin updates.
			if ( empty( $plugin_updates ) ) {
				return array();
			}

			foreach ( $plugin_updates as $plugin_file => $plugin_data ) {
				// reason: The properties come from WP plugin data.
				// phpcs:disable ET.Sniffs.ValidVariableName.UsedPropertyNotSnakeCase
				$plugin_name    = $plugin_data->Name;
				$plugin_version = $plugin_data->Version;
				// phpcs:enable

				// a. Get current and update WP version.
				$wp_version     = get_bloginfo( 'version' );
				$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );

				$core_updates = get_core_updates();
				if ( ! isset( $core_updates[0]->response ) || 'latest' === $core_updates[0]->response || 'development' === $core_updates[0]->response || version_compare( $core_updates[0]->current, $cur_wp_version, '=' ) ) {
					$core_update_version = false;
				} else {
					$core_update_version = $core_updates[0]->current;
				}

				// b. Check PHP versions compatibility. WP doesn't check WP versions compatibility.
				$requires_php   = isset( $plugin_data->update->requires_php ) ? $plugin_data->update->requires_php : null;
				$compatible_php = is_php_version_compatible( $requires_php );

				// c. Icon.
				$icon            = '<span class="dashicons dashicons-admin-plugins"></span>';
				$preferred_icons = array( 'svg', '2x', '1x', 'default' );
				foreach ( $preferred_icons as $preferred_icon ) {
					if ( ! empty( $plugin_data->update->icons[ $preferred_icon ] ) ) {
						$icon = '<img src="' . esc_url( $plugin_data->update->icons[ $preferred_icon ] ) . '" alt="" />';
						break;
					}
				}

				// d. Process compatibility warning text.
				// Get plugin compat for running version of WordPress.
				if ( isset( $plugin_data->update->tested ) && version_compare( $plugin_data->update->tested, $cur_wp_version, '>=' ) ) {
					/* translators: %s: WordPress version. */
					$compat = '<br />' . sprintf( __( 'Compatibility with WordPress %s: 100%% (according to its author)' ), $cur_wp_version );
				} else {
					/* translators: %s: WordPress version. */
					$compat = '<br />' . sprintf( __( 'Compatibility with WordPress %s: Unknown' ), $cur_wp_version );
				}

				// Get plugin compat for updated version of WordPress.
				if ( $core_update_version ) {
					if ( isset( $plugin_data->update->tested ) && version_compare( $plugin_data->update->tested, $core_update_version, '>=' ) ) {
						/* translators: %s: WordPress version. */
						$compat .= '<br />' . sprintf( __( 'Compatibility with WordPress %s: 100%% (according to its author)' ), $core_update_version );
					} else {
						/* translators: %s: WordPress version. */
						$compat .= '<br />' . sprintf( __( 'Compatibility with WordPress %s: Unknown' ), $core_update_version );
					}
				}

				// Get plugin compat for updated version of PHP.
				if ( ! $compatible_php && current_user_can( 'update_php' ) ) {
					$compat .= '<br>' . __( 'This update doesn&#8217;t work with your version of PHP.' ) . '&nbsp;';
					$compat .= sprintf(
						/* translators: %s: URL to Update PHP page. */
						__( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);

					$annotation = wp_get_update_php_annotation();

					if ( $annotation ) {
						$compat .= '</p><p><em>' . $annotation . '</em>';
					}
				}

				// Get the upgrade notice for the new plugin version.
				if ( isset( $plugin_data->update->upgrade_notice ) ) {
					$upgrade_notice = '<br />' . wp_strip_all_tags( $plugin_data->update->upgrade_notice );
				} else {
					$upgrade_notice = '';
				}

				$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data->update->slug . '&section=changelog&TB_iframe=true&width=640&height=662' );
				$details     = sprintf(
					'<a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">%3$s</a>',
					esc_url( $details_url ),
					/* translators: 1: Plugin name, 2: Version number. */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $plugin_data->update->new_version ) ),
					/* translators: %s: Plugin version. */
					sprintf( __( 'View version %s details.' ), $plugin_data->update->new_version )
				);

				$checkbox_id = 'checkbox_' . md5( $plugin_name );

				// Plugin template properties. There is no compatible_wp property passed here.
				$plugin_processed[ $plugin_file ] = array(
					'plugin_file'    => esc_attr( $plugin_file ),
					'name'           => esc_attr( $plugin_name ),
					'checkbox_id'    => esc_attr( 'checkbox_' . md5( $plugin_name ) ),
					'icon'           => et_core_intentionally_unescaped( $icon, 'html' ),
					'version'        => esc_attr( $plugin_version ),
					'new_version'    => esc_attr( $plugin_data->update->new_version ),
					'compatible_php' => $compatible_php,
					'compat'         => et_core_intentionally_unescaped( $compat, 'html' ),
					'upgrade_notice' => et_core_intentionally_unescaped( $upgrade_notice, 'html' ),
					'details'        => et_core_intentionally_unescaped( $details, 'html' ),
				);
			}

			return $plugin_processed;
		}

		/**
		 * Get themes data for Update Core page.
		 *
		 * @since 4.7.0
		 *
		 * @return array
		 */
		public function get_update_core_themes_data() {
			$theme_updates   = get_theme_updates();
			$theme_processed = array();

			// Bail early if there is no theme updates.
			if ( empty( $theme_updates ) ) {
				return array();
			}

			foreach ( $theme_updates as $stylesheet => $theme ) {
				// a. Check compatibility.
				$requires_wp    = et_()->array_get( $theme->update, 'requires', null );
				$requires_php   = et_()->array_get( $theme->update, 'requires_php', null );
				$compatible_wp  = is_wp_version_compatible( $requires_wp );
				$compatible_php = is_php_version_compatible( $requires_php );

				// b. Process compatibility warning text.
				$compat = '';

				if ( ! $compatible_wp && ! $compatible_php ) {
					$compat .= '<br>' . __( 'This update doesn&#8217;t work with your versions of WordPress and PHP.' ) . '&nbsp;';
					if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
						$compat .= sprintf(
							/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
							__( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
							esc_url( self_admin_url( 'update-core.php' ) ),
							esc_url( wp_get_update_php_url() )
						);

						$annotation = wp_get_update_php_annotation();

						if ( $annotation ) {
							$compat .= '</p><p><em>' . $annotation . '</em>';
						}
					} elseif ( current_user_can( 'update_core' ) ) {
						$compat .= sprintf(
							/* translators: %s: URL to WordPress Updates screen. */
							__( '<a href="%s">Please update WordPress</a>.' ),
							esc_url( self_admin_url( 'update-core.php' ) )
						);
					} elseif ( current_user_can( 'update_php' ) ) {
						$compat .= sprintf(
							/* translators: %s: URL to Update PHP page. */
							__( '<a href="%s">Learn more about updating PHP</a>.' ),
							esc_url( wp_get_update_php_url() )
						);

						$annotation = wp_get_update_php_annotation();

						if ( $annotation ) {
							$compat .= '</p><p><em>' . $annotation . '</em>';
						}
					}
				} elseif ( ! $compatible_wp ) {
					$compat .= '<br>' . __( 'This update doesn&#8217;t work with your version of WordPress.' ) . '&nbsp;';
					if ( current_user_can( 'update_core' ) ) {
						$compat .= sprintf(
							/* translators: %s: URL to WordPress Updates screen. */
							__( '<a href="%s">Please update WordPress</a>.' ),
							esc_url( self_admin_url( 'update-core.php' ) )
						);
					}
				} elseif ( ! $compatible_php ) {
					$compat .= '<br>' . __( 'This update doesn&#8217;t work with your version of PHP.' ) . '&nbsp;';
					if ( current_user_can( 'update_php' ) ) {
						$compat .= sprintf(
							/* translators: %s: URL to Update PHP page. */
							__( '<a href="%s">Learn more about updating PHP</a>.' ),
							esc_url( wp_get_update_php_url() )
						);

						$annotation = wp_get_update_php_annotation();

						if ( $annotation ) {
							$compat .= '</p><p><em>' . $annotation . '</em>';
						}
					}
				}

				// Theme template properties.
				$theme_processed[ $stylesheet ] = array(
					'stylesheet'     => esc_attr( $stylesheet ),
					'name'           => esc_attr( $theme->display( 'Name' ) ),
					'checkbox_id'    => esc_attr( 'checkbox_' . md5( $theme->get( 'Name' ) ) ),
					'screenshot'     => esc_url( $theme->get_screenshot() ),
					'version'        => esc_attr( $theme->display( 'Version' ) ),
					'new_version'    => esc_attr( et_()->array_get( $theme->update, 'new_version', '' ) ),
					'compatible_wp'  => $compatible_wp,
					'compatible_php' => $compatible_php,
					'compat'         => et_core_esc_previously( $compat ),
				);
			}

			return $theme_processed;
		}

		/**
		 * Enqueue compatibility warning scripts and its local data.
		 *
		 * @since 4.7.0
		 */
		public function enqueue_scripts() {
			global $pagenow, $wp_customize;

			// Bail early if the current page is not one of the allowed pages.
			$allowed_pages = array(
				'update-core.php',
				'customize.php',
				'themes.php',
			);

			if ( ! in_array( $pagenow, $allowed_pages, true ) ) {
				return;
			}

			// Enqueue main scripts.
			wp_enqueue_script( 'et_compatibility_warning_script', ET_CORE_URL . 'admin/js/compatibility-warning.js', array( 'jquery' ), ET_CORE_VERSION, true );

			$compatibility_warning = array();

			if ( 'update-core.php' === $pagenow ) {
				$compatibility_warning['update_core_data'] = array(
					'plugins' => self::get_update_core_plugins_data(),
					'themes'  => self::get_update_core_themes_data(),
				);
			} elseif ( 'themes.php' === $pagenow ) {
				$compatibility_warning['manage_themes_data'] = true;
			} elseif ( 'customize.php' === $pagenow ) {
				// Ensure style.css file exist.
				$theme_root = $wp_customize->theme()->theme_root;
				$theme_slug = $wp_customize->theme()->stylesheet;
				$theme_file = "{$theme_root}/{$theme_slug}/style.css";

				// Get WP & PHP compatibility info.
				$theme_headers = array();

				if ( file_exists( $theme_file ) ) {
					$theme_headers = get_file_data(
						$theme_file,
						array(
							'RequiresWP'  => 'Requires at least',
							'RequiresPHP' => 'Requires PHP',
						),
						'theme'
					);
				}

				$requires_wp  = et_()->array_get( $theme_headers, 'RequiresWP', false );
				$requires_php = et_()->array_get( $theme_headers, 'RequiresPHP', false );

				// Theme Customizer - Used for disable publish button.
				$compatibility_warning['customizer_data'] = array(
					'compatible_wp'  => is_wp_version_compatible( $requires_wp ),
					'compatible_php' => is_php_version_compatible( $requires_php ),
					'disabled_text'  => esc_html_x( 'Cannot Activate', 'theme' ),
				);
			}

			wp_localize_script( 'et_compatibility_warning_script', 'et_compatibility_warning', $compatibility_warning );
		}

		/**
		 * Overrides table body of plugin updates section.
		 *
		 * The structure is backported from WP 5.5 without any modification.
		 *
		 * @see {list_plugin_updates()} of WP 5.5
		 *
		 * @since 4.7.0
		 */
		public function overrides_update_core_plugins_table_body() {
			// Bail early if there is no plugin updates.
			if ( empty( get_plugin_updates() ) ) {
				return;
			}
			?>
			<script type="text/html" id="tmpl-et-update-core-plugins-table-body">
				<# if (data.plugins) { #>
					<# _.each(data.plugins, function(plugin, slug) { #>
					<tr>
						<td class="check-column">
							<# if (plugin.compatible_php) { #>
								<input type="checkbox" name="checked[]" id="{{plugin.checkbox_id}}" value="{{plugin.plugin_file}}" />
								<label for="{{plugin.checkbox_id}}" class="screen-reader-text">
								<?php
									/* translators: %s: Plugin name. */
									printf( esc_html__( 'Select %s' ), '{{plugin.name}}' );
								?>
								</label>
							<# } #>
						</td>
						<td class="plugin-title"><p>
							{{{plugin.icon}}}
							<strong>{{plugin.name}}</strong>
							<?php
								printf(
									/* translators: 1: Plugin version, 2: New version. */
									esc_html__( 'You have version %1$s installed. Update to %2$s.' ),
									'{{plugin.version}}',
									'{{plugin.new_version}}'
								);

								echo ' {{{plugin.details}}}{{{plugin.compat}}}{{{plugin.upgrade_notice}}}';
							?>
						</p></td>
					</tr>
					<# }); #>
				<# } #>
			</script>
			<?php
		}

		/**
		 * Overrides table body of theme updates section.
		 *
		 * The structure is backported from WP 5.5 without any modification.
		 *
		 * @see {list_theme_updates()} of WP 5.5
		 *
		 * @since 4.7.0
		 */
		public function overrides_update_core_themes_table_body() {
			// Bail early if there is no theme updates.
			if ( empty( get_theme_updates() ) ) {
				return;
			}
			?>
			<script type="text/html" id="tmpl-et-update-core-themes-table-body">
				<# if (data.themes) { #>
					<# _.each(data.themes, function(theme, slug) { #>
					<tr>
						<td class="check-column">
							<# if (theme.compatible_wp && theme.compatible_php) { #>
								<input type="checkbox" name="checked[]" id="{{theme.checkbox_id}}" value="{{theme.styelsheet}}" />
								<label for="{{theme.checkbox_id}}" class="screen-reader-text">
								<?php
									/* translators: %s: Theme name. */
									printf( esc_html__( 'Select %s' ), '{{theme.name}}' );
								?>
								</label>
							<# } #>
						</td>
						<td class="plugin-title"><p>
							<img src="{{theme.screenshot}}" width="85" height="64" class="updates-table-screenshot" alt="" />
							<strong>{{theme.name}}</strong>
							<?php
								printf(
									/* translators: 1: Theme version, 2: New version. */
									esc_html__( 'You have version %1$s installed. Update to %2$s.' ),
									'{{theme.version}}',
									'{{theme.new_version}}'
								);

								echo ' {{{theme.compat}}}';
							?>
						</p></td>
					</tr>
					<# }); #>
				<# } #>
			</script>
			<?php
		}

		/**
		 * Overrides theme info & details display of Manage Themes.
		 *
		 * The structure is backported from WP 5.5 without any modification.
		 *
		 * @see {wp-admin/themes.php} of WP 5.5
		 *
		 * @since 4.7.0
		 */
		public function overrides_tmpl_theme() {
			?>
			<script id="tmpl-theme" type="text/template">
				<# if ( data.screenshot[0] ) { #>
					<div class="theme-screenshot">
						<img src="{{ data.screenshot[0] }}" alt="" />
					</div>
				<# } else { #>
					<div class="theme-screenshot blank"></div>
				<# } #>

				<# if ( data.hasUpdate ) { #>
					<# if ( data.updateResponse.compatibleWP && data.updateResponse.compatiblePHP ) { #>
						<div class="update-message notice inline notice-warning notice-alt"><p>
							<# if ( data.hasPackage ) { #>
								<?php echo et_core_intentionally_unescaped( __( 'New version available. <button class="button-link" type="button">Update now</button>' ), 'html' ); ?>
							<# } else { #>
								<?php esc_html_e( 'New version available.' ); ?>
							<# } #>
						</p></div>
					<# } else { #>
						<div class="update-message notice inline notice-error notice-alt"><p>
							<# if ( ! data.updateResponse.compatibleWP && ! data.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' ),
									'{{{ data.name }}}'
								);
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.updateResponse.compatibleWP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ), 'html' ),
									'{{{ data.name }}}'
								);
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) )
									);
								}
								?>
							<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
								<?php
								printf(
									/* translators: %s: Theme name. */
									et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ), 'html' ),
									'{{{ data.name }}}'
								);
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p></div>
					<# } #>
				<# } #>

				<# if ( ! data.compatibleWP || ! data.compatiblePHP ) { #>
					<div class="notice notice-error notice-alt"><p>
						<# if ( ! data.compatibleWP && ! data.compatiblePHP ) { #>
							<?php
							echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' );
							if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
								printf(
									/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
									' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
									esc_url( self_admin_url( 'update-core.php' ) ),
									esc_url( wp_get_update_php_url() )
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							} elseif ( current_user_can( 'update_core' ) ) {
								printf(
									/* translators: %s: URL to WordPress Updates screen. */
									' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
									esc_url( self_admin_url( 'update-core.php' ) )
								);
							} elseif ( current_user_can( 'update_php' ) ) {
								printf(
									/* translators: %s: URL to Update PHP page. */
									' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
									esc_url( wp_get_update_php_url() )
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							}
							?>
						<# } else if ( ! data.compatibleWP ) { #>
							<?php
							echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of WordPress.' ), 'html' );
							if ( current_user_can( 'update_core' ) ) {
								printf(
									/* translators: %s: URL to WordPress Updates screen. */
									' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
									esc_url( self_admin_url( 'update-core.php' ) )
								);
							}
							?>
						<# } else if ( ! data.compatiblePHP ) { #>
							<?php
							echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of PHP.' ), 'html' );
							if ( current_user_can( 'update_php' ) ) {
								printf(
									/* translators: %s: URL to Update PHP page. */
									' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
									esc_url( wp_get_update_php_url() )
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							}
							?>
						<# } #>
					</p></div>
				<# } #>

				<span class="more-details" id="{{ data.id }}-action"><?php esc_html_e( 'Theme Details' ); ?></span>
				<div class="theme-author">
					<?php
					/* translators: %s: Theme author name. */
					printf( esc_html__( 'By %s' ), '{{{ data.author }}}' );
					?>
				</div>

				<div class="theme-id-container">
					<# if ( data.active ) { #>
						<h2 class="theme-name" id="{{ data.id }}-name">
							<span><?php echo esc_html_x( 'Active:', 'theme' ); ?></span> {{{ data.name }}}
						</h2>
					<# } else { #>
						<h2 class="theme-name" id="{{ data.id }}-name">{{{ data.name }}}</h2>
					<# } #>

					<div class="theme-actions">
						<# if ( data.active ) { #>
							<# if ( data.actions.customize ) { #>
								<a class="button button-primary customize load-customize hide-if-no-customize" href="{{{ data.actions.customize }}}"><?php esc_html_e( 'Customize' ); ?></a>
							<# } #>
						<# } else { #>
							<# if ( data.compatibleWP && data.compatiblePHP ) { #>
								<?php
								/* translators: %s: Theme name. */
								$aria_label = sprintf( esc_html_x( 'Activate %s', 'theme' ), '{{ data.name }}' );
								?>
								<a class="button activate" href="{{{ data.actions.activate }}}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php esc_html_e( 'Activate' ); ?></a>
								<a class="button button-primary load-customize hide-if-no-customize" href="{{{ data.actions.customize }}}"><?php esc_html_e( 'Live Preview' ); ?></a>
							<# } else { #>
								<?php
								/* translators: %s: Theme name. */
								$aria_label = sprintf( esc_html_x( 'Cannot Activate %s', 'theme' ), '{{ data.name }}' );
								?>
								<a class="button disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html_x( 'Cannot Activate', 'theme' ); ?></a>
								<a class="button button-primary hide-if-no-customize disabled"><?php esc_html_e( 'Live Preview' ); ?></a>
							<# } #>
						<# } #>
					</div>
				</div>
			</script>

			<script id="tmpl-theme-single" type="text/template">
				<div class="theme-backdrop"></div>
				<div class="theme-wrap wp-clearfix" role="document">
					<div class="theme-header">
						<button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show previous theme' ); ?></span></button>
						<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show next theme' ); ?></span></button>
						<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Close details dialog' ); ?></span></button>
					</div>
					<div class="theme-about wp-clearfix">
						<div class="theme-screenshots">
						<# if ( data.screenshot[0] ) { #>
							<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
						<# } else { #>
							<div class="screenshot blank"></div>
						<# } #>
						</div>

						<div class="theme-info">
							<# if ( data.active ) { #>
								<span class="current-label"><?php esc_html_e( 'Current Theme' ); ?></span>
							<# } #>
							<h2 class="theme-name">{{{ data.name }}}<span class="theme-version">
								<?php
								/* translators: %s: Theme version. */
								printf( esc_html__( 'Version: %s' ), '{{ data.version }}' );
								?>
							</span></h2>
							<p class="theme-author">
								<?php
								/* translators: %s: Theme author link. */
								printf( esc_html__( 'By %s' ), '{{{ data.authorAndUri }}}' );
								?>
							</p>

							<# if ( ! data.compatibleWP || ! data.compatiblePHP ) { #>
								<div class="notice notice-error notice-alt notice-large"><p>
									<# if ( ! data.compatibleWP && ! data.compatiblePHP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' );
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											printf(
												/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } else if ( ! data.compatibleWP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of WordPress.' ), 'html' );
										if ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										}
										?>
									<# } else if ( ! data.compatiblePHP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of PHP.' ), 'html' );
										if ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } #>
								</p></div>
							<# } #>

							<# if ( data.hasUpdate ) { #>
								<# if ( data.updateResponse.compatibleWP && data.updateResponse.compatiblePHP ) { #>
									<div class="notice notice-warning notice-alt notice-large">
										<h3 class="notice-title"><?php esc_html_e( 'Update Available' ); ?></h3>
										{{{ data.update }}}
									</div>
								<# } else { #>
									<div class="notice notice-error notice-alt notice-large">
										<h3 class="notice-title"><?php esc_html_e( 'Update Incompatible' ); ?></h3>
										<p>
											<# if ( ! data.updateResponse.compatibleWP && ! data.updateResponse.compatiblePHP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
													printf(
														/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												} elseif ( current_user_can( 'update_core' ) ) {
													printf(
														/* translators: %s: URL to WordPress Updates screen. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) )
													);
												} elseif ( current_user_can( 'update_php' ) ) {
													printf(
														/* translators: %s: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												}
												?>
											<# } else if ( ! data.updateResponse.compatibleWP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_core' ) ) {
													printf(
														/* translators: %s: URL to WordPress Updates screen. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) )
													);
												}
												?>
											<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_php' ) ) {
													printf(
														/* translators: %s: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												}
												?>
											<# } #>
										</p>
									</div>
								<# } #>
							<# } #>

							<p class="theme-description">{{{ data.description }}}</p>

							<# if ( data.parent ) { #>
								<p class="parent-theme">
									<?php
									/* translators: %s: Theme name. */
									printf( esc_html__( 'This is a child theme of %s.' ), '<strong>{{{ data.parent }}}</strong>' );
									?>
								</p>
							<# } #>

							<# if ( data.tags ) { #>
								<p class="theme-tags"><span><?php esc_html_e( 'Tags:' ); ?></span> {{{ data.tags }}}</p>
							<# } #>
						</div>
					</div>

					<div class="theme-actions">
						<div class="active-theme">
							<a href="{{{ data.actions.customize }}}" class="button button-primary customize load-customize hide-if-no-customize"><?php esc_html_e( 'Customize' ); ?></a>
							<?php echo et_core_intentionally_unescaped( implode( ' ', $GLOBALS['current_theme_actions'] ), 'html' ); ?>
						</div>
						<div class="inactive-theme">
							<# if ( data.compatibleWP && data.compatiblePHP ) { #>
								<?php
								/* translators: %s: Theme name. */
								$aria_label = sprintf( esc_html_x( 'Activate %s', 'theme' ), '{{ data.name }}' );
								?>
								<# if ( data.actions.activate ) { #>
									<a href="{{{ data.actions.activate }}}" class="button activate" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php esc_html_e( 'Activate' ); ?></a>
								<# } #>
								<a href="{{{ data.actions.customize }}}" class="button button-primary load-customize hide-if-no-customize"><?php esc_html_e( 'Live Preview' ); ?></a>
							<# } else { #>
								<?php
								/* translators: %s: Theme name. */
								$aria_label = sprintf( esc_html_x( 'Cannot Activate %s', 'theme' ), '{{ data.name }}' );
								?>
								<# if ( data.actions.activate ) { #>
									<a class="button disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html_x( 'Cannot Activate', 'theme' ); ?></a>
								<# } #>
								<a class="button button-primary hide-if-no-customize disabled"><?php esc_html_e( 'Live Preview' ); ?></a>
							<# } #>
						</div>

						<# if ( ! data.active && data.actions['delete'] ) { #>
							<a href="{{{ data.actions['delete'] }}}" class="button delete-theme"><?php esc_html_e( 'Delete' ); ?></a>
						<# } #>
					</div>
				</div>
			</script>
			<?php
		}

		/**
		 * Overrides plugin warning display of Plugins list page.
		 *
		 * @see {wp_plugin_update_rows()} of WP 5.5
		 *
		 * @since 4.7.0
		 */
		public function overrides_plugins_table_rows() {
			if ( ! current_user_can( 'update_plugins' ) ) {
				return;
			}

			$update_plugins = get_site_transient( 'update_plugins' );

			if ( isset( $update_plugins->response ) && is_array( $update_plugins->response ) ) {
				foreach ( $update_plugins->response as $plugin_file => $plugin ) {
					$requires_php   = isset( $plugin->requires_php ) ? $plugin->requires_php : null;
					$compatible_php = is_php_version_compatible( $requires_php );

					// Bail early if the package empty or already compatible with current PHP version.
					if ( empty( $plugin->package ) || $compatible_php ) {
						continue;
					}

					// Need to remove default action before we can replace it with the new one.
					remove_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
					add_action( "after_plugin_row_$plugin_file", array( $this, 'plugin_update_row_compatibility_error' ), 10, 2 );
				}
			}
		}

		/**
		 * Display plugin update row with error compatibility.
		 *
		 * @see {wp_plugin_update_row()} of WP 5.5
		 *
		 * @since 4.7.0
		 *
		 * @param string $file        Plugin basename.
		 * @param array  $plugin_data Plugin information.
		 */
		public function plugin_update_row_compatibility_error( $file, $plugin_data ) {
			if ( ! is_network_admin() && is_multisite() ) {
				return;
			}

			$update_plugins = get_site_transient( 'update_plugins' );

			if ( ! isset( $update_plugins->response[ $file ] ) ) {
				return;
			}

			// a. Plugin response.
			$response = $update_plugins->response[ $file ];

			// b. Plugin name.
			$plugins_allowedtags = array(
				'a'       => array(
					'href'  => array(),
					'title' => array(),
				),
				'abbr'    => array( 'title' => array() ),
				'acronym' => array( 'title' => array() ),
				'code'    => array(),
				'em'      => array(),
				'strong'  => array(),
			);

			$plugin_name = wp_kses( $plugin_data['Name'], $plugins_allowedtags );

			// c. Details URL.
			$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $response->slug . '&section=changelog&TB_iframe=true&width=600&height=800' );

			// d. Active class.
			if ( is_network_admin() ) {
				$active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
			} else {
				$active_class = is_plugin_active( $file ) ? ' active' : '';
			}

			/**
			 * Column count.
			 *
			 * @var WP_Plugins_List_Table $wp_list_table
			 */
			$wp_list_table = _get_list_table(
				'WP_Plugins_List_Table',
				array(
					'screen' => get_current_screen(),
				)
			);

			// f. Error text.
			$update_php_notation = wp_get_update_php_annotation();

			$error_text = sprintf(
				/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number 5: URL to Update PHP page. */
				__( 'There is a new version of %1$s available, but it doesn&#8217;t work with your version of PHP. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s">learn more about updating PHP</a>. %6$s' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Plugin name, 2: Version number. */
					esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
				),
				esc_attr( $response->new_version ),
				esc_url( wp_get_update_php_url() ), // #5
				! empty( $update_php_notation ) ? sprintf( __( '<br><em>%s</em>' ), $update_php_notation ) : ''
			);

			printf(
				/* translators: 1: Active class, 2: Update slug, 3: Slug, 4: Plugin file, 5: Column count. */
				'<tr class="plugin-update-tr%1$s" id="%2$s" data-slug="%3$s" data-plugin="%4$s">
					<td colspan="%5$s" class="plugin-update colspanchange">
						<div class="update-message notice inline notice-error notice-alt">
							<p>%6$s</p>
						</div>
					</td>
				</tr>',
				esc_attr( $active_class ),
				esc_attr( $response->slug . '-update' ),
				esc_attr( $response->slug ),
				esc_attr( $file ),
				esc_attr( $wp_list_table->get_column_count() ), // #5
				et_core_intentionally_unescaped( $error_text, 'html' )
			);
		}

		/**
		 * Overrides theme info & details display of Theme Customizer.
		 *
		 * The structure is backported from WP 5.5 without any modification.
		 *
		 * @see {WP_Customize_Theme_Control::content_template()} of WP 5.5
		 *
		 * @since 4.7.0
		 */
		public function overrides_tmpl_customize_control_theme_content() {
			/* translators: %s: Theme name. */
			$details_label = sprintf( esc_html__( 'Details for theme: %s' ), '{{ data.theme.name }}' );
			/* translators: %s: Theme name. */
			$customize_label = sprintf( esc_html__( 'Customize theme: %s' ), '{{ data.theme.name }}' );
			/* translators: %s: Theme name. */
			$preview_label = sprintf( esc_html__( 'Live preview theme: %s' ), '{{ data.theme.name }}' );
			/* translators: %s: Theme name. */
			$install_label = sprintf( esc_html__( 'Install and preview theme: %s' ), '{{ data.theme.name }}' );
			?>
			<script id="tmpl-customize-control-theme-content" type="text/html">
				<# if ( data.theme.active ) { #>
					<div class="theme active" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
				<# } else { #>
					<div class="theme" tabindex="0" aria-describedby="{{ data.section }}-{{ data.theme.id }}-action">
				<# } #>

					<# if ( data.theme.screenshot && data.theme.screenshot[0] ) { #>
						<div class="theme-screenshot">
							<img data-src="{{ data.theme.screenshot[0] }}" alt="" />
						</div>
					<# } else { #>
						<div class="theme-screenshot blank"></div>
					<# } #>

					<span class="more-details theme-details" id="{{ data.section }}-{{ data.theme.id }}-action" aria-label="<?php echo esc_attr( $details_label ); ?>"><?php esc_html_e( 'Theme Details' ); ?></span>

					<div class="theme-author">
					<?php
						/* translators: Theme author name. */
						printf( esc_html_x( 'By %s', 'theme author' ), '{{ data.theme.author }}' );
					?>
					</div>

					<# if ( 'installed' === data.theme.type && data.theme.hasUpdate ) { #>
						<# if ( data.theme.updateResponse.compatibleWP && data.theme.updateResponse.compatiblePHP ) { #>
							<div class="update-message notice inline notice-warning notice-alt" data-slug="{{ data.theme.id }}">
								<p>
									<?php
									if ( is_multisite() ) {
										esc_html_e( 'New version available.' );
									} else {
										printf(
											/* translators: %s: "Update now" button. */
											esc_html__( 'New version available. %s' ),
											'<button class="button-link update-theme" type="button">' . esc_html__( 'Update now' ) . '</button>'
										);
									}
									?>
								</p>
							</div>
						<# } else { #>
							<div class="update-message notice inline notice-error notice-alt" data-slug="{{ data.theme.id }}">
								<p>
									<# if ( ! data.theme.updateResponse.compatibleWP && ! data.theme.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' ),
											'{{{ data.theme.name }}}'
										);
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											printf(
												/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } else if ( ! data.theme.updateResponse.compatibleWP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ), 'html' ),
											'{{{ data.theme.name }}}'
										);
										if ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										}
										?>
									<# } else if ( ! data.theme.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ), 'html' ),
											'{{{ data.theme.name }}}'
										);
										if ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } #>
								</p>
							</div>
						<# } #>
					<# } #>

					<# if ( ! data.theme.compatibleWP || ! data.theme.compatiblePHP ) { #>
						<div class="notice notice-error notice-alt"><p>
							<# if ( ! data.theme.compatibleWP && ! data.theme.compatiblePHP ) { #>
								<?php
								echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' );
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.theme.compatibleWP ) { #>
								<?php
								echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of WordPress.' ), 'html' );
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
										esc_url( self_admin_url( 'update-core.php' ) )
									);
								}
								?>
							<# } else if ( ! data.theme.compatiblePHP ) { #>
								<?php
								echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of PHP.' ), 'html' );
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p></div>
					<# } #>

					<# if ( data.theme.active ) { #>
						<div class="theme-id-container">
							<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">
								<span><?php echo esc_html_x( 'Previewing:', 'theme' ); ?></span> {{ data.theme.name }}
							</h3>
							<div class="theme-actions">
								<button type="button" class="button button-primary customize-theme" aria-label="<?php echo esc_attr( $customize_label ); ?>"><?php esc_html_e( 'Customize' ); ?></button>
							</div>
						</div>
						<div class="notice notice-success notice-alt"><p><?php echo esc_html_x( 'Installed', 'theme' ); ?></p></div>
					<# } else if ( 'installed' === data.theme.type ) { #>
						<div class="theme-id-container">
							<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
							<div class="theme-actions">
								<# if ( data.theme.compatibleWP && data.theme.compatiblePHP ) { #>
									<button type="button" class="button button-primary preview-theme" aria-label="<?php echo esc_attr( $preview_label ); ?>" data-slug="{{ data.theme.id }}"><?php esc_html_e( 'Live Preview' ); ?></button>
								<# } else { #>
									<button type="button" class="button button-primary disabled" aria-label="<?php echo esc_attr( $preview_label ); ?>"><?php esc_html_e( 'Live Preview' ); ?></button>
								<# } #>
							</div>
						</div>
						<div class="notice notice-success notice-alt"><p><?php echo esc_html_x( 'Installed', 'theme' ); ?></p></div>
					<# } else { #>
						<div class="theme-id-container">
							<h3 class="theme-name" id="{{ data.section }}-{{ data.theme.id }}-name">{{ data.theme.name }}</h3>
							<div class="theme-actions">
								<# if ( data.theme.compatibleWP && data.theme.compatiblePHP ) { #>
									<button type="button" class="button button-primary theme-install preview" aria-label="<?php echo esc_attr( $install_label ); ?>" data-slug="{{ data.theme.id }}" data-name="{{ data.theme.name }}"><?php esc_html_e( 'Install &amp; Preview' ); ?></button>
								<# } else { #>
									<button type="button" class="button button-primary disabled" aria-label="<?php echo esc_attr( $install_label ); ?>" disabled><?php esc_html_e( 'Install &amp; Preview' ); ?></button>
								<# } #>
							</div>
						</div>
					<# } #>
				</div>
			</script>

			<script type="text/html" id="tmpl-customize-themes-details-view">
				<div class="theme-backdrop"></div>
				<div class="theme-wrap wp-clearfix" role="document">
					<div class="theme-header">
						<button type="button" class="left dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show previous theme' ); ?></span></button>
						<button type="button" class="right dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Show next theme' ); ?></span></button>
						<button type="button" class="close dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e( 'Close details dialog' ); ?></span></button>
					</div>
					<div class="theme-about wp-clearfix">
						<div class="theme-screenshots">
						<# if ( data.screenshot && data.screenshot[0] ) { #>
							<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
						<# } else { #>
							<div class="screenshot blank"></div>
						<# } #>
						</div>

						<div class="theme-info">
							<# if ( data.active ) { #>
								<span class="current-label"><?php esc_html_e( 'Current Theme' ); ?></span>
							<# } #>
							<h2 class="theme-name">{{{ data.name }}}<span class="theme-version">
								<?php
								/* translators: %s: Theme version. */
								printf( esc_html__( 'Version: %s' ), '{{ data.version }}' );
								?>
							</span></h2>
							<h3 class="theme-author">
								<?php
								/* translators: %s: Theme author link. */
								printf( esc_html__( 'By %s' ), '{{{ data.authorAndUri }}}' );
								?>
							</h3>

							<# if ( data.stars && 0 != data.num_ratings ) { #>
								<div class="theme-rating">
									{{{ data.stars }}}
									<a class="num-ratings" target="_blank" href="{{ data.reviews_url }}">
										<?php
										printf(
											'%1$s <span class="screen-reader-text">%2$s</span>',
											/* translators: %s: Number of ratings. */
											sprintf( esc_html__( '(%s ratings)' ), '{{ data.num_ratings }}' ),
											/* translators: Accessibility text. */
											esc_html__( '(opens in a new tab)' )
										);
										?>
									</a>
								</div>
							<# } #>

							<# if ( data.hasUpdate ) { #>
								<# if ( data.updateResponse.compatibleWP && data.updateResponse.compatiblePHP ) { #>
									<div class="notice notice-warning notice-alt notice-large" data-slug="{{ data.id }}">
										<h3 class="notice-title"><?php esc_html_e( 'Update Available' ); ?></h3>
										{{{ data.update }}}
									</div>
								<# } else { #>
									<div class="notice notice-error notice-alt notice-large" data-slug="{{ data.id }}">
										<h3 class="notice-title"><?php esc_html_e( 'Update Incompatible' ); ?></h3>
										<p>
											<# if ( ! data.updateResponse.compatibleWP && ! data.updateResponse.compatiblePHP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
													printf(
														/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												} elseif ( current_user_can( 'update_core' ) ) {
													printf(
														/* translators: %s: URL to WordPress Updates screen. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) )
													);
												} elseif ( current_user_can( 'update_php' ) ) {
													printf(
														/* translators: %s: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												}
												?>
											<# } else if ( ! data.updateResponse.compatibleWP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_core' ) ) {
													printf(
														/* translators: %s: URL to WordPress Updates screen. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
														esc_url( self_admin_url( 'update-core.php' ) )
													);
												}
												?>
											<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
												<?php
												printf(
													/* translators: %s: Theme name. */
													et_core_intentionally_unescaped( __( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ), 'html' ),
													'{{{ data.name }}}'
												);
												if ( current_user_can( 'update_php' ) ) {
													printf(
														/* translators: %s: URL to Update PHP page. */
														' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
														esc_url( wp_get_update_php_url() )
													);
													wp_update_php_annotation( '</p><p><em>', '</em>' );
												}
												?>
											<# } #>
										</p>
									</div>
								<# } #>
							<# } #>

							<# if ( data.parent ) { #>
								<p class="parent-theme">
									<?php
									printf(
										/* translators: %s: Theme name. */
										esc_html__( 'This is a child theme of %s.' ),
										'<strong>{{{ data.parent }}}</strong>'
									);
									?>
								</p>
							<# } #>

							<# if ( ! data.compatibleWP || ! data.compatiblePHP ) { #>
								<div class="notice notice-error notice-alt notice-large"><p>
									<# if ( ! data.compatibleWP && ! data.compatiblePHP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' ), 'html' );
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											printf(
												/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } else if ( ! data.compatibleWP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of WordPress.' ), 'html' );
										if ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Please update WordPress</a>.' ), 'html' ),
												esc_url( self_admin_url( 'update-core.php' ) )
											);
										}
										?>
									<# } else if ( ! data.compatiblePHP ) { #>
										<?php
										echo et_core_intentionally_unescaped( __( 'This theme doesn&#8217;t work with your version of PHP.' ), 'html' );
										if ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . et_core_intentionally_unescaped( __( '<a href="%s">Learn more about updating PHP</a>.' ), 'html' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } #>
								</p></div>
							<# } #>

							<p class="theme-description">{{{ data.description }}}</p>

							<# if ( data.tags ) { #>
								<p class="theme-tags"><span><?php esc_html_e( 'Tags:' ); ?></span> {{{ data.tags }}}</p>
							<# } #>
						</div>
					</div>

					<div class="theme-actions">
						<# if ( data.active ) { #>
							<button type="button" class="button button-primary customize-theme"><?php esc_html_e( 'Customize' ); ?></button>
						<# } else if ( 'installed' === data.type ) { #>
							<?php if ( current_user_can( 'delete_themes' ) ) { ?>
								<# if ( data.actions && data.actions['delete'] ) { #>
									<a href="{{{ data.actions['delete'] }}}" data-slug="{{ data.id }}" class="button button-secondary delete-theme"><?php esc_html_e( 'Delete' ); ?></a>
								<# } #>
							<?php } ?>

							<# if ( data.compatibleWP && data.compatiblePHP ) { #>
								<button type="button" class="button button-primary preview-theme" data-slug="{{ data.id }}"><?php esc_html_e( 'Live Preview' ); ?></button>
							<# } else { #>
								<button class="button button-primary disabled"><?php esc_html_e( 'Live Preview' ); ?></button>
							<# } #>
						<# } else { #>
							<# if ( data.compatibleWP && data.compatiblePHP ) { #>
								<button type="button" class="button theme-install" data-slug="{{ data.id }}"><?php esc_html_e( 'Install' ); ?></button>
								<button type="button" class="button button-primary theme-install preview" data-slug="{{ data.id }}"><?php esc_html_e( 'Install &amp; Preview' ); ?></button>
							<# } else { #>
								<button type="button" class="button disabled"><?php echo esc_html_x( 'Cannot Install', 'theme' ); ?></button>
								<button type="button" class="button button-primary disabled"><?php esc_html_e( 'Install &amp; Preview' ); ?></button>
							<# } #>
						<# } #>
					</div>
				</div>
			</script>
			<?php
		}

		/**
		 * Deactivate incompatible plugin once it's activated.
		 *
		 * The code is backported from WP 5.5 partially. However, not all the code is inlcuded
		 * here because:
		 * - We can't add `RequiresWP: Requires at least` & `RequiresPHP: Requires PHP` plugin
		 *   headers via `extra_plugin_headers` hook because the keys & values will be combined.
		 * - On WP 5.3, it requires readme.txt instead of main plugin file. We can simplify it
		 *   by directly access main plugin file.
		 *
		 * @see {validate_plugin_requirements()}
		 *
		 * @since 4.7.0
		 *
		 * @param string $plugin Main plugin file name.
		 */
		public static function maybe_deactivate_incompatible_plugin( $plugin ) {
			$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;

			// Ensure the main plugin file exists.
			if ( ! file_exists( $plugin_file ) ) {
				return;
			}

			// Get WP & PHP compatibility info.
			$plugin_headers = get_file_data(
				$plugin_file,
				array(
					'Name'        => 'Plugin Name',
					'RequiresWP'  => 'Requires at least',
					'RequiresPHP' => 'Requires PHP',
				),
				'plugin'
			);

			$requirements = array(
				'requires'     => ! empty( $plugin_headers['RequiresWP'] ) ? $plugin_headers['RequiresWP'] : '',
				'requires_php' => ! empty( $plugin_headers['RequiresPHP'] ) ? $plugin_headers['RequiresPHP'] : '',
			);

			if ( ! function_exists( 'is_wp_version_compatible' ) || ! function_exists( 'is_php_version_compatible' ) ) {
				require_once plugin_dir_path( $plugin_file ) . 'core/wp_functions.php';
			}

			// Check version compatibility.
			$compatible_wp  = is_wp_version_compatible( $requirements['requires'] );
			$compatible_php = is_php_version_compatible( $requirements['requires_php'] );

			/* translators: %s: URL to Update PHP page. */
			$php_update_message = '</p><p>' . sprintf(
				__( '<a href="%s">Learn more about updating PHP</a>.' ),
				esc_url( wp_get_update_php_url() )
			);

			$annotation = wp_get_update_php_annotation();

			if ( $annotation ) {
				$php_update_message .= '</p><p><em>' . $annotation . '</em>';
			}

			// Decide whether current plugin is compatible and should be activated or not.
			$result = true;

			if ( ! $compatible_wp && ! $compatible_php ) {
				$result = new WP_Error(
					'plugin_wp_php_incompatible',
					'<p>' . sprintf(
						/* translators: 1: Current WordPress version, 2: Current PHP version, 3: Plugin name, 4: Required WordPress version, 5: Required PHP version. */
						_x( '<strong>Error:</strong> Current versions of WordPress (%1$s) and PHP (%2$s) do not meet minimum requirements for %3$s. The plugin requires WordPress %4$s and PHP %5$s.', 'plugin' ),
						get_bloginfo( 'version' ),
						phpversion(),
						$plugin_headers['Name'],
						$requirements['requires'],
						$requirements['requires_php']
					) . $php_update_message . '</p>'
				);
			} elseif ( ! $compatible_php ) {
				$result = new WP_Error(
					'plugin_php_incompatible',
					'<p>' . sprintf(
						/* translators: 1: Current PHP version, 2: Plugin name, 3: Required PHP version. */
						_x( '<strong>Error:</strong> Current PHP version (%1$s) does not meet minimum requirements for %2$s. The plugin requires PHP %3$s.', 'plugin' ),
						phpversion(),
						$plugin_headers['Name'],
						$requirements['requires_php']
					) . $php_update_message . '</p>'
				);
			} elseif ( ! $compatible_wp ) {
				$result = new WP_Error(
					'plugin_wp_incompatible',
					'<p>' . sprintf(
						/* translators: 1: Current WordPress version, 2: Plugin name, 3: Required WordPress version. */
						_x( '<strong>Error:</strong> Current WordPress version (%1$s) does not meet minimum requirements for %2$s. The plugin requires WordPress %3$s.', 'plugin' ),
						get_bloginfo( 'version' ),
						$plugin_headers['Name'],
						$requirements['requires']
					) . '</p>'
				);
			}

			if ( is_wp_error( $result ) ) {
				wp_die( $result ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Can't call et_core_intentionally_unescaped function because it's fired during plugin activation hook.
			}
		}
	}

endif;
