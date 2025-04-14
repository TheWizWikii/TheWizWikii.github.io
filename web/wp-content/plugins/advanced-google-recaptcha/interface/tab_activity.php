<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Activity extends WPCaptcha
{
    static function display()
    {
        $tabs[] = array('id' => 'tab_log_locks', 'class' => 'tab-content', 'label' => __('Access Locks', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_locks'));
        $tabs[] = array('id' => 'tab_log_full', 'class' => 'tab-content', 'label' => __('Failed Logins', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_full'));

        echo '<div id="tabs_log" class="ui-tabs wpcaptcha-tabs-2nd-level">';
        echo '<ul>';
        foreach ($tabs as $tab) {
            echo '<li><a href="#' . esc_attr($tab['id']) . '">' . esc_html($tab['label']) . '</a></li>';
        }
        echo '</ul>';

        foreach ($tabs as $tab) {
            if (is_callable($tab['callback'])) {
                echo '<div style="display: none;" id="' . esc_attr($tab['id']) . '" class="' . esc_attr($tab['class']) . '">';
                call_user_func($tab['callback']);
                echo '</div>';
            }
        } // foreach

        echo '</div>'; // second level of tabs
    } // display

    static function tab_locks()
    {
        echo '<div class="wpcaptcha-stats-main wpcaptcha-chart-locks" style="display:none"><canvas id="wpcaptcha-locks-chart" style="height: 160px; width: 100%;"></canvas></div>';
        echo '<div class="wpcaptcha-stats-main wpcaptcha-stats-locks" style="display:none;">';
            echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/advanced_stats.png" alt="WP Captcha" title="WP Captcha Stats" />';
        echo'</div>';
        echo '<div class="tab-content">';
            echo '<div id="wpcaptcha-locks-log-table-wrapper">
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="wpcaptcha-locks-log-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width:160px;">Date &amp; time</th>
                                <th>Reason</th>
                                <th>Location/IP</th>
                                <th style="width:280px;">User Agent</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Date &amp; time</th>
                                <th>Reason</th>
                                <th>Location/IP</th>
                                <th>User Agent</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div data-log="locks" class="tooltip empty_log tooltipstered" data-msg-success="Access Locks Log Emptied" data-btn-confirm="Yes, empty the log" data-title="Are you sure you want to empty the Access Locks Log?" data-wait-msg="Emptying. Please wait." data-name=""><i class="wpcaptcha-icon wpcaptcha-trash"></i> Empty Access Locks Log</div>';
        echo '</div>';
    }

    static function tab_full()
    {
        echo '<div class="wpcaptcha-stats-main wpcaptcha-chart-fails" style="display:none"><canvas id="wpcaptcha-fails-chart" style="height: 160px; width: 100%;"></canvas></div>';
        echo '<div class="wpcaptcha-stats-main wpcaptcha-stats-fails" style="display:none;">';
            echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/advanced_stats.png" alt="WP Captcha" title="WP Captcha Stats" />';
        echo'</div>';
        echo '<div class="tab-content">';
            echo '<div id="wpcaptcha-fails-log-table-wrapper">
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="wpcaptcha-fails-log-table">
                        <thead>
                            <tr>
                                <th style="width:160px;">Date &amp; time</th>
                                <th style="width:280px;">User/Pass</th>
                                <th>Location/IP</th>
                                <th style="width:280px;">User Agent</th>
                                <th style="width:280px;">Reason</th>
                                <th style="width:80px;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Date &amp; time</th>
                                <th>User/Pass</th>
                                <th>Location/IP</th>
                                <th>User Agent</th>
                                <th>Reason</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div data-log="fails" class="tooltip empty_log tooltipstered" data-msg-success="Fails Log Emptied" data-btn-confirm="Yes, empty the log" data-title="Are you sure you want to empty the Failed Logins Log?" data-wait-msg="Emptying. Please wait." data-name=""><i class="wpcaptcha-icon wpcaptcha-trash"></i> Empty Failed Logins Log</div>';
        echo '</div>';
    }
} // class WPCaptcha_Tab_Activity
