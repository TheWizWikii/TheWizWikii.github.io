<?php
// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) )
    exit;

if ( ! class_exists ( 'ezTOC_WidgetSticky' ) )
{

    /**
     * Class ezTOC_WidgetSticky
     */
    class ezTOC_WidgetSticky extends WP_Widget
    {

        /**
         * Setup and register the table of contents widget.
         *
         * @access public
         * @since 2.0.41
         */
        public function __construct ()
        {

            $options = array(
                'classname' => 'ez-toc-widget-sticky',
                'description' => __ ( 'Display the table of contents.', 'easy-table-of-contents' )
            );

            parent::__construct (
                    'ez_toc_widget_sticky',
                    __ ( 'Sticky Sidebar Table of Contents', 'easy-table-of-contents' ),
                    $options
            );

            add_action ( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
            add_action ( 'admin_footer-widgets.php', array( $this, 'printScripts' ), 9999 );
        }

        /**
         * Callback which registers the widget with the Widget API.
         *
         * @access public
         * @since 2.0.41
         * @static
         *
         * @return void
         */
        public static function register ()
        {

            register_widget ( __CLASS__ );
        }

        /**
         * Callback to enqueue scripts on the Widgets admin page.
         *
         * @access private
         * @since 1 .0
         *
         * @param string $hook_suffix
         */
        public function enqueueScripts ( $hook_suffix )
        {
            $min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            if ( 'widgets.php' !== $hook_suffix )
            {
                return;
            }

            wp_enqueue_style ( 'wp-color-picker' );
            wp_enqueue_script ( 'wp-color-picker' );
            wp_enqueue_script ( 'underscore' );

            $widgetStickyAdminCSSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky-admin$min.css" );
            wp_register_style ( 'ez-toc-widget-sticky-admin', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky-admin$min.css", array(), $widgetStickyAdminCSSVersion );
            wp_enqueue_style ( 'ez-toc-widget-sticky-admin', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky-admin$min.css", array(), $widgetStickyAdminCSSVersion );
        }

        /**
         * Callback to print the scripts to the Widgets admin page footer.
         *
         * @access private
         * @since 2.0.41
         */
        public function printScripts ()
        {
            ?>
            <script>
                (function ($) {
                    function initColorPicker(widget) {
                        widget.find('.color-picker').wpColorPicker({
                            change: _.throttle(function () { // For Customizer
                                $(this).trigger('change');
                            }, 3000)
                        });
                    }

                    function onFormUpdate(event, widget) {
                        initColorPicker(widget);
                    }

                    $(document).on('widget-added widget-updated', onFormUpdate);

                    $(document).ready(function () {
                        $('#widgets-right .widget:has(.color-picker)').each(function () {
                            initColorPicker($(this));
                        });
                    });
                }(jQuery));
            </script>
            <?php
        }

        /**
         * Display the post content. Optionally allows post ID to be passed
         *
         * @link http://stephenharris.info/get-post-content-by-id/
         * @link http://wordpress.stackexchange.com/a/143316
         *
         * @access public
         * @since 2.0.41
         *
         * @param int $post_id Optional. Post ID.
         *
         * @return string
         */
        public function the_content ( $post_id = 0 )
        {

            global $post;
            $post = get_post ( $post_id );
            setup_postdata ( $post );
            ob_start ();
            the_content ();
            $content = ob_get_clean ();
            wp_reset_postdata ();

            return $content;
        }

        /**
         * Renders the widgets.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $args
         * @param array $instance
         */
        public function widget ( $args, $instance )
        {
            $min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            if ( is_404 () || is_archive () || is_search () || ( ! is_front_page () && is_home () ) )
                return;

            $post = ezTOC::get ( get_the_ID () );

            if( function_exists( 'post_password_required' ) ) {
                if( post_password_required() ) return;
            }
            
            /**
             * @link https://wordpress.org/support/topic/fatal-error-when-trying-to-access-widget-area/
             */
            if ( ! $post instanceof ezTOC_Post )
                return;

            if ( $post -> hasTOCItems () )
            {

                /**
                 * @var string $before_widget
                 * @var string $after_widget
                 * @var string $before_title
                 * @var string $after_title
                 */
                extract ( $args );

                $js_vars = array();
                $js_vars[ 'appearance_options' ] = '';
                $js_vars[ 'advanced_options' ] = '';
                $js_vars[ 'scroll_fixed_position' ] = '30';
                $js_vars[ 'sidebar_sticky_title' ] = 120;
                $js_vars[ 'sidebar_sticky_title_size_unit' ] = '%';
                $js_vars[ 'sidebar_sticky_title_weight' ] = '500';
                $js_vars[ 'sidebar_sticky_title_color' ] = '#000';
                $js_vars[ 'sidebar_width' ] = 'auto';
                $js_vars[ 'sidebar_width_size_unit' ] = 'none';
                $js_vars[ 'fixed_top_position' ] = '30';
                $js_vars[ 'fixed_top_position_size_unit' ] = 'px';
                $js_vars[ 'navigation_scroll_bar' ] = 'on';
                $js_vars[ 'scroll_max_height' ] = 'auto';
                $js_vars[ 'scroll_max_height_size_unit' ] = 'none';

                if ( (isset($instance[ 'appearance_options' ]) && 'on' == $instance[ 'appearance_options' ] ) || 'on' == $instance[ 'advanced_options' ] || $js_vars[ 'scroll_fixed_position' ] != $instance[ 'scroll_fixed_position' ] ||
                        $js_vars[ 'scroll_fixed_position' ] != $instance[ 'scroll_fixed_position' ] ||
                        $js_vars[ 'sidebar_sticky_title' ] != $instance[ 'sidebar_sticky_title' ] ||
                        $js_vars[ 'sidebar_sticky_title_size_unit' ] != $instance[ 'sidebar_sticky_title_size_unit' ] ||
                        $js_vars[ 'sidebar_sticky_title_weight' ] != $instance[ 'sidebar_sticky_title_weight' ] ||
                        $js_vars[ 'sidebar_sticky_title_color' ] != $instance[ 'sidebar_sticky_title_color' ] ||
                        $js_vars[ 'sidebar_width' ] != $instance[ 'sidebar_width' ] ||
                        $js_vars[ 'sidebar_width_size_unit' ] != $instance[ 'sidebar_width_size_unit' ] ||
                        $js_vars[ 'fixed_top_position' ] != $instance[ 'fixed_top_position' ] ||
                        $js_vars[ 'fixed_top_position_size_unit' ] != $instance[ 'fixed_top_position_size_unit' ] ||
                        $js_vars[ 'navigation_scroll_bar' ] != $instance[ 'navigation_scroll_bar' ] ||
                        $js_vars[ 'scroll_max_height' ] != $instance[ 'scroll_max_height' ] ||
                        $js_vars[ 'scroll_max_height_size_unit' ] != $instance[ 'scroll_max_height_size_unit' ]
                )
                {
                    $js_vars[ 'appearance_options' ] = isset($instance[ 'appearance_options' ]) ? $instance[ 'appearance_options' ] : '';

                    $js_vars[ 'advanced_options' ] = $instance[ 'advanced_options' ];

                    if ( empty ( $instance[ 'scroll_fixed_position' ] ) || ( ! empty ( $instance[ 'scroll_fixed_position' ] ) && ! is_int ( $instance[ 'scroll_fixed_position' ] ) && 'auto' != $instance[ 'scroll_fixed_position' ] ) )
                        $js_vars[ 'scroll_fixed_position' ] = '30';
                    else
                        $js_vars[ 'scroll_fixed_position' ] = $instance[ 'scroll_fixed_position' ];

                    if ( empty ( $instance[ 'sidebar_sticky_title' ] ) || ( ! empty ( $instance[ 'sidebar_sticky_title' ] ) && ! is_int ( $instance[ 'sidebar_sticky_title' ] ) ) )
                        $js_vars[ 'sidebar_sticky_title' ] = 120;
                    else
                        $js_vars[ 'sidebar_sticky_title' ] = $instance[ 'sidebar_sticky_title' ];

                    if ( empty ( $instance[ 'sidebar_sticky_title_size_unit' ] ) || ( ! empty ( $instance[ 'sidebar_sticky_title_size_unit' ] ) ) )
                        $js_vars[ 'sidebar_sticky_title_size_unit' ] = '%';
                    else
                        $js_vars[ 'sidebar_sticky_title_size_unit' ] = $instance[ 'sidebar_sticky_title_size_unit' ];

                    if ( empty ( $instance[ 'sidebar_sticky_title_weight' ] ) || ( ! empty ( $instance[ 'sidebar_sticky_title_weight' ] ) ) )
                        $js_vars[ 'sidebar_sticky_title_weight' ] = '500';
                    else
                        $js_vars[ 'sidebar_sticky_title_weight' ] = $instance[ 'sidebar_sticky_title_weight' ];

                    if ( empty ( $instance[ 'sidebar_sticky_title_color' ] ) || ( ! empty ( $instance[ 'sidebar_sticky_title_color' ] ) ) )
                        $js_vars[ 'sidebar_sticky_title_color' ] = '#000';
                    else
                        $js_vars[ 'sidebar_sticky_title_color' ] = $instance[ 'sidebar_sticky_title_color' ];

                    if ( empty ( $instance[ 'sidebar_width' ] ) || ( ! empty ( $instance[ 'sidebar_width' ] ) && ! is_int ( $instance[ 'sidebar_width' ] ) && 'auto' != $instance[ 'sidebar_width' ] ) )
                        $js_vars[ 'sidebar_width' ] = 'auto';
                    else
                        $js_vars[ 'sidebar_width' ] = $instance[ 'sidebar_width' ];

                    $js_vars[ 'sidebar_width_size_unit' ] = $instance[ 'sidebar_width_size_unit' ];

                    if ( empty ( $instance[ 'fixed_top_position' ] ) || ( ! empty ( $instance[ 'fixed_top_position' ] ) && ! is_int ( $instance[ 'fixed_top_position' ] ) && '30' != $instance[ 'fixed_top_position' ] ) )
                        $js_vars[ 'fixed_top_position' ] = '30';
                    else
                        $js_vars[ 'fixed_top_position' ] = $instance[ 'fixed_top_position' ];

                    $js_vars[ 'fixed_top_position_size_unit' ] = $instance[ 'fixed_top_position_size_unit' ];
                    $js_vars[ 'navigation_scroll_bar' ] = $instance[ 'navigation_scroll_bar' ];

                    if ( empty ( $instance[ 'scroll_max_height' ] ) || ( ! empty ( $instance[ 'scroll_max_height' ] ) && ! is_int ( $instance[ 'scroll_max_height' ] ) && 'auto' != $instance[ 'scroll_max_height' ] ) )
                        $js_vars[ 'scroll_max_height' ] = 'auto';
                    else
                        $js_vars[ 'scroll_max_height' ] = $instance[ 'scroll_max_height' ];

                    $js_vars[ 'scroll_max_height_size_unit' ] = $instance[ 'scroll_max_height_size_unit' ];
                }

                $class = array(
                    'ez-toc-widget-sticky-v' . str_replace ( '.', '_', ezTOC::VERSION ),
                    'ez-toc-widget-sticky',
                );

                $title = apply_filters ( 'widget_title', $instance[ 'title' ], $instance, $this -> id_base );

                if ( false !== strpos ( $title, '%PAGE_TITLE%' ) || false !== strpos ( $title, '%PAGE_NAME%' ) )
                {

                    $title = str_replace ( '%PAGE_TITLE%', get_the_title (), $title );
                }

                if ( ezTOC_Option::get ( 'show_hierarchy' ) )
                {

                    $class[] = 'counter-hierarchy';
                } else
                {

                    $class[] = 'counter-flat';
                }

                if ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ) == 'ltr' )
                {
                    $class[] = 'ez-toc-widget-sticky-container';
                }
                if ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ) == 'rtl' )
                {
                    $class[] = 'ez-toc-widget-sticky-container-rtl';
                }

                $class[] = 'ez-toc-widget-sticky-direction';

                $custom_classes = ezTOC_Option::get ( 'css_container_class', '' );

                if ( 0 < strlen ( $custom_classes ) )
                {

                    $custom_classes = explode ( ' ', $custom_classes );
                    $custom_classes = apply_filters ( 'ez_toc_widget_sticky_container_class', $custom_classes, $this );

                    if ( is_array ( $custom_classes ) )
                    {

                        $class = array_merge ( $class, $custom_classes );
                    }
                }

                $class = array_filter ( $class );
                $class = array_map ( 'trim', $class );
                $class = array_map ( 'sanitize_html_class', $class );
                //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in the core
                echo $before_widget;
                do_action ( 'ez_toc_widget_sticky_before_widget_container' );

                echo '<div id="ez-toc-widget-sticky-container" class="ez-toc-widget-sticky-container ' . esc_attr(implode ( ' ', $class )) . '">' . PHP_EOL;

                do_action ( 'ez_toc_widget_sticky_before_widget' );

                /**
                 * @todo Instead of inline style, use the shadow DOM.
                 * @link https://css-tricks.com/playing-shadow-dom/
                 *
                 * @todo Consider not outputting the style if CSS is disabled.
                 * @link https://wordpress.org/support/topic/inline-styling-triggers-html-validation-error/
                 */
                if ( 0 < strlen ( $title ) )
                {
                    //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in the core
                    echo $before_title; ?>

                    <?php if(isset($instance[ 'sidebar_sticky_title' ]) && isset($instance[ 'sidebar_sticky_title_size_unit' ])){
                            $title_font_size = $instance[ 'sidebar_sticky_title' ].$instance[ 'sidebar_sticky_title_size_unit' ];
                        }else{
                            $title_font_size = '120%';
                        } ?>

                    <span class="ez-toc-widget-sticky-title-container">
                        <style>
                            #<?php echo esc_attr($this -> id) ?> .ez-toc-widget-sticky-title {
                                font-size: <?php echo esc_attr ( $title_font_size ); ?>;
                                font-weight: <?php echo esc_attr ( isset($instance[ 'sidebar_sticky_title_weight' ]) ? $instance[ 'sidebar_sticky_title_weight' ] : '' ); ?>;
                                color: <?php echo esc_attr (isset($instance[ 'sidebar_sticky_title_color' ]) ? $instance[ 'sidebar_sticky_title_color' ] : '' ); ?>;
                            }
                            #<?php echo esc_attr($this -> id) ?> .ez-toc-widget-sticky-container ul.ez-toc-widget-sticky-list li.active{
                                background-color: <?php echo esc_attr ( isset($instance[ 'highlight_color' ]) ? $instance[ 'highlight_color' ] : '' ); ?>;
                            }
                        </style>

                        <?php
                        $headerTextToggleClass = '';
                        $headerTextToggleStyle = '';
                        
                        if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {
                            $headerTextToggleClass = 'ez-toc-toggle';
                            $headerTextToggleStyle = 'style="cursor: pointer"';
                        }
                        $header_label = '<span class="ez-toc-widget-sticky-title ' . esc_attr($headerTextToggleClass) . '" ' .esc_attr($headerTextToggleStyle) . '>' . esc_html($title) . '</span>';
                        ?>
                        <span class="ez-toc-widget-sticky-title-toggle">
                            <?php if ( 'css' != ezTOC_Option::get ( 'toc_loading' ) ): ?>




                                <?php
                                //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped.
                                echo $header_label;
                                if ( ezTOC_Option::get ( 'visibility' ) )
                                {

                                    echo '<a href="#" class="ez-toc-widget-sticky-pull-right ez-toc-widget-sticky-btn ez-toc-widget-sticky-btn-xs ez-toc-widget-sticky-btn-default ez-toc-widget-sticky-toggle" aria-label="Widget Easy TOC toggle icon"><span style="border: 0;padding: 0;margin: 0;position: absolute !important;height: 1px;width: 1px;overflow: hidden;clip: rect(1px 1px 1px 1px);clip: rect(1px, 1px, 1px, 1px);clip-path: inset(50%);white-space: nowrap;">Toggle Table of Content</span>' . wp_kses_post(ezTOC::getTOCToggleIcon ()) . '</a>';
                                }
                                ?>




                            <?php else: ?>
                                <?php
                                $toggle_view = '';
                                if ( ezTOC_Option::get ( 'visibility_hide_by_default' ) == true )
                                {
                                    $toggle_view = "checked";
                                }
                                if( true == get_post_meta( get_the_ID(), '_ez-toc-visibility_hide_by_default', true ) ) {
                                    $toggle_view = "checked";
                                }
                                $cssIconID = uniqid();
                                if ( ezTOC_Option::get( 'visibility_on_header_text' ) ) {
                                    $htmlCSSIcon = '<label for="ez-toc-widget-sticky-cssicon-toggle-item-' . esc_attr($cssIconID) . '" style="cursor:pointer">' . esc_html($header_label) . '<span class="ez-toc-widget-sticky-pull-right ez-toc-widget-sticky-btn ez-toc-widget-sticky-btn-xs ez-toc-widget-sticky-btn-default ez-toc-widget-sticky-toggle"><span style="border: 0;padding: 0;margin: 0;position: absolute !important;height: 1px;width: 1px;overflow: hidden;clip: rect(1px 1px 1px 1px);clip: rect(1px, 1px, 1px, 1px);clip-path: inset(50%);white-space: nowrap;">Toggle Table of Content</span>' . esc_html(ezTOC::getTOCToggleIcon( 'widget-with-visibility_on_header_text' )) . '</span></label>';
                                } else {
                                    //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped.
                                    echo $header_label;
                                    $htmlCSSIcon = '<label for="ez-toc-widget-sticky-cssicon-toggle-item-' . esc_attr($cssIconID) . '" class="ez-toc-widget-sticky-pull-right ez-toc-widget-sticky-btn ez-toc-widget-sticky-btn-xs ez-toc-widget-sticky-btn-default ez-toc-widget-sticky-toggle"><span style="border: 0;padding: 0;margin: 0;position: absolute !important;height: 1px;width: 1px;overflow: hidden;clip: rect(1px 1px 1px 1px);clip: rect(1px, 1px, 1px, 1px);clip-path: inset(50%);white-space: nowrap;">Toggle Table of Content</span>' . esc_html(ezTOC::getTOCToggleIcon( 'widget-with-visibility_on_header_text' )) . '</label>';
                                }
                                //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped.
                                echo $htmlCSSIcon;
                                ?>
                            <?php endif; ?>
                        </span>
                    </span>

                    <?php 
                        //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in the core
                        echo $after_title; ?>
                    <?php if ( 'css' == ezTOC_Option::get ( 'toc_loading' ) ): ?>
                        <label for="ez-toc-widget-sticky-cssicon-toggle-item-count-<?php echo esc_attr($cssIconID) ?>" class="cssiconcheckbox">1</label><input type="checkbox" id="ez-toc-widget-sticky-cssicon-toggle-item-<?php echo esc_attr($cssIconID) ?>" <?php esc_attr($toggle_view) ?> style="display:none" />
                    <?php endif; ?>
                    <?php
                }
                do_action ( 'ez_toc_widget_sticky_before' );
                //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : getTOCList output is escaped.
                echo '<nav>' . PHP_EOL . $post -> getTOCList ( 'ez-toc-widget-sticky' ) . '</nav>' . PHP_EOL;
                do_action ( 'ez_toc_widget_sticky_after' );
                do_action ( 'ez_toc_widget_sticky_after_widget' );

                echo '</div>' . PHP_EOL;
                do_action ( 'ez_toc_widget_sticky_after_widget_container' );

                //phpcs:ignore  WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in the core
                echo $after_widget;

                // Enqueue the script.
                $widgetCSSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky$min.css" );
                wp_register_style ( 'ez-toc-widget-sticky', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky$min.css", array(), $widgetCSSVersion );
                wp_enqueue_style ( 'ez-toc-widget-sticky', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky$min.css", array(), $widgetCSSVersion );

                wp_add_inline_style ( 'ez-toc-widget-sticky', ezTOC::InlineCountingCSS ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ), 'ez-toc-widget-sticky-direction', 'ez-toc-widget-sticky-container', 'counter', 'ez-toc-widget-sticky-container' ) );

                $widgetJSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky$min.js" );
                wp_register_script ( 'ez-toc-widget-stickyjs', EZ_TOC_URL . "assets/js/ez-toc-widget-sticky$min.js", array( 'jquery' ), $widgetJSVersion , true);
                wp_enqueue_script ( 'ez-toc-widget-stickyjs', EZ_TOC_URL . "assets/js/ez-toc-widget-sticky$min.js", array( 'jquery' ), $widgetJSVersion , true);
                if ( 0 < count ( $js_vars ) )
                {
                    wp_localize_script ( 'ez-toc-widget-stickyjs', 'ezTocWidgetSticky', $js_vars );
                }
            }
        }

        /**
         * Update the widget settings.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $new_instance
         * @param array $old_instance
         *
         * @return array
         */
        public function update ( $new_instance, $old_instance )
        {

            $instance = $old_instance;

            $instance[ 'title' ] = wp_strip_all_tags ( $new_instance[ 'title' ] );

            $instance[ 'highlight_color' ] = wp_strip_all_tags ( $new_instance[ 'highlight_color' ] );

            $instance[ 'hide_inline' ] = array_key_exists ( 'hide_inline', $new_instance ) ? $new_instance[ 'hide_inline' ] : '0';

            if ( isset ( $new_instance[ 'appearance_options' ] ) && $new_instance[ 'appearance_options' ] == 'on' )
            {
                $instance[ 'sidebar_sticky_title' ] = ( int ) wp_strip_all_tags ( $new_instance[ 'sidebar_sticky_title' ] );
                $instance[ 'sidebar_sticky_title_size_unit' ] = wp_strip_all_tags ( $new_instance[ 'sidebar_sticky_title_size_unit' ] );
                $instance[ 'sidebar_sticky_title_weight' ] = wp_strip_all_tags ( $new_instance[ 'sidebar_sticky_title_weight' ] );
                $instance[ 'sidebar_sticky_title_color' ] = wp_strip_all_tags ( $new_instance[ 'sidebar_sticky_title_color' ] );
            } else
            {
                $instance[ 'sidebar_sticky_title' ] = 120;
                $instance[ 'sidebar_sticky_title_size_unit' ] = '%';
                $instance[ 'sidebar_sticky_title_weight' ] = '500';
                $instance[ 'sidebar_sticky_title_color' ] = '#000';
            }

            if ( isset ( $new_instance[ 'advanced_options' ] ) && $new_instance[ 'advanced_options' ] == 'on' )
            {
                $instance[ 'advanced_options' ] = 'on';
                $instance[ 'scroll_fixed_position' ] = ( int ) wp_strip_all_tags ( $new_instance[ 'scroll_fixed_position' ] );
                $instance[ 'sidebar_width' ] = ( 'auto' == $new_instance[ 'sidebar_width' ] ) ? $new_instance[ 'sidebar_width' ] : ( int ) wp_strip_all_tags ( $new_instance[ 'sidebar_width' ] );
                $instance[ 'sidebar_width_size_unit' ] = wp_strip_all_tags ( $new_instance[ 'sidebar_width_size_unit' ] );
                $instance[ 'fixed_top_position' ] = ( 'auto' == $new_instance[ 'fixed_top_position' ] ) ? $new_instance[ 'fixed_top_position' ] : ( int ) wp_strip_all_tags ( $new_instance[ 'fixed_top_position' ] );
                $instance[ 'fixed_top_position_size_unit' ] = wp_strip_all_tags ( $new_instance[ 'fixed_top_position_size_unit' ] );

                $instance[ 'navigation_scroll_bar' ] = wp_strip_all_tags ( $new_instance[ 'navigation_scroll_bar' ] );

                $instance[ 'scroll_max_height' ] = ( 'auto' == $new_instance[ 'scroll_max_height' ] ) ? $new_instance[ 'scroll_max_height' ] : ( int ) wp_strip_all_tags ( $new_instance[ 'scroll_max_height' ] );
                $instance[ 'scroll_max_height_size_unit' ] = wp_strip_all_tags ( $new_instance[ 'scroll_max_height_size_unit' ] );
            } else
            {
                $instance[ 'advanced_options' ] = '';
                $instance[ 'scroll_fixed_position' ] = 30;
                $instance[ 'sidebar_width' ] = 'auto';
                $instance[ 'sidebar_width_size_unit' ] = 'none';
                $instance[ 'fixed_top_position' ] = 30;
                $instance[ 'fixed_top_position_size_unit' ] = 'px';
                $instance[ 'navigation_scroll_bar' ] = 'on';
                $instance[ 'scroll_max_height' ] = 'auto';
                $instance[ 'scroll_max_height_size_unit' ] = 'none';
            }

            return $instance;
        }

        /**
         * Displays the widget settings on the Widgets admin page.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $instance
         *
         * @return string|void
         */
        public function form ( $instance )
        {

            $defaults = array(
                'highlight_color' => '#ededed',
                'title' => 'Table of Contents',
                'appearance_options' => '',
                'advanced_options' => '',
                'scroll_fixed_position' => 30,
                'sidebar_sticky_title' => 120,
                'sidebar_sticky_title_size_unit' => '%',
                'sidebar_sticky_title_weight' => '500',
                'sidebar_sticky_title_color' => '#000',
                'sidebar_width' => 'auto',
                'sidebar_width_size_unit' => 'none',
                'fixed_top_position' => 30,
                'fixed_top_position_size_unit' => 'px',
                'navigation_scroll_bar' => 'on',
                'scroll_max_height' => 'auto',
                'scroll_max_height_size_unit' => 'none',
            );

            $instance = wp_parse_args ( ( array ) $instance, $defaults );

            $highlight_color = esc_attr ( $instance[ 'highlight_color' ] );
            $title_color = esc_attr ( $instance[ 'sidebar_sticky_title_color' ] );
            ?>
            <p>
                <label for="<?php echo esc_attr($this -> get_field_id ( 'title' )); ?>"><?php esc_html_e ( 'Title', 'easy-table-of-contents' ); ?>:</label>
                <input type="text" id="<?php echo esc_attr($this -> get_field_id ( 'title' )); ?>"
                       name="<?php echo esc_attr($this -> get_field_name ( 'title' )); ?>" value="<?php echo esc_attr($instance[ 'title' ]); ?>"
                       style="width:100%;"/>
            </p>

            <div class="ez-toc-widget-appearance-title">
                <input type="checkbox" class="ez_toc_widget_appearance_options" id="<?php echo esc_attr($this -> get_field_id ( 'appearance_options' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'appearance_options' )); ?>" <?php ( 'on' === $instance[ 'appearance_options' ] ) ? 'checked="checked"' : ''; ?>/>
                <label for="<?php echo esc_attr($this -> get_field_id ( 'appearance_options' )); ?>"><?php esc_html_e ( 'Appearance', 'easy-table-of-contents' ); ?></label>
                <div id="ez-toc-widget-options-container" class="ez-toc-widget-appearance-options-container">
                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title' )); ?>"><?php esc_html_e ( 'Title Font Size', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_sticky_title' )); ?>" value="<?php echo esc_attr($instance[ 'sidebar_sticky_title' ]); ?>" />

                        <select id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title_size_unit' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_sticky_title_size_unit' )); ?>" data-placeholder="" >
                            <option value="%" <?php echo ( '%' == $instance[ 'sidebar_sticky_title_size_unit' ] ) ? 'selected' : ''; ?>><?php esc_html_e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="pt" <?php echo ( 'pt' == $instance[ 'sidebar_sticky_title_size_unit' ] ) ? 'selected=' : ''; ?> ><?php esc_html_e ( 'pt', 'easy-table-of-contents' ); ?></option>
                            <option value="px" <?php echo ( 'px' == $instance[ 'sidebar_sticky_title_size_unit' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?php echo ( 'em' == $instance[ 'sidebar_sticky_title_size_unit' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( 'em', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>

                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title_wgt' )); ?>"><?php esc_html_e ( 'Title Font Weight', 'easy-table-of-contents' ); ?>:</label>

                        <select id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title_weight' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_sticky_title_weight' )); ?>" data-placeholder="" style=" width: 60px; ">
                            <option value="100" <?php echo ( '100' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected' : ''; ?>><?php esc_html_e ( '100', 'easy-table-of-contents' ); ?></option>
                            <option value="200" <?php echo ( '200' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?> ><?php esc_html_e ( '200', 'easy-table-of-contents' ); ?></option>
                            <option value="300" <?php echo ( '300' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '300', 'easy-table-of-contents' ); ?></option>
                            <option value="400" <?php echo ( '400' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '400', 'easy-table-of-contents' ); ?></option>
                            <option value="500" <?php echo ( '500' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '500', 'easy-table-of-contents' ); ?></option>
                            <option value="600" <?php echo ( '600' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '600', 'easy-table-of-contents' ); ?></option>
                            <option value="700" <?php echo ( '700' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '700', 'easy-table-of-contents' ); ?></option>
                            <option value="800" <?php echo ( '800' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '800', 'easy-table-of-contents' ); ?></option>
                            <option value="900" <?php echo ( '900' == $instance[ 'sidebar_sticky_title_weight' ] ) ? 'selected=' : ''; ?>><?php esc_html_e ( '900', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>

                    <p class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title_color' )); ?>" style="margin-right: 12px;"><?php esc_html_e ( 'Font Title Color:', 'easy-table-of-contents' ); ?></label><br>
                        <input type="text" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_sticky_title_color' )); ?>" class="color-picker" id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_sticky_title_color' )); ?>" value="<?php echo esc_attr($title_color); ?>" data-default-color="<?php echo esc_attr($defaults[ 'sidebar_sticky_title_color' ]); ?>" />
                    </p>
                    <p class="ez-toc-widget-form-group" style="margin: 0;margin-top: 7px;">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'highlight_color' )); ?>" style="margin-right: 12px;"><?php esc_html_e ( 'Active Section Highlight Color:', 'easy-table-of-contents' ); ?></label><br>
                        <input type="text" name="<?php echo esc_attr($this -> get_field_name ( 'highlight_color' )); ?>" class="color-picker" id="<?php echo esc_attr($this -> get_field_id ( 'highlight_color' )); ?>" value="<?php echo esc_attr($highlight_color); ?>" data-default-color="<?php echo esc_attr($defaults[ 'highlight_color' ]); ?>" />
                    </p>
                </div>
            </div>

            <div class="ez-toc-widget-advanced-title">
                <input type="checkbox" class="ez_toc_widget_advanced_options" id="<?php echo esc_attr($this -> get_field_id ( 'advanced_options' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'advanced_options' )); ?>" <?php ( 'on' === $instance[ 'advanced_options' ] ) ? 'checked="checked"' : ''; ?>/><label for="<?php echo esc_attr($this -> get_field_id ( 'advanced_options' )); ?>"><?php esc_html_e ( 'Advanced Options', 'easy-table-of-contents' ); ?></label>

                <div id="ez-toc-widget-options-container" class="ez-toc-widget-advanced-options-container">
                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'scroll_fixed_position' )); ?>"><?php esc_html_e ( 'Scroll Fixed Position', 'easy-table-of-contents' ); ?>:</label>
                        <input type="number" id="<?php echo esc_attr($this -> get_field_id ( 'scroll_fixed_position' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'scroll_fixed_position' )); ?>" value="<?php echo esc_attr($instance[ 'scroll_fixed_position' ]); ?>" />
                    </div>

                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'sidebar_width' )); ?>"><?php esc_html_e ( 'Sidebar Width', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_width' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_width' )); ?>" value="<?php echo esc_attr($instance[ 'sidebar_width' ]); ?>" />

                        <select id="<?php echo esc_attr($this -> get_field_id ( 'sidebar_width_size_unit' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'sidebar_width_size_unit' )); ?>" data-placeholder="" >
                            <option value="pt" <?php ( 'pt' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php esc_html_e ( 'pt', 'easy-table-of-contents' ); ?></option>

                            <option value="px" <?php ( 'px' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?php ( '%' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?php ( 'em' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?php ( 'none' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>


                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'fixed_top_position' )); ?>"><?php esc_html_e ( 'Fixed Top Position', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo esc_attr($this -> get_field_id ( 'fixed_top_position' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'fixed_top_position' )); ?>" value="<?php echo esc_attr($instance[ 'fixed_top_position' ]); ?>" />

                        <select id="<?php echo esc_attr($this -> get_field_id ( 'fixed_top_position_size_unit' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'fixed_top_position_size_unit' )); ?>" data-placeholder="" >
                            <option value="pt" <?php ( 'pt' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php esc_html_e ( 'pt', 'easy-table-of-contents' ); ?></option>
                            <option value="px" <?php ( 'px' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?php ( '%' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?php ( 'em' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?php ( 'none' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>


                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'navigation_scroll_bar' )); ?>"><?php esc_html_e ( 'Navigation Scroll Bar', 'easy-table-of-contents' ); ?>:</label>
                        <input type="checkbox" id="<?php echo esc_attr($this -> get_field_id ( 'navigation_scroll_bar' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'navigation_scroll_bar' )); ?>" <?php ( 'on' === $instance[ 'navigation_scroll_bar' ] ) ? 'checked="checked"' : ''; ?>/>

                    </div>

                    <div class="ez-toc-widget-form-group">
                        <label for="<?php echo esc_attr($this -> get_field_id ( 'scroll_max_height' )); ?>"><?php esc_html_e ( 'Scroll Max Height', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo esc_attr($this -> get_field_id ( 'scroll_max_height' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'scroll_max_height' )); ?>" value="<?php echo esc_attr($instance[ 'scroll_max_height' ]); ?>" />

                        <select id="<?php echo esc_attr($this -> get_field_id ( 'scroll_max_height_size_unit' )); ?>" name="<?php echo esc_attr($this -> get_field_name ( 'scroll_max_height_size_unit' )); ?>" data-placeholder="" >
                            <option value="pt" <?php ( 'pt' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php esc_html_e ( 'pt', 'easy-table-of-contents' ); ?></option>
                            <option value="px" <?php ( 'px' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?php ( '%' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?php ( 'em' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?php ( 'none' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php esc_html_e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>

                </div>
            </div>
            <?php
        }

    }

    // end class

    add_action ( 'widgets_init', array( 'ezTOC_WidgetSticky', 'register' ) );
}
