<?php
/**
 * Builder ab testing.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.6.2
 */

// Prevent file from being loaded directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( -1 );
}

if ( ! defined( 'ET_PB_AB_DB_VERSION' ) ) {
	define( 'ET_PB_AB_DB_VERSION', '1.1' );
}

/**
 * AB Testing related data
 *
 * @param integer $post_id Post id.
 *
 * @return mixed|void {array} AB Testing related data
 */
function et_builder_ab_options( $post_id ) {
	$ab_options = array(
		'db_status'                  => true === et_pb_db_status_up_to_date() ? 'on' : 'off',
		'test_id'                    => $post_id,
		'has_report'                 => et_pb_ab_has_report( $post_id ),
		'has_permission'             => et_pb_is_allowed( 'ab_testing' ),
		'refresh_interval_duration'  => et_pb_ab_get_refresh_interval_duration( $post_id ),
		'refresh_interval_durations' => et_pb_ab_refresh_interval_durations(),
		'analysis_formula'           => et_pb_ab_get_analysis_formulas(),
		'have_conversions'           => et_pb_ab_get_modules_have_conversions(),
		'sales_title'                => esc_html__( 'Sales', 'et_builder' ),
		'total_title'                => esc_html__( 'Total', 'et_builder' ),

		// Saved data.
		'subjects_rank'              => ( 'on' === get_post_meta( $post_id, '_et_pb_use_builder', true ) ) ? et_pb_ab_get_saved_subjects_ranks( $post_id ) : false,

		// Rank color.
		'subjects_rank_color'        => et_pb_ab_get_subject_rank_colors(),
	);
	return apply_filters( 'et_builder_ab_options', $ab_options );
}

/**
 * Filterable AB Testing labels
 *
 * @return {array} AB Testing labels
 */
function et_builder_ab_labels() {
	$ab_settings = array(
		'alert_modal_defaults'                          => array(
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'select_subject'                                => array(
			'title' => esc_html__( 'Select Split Testing Subject', 'et_builder' ),
			'desc'  => esc_html__( 'You have activated the Divi Leads Split Testing System. Using split testing, you can create different element variations on your page to find out which variation most positively affects the conversion rate of your desired goal. After closing this window, please click on the section, row or module that you would like to split test.', 'et_builder' ),
		),
		'select_goal'                                   => array(
			'title'         => esc_html__( 'Select Your Goal', 'et_builder' ),
			'desc'          => esc_html__( 'Congratulations, you have selected a split testing subject! Next you need to select your goal. After closing this window, please click the section, row or module that you want to use as your goal. Depending on the element you choose, Divi will track relevant conversion rates for clicks, reads or sales. For example, if you select a Call To Action module as your goal, then Divi will track how variations in your test subjects affect how often visitors read and click the button in your Call To Action module. The test subject itself can also be selected as your goal.', 'et_builder' ),
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'configure_alternative'                         => array(
			'title'         => esc_html__( 'Configure Subject Variations', 'et_builder' ),
			'desc'          => esc_html__( 'Congratulations, your split test is ready to go! You will notice that your split testing subject has been duplicated. Each split testing variation will be displayed to your visitors and statistics will be collected to figure out which variation results in the highest goal conversion rate. Your test will begin when you save this page.', 'et_builder' ),
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'select_winner_first'                           => array(
			'title' => esc_html__( 'Select Split Testing Winner', 'et_builder' ),
			'desc'  => esc_html__( 'Before ending your split test, you must choose which split testing variation to keep. Please select your favorite or highest converting subject. Alternative split testing subjects will be removed and stats will be cleared.', 'et_builder' ),
		),
		'select_subject_first'                          => array(
			'title' => esc_html__( 'Select Split Testing Subject', 'et_builder' ),
			'desc'  => esc_html__( 'You need to select a split testing subject first.', 'et_builder' ),
		),
		'select_goal_first'                             => array(
			'title' => esc_html__( 'Select Split Testing Goal', 'et_builder' ),
			'desc'  => esc_html__( 'You need to select a split testing goal first. ', 'et_builder' ),
		),
		'cannot_select_subject_parent_as_goal'          => array(
			'title'         => esc_html__( 'Select A Different Goal', 'et_builder' ),
			'desc'          => esc_html__( 'This element cannot be used as a your split testing goal. Please select a different module, or section.', 'et_builder' ),
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'cannot_select_global_children_as_subject'      => array(
			'title' => esc_html__( 'Select a Different Subject', 'et_builder' ),
			'desc'  => esc_html__( 'This element cannot be used as split testing subject because it is part of global module. Please select different module, row, or section', 'et_builder' ),
		),
		'cannot_select_global_children_as_goal'         => array(
			'title' => esc_html__( 'Select a Different Goal', 'et_builder' ),
			'desc'  => esc_html__( 'This element cannot be used as split testing goal because it is part of global module. Please select different module, row, or section', 'et_builder' ),
		),
		'cannot_publish_finish_configuration_first'     => array(
			'title'         => esc_html__( 'Setup Split Test First', 'et_builder' ),
			'desc'          => esc_html__( 'You cannot publish the layout right now because you have incomplete split test configuration. Please finish the split test configuration first, then try saving again.', 'et_builder' ),
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'cannot_save_draft_finish_configuration_first'  => array(
			'title'         => esc_html__( 'Setup Split Test First', 'et_builder' ),
			'desc'          => esc_html__( 'You cannot save the layout right now because you have incomplete split test configuration. Please finish the split test configuration first, then try save draft again.', 'et_builder' ),
			'proceed_label' => esc_html__( 'Ok', 'et_builder' ),
		),
		'view_stats_thead_titles'                       => array(
			'clicks'                => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Clicks', 'et_builder' ),
				esc_html__( 'Clickthrough Rate', 'et_builder' ),
			),
			'reads'                 => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Reads', 'et_builder' ),
				esc_html__( 'Reading Rate', 'et_builder' ),
			),
			'bounces'               => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Stays', 'et_builder' ),
				esc_html__( 'Bounce Rate', 'et_builder' ),
			),
			'engagements'           => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Goal Views', 'et_builder' ),
				esc_html__( 'Goal Reads', 'et_builder' ),
				esc_html__( 'Engagement Rate', 'et_builder' ),
			),
			'conversions'           => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Conversion Goals', 'et_builder' ),
				esc_html__( 'Conversion Rate', 'et_builder' ),
			),
			'shortcode_conversions' => array(
				esc_html__( 'ID', 'et_builder' ),
				esc_html__( 'Subject', 'et_builder' ),
				esc_html__( 'Impressions', 'et_builder' ),
				esc_html__( 'Shortcode Conversions', 'et_builder' ),
				esc_html__( 'Conversion Rate', 'et_builder' ),
			),
		),

		// Save to Library.
		'cannot_save_app_layout_has_ab_testing'         => array(
			'title' => esc_html__( 'Can\'t Save Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_section_layout_has_ab_testing'     => array(
			'title' => esc_html__( 'Can\'t Save Section', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this section while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_row_layout_has_ab_testing'         => array(
			'title' => esc_html__( 'Can\'t Save Row', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this row while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_row_inner_layout_has_ab_testing'   => array(
			'title' => esc_html__( 'Can\'t Save Row', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this row while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		'cannot_save_module_layout_has_ab_testing'      => array(
			'title' => esc_html__( 'Can\'t Save Module', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot save this module while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		// Load / Clear Layout.
		'cannot_load_layout_has_ab_testing'             => array(
			'title' => esc_html__( 'Can\'t Load Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot load a new layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),
		'cannot_clear_layout_has_ab_testing'            => array(
			'title' => esc_html__( 'Can\'t Clear Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot clear your layout while a split testing is running. Please end your split test before clearing your layout.', 'et_builder' ),
		),

		// Cannot Import / Export Layout (Portability).
		'cannot_import_export_layout_has_ab_testing'    => array(
			'title' => esc_html__( 'Can\'t Import/Export Layout', 'et_builder' ),
			'desc'  => esc_html__( 'You cannot import or export a layout while a split test is running. Please end your split test and then try again.', 'et_builder' ),
		),

		// Moving Goal / Subject.
		'cannot_move_module_goal_out_from_subject'      => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
		),
		'cannot_move_row_goal_out_from_subject'         => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
		),
		'cannot_move_goal_into_subject'                 => array(
			'title' => esc_html__( 'Can\'t Move Goal', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing goal cannot be moved inside of a split testing subject. To perform this action you must first end your split test.', 'et_builder' ),
		),
		'cannot_move_subject_into_goal'                 => array(
			'title' => esc_html__( 'Can\'t Move Subject', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing subject cannot be moved inside of a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
		),

		// Cannot Paste Goal / Subject.
		'cannot_paste_goal'                             => array(
			'title' => esc_html__( 'Can\'t Paste Goal', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing goal cannot be copied, cut, and pasted. To perform this action you must first end your split test.', 'et_builder' ),
		),
		'cannot_paste_row_has_subject_into_goal'        => array(
			'title' => esc_html__( 'Can\'t Paste Row', 'et_builder' ),
			'desc'  => esc_html__( 'Row that has split testing subject cannot be pasted inside a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
		),
		'cannot_paste_subject_into_goal'                => array(
			'title' => esc_html__( 'Can\'t Paste Subject', 'et_builder' ),
			'desc'  => esc_html__( 'A split testing subject cannot be pasted inside a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
		),

		// Removing + Has Goal.
		'cannot_remove_section_has_goal'                => array(
			'title' => esc_html__( 'Can\'t Remove Section', 'et_builder' ),
			'desc'  => esc_html__( 'This section cannot be removed because it contains a split testing goal. Goals cannot be deleted. You must first end your split test before performing this action.', 'et_builder' ),
		),
		'cannot_remove_row_has_goal'                    => array(
			'title' => esc_html__( 'Can\'t Remove Row', 'et_builder' ),
			'desc'  => esc_html__( 'This row cannot be removed because it contains a split testing goal. Goals cannot be deleted. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// Removing + Has Unremovable Subjects.
		'cannot_remove_section_has_unremovable_subject' => array(
			'title' => esc_html__( 'Can\'t Remove Section', 'et_builder' ),
			'desc'  => esc_html__( 'Split testing requires at least 2 subject variations. This variation cannot be removed until additional variations have been added.', 'et_builder' ),
		),
		'cannot_remove_row_has_unremovable_subject'     => array(
			'title' => esc_html__( 'Can\'t Remove Row', 'et_builder' ),
			'desc'  => esc_html__( 'Split testing requires at least 2 subject variations. This variation cannot be removed until additional variations have been added', 'et_builder' ),
		),

		// Cloning + Has Goal.
		'cannot_clone_section_has_goal'                 => array(
			'title' => esc_html__( 'Can\'t Clone Section', 'et_builder' ),
			'desc'  => esc_html__( 'This section cannot be duplicated because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),
		'cannot_clone_row_has_goal'                     => array(
			'title' => esc_html__( 'Can\'t Clone Row', 'et_builder' ),
			'desc'  => esc_html__( 'This row cannot be duplicated because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// Copy + Has Goal.
		'cannot_copy_section_has_goal'                  => array(
			'title' => esc_html__( 'Can\'t Copy Section', 'et_builder' ),
			'desc'  => esc_html__( 'This section cannot be copied because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),
		'cannot_copy_row_has_goal'                      => array(
			'title' => esc_html__( 'Can\'t Copy Row', 'et_builder' ),
			'desc'  => esc_html__( 'This row cannot be copied because it contains a split testing goal. Goals cannot be duplicated. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// Copy Goal.
		'cannot_copy_goal'                              => array(
			'title' => esc_html__( 'Can\'t Copy Goal', 'et_builder' ),
			'desc'  => esc_html__( 'Goal cannot be copied. You must first end your split test before performing this action.', 'et_builder' ),
		),

		// No AB Testing Permission.
		'has_no_ab_permission'                          => array(
			'title' => esc_html__( 'Can\'t Edit Split Test', 'default' ),
			'desc'  => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
		),

		// No AB Testing Report Yet.
		'no_report'                                     => array(
			'title' => esc_html__( 'Statistics are being collected', 'et_builder' ),
			'desc'  => esc_html__( 'Stats will be displayed upon sufficient data collection', 'et_builder' ), // 10
		),

		// Set Global Winner Status.
		'set_global_winner_status'                      => array(
			'title'    => esc_html__( 'Set Winner Status', 'et_builder' ),
			'desc'     => esc_html__( 'You were using global item as split testing winner. Consequently, you have to choose between:', 'et_builder' ),
			'option_1' => esc_html__( 'Save winner as global item (selected subject will be synced and your global item will be updated in the Divi Library)', 'et_builder' ),
			'option_2' => esc_html__( 'Save winner as non-global item (selected subject will no longer be a global item and your changes will not modify the global item)', 'et_builder' ),
			'cancel'   => esc_html__( 'Save as Global Item', 'et_builder' ),
			'proceed'  => esc_html__( 'Save', 'et_builder' ),
		),

	);
	return apply_filters( 'et_builder_ab_settings', $ab_settings );
}

/**
 * AJAX endpoint for builder data
 *
 * @return void
 */
function et_pb_ab_builder_data() {
	// Verify nonce.
	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- `wp_verify_nonce()` does not store or display the nonce value, therefor XSS safe.
	if ( ! isset( $_POST['et_pb_ab_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	$defaults = array(
		'et_pb_ab_test_id'  => '',
		'et_pb_ab_duration' => 'week',
	);

	$_post = wp_parse_args( $_POST, $defaults );

	$_post['et_pb_ab_test_id'] = ! empty( $_post['et_pb_ab_test_id'] ) ? intval( $_post['et_pb_ab_test_id'] ) : '';

	// Verify user permission.
	if ( empty( $_post['et_pb_ab_test_id'] ) || ! current_user_can( 'edit_post', $_post['et_pb_ab_test_id'] ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	// Allowlist the duration value.
	$duration = in_array( $_post['et_pb_ab_duration'], et_pb_ab_get_stats_data_duration(), true ) ? $_post['et_pb_ab_duration'] : $defaults['et_pb_ab_duration'];

	// Get data.
	$output = et_pb_ab_get_stats_data( intval( $_post['et_pb_ab_test_id'] ), $duration );

	// Print output.
	die( et_core_esc_previously( wp_json_encode( $output ) ) );
}
add_action( 'wp_ajax_et_pb_ab_builder_data', 'et_pb_ab_builder_data' );

/**
 * Get AB Testing subject ranking data
 *
 * @param integer $post_id Post id.
 *
 * @return array
 */
function et_pb_ab_get_saved_subjects_ranks( $post_id ) {
	global $post;

	// Make sure that there are $post_id.
	if ( ! isset( $post_id ) && isset( $post->ID ) ) {
		$post_id = $post->ID;
	}

	// Get list of subjects.
	$subject_list = get_post_meta( $post_id, '_et_pb_ab_subjects', true );
	$subjects_ids = explode( ',', $subject_list );
	$subjects     = array();
	$goal_slug    = et_pb_ab_get_goal_module( $post_id );
	$rank_metrics = in_array( $goal_slug, et_pb_ab_get_modules_have_conversions(), true ) ? 'conversions' : 'clicks';

	if ( ! empty( $subjects_ids ) ) {
		// Get conversion rate data.
		$subjects_ranks = et_pb_ab_get_subjects_ranks( $post_id, $rank_metrics, 'all' );

		// Sort from high to low and mantain key association.
		arsort( $subjects_ranks );

		// Loop saved subject ids.
		foreach ( $subjects_ids as $subject_id ) {
			$subject_key  = 'subject_' . $subject_id;
			$subject_rank = isset( $subjects_ranks[ $subject_key ] ) ? array_search( $subjects_ranks[ $subject_key ], array_values( $subjects_ranks ), true ) + 1 : false;

			// Check whether current subject has saved conversion rate data or not.
			if ( $subject_rank ) {
				$subjects[ $subject_key ] = array(
					'percentage' => esc_html( $subjects_ranks[ $subject_key ] . '%' ),
					'rank'       => esc_attr( $subject_rank ),
				);
			}
		}
	}

	return $subjects;
}

/**
 * Define ranking-based subject color
 *
 * @return array
 */
function et_pb_ab_get_subject_rank_colors() {
	$subject_rank_colors = array(
		'#F3CB57',
		'#F8B852',
		'#F8A653',
		'#F88F55',
		'#F87356',
		'#F95A57',
		'#EA5552',
		'#DB514F',
		'#CE4441',
		'#BF2F2C',
		'#AA201C',
		'#920E08',
		'#7E0000',
	);
	return array_map( 'et_sanitize_alpha_color', apply_filters( 'et_pb_ab_get_subject_rank_colors', $subject_rank_colors ) );
}

/**
 * Print AB Testing subject-ranking color scheme
 *
 * @return string inline CSS styling for subject rank
 */
function et_pb_ab_get_subject_rank_colors_style() {
	$style  = '';
	$colors = et_pb_ab_get_subject_rank_colors();
	$index  = 1;

	foreach ( $colors as $color ) {
		$style .= sprintf(
			'.et_pb_ab_subject.rank-%1$s .et_pb_module_block,
			.et_pb_ab_subject.rank-%1$s.et_pb_section .et-pb-controls,
			.et_pb_ab_subject.rank-%1$s.et_pb_row .et-pb-controls,
			.et_pb_ab_subject.rank-%1$s.et_pb_module_block {
				background: %2$s;
			}',
			esc_html( $index ),
			esc_html( $color )
		);
		$index++;
	}

	return $style;
}

/**
 * Get subjects' ranks
 *
 * @param int    $post_id post ID.
 * @param string $ranking_basis ranking basis. This can be any value on data's subjects_totals
 *               view_page|read_page|view_goal|read_goal|click_goal|con_goal|clicks|reads|bounces|engagements|conversions.
 * @param string $duration duration of the data that is used.
 * @return array key = `subject_` + subject_id as key and the value as value, sorted in ascending
 */
function et_pb_ab_get_subjects_ranks( $post_id, $ranking_basis = 'engagements', $duration = 'week' ) {
	$data     = et_pb_ab_get_stats_data( $post_id, $duration );
	$subjects = et_pb_ab_get_subjects( $post_id, 'array', 'subject_' );

	if ( isset( $data['subjects_totals'] ) && ! empty( $data['subjects_totals'] ) && ! empty( $subjects ) ) {
		// Pluck data.
		$ranks = wp_list_pluck( $data['subjects_totals'], $ranking_basis );

		// Remove inactive subjects from ranks.
		foreach ( $ranks as $rank_key => $rank_value ) {
			if ( ! in_array( $rank_key, $subjects, true ) ) {
				unset( $ranks[ $rank_key ] );
			}
		}

		// Sort rank.
		arsort( $ranks );
	} else {
		$ranks = array();
	}

	return $ranks;
}

/**
 * Get formatted stats data that is used by builder's AB Testing stats
 *
 * @param int    $post_id post ID.
 * @param string $duration day|week|month|all duration of stats.
 * @param bool   $time has to be in Y-m-d H:i:s format.
 * @param bool   $force_update Whether to force stats update.
 * @param bool   $is_cron_task Is it a cron task.
 *
 * @return array stats data
 */
function et_pb_ab_get_stats_data( $post_id, $duration = 'week', $time = false, $force_update = false, $is_cron_task = false ) {
	global $wpdb;

	$post_id      = intval( $post_id );
	$goal_slug    = et_pb_ab_get_goal_module( $post_id );
	$rank_metrics = in_array( $goal_slug, et_pb_ab_get_modules_have_conversions(), true ) ? 'conversions' : 'clicks';

	// Get subjects.
	$subjects    = et_pb_ab_get_subjects( $post_id, 'array', 'subject_', $is_cron_task );
	$subjects_id = et_pb_ab_get_subjects( $post_id, 'array', false, $is_cron_task );

	// Get cached data.
	$cached_data = get_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration );

	// Get rank coloring scheme.
	$subject_rank_colors = et_pb_ab_get_subject_rank_colors();

	// return cached logs if exist and if force_update == false.
	if ( $cached_data && ! $force_update ) {
		// Remove inactive subjects.
		if ( isset( $cached_data['subjects_id'] ) && ! empty( $cached_data['subjects_id'] ) ) {
			foreach ( $cached_data['subjects_id'] as $subject_id_key => $subject_id_value ) {
				if ( ! in_array( $subject_id_value, $subjects_id, true ) ) {
					unset( $cached_data['subjects_id'][ $subject_id_key ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_logs'] ) && ! empty( $cached_data['subjects_logs'] ) ) {
			foreach ( $cached_data['subjects_logs'] as $subject_log_id => $subject_logs ) {
				if ( ! in_array( $subject_log_id, $subjects, true ) ) {
					unset( $cached_data['subjects_logs'][ $subject_log_id ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_analysis'] ) && ! empty( $cached_data['subjects_analysis'] ) ) {
			foreach ( $cached_data['subjects_analysis'] as $subject_analysis_id => $subject_analysis ) {
				if ( ! in_array( $subject_analysis_id, $subjects, true ) ) {
					unset( $cached_data['subjects_analysis'][ $subject_analysis_id ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_totals'] ) && ! empty( $cached_data['subjects_totals'] ) ) {
			$subject_totals_index = 0;
			foreach ( $cached_data['subjects_totals'] as $subject_total_id => $subject_totals ) {
				if ( ! in_array( $subject_total_id, $subjects, true ) ) {
					unset( $cached_data['subjects_totals'][ $subject_total_id ] );
					continue;
				}
			}

			// Rank by engagement.
			$cached_subjects_ranks       = wp_list_pluck( $cached_data['subjects_totals'], $rank_metrics );
			$cached_subjects_ranks_index = 0;

			// Sort from high to low, mantain keys.
			arsort( $cached_subjects_ranks );

			// Push color data.
			foreach ( $cached_subjects_ranks as $subject_rank_id => $subject_rank_value ) {
				$is_empty_rank_value    = 0 === $subject_rank_value;
				$has_subject_rank_color = isset( $subject_rank_colors[ $cached_subjects_ranks_index ] );

				// If the rank value (derived from engagement) is empty, display default subject color.
				if ( $is_empty_rank_value ) {
					$cached_data['subjects_totals'][ $subject_rank_id ]['color'] = '#F3CB57';
				} else {
					$cached_data['subjects_totals'][ $subject_rank_id ]['color'] = $has_subject_rank_color ? $subject_rank_colors[ $cached_subjects_ranks_index ] : '#7E0000';
				}

				$cached_subjects_ranks_index++;
			}
		}

		return $cached_data;
	}

	$wpdb->et_divi_ab_testing_stats = $wpdb->prefix . 'et_divi_ab_testing_stats';

	// do nothing if no stats table exists in current WP.
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->et_divi_ab_testing_stats'" ) ) {
		return false;
	}

	// Main placeholder.
	$event_types       = et_pb_ab_get_event_types();
	$analysis_types    = et_pb_ab_get_analysis_types();
	$analysis_formulas = et_pb_ab_get_analysis_formulas();
	$time              = $time ? $time : gmdate( 'Y-m-d H:i:s' );
	$stats             = array(
		'subjects_id'       => $subjects_id,
		'subjects_logs'     => array(),
		'subjects_analysis' => array(),
		'subjects_totals'   => array(),
		'events_totals'     => array(),
		'dates'             => array(),
	);

	// Get all logs in test.
	switch ( $duration ) {
		case 'all':
			$date_range_interval = 'week';
			$query               = $wpdb->prepare(
				"SELECT subject_id, event, YEARWEEK(record_date) AS 'date', COUNT(id) AS 'count' FROM `{$wpdb->et_divi_ab_testing_stats}` WHERE test_id = %d GROUP BY subject_id, YEARWEEK(record_date), event",
				$post_id
			);
			break;

		case 'month':
			$date_range_interval = 'day';
			$query               = $wpdb->prepare(
				"SELECT subject_id, event, DATE(record_date) AS 'date', COUNT(id) AS 'count' FROM `{$wpdb->et_divi_ab_testing_stats}` WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 MONTH ) GROUP BY subject_id, DAYOFMONTH(record_date), event",
				$post_id,
				$time,
				$time
			);
			break;

		case 'day':
			$date_range_interval = 'hour';
			$query               = $wpdb->prepare(
				"SELECT subject_id, event, DATE_FORMAT(record_date, %s) AS 'date', COUNT(id) AS 'count' FROM `{$wpdb->et_divi_ab_testing_stats}` WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 DAY ) GROUP BY subject_id, HOUR(record_date), event",
				'%Y-%m-%d %H:00',
				$post_id,
				$time,
				$time
			);
			break;

		default:
			$date_range_interval = 'day';
			$query               = $wpdb->prepare(
				"SELECT subject_id, event, DATE(record_date) AS 'date', COUNT(id) AS 'count' FROM `{$wpdb->et_divi_ab_testing_stats}` WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 WEEK ) GROUP BY subject_id, DAYOFMONTH(record_date), event",
				$post_id,
				$time,
				$time
			);
			break;
	}

	$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- value of $query was prepared in above switch statement.

	unset( $wpdb->et_divi_ab_testing_stats );

	if ( ! empty( $results ) ) {
		// Get min and max timestamp based on query result.
		$min_max_date = et_pb_ab_get_min_max_timestamp( $results, $date_range_interval );

		// Create default list.
		$date_list = et_pb_ab_get_date_range( $min_max_date['min'], $min_max_date['max'], $date_range_interval );

		// Insert date list to main placeholder.
		$stats['dates'] = $date_list;

		// Format YYYYWW format on all-time stats into human-readable format (M jS).
		foreach ( $stats['dates'] as $date_key => $date_time ) {
			if ( 'all' === $duration ) {
				// Format weekly label.
				$week_in_seconds = 60 * 60 * 24 * 7;
				$current_time    = time();
				$week_start_time = strtotime( substr( $date_time, 0, 4 ) . 'W' . substr( $date_time, 4, 2 ) );
				$week_end_time   = $week_start_time + $week_in_seconds;

				// Don't let the end time pass current time.
				if ( $week_end_time > $current_time ) {
					$week_end_time = $current_time;
				}

				// Simplify the label by removing the end month when the start and end month are identical.
				if ( gmdate( 'M', $week_start_time ) === gmdate( 'M', $week_end_time ) ) {
					$stats['dates'][ $date_key ] = gmdate( 'M jS', $week_start_time ) . ' - ' . gmdate( 'jS', $week_end_time );
				} else {
					$stats['dates'][ $date_key ] = gmdate( 'M jS', $week_start_time ) . ' - ' . gmdate( 'M jS', $week_end_time );
				}
			} elseif ( 'day' === $duration ) {
				$stats['dates'][ $date_key ] = gmdate( 'H:i', strtotime( $date_time ) );
			} else {
				$stats['dates'][ $date_key ] = gmdate( 'M jS', strtotime( $date_time ) );
			}
		}

		// Fill subject logs placeholder with proper default.
		$stats['subjects_logs'] = array_fill_keys(
			$subjects,
			array_fill_keys(
				$event_types,
				array_fill_keys(
					$date_list,
					0
				)
			)
		);

		// Loop query result and place into placeholder.
		foreach ( $results as $log ) {
			if ( ! in_array( $log->subject_id, $subjects_id, true ) ) {
				continue;
			}

			$log_date = $log->date;

			// Format year-week to ensure the given date is equal to the expected format. MySQl YEARWEEK() seems to output
			// the date in ISO-8601 format (first week of the year becomes 201753 instead of the expected 201801).
			if ( 'all' === $duration ) {
				$log_date = gmdate( 'YW', strtotime( substr( $log_date, 0, 4 ) . 'W' . substr( $log_date, 4, 2 ) ) );
			}

			$stats['subjects_logs'][ "subject_{$log->subject_id}" ][ $log->event ][ $log_date ] = $log->count;
		}

		// Determine logs' totals and run analysis.
		foreach ( $stats['subjects_logs'] as $subject_log_id => $subject_log ) {

			// Push stats total data.
			foreach ( $subject_log as $log_type => $logs ) {
				$stats['subjects_totals'][ $subject_log_id ][ $log_type ] = array_sum( $logs );
			}

			// Run analysis for stats' total data.
			foreach ( $analysis_types as $analysis_type ) {
				$numerator_event   = $analysis_formulas[ $analysis_type ]['numerator'];
				$denominator_event = $analysis_formulas[ $analysis_type ]['denominator'];
				$numerator         = isset( $stats['subjects_totals'][ $subject_log_id ][ $numerator_event ] ) ? $stats['subjects_totals'][ $subject_log_id ][ $numerator_event ] : 0;
				$denominator       = isset( $stats['subjects_totals'][ $subject_log_id ][ $denominator_event ] ) ? $stats['subjects_totals'][ $subject_log_id ][ $denominator_event ] : 0;
				$analysis          = 0 === $denominator ? 0 : floatval( number_format( ( $numerator / $denominator ) * 100, 2 ) );

				if ( $analysis_formulas[ $analysis_type ]['inverse'] && 0 !== $numerator && 0 !== $denominator_event ) {
					$analysis = 100 - $analysis;
				}

				$stats['subjects_totals'][ $subject_log_id ][ $analysis_type ] = $analysis;
			}

			// Run analysis for each log date.
			foreach ( $date_list as $log_date ) {

				// Run analysis per analysis type.
				foreach ( $analysis_types as $analysis_type ) {
					$numerator_event   = $analysis_formulas[ $analysis_type ]['numerator'];
					$denominator_event = $analysis_formulas[ $analysis_type ]['denominator'];
					$numerator         = isset( $stats['subjects_logs'][ $subject_log_id ][ $numerator_event ][ $log_date ] ) ? intval( $stats['subjects_logs'][ $subject_log_id ][ $numerator_event ][ $log_date ] ) : 0;
					$denominator       = isset( $stats['subjects_logs'][ $subject_log_id ][ $denominator_event ][ $log_date ] ) ? intval( $stats['subjects_logs'][ $subject_log_id ][ $denominator_event ][ $log_date ] ) : 0;
					$analysis          = 0 === $denominator ? 0 : floatval( number_format( ( $numerator / $denominator ) * 100, 2 ) );

					if ( $analysis_formulas[ $analysis_type ]['inverse'] ) {
						$analysis = 100 - $analysis;
					}

					$stats['subjects_analysis'][ $subject_log_id ][ $analysis_type ][ $log_date ] = $analysis;
				}
			}
		}

		// Push total events data.
		foreach ( $event_types as $event_type ) {
			$stats['events_totals'][ $event_type ] = array_sum( wp_list_pluck( $stats['subjects_totals'], $event_type ) );
		}

		foreach ( $analysis_types as $analysis_type ) {
			$analysis_data                            = wp_list_pluck( $stats['subjects_totals'], $analysis_type );
			$analysis_count                           = count( $analysis_data );
			$stats['events_totals'][ $analysis_type ] = floatval( number_format( array_sum( $analysis_data ) / $analysis_count, 2 ) );
		}

		// Rank by engagement.
		$subjects_ranks       = wp_list_pluck( $stats['subjects_totals'], $rank_metrics );
		$subjects_ranks_index = 0;

		// Sort from high to low, mantain keys.
		arsort( $subjects_ranks );

		// Push color data.
		foreach ( $subjects_ranks as $subject_rank_id => $subject_rank_value ) {
			$is_empty_rank_value    = 0 === $subject_rank_value;
			$has_subject_rank_color = isset( $subject_rank_colors[ $subjects_ranks_index ] );

			// If the rank value (derived from engagement) is empty, display default subject color.
			if ( $is_empty_rank_value ) {
				$stats['subjects_totals'][ $subject_rank_id ]['color'] = '#F3CB57';
			} else {
				$stats['subjects_totals'][ $subject_rank_id ]['color'] = $has_subject_rank_color ? $subject_rank_colors[ $subjects_ranks_index ] : '#7E0000';
			}

			$subjects_ranks_index++;
		}

		// update cache.
		set_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration, $stats, DAY_IN_SECONDS );
	} else {
		// remove the cache if no logs found.
		delete_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration );
		return false;
	}

	return $stats;
}

/**
 * Outputs get data stats duration
 *
 * @return array of data
 */
function et_pb_ab_get_stats_data_duration() {
	$stats_data_duration = array(
		'day',
		'week',
		'month',
		'all',
	);
	return apply_filters( 'et_pb_ab_get_stats_data_duration', $stats_data_duration );
}

/**
 * Get list of AB Testing event type
 *
 * @return array of event types
 */
function et_pb_ab_get_event_types() {
	$event_types = array(
		'view_page',
		'read_page',
		'view_goal',
		'read_goal',
		'click_goal',
		'con_goal',
		'con_short',
	);
	return apply_filters( 'et_pb_ab_get_event_types', $event_types );
}

/**
 * Get min and max timestamp from returned MySQL query
 *
 * @param array  $query_result MySQL returned value. Expected to be array( array ( 'date' => 'YYYY-MM-DD' ) ) format.
 * @param string $interval day|week.
 * @return array using min and max key
 */
function et_pb_ab_get_min_max_timestamp( $query_result, $interval = 'day' ) {
	$output = array(
		'min' => false,
		'max' => false,
	);

	// Get all available dates from logs.
	$dates = array_unique( wp_list_pluck( $query_result, 'date' ) );

	// Sort low-to-high and reset array keys.
	sort( $dates );

	// Get min and max dates from logs.
	$min_date = $dates[0];
	$max_date = $dates[ ( count( $dates ) - 1 ) ];

	switch ( $interval ) {
		case 'week':
			$output['min'] = strtotime( substr( $min_date, 0, 4 ) . 'W' . substr( $min_date, 4, 2 ) );
			$output['max'] = strtotime( substr( $max_date, 0, 4 ) . 'W' . substr( $max_date, 4, 2 ) );
			break;

		default:
			$output['min'] = strtotime( $min_date );
			$output['max'] = strtotime( $max_date );
			break;
	}

	return $output;
}

/**
 * Get all days between min and max dates from logs
 *
 * @param int    $min_date_timestamp start date timestamp.
 * @param int    $max_date_timestamp end date timestamp.
 * @param string $interval day|week interval of rage.
 * @return array of dates
 */
function et_pb_ab_get_date_range( $min_date_timestamp, $max_date_timestamp, $interval = 'day' ) {
	$day_timestamp = $min_date_timestamp;
	$full_dates    = array();

	switch ( $interval ) {
		case 'week':
			$date_format   = 'YW';
			$time_interval = '+1 week';
			break;

		case 'hour':
			$date_format   = 'Y-m-d H:i';
			$time_interval = '+1 hour';
			break;

		default:
			$date_format   = 'Y-m-d';
			$time_interval = '+1 day';
			break;
	}

	while ( $day_timestamp <= $max_date_timestamp ) {
		$full_dates[]  = gmdate( $date_format, $day_timestamp );
		$day_timestamp = strtotime( $time_interval, $day_timestamp );
	}

	return $full_dates;
}

/**
 * Get list of Split analysis types
 *
 * @return array analysis types
 */
function et_pb_ab_get_analysis_types() {
	$analysis_types = array(
		'clicks',
		'reads',
		'bounces',
		'engagements',
		'conversions',
		'shortcode_conversions',
	);
	return apply_filters( 'et_pb_ab_get_analysis_types', $analysis_types );
}

/**
 * Get numerator and denominator of various stats types
 *
 * @return array stats' data type formula
 */
function et_pb_ab_get_analysis_formulas() {
	$analysis_formulas = array(
		'clicks'                => array(
			'numerator'   => 'click_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'reads'                 => array(
			'numerator'   => 'read_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'bounces'               => array(
			'numerator'   => 'read_page',
			'denominator' => 'view_page',
			'inverse'     => true,
		),
		'engagements'           => array(
			'numerator'   => 'read_goal',
			'denominator' => 'view_goal',
			'inverse'     => false,
		),
		'conversions'           => array(
			'numerator'   => 'con_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'shortcode_conversions' => array(
			'numerator'   => 'con_short',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
	);
	return apply_filters( 'et_pb_ab_get_analysis_formulas', $analysis_formulas );
}

/**
 * List modules' slug which has conversions support
 *
 * @return array slugs of modules which have conversions support
 */
function et_pb_ab_get_modules_have_conversions() {
	$modules_have_conversions = array(
		'et_pb_shop',
		'et_pb_contact_form',
		'et_pb_signup',
		'et_pb_comments',
	);
	return apply_filters( 'et_pb_ab_get_modules_have_conversions', $modules_have_conversions );
}

/**
 * Check whether AB Testing active on current page
 *
 * @since 4.0 Added the $post_id parameter.
 *
 * @param integer $post_id post ID.
 *
 * @return bool
 */
function et_is_ab_testing_active( $post_id = 0 ) {
	$post_id = $post_id > 0 ? $post_id : get_the_ID();
	$post_id = apply_filters( 'et_is_ab_testing_active_post_id', $post_id );

	$ab_testing_status = 'on' === get_post_meta( $post_id, '_et_pb_use_ab_testing', true );

	$fb_enabled = function_exists( 'et_core_is_fb_enabled' ) ? et_core_is_fb_enabled() : false;

	if ( ! $ab_testing_status && $fb_enabled && 'publish' !== get_post_status() ) {
		$ab_testing_status = 'on' === get_post_meta( $post_id, '_et_pb_use_ab_testing_draft', true );
	}

	return $ab_testing_status;
}

/**
 * Check whether AB Testing has report
 *
 * @param integer $post_id post ID.
 *
 * @return bool
 */
function et_pb_ab_has_report( $post_id ) {
	global $wpdb;

	if ( ! et_is_ab_testing_active() ) {
		return false;
	}

	$wpdb->et_divi_ab_testing_stats = $wpdb->prefix . 'et_divi_ab_testing_stats';

	$result = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM `{$wpdb->et_divi_ab_testing_stats}` WHERE test_id = %d",
			$post_id
		)
	) ? true : false;

	unset( $wpdb->et_divi_ab_testing_stats );

	return apply_filters( 'et_pb_ab_has_report', $result, $post_id );
}

/**
 * Check the status of the ab db version
 *
 * @return bool
 */
function et_pb_db_status_up_to_date() {
	$ab_db_settings = get_option( 'et_pb_ab_test_settings' );
	return ( $ab_db_settings ) && version_compare( $ab_db_settings['db_version'], ET_PB_AB_DB_VERSION, '>=' );
}

/**
 * Create AB Testing table needed for AB Testing feature
 *
 * @return void
 */
function et_pb_create_ab_tables() {
	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- `wp_verify_nonce()` does not store or display the nonce value, therefor XSS safe.
	if ( isset( $_POST['et_pb_ab_nonce'] ) && ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	// Verify user permission.
	if ( ! current_user_can( 'edit_posts' ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	// Verify update is needed.
	if ( et_pb_db_status_up_to_date() ) {
		die( -1 );
	}

	global $wpdb;

	$stats_table_name                 = $wpdb->prefix . 'et_divi_ab_testing_stats';
	$wpdb->et_divi_ab_testing_stats   = $stats_table_name;
	$client_subject_table_name        = $wpdb->prefix . 'et_divi_ab_testing_clients';
	$wpdb->et_divi_ab_testing_clients = $client_subject_table_name;

	/*
	 * We'll set the default character set and collation for this table.
	 * If we don't do this, some characters could end up being converted
	 * to just ?'s when saved in our table.
	 */
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = sprintf(
			'DEFAULT CHARACTER SET %1$s',
			sanitize_text_field( $wpdb->charset )
		);
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= sprintf(
			' COLLATE %1$s',
			sanitize_text_field( $wpdb->collate )
		);
	}

	$ab_tables_queries = array();

	// Remove client_id column from stats table.
	if ( 0 < $wpdb->query( "SHOW COLUMNS FROM `$wpdb->et_divi_ab_testing_stats` LIKE 'client_id'" ) ) {
		$wpdb->query( "ALTER TABLE `$wpdb->et_divi_ab_testing_stats` DROP COLUMN client_id" );
	}

	// Remove client subject table.
	if ( 0 < $wpdb->query( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->et_divi_ab_testing_clients ) ) ) {
		$wpdb->query( "DROP TABLE $wpdb->et_divi_ab_testing_clients" );
	}

	$ab_tables_queries[] = "CREATE TABLE $wpdb->et_divi_ab_testing_stats (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		test_id varchar(20) NOT NULL,
		subject_id varchar(20) NOT NULL,
		record_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		event varchar(10) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $ab_tables_queries );

	$db_settings = array(
		'db_version' => ET_PB_AB_DB_VERSION,
	);

	update_option( 'et_pb_ab_test_settings', $db_settings );

	// Register AB Testing cron.
	et_pb_create_ab_cron();

	unset( $wpdb->et_divi_ab_testing_stats );
	unset( $wpdb->et_divi_ab_testing_clients );

	die( 'success' );
}
add_action( 'wp_ajax_et_pb_create_ab_tables', 'et_pb_create_ab_tables' );

/**
 * Handle adding the AB testing log record via ajax
 *
 * @return void
 */
function et_pb_update_stats_table() {
	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- `wp_verify_nonce()` does not store or display the nonce value, therefor XSS safe.
	if ( ! isset( $_POST['et_ab_log_nonce'] ) || ! wp_verify_nonce( $_POST['et_ab_log_nonce'], 'et_ab_testing_log_nonce' ) ) {
		die( -1 );
	}

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- Stats data will be sanitized in the `et_pb_add_stats_record()`.
	$stats_data_json  = str_replace( '\\', '', $_POST['stats_data_array'] );
	$stats_data_array = json_decode( $stats_data_json, true );

	et_pb_add_stats_record( $stats_data_array );

	die( 1 );
}
add_action( 'wp_ajax_et_pb_update_stats_table', 'et_pb_update_stats_table' );
add_action( 'wp_ajax_nopriv_et_pb_update_stats_table', 'et_pb_update_stats_table' );

/**
 * List of valid AB Testing refresh interval duration
 *
 * @return array
 */
function et_pb_ab_refresh_interval_durations() {
	$refresh_interval_durations = array(
		'hourly' => 'day',
		'daily'  => 'week',
	);
	return apply_filters( 'et_pb_ab_refresh_interval_durations', $refresh_interval_durations );
}

/**
 * Get refresh interval of particular AB Testing
 *
 * @param int    $post_id post ID.
 * @param string $default default interval.
 * @return string interval used in particular AB Testing
 */
function et_pb_ab_get_refresh_interval( $post_id, $default = 'hourly' ) {
	$interval = get_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval', true );

	if ( in_array( $interval, array_keys( et_pb_ab_refresh_interval_durations() ), true ) ) {
		return apply_filters( 'et_pb_ab_get_refresh_interval', $interval, $post_id );
	}

	return apply_filters( 'et_pb_ab_default_refresh_interval', $default, $post_id );
}

/**
 * Get refresh interval duration of particular AB Testing
 *
 * @param int    $post_id post ID.
 * @param string $default default interval duration.
 * @return string test's interval duration
 */
function et_pb_ab_get_refresh_interval_duration( $post_id, $default = 'day' ) {
	$durations = et_pb_ab_refresh_interval_durations();

	$interval = et_pb_ab_get_refresh_interval( $post_id );

	$interval_duration = isset( $durations[ $interval ] ) ? $durations[ $interval ] : $default;

	return apply_filters( 'et_pb_ab_get_refresh_interval_duration', $interval_duration, $post_id );
}

/**
 * Get goal module slug of particular AB Testing
 *
 * @param int $post_id post ID.
 * @return string test's goal module slug
 */
function et_pb_ab_get_goal_module( $post_id ) {
	return get_post_meta( $post_id, '_et_pb_ab_goal_module', true );
}

/**
 * Register Divi's AB Testing cron
 * There are 2 options - daily and hourly, so schedule 2 events
 *
 * @return void
 */
function et_pb_create_ab_cron() {
	// schedule daily event.
	if ( ! wp_next_scheduled( 'et_pb_ab_cron', array( 'daily' ) ) ) {
		wp_schedule_event( time(), 'daily', 'et_pb_ab_cron', array( 'daily' ) );
	}

	// schedule hourly event.
	if ( ! wp_next_scheduled( 'et_pb_ab_cron', array( 'hourly' ) ) ) {
		wp_schedule_event( time(), 'hourly', 'et_pb_ab_cron', array( 'hourly' ) );
	}
}

/**
 * Perform Divi's AB Testing cron
 *
 * @param string $args Interval.
 *
 * @return void
 */
function et_pb_ab_cron( $args ) {
	$all_tests = et_pb_ab_get_all_tests();
	$interval  = isset( $args ) ? $args : 'hourly';

	if ( empty( $all_tests ) ) {
		return;
	}

	// update cache for each test and for each duration.
	foreach ( $all_tests as $test ) {
		$current_test_interval = et_pb_ab_get_refresh_interval( $test['test_id'] );

		// determine whether or not we should update the stats for current test depending on interval parameter.
		if ( $current_test_interval !== $interval ) {
			continue;
		}

		foreach ( et_pb_ab_get_stats_data_duration() as $duration ) {
			et_pb_ab_get_stats_data( $test['test_id'], $duration, false, true, true );
		}
	}
}
add_action( 'et_pb_ab_cron', 'et_pb_ab_cron' );

/**
 * Refresh testings stats.
 *
 * @param int $test_id Post id.
 */
function et_pb_ab_clear_cache_handler( $test_id ) {
	if ( ! $test_id ) {
		return;
	}

	foreach ( et_pb_ab_get_stats_data_duration() as $duration ) {
		delete_transient( 'et_pb_ab_' . $test_id . '_stats_' . $duration );
	}
}

/**
 * Ajax Callback :: Refresh stats
 */
function et_pb_ab_clear_cache() {
	// Verify nonce.
	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- `wp_verify_nonce()` does not store or display the nonce value, therefor XSS safe.
	if ( ! isset( $_POST['et_pb_ab_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	$test_id = ! empty( $_POST['et_pb_test_id'] ) ? intval( $_POST['et_pb_test_id'] ) : '';

	// Verify user permission.
	if ( empty( $test_id ) || ! current_user_can( 'edit_post', $test_id ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	et_pb_ab_clear_cache_handler( $test_id );

	// VB ask to load data to save request.
	if ( isset( $_POST['et_pb_ab_load_data'] ) && isset( $_POST['et_pb_test_id'] ) && isset( $_POST['et_pb_ab_duration'] ) ) {
		// Allowlist the duration value.
		$duration = in_array( sanitize_text_field( $_POST['et_pb_ab_duration'] ), et_pb_ab_get_stats_data_duration(), true ) ? sanitize_text_field( $_POST['et_pb_ab_duration'] ) : 'day';

		// Get data.
		$output = et_pb_ab_get_stats_data( intval( $_POST['et_pb_test_id'] ), $duration );

		// Print output.
		die( wp_json_encode( $output ) );
	}

	die( 1 );
}

add_action( 'wp_ajax_et_pb_ab_clear_cache', 'et_pb_ab_clear_cache' );

/**
 * Get all the test ID from db.
 *
 * @return array|bool|object|null
 */
function et_pb_ab_get_all_tests() {
	global $wpdb;

	$wpdb->et_divi_ab_testing_stats = $wpdb->prefix . 'et_divi_ab_testing_stats';

	// do nothing if no stats table exists in current WP.
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->et_divi_ab_testing_stats'" ) ) {
		return false;
	}

	// construct sql query to get all the test ID from db.
	$sql = "SELECT DISTINCT test_id FROM `$wpdb->et_divi_ab_testing_stats`";

	// cache the data from conversions table.
	$all_tests = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL -- unprepared SQL okay, value of $sql was prepared above.

	unset( $wpdb->et_divi_ab_testing_stats );

	return $all_tests;
}

/**
 * Ajax callback :: Stats removal immediately after test stopped.
 */
function et_pb_ab_clear_stats() {
	// Verify nonce.
	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- wp_verify_nonce does not store or display the nonce value, therefor XSS safe.
	if ( ! isset( $_POST['et_pb_ab_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	$test_id = ! empty( $_POST['et_pb_test_id'] ) ? intval( $_POST['et_pb_test_id'] ) : '';

	// Verify user permission.
	if ( empty( $test_id ) || ! current_user_can( 'edit_post', $test_id ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	et_pb_ab_remove_stats( $test_id );

	et_pb_ab_clear_cache_handler( $test_id );

	die( 1 );
}
add_action( 'wp_ajax_et_pb_ab_clear_stats', 'et_pb_ab_clear_stats' );

/**
 * Remove AB Testing log and clear stats cache
 *
 * @param int $test_id Post ID.
 *
 * @return void|bool
 */
function et_pb_ab_remove_stats( $test_id ) {
	global $wpdb;

	$test_id = intval( $test_id );

	et_pb_ab_clear_cache_handler( $test_id );

	$sql_args = array(
		$test_id,
	);

	$wpdb->et_divi_ab_testing_stats = $wpdb->prefix . 'et_divi_ab_testing_stats';

	// do nothing if no stats table exists in current WP.
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->et_divi_ab_testing_stats'" ) ) {
		return false;
	}

	// construct sql query to remove value from DB table.
	$sql = "DELETE FROM `$wpdb->et_divi_ab_testing_stats` WHERE test_id = %d";

	$wpdb->query( $wpdb->prepare( $sql, $sql_args ) ); // phpcs:ignore WordPress.DB.PreparedSQL -- unprepared SQL okay, value of $sql was prepared above.

	unset( $wpdb->et_divi_ab_testing_stats );
}

/**
 * Shop trigger DOM
 *
 * @return void
 */
function et_pb_ab_shop_trigger() {
	echo '<div class="et_pb_ab_shop_conversion"></div>';
}
add_action( 'woocommerce_thankyou', 'et_pb_ab_shop_trigger' );

/**
 * Tracking shortcode
 *
 * @param array $atts User defined attributes in shortcode tag.
 *
 * @return string
 */
function et_pb_split_track( $atts ) {
	$settings = shortcode_atts(
		array(
			'id' => '',
		),
		$atts
	);

	$output = sprintf(
		'<div class="et_pb_ab_split_track" style="display:none;" data-test_id="%1$s"></div>',
		esc_attr( $settings['id'] )
	);

	return $output;
}
add_shortcode( 'et_pb_split_track', 'et_pb_split_track' );

/**
 * Get all posts loaded for the current request that have AB testing enabled.
 * This includes TB layouts and the current post, if any.
 *
 * @since 4.0
 *
 * @return integer[]
 */
function et_builder_ab_get_current_tests() {
	$layouts = et_theme_builder_get_template_layouts();
	$posts   = array();
	$tests   = array();

	foreach ( $layouts as $layout ) {
		if ( is_array( $layout ) && $layout['override'] ) {
			$posts[] = $layout['id'];
		}
	}

	if ( is_singular() ) {
		$posts[] = get_the_ID();
	}

	foreach ( $posts as $post_id ) {
		if ( et_pb_is_pagebuilder_used( $post_id ) && et_is_ab_testing_active( $post_id ) ) {
			$tests[] = array(
				'post_id' => $post_id,
				'test_id' => get_post_meta( $post_id, '_et_pb_ab_testing_id', true ),
			);
		}
	}

	return $tests;
}

/**
 * Initialize AB Testing. Check whether the user has visited the page or not by checking its cookie
 *
 * @since
 *
 * @return void
 */
function et_pb_ab_init() {
	$tests = et_builder_ab_get_current_tests();

	foreach ( $tests as $test ) {
		et_builder_ab_initialize_for_post( $test['post_id'] );
	}
}
add_action( 'wp', 'et_pb_ab_init' );

/**
 * Initialize AB testing for the specified post.
 *
 * @since 4.0
 *
 * @param integer $post_id Post id.
 *
 * @return void
 */
function et_builder_ab_initialize_for_post( $post_id ) {
	global $et_pb_ab_subject;

	if ( ! is_array( $et_pb_ab_subject ) ) {
		$et_pb_ab_subject = array();
	}

	$ab_subjects       = et_pb_ab_get_subjects( $post_id );
	$ab_hash_key       = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'default-divi-hash-key';
	$hashed_subject_id = et_pb_ab_get_visitor_cookie( $post_id, 'view_page' );

	if ( $hashed_subject_id ) {
		// Compare subjects against hashed subject id found on cookie to verify whether cookie value is valid or not.
		foreach ( $ab_subjects as $ab_subject ) {
			// Valid subject_id is found.
			if ( hash_hmac( 'md5', $ab_subject, $ab_hash_key ) === $hashed_subject_id ) {
				$et_pb_ab_subject[ $post_id ] = $ab_subject;

				// no need to continue.
				break;
			}
		}

		// If no valid subject found, get the first one.
		if ( isset( $ab_subjects[0] ) && ! et_()->array_get( $et_pb_ab_subject, $post_id, '' ) ) {
			$et_pb_ab_subject[ $post_id ] = $ab_subjects[0];
		}
	} else {
		// First visit. Get next subject on queue.
		$next_subject_index = get_post_meta( $post_id, '_et_pb_ab_next_subject', true );

		// Get current subject index based on `_et_pb_ab_next_subject` post meta value.
		$subject_index = false !== $next_subject_index && isset( $ab_subjects[ $next_subject_index ] ) ? (int) $next_subject_index : 0;

		// Get current subject index.
		$et_pb_ab_subject[ $post_id ] = $ab_subjects[ $subject_index ];

		// Hash the subject.
		$hashed_subject_id = hash_hmac( 'md5', $et_pb_ab_subject[ $post_id ], $ab_hash_key );

		// Set cookie for returning visit.
		et_pb_ab_set_visitor_cookie( $post_id, 'view_page', $hashed_subject_id );

		// Bump subject index and save on post meta for next visitor.
		et_pb_ab_increment_current_ab_module_id( $post_id );

		// log the view_page event right away.
		$is_et_fb_enabled = function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled();

		if ( ! is_admin() && ! $is_et_fb_enabled ) {
			et_pb_add_stats_record(
				array(
					'test_id'     => $post_id,
					'subject_id'  => $et_pb_ab_subject[ $post_id ],
					'record_type' => 'view_page',
				)
			);
		}
	}
}
