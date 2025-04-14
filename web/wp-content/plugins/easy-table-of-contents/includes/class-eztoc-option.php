<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Option' ) ) {

	/**
	 * Class ezTOC_Option
	 *
	 * Credit: Adapted from Easy Digital Downloads.
	 */
	final class ezTOC_Option {

		/**
		 * Register the plugins core settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function register() {

			if ( false === get_option( 'ez-toc-settings' ) ) {

				add_option( 'ez-toc-settings', self::getDefaults() );
			}

			foreach ( self::getRegistered() as $section => $settings ) {

				add_settings_section(
					'ez_toc_settings_' . $section,
					__return_null(),
					'__return_false',
					'ez_toc_settings_' . $section
				);

				foreach ( $settings as $option ) {

					$name = isset( $option['name'] ) ? $option['name'] : '';

					add_settings_field(
						'ez-toc-settings[' . $option['id'] . ']',
						$name,
						method_exists( __CLASS__, $option['type'] ) ? array( __CLASS__, $option['type'] ) : array( __CLASS__, 'missingCallback' ),
						'ez_toc_settings_' . $section,
						'ez_toc_settings_' . $section,
						array(
							'section'     => $section,
							'id'          => isset( $option['id'] ) ? $option['id'] : null,
							'label_for'   => isset( $option['id'] ) && isset( $option['type'] ) && $option['type'] == 'checkbox' ? 'ez-toc-settings[' . $option['id'] . ']' : null,
							'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
							'name'        => isset( $option['name'] ) ? $option['name'] : null,
							'size'        => isset( $option['size'] ) ? $option['size'] : null,
							'options'     => isset( $option['options'] ) ? $option['options'] : '',
							'default'     => isset( $option['default'] ) ? $option['default'] : '',
							'min'         => isset( $option['min'] ) ? $option['min'] : null,
							'max'         => isset( $option['max'] ) ? $option['max'] : null,
							'step'        => isset( $option['step'] ) ? $option['step'] : null,
							'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : null,
							'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null,
							'allow_blank' => isset( $option['allow_blank'] ) ? $option['allow_blank'] : true,
							'readonly'    => isset( $option['readonly'] ) ? $option['readonly'] : false,
							'faux'        => isset( $option['faux'] ) ? $option['faux'] : false,
							'without_hr'        => isset( $option['without_hr'] ) ? $option['without_hr'] : true,
							'allowedHtml'       => isset( $option['allowedHtml'] ) ? $option['allowedHtml'] : [],
						)
					);
				}

			}

			// Creates our settings in the options table
			register_setting( 'ez-toc-settings', 'ez-toc-settings', array( __CLASS__, 'sanitize' ) );
		}

		/**
		 * Callback for settings sanitization.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param array $input The value inputted in the field.
		 *
		 * @return string $input Sanitized value.
		 */
		public static function sanitize( $input = array() ) {

			$options = self::getOptions();
			//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Nonce is already verified in the settings page
			if ( empty( $_POST['_wp_http_referer'] ) ) {

				return $input;
			}
			// Code to settings backup file
			$uploaded_file_settings = array();
			//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Nonce is already verified in the settings page
			if(isset($_FILES['eztoc_import_backup'])){
				//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Nonce is already verified in the settings page
		    	$fileInfo = wp_check_filetype(basename($_FILES['eztoc_import_backup']['name']));
		        if (!empty($fileInfo['ext']) && $fileInfo['ext'] == 'json') {
					//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Nonce is already verified in the settings page
		            if(!empty($_FILES["eztoc_import_backup"]["tmp_name"])){
						//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Nonce is already verified in the settings page
		            	$uploaded_file_settings = json_decode(eztoc_read_file_contents($_FILES["eztoc_import_backup"]["tmp_name"]), true);	
		           }
		        }
		    }
		    if(!empty($uploaded_file_settings) && is_array($uploaded_file_settings) && count($uploaded_file_settings) >= 40){
		    	$etoc_default_settings = self::getDefaults();
		    	if(!empty($etoc_default_settings) && is_array($etoc_default_settings)){
		    		// Pro Options
		    		$etoc_default_settings['exclude_by_class'] = '';
		    		$etoc_default_settings['exclude_by_shortcode'] = '';
		    		$etoc_default_settings['fixedtoc'] = false;
		    		$etoc_default_settings['highlightheadings'] = false;
		    		$etoc_default_settings['shrinkthewidth'] = false;
		    		$etoc_default_settings['acf-support'] = false;
		    		$etoc_default_settings['gp-premium-element-support'] = false;
		    		$exported_array = array();
		    		foreach ($etoc_default_settings as $inkey => $invalue) {
				    	foreach ($uploaded_file_settings as $ufs_key => $ufs_value) {
				    		if($inkey == $ufs_key){
								if(is_array($ufs_value)){
									$exported_array[$inkey] = array_map('sanitize_text_field', $ufs_value);	
								}else{
				    				$exported_array[$inkey] = sanitize_text_field($ufs_value);
								}
				    		}
				    	}
				    }
				    if(count($exported_array) >= 40){
				    	$input = array();
				    	$input = $exported_array;
				    }
			    }
		    }

			$registered = self::getRegistered();

			foreach ( $registered as $sectionID => $sectionOptions ) {

				$input = $input ? $input : array();
				$input = apply_filters( 'ez_toc_settings_' . $sectionID . '_sanitize', $input );

				// Loop through each setting being saved and pass it through a sanitization filter
				foreach ( $input as $key => $value ) {

					// Get the setting type (checkbox, select, etc)
					$type = isset( $registered[ $sectionID ][ $key ]['type'] ) ? $registered[ $sectionID ][ $key ]['type'] : false;

					if ( $type ) {

						// Field type specific filter
						$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize_' . $type, $value, $key );
					}

					// General filter
					$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize', $input[ $key ], $key );
				}

				// Loop through the registered options.
				foreach ( $sectionOptions as $optionID => $optionProperties ) {

					// Unset any that are empty for the section being saved.
					if ( empty( $input[ $optionID ] ) ) {

						unset( $options[ $optionID ] );
					}

					// Check for the checkbox option type.
					if ( array_key_exists( 'type', $optionProperties ) && 'checkbox' == $optionProperties['type'] ) {

						// If it does not exist in the options values being saved, add the option ID and set its value to `0`.
						// This matches WP core behavior for saving checkbox option values.
						if ( ! array_key_exists( $optionID, $input ) ) {

							$input[ $optionID ] = '0';
						}
					}
				}

			}

			// Merge our new settings with the existing
			$output = array_merge( $options, $input );

			return $output;
		}

		/**
		 * The core registered settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getRegistered() {
												
			$options = array(
				'general' => apply_filters(
					'ez_toc_settings_general',
					array(
						'enabled_post_types' => array(
							'id' => 'enabled_post_types',
							'name' => esc_html__( 'Enable Support', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select the post types to enable the support for table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'auto_insert_post_types' => array(
							'id' => 'auto_insert_post_types',
							'name' => esc_html__( 'Auto Insert', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select the post types which will have the table of contents automatically inserted.', 'easy-table-of-contents' ) .
							          '<br><span class="description">' . esc_html__( 'NOTE: The table of contents will only be automatically inserted on post types for which it has been enabled.', 'easy-table-of-contents' ) . '<span>',
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'position' => array(
							'id' => 'position',
							'name' => esc_html__( 'Position', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Choose where where you want to display the table of contents.', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'before' => esc_html__( 'Before first heading (default)', 'easy-table-of-contents' ),
								'after' => esc_html__( 'After first heading', 'easy-table-of-contents' ),
								'afterpara' => esc_html__( 'After first paragraph', 'easy-table-of-contents' ),
								'aftercustompara' => esc_html__( 'After paragraph number', 'easy-table-of-contents' ),
								'aftercustomimg' => esc_html__( 'After Image number', 'easy-table-of-contents' ),
								'top' => esc_html__( 'Top', 'easy-table-of-contents' ),
								'bottom' => esc_html__( 'Bottom', 'easy-table-of-contents' ),
							),
							'default' => 1,
						),
						'custom_para_number' => array(
							'id' => 'custom_para_number',
							'name' => esc_html__( 'Select Paragraph', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select paragraph after which ETOC should get display', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 1,
						),
						'custom_img_number' => array(
							'id' => 'custom_img_number',
							'name' => esc_html__( 'Select Image', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select Image after which ETOC should get display', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 1,
						),
						'blockqoute_checkbox' => array(
							'id' => 'blockqoute_checkbox',
							'name' => esc_html__( 'Exclude Blockqoute', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Do not consider Paragraphs which are inside Blockqoute', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'start' => array(
							'id' => 'start',
							'name' => esc_html__( 'Show when', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'or more headings are present', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array_combine( range( 1, 10 ), range( 1, 10 ) ),
							'default' => 2,
						),
						'show_heading_text' => array(
							'id' => 'show_heading_text',
							'name' => esc_html__( 'Display Header Label', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show header text above the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						'visibility_on_header_text' => array(
							'id' => 'visibility_on_header_text',
							'name' => esc_html__( 'Toggle on Header Text', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Allow the user to toggle the visibility of the table of contents on header text', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'heading_text' => array(
							'id' => 'heading_text',
							'name' => esc_html__( 'Header Label', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Eg: Contents, Table of Contents, Page Contents', 'easy-table-of-contents' ),
							'type' => 'text',
							'default' => esc_html__( 'Contents', 'easy-table-of-contents' ),
						),
						'heading_text_tag' => array(
							'id' => 'heading_text_tag',
							'name' => esc_html__( 'Header Label Tag', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'p' => esc_html__( 'p (default)', 'easy-table-of-contents' ),
								'span' => esc_html__( 'span', 'easy-table-of-contents' ),
								'div' => esc_html__( 'div', 'easy-table-of-contents' ),
								'label' => esc_html__( 'label', 'easy-table-of-contents' ),
							),
							'default' => 'p',
						),
						'visibility' => array(
							'id' => 'visibility',
							'name' => esc_html__( 'Toggle View', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Allow the user to toggle the visibility of the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						'visibility_hide_by_default' => array(
							'id' => 'visibility_hide_by_default',
							'name' => esc_html__( 'Initial View', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Initially hide the table of contents.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'show_hierarchy' => array(
							'id' => 'show_hierarchy',
							'name' => esc_html__( 'Show as Hierarchy', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						'counter' => array(
							'id' => 'counter',
							'name' => esc_html__( 'Counter', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => self::getCounterList(),
							'default' => 'decimal',
						),
						'counter-position' => array(
							'id' => 'counter-position',
							'name' => esc_html__( 'Counter Position', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => self::getCounterPositionList(),
							'default' => 'inside',
						),
						'toc_loading' => array(
							'id' => 'toc_loading',
							'name' => esc_html__( 'TOC Loading Method', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'js' => esc_html__( 'JavaScript (default)', 'easy-table-of-contents' ),
								'css' => esc_html__( 'Pure CSS', 'easy-table-of-contents' ),
								 
							),
							'default' => 'js',
						),
						'smooth_scroll' => array(
							'id' => 'smooth_scroll',
							'name' => esc_html__( 'Smooth Scroll', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						),
						'avoid_anch_jump' => array(
							'id' => 'avoid_anch_jump',
							'name' => esc_html__( 'Exclude href from url', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Jump link works without adding ids in the URL', 'easy-table-of-contents' ) .
							          '<br><span class="description">' . esc_html__( 'NOTE: Please keep Smooth Scroll "ON" to make this option work properly.', 'easy-table-of-contents' ) . '<span>',
							'type' => 'checkbox',
							'default' => false,
						),
                                            
                        	'toc-run-on-amp-pages' => array(
							'id' => 'toc-run-on-amp-pages',
							'name' => esc_html__( 'TOC AMP Page Support', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'You can on or off Easy TOC for the AMP Pages.', 'easy-table-of-contents' ),
							'type'    => 'checkbox',
							'default' => 'Off',
						),
					)
				),
				'appearance' => apply_filters(
					'ez_toc_settings_appearance',
					array(
						'width' => array(
							'id' => 'width',
							'name' => esc_html__( 'Width', 'easy-table-of-contents' ),
							'type' => 'selectgroup',
							'options' => array(
								'fixed' => array(
									'name' => esc_html__( 'Fixed', 'easy-table-of-contents' ),
									'options' => array(
										'200px' => '200px',
										'225px' => '225px',
										'250px' => '250px',
										'275px' => '275px',
										'300px' => '300px',
										'325px' => '325px',
										'350px' => '350px',
										'375px' => '375px',
										'400px' => '400px',
									),
								),
								'relative' => array(
									'name' => esc_html__( 'Relative', 'easy-table-of-contents' ),
									'options' => array(
										'auto' => esc_html__( 'Auto', 'easy-table-of-contents' ),
										'25%' => '25%',
										'33%' => '33%',
										'50%' => '50%',
										'66%' => '66%',
										'75%' => '75%',
										'100%' => '100%',
									),
								),
								'other' => array(
									'name' => esc_html__( 'Custom', 'easy-table-of-contents' ),
									'options' => array(
										'custom' => esc_html__( 'User Defined', 'easy-table-of-contents' ),
									),
								),
							),
							'default' => 'auto',
						),
						'width_custom' => array(
							'id' => 'width_custom',
							'name' => esc_html__( 'Custom Width', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select the User Defined option from the Width option to utilitze the custom width.', 'easy-table-of-contents' ),
							'type' => 'custom_width',
							'default' => 275,
						),
						'wrapping' => array(
							'id' => 'wrapping',
							'name' => esc_html__( 'Alignment', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'none' => esc_html__( 'None (Default)', 'easy-table-of-contents' ),
								'left' => esc_html__( 'Left', 'easy-table-of-contents' ),
								'right' => esc_html__( 'Right', 'easy-table-of-contents' ),
								'center' => esc_html__( 'Center', 'easy-table-of-contents' ),
							),
							'default' => 'none',
						),
						'toc_wrapping'  => array(
							'id'      => 'toc_wrapping',
							'name'    => esc_html__( 'Enable Wrapping', 'easy-table-of-contents' ),
							'type'    => 'checkbox',
							'default' => false,
						),
						'headings-padding'                   => array(
							'id'      => 'headings-padding',
							'name'    => esc_html__( 'Headings Padding', 'easy-table-of-contents' ),
							'type'    => 'checkbox',
							'default' => false,
						),
						'headings-padding-top' => array(
							'id' => 'headings-padding-top',
							'name' => esc_html__( 'Headings Padding Top', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 0,
						),
						'headings-padding-bottom' => array(
							'id' => 'headings-padding-bottom',
							'name' => esc_html__( 'Headings Padding Bottom', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 0,
						),
						'headings-padding-left' => array(
							'id' => 'headings-padding-left',
							'name' => esc_html__( 'Headings Padding Left', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 0,
						),
						'headings-padding-right' => array(
							'id' => 'headings-padding-right',
							'name' => esc_html__( 'Headings Padding Right', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 0,
						),
						'font_options_header' => array(
							'id' => 'font_options',
							'name' => '<strong>' . esc_html__( 'Font Option', 'easy-table-of-contents' ) . '</strong>',
							'type' => 'header',
						),
						'title_font_size' => array(
							'id' => 'title_font_size',
							'name' => esc_html__( 'Title Font Size', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 120,
						),
						'title_font_weight' => array(
							'id' => 'title_font_weight',
							'name' => esc_html__( 'Title Font Weight', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'100' => esc_html__( 'Thin', 'easy-table-of-contents' ),
								'200' => esc_html__( 'Extra Light', 'easy-table-of-contents' ),
								'300' => esc_html__( 'Light', 'easy-table-of-contents' ),
								'400' => esc_html__( 'Normal', 'easy-table-of-contents' ),
								'500' => esc_html__( 'Medium', 'easy-table-of-contents' ),
								'600' => esc_html__( 'Semi Bold', 'easy-table-of-contents' ),
								'700' => esc_html__( 'Bold', 'easy-table-of-contents' ),
								'800' => esc_html__( 'Extra Bold', 'easy-table-of-contents' ),
								'900' => esc_html__( 'Heavy', 'easy-table-of-contents' ),
							),
							'default' => '500',
						),
						'font_size' => array(
							'id' => 'font_size',
							'name' => esc_html__( 'Font Size', 'easy-table-of-contents' ),
							'type' => 'font_size',
							'default' => 95,
						),
						'font_weight' => array(
							'id' => 'font_weight',
							'name' => esc_html__( 'Font Weight', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'100' => esc_html__( 'Thin', 'easy-table-of-contents' ),
								'200' => esc_html__( 'Extra Light', 'easy-table-of-contents' ),
								'300' => esc_html__( 'Light', 'easy-table-of-contents' ),
								'400' => esc_html__( 'Normal', 'easy-table-of-contents' ),
								'500' => esc_html__( 'Medium', 'easy-table-of-contents' ),
								'600' => esc_html__( 'Semi Bold', 'easy-table-of-contents' ),
								'700' => esc_html__( 'Bold', 'easy-table-of-contents' ),
								'800' => esc_html__( 'Extra Bold', 'easy-table-of-contents' ),
								'900' => esc_html__( 'Heavy', 'easy-table-of-contents' ),
							),
							'default' => '500',
						),
						'child_font_size' => array(
							'id' => 'child_font_size',
							'name' => esc_html__( 'Child Font Size', 'easy-table-of-contents' ),
							'type' => 'child_font_size',
							'default' => 90,
						),						
						'theme_option_header' => array(
							'id' => 'theme_option_header',
							'name' => '<strong>' . esc_html__( 'Theme Options', 'easy-table-of-contents' ) . '</strong>',
							'type' => 'header',
						),
						'theme' => array(
							'id' => 'theme',
							'name' => esc_html__( 'Theme', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'The theme is only applied to the table of contents which is auto inserted into the post. The Table of Contents widget will inherit the theme widget styles.', 'easy-table-of-contents' ),
							'type' => 'radio',
							'options' => array(
								'grey' => esc_html__( 'Grey', 'easy-table-of-contents' ),
								'light-blue' => esc_html__( 'Light Blue', 'easy-table-of-contents' ),
								'white' => esc_html__( 'White', 'easy-table-of-contents' ),
								'black' => esc_html__( 'Black', 'easy-table-of-contents' ),
								'transparent' => esc_html__( 'Transparent', 'easy-table-of-contents' ),
								'custom' => esc_html__( 'Custom', 'easy-table-of-contents' ),
							),
							'default' => 'grey',
						),
						'custom_theme_header' => array(
							'id' => 'custom_theme_header',
							'name' => '<strong>' . esc_html__( 'Custom Theme', 'easy-table-of-contents' ) . '</strong>',
							'desc' => esc_html__( 'For the following settings to apply, select the Custom Theme option.', 'easy-table-of-contents' ),
							'type' => 'header',
						),
						'custom_background_colour' => array(
							'id' => 'custom_background_colour',
							'name' => esc_html__( 'Background Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#fff',
						),
						'custom_border_colour' => array(
							'id' => 'custom_border_colour',
							'name' => esc_html__( 'Border Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#ddd',
						),
						'custom_title_colour' => array(
							'id' => 'custom_title_colour',
							'name' => esc_html__( 'Title Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#999',
						),
						'custom_link_colour' => array(
							'id' => 'custom_link_colour',
							'name' => esc_html__( 'Link Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#428bca',
						),
						'custom_link_hover_colour' => array(
							'id' => 'custom_link_hover_colour',
							'name' => esc_html__( 'Link Hover Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#2a6496',
						),
						'custom_link_visited_colour' => array(
							'id' => 'custom_link_visited_colour',
							'name' => esc_html__( 'Link Visited Color', 'easy-table-of-contents' ),
							'type' => 'color',
							'default' => '#428bca',
						),
						'heading-text-direction' => array(
                            'id' => 'heading-text-direction',
                            'name' => esc_html__( 'Heading Text Direction', 'easy-table-of-contents' ),
                            'type' => 'radio',
                            'options' => array(
                                'ltr' => esc_html__( 'Left to Right (LTR)', 'easy-table-of-contents' ),
                                'rtl' => esc_html__( 'Right to Left (RTL)', 'easy-table-of-contents' ),
                            ),
                            'default' => 'ltr',
                        ),
					)
				),
				'advanced' => apply_filters(
					'ez_toc_settings_advanced',
					array(
						'lowercase' => array(
							'id' => 'lowercase',
							'name' => esc_html__( 'Lowercase', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Ensure anchors are in lowercase.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'hyphenate' => array(
							'id' => 'hyphenate',
							'name' => esc_html__( 'Hyphenate', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Use - rather than _ in anchors.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_homepage' => array(
							'id' => 'include_homepage',
							'name' => esc_html__( 'Homepage', 'easy-table-of-contents' ),
							'desc' => sprintf(/* translators: %s: URL to the documentation */
								      esc_html__( 'Show the table of contents for qualifying items on the homepage.', 'easy-table-of-contents' ).' <a target="_blank" href="%s">'.esc_html__( 'Learn More', 'easy-table-of-contents' ).'</a>', 'https://tocwp.com/docs/knowledge-base/how-to-add-a-table-of-content-on-the-homepage/'
								      ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_category' => array(
							'id' => 'include_category',
							'name' => esc_html__( 'Category', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the table of contents for description on the category pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_tag' => array(
							'id' => 'include_tag',
							'name' => esc_html__( 'Tag', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the table of contents for description on the tag pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_product_category' => array(
							'id' => 'include_product_category',
							'name' => esc_html__( 'Product Category', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the table of contents for description on the product category pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'include_custom_tax' => array(
							'id' => 'include_custom_tax',
							'name' => esc_html__( 'Custom Taxonomy', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the table of contents for description on the custom taxonomy pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'device_target' => array(
							'id' => 'device_target',
							'name' => esc_html__( 'Device Target', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'' => esc_html__( 'Select', 'easy-table-of-contents' ),
								'mobile' => esc_html__( 'Mobile', 'easy-table-of-contents' ),
								'desktop' => esc_html__( 'Desktop', 'easy-table-of-contents' ),
								 
							),
							'default' => 'Select',
						),
						'load_js_in' => array(
							'id' => 'load_js_in',
							'name' => esc_html__( 'Load Js In', 'easy-table-of-contents' ),
							'type' => 'select',
							'options' => array(
								'footer' => esc_html__( 'Footer (default)', 'easy-table-of-contents' ),
								'header' => esc_html__( 'Header', 'easy-table-of-contents' ),
								 
							),
							'default' => 'footer',
						),
						'exclude_css' => array(
							'id' => 'exclude_css',
							'name' => esc_html__( 'CSS', 'easy-table-of-contents' ),
							'desc' => esc_html__( "Prevent the loading the core CSS styles. When selected, the appearance options from above will be ignored.", 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'inline_css' => array(
							'id' => 'inline_css',
							'name' => esc_html__( 'Inline CSS', 'easy-table-of-contents' ),
							'desc' => esc_html__( "Improve your  website performance by inlining your CSS.", 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'heading_levels' => array(
							'id' => 'heading_levels',
							'name' => esc_html__( 'Headings:', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.', 'easy-table-of-contents' ),
							'type' => 'checkboxgroup',
							'options' => array(
								'1' => esc_html__( 'Heading 1 (h1)', 'easy-table-of-contents' ),
								'2' => esc_html__( 'Heading 2 (h2)', 'easy-table-of-contents' ),
								'3' => esc_html__( 'Heading 3 (h3)', 'easy-table-of-contents' ),
								'4' => esc_html__( 'Heading 4 (h4)', 'easy-table-of-contents' ),
								'5' => esc_html__( 'Heading 5 (h5)', 'easy-table-of-contents' ),
								'6' => esc_html__( 'Heading 6 (h6)', 'easy-table-of-contents' ),
							),
							'default' => array( '1', '2', '3', '4', '5', '6' ),
						),
						//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Reason : This is not a query
						'exclude' => array(
							'id' => 'exclude',
							'name' => esc_html__( 'Exclude Headings', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Specify headings to be excluded from appearing in the table of contents. Separate multiple headings with a pipe', 'easy-table-of-contents').'<code>|</code>.'.esc_html__(' Use an asterisk ', 'easy-table-of-contents').'<code>*</code> '.esc_html__('as a wildcard to match other text.', 'easy-table-of-contents' ),
							'type' => 'text',
							'size' => 'large',
							'default' => '',
						),
						'exclude_desc' => array(
							'id' => 'exclude_desc',
							'name' => '',
							'desc' => '<p><strong>' . esc_html__('Examples:', 'easy-table-of-contents') . '</strong></p><ul><li><code>'. esc_html__('Fruit*', 'easy-table-of-contents').'</code> '.esc_html__('Ignore headings starting with "Fruit"', 'easy-table-of-contents').'.</li><li><code>'.esc_html__('*Fruit Diet*', 'easy-table-of-contents').'</code> '.esc_html__('Ignore headings with "Fruit Diet" somewhere in the heading.', 'easy-table-of-contents').'</li><li><code>'.esc_html__('Apple Tree|Oranges|Yellow Bananas', 'easy-table-of-contents').'</code> '.esc_html__('Ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas".', 'easy-table-of-contents').'</li></ul><p><strong>'.esc_html__('Note:', 'easy-table-of-contents').'</strong> '.esc_html__('This is not case sensitive', 'easy-table-of-contents').'</p>'
							      		,
							'type' => 'descriptive_text',
						),
						'schema_sitenav_checkbox' => array(
							'id' => 'schema_sitenav_checkbox',
							'name' => esc_html__( 'SiteNavigation Schema', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Add SiteNavigation Schema for displayed table of contents', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'smooth_scroll_offset' => array(
							'id' => 'smooth_scroll_offset',
							'name' => esc_html__( 'Smooth Scroll Offset', 'easy-table-of-contents' ),
							'desc' => 'px<br/>' . esc_html__( 'If you have a consistent menu across the top of your site, you can adjust the top offset to stop the headings from appearing underneath the top menu. A setting of 30 accommodates the WordPress admin bar. This setting only has an effect after you have enabled Smooth Scroll option.', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 30
						),
						'mobile_smooth_scroll_offset' => array(
							'id' => 'mobile_smooth_scroll_offset',
							'name' => esc_html__( 'Mobile Smooth Scroll Offset', 'easy-table-of-contents' ),
							'desc' => 'px<br/>' . esc_html__( 'This provides the same function as the Smooth Scroll Offset option above but applied when the user is visiting your site on a mobile device.', 'easy-table-of-contents' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 0
						),
						'restrict_path' => array(
							'id' => 'restrict_path',
							'name' => esc_html__( 'Limit Path', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'Restrict generation of the table of contents to pages that match the required path. This path is from the root of your site and always begins with a forward slash.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . esc_html__( 'Eg: /wiki/, /corporate/annual-reports/', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
						),
						'restrict_url_text' => array(
							'id' => 'restrict_url_text',
							'name' => esc_html__( 'Exclude By Matching Url/String', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'Add the url of the pages that you do not want to show table of contents on. Any part or match of the url, will restrict table of contents from loading on those pages. Please add the urls in the new lines by clicking on "enter".', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . esc_html__( 'Note: This setting will override above Limit Path option, if the limit path has been set.', 'easy-table-of-contents' ) . '</span>',
							'type' => 'textarea',
							'placeholder' => 'wp
text
/featured/',
						),
						'fragment_prefix' => array(
							'id' => 'fragment_prefix',
							'name' => esc_html__( 'Default Anchor Prefix', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'Anchor targets are restricted to alphanumeric characters as per HTML specification (see readme for more detail). The default anchor prefix will be used when no characters qualify. When left blank, a number will be used instead.', 'easy-table-of-contents' ) .
							          '<br/>' . esc_html__( 'This option normally applies to content written in character sets other than ASCII.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . esc_html__( 'Eg: i, toc_index, index, _', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
							'default' => 'i',
						),
						'all_fragment_prefix' => array(
							'id' => 'all_fragment_prefix',
							'name' => esc_html__( 'Default Anchor Prefix For All', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Apply default anchor prefix option to all anchors whether characters qualify or not.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'widget_affix_selector' => array(
							'id' => 'widget_affix_selector',
							'name' => esc_html__( 'Widget Affix Selector', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'To enable the option to affix or pin the Table of Contents widget enter the theme\'s sidebar class or id.', 'easy-table-of-contents' ) .
							          '<br/>' . esc_html__( 'Since every theme is different, this can not be determined automatically. If you are unsure how to find the sidebar\'s class or id, please ask the theme\'s support persons.', 'easy-table-of-contents' ) .
							          '<br/><span class="description">' . esc_html__( 'Eg: .widget-area or #sidebar', 'easy-table-of-contents' ) . '</span>',
							'type' => 'text',
							'default' => '',
						),
						'add_request_uri' => array(
							'id' => 'add_request_uri',
							'name' => esc_html__( 'Add Request URI', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Add request URI before anchor link. ', 'easy-table-of-contents' ) . esc_html__( 'Eg: href="/post/id#xxxx"', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'remove_special_chars_from_title' => array(
							'id' => 'remove_special_chars_from_title',
							'name' => esc_html__( 'Remove \':\' from TOC Title', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'show_title_in_toc' => array(
							'id' => 'show_title_in_toc',
							'name' => esc_html__( 'Show Page title in TOC', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'It will add page title to the list in TOC', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'disable_in_restapi' => array(
							'id' => 'disable_in_restapi',
							'name' => esc_html__( 'Disable TOC in RestAPI', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'It excludes TOC from Rest API Content.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'generate_toc_link_ids' => array(
							'id' => 'generate_toc_link_ids',
							'name' => esc_html__( 'Generate TOC link ids', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Enable This option when the TOC shortcode is used inside custom template, sidebar or when manually added do_shortcode("[ez-toc]") function in php files', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'enable_memory_fix' => array(
							'id' => 'enable_memory_fix',
							'name' => esc_html__( 'Fix Out of Memory / 500 Error', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'To solve memory / 500 error on the page where toc shortcode is added through pagebuilder such as DIVI.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'prsrv_line_brk' => array(
							'id' => 'prsrv_line_brk',
							'name' => esc_html__( 'Preserve Line Breaks', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Keeps line break of headings while generating toc.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'ajax_load_more' => array(
							'id' => 'ajax_load_more',
							'name' => esc_html__( 'Ajax Load More', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Makes toggle (js method) work for Infinite Scroll – Ajax Loaded contents/posts.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'no_heading_text' => array(
							'id' => 'no_heading_text',
							'name' => esc_html__( 'Display no heading text', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Display text when heading not available.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'no_heading_text_value' => array(
							'id' => 'no_heading_text_value',
							'name' => esc_html__( 'No heading text value', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'This text will display when no heading found on page/post', 'easy-table-of-contents' ),
							'type' => 'text',
							'default' => 'No heading found',
							'class'=>'js_v'
						),
					)
				),
                'shortcode' => apply_filters(
                    'Copy shortcode  ',
                    array(
                        'shortcode-first-paragraph'      => array(
                            'id'   => 'shortcode-first-paragraph',
                            'name' => esc_html__( 'Manual Adding the shortcode', 'easy-table-of-contents' ),
                            'desc' => sprintf(/* translators: %s: URL to the documentation */
                            		wp_kses_post( 'You can use the following shortcode to `Easy Table of Contents` display in your particular post or page. <a target="_blank" href="%s">Learn More</a><br/><input type="text" id="ez-toc-clipboard-apply" value="[ez-toc]" disabled />&nbsp;<span class="ez-toc-tooltip"><button type="button" class="button" onclick="ez_toc_clipboard(\'ez-toc-clipboard-apply\', \'ez-toc-myTooltip\', this, event)" onmouseout="ez_toc_outFunc(\'ez-toc-myTooltip\', this, event)"><span class="ez-toc-tooltiptext ez-toc-myTooltip">Copy to clipboard</span>Copy shortcode</button></span>', 'easy-table-of-contents' ), 'https://tocwp.com/docs/knowledge-base/how-to-add-toc-with-shortcode/'
                            		),
                            'type' => 'paragraph',
                            'allowedHtml' => array(
								'br' => array(),
								'a' => array(
								    'target' => array(),
								    'href' => array()
								),
								'input' => array(
					               'type' => true,
					               'id' => true,
					               'value' => true,
					               'readonly' => true,
					               'disabled' => true,
					               'class' => true,
					           ),
					           '&nbsp;' => array(),
					           'span' => array(
					               'class' => true,
					               'id' => true,
					           ),
					           'button' => array(
					               'type' => true,
					               'onclick' => true,
					               'onmouseout' => true,
					               'id' => true,
					               'class' => true,
					           ),
				           ),
                        ),
                        'shortcode-second-paragraph'      => array(
                            'id'   => 'shortcode-second-paragraph',
                            'name' => esc_html__( 'Supported Attributes', 'easy-table-of-contents' ),
                            'desc' => sprintf(
                            			wp_kses_post( '<p><code>[header_label="Title"]</code> – title for the table of contents</p><p><code>[display_header_label="no"]</code> – no title for the table of contents</p><p><code>[toggle_view="no"]</code> – no toggle for the table of contents</p><p><code>[initial_view="hide"]</code> – initially hide the table of contents</p><p><code>[initial_view="show"]</code> – initially show the table of contents</p><p><code>[display_counter="no"]</code> – no counter for the table of contents</p><p><code>[post_types="post,page"]</code> – post types seperated by ,(comma)</p><p><code>[post_in="1,2"]</code> – ID’s of the posts|pages seperated by ,(comma)</p><p><code>[device_target="desktop"]</code> – mobile or desktop device support for the table of contents</p><p><code>[view_more="5"]</code> – 5, is the number of headings loads on first view, before user interaction (PRO)</p>', 'easy-table-of-contents' )
                            		),
                            'type' => 'descriptive_text',
                        ),
                        'shortcode-third-paragraph'      => array(
                            'id'   => 'shortcode-third-paragraph',
                            'name' => esc_html__( 'Manual Adding widget shortcode', 'easy-table-of-contents' ),
                            'desc' => sprintf(/* translators: %s: URL to the documentation */
										wp_kses_post( 'You can use the following widget shortcode to display `Easy Table of Contents` in your sidebar. <a target="_blank" href="%s">Learn More</a><br/><input type="text" id="ez-toc-clipboard-apply" value="[ez-toc-widget-sticky]" disabled />&nbsp;<span class="ez-toc-tooltip"><button type="button" class="button" onclick="ez_toc_clipboard(\'ez-toc-clipboard-apply\', \'ez-toc-myTooltip\', this, event)" onmouseout="ez_toc_outFunc(\'ez-toc-myTooltip\', this, event)"><span class="ez-toc-tooltiptext ez-toc-myTooltip">Copy to clipboard</span>Copy shortcode</button></span>', 'easy-table-of-contents' ), 'https://tocwp.com/docs/knowledge-base/how-to-add-toc-with-shortcode/'
                            		),
                            'type' => 'paragraph',
                            'allowedHtml' => array(
								'br' => array(),
								'a' => array(
								    'target' => array(),
								    'href' => array()
								),
								'input' => array(
					               'type' => true,
					               'id' => true,
					               'value' => true,
					               'readonly' => true,
					               'disabled' => true,
					               'class' => true,
					           ),
					           '&nbsp;' => array(),
					           'span' => array(
					               'class' => true,
					               'id' => true,
					           ),
					           'button' => array(
					               'type' => true,
					               'onclick' => true,
					               'onmouseout' => true,
					               'id' => true,
					               'class' => true,
					           ),
				           ),
                        ),
                        'shortcode-fourth-paragraph'      => array(
                            'id'   => 'shortcode-fourth-paragraph',
                            'name' => esc_html__( 'Auto Insert', 'easy-table-of-contents' ),
                            'desc' => esc_html__( 'You can add `Easy Table of Contents` without using shortcode from `Auto Insert` option in General Setting so then there is no need to add shortcode while post, page or any post type editing.', 'easy-table-of-contents' ),
                            'type' => 'paragraph',
                        ),
                    )
                ),
				'sticky' => apply_filters(
                    'ez_toc_settings_sticky',
                    array(
						'sticky-toggle'                   => array(
							'id'      => 'sticky-toggle',
							'name'    => esc_html__( 'Sticky TOC', 'easy-table-of-contents' ),
							'desc' => sprintf(/* translators: %s: URL to the documentation */
							      		esc_html__( 'Table of contents as Sticky on your site.', 'easy-table-of-contents' ).' <a target="_blank" href="%s">'.esc_html__( 'Learn More.', 'easy-table-of-contents' ).'</a>', 'https://tocwp.com/docs/knowledge-base/how-to-use-fixed-sticky-toc/'
							      		),
							'type'    => 'checkbox',
							'default' => false,
						),
						'sticky-post-types' => array(
							'id' => 'sticky-post-types',
							'name' => esc_html__( 'Enable Support', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Select the post types which will have the Sticky TOC inserted.', 'easy-table-of-contents' ) .
							          '<br><span class="description">' . esc_html__( 'NOTE: The Sticky TOC will only be inserted on post types for which it has been enabled.', 'easy-table-of-contents' ) . '<span>',
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array('post'=>'Post','page'=>'Page'),
						),
						'sticky_include_homepage' => array(
							'id' => 'sticky_include_homepage',
							'name' => esc_html__( 'Homepage', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the Sticky TOC for qualifying items on the homepage.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						  ),
						  'sticky_include_category' => array(
							'id' => 'sticky_include_category',
							'name' => esc_html__( 'Category', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the Sticky TOC for description on the category pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						  ),
						  'sticky_include_tag' => array(
							'id' => 'sticky_include_tag',
							'name' => esc_html__( 'Tag', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the Sticky TOC for description on the tag pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						  ),
						  'sticky_include_product_category' => array(
							'id' => 'sticky_include_product_category',
							'name' => esc_html__( 'Product Category', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the Sticky TOC for description on the product category pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => true,
						  ),
						  'sticky_include_custom_tax' => array(
							'id' => 'sticky_include_custom_tax',
							'name' => esc_html__( 'Custom Taxonomy', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'Show the Sticky TOC for description on the custom taxonomy pages.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						  ),
						  'sticky_device_target' => array(
  							'id' => 'sticky_device_target',
  							'name' => esc_html__( 'Device Target', 'easy-table-of-contents' ),
  							'type' => 'select',
  							'options' => array(
  								'' => esc_html__( 'Select', 'easy-table-of-contents' ),
  								'mobile' => esc_html__( 'Mobile', 'easy-table-of-contents' ),
  								'desktop' => esc_html__( 'Desktop', 'easy-table-of-contents' ),
  								 
  							),
  							'default' => 'Select',
  						),
						'sticky-toggle-position'                   => array(
							'id'      => 'sticky-toggle-position',
							'name'    => esc_html__( 'Position', 'easy-table-of-contents' ),
							'type' => 'radio',
							'options' => array(
								'left' => _x( 'Left', 'Position', 'easy-table-of-contents' ),
								'right' => _x( 'Right', 'Position', 'easy-table-of-contents' ),
							),
							'default' => 'left',
						),

						'sticky-toggle-alignment'                   => array(
							'id'      => 'sticky-toggle-alignment',
							'name'    => esc_html__( 'Alignment', 'easy-table-of-contents' ),
							'type' => 'radio',
							'options' => array(
								'top' => _x( 'Top', 'Alignment', 'easy-table-of-contents' ),
								'middle' => esc_html__( 'Middle', 'easy-table-of-contents' ),
								'bottom' => _x( 'Bottom', 'Alignment', 'easy-table-of-contents' ),
							),
							'default' => 'top',
						),
						'sticky-toggle-open' => array(
							'id'      => 'sticky-toggle-open',
							'name'    => esc_html__( 'TOC open on load', 'easy-table-of-contents' ),
							'type'    => 'checkbox',
							'default' => false,
						),
						'sticky-toggle-width'             => array(
							'id'      => 'sticky-toggle-width',
							'name'    => esc_html__( 'Width', 'easy-table-of-contents' ),
							'type'    => 'select',
							'options' => array(
								'auto'   => esc_html__( 'Auto', 'easy-table-of-contents' ),
								'custom' => esc_html__( 'User Defined', 'easy-table-of-contents' ),
							),
							'default' => 'auto',
						),
						'sticky-toggle-width-custom'      => array(
							'id'          => 'sticky-toggle-width-custom',
							'name'        => esc_html__( 'Custom Width', 'easy-table-of-contents' ),
							'type'        => 'custom_width',
							'default'     => 350,
						),
						'sticky-toggle-height'            => array(
							'id'      => 'sticky-toggle-height',
							'name'    => esc_html__( 'Height', 'easy-table-of-contents' ),
							'type'    => 'select',
							'options' => array(
								'auto'   => esc_html__( 'Auto', 'easy-table-of-contents' ),
								'custom' => esc_html__( 'User Defined', 'easy-table-of-contents' ),
							),
							'default' => 'auto',
						),
						'sticky-toggle-height-custom'     => array(
							'id'          => 'sticky-toggle-height-custom',
							'name'        => esc_html__( 'Custom Height', 'easy-table-of-contents' ),
							'type'        => 'custom_width',
							'default'     => 800,
						),
						'sticky-toggle-open-button-text'     => array(
							'id'          => 'sticky-toggle-open-button-text',
							'name'        => esc_html__( 'Open Button Text', 'easy-table-of-contents' ),
							'type'        => 'text',
							'default'     => false,
							'placeholder' => esc_html__( 'Enter sticky toggle open button text here..', 'easy-table-of-contents' )
						),
						'sticky-toggle-close-on-mobile'     => array(
							'id'          => 'sticky-toggle-close-on-mobile',
							'name'        => esc_html__( 'Click TOC Close on Mobile', 'easy-table-of-contents' ),
							'type'        => 'checkbox',
							'default'     => false,
							'placeholder' => esc_html__( 'Close Sticky Toggle on click over headings in mobile devices', 'easy-table-of-contents' )
						),
						'sticky-toggle-close-on-desktop'     => array(
							'id'          => 'sticky-toggle-close-on-desktop',
							'name'        => esc_html__( 'Click TOC Close on desktop', 'easy-table-of-contents' ),
							'type'        => 'checkbox',
							'default'     => false,
							'placeholder' => esc_html__( 'Close Sticky Toggle on click over headings in desktop', 'easy-table-of-contents' )
						),
						'sticky_restrict_url_text' => array(
							'id' => 'sticky_restrict_url_text',
							'name' => esc_html__( 'Exclude By Matching Url/String', 'easy-table-of-contents' ),
							'desc' => '<br/>' . esc_html__( 'Add the url of the pages that you do not want to show table of contents on. Any part or match of the url, will restrict table of contents from loading on those pages. Please add the urls in the new lines by clicking on "enter".', 'easy-table-of-contents' ),
							'type' => 'textarea',
							'placeholder' => 'wp
text
/featured/'),
                    )
                ),
                'compatibility' => apply_filters(
                    'ez_toc_settings_compatibility',
                    array(
                        'mediavine-create' => array(
							'id' => 'mediavine-create',
							'name' => esc_html__( 'Create by Mediavine', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'It includes headings created by mediavine recipe card custom post.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'goodlayers-core' => array(
							'id' => 'goodlayers-core',
							'name' => esc_html__( 'Goodlayers Core Builder', 'easy-table-of-contents' ),
							'desc' => esc_html__( 'It includes Goodlayers Builder content to TOC.', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
						'molongui-authorship' => array(
							'id' => 'molongui-authorship',
							'name' => esc_html__( 'Molongui Authorship', 'easy-table-of-contents' ),
							'type' => 'checkbox',
							'default' => false,
						),
                    )
                ),
				'prosettings' => apply_filters(
					'ez_toc_settings_prosettings', array()
				),
			);

			return apply_filters( 'ez_toc_registered_settings', $options );
		}

        /**
         * getCounterListBasic Method
         * @since 2.0.33
         * @scope protected
         * @static
         * @return array
        */
        protected static function getCounterList() {
            return array_merge( self::getCounterListBasic(), self::getCounterListDecimal(), self::getCounterList_i18n() );
        }

		/**
		 * getCounterPositionList function
		 *
		 * @since 2.0.51
		 * @static
		 * @access protected
		 * @return array
		 */
		protected static function getCounterPositionList() 
		{
			return array(
				'inside' => esc_html__( 'Inside', 'easy-table-of-contents' ),
				'outside' => esc_html__( 'Outside', 'easy-table-of-contents' ),
			);
		}

        /**
         * getCounterListBasic Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterListBasic() {
            return array(
                'none' => esc_html__( 'None', 'easy-table-of-contents' ),
                'disc' => esc_html__( 'Disc', 'easy-table-of-contents' ),
                'circle' => esc_html__( 'Circle', 'easy-table-of-contents' ),
                'square' => esc_html__( 'Square', 'easy-table-of-contents' ),
                '- ' => esc_html__( 'Hyphen', 'easy-table-of-contents' ),
                'cjk-earthly-branch' => esc_html__( 'Earthly Branch', 'easy-table-of-contents' ),
                'disclosure-open' => esc_html__( 'Disclosure Open', 'easy-table-of-contents' ),
                'disclosure-closed' => esc_html__( 'Disclosure Closed', 'easy-table-of-contents' ),
                'numeric' => esc_html__( 'Numeric', 'easy-table-of-contents' ),
            );
        }

        /**
         * getCounterListDecimal Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterListDecimal() {
            return array(
				'decimal' => esc_html__( 'Decimal (default)', 'easy-table-of-contents' ),
                'decimal-leading-zero' => esc_html__( 'Decimal Leading Zero', 'easy-table-of-contents' ),
                'cjk-decimal' => esc_html__( 'CJK Decimal', 'easy-table-of-contents' ),
            );
        }

        /**
         * getCounterList_i18n Method
         * @since 2.0.33
         * @scope public
         * @static
         * @return array
        */
        public static function getCounterList_i18n() {
            return array(
                'upper-roman' => esc_html__( 'Upper Roman', 'easy-table-of-contents' ),
                'lower-roman' => esc_html__( 'Lower Roman', 'easy-table-of-contents' ),
                'lower-greek' => esc_html__( 'Lower Greek', 'easy-table-of-contents' ),
                'upper-alpha' => esc_html__( 'Upper Alpha/Latin', 'easy-table-of-contents' ),
                'lower-alpha' => esc_html__( 'Lower Alpha/Latin', 'easy-table-of-contents' ),
                'armenian' => esc_html__( 'Armenian', 'easy-table-of-contents' ),
                'lower-armenian' => esc_html__( 'Lower Armenian', 'easy-table-of-contents' ),
                'arabic-indic' => esc_html__( 'Arabic', 'easy-table-of-contents' ),
                'bengali' => esc_html__( 'Bengali', 'easy-table-of-contents' ),
                'cambodian' => esc_html__( 'Cambodian/Khmer', 'easy-table-of-contents' ),
                'cjk-heavenly-stem' => esc_html__( 'Heavenly Stem', 'easy-table-of-contents' ),
                'cjk-ideographic' => esc_html__( 'CJK Ideographic/trad-chinese-informal', 'easy-table-of-contents' ),
                'devanagari' => esc_html__( 'Hindi (Devanagari)', 'easy-table-of-contents' ),
                'ethiopic-numeric' => esc_html__( 'Ethiopic', 'easy-table-of-contents' ),
                'georgian' => esc_html__( 'Georgian', 'easy-table-of-contents' ),
                'gujarati' => esc_html__( 'Gujarati', 'easy-table-of-contents' ),
                'gurmukhi' => esc_html__( 'Gurmukhi', 'easy-table-of-contents' ),
                'hebrew' => esc_html__( 'Hebrew', 'easy-table-of-contents' ),
                'hiragana' => esc_html__( 'Hiragana', 'easy-table-of-contents' ),
                'hiragana-iroha' => esc_html__( 'Hiragana-Iroha', 'easy-table-of-contents' ),
                'japanese-formal' => esc_html__( 'Japanese Formal', 'easy-table-of-contents' ),
                'japanese-informal' => esc_html__( 'Japanese Informal', 'easy-table-of-contents' ),
                'kannada' => esc_html__( 'Kannada', 'easy-table-of-contents' ),
                'katakana' => esc_html__( 'Katakana', 'easy-table-of-contents' ),
                'katakana-iroha' => esc_html__( 'Katakana-Iroha', 'easy-table-of-contents' ),
                'korean-hangul-formal' => esc_html__( 'Korean Hangul Formal', 'easy-table-of-contents' ),
                'korean-hanja-formal' => esc_html__( 'Korean Hanja Formal', 'easy-table-of-contents' ),
                'korean-hanja-informal' => esc_html__( 'Korean Hanja Informal', 'easy-table-of-contents' ),
                'lao' => esc_html__( 'Laotian', 'easy-table-of-contents' ),
                'malayalam' => esc_html__( 'Malayalam', 'easy-table-of-contents' ),
                'mongolian' => esc_html__( 'Mongolian', 'easy-table-of-contents' ),
                'myanmar' => esc_html__( 'Myanmar', 'easy-table-of-contents' ),
                'oriya' => esc_html__( 'Oriya', 'easy-table-of-contents' ),
                'persian' => esc_html__( 'Persian', 'easy-table-of-contents' ),
                'simp-chinese-formal' => esc_html__( 'Simplified Chinese Formal', 'easy-table-of-contents' ),
                'simp-chinese-informal' => esc_html__( 'Simplified Chinese Informal', 'easy-table-of-contents' ),
                'tamil' => esc_html__( 'Tamil', 'easy-table-of-contents' ),
                'telugu' => esc_html__( 'Telugu', 'easy-table-of-contents' ),
                'thai' => esc_html__( 'Thai', 'easy-table-of-contents' ),
                'tibetan' => esc_html__( 'Tibetan', 'easy-table-of-contents' ),
                'trad-chinese-formal' => esc_html__( 'Traditional Chinese Formal', 'easy-table-of-contents' ),
                'trad-chinese-informal' => esc_html__( 'Traditional Chinese Informal', 'easy-table-of-contents' ),
                'hangul' => esc_html__( 'Hangul', 'easy-table-of-contents' ),
                'hangul-consonant' => esc_html__( 'Hangul Consonant', 'easy-table-of-contents' ),
                'urdu' => esc_html__( 'Urdu', 'easy-table-of-contents' ),
            );
        }

		/**
		 * The default values for the registered settings and options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getDefaults() {

			$defaults = array(
				'fragment_prefix'                    => 'i',
				'position'                           => 'before',
				'start'                              => 2,
				'show_heading_text'                  => true,
				'heading_text'                       => 'Table of Contents',
				'heading_text_tag'                   => 'p',
				'visibility_on_header_text'			 => false,	
				'enabled_post_types'                 => array( 'post','page' ),
				'auto_insert_post_types'             => array( 'post','page' ),
				'show_hierarchy'                     => true,
				'counter'                            => 'decimal',
				'counter-position'                   => 'inside',
				'smooth_scroll'                      => true,
				'smooth_scroll_offset'               => 30,
				'mobile_smooth_scroll_offset'        => 0,
				'visibility'                         => true,
				'toc_loading'                        => 'js',
				'avoid_anch_jump'                    => false,
				'remove_special_chars_from_title'    => false,
				'visibility_hide_by_default'         => false,
				'width'                              => 'auto',
				'width_custom'                       => 275,
				'width_custom_units'                 => 'px',
				'wrapping'                           => 'none',
				'toc_wrapping'                       => false,
				'headings-padding'                   => false,
				'headings-padding-top'               => 0,
				'headings-padding-bottom'            => 0,
				'headings-padding-left'              => 0,
				'headings-padding-right'             => 0,
				'title_font_size'                    => 120,
				'title_font_size_units'              => '%',
				'title_font_weight'                  => 500,
				'font_size'                          => 95,
				'child_font_size'					 => 90,
				'font_size_units'                    => '%',
				'child_font_size_units'              => '%',
				'theme'                              => 'grey',
				'custom_background_colour'           => '#fff',
				'custom_border_colour'               => '#ddd',
				'custom_title_colour'                => '#999',
				'custom_link_colour'                 => '#428bca',
				'custom_link_hover_colour'           => '#2a6496',
				'custom_link_visited_colour'         => '#428bca',
				'lowercase'                          => false,
				'hyphenate'                          => false,
				'include_homepage'                   => false,
				'include_category'                   => false,
				'include_tag'                        => false,
				'include_custom_tax'                 => false,
				'exclude_css'                        => false,
				'inline_css'                         => false,
				//phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Reason : This is not a query
				'exclude'                            => '',
				'heading_levels'                     => array( '1', '2', '3', '4', '5', '6' ),
				'restrict_path'                      => '',
				'css_container_class'                => '',
				'widget_affix_selector'              => '',
				'heading-text-direction'              => 'ltr',
				'toc-run-on-amp-pages'              => 1,
				'sticky-toggle-position'              => 'left',
				'sticky-toggle-alignment'             => 'top',
				'add_request_uri'                     => false,
				'mediavine-create'                    => 0,
				'molongui-authorship'                 => false,
				'custom_para_number'                  => 1,
				'blockqoute_checkbox'                  => false,
				'disable_in_restapi'                  => false,
				'show_title_in_toc'				      => false,	
				'sticky-post-types'					  => array('post','page'),
				'sticky_include_homepage' 			  => false,
				'sticky_include_category' 			  => true,
				'sticky_include_tag' 		     	  => false,
				'sticky_include_product_category'     => true,
				'sticky_include_custom_tax'           => false,
				'generate_toc_link_ids'               => false,
				'enable_memory_fix'					  => false,
			);

			return apply_filters( 'ez_toc_get_default_options', $defaults );
		}

		/**
		 * Get the default options array.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		private static function getOptions() {

			$defaults = self::getDefaults();
			$options  = get_option( 'ez-toc-settings', $defaults );

			return apply_filters( 'ez_toc_get_options', $options );
		}

		/**
		 * Get option value by key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string     $key
		 * @param bool|false $default
		 *
		 * @return mixed
		 */
		public static function get( $key, $default = false ) {

			$options = (array) self::getOptions();

			$value = array_key_exists( $key, $options ) ? $options[ $key ] : $default;
			$value = apply_filters( 'ez_toc_get_option', $value, $key, $default );

			return apply_filters( 'ez_toc_get_option_' . $key, $value, $key, $default );
		}

		/**
		 * Set an option value by key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string     $key
		 * @param bool|false $value
		 *
		 * @return bool
		 */
		public static function set( $key, $value = false ) {

			if ( empty( $value ) ) {

				$remove_option = self::delete( $key );

				return $remove_option;
			}

			$options = self::getOptions();

			$options[ $key ] = apply_filters( 'ez_toc_update_option', $value, $key );

			return update_option( 'ez-toc-settings', $options );
		}

		/**
		 * Delete an option from the options table by option key name.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public static function delete( $key ) {

			// First let's grab the current settings
			$options = get_option( 'ez-toc-settings' );

			// Next let's try to update the value
			if ( array_key_exists( $key, $options ) ) {

				unset( $options[ $key ] );
			}

			return update_option( 'ez-toc-settings', $options );
		}

		/**
		 * Sanitize a hex color from user input.
		 *
		 * Tries to convert $string into a valid hex colour.
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param string $string
		 * @param string $default
		 *
		 * @return mixed|string
		 */
		private static function hex_value( $string = '', $default = '#' ) {

			$return = $default;

			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );

				switch ( strlen( $return ) ) {
					case 3:    // do next
					case 6:
						$return = '#' . $return;
						break;

					default:
						if ( strlen( $return ) > 6 ) {
							$return = '#' . substr( $return, 0, 6 );
						}    // if > 6 chars, then take the first 6
						elseif ( strlen( $return ) > 3 && strlen( $return ) < 6 ) {
							$return = '#' . substr( $return, 0, 3 );
						}    // if between 3 and 6, then take first 3
						else {
							$return = $default;
						}                        // not valid, return $default
				}
			}

			return $return;
		}

		/**
		 * Get the registered post types minus excluded core types.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return array
		 */
		public static function getPostTypes() {

			$exclude    = apply_filters( 'ez_toc_exclude_post_types', array( 'attachment', 'revision', 'nav_menu_item', 'safecss' ) );
			$registered = get_post_types( array(), 'objects' );
			$types      = array();

			foreach ( $registered as $post ) {

				if ( in_array( $post->name, $exclude ) ) {

					continue;
				}

				$types[ $post->name ] = $post->label;
			}

			return $types;
		}

		/**
		 * Missing Callback
		 *
		 * If a settings field type callback is not callable, alert the user.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function missingCallback( $args ) {

			printf(/* translators: %s: Setting ID */
				esc_html__( 'The callback function used for the <strong>%s</strong> setting is missing.', 'easy-table-of-contents' ),
				esc_attr($args['id'])
			);
		}

		/**
		 * HR Callback
		 *
		 * Renders hr html tag.
		 *
		 * @access public
		 *
		 * @param array $args Arguments passed by the setting
		 *
		 * @since  1.0
		 * @static
		 *
		 */
		public static function hr( array $args ) {
			$class = '';
			if ( isset( $args['class'] ) && true === $args['class'] ) {
				$class = self::get( $args['class'], $args['default'] );
			} ?>
            <hr class='<?php echo esc_attr($class);?>' />
		<?php	
		}

		/**
		 * Text Callback
		 *
		 * Renders text fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function text( $args, $value = null ) {
			$name_flag = true;
			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name_flag        = false;

			}
			$placeholder = '';
			if ( isset( $args['placeholder'] ) && ! empty( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$value = $value ? stripslashes($value) : '';
			?>
			<input type="text" class="<?php echo esc_attr($size) ?>-text" id="ez-toc-settings['<?php echo esc_attr($args['id'])?>']" <?php echo $name_flag ?' name="ez-toc-settings[' . esc_attr( $args['id'] ) . ']"':''; ?> value="<?php echo esc_attr( $value ) ?>" <?php echo esc_attr($readonly) ?> placeholder="<?php echo esc_attr($placeholder) ?>" />

			<?php if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) { ?>
				<label for="ez-toc-settings['<?php echo esc_attr( $args['id'] ) ?>']"> 
					<?php echo wp_kses_post($args['desc']) ?>
				</label>
			<?php }  

		}

		/**
		 * Textarea Callback.
		 *
		 * Renders a textarea.
		 *
		 * @access public
		 * @since  1.1
		 * @static
		 *
		 * @param array $args  Arguments passed by the setting
		 * @param null  $value
		 */
		public static function textarea( $args, $value = null ) {

			$name_flag = true;
			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name_flag        = false;

			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			?>

			<textarea rows="10" cols="50" class="<?php echo esc_attr( $size ); ?>-text" id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" <?php echo $name_flag ?' name="ez-toc-settings[' . esc_attr( $args['id'] ) . ']"':''; ?> <?php echo esc_attr( $readonly ); ?><?php echo isset( $args['placeholder'] ) && $args['placeholder'] != '' ? 'placeholder="'.esc_attr($args['placeholder']).'"' : ''; ?>><?php echo esc_textarea( $value ); ?></textarea>

			<?php if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) { ?>
				<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]">
					<?php echo wp_kses_post( $args['desc'] ); ?>
				</label>
			<?php }
		}

		/**
		 * Number Callback
		 *
		 * Renders number fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args        Arguments passed by the setting
		 */
		public static function number( $args ) {

			$value = self::get( $args['id'], $args['default'] );
			$name_flag = true;
			if ( isset( $args['faux'] ) && true === $args['faux'] ) {

				$args['readonly'] = true;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name_flag		  = false;

			} 

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === true ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$min = isset( $args['min'] ) && $args['min'] != '' ? 'min="'.$args['min'].'"' : '';
			?>
			<input type="number" class="<?php echo esc_attr( $size ); ?>-text" id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" <?php echo $name_flag ?' name="ez-toc-settings[' . esc_attr( $args['id'] ) . ']"':''; ?>  value="<?php echo esc_attr( stripslashes( $value ) ); ?>" <?php echo esc_attr( $readonly ); ?><?php echo esc_attr( $min ); ?> />
			<?php if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) { ?>
				<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]">
					<?php echo wp_kses_post( $args['desc'] ); ?>
				</label>
			<?php }
		}

		/**
		 * Checkbox Callback
		 *
		 * Renders checkboxes.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkbox( $args, $value = null ) {
			$is_faux = false;
			if ( is_null( $value ) ) {
				$value = self::get( $args['id'], $args['default'] );
			}
		
			if ( isset( $args['faux'] ) && true === $args['faux'] ) {
				$is_faux = true;
			}
			?>
			<input type="checkbox" id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" <?php echo (!$is_faux)?'name="ez-toc-settings[' . esc_attr($args['id']) . ']"':''?> value="1" <?php echo $value ? checked( 1, $value, false ) : ''; ?> />
		
			<?php if ( isset( $args['desc'] ) && strlen( $args['desc'] ) > 0 ) { ?>
				<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]">
					<?php echo wp_kses_post( $args['desc'] ); ?>
				</label>
			<?php }
		}
		

		/**
		 * Multicheck Callback
		 *
		 * Renders multiple checkboxes.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkboxgroup( $args, $value = null ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( ! empty( $args['options'] ) ) {

				foreach ( $args['options'] as $key => $option ):

					if ( in_array( $key, $value ) ) {

						$enabled = $option;

					} else {

						$enabled = null;
					}
					?>
					<input name="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo esc_attr( $key ); ?>]" 
						id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo esc_attr( $key ); ?>]" 
						type="checkbox" 
						value="<?php echo esc_attr( $key ); ?>" 
						<?php echo checked( $option, $enabled, false ); ?> 
					/>&nbsp;
					<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo esc_attr( $key ); ?>]">
						<?php echo esc_html( $option ); ?>
					</label><br/>

					<?php
				endforeach;

				if ( isset( $args['desc'] ) && strlen( $args['desc'] ) > 0 ) { ?>
					<p class="description">
						<?php echo wp_kses_post( $args['desc'] ); ?>
					</p>
				<?php } 
			}
		}

		/**
		 * Radio Callback
		 *
		 * Renders radio groups.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function radio( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			foreach ( $args['options'] as $key => $option ) {
				?>

				<input name="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" 
					id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo esc_attr( $key ); ?>]" 
					type="radio" 
					value="<?php echo esc_attr( $key ); ?>" 
					<?php echo checked( $key, $value, false ); ?> 
				/>&nbsp;

				<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>][<?php echo esc_attr( $key ); ?>]">
					<?php echo esc_html( $option ); ?>
				</label><br/>
			<?php	
			}

			if ( isset( $args['desc'] ) && strlen( $args['desc'] ) > 0 ) { ?>
				<p class="description">
					<?php echo wp_kses_post( $args['desc'] ); ?>
				</p>
			<?php } 
		}

		/**
		 * Select Callback
		 *
		 * Renders select fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function select( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}?>
			<select id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" 
					name="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" 
					<?php echo esc_attr( $chosen ); ?> 
					data-placeholder="<?php echo esc_attr( $placeholder ); ?>">
				<?php foreach ( $args['options'] as $option => $name ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php echo selected( $option, $value, false ); ?>>
						<?php echo esc_html( $name ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<?php if ( isset( $args['desc'] ) && strlen( $args['desc'] ) > 0 ) : ?>
				<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]">
					<?php echo wp_kses_post( $args['desc'] ); ?>
				</label>
			<?php endif; 

		}

		/**
		 * Select Drop Down Callback
		 *
		 * Renders select with option group fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function selectgroup( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}

			?>

				<select id="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" 
						name="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]" 
						<?php echo esc_attr( $chosen ); ?> 
						data-placeholder="<?php echo esc_attr( $placeholder ); ?>">
					<?php foreach ( $args['options'] as $group ) : ?>
						<optgroup label="<?php echo esc_attr( $group['name'] ); ?>">
							<?php foreach ( $group['options'] as $option => $name ) : ?>
								<option value="<?php echo esc_attr( $option ); ?>" <?php echo selected( $option, $value, false ); ?>>
									<?php echo esc_html( $name ); ?>
								</option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>

				<?php if ( isset( $args['desc'] ) && strlen( $args['desc'] ) > 0 ) : ?>
					<label for="ez-toc-settings[<?php echo esc_attr( $args['id'] ); ?>]">
						<?php echo wp_kses_post( $args['desc'] ); ?>
					</label>
				<?php endif; 
			
		}

		/**
		 * Header Callback
		 *
		 * Renders the header.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function header( $args ) {

            if( !isset( $args['without_hr'] ) || ( isset( $args['without_hr'] ) && $args['without_hr']) )
			    echo '<hr/>';

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				echo '<p>' . wp_kses_post( $args['desc'] ) . '</p>';
			}
		}

        /**
		 * Paragraph Callback
		 *
		 * Renders the paragraph.
		 *
		 * @access public
		 * @since  2.0.33
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
         * @return void
		 */
		public static function paragraph( $args ) {

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				$allowed_html = [];
				if( is_array( $args['allowedHtml'] ) && count( $args['allowedHtml'] ) > 0 ) {
					$allowed_html = $args['allowedHtml'];
				}
				echo '<p>' . wp_kses( $args['desc'] , $allowed_html ) . '</p>';
			}
		}

		/**
		 * Descriptive text callback.
		 *
		 * Renders descriptive text onto the settings field.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function descriptive_text( $args ) {

			echo wp_kses_post( $args['desc'] );
		}

		/**
		 * Color picker Callback
		 *
		 * Renders color picker fields.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function color( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			$default = isset( $args['default'] ) ? $args['default'] : '';

			echo '<input type="text" class="ez-toc-color-picker" id="ez-toc-settings[' . esc_attr($args['id']) . ']" name="ez-toc-settings[' . esc_attr($args['id']) . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . esc_attr($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
			}

		}

		/**
		 * Custom table of contents width.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
		public static function custom_width( $args ) {

			self::number(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
						'vh' => 'vh',
					),
					'default' => 'px',
				)
			);

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . esc_attr($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
			}
		}

		/**
		 * Custom font size callback.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
		public static function font_size( $args ) {

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'pt' => 'pt',
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => '%',
				)
			);

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . esc_attr($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
			}
		}
/**
		 * Custom font size callback.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param array $args
		 */
public static function child_font_size( $args ) {

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'pt' => 'pt',
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => '%',
				)
			);

			if ( isset( $args['desc'] ) && 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . esc_attr($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
			}
		}

		/**
         * reset_options_to_default Method
         * to reset options
         * @since 2.0.37
         * @return bool|string
        */
        public static function eztoc_reset_options_to_default() {
            if( !wp_verify_nonce( sanitize_text_field( $_POST['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) )
            {
                return esc_html__('Security Alert: nonce not verified!', 'easy-table-of-contents' );
            }

			if ( !current_user_can( 'manage_options' ) ) {
				return esc_html__('Security Alert: Unauthorized Access!', 'easy-table-of-contents' );
			}
            delete_option('ez-toc-settings');
            return add_option( 'ez-toc-settings', self::getDefaults() );
        }
	}

	add_action( 'admin_init', array( 'ezTOC_Option', 'register' ) );

	add_action( 'wp_ajax_eztoc_reset_options_to_default', array( 'ezTOC_Option', 'eztoc_reset_options_to_default' ) );

}

add_filter("ez_toc_settings_sticky", "ez_toc_settings_sticky_func_nonpro");
function ez_toc_settings_sticky_func_nonpro($settings)
{
	if(function_exists('is_plugin_active') && !is_plugin_active('easy-table-of-contents-pro/easy-table-of-contents-pro.php')){
			$sticky_pro_settings = array(
			'upgrade-paragraph'      => array(
				'id'   => 'upgrade-paragraph',
				'name' => '<h4 class="ez-toc-upgrade-paragraph">'.esc_html__( 'To unlock Sticky Theme Design options', 'easy-table-of-contents' ).'<a href="https://tocwp.com/pricing/" target="_blank"> <u>'.esc_html__( 'Upgrade to  Pro', 'easy-table-of-contents' ).'</u></a></h4><br>',
				'type' => 'header',
			)
		);
		return array_merge($settings, $sticky_pro_settings);
	}
	return $settings;
	
}