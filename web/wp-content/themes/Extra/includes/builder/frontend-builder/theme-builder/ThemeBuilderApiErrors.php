<?php

class ET_Theme_Builder_Api_Errors {
	const UNKNOWN                       = 'unknown';
	const PORTABILITY_INCORRECT_CONTEXT = 'incorrect_context';
	const PORTABILITY_REQUIRE_INCOMING_LAYOUT_DUPLICATE_DECISION = 'require_incoming_layout_duplicate_decision';
	const PORTABILITY_IMPORT_PRESETS_FAILURE                     = 'import_presets_failure';
	const PORTABILITY_IMPORT_INVALID_FILE                        = 'invalid_file';

	/**
	 * Get map of all error codes.
	 *
	 * @since 4.0
	 *
	 * @return string[]
	 */
	public static function getMap() {
		return array(
			'unknown'                         => self::UNKNOWN,
			'portabilityIncorrectContext'     => self::PORTABILITY_INCORRECT_CONTEXT,
			'portabilityRequireIncomingLayoutDuplicateDecision' => self::PORTABILITY_REQUIRE_INCOMING_LAYOUT_DUPLICATE_DECISION,
			'portabilityImportPresetsFailure' => self::PORTABILITY_IMPORT_PRESETS_FAILURE,
			'portabilityImportInvalidFile'    => self::PORTABILITY_IMPORT_INVALID_FILE,
		);
	}
}
