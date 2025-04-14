<?php
// phpcs:disable Generic.WhiteSpace.ScopeIndent -- our preference is to not indent the whole inner function in this scenario.
if ( ! class_exists( 'ET_Core_VersionRollback' ) ) :
/**
 * Handles version rollback.
 *
 * @since 3.10
 *
 * @private
 *
 * @package ET\Core\VersionRollback
 */
class ET_Core_VersionRollback {
	/**
	 * Product name.
	 *
	 * @var string
	 */
	protected $product_name = '';

	/**
	 * Product shortname.
	 *
	 * @var string
	 */
	protected $product_shortname = '';

	/**
	 * Current product version.
	 *
	 * @var string
	 */
	protected $product_version = '';

	/**
	 * Is rollback service enabled.
	 *
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * API Username.
	 *
	 * @var string
	 */
	protected $api_username = '';

	/**
	 * API Key.
	 *
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * ET_Core_VersionRollback constructor.
	 *
	 * @since 3.10
	 *
	 * @param string $product_name Product name.
	 * @param string $product_shortname Product shortname.
	 * @param string $product_version Product version.
	 */
	public function __construct( $product_name, $product_shortname, $product_version ) {
		$this->product_name = sanitize_text_field( $product_name );
		$this->product_shortname = sanitize_text_field( $product_shortname );
		$this->product_version = sanitize_text_field( $product_version );

		if ( ! $options = get_site_option( 'et_automatic_updates_options' ) ) {
			$options = get_option( 'et_automatic_updates_options' );
		}

		$this->api_username = isset( $options['username'] ) ? sanitize_text_field( $options['username'] ) : '';
		$this->api_key = isset( $options['api_key'] ) ? sanitize_text_field( $options['api_key'] ) : '';
	}

		/**
		 * Enqueue assets.
		 *
		 * @since ?.? Script `et-core-version-rollback` now loads in footer.
		 * @since 3.10
		 */
		public function assets() {
			wp_enqueue_style(
				'et-core-version-rollback',
				ET_CORE_URL . 'admin/css/version-rollback.css',
				array(
					'et-core-admin',
				),
				ET_CORE_VERSION
			);

			wp_enqueue_script(
				'et-core-version-rollback',
				ET_CORE_URL . 'admin/js/version-rollback.js',
				array(
					'jquery',
					'jquery-ui-tabs',
					'jquery-form',
					'et-core-admin',
				),
				ET_CORE_VERSION,
				true
			);

			wp_localize_script(
				'et-core-version-rollback',
				'etCoreVersionRollbackI18n',
				array(
					'unknownError' => esc_html__( 'An unknown error has occurred. Please try again later.', 'et-core' ),
				)
			);
		}

	/**
	 * Get previous installed version, if any.
	 *
	 * @since 3.10
	 *
	 * @return string
	 */
	protected function _get_previous_installed_version() {
		return et_get_option( "{$this->product_shortname}_previous_installed_version", '' );
	}

	/**
	 * Set previous installed version.
	 *
	 * @since 3.10
	 *
	 * @param string $version
	 *
	 * @return void
	 */
	protected function _set_previous_installed_version( $version ) {
		et_update_option( "{$this->product_shortname}_previous_installed_version", sanitize_text_field( $version ) );
	}

	/**
	 * Get latest installed version, if any.
	 *
	 * @since 3.10
	 *
	 * @return string
	 */
	protected function _get_latest_installed_version() {
		return et_get_option( "{$this->product_shortname}_latest_installed_version", '' );
	}

	/**
	 * Set latest installed version.
	 *
	 * @since 3.10
	 *
	 * @param string $version
	 *
	 * @return void
	 */
	protected function _set_latest_installed_version( $version ) {
		et_update_option( "{$this->product_shortname}_latest_installed_version", sanitize_text_field( $version ) );
	}

	/**
	 * Check if the product has already been rolled back.
	 *
	 * @since 3.10
	 *
	 * @return bool
	 */
	protected function _is_rolled_back() {
		return version_compare( $this->_get_latest_installed_version(), $this->_get_previous_installed_version(), '<=' );
	}

	/**
	 * Get unique ajax action.
	 *
	 * @since 3.10
	 *
	 * @return string
	 */
	protected function _get_ajax_action() {
		return 'et_core_version_rollback';
	}

	/**
	 * Enable update rollback.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function enable() {
		if ( $this->enabled ) {
			return;
		}

		$this->enabled = true;

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_ajax_' . $this->_get_ajax_action(), array( $this, 'ajax_rollback' ) );
		// Update version number when theme is manually replaced.
		add_action( 'admin_init', array( $this, 'store_previous_version_number' ) );
		// Update version number when theme is activated.
		add_action( 'after_switch_theme', array( $this, 'store_previous_version_number' ) );
		// Update version number when theme is updated.
		add_action( 'upgrader_process_complete', array( $this, 'store_previous_version_number' ), 10, 0 );
	}

	/**
	 * Handle REST API requests to rollback.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function ajax_rollback() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], $this->_get_ajax_action() ) ) {
			wp_send_json_error( array(
				'errorCode' => 'et_unknown',
				'error'     => esc_html__( 'Security check failed. Please refresh and try again.', 'et-core' ),
			), 400 );
		}

		if ( ! current_user_can( 'install_themes' ) ) {
			wp_send_json_error( array(
				'errorCode' => 'et_unknown',
				'error'     => esc_html__( 'You don\'t have sufficient permissions to access this page.', 'et-core' ),
			), 400 );
		}

		if ( $this->_is_rolled_back() ) {
			$error = '
				<p>
					' . et_get_safe_localization( sprintf(
						__( 'You\'re currently rolled back to <strong>Version %1$s</strong> from <strong>Version %2$s</strong>.', 'et-core' ),
						esc_html( $this->_get_latest_installed_version() ),
						esc_html( $this->_get_previous_installed_version() )
					) ) . '
				</p>
				<p>
					' . et_get_safe_localization( sprintf(
						__( 'Update to the latest version to unlock the full power of %1$s. <a href="%2$s" target="_blank">Learn more here</a>.', 'et-core' ),
						esc_html( $this->product_name ),
						esc_url( $this->_get_update_documentation_url() )
					) ) . '
				</p>
			';

			wp_send_json_error( array(
				'errorCode' => 'et_unknown',
				'error'     => $error,
			), 400 );
		}

		$success = $this->rollback();

		if ( is_wp_error( $success ) ) {
			$error = $success->get_error_message();
			if ( $success->get_error_code() === 'et_version_rollback_blocklisted' ) {
				$error = '
					<p>
						' . et_get_safe_localization( sprintf(
							__( 'For privacy and security reasons, you cannot rollback to <strong>Version %1$s</strong>.', 'et-core' ),
							esc_html( $this->_get_previous_installed_version() )
						) ) . '
					</p>
					<p>
						<a href="' . esc_url( $this->_get_update_documentation_url() ) . '" target="_blank">
							' . esc_html__( 'Learn more here.', 'et-core' ) . '
						</a>
					</p>
				';
			}

			wp_send_json_error( array(
				'errorIsUnrecoverable' => in_array( $success->get_error_code(), array( 'et_version_rollback_not_available', 'et_version_rollback_blocklisted' ) ),
				'errorCode'            => $success->get_error_code(),
				'error'                => $error,
			), 400 );
		}

		wp_send_json_success();
	}

	/**
	 * Execute a version rollback.
	 *
	 * @since 3.10
	 *
	 * @return bool|WP_Error
	 */
	public function rollback() {
		// Load versions before rollback so they are not affected.
		$previous_version = $this->_get_previous_installed_version();
		$latest_version = $this->_get_latest_installed_version();

		$api = new ET_Core_API_ElegantThemes( $this->api_username, $this->api_key );
		$available = $api->is_product_available( $this->product_name, $previous_version );

		if ( is_wp_error( $available ) ) {
			$major_minor = implode( '.', array_slice( explode( '.', $previous_version ), 0, 2 ) );

			if ( $major_minor . '.0' === $previous_version ) {
					// Skip the trailing 0 in the version number and retry.
					$previous_version = $major_minor;
					$available        = $api->is_product_available( $this->product_name, $previous_version );
			}

			if ( is_wp_error( $available ) ) {
				return $available;
			}
		}

		$download_url = $api->get_download_url( $this->product_name, $previous_version );

		// Buffer and discard output as upgrader classes still output content even if the upgrader skin is silent.
		$buffer_started = ob_start();
		$result = $this->_install_theme( $download_url );
		if ( $buffer_started ) {
			ob_end_clean();
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( true !== $result ) {
			return new WP_Error( 'et_unknown', esc_html__( 'An unknown error has occurred. Please try again later.', 'et-core' ) );
		}

		/**
		 * Fires after successful product version rollback.
		 *
		 * @since 3.26
		 *
		 * @param string $product_short_name    - The short name of the product rolling back.
		 * @param string $rollback_from_version - The product version rolling back from.
		 * @param string $rollback_to_version   - The product version rolling back to.
		 */
		do_action( 'et_after_version_rollback', $this->product_shortname, $latest_version, $previous_version );

		// Swap version numbers after a successful rollback.
		$this->_set_previous_installed_version( $latest_version );
		$this->_set_latest_installed_version( $previous_version );
	}

	/**
	 * Install a theme overwriting it if it already exists.
	 * Copied from Theme_Upgrader::install() due to lack of control over the clear_desination argument.
	 *
	 * @see Theme_Upgrader::install() @ WordPress 4.9.4
	 *
	 * @since 3.10
	 *
	 * @param string $package
	 *
	 * @return bool|WP_Error
	 */
	protected function _install_theme( $package ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		$upgrader = new Theme_Upgrader( new ET_Core_LIB_SilentThemeUpgraderSkin() );

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( array(), $defaults );

		$upgrader->init();
		$upgrader->install_strings();

		add_filter('upgrader_source_selection', array( $upgrader, 'check_package' ) );
		add_filter('upgrader_post_install', array( $upgrader, 'check_parent_theme_filter' ), 10, 3 );
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_themes() knows about the new theme.
			add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );
		}

		$upgrader->run( array(
			'package'           => $package,
			'destination'       => get_theme_root(),
			'clear_destination' => true, // Overwrite theme.
			'clear_working'     => true,
			'hook_extra'        => array(
				'type'              => 'theme',
				'action'            => 'install',
			),
		) );

		remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
		remove_filter( 'upgrader_source_selection', array( $upgrader, 'check_package' ) );
		remove_filter( 'upgrader_post_install', array( $upgrader, 'check_parent_theme_filter' ) );

		if ( ! $upgrader->result || is_wp_error( $upgrader->result ) ) {
			return $upgrader->result;
		}

		// Refresh the Theme Update information.
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	/**
	 * Get update documentation url for the product.
	 *
	 * @since 3.10
	 *
	 * @return string
	 */
	protected function _get_update_documentation_url() {
		return "https://www.elegantthemes.com/documentation/{$this->product_shortname}/update-{$this->product_shortname}/";
	}

	/**
	 * Return ePanel option.
	 *
	 * @since 3.10
	 *
	 * @return array
	 */
	public function get_epanel_option() {
		return array(
			'name'            => esc_html__( 'Version Rollback', 'et-core' ),
			'id'              => 'et_version_rollback',
			'type'            => 'callback_function',
			'function_name'   => array( $this, 'render_epanel_option' ),
			'desc'            => et_get_safe_localization( __( 'If you recently updated to a new version and are experiencing problems, you can easily roll back to the previously-installed version. We always recommend using the latest version and testing updates on a staging site. However, if you run into problems after updating you always have the option to roll back.', 'et-core' ) ),
		);
	}

	/**
	 * Render ePanel option.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function render_epanel_option() {
		$previous = $this->_get_previous_installed_version();
		$modal_renderer = array( $this, 'render_epanel_no_previous_version_modal' );

		if ( ! empty( $previous ) ) {
			$modal_renderer = array( $this, 'render_epanel_confirm_rollback_modal' );

			if ( $this->_is_rolled_back() ) {
				$modal_renderer = array( $this, 'render_epanel_already_rolled_back_modal' );
			}
		}

		add_action( 'admin_footer', $modal_renderer );
		?>
		<button type="button" class="et-button et-button--simple" data-et-core-modal=".et-core-version-rollback-modal">
			<?php esc_html_e( 'Rollback to the previous version', 'et-core' ); ?>
		</button>
		<?php
	}

	/**
	 * Render ePanel warning modal when no previous supported version has been used.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function render_epanel_no_previous_version_modal() {
		?>
		<div class="et-core-modal-overlay et-core-form et-core-version-rollback-modal et-core-modal-actionless">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">
						<?php esc_html_e( 'Version Rollback', 'et-core' ); ?>
					</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div id="et-core-version-rollback-modal-content">
					<div class="et-core-modal-content">
						<p>
							<?php
							printf(
								esc_html__( 'The previously used version of %1$s does not support version rollback.', 'et-core' ),
								esc_html( $this->product_name )
							);
							?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render ePanel confirmation modal for rollback.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function render_epanel_confirm_rollback_modal() {
		$action = $this->_get_ajax_action();
		$url = add_query_arg( array(
			'action'  => $action,
			'nonce'   => wp_create_nonce( $action ),
		), admin_url( 'admin-ajax.php' ) );
		?>
		<div class="et-core-modal-overlay et-core-form et-core-version-rollback-modal">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">
						<?php esc_html_e( 'Version Rollback', 'et-core' ); ?>
					</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div id="et-core-version-rollback-modal-content">
					<div class="et-core-modal-content">
						<p>
							<?php
							echo et_get_safe_localization( sprintf(
								__( 'You\'ll be rolled back to <strong>Version %1$s</strong> from the current <strong>Version %2$s</strong>.', 'et-core' ),
								esc_html( $this->_get_previous_installed_version() ),
								esc_html( $this->_get_latest_installed_version() )
							) );
							?>
						</p>
						<p>
							<?php
							echo et_get_safe_localization( sprintf(
								__( 'Rolling back will reinstall the previous version of %1$s. You will be able to update to the latest version at any time. <a href="%2$s" target="_blank">Learn more here</a>.', 'et-core' ),
								esc_html( $this->product_name ),
								esc_url( $this->_get_update_documentation_url() )
							) );
							?>
						</p>
						<p>
							<strong>
								<?php esc_html_e( 'Make sure you have a full site backup before proceeding.', 'et-core' ); ?>
							</strong>
						</p>
					</div>
					<a class="et-core-modal-action et-core-version-rollback-confirm" href="<?php echo esc_url( $url ); ?>">
						<?php esc_html_e( 'Rollback to the previous version', 'et-core' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render ePanel warning modal when a rollback has already been done.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function render_epanel_already_rolled_back_modal() {
		?>
		<div class="et-core-modal-overlay et-core-form et-core-version-rollback-modal">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">
						<?php esc_html_e( 'Version Rollback', 'et-core' ); ?>
					</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div id="et-core-version-rollback-modal-content">
					<div class="et-core-modal-content">
						<p>
							<?php
							echo et_get_safe_localization( sprintf(
								__( 'You\'re currently rolled back to <strong>Version %1$s</strong> from <strong>Version %2$s</strong>.', 'et-core' ),
								esc_html( $this->_get_latest_installed_version() ),
								esc_html( $this->_get_previous_installed_version() )
							) );
							?>
						</p>
						<p>
							<?php
							echo et_get_safe_localization( sprintf(
								__( 'Update to the latest version to unlock the full power of %1$s. <a href="%2$s" target="_blank">Learn more here</a>.', 'et-core' ),
								esc_html( $this->product_name ),
								esc_url( $this->_get_update_documentation_url() )
							) );
							?>
						</p>
					</div>
					<a class="et-core-modal-action" href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>">
						<?php esc_html_e( 'Update to the Latest Version', 'et-core' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Store latest and previous installed version.
	 *
	 * @since 3.10
	 *
	 * @return void;
	 */
	public function store_previous_version_number() {
		$previous_installed_version = $this->_get_previous_installed_version();
		$latest_installed_version = $this->_get_latest_installed_version();

		// Get the theme version since the files may have changed but
		// we are still executing old code from memory.
		$theme_version = et_get_theme_version();

		if ( $latest_installed_version === $theme_version ) {
			return;
		}

		if ( empty( $latest_installed_version ) ) {
			$latest_installed_version = $theme_version;
		}

		if ( version_compare( $theme_version, $latest_installed_version, '!=') ) {
			$previous_installed_version = $latest_installed_version;
			$latest_installed_version = $theme_version;
		}

		/**
		 * Fires after new version number is updated.
		 *
		 * @since 4.10.0
		 */
		do_action( 'et_store_before_new_version_update' );

		$this->_set_previous_installed_version( $previous_installed_version );
		$this->_set_latest_installed_version( $latest_installed_version );

		/**
		 * Fires after new version number is updated.
		 *
		 * @since 4.10.0
		 */
		do_action( 'et_store_after_new_version_update' );
	}

}
endif;
