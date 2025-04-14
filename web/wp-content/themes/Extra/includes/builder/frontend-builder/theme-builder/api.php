<?php
/**
 * Create a new layout.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_api_create_layout() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_create_layout', 'nonce' );

	$layout_type = isset( $_POST['layout_type'] ) ? sanitize_text_field( $_POST['layout_type'] ) : '';  // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need to use nonce.
	$post_type   = et_theme_builder_get_valid_layout_post_type( $layout_type );

	if ( '' === $post_type ) {
		wp_send_json_error(
			array(
				'message' => 'Invalid layout type: ' . $layout_type,
			)
		);
	}

	$post_id = et_theme_builder_insert_layout(
		array(
			'post_type' => $post_type,
		)
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error(
			array(
				'message' => 'Failed to create layout.',
			)
		);
	}

	wp_send_json_success(
		array(
			'id' => $post_id,
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_create_layout', 'et_theme_builder_api_create_layout' );

/**
 * Duplicate a layout.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_api_duplicate_layout() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_duplicate_layout', 'nonce' );

	$layout_type = isset( $_POST['layout_type'] ) ? sanitize_text_field( $_POST['layout_type'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need to use nonce.
	$post_type   = et_theme_builder_get_valid_layout_post_type( $layout_type );
	$layout_id   = isset( $_POST['layout_id'] ) ? (int) $_POST['layout_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need to use nonce.
	$layout      = get_post( $layout_id );

	if ( ! $layout ) {
		wp_send_json_error(
			array(
				'message' => 'Failed to duplicate layout.',
			)
		);
	}

	$post_id = et_theme_builder_insert_layout(
		array(
			'post_type'    => '' !== $post_type ? $post_type : $layout->post_type,
			'post_status'  => $layout->post_status,
			'post_title'   => $layout->post_title,
			'post_content' => $layout->post_content,
		)
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error(
			array(
				'message' => 'Failed to duplicate layout.',
			)
		);
	}

	$meta = et_core_get_post_builder_meta( $layout_id );

	foreach ( $meta as $entry ) {
		update_post_meta( $post_id, $entry['key'], $entry['value'] );
	}

	wp_send_json_success(
		array(
			'id' => $post_id,
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_duplicate_layout', 'et_theme_builder_api_duplicate_layout' );

/**
 * Get layout url.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_api_get_layout_url() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_get_layout_url', 'nonce' );

	$layout_id = isset( $_POST['layout_id'] ) ? (int) $_POST['layout_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need to use nonce.
	$layout    = get_post( $layout_id );

	if ( ! $layout ) {
		wp_send_json_error(
			array(
				'message' => 'Failed to load layout.',
			)
		);
	}

	$edit_url = add_query_arg( 'et_tb', '1', et_fb_get_builder_url( get_permalink( $layout_id ) ) );
	// If Admin is SSL but FE is not, we need to fix VB url or it won't work
	// because trying to load insecure resource.
	$edit_url = set_url_scheme( $edit_url, is_ssl() ? 'https' : 'http' );

	wp_send_json_success(
		array(
			'editUrl' => $edit_url,
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_get_layout_url', 'et_theme_builder_api_get_layout_url' );

/**
 * Save the theme builder post.
 *
 * The templates upload will be chunked into several POST requests with size 30 templates per request.
 * Hence we need to store the uploaded templates data into temporary file in cache directory before
 * making changes into database.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_api_save() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_save', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is done in `et_builder_security_check`.
	$_                   = et_();
	$live                = '1' === $_->array_get( $_POST, 'live', '0' );
	$first_request       = '1' === $_->array_get( $_POST, 'first_request', '1' );
	$last_request        = '1' === $_->array_get( $_POST, 'last_request', '1' );
	$templates           = wp_unslash( $_->array_get( $_POST, 'templates', array() ) );
	$processed_templates = wp_unslash( $_->array_get( $_POST, 'processed_templates', array() ) );
	$library_tb_id       = (int) $_->array_get( $_POST, 'library_theme_builder_id', 0 );
	$library_item_id     = (int) $_->array_get( $_POST, 'library_item_id', 0 );
	$theme_builder_id    = $library_tb_id ? $library_tb_id : et_theme_builder_get_theme_builder_post_id( $live, true );
	$has_default         = '1' === $_->array_get( $_POST, 'hasDefault', '0' );
	$updated_ids         = array();
	// phpcs:enable

	// Remove this action as it not necessary when we're saving entire TB.
	// save_post_cb is a heavy operation and significanlty slows down the saving of TB.
	// We remove static page resources after TB save below in this function.
	remove_action( 'save_post', array( 'ET_Core_PageResource', 'save_post_cb' ), 10, 3 );

	$templates_to_process = array();

	// Populate the templates.
	foreach ( $templates as $index => $template ) {
		$templates_to_process[ $_->array_get( $template, 'id', 'unsaved_' . $index ) ] = $template;
	}

	$affected_templates = array();

	// Update or insert templates.
	foreach ( $templates_to_process as $template ) {
		$raw_post_id = $_->array_get( $template, 'id', 0 );
		$post_id     = is_numeric( $raw_post_id ) ? (int) $raw_post_id : 0;
		$new_post_id = et_theme_builder_store_template( $theme_builder_id, $template, ! $has_default );

		if ( ! $new_post_id ) {
			continue;
		}

		$is_default = get_post_meta( $new_post_id, '_et_default', true ) === '1';

		if ( $is_default ) {
			$has_default = true;
		}

		// Add template ID into $affected_templates for later use
		// to Add mapping template ID to theme builder ID
		// and delete existing template mapping.
		$affected_templates[] = array(
			'raw'         => $raw_post_id,
			'normalized'  => $post_id,
			'new_post_id' => $new_post_id,
		);
	}

	foreach ( $affected_templates as $template_pair ) {
		if ( $template_pair['normalized'] !== $template_pair['new_post_id'] ) {
			$updated_ids[ $template_pair['raw'] ] = $template_pair['new_post_id'];
		}
	}

	if ( $last_request ) {
		$existing_templates = get_post_meta( $theme_builder_id, '_et_template', false );

		if ( $existing_templates ) {
			// Store existing template mapping as backup to avoid data lost
			// when user interrupting the saving process before completed.
			update_option( 'et_tb_templates_backup_' . $theme_builder_id, $existing_templates );
		}

		// Delete existing template mapping.
		delete_post_meta( $theme_builder_id, '_et_template' );

		$processed_templates = array_merge( $processed_templates, $affected_templates );

		// Insert new template mapping.
		foreach ( $processed_templates as $template_pair ) {
			add_post_meta( $theme_builder_id, '_et_template', $template_pair['new_post_id'] );
		}

		// Delete existing template mapping backup.
		delete_option( 'et_tb_templates_backup_' . $theme_builder_id );

		if ( $live ) {
			et_theme_builder_trash_draft_and_unused_posts();
		}

		et_theme_builder_clear_wp_cache( 'all' );

		// Remove static resources on save. It's necessary because how we are generating the dynamic assets for the TB.
		ET_Core_PageResource::remove_static_resources( 'all', 'all', false, 'dynamic' );
	}

	// Edit Template and Edit Preset: Save the templates into local library.
	if ( $library_tb_id && $library_item_id ) {
		et_theme_builder_update_library_item( $library_item_id, $templates );
	}

	// Add this action back.
	add_action( 'save_post', array( 'ET_Core_PageResource', 'save_post_cb' ), 10, 3 );

	wp_send_json_success(
		array(
			'updatedTemplateIds'     => (object) $updated_ids,
			'processedTemplatesData' => (object) $affected_templates,
			'hasDefault'             => $has_default ? '1' : '0',
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_save', 'et_theme_builder_api_save' );

/**
 * Drop the theme builder post autosave.
 *
 * @since 4.0
 *
 * @return void
 */
function et_theme_builder_api_drop_autosave() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_drop_autosave', 'nonce' );

	et_theme_builder_trash_draft_and_unused_posts();

	wp_send_json_success();
}
add_action( 'wp_ajax_et_theme_builder_api_drop_autosave', 'et_theme_builder_api_drop_autosave' );

function et_theme_builder_api_get_template_settings() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_get_template_settings', 'nonce', '_GET' );

	$parent   = isset( $_GET['parent'] ) ? sanitize_text_field( $_GET['parent'] ) : '';
	$search   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
	$page     = isset( $_GET['page'] ) ? (int) $_GET['page'] : 1;
	$page     = $page >= 1 ? $page : 1;
	$per_page = 30;
	$settings = et_theme_builder_get_flat_template_settings_options();

	if ( ! isset( $settings[ $parent ] ) || empty( $settings[ $parent ]['options'] ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid parent setting specified.', 'et_builder' ),
			)
		);
	}

	$setting = $settings[ $parent ];
	$results = et_theme_builder_get_template_setting_child_options( $setting, array(), $search, $page, $per_page );

	wp_send_json_success(
		array(
			'results' => array_values( $results ),
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_get_template_settings', 'et_theme_builder_api_get_template_settings' );

function et_theme_builder_api_reset() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_reset', 'nonce' );

	$live_id = et_theme_builder_get_theme_builder_post_id( true, false );

	if ( $live_id > 0 && current_user_can( 'delete_others_posts' ) ) {
		wp_trash_post( $live_id );
		// Reset cache when theme builder is reset.
		ET_Core_PageResource::remove_static_resources( 'all', 'all', true );
	}

	et_theme_builder_trash_draft_and_unused_posts();

	wp_send_json_success();
}
add_action( 'wp_ajax_et_theme_builder_api_reset', 'et_theme_builder_api_reset' );

function et_theme_builder_api_export_theme_builder() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check(
		'et_theme_builder_portability',
		et_core_portability_cap( 'et_theme_builder' ),
		'et_theme_builder_api_export_theme_builder',
		'nonce'
	);

	$_              = et_();
	$raw_templates  = wp_unslash( $_->array_get( $_POST, 'templates', array() ) );
	$global_layouts = array(
		'header' => (int) $_->array_get( $_POST, 'global_layouts.header', 0 ),
		'body'   => (int) $_->array_get( $_POST, 'global_layouts.body', 0 ),
		'footer' => (int) $_->array_get( $_POST, 'global_layouts.footer', 0 ),
	);
	$has_default    = false;
	$steps          = array();

	foreach ( $raw_templates as $template ) {
		$is_default = ! $has_default && '1' === $_->array_get( $template, 'default', '0' );

		if ( $is_default ) {
			$has_default = true;
		}

		$sanitized = et_theme_builder_sanitize_template(
			array_merge(
				$template,
				array(
					'default' => $is_default ? '1' : '0',
				)
			)
		);

		$template_item_id = isset( $template['item_id'] ) ? absint( $template['item_id'] ) : 0;
		if ( $template_item_id > 0 ) {
			$sanitized = array_merge(
				$sanitized,
				[
					'description' => et_theme_builder_library_get_item_description( $template['item_id'], $is_default ),
				]
			);
		} else {
			$sanitized = array_merge(
				$sanitized,
				[
					'description' => et_theme_builder_library_get_item_description_from_payload( $template ),
				]
			);
		}

		$steps[] = array(
			'type' => 'template',
			'data' => $sanitized,
		);

		$layout_keys = array( 'header', 'body', 'footer' );
		foreach ( $layout_keys as $key ) {
			$layout_id = (int) $_->array_get( $sanitized, array( 'layouts', $key, 'id' ), 0 );

			if ( 0 === $layout_id ) {
				continue;
			}

			$steps[] = array(
				'type' => 'layout',
				'data' => array(
					'post_id'   => $layout_id,
					'is_global' => $layout_id === $global_layouts[ $key ],
				),
			);
		}
	}

	$presets_manager = ET_Builder_Global_Presets_Settings::instance();
	$presets         = $presets_manager->get_global_presets();

	if ( ! empty( $presets ) ) {
		$steps[] = array(
			'type' => 'presets',
			'data' => $presets,
		);
	}

	$id        = md5( get_current_user_id() . '_' . uniqid( 'et_theme_builder_export_', true ) );
	$transient = 'et_theme_builder_export_' . get_current_user_id() . '_' . $id;

	set_transient(
		$transient,
		array(
			'ready'        => false,
			'steps'        => $steps,
			'temp_file'    => '',
			'temp_file_id' => '',
		),
		60 * 60 * 24
	);

	wp_send_json_success(
		array(
			'id'    => $id,
			'steps' => count( $steps ),
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_export_theme_builder', 'et_theme_builder_api_export_theme_builder' );

function et_theme_builder_api_export_theme_builder_step() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check(
		'et_theme_builder_portability',
		et_core_portability_cap( 'et_theme_builder' ),
		'et_theme_builder_api_export_theme_builder',
		'nonce'
	);

	$_         = et_();
	$id        = sanitize_text_field( $_->array_get( $_POST, 'id', '' ) );
	$step      = (int) $_->array_get( $_POST, 'step', 0 );
	$chunk     = (int) $_->array_get( $_POST, 'chunk', 0 );
	$transient = 'et_theme_builder_export_' . get_current_user_id() . '_' . $id;
	$export    = get_transient( $transient );

	if ( false === $export || ! isset( $export['steps'][ $step ] ) ) {
		wp_send_json_error();
	}

	$portability = et_core_portability_load( 'et_theme_builder' );
	$export_step = isset( $export['steps'][ $step ] ) ? $export['steps'][ $step ] : array();
	$result      = $portability->export_theme_builder( $id, $export_step, count( $export['steps'] ), $step, $chunk );

	if ( false === $result ) {
		wp_send_json_error();
	}

	if ( $result['ready'] ) {
		set_transient(
			$transient,
			array_merge(
				$export,
				array(
					'ready'        => $result['ready'],
					'temp_file'    => $result['temp_file'],
					'temp_file_id' => $result['temp_file_id'],
				)
			),
			60 * 60 * 24
		);
	}

	wp_send_json_success(
		array(
			'chunks' => $result['chunks'],
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_export_theme_builder_step', 'et_theme_builder_api_export_theme_builder_step' );

function et_theme_builder_api_export_theme_builder_download() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check(
		'et_theme_builder_portability',
		et_core_portability_cap( 'et_theme_builder' ),
		'et_theme_builder_api_export_theme_builder',
		'nonce',
		'_GET'
	);

	$_         = et_();
	$id        = sanitize_text_field( $_->array_get( $_GET, 'id', '' ) );
	$filename  = sanitize_text_field( $_->array_get( $_GET, 'filename', '' ) );
	$filename  = '' !== $filename ? $filename : 'Divi Theme Builder Templates';
	$filename  = sanitize_file_name( $filename );
	$transient = 'et_theme_builder_export_' . get_current_user_id() . '_' . $id;
	$export    = get_transient( $transient );

	if ( false === $export || ! $export['ready'] ) {
		wp_send_json_error();
	}

	$portability = et_core_portability_load( 'et_theme_builder' );
	$portability->download_file( $filename, $export['temp_file_id'], $export['temp_file'] );
}
add_action( 'wp_ajax_et_theme_builder_api_export_theme_builder_download', 'et_theme_builder_api_export_theme_builder_download' );

/**
 * Save a layout in a temporary file to prepare it for import.
 *
 * @since 4.1.0
 *
 * @param ET_Core_Portability $portability Portability object.
 * @param string              $template_id Template ID.
 * @param integer             $layout_id   Layout ID.
 * @param array               $layout      Layout.
 * @param string              $temp_id     Temporary ID.
 * @param string              $temp_group  Temporary Group.
 */
function et_theme_builder_api_import_theme_builder_save_layout( $portability, $template_id, $layout_id, $layout, $temp_id, $temp_group ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_send_json_error();
	}

	if ( ! empty( $layout['images'] ) ) {
		// Split up images into individual temporary files
		// to avoid hitting the memory limit.
		foreach ( $layout['images'] as $url => $data ) {
			$image_temp_id = $temp_id . '-image-' . md5( $url );

			$portability->temp_file( $image_temp_id, $temp_group, false, wp_json_encode( $data ) );

			$layout['images'][ $url ] = array(
				'id'    => $image_temp_id,
				'group' => $temp_group,
			);
		}
	}

	$portability->temp_file(
		$temp_id,
		$temp_group,
		false,
		wp_json_encode(
			array(
				'type'        => 'layout',
				'data'        => $layout,
				'id'          => $layout_id,
				'template_id' => $template_id,
			)
		)
	);
}

/**
 * Load a previously saved layout from a temporary file.
 *
 * @since 4.1.0
 *
 * @param ET_Core_Portability $portability Portability Object.
 * @param string              $temp_id     Temporary ID.
 * @param string              $temp_group  Temporary Group.
 *
 * @return array
 */
function et_theme_builder_api_import_theme_builder_load_layout( $portability, $temp_id, $temp_group ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_send_json_error();
	}

	$import = $portability->get_temp_file_contents( $temp_id, $temp_group );
	$import = ! empty( $import ) ? json_decode( $import, true ) : array();
	$images = et_()->array_get( $import, array( 'data', 'images' ), array() );

	// Hydrate images back from their individual temporary files.
	foreach ( $images as $url => $file ) {
		$import['data']['images'][ $url ] = json_decode( $portability->get_temp_file_contents( $file['id'], $file['group'] ), true );
	}

	return $import;
}

function et_theme_builder_api_import_theme_builder() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_send_json_error();
	}

	$i18n = array_merge(
		require ET_BUILDER_DIR . 'frontend-builder/i18n/generic.php',
		require ET_BUILDER_DIR . 'frontend-builder/i18n/portability.php',
		require ET_BUILDER_DIR . 'frontend-builder/i18n/theme-builder.php'
	);

	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error(
			array(
				'code'  => ET_Theme_Builder_Api_Errors::UNKNOWN,
				'error' => $i18n['An unknown error has occurred. Please try again later.'],
			)
		);
	}

	et_builder_security_check(
		'et_theme_builder_portability',
		et_core_portability_cap( 'et_theme_builder' ),
		'et_theme_builder_api_import_theme_builder',
		'nonce'
	);

	if ( ! isset( $_FILES['file']['name'] ) || ! et_()->ends_with( sanitize_file_name( $_FILES['file']['name'] ), '.json' ) ) {
		wp_send_json_error(
			array(
				'code'  => ET_Theme_Builder_Api_Errors::PORTABILITY_IMPORT_INVALID_FILE,
				'error' => $i18n['$invalid_file'],
			)
		);
	}

	$_      = et_();
	$upload = wp_handle_upload(
		$_FILES['file'],
		array(
			'test_size' => false,
			'test_type' => false,
			'test_form' => false,
		)
	);

	if ( ! $_->array_get( $upload, 'file', null ) ) {
		wp_send_json_error(
			array(
				'code'  => ET_Theme_Builder_Api_Errors::UNKNOWN,
				'error' => $i18n['An unknown error has occurred. Please try again later.'],
			)
		);
	}

	$export = json_decode( et_()->WPFS()->get_contents( $upload['file'] ), true );

	if ( null === $export ) {
		wp_send_json_error(
			array(
				'code'  => ET_Theme_Builder_Api_Errors::UNKNOWN,
				'error' => $i18n['An unknown error has occurred. Please try again later.'],
			)
		);
	}

	$portability = et_core_portability_load( 'et_theme_builder' );

	if ( ! $portability->is_valid_theme_builder_export( $export ) ) {
		wp_send_json_error(
			array(
				'code'  => ET_Theme_Builder_Api_Errors::PORTABILITY_INCORRECT_CONTEXT,
				'error' => $i18n['This file should not be imported in this context.'],
			)
		);
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verfied in `et_builder_security_check`.
	$override_default_website_template = '1' === $_->array_get( $_POST, 'override_default_website_template', '0' );
	$import_presets                    = '1' === $_->array_get( $_POST, 'import_presets', '0' );
	$library_template_import           = '1' === $_->array_get( $_POST, 'library_template_import', '0' );
	$has_default_template              = $_->array_get( $export, 'has_default_template', false );
	$has_global_layouts                = $_->array_get( $export, 'has_global_layouts', false );
	$presets                           = $_->array_get( $export, 'presets', array() );
	$presets_rewrite_map               = array();
	$incoming_layout_duplicate         = false;
	$uploaded_file_name                = substr( sanitize_file_name( $_FILES['file']['name'] ), 0, -5 );
	$cloud_item_editor                 = $_->array_get( $_POST, 'cloud_item_editor', '' );
	$temp_import                       = '1' === $_->array_get( $_POST, 'temp_import', '0' );
	$preset_prefix                     = $_->array_get( $_POST, 'preset_prefix', '' );
	$duplicate_presets                 = filter_var( $_->array_get( $_POST, 'duplicate_presets', true ), FILTER_VALIDATE_BOOLEAN );

	// Maybe ask the user to make a decision on how to deal with global layouts.
	if ( ( ! $override_default_website_template || ! $has_default_template ) && $has_global_layouts ) {
		$incoming_layout_duplicate_decision = $_->array_get( $_POST, 'incoming_layout_duplicate_decision', '' );

		if ( 'duplicate' === $incoming_layout_duplicate_decision || $library_template_import ) {
			$incoming_layout_duplicate = true;
		} elseif ( 'relink' === $incoming_layout_duplicate_decision ) {
			$incoming_layout_duplicate = false;
		} else {
			wp_send_json_error(
				array(
					'code'  => ET_Theme_Builder_Api_Errors::PORTABILITY_REQUIRE_INCOMING_LAYOUT_DUPLICATE_DECISION,
					'error' => $i18n['This import contains references to global layouts.'],
				)
			);
		}
	}
	// phpcs:enable

	// Make imported preset overrides to avoid collisions with local presets.
	if ( $import_presets && is_array( $presets ) && ! empty( $presets ) && ! $preset_prefix ) {
		$presets_rewrite_map = $portability->prepare_to_import_layout_presets( $presets );
	}

	// Prepare import steps.
	$layout_id_map = array();
	$layout_keys   = array( 'header', 'body', 'footer' );
	$id            = md5( get_current_user_id() . '_' . uniqid( 'et_theme_builder_import_', true ) );
	$transient     = 'et_theme_builder_import_' . get_current_user_id() . '_' . $id;
	$steps_files   = array();

	foreach ( $export['templates'] as $index => $template ) {
		foreach ( $layout_keys as $key ) {
			$layout_id = (int) $_->array_get( $template, array( 'layouts', $key, 'id' ), 0 );

			if ( 0 === $layout_id ) {
				continue;
			}

			$layout = $_->array_get( $export, array( 'layouts', $layout_id ), null );

			if ( empty( $layout ) ) {
				continue;
			}

			// Use a temporary string id to avoid numerical keys being reset by various array functions.
			$template_id = 'template_' . $index;
			$is_global   = (bool) $_->array_get( $layout, 'theme_builder.is_global', false );
			$create_new  = ( $template['default'] && $override_default_website_template ) || ! $is_global || $incoming_layout_duplicate;

			if ( $create_new ) {
				$temp_id = 'tbi-step-' . count( $steps_files );

				et_theme_builder_api_import_theme_builder_save_layout( $portability, $template_id, $layout_id, $layout, $temp_id, $transient );

				$steps_files[] = array(
					'id'    => $temp_id,
					'group' => $transient,
				);
			} else {
				if ( ! isset( $layout_id_map[ $layout_id ] ) ) {
					$layout_id_map[ $layout_id ] = array();
				}

				$layout_id_map[ $layout_id ][ $template_id ] = 'use_global';
			}
		}
	}

	set_transient(
		$transient,
		array(
			'file_name'                         => $uploaded_file_name,
			'ready'                             => false,
			'steps'                             => $steps_files,
			'templates'                         => $export['templates'],
			'override_default_website_template' => $override_default_website_template,
			'incoming_layout_duplicate'         => $incoming_layout_duplicate,
			'layout_id_map'                     => $layout_id_map,
			'presets'                           => $presets,
			'import_presets'                    => $import_presets,
			'library_template_import'           => $library_template_import,
			'presets_rewrite_map'               => $presets_rewrite_map,
			'cloud_item_editor'                 => $cloud_item_editor,
			'temp_import'                       => $temp_import,
			'duplicate_presets'                 => $duplicate_presets,
			'preset_prefix'                     => $preset_prefix,
		),
		60 * 60 * 24
	);

	wp_send_json_success(
		array(
			'id'    => $id,
			'steps' => count( $steps_files ),
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_import_theme_builder', 'et_theme_builder_api_import_theme_builder' );

function et_theme_builder_api_import_theme_builder_step() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check(
		'et_theme_builder_portability',
		et_core_portability_cap( 'et_theme_builder' ),
		'et_theme_builder_api_import_theme_builder',
		'nonce'
	);

	$_         = et_();
	$id        = sanitize_text_field( $_->array_get( $_POST, 'id', '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is done in `et_builder_security_check`.
	$step      = (int) $_->array_get( $_POST, 'step', 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is done in `et_builder_security_check`.
	$chunk     = (int) $_->array_get( $_POST, 'chunk', 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is done in `et_builder_security_check`.
	$transient = 'et_theme_builder_import_' . get_current_user_id() . '_' . $id;
	$export    = get_transient( $transient );

	if ( false === $export ) {
		wp_send_json_error();
	}

	$layout_keys             = array( 'header', 'body', 'footer' );
	$portability             = et_core_portability_load( 'et_theme_builder' );
	$steps                   = $export['steps'];
	$ready                   = empty( $steps );
	$layout_id_map           = $export['layout_id_map'];
	$presets                 = $export['presets'];
	$presets_rewrite_map     = $export['presets_rewrite_map'];
	$import_presets          = $export['import_presets'];
	$library_template_import = $export['library_template_import'];
	$file_name               = $export['file_name'];
	$cloud_item_editor       = $export['cloud_item_editor'];
	$temp_import             = $export['temp_import'];
	$duplicate_presets       = $export['duplicate_presets'];
	$preset_prefix           = $export['preset_prefix'];
	$templates               = array();
	$template_settings       = array();
	$chunks                  = 1;
	$preset_id               = 0;

	if ( ! $ready ) {
		$import_step                      = et_theme_builder_api_import_theme_builder_load_layout( $portability, $steps[ $step ]['id'], $steps[ $step ]['group'] );
		$import_step                      = array_merge( $import_step, array( 'presets' => $presets ) );
		$import_step                      = array_merge( $import_step, array( 'presets_rewrite_map' => $presets_rewrite_map ) );
		$import_step['import_presets']    = $import_presets;
		$import_step['rewrite_preset_id'] = ! empty( $preset_prefix );

		if ( $temp_import ) {
			$import_step['data']['post_status'] = 'draft';
		}

		$result = $portability->import_theme_builder( $id, $import_step, count( $steps ), $step, $chunk );

		if ( false === $result ) {
			wp_send_json_error();
		}

		$ready  = $result['ready'];
		$chunks = $result['chunks'];

		foreach ( $result['layout_id_map'] as $old_id => $new_ids ) {
			$layout_id_map[ $old_id ] = array_merge(
				$_->array_get( $layout_id_map, $old_id, array() ),
				$new_ids
			);
		}
	}

	if ( $ready ) {
		if ( $import_presets && is_array( $presets ) && ! empty( $presets ) ) {
			if ( false === $duplicate_presets && ! $preset_prefix ) {
				$presets = $portability->prepare_to_import_non_duplicate_presets( $presets );
			}

			$override_defaults = ! empty( $preset_prefix );

			if ( ! $portability->import_global_presets( $presets, false, $override_defaults, $preset_prefix, true ) ) {
				$presets_error = apply_filters( 'et_core_portability_import_error_message', '' );

				if ( $presets_error ) {
					wp_send_json_error(
						array(
							'code'  => ET_Theme_Builder_Api_Errors::PORTABILITY_IMPORT_PRESETS_FAILURE,
							'error' => $presets_error,
						)
					);
				}
			}
		}

		$portability->delete_temp_files( $transient );

		$conditions     = array();
		$global_layouts = array();

		foreach ( $export['templates'] as $index => $template ) {
			$sanitized  = et_theme_builder_sanitize_template( $template );
			$is_default = $_->array_get( $sanitized, 'default', false );

			foreach ( $layout_keys as $key ) {
				$old_layout_id = (int) $_->array_get( $sanitized, array( 'layouts', $key, 'id' ), 0 );
				$layout_id     = et_()->array_get( $layout_id_map, array( $old_layout_id, 'template_' . $index ), '' );
				$layout_id     = ! empty( $layout_id ) ? $layout_id : 0;

				$_->array_set( $sanitized, array( 'layouts', $key, 'id' ), $layout_id );

				if ( $is_default ) {
					$global_layouts[ $key ]['id'] = $layout_id;
				}
			}

			$conditions = array_merge( $conditions, $sanitized['use_on'], $sanitized['exclude_from'] );
			$_->array_set( $sanitized, array( 'global_layouts' ), $global_layouts );

			$templates[] = $sanitized;
		}

		// Load all conditions from templates.
		$conditions        = array_unique( $conditions );
		$template_settings = array_replace(
			et_theme_builder_get_flat_template_settings_options(),
			et_theme_builder_load_template_setting_options( $conditions )
		);
		$valid_settings    = array_keys( $template_settings );

		// Strip all invalid conditions from templates.
		foreach ( $templates as $index => $template ) {
			$templates[ $index ]['use_on']       = array_values( array_intersect( $template['use_on'], $valid_settings ) );
			$templates[ $index ]['exclude_from'] = array_values( array_intersect( $template['exclude_from'], $valid_settings ) );
		}

		if ( $library_template_import ) {
			$is_multi_template = count( $templates ) > 1;

			if ( $is_multi_template || 'set' === $cloud_item_editor ) {
				$template_settings['set_name'] = $file_name;

				foreach ( $templates as $key => $template ) {
					foreach ( array( 'body', 'header', 'footer' ) as $layout_type ) {
						$layout_id = $_->array_get( $template, array( 'layouts', $layout_type, 'id' ) );
						if ( 'use_global' === $layout_id && isset( $global_layouts[ $layout_type ] ) ) {
							$global_layout_id = $_->array_get( $global_layouts, array( $layout_type, 'id' ) );
							$_->array_set( $templates, array( $key, 'layouts', $layout_type, 'id' ), $global_layout_id );
						}
					}
				}

				if ( $temp_import ) {
					$template_settings['post_status'] = 'draft';
				}

				$preset_id = et_theme_builder_save_preset_to_library( $templates, $template_settings );
			} else {
				$first_template = $templates[0];
				if ( 'template' === $cloud_item_editor ) {
					$template_settings['template_name'] = $first_template['title'];
				} else {
					$template_settings['template_name'] = $file_name;
				}

				if ( $temp_import ) {
					$first_template['status'] = 'draft';
				}

				$templates[0]['template_id'] = et_theme_builder_save_template_to_library( $first_template, $template_settings );
			}
		}
	} else {
		set_transient(
			$transient,
			array_merge(
				$export,
				array(
					'layout_id_map' => $layout_id_map,
				)
			),
			60 * 60 * 24
		);
	}

	wp_send_json_success(
		array(
			'presetId'         => $preset_id,
			'chunks'           => $chunks,
			'templates'        => $templates,
			'templateSettings' => $template_settings,
		)
	);
}
add_action( 'wp_ajax_et_theme_builder_api_import_theme_builder_step', 'et_theme_builder_api_import_theme_builder_step' );

/**
 * Ajax action: save template into the local library.
 */
function et_theme_builder_api_save_template_to_library() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_save_template_to_library', 'nonce' );

	$_ = et_();

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.
	$raw_templates = wp_unslash( $_->array_get( $_POST, 'template', array() ) );
	$preferences   = wp_unslash( $_->array_get( $_POST, 'preferences', array() ) );
	// phpcs:enable

	$post_id = et_theme_builder_save_template_to_library( $raw_templates, $preferences );

	if ( $post_id ) {
		wp_send_json_success(
			array(
				'post_id' => $post_id,
			)
		);
	} else {
		wp_send_json_error();
	}
}

add_action( 'wp_ajax_et_theme_builder_api_save_template_to_library', 'et_theme_builder_api_save_template_to_library' );

/**
 * Ajax action: save preset into the local library.
 */
function et_theme_builder_api_save_preset_to_library() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_save_preset_to_library', 'nonce' );

	$_             = et_();
	$preferences   = wp_unslash( $_->array_get( $_POST, 'preferences', [] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.
	$raw_templates = wp_unslash( $_->array_get( $_POST, 'templates', [] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.

	$post_id = et_theme_builder_save_preset_to_library( $raw_templates, $preferences );

	if ( $post_id ) {
		wp_send_json_success(
			array(
				'post_id' => $post_id,
			)
		);
	} else {
		wp_send_json_error();
	}
}

add_action( 'wp_ajax_et_theme_builder_api_save_preset_to_library', 'et_theme_builder_api_save_preset_to_library' );

/**
 * Ajax action: Retrieve terms for the given taxonomy.
 */
function et_theme_builder_api_get_terms() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_get_terms', 'nonce' );

	$_   = et_();
	$tax = sanitize_text_field( $_->array_get( $_POST, 'tax', '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.

	if ( ! in_array( $tax, array( 'layout_category', 'layout_tag' ), true ) ) {
		wp_send_json_error();
	}

	$terms_by_id = et_theme_builder_get_terms( $tax );

	wp_send_json_success( $terms_by_id );
}

add_action( 'wp_ajax_et_theme_builder_api_get_terms', 'et_theme_builder_api_get_terms' );

/**
 * Ajax action: Use local library item.
 */
function et_theme_builder_api_use_library_item() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_api_use_library_item', 'nonce' );

	$i18n = array_merge(
		require ET_BUILDER_DIR . 'frontend-builder/i18n/generic.php',
		require ET_BUILDER_DIR . 'frontend-builder/i18n/portability.php',
		require ET_BUILDER_DIR . 'frontend-builder/i18n/theme-builder.php'
	);

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.
	$_                                  = et_();
	$item_id                            = (int) $_->array_get( $_POST, 'item_id', 0 );
	$override_default_website_template  = '1' === $_->array_get( $_POST, 'override_default_website_template', '0' );
	$override_assignments               = '1' === $_->array_get( $_POST, 'override_assignments', '0' );
	$download_backup                    = '1' === $_->array_get( $_POST, 'download_backup', '0' );
	$incoming_layout_duplicate_decision = $_->array_get( $_POST, 'incoming_layout_duplicate_decision', '' );
	$item_id                            = (int) $_->array_get( $_POST, 'item_id', 0 );
	// phpcs:enable

	$args = [
		'override_default_website_template'  => $override_default_website_template,
		'override_assignments'               => $override_assignments,
		'download_backup'                    => $download_backup,
		'incoming_layout_duplicate_decision' => $incoming_layout_duplicate_decision,
	];

	$return_additional_args = [];
	$library_item           = new ET_Theme_Builder_Local_Library_Item( $item_id );

	if ( ET_THEME_BUILDER_ITEM_SET === $library_item->get_item_type() ) {
		$has_global_layouts   = $library_item->has_global_layouts();
		$has_default_template = $library_item->has_default_template();
		$show_layout_decision = ( ! $override_default_website_template || ! $has_default_template ) && $has_global_layouts;

		if ( $show_layout_decision && empty( $incoming_layout_duplicate_decision ) ) {
			wp_send_json_error(
				array(
					'code'  => ET_Theme_Builder_Api_Errors::PORTABILITY_REQUIRE_INCOMING_LAYOUT_DUPLICATE_DECISION,
					'error' => $i18n['This import contains references to global layouts.'],
				)
			);
		}

		$return_additional_args = [
			'override_default_website_template'  => $override_default_website_template,
			'override_assignments'               => $override_assignments,
			'download_backup'                    => $download_backup,
			'incoming_layout_duplicate_decision' => $incoming_layout_duplicate_decision,
		];
	}

	$item_data = $library_item->use_library_item( $args );

	if ( is_wp_error( $item_data ) ) {
		wp_send_json_error();
	}

	$data = array_merge(
		array(
			'item_type' => $library_item->item_type,
			'item_data' => $item_data,
		),
		$return_additional_args
	);

	wp_send_json_success( $data );
}

add_action( 'wp_ajax_et_theme_builder_api_use_library_item', 'et_theme_builder_api_use_library_item' );

/**
 * Ajax action: Trash interim library editor posts.
 */
function et_theme_builder_trash_theme_builder() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_trash_theme_builder', 'nonce' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.
	$_                = et_();
	$theme_builder_id = (int) $_->array_get( $_POST, 'theme_builder_id', 0 );
	$template_ids     = et_theme_builder_get_theme_builder_template_ids( true, $theme_builder_id );
	$item_id          = (int) $_->array_get( $_POST, 'item_id' );
	$cloud_item_id    = (int) $_->array_get( $_POST, 'cloud_item_id' );
	$used_posts       = array();
	// phpcs:enable

	foreach ( $template_ids as $template_id ) {
		foreach ( array( 'header', 'body', 'footer' ) as $layout_type ) {
			$layout_id = get_post_meta( $template_id, "_et_{$layout_type}_layout_id", true );

			// Clean layouts.
			if ( $layout_id ) {
				$used_posts[] = $layout_id;
			}
		}

		// Clean template.
		$used_posts[] = $template_id;
	}

	$used_posts[] = $theme_builder_id;

	$used_posts = array_map( 'intval', $used_posts );
	foreach ( $used_posts as $used_post ) {
		if ( current_user_can( 'delete_others_posts' ) ) {
			wp_trash_post( $used_post );
		}
	}

	// Delete local library item.
	if ( $cloud_item_id ) {
		if ( current_user_can( 'delete_others_posts' ) ) {
			wp_delete_post( $item_id );
		}
	}
}

add_action( 'wp_ajax_et_theme_builder_trash_theme_builder', 'et_theme_builder_trash_theme_builder' );

/**
 * AJAX action: Gets items data for the theme builder's library UI.
 */
function et_theme_builder_library_get_items_data() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_get_items_data', 'nonce' );

	$item_type = isset( $_POST['et_item_type'] ) ? (string) sanitize_text_field( $_POST['et_item_type'] ) : 'template';

	if ( ! in_array( $item_type, array( 'set', 'template' ), true ) ) {
		wp_send_json_error( 'Error: Wrong item type provided.' );
	}

	$item_library_local = et_pb_theme_builder_library_local();
	$data               = $item_library_local->get_library_items( $item_type );

	wp_send_json_success(
		$data
	);
}

add_action( 'wp_ajax_et_theme_builder_library_get_items_data', 'et_theme_builder_library_get_items_data' );


/**
 * AJAX action: Add/Remove/Rename Library terms for taxonomies.
 */
function et_theme_builder_library_update_terms() {
	et_builder_security_check( 'theme_builder', 'manage_categories', 'et_theme_builder_library_update_terms', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done at the time of accessing value.

	$et_library_taxonomy = isset( $_POST['et_library_taxonomy'] ) ? (string) $_POST['et_library_taxonomy'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['et_library_taxonomy'] is a string, it's value sanitization is done at the time of accessing value.

	$response = et_theme_builder_library_update_taxonomy_terms( $payload, $et_library_taxonomy );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Please provide valid payload and taxonomy' );
	}

	return wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_builder_library_update_terms', 'et_theme_builder_library_update_terms' );

/**
 * AJAX action: Update the theme builder library item.
 */
function et_theme_builder_library_update_item() {
	if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
		wp_send_json_error();
	}

	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_update_item', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	if ( empty( $payload ) ) {
		wp_send_json_error( 'Error: Payload is empty.' );
	}

	$item_library_local = et_pb_theme_builder_library_local();
	$response           = $item_library_local->perform_item_update( $payload );

	if ( ! $response ) {
		wp_send_json_error( 'Error: Provide valid data.' );
	}

	return wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_builder_library_update_item', 'et_theme_builder_library_update_item' );

/**
 * AJAX action: Save the theme builder library temporary item.
 */
function et_theme_builder_library_save_temp_layout() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_save_temp_layout', 'nonce' );

	// phpcs:disable -- Sanitization will be handled inside the method.
	$local_content = isset( $_POST['localContent'] ) ? (array) $_POST['localContent'] : array();
	$cloud_content = isset( $_POST['cloudContent'] ) ? (array) $_POST['cloudContent'] : array();
	// phpcs:enable

	$templates      = array();
	$global_layouts = array();
	$is_cloud_item  = ! empty( $cloud_content['layouts'] );
	$data           = $is_cloud_item ? $cloud_content['templates'] : $local_content;

	foreach ( $data as $template ) :
		if ( $is_cloud_item ) {
			$is_default = filter_var( $template['default'], FILTER_VALIDATE_BOOLEAN );

			$template = array_merge(
				$template,
				et_theme_builder_library_save_temp_cloud_layout_data( $template, $cloud_content['layouts'], $global_layouts )
			);
		} else {
			$post = get_post( $template['id'] );

			if ( ! $post ) {
				continue;
			}

			$content    = json_decode( $post->post_content );
			$is_default = get_post_meta( $post->ID, '_et_default', true );

			$template = array_merge(
				$template,
				et_theme_builder_library_save_temp_local_layout_data( $post->ID, $content, $global_layouts )
			);
		}

		// Set global references.
		$layouts      = $template['layouts'];
		$layout_types = array( 'header', 'body', 'footer' );

		foreach ( $layout_types as $layout_type ) {
			if ( ! isset( $layouts[ $layout_type ] ) ) {
				continue;
			}

			if ( $is_cloud_item ) {
				$global_info = et_()->array_get( $template, $layout_type . '_layout_global', false );
				$is_global   = filter_var( $global_info, FILTER_VALIDATE_BOOLEAN );
			} else {
				$is_global = get_post_meta( $post->ID, '_et_' . $layout_type . '_layout_global', true );
			}

			if ( $is_default || $is_global ) {
				$global_layouts[ $layout_type ] = $layouts[ $layout_type ]['id'];
			}
		}

		array_push( $templates, $template );
	endforeach;

	$response = array(
		'templates'      => $templates,
		'global_layouts' => $global_layouts,
	);

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_et_theme_builder_library_save_temp_layout', 'et_theme_builder_library_save_temp_layout' );

/**
 * AJAX action: Remove the theme builder library temporary item.
 */
function et_theme_builder_library_remove_temp_layout() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_remove_temp_layout', 'nonce' );

	$payload = isset( $_POST['payload'] ) ? (array) $_POST['payload'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['payload'] is an array, it's value sanitization is done  at the time of accessing value.

	foreach ( $payload as $template ) {
		et_theme_builder_library_remove_temp_layout_data( $template );
	}

	wp_send_json_success();
}

add_action( 'wp_ajax_et_theme_builder_library_remove_temp_layout', 'et_theme_builder_library_remove_temp_layout' );

/**
 * AJAX action: Gets an item by ID.
 */
function et_theme_builder_library_get_item() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_get_item', 'nonce' );

	$id        = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
	$item_type = isset( $_POST['itemType'] ) ? (string) sanitize_text_field( $_POST['itemType'] ) : 'template';

	if ( empty( $id ) || ! in_array( $item_type, array( 'set', 'template' ), true ) ) {
		wp_send_json_error( 'Error: Please provide an ID and a valid item type.' );
	}

	$result   = array();
	$items_id = array( $id );

	if ( ET_THEME_BUILDER_ITEM_SET === $item_type ) {
		$items_id            = array();
		$callback            = 'et_theme_builder_library_get_' . ET_THEME_BUILDER_ITEM_SET . '_items_data';
		$items               = $callback( $id );
		$default_template_id = (int) get_post_meta( $id, '_et_default_template_id', true );

		foreach ( $items as $item ) {
			array_push( $items_id, $item->id );
		}
	}

	// Continue processing for both set and template.

	$result['exported'] = et_theme_builder_library_get_exported_content( $items_id );

	if (
		! isset( $result['exported']['context'] ) ||
		! isset( $result['exported']['templates'] ) ||
		! isset( $result['exported']['layouts'] )
	) {
		wp_send_json_error( 'Error: Invalid data.' );
	}

	$response = wp_json_encode(
		array(
			'success' => true,
			'data'    => $result,
		)
	);

	if ( ! $response ) {
		wp_send_json_error( 'Error: Invalid response.' );
	}

	$tmp_dir = function_exists( 'sys_get_temp_dir' ) ? sys_get_temp_dir() : '/tmp';

	$tmp_file = tempnam( $tmp_dir, 'et' );

	et_()->WPFS()->put_contents( $tmp_file, $response );

	// Remove any previous buffered content since we're setting `Content-Length` header
	// based on $response value only.
	while ( ob_get_level() ) {
		ob_end_clean();
	}

	header( 'Content-Length: ' . @filesize( $tmp_file ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- `filesize` may fail due to the permissions denied error.

	@unlink( $tmp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- `unlink` may fail due to the permissions denied error.

	// Charset has to be explicitly mentioned when it is other than UTF-8.
	header( 'Content-Type: application/json; charset=' . esc_attr( get_option( 'blog_charset' ) ) );

	die( et_core_intentionally_unescaped( $response, 'html' ) );
}

add_action( 'wp_ajax_et_theme_builder_library_get_item', 'et_theme_builder_library_get_item' );


/**
 * AJAX action: Get the theme builder library preset items.
 */
function et_theme_builder_library_get_set_items() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_get_set_items', 'nonce' );

	$item_id = isset( $_POST['itemId'] ) ? intval( $_POST['itemId'] ) : 0;

	if ( empty( $item_id ) ) {
		wp_send_json_error();
	}

	$items = et_theme_builder_library_get_set_items_data( $item_id );

	wp_send_json_success( $items );
}

add_action( 'wp_ajax_et_theme_builder_library_get_set_items', 'et_theme_builder_library_get_set_items' );

/**
 * Ajax action: Get default template id of the preset.
 */
function et_theme_builder_get_preset_default_template_id() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_get_preset_default_template_id', 'nonce' );

	$_                   = et_();
	$item_id             = (int) $_->array_get( $_POST, 'item_id', 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.
	$default_template_id = (int) get_post_meta( $item_id, '_et_default_template_id', true );

	if ( $default_template_id > 0 ) {
		wp_send_json_success( array( 'default_template_id' => $default_template_id ) );
	} else {
		wp_send_json_error( array( 'default_template_id' => 0 ) );
	}
}

add_action( 'wp_ajax_et_theme_builder_get_preset_default_template_id', 'et_theme_builder_get_preset_default_template_id' );

/**
 * Ajax action: Remove the Library item after it is moved to the Cloud.
 */
function et_theme_builder_library_toggle_cloud_status() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_toggle_cloud_status', 'nonce' );

	$post_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in `et_builder_security_check`.

	if ( ! $post_id ) {
		wp_send_json_error( 'Error: ID is required.' );
	}

	$post_type = get_post_type( $post_id );

	if ( ! current_user_can( 'edit_post', $post_id ) || ET_TB_ITEM_POST_TYPE !== $post_type ) {
		wp_send_json_error( 'You do not have permission.' );
	}

	wp_send_json_success( wp_delete_post( $post_id, true ) );
}

add_action( 'wp_ajax_et_theme_builder_library_toggle_cloud_status', 'et_theme_builder_library_toggle_cloud_status' );


/**
 * Ajax action: Remove temp layouts, templates theme builder.
 */
function et_theme_builder_library_clear_temp_data() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_clear_temp_data', 'nonce' );

	$args = array(
		'post_status'   => 'draft',
		'post_type'     => array( 'et_tb_item', 'et_header_layout', 'et_body_layout', 'et_footer_layout' ),
		'author'        => get_current_user_id(),
		'fields'        => 'ids',
		'no_found_rows' => true,
		'nopaging'      => true,
	);

	$draft_query = new WP_Query( $args );

	foreach ( $draft_query->posts as $draft_post_id ) {
		$post_type = get_post_type( $draft_post_id );

		if ( current_user_can( 'edit_post', $draft_post_id ) && ET_TB_ITEM_POST_TYPE === $post_type ) {
			wp_delete_post( $draft_post_id );
		}
	}

	wp_send_json_success();
}

add_action( 'wp_ajax_et_theme_builder_library_clear_temp_data', 'et_theme_builder_library_clear_temp_data' );


/**
 * AJAX action: Gets Cloud access token from DB and send it to client.
 */
function et_theme_builder_library_get_cloud_token() {
	et_builder_security_check( 'theme_builder', 'edit_others_posts', 'et_theme_builder_library_get_cloud_token', 'nonce' );

	wp_send_json_success(
		array(
			'accessToken' => get_transient( 'et_cloud_access_token' ),
		)
	);
}

add_action( 'wp_ajax_et_theme_builder_library_get_cloud_token', 'et_theme_builder_library_get_cloud_token' );
