<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
class eztoc_pointers {
	public function __construct () {
		add_filter( 'eztoc_localize_filter',array($this,'eztoc_add_localize_footer_data'),10,2);
		add_action('wp_ajax_eztoc_subscribe_newsletter',array($this, 'eztoc_subscribe_for_newsletter'));
	}

	public function eztoc_subscribe_for_newsletter(){
		if( !wp_verify_nonce( sanitize_text_field( $_POST['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
			echo 'security_nonce_not_verified';
			wp_die();
		}
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';
		$api_params = array(
			'name' => sanitize_text_field($_POST['name']),
			'email'=> sanitize_email($_POST['email']),
			'website'=> sanitize_text_field($_POST['website']),
			'type'=> 'etoc'
		);
		wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		wp_die();
	}
	public function eztoc_add_localize_footer_data( $object, $object_name ) {
            
        $dismissed = explode ( ',', get_user_meta ( wp_get_current_user()->ID, 'dismissed_wp_pointers', true ) );
        $do_tour   = !in_array ( 'eztoc_subscribe_pointer', $dismissed );
     
        if ( $object_name == 'eztoc_admin_data' ) {
                global $current_user;                
				$tour     = array();
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information but only loading it inside the admin_enqueue_scripts.
                $tab      = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
                
                if ( ! array_key_exists( $tab, $tour ) ) {
			                                           			            	
                        $object['do_tour']            = $do_tour;        
                        $object['get_home_url']       = get_home_url();                
                        $object['current_user_email'] = $current_user->user_email;                
                        $object['current_user_name']  = $current_user->display_name;        
						$object['displayID']          = '.settings_page_table-of-contents';                        
                        $object['button1']            = esc_html__( 'No Thanks', 'easy-table-of-contents' );
                        $object['button2']            = false;
                        $object['function_name']      = '';  
						$object['translable_txt']['using_eztoc']        = esc_html__('Thank You for using Easy TOC!', 'easy-table-of-contents');  
						$object['translable_txt']['do_you_want']        = esc_html__('Do you want the latest update on', 'easy-table-of-contents');  
						$object['translable_txt']['sd_update']       	= esc_html__(' Easy TOC ', 'easy-table-of-contents');  
						$object['translable_txt']['before_others']      = esc_html__('before others and some best resources on Easy TOC in a single email? - Free just for users of Easy TOC!', 'easy-table-of-contents');  

		}
		                                                                                                                                                    
        }
        return $object;
         
    }
}
$eztoc_pointers = new eztoc_pointers();