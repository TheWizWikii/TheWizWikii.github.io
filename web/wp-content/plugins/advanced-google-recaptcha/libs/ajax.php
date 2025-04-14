<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_AJAX extends WPCaptcha
{
    /**
     * Run one tool via AJAX call
     *
     * @return null
     */
    static function ajax_run_tool()
    {
        global $wpdb, $current_user;

        check_ajax_referer('wpcaptcha_run_tool');
        set_time_limit(300);

        $tool = trim(@$_REQUEST['tool']);

        $options = WPCaptcha_Setup::get_options();

        $update['last_options_edit'] = current_time('mysql', true);
        update_option(WPCAPTCHA_OPTIONS_KEY, array_merge($options, $update));

        if ($tool == 'activity_logs') {
            self::get_activity_logs();
        } else if ($tool == 'locks_logs') {
            self::get_locks_logs();
        } else if ($tool == 'recovery_url') {
            if ($_POST['reset'] == 'true') {
                sleep(1);
                $options['global_unblock_key'] = 'll' . md5(time() . rand(10000, 9999));
                update_option(WPCAPTCHA_OPTIONS_KEY, array_merge($options, $update));
            }
            wp_send_json_success(array('url' => '<a href="' . site_url('/?wpcaptcha_unblock=' . $options['global_unblock_key']) . '">' . site_url('/?wpcaptcha_unblock=' . $options['global_unblock_key']) . '</a>'));
        } else if ($tool == 'empty_log') {
            self::empty_log(sanitize_text_field($_POST['log']));
            wp_send_json_success();
        } else if ($tool == 'unlock_accesslock') {
            $wpdb->update(
                $wpdb->wpcatcha_accesslocks,
                array(
                    'unlocked' => 1
                ),
                array(
                    'accesslock_ID' => intval($_POST['lock_id'])
                )
            );
            wp_send_json_success(array('id' => $_POST['lock_id']));
        } else if ($tool == 'delete_lock_log') {
            $wpdb->delete(
                $wpdb->wpcatcha_accesslocks,
                array(
                    'accesslock_ID' => intval($_POST['lock_id'])
                )
            );
            wp_send_json_success(array('id' => $_POST['lock_id']));
        } else if ($tool == 'delete_fail_log') {
            $wpdb->delete(
                $wpdb->wpcatcha_login_fails,
                array(
                    'login_attempt_ID' => intval($_POST['fail_id'])
                )
            );
            wp_send_json_success(array('id' => $_POST['fail_id']));
        } else if ($tool == 'wpcaptcha_dismiss_pointer') {
            delete_option(WPCAPTCHA_POINTERS_KEY);
            wp_send_json_success();
        } else if ($tool == 'verify_captcha') {
            $captcha_result = self::verify_captcha($_POST['captcha_type'], $_POST['captcha_site_key'], $_POST['captcha_secret_key'], $_POST['captcha_response']);
            if (is_wp_error($captcha_result)) {
                wp_send_json_error($captcha_result->get_error_message());
            }
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Unknown tool.', 'advanced-google-recaptcha'));
        }
        die();
    } // ajax_run_tool

    /**
     * Get rule row html
     *
     * @return string row HTML
     *
     * @param array $data with rule settings
     */
    static function get_date_time($timestamp)
    {
        $interval = current_time('timestamp') - $timestamp;
        return '<span class="wpcaptcha-dt-small">' . self::humanTiming($interval, true) . '</span><br />' . date('Y/m/d', $timestamp) . ' <span class="wpcaptcha-dt-small">' . date('h:i:s A', $timestamp) . '</span>';
    }

    static function verify_captcha($type, $site_key, $secret_key, $response)
    {
        if ($type == 'builtin') {
            if ($response === $_COOKIE['wpcaptcha_captcha']) {
                return true;
            } else {
                return new WP_Error('wpcaptcha_builtin_captcha_failed', __("<strong>ERROR</strong>: captcha verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
            }
        } else if ($type == 'recaptchav2') {
            if (!isset($response) || empty($response)) {
                return new WP_Error('wpcaptcha_recaptchav2_not_submitted', __("reCAPTCHA verification failed ", 'advanced-google-recaptcha'));
            } else {
                $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response);
                $response = json_decode($response['body']);

                if ($response->success) {
                    return true;
                } else {
                    return new WP_Error('wpcaptcha_recaptchav2_failed', __("reCAPTCHA verification failed " . (isset($response->{'error-codes'}) ? ': ' . implode(',', $response->{'error-codes'}) : ''), 'advanced-google-recaptcha'));
                }
            }
        } else if ($type == 'recaptchav3') {
            if (!isset($response) || empty($response)) {
                return new WP_Error('wpcaptcha_recaptchav3_not_submitted', __("reCAPTCHA verification failed ", 'advanced-google-recaptcha'));
            } else {
                $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response);
                $response = json_decode($response['body']);

                if ($response->success) {
                    return true;
                } else {
                    return new WP_Error('wpcaptcha_recaptchav2_failed', __("reCAPTCHA verification failed " . (isset($response->{'error-codes'}) ? ': ' . implode(',', $response->{'error-codes'}) : ''), 'advanced-google-recaptcha'));
                }
            }
        } 
    }

    /**
     * Get human readable timestamp like 2 hours ago
     *
     * @return int time
     *
     * @param string timestamp
     */
    static function humanTiming($time)
    {
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        if ($time < 1) {
            return 'just now';
        }
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
        }
    }

    static function empty_log($log)
    {
        global $wpdb;

        if ($log == 'fails') {
            $wpdb->query('TRUNCATE TABLE ' . $wpdb->wpcatcha_login_fails);
        } else {
            $wpdb->query('TRUNCATE TABLE ' . $wpdb->wpcatcha_accesslocks);
        }
    }

    /**
     * Fetch activity logs and output JSON for datatables
     *
     * @return null
     */
    static function get_locks_logs()
    {
        global $wpdb;

        $aColumns = array('accesslock_ID', 'unlocked', 'accesslock_date', 'release_date', 'reason', 'accesslock_IP');
        $sIndexColumn = "accesslock_ID";

        // paging
        $sLimit = '';
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " .
            intval($_GET['iDisplayLength']);
        } // paging

        // ordering
        $sOrder = '';
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " "
                        . ($_GET['sSortDir_' . $i] == 'desc'?'desc':'asc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, '', -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = '';
            }
        } // ordering

        // filtering
        $sWhere = '';
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != '') {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                $sWhere .= $aColumns[$i] . " LIKE '%" . esc_sql($_GET['sSearch']) . "%' OR ";
            }
            $sWhere  = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } // filtering

        // individual column filtering
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == '') {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i] . " LIKE '%" . esc_sql($_GET['sSearch_' . $i]) . "%' ";
            }
        } // individual columns

        // build query
        $wpdb->sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . " FROM " . $wpdb->wpcatcha_accesslocks . " $sWhere $sOrder $sLimit";

        $rResult = $wpdb->get_results($wpdb->sQuery);

        // data set length after filtering
        $wpdb->sQuery = "SELECT FOUND_ROWS()";
        $iFilteredTotal = $wpdb->get_var($wpdb->sQuery);

        // total data set length
        $wpdb->sQuery = "SELECT COUNT(" . $sIndexColumn . ") FROM " . $wpdb->wpcatcha_accesslocks;
        $iTotal = $wpdb->get_var($wpdb->sQuery);

        // construct output
        $output = array(
            "sEcho" => intval(@$_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {
            $row = array();
            $row['DT_RowId'] = $aRow->accesslock_ID;

            if (strtotime($aRow->release_date) < time()) {
                $row['DT_RowClass'] = 'lock_expired';
            }

            for ($i = 0; $i < count($aColumns); $i++) {

                if ($aColumns[$i] == 'unlocked') {
                    $unblocked = $aRow->{$aColumns[$i]};
                    if ($unblocked == 0 && strtotime($aRow->release_date) > time()) {
                        $row[] = '<div class="tooltip unlock_accesslock" data-lock-id="' . $aRow->accesslock_ID . '" title="Unlock"><i class="wpcaptcha-icon wpcaptcha-lock"></i></div>';
                    } else {
                        $row[] = '<div class="tooltip unlocked_accesslock" title="Unlock"><i class="wpcaptcha-icon wpcaptcha-unlock"></i></div>';
                    }
                } else if ($aColumns[$i] == 'accesslock_date') {
                    $row[] = self::get_date_time(strtotime($aRow->{$aColumns[$i]}));
                } else if ($aColumns[$i] == 'reason') {
                    $row[] = $aRow->{$aColumns[$i]};
                } else if ($aColumns[$i] == 'accesslock_IP') {
                    $row[] = '<a href="#" class="open-pro-dialog pro-feature" data-pro-feature="access-log-user-location">Available in PRO</a>';
                    $row[] = '<a href="#" class="open-pro-dialog pro-feature" data-pro-feature="access-log-user-agent">Available in PRO</a>';
                } 
            }
            $row[] = '<div data-lock-id="' . $aRow->accesslock_ID . '" class="tooltip delete_lock_entry" title="Delete Access Lock?" data-msg-success="Access Lock deleted" data-btn-confirm="Delete Access Lock" data-title="Delete Access Lock?" data-wait-msg="Deleting. Please wait." data-name="" title="Delete this Access Lock"><i class="wpcaptcha-icon wpcaptcha-trash"></i></div>';
            $output['aaData'][] = $row;
        } // foreach row

        // json encoded output
        @ob_end_clean();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        echo json_encode($output);
        die();
    }

    /**
     * Fetch activity logs and output JSON for datatables
     *
     * @return null
     */
    static function get_activity_logs()
    {
        global $wpdb;
        $options = WPCaptcha_Setup::get_options();

        $aColumns = array('login_attempt_ID', 'login_attempt_date', 'failed_user', 'failed_pass', 'login_attempt_IP', 'reason');
        $sIndexColumn = "login_attempt_ID";

        // paging
        $sLimit = '';
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " .
                intval($_GET['iDisplayLength']);
        } // paging

        // ordering
        $sOrder = '';
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " "
                        . ($_GET['sSortDir_' . $i] == 'desc'?'desc':'asc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, '', -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = '';
            }
        } // ordering

        // filtering
        $sWhere = '';
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != '') {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                $sWhere .= $aColumns[$i] . " LIKE '%" . esc_sql($_GET['sSearch']) . "%' OR ";
            }
            $sWhere  = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } // filtering

        // individual column filtering
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == '') {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i] . " LIKE '%" . esc_sql($_GET['sSearch_' . $i]) . "%' ";
            }
        } // individual columns

        // build query
        $wpdb->sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) .
            " FROM " . $wpdb->wpcatcha_login_fails . " $sWhere $sOrder $sLimit";

        $rResult = $wpdb->get_results($wpdb->sQuery);

        // data set length after filtering
        $wpdb->sQuery = "SELECT FOUND_ROWS()";
        $iFilteredTotal = $wpdb->get_var($wpdb->sQuery);

        // total data set length
        $wpdb->sQuery = "SELECT COUNT(" . $sIndexColumn . ") FROM " . $wpdb->wpcatcha_login_fails;
        $iTotal = $wpdb->get_var($wpdb->sQuery);

        // construct output
        $output = array(
            "sEcho" => intval(@$_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {
            $row = array();
            $row['DT_RowId'] = $aRow->login_attempt_ID;

            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == 'login_attempt_date') {
                    $row[] = self::get_date_time(strtotime($aRow->{$aColumns[$i]}));
                } elseif ($aColumns[$i] == 'failed_user') {
                    $failed_login = '';
                    $failed_login .= '<strong>User:</strong> ' . htmlspecialchars($aRow->failed_user) . '<br />';
                    if ($options['log_passwords'] == 1) {
                        $failed_login .= '<strong>Pass:</strong> ' . htmlspecialchars($aRow->failed_pass) . '<br />';
                    }
                    $row[] = $failed_login;
                } else if ($aColumns[$i] == 'login_attempt_IP') {
                    $row[] = '<a href="#" class="open-pro-dialog pro-feature" data-pro-feature="fail-log-user-location">Available in PRO</a>';
                    $row[] = '<a href="#" class="open-pro-dialog pro-feature" data-pro-feature="fail-log-user-agent">Available in PRO</a>';
                } elseif ($aColumns[$i] == 'reason') {
                    $row[] = WPCaptcha_Functions::pretty_fail_errors($aRow->{$aColumns[$i]});
                }
            }
            $row[] = '<div data-failed-id="' . $aRow->login_attempt_ID . '" class="tooltip delete_failed_entry" title="Delete failed login attempt log entry" data-msg-success="Failed login attempt log entry deleted" data-btn-confirm="Delete failed login attempt log entry" data-title="Delete failed login attempt log entry" data-wait-msg="Deleting. Please wait." data-name="" title="Delete this failed login attempt log entry"><i class="wpcaptcha-icon wpcaptcha-trash"></i></div>';
            $output['aaData'][] = $row;
        } // foreach row

        // json encoded output
        @ob_end_clean();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        echo json_encode($output);
        die();
    }
} // class
