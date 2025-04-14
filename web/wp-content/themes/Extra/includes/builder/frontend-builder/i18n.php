<?php
// Add strings from i18n directory. Note: We don't handle subdirectories, but we should in the future.
$i18n_files = glob( __DIR__ . '/i18n/*.php' );

// Library localization has been moved to the Cloud app.
$root_directory = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ? ET_BUILDER_PLUGIN_DIR : get_template_directory();
$i18n_library   = require $root_directory . '/cloud/i18n/library.php';

$strings = array();

foreach ( $i18n_files as $file ) {
	$filename        = basename( $file, '.php' );
	$key             = et_()->camel_case( $filename );
	$strings[ $key ] = require $file;
}

$strings['library'] = $i18n_library;

return $strings;
