<?php
/**
 * WHIP libary file.
 *
 * @package Yoast\WHIP
 */

use Yoast\WHIPv2\MessageDismisser;
use Yoast\WHIPv2\Presenters\WPMessagePresenter;
use Yoast\WHIPv2\RequirementsChecker;
use Yoast\WHIPv2\VersionRequirement;
use Yoast\WHIPv2\WPDismissOption;

if ( ! function_exists( 'whip_wp_check_versions' ) ) {
	/**
	 * Facade to quickly check if version requirements are met.
	 *
	 * @param array<string> $requirements The requirements to check.
	 *
	 * @return void
	 */
	function whip_wp_check_versions( $requirements ) {
		// Only show for admin users.
		if ( ! is_array( $requirements ) ) {
			return;
		}

		$config  = include __DIR__ . '/../Configs/default.php';
		$checker = new RequirementsChecker( $config );

		foreach ( $requirements as $component => $versionComparison ) {
			$checker->addRequirement( VersionRequirement::fromCompareString( $component, $versionComparison ) );
		}

		$checker->check();

		if ( ! $checker->hasMessages() ) {
			return;
		}

		$dismissThreshold = ( WEEK_IN_SECONDS * 4 );
		$dismissMessage   = __( 'Remind me again in 4 weeks.', 'default' );

		$dismisser = new MessageDismisser( time(), $dismissThreshold, new WPDismissOption() );

		$presenter = new WPMessagePresenter( $checker->getMostRecentMessage(), $dismisser, $dismissMessage );

		// Prevent duplicate notices across multiple implementing plugins.
		if ( ! has_action( 'whip_register_hooks' ) ) {
			add_action( 'whip_register_hooks', array( $presenter, 'registerHooks' ) );
		}

		/**
		 * Fires during hooks registration for the message presenter.
		 *
		 * @param WPMessagePresenter $presenter Message presenter instance.
		 */
		do_action( 'whip_register_hooks', $presenter );
	}
}
