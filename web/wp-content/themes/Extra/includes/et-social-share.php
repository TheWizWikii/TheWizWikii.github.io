<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class ET_Social_Share {

	public $name;

	public $slug;

	public $share_url = '#';

	private static $_networks = array();

	function __construct() {
		$this->init();

		self::$_networks[$this->slug] = $this;
	}

	static function get_networks() {
		return self::$_networks;
	}

	function create_share_url( $permalink, $title ) {
		$permalink = esc_url( $permalink );
		$title = esc_attr( $title );
		$title = rawurlencode( wp_strip_all_tags( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) ) );
		$featured_image_url = '';

		// get the URL of featured image for Pinterest network
		if ( 'pinterest' === $this->slug ) {
			$featured_image_id        = get_post_thumbnail_id();
			$featured_image_url_array = wp_get_attachment_image_src( $featured_image_id );
			$featured_image_url       = is_array( $featured_image_url_array ) ? $featured_image_url_array[0] : '';
		}

		if ( false !== strpos( $this->share_url, '%1$s' ) ) {
			$url = sprintf( $this->share_url, $permalink, $title, $featured_image_url );
		} else {
			return '#';
		}

		$url = esc_url( $url );

		return $url;
	}

}

class ET_Facebook_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Facebook', 'extra' );
		$this->slug = 'facebook';
		$this->share_url = 'http://www.facebook.com/sharer.php?u=%1$s&t=%2$s';
	}

}
new ET_Facebook_Social_Share;

class ET_Twitter_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Twitter', 'extra' );
		$this->slug = 'twitter';
		$this->share_url = 'http://twitter.com/intent/tweet?text=%2$s%%20%1$s';
	}

}
new ET_Twitter_Social_Share;

class ET_Google_Plus_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Google +', 'extra' );
		$this->slug = 'googleplus';
		$this->share_url = 'https://plus.google.com/share?url=%1$s&t=%2$s';
	}

}
new ET_Google_Plus_Social_Share;

class ET_Tumblr_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Tumblr', 'extra' );
		$this->slug = 'tumblr';
		$this->share_url = 'https://www.tumblr.com/share?v=3&u=%1$s&t=%2$s';
	}

}
new ET_Tumblr_Social_Share;

class ET_Pinterest_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Pinterest', 'extra' );
		$this->slug = 'pinterest';
		$this->share_url = 'http://www.pinterest.com/pin/create/button/?url=%1$s&description=%2$s&media=%3$s';
	}

}
new ET_Pinterest_Social_Share;

class ET_LinkedIn_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'LinkedIn', 'extra' );
		$this->slug = 'linkedin';
		$this->share_url = 'http://www.linkedin.com/shareArticle?mini=true&url=%1$s&title=%2$s';
	}

}
new ET_LinkedIn_Social_Share;

class ET_Buffer_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Buffer', 'extra' );
		$this->slug = 'buffer';
		$this->share_url = 'https://bufferapp.com/add?url=%1$s&title=%2$s';
	}

}
new ET_Buffer_Social_Share;

class ET_Stumbleupon_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Stumbleupon', 'extra' );
		$this->slug = 'stumbleupon';
		$this->share_url = 'http://www.stumbleupon.com/badge?url=%1$s&title=%2$s';
	}

}
new ET_Stumbleupon_Social_Share;

class ET_Basic_Email_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Email', 'extra' );
		$this->slug = 'basic_email';
	}

}
new ET_Basic_Email_Social_Share;

class ET_Basic_Print_Social_Share extends ET_Social_Share {

	function init() {
		$this->name = esc_html__( 'Print', 'extra' );
		$this->slug = 'basic_print';
	}

}
new ET_Basic_Print_Social_Share;
