<?php 
    // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id='toc' class='wrap'>
    <a href="https://tocwp.com/" target="_blank">
        <img src="<?php echo esc_url( plugins_url('assets/img/eztoc-logo.png', dirname(__FILE__))) ?>" alt="tocwp"
             srcset="<?php echo esc_url(plugins_url('assets/img/eztoc-logo.png', dirname(__FILE__))) ?> 1x, <?php echo esc_url(plugins_url('assets/img/eztoc-logo.png', dirname(__FILE__))) ?> 2x">
    </a>
    <h1 style="display:none;">&nbsp;</h1>
    <div class="toc-tab-panel">
        <a id="eztoc-welcome" class="eztoc-tablinks" data-href="no" href="#welcome"
           onclick="ezTocTabToggle(event, 'welcome')"><?php esc_html_e( 'Welcome', 'easy-table-of-contents' ) ?></a>
        <a id="eztoc-default" class="eztoc-tablinks" data-href="no" href="#general-settings"
           onclick="ezTocTabToggle(event, 'general')"><?php esc_html_e( 'Settings', 'easy-table-of-contents' ) ?></a>
        <?php
        if (function_exists('ez_toc_pro_activation_link')) { ?>
         <a id="eztoc-default" class="eztoc-tablinks ez-toc-pro-settings-link-paid" data-href="no" href="#eztoc-prosettings" onclick="ezTocTabToggle(event, 'general')"><?php echo esc_html__( 'PRO Settings', 'easy-table-of-contents' ) ?></a>
        <?php }
       
        if (!function_exists('ez_toc_pro_activation_link')) { ?>
            <a class="eztoc-tablinks" id="eztoc-freevspro" href="#freevspro-support"
               onclick="ezTocTabToggle(event, 'freevspro')" data-href="no"><?php esc_html_e( 'Free vs PRO', 'easy-table-of-contents' ) ?></a>
        <?php }
        ?>
        <a class="eztoc-tablinks" id="eztoc-technical" href="#technical-support"
           onclick="ezTocTabToggle(event, 'technical')" data-href="no"><?php esc_html_e( 'Help & Support', 'easy-table-of-contents' ) ?></a>
           <?php if (!function_exists('ez_toc_pro_activation_link')) { ?>
            <a class="eztoc-tablinks" id="eztoc-upgrade" href="https://tocwp.com/pricing/" target="_blank"><?php esc_html_e( 'UPGRADE to PRO', 'easy-table-of-contents' ) ?></a>
            <?php } ?>
        <?php

        if (function_exists('ez_toc_pro_activation_link')) {
            $license_info = get_option("easytoc_pro_upgrade_license");
            $license_exp = null;
            if( !empty( $license_info['pro']['license_key_expires'] ) ) {
                $license_exp = gmdate( 'Y-m-d', strtotime($license_info['pro']['license_key_expires'] ) );
            }

            ?>
            <a class="eztoc-tablinks" id="eztoc-license" href="#license"
               onclick="ezTocTabToggle(event, 'license')"
               data-href="no"><?php esc_html_e('License', 'easy-table-of-contents') ?>
            </a>
            <?php

            $today = gmdate('Y-m-d');
            $exp_date = $license_exp;
            $date1 = date_create($today);
            if($exp_date){
                $date2 = date_create($exp_date);
                $diff = date_diff($date1, $date2);
                $days = $diff->format("%a");
                $days = intval($days);
                if ($days < 30) {
                    ?>
                    <span class="dashicons dashicons-warning" style="color: #ffb229;position: relative;top:
                    15px;left: -10px;"></span>
                <?php }
            }                                     
            
        } ?>
    </div><!-- /.Tab panel -->
    <div class="eztoc_support_div eztoc-tabcontent" id="welcome" style="display: block;">
        <p style="font-weight: bold;font-size: 30px;color: #000;"><?php esc_html_e( 'Thank YOU for using Easy Table of Content.', 'easy-table-of-contents' ) ?></p>
        <p style="font-size: 18px;padding: 0 10%;line-height: 1.7;color: #000;"><?php esc_html_e( 'We strive to create the best TOC solution in WordPress. Our dedicated development team does continuous development and innovation to make sure we are able to meet your demand.', 'easy-table-of-contents' ) ?></p>
        <p style="font-size: 16px;font-weight: 600;color: #000;"><?php esc_html_e( 'Please support us by Upgrading to Premium version.', 'easy-table-of-contents' ) ?></p>
        <a target="_blank" href="https://tocwp.com/pricing/">
            <button class="button-toc" style="display: inline-block;font-size: 20px;">
                <span><?php esc_html_e( 'YES! I want to Support by UPGRADING.', 'easy-table-of-contents' ) ?></span></button>
        </a>
        <a href="<?php echo esc_url(add_query_arg('page', 'table-of-contents', admin_url('options-general.php'))); ?>"
           style="text-decoration: none;">
            <button class="button-toc1"
                    style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">
                <span style="cursor: pointer;"><?php esc_html_e( 'No Thanks, I will stick with FREE version for now.', 'easy-table-of-contents' ) ?></span>
            </button>
        </a>
    </div>
    <div class="eztoc-tabcontent" id="general">
        <div id="eztoc-tabs" style="margin-top: 10px;">
            <a href="#eztoc-general" id="eztoc-link-general" class="active"><?php esc_html_e( 'General', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-appearance" id="eztoc-link-appearance"><?php esc_html_e( 'Appearance', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-advanced" id="eztoc-link-advanced"><?php esc_html_e( 'Advanced', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-shortcode" id="eztoc-link-shortcode"><?php esc_html_e( 'Shortcode', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-sticky" id="eztoc-link-sticky"><?php esc_html_e( 'Sticky TOC', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-compatibility" id="eztoc-link-compatibility"><?php esc_html_e( 'Compatibility', 'easy-table-of-contents' ) ?></a> | <a href="#eztoc-iesettings" id="eztoc-link-iesettings"><?php esc_html_e( 'Import/Export', 'easy-table-of-contents' ) ?></a>
        </div>
        <form method="post" action="<?php echo esc_url(self_admin_url('options.php')); ?>" enctype="multipart/form-data">

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-general">
                    <br />
                    <h3><span><?php esc_html_e('General', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_general', 'ez_toc_settings_general'); ?>

                        </table>
                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-appearance">
                    <br />
                    <h3><span><?php esc_html_e('Appearance', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_appearance', 'ez_toc_settings_appearance'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-advanced">
                    <br />
                    <h3><span><?php esc_html_e('Advanced', 'easy-table-of-contents'); ?></span></h3>

                    <div class="inside">

                        <table class="form-table">

                            <?php do_settings_fields('ez_toc_settings_advanced', 'ez_toc_settings_advanced'); ?>

                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-shortcode">
                    <br />
                    <h3><span><?php esc_html_e('Shortcode', 'easy-table-of-contents'); ?></span></h3>
                    <div class="inside">

                        <table class="form-table">
                            <?php do_settings_fields('ez_toc_settings_shortcode', 'ez_toc_settings_shortcode'); ?>
                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

            <div class="postbox" id="eztoc-sticky">
                <br />
                <h3><span><?php esc_html_e('Sticky TOC', 'easy-table-of-contents'); ?></span></h3>
                <div class="inside">

                    <table class="form-table">
                        <?php do_settings_fields('ez_toc_settings_sticky', 'ez_toc_settings_sticky'); ?>
                    </table>

                </div><!-- /.inside -->
            </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-compatibility">
                    <br />
                    <h3><span><?php esc_html_e('Compatibility', 'easy-table-of-contents'); ?></span></h3>
                    <div class="inside">

                        <table class="form-table">
                            <?php do_settings_fields('ez_toc_settings_compatibility', 'ez_toc_settings_compatibility'); ?>
                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <div class="metabox-holder">

                <div class="postbox" id="eztoc-iesettings">
                    <br />
                    <h3><span><?php esc_html_e('Import/Export Settings', 'easy-table-of-contents'); ?></span></h3>
                    <div class="inside">

                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <?php $url = wp_nonce_url(admin_url('admin-ajax.php?action=ez_toc_export_all_settings'), '_wpnonce'); ?>
                                    <th scope="row"><?php esc_html_e( 'Export Settings', 'easy-table-of-contents' ) ?></th>
                                    <td>
                                        <button type="button"><a href="<?php echo esc_url($url); ?>" style="text-decoration:none; color: black;"><?php esc_html_e('Export', 'easy-table-of-contents'); ?></a></button>
                                        <label> <br><?php esc_html_e('Export all ETOC settings to json file', 'easy-table-of-contents'); ?></label>
                                    </td>
                                </tr> 
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Import Settings', 'easy-table-of-contents' ) ?></th>
                                    <td>
                                        <input type="file" name="eztoc_import_backup" id="eztoc-import-backup">
                                        <label> <br><?php esc_html_e('Upload json settings file to import', 'easy-table-of-contents'); ?></label>
                                    </td>
                                </tr>                       
                            </tbody>
                        </table>

                    </div><!-- /.inside -->
                </div><!-- /.postbox -->

            </div><!-- /.metabox-holder -->

            <?php if (function_exists('ez_toc_pro_activation_link')) { ?>
                <div class="metabox-holder">

                    <div class="postbox" id="eztoc-prosettings">
                        <br />
                        <h3><span><?php esc_html_e('PRO Settings', 'easy-table-of-contents'); ?></span></h3>
                        <div class="inside">

                            <table class="form-table">
                                <?php do_settings_fields('ez_toc_settings_prosettings', 'ez_toc_settings_prosettings'); ?>

                            </table>

                        </div><!-- /.inside -->
                    </div><!-- /.postbox -->

                </div><!-- /.metabox-holder -->
            <?php } ?>
            <?php settings_fields('ez-toc-settings'); ?>
            <p class="submit">
                <?php submit_button(esc_html__( 'Save Changes', 'easy-table-of-contents'  ), 'primary large', 'submit', false) ; ?>
                <button type="button" id="reset-options-to-default-button" class="button button-primary button-large" style="background-color: #cd3241"><?php esc_html_e( 'Reset', 'easy-table-of-contents' ) ?></button>
            </p>
        </form>
    </div><!-- /.General Settings ended -->


    <div class="eztoc_support_div eztoc-tabcontent" id="technical">
        <div id="eztoc-tabs-technical">
            <a href="#" onclick="ezTocTabToggle(event, 'eztoc-technical-support',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical active"><?php esc_html_e('Technical Support', 'easy-table-of-contents') ?></a>
            |
            <a href="#" onclick="ezTocTabToggle(event, 'eztoc-technical-how-to-use',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php esc_html_e('How to Use', 'easy-table-of-contents') ?></a>
            |
            <a href="#" onclick="ezTocTabToggle(event, 'eztoc-technical-shortcode',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php esc_html_e('Shortcode', 'easy-table-of-contents') ?></a>
            |
            <a href="https://tocwp.com/docs/" target="_blank" class="eztoc-tablinks-technical"><?php echo
                esc_html_e('Documentation', 'easy-table-of-contents') ?></a>
            |
            <a href="#" onclick="ezTocTabToggle(event, 'eztoc-technical-hooks-for-developers',
            'eztoc-tabcontent-technical', 'eztoc-tablinks-technical')"
               class="eztoc-tablinks-technical"><?php esc_html_e('Hooks (for Developers)', 'easy-table-of-contents') ?></a>
        </div>
        <div class="eztoc-form-page-ui">
            <div class="eztoc-left-side">
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-support">
                    <h1><?php esc_html_e('Technical Support', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php esc_html_e('We are dedicated to provide Technical support & Help to our users. Use the below form for sending your questions.', 'easy-table-of-contents') ?> </p>
                    <p><?php esc_html_e('You can also contact us from ', 'easy-table-of-contents') ?><a
                                href="https://tocwp.com/contact/">https://tocwp.com/contact/</a>.</p>

                    <div class="eztoc_support_div_form" id="technical-form">
                        <ul>
                            <li>
                                <label class="ez-toc-support-label"><?php esc_html_e( 'Email', 'easy-table-of-contents' ) ?><span class="star-mark">*</span></label>
                                <div class="ez-toc-support-input">

                                    <input type="text" id="eztoc_query_email" name="eztoc_query_email"
                                           placeholder="<?php esc_html_e( 'Enter your Email', 'easy-table-of-contents' ) ?>" required style="width: 350px;"/>
                                </div>
                            </li>

                            <li>
                                <label class="ez-toc-support-label"><?php esc_html_e( 'Query', 'easy-table-of-contents' ) ?><span class="star-mark">*</span></label>

                                <div class="ez-toc-support-input">
                                    <label for="eztoc_query_message">
                                    <textarea rows="5" cols="50" id="eztoc_query_message"
                                              name="eztoc_query_message"
                                              placeholder="<?php esc_html_e( 'Write your query', 'easy-table-of-contents' ) ?>"></textarea></label>
                                </div>


                            </li>


                            <li>
                                <div class="eztoc-customer-type">
                                    <label class="ez-toc-support-label"><?php esc_html_e( 'Type', 'easy-table-of-contents' ) ?></label>
                                    <div class="ez-toc-support-input">
                                        <select name="eztoc_customer_type" id="eztoc_customer_type" style="width: 350px;">
                                            <option value="select"><?php esc_html_e( 'Select Customer Type', 'easy-table-of-contents' ) ?></option>
                                            <option value="paid"><?php esc_html_e( 'Paid', 'easy-table-of-contents' ) ?><span> <?php esc_html_e( '(Response within 24 hrs)', 'easy-table-of-contents' ) ?></span>
                                            </option>
                                            <option value="free">
                                                <?php esc_html_e( 'Free', 'easy-table-of-contents' ) ?><span> <?php esc_html_e( '( Avg Response within 48-72 hrs)', 'easy-table-of-contents' ) ?></span>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <button class="button button-primary eztoc-send-query"><?php esc_html_e('Send Support Request', 'easy-table-of-contents'); ?></button>
                            </li>
                        </ul>
                        <div class="clear"></div>
                        <span class="eztoc-query-success eztoc-result eztoc_hide"><?php esc_html_e('Message sent successfully, Please wait we will get back to you shortly', 'easy-table-of-contents'); ?></span>
                        <span class="eztoc-query-error eztoc-result eztoc_hide"><?php esc_html_e('Message not sent. please check your network connection', 'easy-table-of-contents'); ?></span>
                    </div>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-how-to-use" style="display:
                none;">
                    <h1><?php esc_html_e('How to Use', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php esc_html_e('You can check how to use `Easy Table of Contents`, follow the basic details below.', 'easy-table-of-contents'); ?></p>
                    <h3><?php esc_html_e('1. AUTOMATICALLY', 'easy-table-of-contents'); ?></h3>
                    <ol>
                        <li><?php esc_html_e('Go to the tab Settings &gt; General section, check Auto Insert', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('Select the post types which will have the table of contents automatically inserted.', 'easy-table-of-contents'); ?></li>
                        <li><?php esc_html_e('NOTE: The table of contents will only be automatically inserted on post types for which it has been enabled.', 'easy-table-of-contents'); ?></li>
                        <li><?php esc_html_e('After Auto Insert, the Position option for choosing where you want to display the `Easy Table of Contents`.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?php esc_html_e('2. MANUALLY', 'easy-table-of-contents'); ?></h3>
                    <p><?php esc_html_e('There are two ways for manual adding & display `Easy Table of Contents`:', 'easy-table-of-contents');
                        ?></p>
                    <ol>
                        <li><?php esc_html_e('Using shortcode, you can copy shortcode and paste the shortcode on editor of any post type.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('Using Insert table of contents option on editor of any post type.',
                                'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You have to choose post types on tab General &gt; Enable Support section then `Easy Table of Contents` editor options would be shown to choose settings for particular post type.', 'easy-table-of-contents'); ?></li>
                    </ol>
                    <h3><?php esc_html_e('3. DESIGN CUSTOMIZATION', 'easy-table-of-contents');
                        ?></h3>
                    <ol>
                        <li><?php esc_html_e('Go to tab Settings &gt; Appearance for design customization.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can change width of `Easy Table of Contents` from select Fixed or Relative sizes or you select custom width then it will be showing custom width option for enter manually width.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Alignment of `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also set Font Option of `Easy Table of Contents` according to your needs.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Theme color of `Easy Table of Contents` on Theme Options section according to your choice.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Custom Theme colors of `Easy Table of Contents`. according to your requirements', 'easy-table-of-contents');
                            ?></li>
                    </ol>
                    <h3><?php esc_html_e('4. STICKY TABLE', 'easy-table-of-contents');
                        ?></h3>
                    <ol>
                        <li><?php esc_html_e('Go to Sticky TOC tab to show Table of contents as sticky on your site.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('Select the post types on which sticky table of contents has been to be enabled.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide whether to have sticky table of contents enabled on Homepage.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide whether to have sticky table of contents enabled on Category|Tag.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide whether to have sticky table of contents enabled on Product Category.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide whether to have sticky table of contents enabled on Custom Taxonomy.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide on which device you want to show sticky table of contents Mobile or Laptop.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide the position of sticky table of contents on left or right.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Alignment of Sticky `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also decide whether the sticky toc should be opened by default on load.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can change width of Sticky `Easy Table of Contents` from select Fixed or Relative sizes or you select custom width then it will be showing custom width option for enter manually width.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can change height of Sticky `Easy Table of Contents` from select Fixed or Relative sizes or you select custom height then it will be showing custom height option for enter manually height.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can change Button Text of Sticky `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Click TOC Close on Mobile of Sticky `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Click TOC Close on desktop of Sticky `Easy Table of Contents`.', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also change title of Sticky `Easy Table of Contents`. (PRO)', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also highlight headings while scrolling of Sticky `Easy Table of Contents`. (PRO)', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also change background of highlight headings of Sticky `Easy Table of Contents`. (PRO)', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also change title of highlight headings of Sticky `Easy Table of Contents`. (PRO)', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Theme color of Sticky `Easy Table of Contents` on Theme Options section according to your choice. (PRO)', 'easy-table-of-contents');
                            ?></li>
                        <li><?php esc_html_e('You can also choose Custom Theme colors of Sticky `Easy Table of Contents` according to your requirements. (PRO)', 'easy-table-of-contents');
                            ?></li>
                    </ol>
                    <h3><?php esc_html_e('5. MORE DOCUMENTATION:', 'easy-table-of-contents'); ?></h3>
                    <p><?php esc_html__('You can visit this link ', 'easy-table-of-contents') . '<a href="https://tocwp.com/docs/" target="_blank">' . esc_html__('More Documentation', 'easy-table-of-contents') . '</a>' . esc_html__(' for more documentation of `Easy Table of Contents`', 'easy-table-of-contents'); ?></p>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-shortcode" style="display: none;">
                    <h1><?php esc_html_e('Shortcode', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php esc_html_e('Use the following shortcode within your content to have the table of contents display where you wish to:', 'easy-table-of-contents'); ?></p>
                    <table class="form-table">
                        <?php do_settings_fields('ez_toc_settings_shortcode', 'ez_toc_settings_shortcode'); ?>
                    </table>
                </div>
                <div class="eztoc-tabcontent-technical" id="eztoc-technical-hooks-for-developers" style="display:
                none;">
                    <h1><?php esc_html_e('Hooks (for Developers)', 'easy-table-of-contents'); ?></h1>
                    <p class="ez-toc-tabcontent-technical-title-content"><?php esc_html_e('This plugin has been designed for easiest way & best features for the users & also as well as for the developers, any developer follow the below advanced instructions:', 'easy-table-of-contents') ?> </p>

                    <h2><?php esc_html_e('Hooks', 'easy-table-of-contents') ?></h2>
                    <p><?php esc_html_e('Developer can use these below hooks for customization of this plugin:', 'easy-table-of-contents')
                        ?></p>
                    <h4><?php esc_html_e('Actions:', 'easy-table-of-contents') ?></h4>
                    <ul>
                        <li><code><?php esc_html_e('ez_toc_before', 'easy-table-of-contents') ?></code>
                        </li>
                        <li><code><?php esc_html_e('ez_toc_after', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php esc_html_e('ez_toc_sticky_toggle_before', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_sticky_toggle_after', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php esc_html_e('ez_toc_before_widget_container', 'easy-table-of-contents')
                                ?></code></li>
                        <li><code><?php esc_html_e('ez_toc_before_widget', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php esc_html_e('ez_toc_after_widget_container', 'easy-table-of-contents') ?></code>
                        </li>
                        <li><code><?php esc_html_e('ez_toc_after_widget', 'easy-table-of-contents')
                                ?></code></li>
                        <li>
                            <code><?php esc_html_e('ez_toc_title', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_sticky_title', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_container_class', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_widget_sticky_container_class', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_url_anchor_target', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_sticky_enable_support', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_sticky_post_types', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_modify_icon', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('ez_toc_label_below_html', 'easy-table-of-contents') ?></code>
                        </li>
                        <li>
                            <code><?php esc_html_e('eztoc_wordpress_final_output', 'easy-table-of-contents') ?></code>
                        </li>
                    </ul>


                    <h4><?php esc_html_e('Example: adding a span tag before the `Easy Table of Contents`',
                            'easy-table-of-contents') ?></h4>
                    <p><?php esc_html_e("Get this following code and paste into your theme\'s function.php file:", 'easy-table-of-contents') ?></p>
                    <pre>
add_action( 'ez_toc_before', 'addCustomSpan' );
function addCustomSpan()
{
    echo <span>Some Text or Element here</span>;
}
                       </pre>

                </div>
            </div>
            <div class="eztoc-right-side">
                <div class="eztoc-bio-box" id="ez_Bio">
                    <h1><?php esc_html_e("Vision & Mission", 'easy-table-of-contents') ?></h1>
                    <p class="eztoc-p"><?php esc_html_e("We strive to provide the best TOC in the world.", 'easy-table-of-contents') ?></p>
                    <section class="eztoc_dev-bio">
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo esc_url(plugins_url('assets/img/ahmed-kaludi.jpg', dirname(__FILE__)))
                                 ?>" alt="ahmed-kaludi"/>
                            <p><?php esc_html_e('Lead Dev', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo esc_url(plugins_url('assets/img/Mohammed-kaludi.jpeg', dirname(__FILE__))) 
                                 ?>" alt="Mohammed-kaludi"/>
                            <p><?php esc_html_e('Developer', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ezoc-bio-wrap">
                            <img width="50px" height="50px"
                                 src="<?php echo esc_url(plugins_url('assets/img/sanjeev.jpg', dirname(__FILE__))) ?>"
                                 alt="Sanjeev"/>
                            <p><?php esc_html_e('Developer', 'easy-table-of-contents'); ?></p>
                        </div>
                    </section>
                    <p class="eztoc_boxdesk"><?php esc_html_e('Delivering a good user experience means a lot to us, so we try our best to reply each and every question.', 'easy-table-of-contents'); ?></p>
                    <p class="ez-toc-company-link"><?php esc_html_e('Support the innovation & development by upgrading to PRO ', 'easy-table-of-contents'); ?> <a href="https://tocwp.com/pricing/"><?php esc_html_e('I Want To Upgrade!', 'easy-table-of-contents'); ?></a></p>
                </div>
            </div>
        </div>
    </div>        <!-- /.Technical support div ended -->

    <div class="eztoc_support_div eztoc-tabcontent" id="freevspro">
        <div class="eztoc-wrapper">
            <div class="eztoc-wr">
                <div class="etoc-eztoc-img">
                    <span class="sp_ov"></span>
                </div>
                <div class="etoc-eztoc-cnt">
                    <h1><?php esc_html_e('UPGRADE to PRO Version', 'easy-table-of-contents'); ?></h1>
                    <p><?php esc_html_e('Take your Table of Contents to the NEXT Level!', 'easy-table-of-contents'); ?></p>
                    <a class="buy" href="#upgrade"><?php esc_html_e('Purchase Now', 'easy-table-of-contents'); ?></a>
                </div>
                <div class="pvf">
                    <div class="ext">
                        <div class="ex-1 e-1">
                            <h4><?php esc_html_e('Premium Features', 'easy-table-of-contents'); ?></h4>
                            <p><?php esc_html_e('Easy TOC Pro will enhances your website table of contents and takes it to a next level to help you reach more engagement and personalization with your users.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-2">
                            <h4><?php esc_html_e('Continuous Innovation', 'easy-table-of-contents'); ?></h4>
                            <p><?php esc_html_e('We are planning to continiously build premium features and release them. We have a roadmap and we listen to our customers to turn their feedback into reality.', 'easy-table-of-contents'); ?></p>
                        </div>
                        <div class="ex-1 e-3">
                            <h4><?php esc_html_e('Tech Support', 'easy-table-of-contents'); ?></h4>
                            <p><?php esc_html_e('Get private ticketing help from our full-time technical staff & developers who helps you with the technical issues.', 'easy-table-of-contents'); ?></p>
                        </div>
                    </div><!-- /. ext -->
                    <div class="pvf-cnt">
                        <div class="pvf-tlt">
                            <h2><?php esc_html_e('Compare Pro vs. Free Version', 'easy-table-of-contents'); ?></h2>
                            <span><?php esc_html_e('See what you\'ll get with the professional version', 'easy-table-of-contents'); ?></span>
                        </div>
                        <div class="pvf-cmp">
                            <div class="fr">
                                <h1><?php esc_html_e('FREE', 'easy-table-of-contents'); ?></h1>
                                <div class="fr-fe">
                                    <div class="fe-1">
                                        <h4><?php esc_html_e('Continious Development', 'easy-table-of-contents'); ?></h4>
                                        <p><?php esc_html_e('We take bug reports and feature requests seriously. We’re continiously developing &amp; improve this product for last 2 years with passion and love.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                    <div class="fe-1">
                                        <h4><?php esc_html_e('50+ Features', 'easy-table-of-contents'); ?></h4>
                                        <p><?php esc_html_e('We\'re constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.', 'easy-table-of-contents'); ?></p>
                                    </div>
                                </div><!-- /. fr-fe -->
                            </div><!-- /. fr -->
                            <div class="pr">
                                <h1><?php esc_html_e('PRO', 'easy-table-of-contents'); ?></h1>
                                <div class="pr-fe">
                                    <span><?php esc_html_e('Everything in Free, and:', 'easy-table-of-contents'); ?></span>
                                    <div class="fet">
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Gutenberg Block', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Easily create TOC in Gutenberg block without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Elementor Widget', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Easily create TOC in Elementor with the widget without the need any coding or shortcode.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Fixed/Sticky TOC', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Users can faster find the content they want with sticky. Also can change the position of Sticky table of contents with different options.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Customize Sticky TOC', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Users can alos customize the appearance of Sticky of the table of contents.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('View More', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Users can show limited number of headings on initial view and show remaining headings on clicking a button.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Read Time', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Users can show estimated read time for your posts/pages inside the table of contents.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Collapsable Sub Headings', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Users can show/hide sub headings of the table of contents.', 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e("ACF Support", 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e("Easily create TOC with your custom ACF fields.", 'easy-table-of-contents'); ?></p>
                                        </div>

                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Full AMP Support', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e('Generates a table of contents with your existing setup and makes them AMP automatically.', 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e('Continuous Updates', 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e("We're continuously updating our premium features and releasing them.", 'easy-table-of-contents'); ?></p>
                                        </div>
                                        <div class="fe-2">
                                            <div class="fe-t">
                                                <img src="<?php echo esc_url(plugins_url('assets/img/right-tick.png',
                                                    dirname(__FILE__))) ?>" alt="right-tick"/>
                                                <h4><?php esc_html_e("Documentation", 'easy-table-of-contents'); ?></h4>
                                            </div>
                                            <p><?php esc_html_e("We create tutorials for every possible feature and keep it updated for you.", 'easy-table-of-contents'); ?></p>
                                        </div>
                                    </div><!-- /. fet -->
                                    <div class="pr-btn">
                                        <a href="#upgrade"><?php esc_html_e("Upgrade to Pro", 'easy-table-of-contents'); ?></a>
                                    </div><!-- /. pr-btn -->
                                </div><!-- /. pr-fe -->
                            </div><!-- /.pr -->
                        </div><!-- /. pvf-cmp -->
                    </div><!-- /. pvf-cnt -->
                    <div id="upgrade" class="amp-upg">
                        <div class="upg-t">
                            <h2><?php esc_html_e("Let's Upgrade Your Easy Table of Contents", 'easy-table-of-contents'); ?></h2>
                            <span><?php esc_html_e("Choose your plan and upgrade in minutes!", 'easy-table-of-contents'); ?></span>
                        </div>
                        <div class="etoc-pri-lst">
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=1"
                                   target="_blank">
                                    <h5><?php esc_html_e("PERSONAL", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>49</span>
                                    <span class="amt"><sup>$</sup>49</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $59)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("1 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("1 year Updates", 'easy-table-of-contents'); ?> </span>
                                    <span class="etoc-sv"><?php esc_html_e("Pro Features", 'easy-table-of-contents'); ?> </span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb rec">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=2"
                                   target="_blank">
                                    <h5><?php esc_html_e("MULTIPLE", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>69</span>
                                    <span class="amt"><sup>$</sup>69</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $79)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("3 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?php esc_html_e("Save 78%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-rcm"><?php esc_html_e("RECOMMENDED", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=3"
                                   target="_blank">
                                    <h5><?php esc_html_e("WEBMASTER", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>79</span>
                                    <span class="amt"><sup>$</sup>79</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $99)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("10 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?php esc_html_e("Save 83%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=4"
                                   target="_blank">
                                    <h5><?php esc_html_e("FREELANCER", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>99</span>
                                    <span class="amt"><sup>$</sup>99</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $119)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("25 Site License", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("Tech Support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?php esc_html_e("Save 90%", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=5"
                                   target="_blank">
                                    <h5><?php esc_html_e("AGENCY", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>199</span>
                                    <span class="amt"><sup>$</sup>199</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $199)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("Billed Annually", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("Unlimited Sites", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("E-mail support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("1 year Updates", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?php esc_html_e("UNLIMITED", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                            <div class="pri-tb">
                                <a href="https://tocwp.com/checkout/?edd_action=add_to_cart&download_id=1618&edd_options[price_id]=6"
                                   target="_blank">
                                    <h5><?php esc_html_e("LIFETIME", 'easy-table-of-contents'); ?></h5>
                                    <span class="d-amt"><sup>$</sup>499</span>
                                    <span class="amt"><sup>$</sup>499</span>
                                    <span class="s-amt"><?php esc_html_e("(Save $199)", 'easy-table-of-contents'); ?></span>
                                    <span class="bil"><?php esc_html_e("One-Time Fee", 'easy-table-of-contents'); ?></span>
                                    <span class="s"><?php esc_html_e("Unlimited Sites", 'easy-table-of-contents'); ?></span>
                                    <span class="e"><?php esc_html_e("Unlimited E-mail support", 'easy-table-of-contents'); ?></span>
                                    <span class="f"><?php esc_html_e("Lifetime License", 'easy-table-of-contents'); ?></span>
                                    <span class="etoc-sv"><?php esc_html_e("UNLIMITED", 'easy-table-of-contents'); ?></span>
                                    <span class="pri-by"><?php esc_html_e("Buy Now", 'easy-table-of-contents'); ?></span>
                                </a>
                            </div>
                        </div><!-- /.pri-lst -->
                        <div class="tru-us">
                            <img src="<?php echo esc_url(plugins_url('assets/img/toc-rating.png', dirname(__FILE__)))
                            ?>" alt="toc-rating"/>
                            <h2><?php esc_html_e("Used by more than 5,00,000+ Users!", 'easy-table-of-contents'); ?></h2>
                            <p><?php esc_html_e("More than 500k Websites, Blogs &amp; E-Commerce shops are powered by our easy table of contents plugin making it the #1 Independent TOC plugin in WordPress.", 'easy-table-of-contents'); ?></p>
                            <a href="https://wordpress.org/support/plugin/easy-table-of-contents/reviews/?filter=5"
                               target="_blank"><?php esc_html_e("Read The Reviews", 'easy-table-of-contents'); ?></a>
                        </div>
                    </div><!--/ .amp-upg -->
                    <div class="ampfaq">
                        <h2><?php esc_html_e("Frequently Asked Questions", 'easy-table-of-contents'); ?></h2>
                        <div class="faq-lst">
                            <div class="lt">
                                <ul>
                                    <li>
                                        <span><?php esc_html_e("Is there a setup fee?", 'easy-table-of-contents'); ?></span>
                                        <p><?php esc_html_e("No. There are no setup fees on any of our plans", 'easy-table-of-contents'); ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("What's the time span for your contracts?", 'easy-table-of-contents'); ?></span>
                                        <p><?php esc_html_e("All the plans are year-to-year which are subscribed annually except for lifetime plan.", 'easy-table-of-contents'); ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("What payment methods are accepted?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("We accepts PayPal and Credit Card payments.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("Do you offer support if I need help?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("Yes! Top-notch customer support for our paid customers is key for a quality product, so we’ll do our very best to resolve any issues you encounter via our support page.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("Can I use the plugins after my subscription is expired?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("Yes, you can use the plugins, but you will not get future updates for those plugins.", 'easy-table-of-contents') ?></p>
                                    </li>
                                </ul>
                            </div>
                            <div class="rt">
                                <ul>
                                    <li>
                                        <span><?php esc_html_e("Can I cancel my membership at any time?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("Yes. You can cancel your membership by contacting us.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("Can I change my plan later on?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("Yes. You can upgrade your plan by contacting us.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("Do you offer refunds?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("You are fully protected by our 100% Money-Back Guarantee Unconditional. If during the next 14 days you experience an issue that makes the plugin unusable, and we are unable to resolve it, we’ll happily offer a full refund.", 'easy-table-of-contents') ?></p>
                                    </li>
                                    <li>
                                        <span><?php esc_html_e("Do I get updates for the premium plugin?", 'easy-table-of-contents') ?></span>
                                        <p><?php esc_html_e("Yes, you will get updates for all the premium plugins until your subscription is active.", 'easy-table-of-contents') ?></p>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.faq-lst -->
                        <div class="f-cnt">
                            <span><?php esc_html_e("I have other pre-sale questions, can you help?", 'easy-table-of-contents') ?></span>
                            <p><?php esc_html_e("All the plans are year-to-year which are subscribed annually.", 'easy-table-of-contents') ?></p>
                            <a href="https://tocwp.com/contact/'?utm_source=tocwp-plugin&utm_medium=addon-card'"
                               target="_blank"><?php esc_html_e("Contact a Human", 'easy-table-of-contents') ?></a>
                        </div><!-- /.f-cnt -->
                    </div><!-- /.faq -->
                </div><!-- /. pvf -->
            </div>
        </div>
    </div><!-- /.freevspro div ended -->

    <div id="license" class="eztoc_support_div eztoc-tabcontent">
        <?php
        do_action("admin_upgrade_license_page");
        ?>
    </div>
    <!--<details id="eztoc-ocassional-pop-up-container" open>
        <summary class="eztoc-ocassional-pop-up-open-close-button"><?php esc_html_e('40% OFF - Limited Time Only', 'easy-table-of-contents'); ?><svg fill="#fff" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 288.359 288.359" style="enable-background:new 0 0 288.359 288.359;" xml:space="preserve"><g><path d="M283.38,4.98c-3.311-3.311-7.842-5.109-12.522-4.972L163.754,3.166c-4.334,0.128-8.454,1.906-11.52,4.972L4.979,155.394   c-6.639,6.639-6.639,17.402,0,24.041L108.924,283.38c6.639,6.639,17.402,6.639,24.041,0l147.256-147.256   c3.065-3.065,4.844-7.186,4.972-11.52l3.159-107.103C288.49,12.821,286.691,8.291,283.38,4.98z M247.831,130.706L123.128,255.407   c-1.785,1.785-4.679,1.785-6.464,0l-83.712-83.712c-1.785-1.785-1.785-4.679,0-6.464L157.654,40.529   c1.785-1.785,4.679-1.785,6.464,0l83.713,83.713C249.616,126.027,249.616,128.921,247.831,130.706z M263.56,47.691   c-6.321,6.322-16.57,6.322-22.892,0c-6.322-6.321-6.322-16.57,0-22.892c6.321-6.322,16.569-6.322,22.892,0   C269.882,31.121,269.882,41.37,263.56,47.691z"/><path d="M99.697,181.278c-5.457,2.456-8.051,3.32-10.006,1.364c-1.592-1.591-1.5-4.411,1.501-7.412   c1.458-1.458,2.927-2.52,4.26-3.298c1.896-1.106,2.549-3.528,1.467-5.438l-0.018-0.029c-0.544-0.96-1.455-1.658-2.522-1.939   c-1.067-0.279-2.202-0.116-3.147,0.453c-1.751,1.054-3.64,2.48-5.587,4.428c-7.232,7.23-7.595,15.599-2.365,20.829   c4.457,4.457,10.597,3.956,17.463,0.637c5.004-2.364,7.55-2.729,9.46-0.819c2.002,2.002,1.638,5.004-1.545,8.186   c-1.694,1.694-3.672,3.044-5.582,4.06c-0.994,0.528-1.728,1.44-2.027,2.525c-0.3,1.085-0.139,2.245,0.443,3.208l0.036,0.06   c1.143,1.889,3.575,2.531,5.503,1.457c2.229-1.241,4.732-3.044,6.902-5.215c8.412-8.412,8.002-16.736,2.864-21.875   C112.475,178.141,107.109,177.868,99.697,181.278z"/><path d="M150.245,157.91l-31.508-16.594c-1.559-0.821-3.47-0.531-4.716,0.714l-4.897,4.898c-1.25,1.25-1.537,3.169-0.707,4.73   l16.834,31.654c0.717,1.347,2.029,2.274,3.538,2.5c1.509,0.225,3.035-0.278,4.114-1.357c1.528-1.528,1.851-3.89,0.786-5.771   l-3.884-6.866l8.777-8.777l6.944,3.734c1.952,1.05,4.361,0.696,5.928-0.871c1.129-1.129,1.654-2.726,1.415-4.303   C152.63,160.023,151.657,158.653,150.245,157.91z M125.621,165.632c0,0-7.822-13.37-9.187-15.644l0.091-0.092   c2.274,1.364,15.872,8.959,15.872,8.959L125.621,165.632z"/><path d="M173.694,133.727c-1.092,0-2.139,0.434-2.911,1.205l-9.278,9.278l-21.352-21.352c-0.923-0.923-2.175-1.441-3.479-1.441   s-2.557,0.519-3.479,1.441c-1.922,1.922-1.922,5.037,0,6.958l24.331,24.332c1.57,1.569,4.115,1.569,5.685,0l13.395-13.395   c1.607-1.607,1.607-4.213,0-5.821C175.833,134.16,174.786,133.727,173.694,133.727z"/><path d="M194.638,111.35l-9.755,9.755l-7.276-7.277l8.459-8.458c1.557-1.558,1.557-4.081-0.001-5.639   c-1.557-1.557-4.082-1.557-5.639,0l-8.458,8.458l-6.367-6.366l9.117-9.117c1.57-1.57,1.57-4.115,0-5.686   c-0.754-0.755-1.776-1.179-2.843-1.179c-1.066,0-2.089,0.424-2.843,1.178l-13.234,13.233c-0.753,0.754-1.177,1.776-1.177,2.843   c0,1.066,0.424,2.089,1.178,2.843l24.968,24.968c1.57,1.569,4.115,1.569,5.685,0l13.87-13.87c1.57-1.57,1.57-4.115,0-5.686   C198.752,109.78,196.208,109.78,194.638,111.35z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></summary>
        <span class="eztoc-promotion-close-btn">  &times;  </span>
        <div class="eztoc-ocassional-pop-up-contents">

            <img src="<?php plugins_url('assets/img/offer-gift-icon.png', dirname(__FILE__)) ?>" class="eztoc-promotion-surprise-icon" />
            <p class="eztoc-ocassional-pop-up-headline"><?php esc_html_e('40% OFF on', 'easy-table-of-contents'); ?> <span><?php esc_html_e('Easy TOC PRO', 'easy-table-of-contents');?></span></p>
            <p class="eztoc-ocassional-pop-up-second-headline"><?php esc_html_e('Upgrade the PRO version during this festive season and get our biggest discount of all time on New Purchases, Renewals &amp; Upgrades', 'easy-table-of-contents'); ?></p>
            <a class="eztoc-ocassional-pop-up-offer-btn" href="<?php esc_url('https://tocwp.com/november-deal/') ?>" target="_blank"><?php esc_html_e('Get This Offer Now', 'easy-table-of-contents'); ?></a>
            <p class="eztoc-ocassional-pop-up-last-line"><?php esc_html_e('Black Friday, Cyber Monday, Christmas &amp; New year are the only times we offer discounts this big.', 'easy-table-of-contents'); ?> </p>

        </div>

    </details>-->
</div>
