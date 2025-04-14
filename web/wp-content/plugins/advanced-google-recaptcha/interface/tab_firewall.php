<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Firewall extends WPCaptcha
{
  static function display()
  {
    $tabs[] = array('id' => 'tab_general', 'class' => 'tab-content', 'label' => __('General', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_general'));
    $tabs[] = array('id' => 'tab_2fa', 'class' => 'tab-content', 'label' => __('2FA', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_2fa'));
    $tabs[] = array('id' => 'tab_cloud_protection', 'class' => 'tab-content', 'label' => __('Cloud Protection', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_cloud_protection'));

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

  static function tab_general()
  {
    $options = WPCaptcha_Setup::get_options();

    echo '<p>Securing your WordPress website is vital for maintaining the security and privacy of its users. By preventing against the types of attacks below, website owners can ensure that their users receive legitimate content without being exposed to harmful or malicious data.</p>
        <p>A secure WordPress website promotes a safe browsing experience for users, fostering trust in the site\'s content and services. Additionally, mitigating these risks helps website owners avoid potential legal issues and financial losses associated with security breaches. It also protects the website\'s reputation, ensuring that users continue to rely on the site as a trustworthy source of information and services.</p>';

    echo '<table class="form-table"><tbody>';

    echo '<tr valign="top">
        <th scope="row"><label for="firewall_block_bots">Block bad bots</label></th>
        <td>';
    WPCaptcha_Utility::create_toggle_switch('firewall_block_bots', array('saved_value' => $options['firewall_block_bots'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[firewall_block_bots]'), true, 'firewall_rule_toggle');
    echo '<br><span>Blocking bad bots on a WordPress site refers to the process of identifying and preventing malicious automated software programs, known as "bots," from accessing, crawling, or interacting with the website. Bad bots are typically used by attackers to perform various malicious activities, such as content scraping, spamming, DDoS attacks, vulnerability scanning, or brute-force attacks to gain unauthorized access to the site.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_directory_traversal">Directory Traversal</label></th>
        <td>';
    echo '<div>';
    WPCaptcha_Utility::create_toggle_switch('firewall_directory_traversal', array('saved_value' => $options['firewall_directory_traversal'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[firewall_directory_traversal]'), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>Directory traversal (also known as file path traversal) is a web security vulnerability that allows an attacker to access files on the server that they should not by passing file paths that attempt to traverse the normal directory structure using the parent folder path.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_http_response_splitting">HTTP Response Splitting</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_http_response_splitting" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_http_response_splitting">';
    WPCaptcha_Utility::create_toggle_switch('firewall_http_response_splitting', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>HTTP Response Splitting is a type of attack that occurs when an attacker can manipulate the response headers that will be interpreted by the client. Protecting against HTTP Response Splitting on a WordPress website is crucial to maintain its security and the privacy of its users. By preventing this vulnerability, website owners can reduce the risk of attackers stealing sensitive information, compromising user accounts, or damaging the website\'s reputation. </span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_xss">(XSS) Cross-Site Scripting</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_xss" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_xss">';
    WPCaptcha_Utility::create_toggle_switch('firewall_xss', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>Cross-Site Scripting (XSS) is a type of web application vulnerability that allows an attacker to inject malicious scripts into web pages viewed by other users. This occurs when a web application does not properly validate or sanitize user input and includes it in the rendered HTML output. There are three main types of XSS: stored, reflected, and DOM-based.<br />In stored XSS, the malicious script is saved in the target server (e.g., in a database), while in reflected XSS, the malicious script is part of the user\'s request and reflected back in the response. DOM-based XSS occurs when the vulnerability is in the client-side JavaScript code, allowing the attacker to manipulate the Document Object Model (DOM) directly. This option only protects agains reflected/request type XSS attacks. You should still be careful about what plugins you install and make sure they are secure.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_cache_poisoning">Cache Poisoning</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_cache_poisoning" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_cache_poisoning">';
    WPCaptcha_Utility::create_toggle_switch('firewall_cache_poisoning', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>Cache Poisoning is a type of cyberattack where an attacker manipulates the cache data of web applications, content delivery networks (CDNs), or DNS resolvers to serve malicious content to unsuspecting users. The attacker exploits vulnerabilities or misconfigurations in caching mechanisms to insert malicious data into the cache, effectively "poisoning" it. When a user makes a request, the compromised cache serves the malicious content instead of the legitimate content. This can lead to various harmful consequences, such as redirecting users to phishing sites, spreading malware, or stealing sensitive information.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_dual_header">Dual-Header Exploits</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_dual_header" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_dual_header">';
    WPCaptcha_Utility::create_toggle_switch('firewall_dual_header', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>Dual-Header Exploits, also known as HTTP Header Injection, is a type of web application vulnerability that involves manipulating HTTP headers to execute malicious actions or inject malicious content. Similar to HTTP Response Splitting, an attacker exploits this vulnerability by injecting newline characters (CRLF - carriage return and line feed) or other special characters into user input. This allows the attacker to create or modify HTTP headers, which can lead to various harmful consequences. For instance, an attacker can set cookies, redirect users to malicious websites, or perform cross-site scripting (XSS) attacks.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_sql_injection">SQL/PHP/Code Injection</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_sql_injection" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_sql_injection">';
    WPCaptcha_Utility::create_toggle_switch('firewall_sql_injection', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>SQL/PHP/Code Injection is a type of web application vulnerability where an attacker inserts malicious code or commands into a web application, typically by exploiting insufficient input validation or sanitization. This allows the attacker to execute unauthorized actions, such as extracting sensitive information from databases, modifying data, or gaining unauthorized access to the system.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_file_injection">File Injection/Inclusion</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_file_injection" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_file_injection">';
    WPCaptcha_Utility::create_toggle_switch('firewall_file_injection', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>File Injection/Inclusion is a type of web application vulnerability where an attacker exploits insufficient validation or sanitization of user input to include or inject malicious files into a web application. There are two main types of File Injection/Inclusion vulnerabilities: Local File Inclusion (LFI) and Remote File Inclusion (RFI). This can lead to unauthorized access to sensitive files, source code disclosure, or even the execution of server-side scripts if the application processes the included file. If the application is manipulated to process a remote file, the attacker\'s code is executed, potentially granting unauthorized access, control over the server, or the ability to perform various malicious actions.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_null_byte_injection">Null Byte Injection</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_null_byte_injection" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_null_byte_injection">';
    WPCaptcha_Utility::create_toggle_switch('firewall_null_byte_injection', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>Null Byte Injection is a type of web application vulnerability that exploits the way certain programming languages, such as C and PHP, handle null characters. The null character serves as a string terminator in these languages, signaling the end of a string. An attacker can use a null byte to manipulate user input or file paths, causing the application to truncate the string after the null character. This can lead to unexpected behaviors, such as bypassing input validation or accessing sensitive files.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="firewall_php_info">PHP information leakage</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="firewall_php_info" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="firewall_php_info">';
    WPCaptcha_Utility::create_toggle_switch('firewall_php_info', array('saved_value' => 0, 'option_key' => ''), true, 'firewall_rule_toggle');
    echo '</div>';
    echo '<span>PHP information leakage refers to the unintended exposure of sensitive information about the PHP environment, configurations, or code running on a WordPress website. This information can be valuable for attackers, as it may reveal potential vulnerabilities, system details, or other information that could be exploited to compromise the site.</span>';
    echo '</td></tr>';

    echo '<tr><td></td><td>';
    WPCaptcha_admin::footer_save_button();
    echo '</td></tr>';

    echo '</tbody></table>';
  }

  static function tab_2fa()
  {
    echo '<div class="tab-content">';

    echo '<table class="form-table"><tbody>';

    echo '<tr valign="top">
        <th scope="row" style="width:300px;"><label class="open-upsell open-upsell-block" for="2fa_email">Email Based Two Factor Authentication</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="2fa_email" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="2fa_email">';
    WPCaptcha_Utility::create_toggle_switch('2fa_email', array('saved_value' => 0, 'option_key' => ''));
    echo '</div>';
    echo '<span>After the correct username &amp; password are entered the user will receive an email with a one-time link to confirm the login.<br>In case somebody steals the username &amp; password they still won\'t be able to login without access to the account email.</span>';
    echo '</td></tr>';

    echo '<tr><td></td><td>';
    echo '<p class="submit"><a class="button button-primary button-large open-upsell" data-feature="2fa-save">Save Changes <i class="wpcaptcha-icon wpcaptcha-checkmark"></i></a></p>';
    echo '</td></tr>';

    echo '</tbody></table>';

    echo '</div>';
  } // display

  static function tab_cloud_protection()
  {
    echo '<div class="tab-content">';

    echo '<table class="form-table"><tbody>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="cloud_use_account_lists">Use Account Whitelist &amp; Blacklist</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cloud_use_account_lists" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="cloud_use_account_lists">';
    WPCaptcha_Utility::create_toggle_switch('cloud_use_account_lists', array('saved_value' => 0, 'option_key' => ''));
    echo '</div>';
    echo '<span>These lists are private and available only to your sites. Configure them in the WP Captcha Dashboard</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="cloud_use_blacklist">Use Global Cloud Blacklist</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cloud_use_account_lists" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<div class="open-upsell open-upsell-block" data-feature="cloud_use_blacklist">';
    WPCaptcha_Utility::create_toggle_switch('cloud_use_blacklist', array('saved_value' => 0, 'option_key' => ''));
    echo '</div>';
    echo '<span>A list of bad IPs maintained daily by WebFactory, and based on realtime malicios activity observed on thousands of websites. IPs found on this list are trully bad and should not have access to your site.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="global_block">Cloud Block Type</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cloud_global_block" class="open-upsell pro-label">PRO</a></th>
        <td>';
    echo '<label class="open-upsell open-upsell-block wpcaptcha-radio-option">';
    echo '<span class="radio-container"><input type="radio" name="" id="cloud_global_block_global" value="1"><span class="radio"></span></span> Completely block website access';
    echo '</label>';

    echo '<label class="open-upsell open-upsell-block wpcaptcha-radio-option">';
    echo '<span class="radio-container"><input type="radio" name="" id="cloud_global_block_login" value="0"><span class="radio"></span></span> Only block access to the login page';
    echo '</label>';
    echo '<span>Completely block website access for IPs on cloud blacklist, or just blocking access to the login page.</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="block_message_cloud">Block Message</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="block_message_cloud" class="open-upsell pro-label">PRO</a></th>
        <td><input type="text" class="open-upsell open-upsell-block regular-text" id="block_message_cloud" name="" value="We\'re sorry, but access from your IP is not allowed." />';
    echo '<span>Message displayed to visitors blocked based on cloud lists. Default: <i>We\'re sorry, but access from your IP is not allowed.</i></span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block">Cloud Whitelist</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cloud_protection_whitelist" class="open-upsell pro-label">PRO</a></th>
        <td><textarea class="open-upsell open-upsell-block" rows="4"></textarea>';
    echo '<span>The Cloud Protection Whitelist can only be edited in the WP Captcha Dashboard</span>';
    echo '</td></tr>';

    echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block">Cloud Blacklist</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cloud_protection_blacklist" class="open-upsell pro-label">PRO</a></th>
        <td><textarea class="open-upsell open-upsell-block" rows="4"></textarea>';
    echo '<span>The Cloud Protection Blacklist can only be edited in the WP Captcha Dashboard</span>';
    echo '</td></tr>';

    echo '<tr><td></td><td>';
    echo '<p class="submit"><a class="button button-primary button-large open-upsell" data-feature="cloud-protection-save">Save Changes <i class="wpcaptcha-icon wpcaptcha-checkmark"></i></a></p>';
    echo '</td></tr>';

    echo '</tbody></table>';

    echo '</div>';
  } // display

} // class WPCaptcha_Tab_Firewall
