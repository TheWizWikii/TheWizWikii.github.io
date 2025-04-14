<?php
/**
 * Register autoloader.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.6.2
 */

/**
 * Autoloader for module fields.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_fields( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "module/field/{$class}.php";
}

/**
 * Autoloader for module helpers.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_helpers( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "module/helpers/{$class}.php";
}

/**
 * Autoloader for module motion helpers.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_helpers_motion( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "module/helpers/motion/{$class}.php";
}

/**
 * Autoloader for module mask style.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_mask_pattern_helpers( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "feature/background-masks/{$class}.php";
}

/**
 * Autoloader for module mask style.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_mask( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "feature/background-masks/mask/{$class}.php";
}

/**
 * Autoloader for module pattern style.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_pattern( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "feature/background-masks/pattern/{$class}.php";
}

/**
 * Autoloader for module types.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_types( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );
	require_once "module/type/{$class}.php";
}

/**
 * Autoloader for woo modules.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_woo_modules( $class ) {
	if ( et_is_woocommerce_plugin_active() ) {
		// For multipart classnames.
		$class = str_replace( '_', '', $class );
		require_once "module/woocommerce/{$class}.php";
	}
}

/**
 * Autoloader for modules.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload_modules( $class ) {
	// For multipart classnames.
	$class = str_replace( '_', '', $class );

	if ( file_exists( ET_BUILDER_DIR . "module/{$class}.php" ) ) {
		require_once "module/{$class}.php";
	} elseif ( 'Shop' === $class && et_is_woocommerce_plugin_active() &&

		/*
		 * ET_Builder_Module_Shop class moved to "woocommerce/" directory
		 * as part of WooCommerce Modules v2.
		*/
		file_exists( ET_BUILDER_DIR . "module/woocommerce/{$class}.php" ) ) {
		require_once "module/woocommerce/{$class}.php";
	}
}

/**
 * Autoloader for module helpers and structure elements.
 *
 * @param string $class The class name.
 */
function _et_pb_autoload( $class ) {
	if ( in_array( $class, [ 'ET_Builder_Section', 'ET_Builder_Row', 'ET_Builder_Row_Inner', 'ET_Builder_Column' ], true ) ) {
		require_once 'main-structure-elements.php';
	} elseif ( 'ET_Builder_I18n' === $class ) {
		require_once 'feature/I18n.php';
	} elseif ( in_array( $class, [ 'ET_Builder_Element', 'ET_Builder_Module', 'ET_Builder_Structure_Element' ], true ) ) {
		// This is needed for custom module that extends official module and gets registered in unexpected location.
		require_once ET_BUILDER_DIR . 'functions.php';

		require_once 'class-et-builder-element.php';
	} elseif ( 'ET_Builder_Module_Shortcode_Manager' === $class ) {
		require_once 'class-et-builder-module-shortcode-manager.php';
	} elseif ( 'ET_Builder_Post_Feature_Base' === $class ) {
		require_once 'class-et-builder-post-feature-base.php';
	} elseif ( 'ET_Builder_Module_Features' === $class ) {
		require_once 'class-et-builder-module-features.php';
	} elseif ( 'ET_Builder_Module_Fields_Factory' === $class ) {
		require_once 'module/field/Factory.php';
	} elseif ( 'ET_Builder_Module_Use_Detection' === $class ) {
		require_once 'class-et-builder-module-use-detection.php';
	} elseif ( 'ET_Global_Settings' === $class ) {
		require_once 'class-et-global-settings.php';
	} elseif ( 'ET_Builder_Global_Feature_Base' === $class ) {
		require_once 'class-et-builder-global-feature-base.php';
	} elseif ( 'ET_Builder_Google_Fonts_Feature' === $class ) {
		require_once 'class-et-builder-google-fonts-feature.php';
	} elseif ( 'ET_Builder_Dynamic_Assets_Feature' === $class ) {
		require_once 'class-et-builder-dynamic-assets-feature.php';
	} elseif ( 'ET_Builder_Module_Field_DisplayConditions' === $class ) {
		require_once 'module/field/DisplayConditions.php';
	} elseif ( strpos( $class, 'ET_Builder_Background_Mask' ) !== false || strpos( $class, 'ET_Builder_Background_Pattern' ) !== false ) {
		_et_pb_autoload_mask_pattern_helpers( str_replace( 'ET_Builder_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Mask_' ) !== false ) {
		_et_pb_autoload_mask( str_replace( 'ET_Builder_Mask_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Pattern_' ) !== false ) {
		_et_pb_autoload_pattern( str_replace( 'ET_Builder_Pattern_', '', $class ) );
	} elseif ( 'ET_Builder_Woocommerce_Product_Simple_Placeholder' === $class ) {
		require_once 'feature/woocommerce/placeholder/WoocommerceProductSimplePlaceholder.php';
	} elseif ( strpos( $class, 'ET_Builder_Module_Helper_Motion_' ) !== false ) {
		_et_pb_autoload_helpers_motion( str_replace( 'ET_Builder_Module_Helper_Motion_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Module_Helper_' ) !== false ) {
		_et_pb_autoload_helpers( str_replace( 'ET_Builder_Module_Helper_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Module_Field_' ) !== false ) {
		_et_pb_autoload_fields( str_replace( 'ET_Builder_Module_Field_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Module_Type_' ) !== false ) {
		_et_pb_autoload_types( str_replace( 'ET_Builder_Module_Type_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Module_Woocommerce_' ) !== false ) {
		_et_pb_autoload_woo_modules( str_replace( 'ET_Builder_Module_Woocommerce_', '', $class ) );
	} elseif ( strpos( $class, 'ET_Builder_Module_' ) !== false ) {
		_et_pb_autoload_modules( str_replace( 'ET_Builder_Module_', '', $class ) );
	} elseif ( 'ET_Item_Library_Local' === $class ) {
		require_once ET_CORE_PATH . '/item-library-local/ItemLibraryLocal.php';
	} elseif ( 'ET_Theme_Options_Library_Local' === $class ) {
		require_once ET_EPANEL_DIR . '/theme-options-library/theme-options-library-local/ThemeOptionsLibraryLocal.php';
	} elseif ( 'ET_Code_Snippets_Library_Local' === $class ) {
		require_once ET_CORE_PATH . '/code-snippets/code-snippets-library-local/CodeSnippetsLibraryLocal.php';
	} elseif ( 'ET_Theme_Builder_Library_Local' === $class ) {
		require_once ET_BUILDER_DIR . 'frontend-builder/theme-builder/theme-builder-library-local/ThemeBuilderLibraryLocal.php';
	}
}

spl_autoload_register( '_et_pb_autoload' );

/**
 * Get an instance of  `ET_Builder_Module_Helper_Multi_Value`.
 *
 * @return ET_Builder_Module_Helper_Multi_Value
 */
function et_pb_multi_value() {
	return ET_Builder_Module_Helper_Multi_Value::instance();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Overflow`.
 *
 * @return ET_Builder_Module_Helper_Overflow
 */
function et_pb_overflow() {
	return ET_Builder_Module_Helper_Overflow::get();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Alignment`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Alignment
 */
function et_pb_alignment_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Alignment( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Height`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Height
 */
function et_pb_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Height( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Hover_Options`.
 *
 * @return ET_Builder_Module_Helper_Hover_Options
 */
function et_pb_hover_options() {
	return ET_Builder_Module_Helper_Hover_Options::get();
}

/**
 * Get sticky option instance.
 *
 * @since 4.6.0
 *
 * @return ET_Builder_Module_Helper_Sticky_Options
 */
function et_pb_sticky_options() {
	return ET_Builder_Module_Helper_Sticky_Options::get();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Max_Height`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Max_Height
 */
function et_pb_max_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Max_Height( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Max_Width`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Max_Width
 */
function et_pb_max_width_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Max_Width( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Min_Height`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Min_Height
 */
function et_pb_min_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Min_Height( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Helper_ResponsiveOptions`.
 *
 * @return ET_Builder_Module_Helper_ResponsiveOptions
 */
function et_pb_responsive_options() {
	return ET_Builder_Module_Helper_ResponsiveOptions::instance();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Slider`.
 *
 * @return ET_Builder_Module_Helper_Slider
 */
function et_pb_slider_options() {
	return new ET_Builder_Module_Helper_Slider();
}

/**
 * Get an instance of `ET_Builder_Module_Transition_Options`.
 *
 * @return ET_Builder_Module_Transition_Options
 */
function et_pb_transition_options() {
	return ET_Builder_Module_Helper_Transition_Options::get();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Width`.
 *
 * @param string $prefix The prefix string that may be added to field name.
 *
 * @return ET_Builder_Module_Helper_Width
 */
function et_pb_width_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Width( $prefix );
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Font`.
 *
 * @return ET_Builder_Module_Helper_Font
 */
function et_pb_font_options() {
	return ET_Builder_Module_Helper_Font::instance();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_BackgroundLayout`.
 *
 * @return ET_Builder_Module_Helper_BackgroundLayout
 */
function et_pb_background_layout_options() {
	return ET_Builder_Module_Helper_BackgroundLayout::instance();
}

/**
 * Get helper instance
 *
 * @since 4.6.0
 *
 * @param string $helper_name Helper name.
 *
 * @return object
 */
function et_builder_get_helper( $helper_name ) {
	switch ( $helper_name ) {
		case 'sticky':
			$helper = et_pb_sticky_options();
			break;

		case 'hover':
			$helper = et_pb_hover_options();
			break;

		case 'responsive':
			$helper = et_pb_responsive_options();
			break;

		default:
			$helper = false;
			break;
	}

	return $helper;
}

/**
 * Class ET_Builder_Module_Helper_MultiViewOptions wrapper
 *
 * @since 3.27.1
 *
 * @param ET_Builder_Element|bool $module             Module object.
 * @param array                   $custom_props       Defined custom props data.
 * @param array                   $conditional_values Defined options conditional values.
 * @param array                   $default_values     Defined options default values.
 *
 * @return ET_Builder_Module_Helper_MultiViewOptions
 */
function et_pb_multi_view_options( $module = false, $custom_props = array(), $conditional_values = array(), $default_values = array() ) {
	return new ET_Builder_Module_Helper_MultiViewOptions( $module, $custom_props, $conditional_values, $default_values );
}

if ( et_is_woocommerce_plugin_active() ) {
	add_filter(
		'et_builder_get_woo_default_columns',
		array(
			'ET_Builder_Module_Helper_Woocommerce_Modules',
			'get_columns_posts_default_value',
		)
	);
}

/**
 * Get an instance of `ET_Builder_Module_Helper_OptionTemplate`.
 *
 * @return ET_Builder_Module_Helper_OptionTemplate
 */
function et_pb_option_template() {
	return ET_Builder_Module_Helper_OptionTemplate::instance();
}

/**
 * Get an instance of `ET_Builder_Module_Helper_Background`.
 *
 * @return ET_Builder_Module_Helper_Background
 *
 * @since 4.3.3
 */
function et_pb_background_options() {
	return ET_Builder_Module_Helper_Background::instance();
}

/**
 * Class ET_Builder_Module_Helper_Media wrapper
 *
 * @since 4.6.4
 *
 * @return ET_Builder_Module_Helper_Media
 */
function et_pb_media_options() {
	return ET_Builder_Module_Helper_Media::instance();
}

/**
 * Class ET_Theme_Options_Library_Local wrapper
 *
 * @since 4.21.0
 *
 * @return ET_Theme_Options_Library_Local
 */
function et_pb_theme_options_library_local() {
	return ET_Theme_Options_Library_Local::instance();
}

/**
 * Class ET_Code_Snippets_Library_Local wrapper
 *
 * @since 4.21.0
 *
 * @return ET_Code_Snippets_Library_Local
 */
function et_pb_code_snippets_library_local() {
	return ET_Code_Snippets_Library_Local::instance();
}

/**
 * Class ET_Theme_Builder_Library_Local wrapper
 *
 * @since 4.21.0
 *
 * @return ET_Theme_Builder_Library_Local
 */
function et_pb_theme_builder_library_local() {
	return ET_Theme_Builder_Library_Local::instance();
}
