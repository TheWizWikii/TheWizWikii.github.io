<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Stats extends WPCaptcha
{
    static public $stats_cutoff = 1;

    /**
     * Get statistics
     *
     * @since 5.0
     *
     * @param string $type locks|fails
     * @param int $ndays period for statistics
     * @return bool
     */
    static function get_stats($type = "locks", $ndays = 60)
    {
        global $wpdb;
        
        $days = array();
        for ($i = $ndays; $i >= 0; $i--){
            $days[date("Y-m-d", strtotime('-' . $i . ' days'))] = 0;
        }

        if ($type == 'locks') {
            $results = $wpdb->get_results("SELECT COUNT(*) as count,DATE_FORMAT(accesslock_date, '%Y-%m-%d') AS date FROM " . $wpdb->wpcatcha_accesslocks . " GROUP BY DATE_FORMAT(accesslock_date, '%Y%m%d')");
        } else {
            $results = $wpdb->get_results("SELECT COUNT(*) as count,DATE_FORMAT(login_attempt_date, '%Y-%m-%d') AS date FROM " . $wpdb->wpcatcha_login_fails . " GROUP BY DATE_FORMAT(login_attempt_date, '%Y%m%d')");
        }

        $total = 0;

        foreach ($results as $day) {
            if(array_key_exists($day->date, $days)){
                $days[$day->date] = $day->count;
                $total += $day->count;
            }
        }

        if ($total < self::$stats_cutoff) {
            $stats['days'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
            $stats['count'] = array(3, 4, 67, 76, 45, 32, 134, 6, 65, 65, 56, 123, 156, 156, 123, 156, 67, 88, 54, 178);
            $stats['total'] = $total;

            return $stats;
        }

        $stats = array('days' => array(), 'count' => array(), 'total' => 0);
        foreach ($days as $day => $count) {
            $stats['days'][] = $day;
            $stats['count'][] = $count;
            $stats['total'] += $count;
        }
        $stats['period'] = $ndays;
        return $stats;
    } // get_stats

} // class
