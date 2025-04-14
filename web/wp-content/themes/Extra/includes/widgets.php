<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class ET_Social_Followers_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'et_social_followers',
			esc_html__( 'Extra - Social Network Followers', 'extra' ),
			array(
				'description' => esc_html__( 'Display your follower counts from top social networks', 'extra' ),
			)
		);
		require_once dirname( __FILE__ ) . '/et-social-followers.php';
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		?>

		<div class="widget_content">
			<ul class="widget_list">
			<?php
			$networks = ET_Social_Followers::get_networks();

			foreach ( $networks as $network ) {
				if ( $instance[ $network->slug . '-enabled' ] ) {
					$count = '';

					$url = $instance[ $network->slug . '-url' ];

					if ( empty( $url ) ) {
						continue;
					}

					$settings_populated = true;
					$settings = array();
					foreach ( $network->get_fields() as $field_name => $field ) {
						$value = $instance[ $network->slug . '-' . $field_name ];
						if ( empty( $value ) ) {
							$settings_populated = false;
							continue;
						}
						$settings[$field_name] = $value;
					}

					if ( $settings_populated ) {
						$network->populate_settings( $settings );
						$count = $network->get_count();
						$text = $network->get_followers_text( $count );
					}

					?>
					<li>
						<a class="et-extra-icon et-extra-icon-<?php echo esc_attr( $network->slug );?> et-extra-icon-background social-icon" href="<?php echo esc_url( $url ); ?>"></a>
						<a href="<?php echo esc_url( $url ); ?>" class="widget_list_social">
							<h3 class="title"><?php echo esc_html( $network->name ); ?></h3>
							<?php if ( !empty( $count ) ) { ?>
							<span class="post-meta"><?php echo esc_html( $text ); ?></span>
							<?php } ?>
						</a>
					</li>
					<?php
				}
			}
			?>
			</ul>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : esc_html__( 'Follow Us', 'extra' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<hr />
		<?php

		$networks = ET_Social_Followers::get_networks();
		foreach ( $networks as $network ) {
		?>
		<div class="et-social-followers-network">
			<p>
				<strong><?php echo esc_html( $network->name ); ?></strong>
				<?php $enabled = isset( $instance[ $network->slug . '-enabled' ] ) ? $instance[ $network->slug . '-enabled' ] : true; ?>
				<label class="network-enabled-label"><input type="checkbox" class="network-enabled" data-network="<?php echo esc_attr( $network->slug ); ?>" value="1" id="<?php echo $this->get_field_id( $network->slug . '-enabled' ); ?>" name="<?php echo $this->get_field_name( $network->slug . '-enabled' ); ?>" <?php checked( $enabled, true ); ?> /> <?php esc_html_e( 'Enabled?', 'extra' ); ?></label>
			</p>

			<?php $enabled_style = $enabled ? '' : 'style="display:none;'; ?>
			<div class="et-network-settings network-<?php echo esc_attr( $network->slug ); ?>" <?php echo $enabled_style; ?>>

				<p>
					<?php $url = isset( $instance[ $network->slug . '-url' ] ) ? $instance[ $network->slug . '-url' ] : $network->get_default_api_data( 'username' ); ?>
					<label for="<?php echo $this->get_field_id( $network->slug . '-url' ); ?>"><?php esc_html_e( 'URL:', 'extra' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( $network->slug . '-url' ); ?>" name="<?php echo $this->get_field_name( $network->slug . '-url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>">
				</p>

				<?php foreach ( $network->get_fields() as $field_name => $field ) { ?>
				<?php
					$original_field_name = $field_name;
					$field_name = $network->slug . '-' . $field_name;
					$default_value = isset( $field['default'] ) ? $field['default'] : '';
					$field_value = isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : $default_value;
				?>
					<p>
						<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
						<input class="widefat<?php echo isset( $field['authorization_field'] ) && true === $field['authorization_field'] ? ' et-autorize-required-field' : ''; ?>" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>" type="<?php echo esc_html( $field['type'] ); ?>" value="<?php echo esc_attr( $field_value ); ?>" data-original_field_name="<?php echo esc_attr( $original_field_name ); ?>">
					</p>
					<?php if ( !empty( $field['description'] ) ) { ?>
						<p class="description"><?php echo $field['description']; ?></p>
					<?php } ?>
				<?php } ?>
				<?php if ( isset( $network->authorization_required ) && $network->authorization_required ) { ?>
					<?php
					$social_follow_api = et_get_option( 'social_follow_api', array() );
					$button_text = ! empty( $social_follow_api[ $network->slug . '_access_token' ] ) ? esc_html__( 'Re-Authorize', 'extra' ) : esc_html__( 'Authorize', 'extra' ); ?>
					<div class="alignright">
						<input type="button" class="button button-primary et-authorize-network right" value="<?php echo $button_text;?>" data-et-network-name="<?php echo $network->slug; ?>">
						<span class="spinner"></span>
					</div>
					<br class="clear"/>
				<?php } ?>
			</div>
			<hr />
		</div>
		<?php
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$social_follow_api = et_get_option( 'social_follow_api', array() );
		$api_data_changed = false;

		$networks = ET_Social_Followers::get_networks();
		foreach ( $networks as $network ) {

			$instance[ $network->slug . '-enabled' ] = isset( $new_instance[ $network->slug . '-enabled' ] ) ? true : false;
			$instance[ $network->slug . '-url' ] = sanitize_text_field( $new_instance[ $network->slug . '-url' ] );
			foreach ( $network->get_fields() as $field_name => $field ) {
				$original_field_name = $field_name;
				$field_name = $network->slug . '-' . $field_name;
				$instance[ $field_name ] = sanitize_text_field( $new_instance[ $field_name ] );

				// update follow api default fields if needed
				if ( isset( $field['authorization_field'] ) && $field['authorization_field'] ) {
					$social_follow_api[ $network->slug . '_' . $original_field_name ] = sanitize_text_field( $new_instance[ $field_name ] );
					$api_data_changed = true;
				}
			}
		}

		// update social_follow_api option if it was changed
		if ( $api_data_changed ) {
			et_update_option( 'social_follow_api', $social_follow_api );
		}

		return $instance;
	}

}

class ET_Recent_Tweets_Widget extends WP_Widget {

	private $_hashes = array();

	function __construct() {
		parent::__construct(
			'et_recent_tweets',
			esc_html__( 'Extra - Recent Tweets', 'extra' ),
			array(
				'description' => esc_html__( 'Display your recent tweets.', 'extra' ),
			)
		);

		require_once dirname( __FILE__ ) . '/ext/twitter_oauth.php';
	}

	public function fetch_tweets( $instance ) {
		$twitterConnection = new TwitterOAuth(
			$instance['consumer_key'],
			$instance['consumer_secret'],
			$instance['access_token'],
			$instance['access_token_secret']
		);

		$twitterData = $twitterConnection->get('statuses/user_timeline', array(
			'count'       => ( isset( $instance['count'] ) ? $instance['count'] : 10 ),
			'include_rts' => 1,
		));

		return $twitterData;
	}

	public function get_tweets( $instance ) {
		if ( false === ( $tweets = get_transient( 'et_recent_tweets' ) ) ) {
			$tweets = $this->fetch_tweets( $instance );
			$transient_expiration = HOUR_IN_SECONDS;
			set_transient( 'et_recent_tweets', $tweets, $transient_expiration );
		}
		return $tweets;
	}

	function generate_unique_hash( $seed, $length ) {
		$hash = md5( $seed );
		$hash = substr( $hash, 0, $length );

		if ( in_array( $hash, $this->_hashes ) ) {
			$seed = $seed . rand( 1, 100 );
			return $this->generate_unique_hash( $seed, $length );
		}

		$this->_hashes[] = $hash;

		return $hash;
	}

	public function widget( $args, $instance ) {
		if ( empty( $instance['consumer_key'] ) || empty( $instance['consumer_secret'] ) || empty( $instance['access_token'] ) || empty( $instance['access_token_secret'] ) ) {
			return;
		}

		$tweets = $this->get_tweets( $instance );

		if ( empty( $tweets ) || ! empty( $tweets->errors ) ) {
			return;
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		?>
		<div class="widget_content">
			<ul class="widget_list">
			<?php
			foreach ( $tweets as $tweet ) {
				$id = $tweet->id_str;
				$user = $tweet->user;

				$user_name = $user->screen_name;
				$user_id = $user->id;

				$tweet_text = $tweet->text;
				$date = $tweet->created_at;

				$entities = $tweet->entities;

				if ( !empty( $entities->hashtags ) ) {
					foreach ( $entities->hashtags as $hashtag ) {

						$hashtag_url = 'https://twitter.com/hashtag/' . $hashtag->text .'?src=hash';
						$new_hashtag_text = '<a href="' . esc_url( $hashtag_url ) . '" target="_blank">#' . esc_html( $hashtag->text ) .'</a>';
						$hashtag->new_text = $new_hashtag_text;

						$seed = $hashtag->text . $hashtag->indices[0] . $hashtag->indices[1];
						$length = $hashtag->indices[1] - $hashtag->indices[0];
						$hash = $this->generate_unique_hash( $seed, $length );
						$hashtag->hash = $hash;
						$new_hashed_text = substr_replace( $tweet->text, $hash, $hashtag->indices[0], $length );
						$tweet->text = $new_hashed_text;
					}
				}

				if ( !empty( $entities->urls ) ) {
					foreach ( $entities->urls as $url ) {

						$new_url_text = '<a href="' . esc_url( $url->expanded_url ) . '" target="_blank">' . esc_html( $url->display_url ) .'</a>';
						$url->new_text = $new_url_text;

						$seed = $url->url . $url->indices[0] . $url->indices[1];
						$length = $url->indices[1] - $url->indices[0];
						$hash = $this->generate_unique_hash( $seed, $length );
						$url->hash = $hash;
						$new_hashed_text = substr_replace( $tweet->text, $hash, $url->indices[0], $length );
						$tweet->text = $new_hashed_text;
					}
				}

				if ( !empty( $entities->user_mentions ) ) {
					foreach ( $entities->user_mentions as $user_mention ) {

						$user_mention_url = 'https://twitter.com/' . $user_mention->screen_name;
						$new_user_mention_text = '<a href="' . esc_url( $user_mention_url ) . '" target="_blank">@' . esc_html( $user_mention->screen_name ) .'</a>';
						$user_mention->new_text = $new_user_mention_text;

						$seed = $user_mention->screen_name . $user_mention->indices[0] . $user_mention->indices[1];
						$length = $user_mention->indices[1] - $user_mention->indices[0];
						$hash = $this->generate_unique_hash( $seed, $length );
						$user_mention->hash = $hash;
						$new_hashed_text = substr_replace( $tweet->text, $hash, $user_mention->indices[0], $length );
						$tweet->text = $new_hashed_text;
					}
				}

				if ( !empty( $entities->hashtags ) ) {
					foreach ( $entities->hashtags as $hashtag ) {
						$tweet->text = str_replace( $hashtag->hash, $hashtag->new_text, $tweet->text );
					}
				}

				if ( !empty( $entities->urls ) ) {
					foreach ( $entities->urls as $url ) {
						$tweet->text = str_replace( $url->hash, $url->new_text, $tweet->text );
					}
				}

				if ( !empty( $entities->user_mentions ) ) {
					foreach ( $entities->user_mentions as $user_mention ) {
						$tweet->text = str_replace( $user_mention->hash, $user_mention->new_text, $tweet->text );
					}
				}

				$ago_text = sprintf( esc_html__( '%s ago', 'extra' ), human_time_diff( strtotime( $date ) ) );

				$tweet_link = 'https://twitter.com/' . $user_name . '/status/' . $id;
				$reply_link = 'https://twitter.com/intent/tweet?in_reply_to=' . $id;
				$retweet_link = 'https://twitter.com/intent/retweet?tweet_id=' . $id;
				$favorite_link = 'https://twitter.com/intent/favorite?tweet_id=' . $id;

			?>
			<li>
				<a href="<?php echo esc_url( $tweet_link ); ?>" class="et-extra-icon et-extra-icon-twitter et-extra-icon-background-none et-extra-social-icon social-icon"></a>
				<div class="post-tweet">
					<?php echo $tweet->text; ?>
					<div class="post-meta">
						<?php echo esc_html( $ago_text ); ?> • <a href="<?php echo esc_url( $reply_link ); ?>" target="_blank"><?php esc_html_e( 'Reply', 'extra' ); ?></a> • <a href="<?php echo esc_url( $retweet_link ); ?>" target="_blank"><?php esc_html_e( 'Retweet', 'extra' ); ?></a> • <a href="<?php echo esc_url( $favorite_link ); ?>"target="_blank"><?php esc_html_e( 'Favorite', 'extra' ); ?></a>
					</div>
				</div>
			</li>
			<?php } ?>
			</ul>

			<?php
			$user = $tweets[0]->user;
			$user_name = $user->screen_name;
			$user_url = 'http://twitter.com/' . $user_name;
			$follow_text = sprintf( __( 'Follow @%s', 'extra' ), $user_name );
			?>
			<div class="widget_footer">
				<a href="<?php echo esc_url( $user_url ); ?>" target="_blank"><span class="et-extra-icon et-extra-icon-twitter et-extra-social-icon et-extra-icon-background-none social-icon"></span> <?php echo esc_html( $follow_text ); ?></a>
			</div>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : esc_html__( 'Recent Tweets', 'extra' );
		$count = isset( $instance[ 'count' ] ) ? $instance[ 'count' ] : 5;
		$consumer_key = isset( $instance[ 'consumer_key' ] ) ? $instance[ 'consumer_key' ] : '';
		$consumer_secret = isset( $instance[ 'consumer_secret' ] ) ? $instance[ 'consumer_secret' ] : '';
		$access_token = isset( $instance[ 'access_token' ] ) ? $instance[ 'access_token' ] : '';
		$access_token_secret = isset( $instance[ 'access_token_secret' ] ) ? $instance[ 'access_token_secret' ] : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php esc_html_e( 'Display # of Tweets:', 'extra' ); ?><span class="description">(<?php esc_html_e( 'Maximum 200', 'extra' ); ?>)</span></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />

		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>"><?php esc_html_e( 'Consumer Key:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" type="text" value="<?php echo esc_attr( $consumer_key ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>"><?php esc_html_e( 'Consumer Secret:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" type="text" value="<?php echo esc_attr( $consumer_secret ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token' ); ?>"><?php esc_html_e( 'Access token:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'access_token' ); ?>" name="<?php echo $this->get_field_name( 'access_token' ); ?>" type="text" value="<?php echo esc_attr( $access_token ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token_secret' ); ?>"><?php esc_html_e( 'Access Token Secret:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'access_token_secret' ); ?>" type="text" value="<?php echo esc_attr( $access_token_secret ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? absint( sanitize_text_field( $new_instance['count'] ) ) : '';
		$instance['count'] = min( 20, $instance['count'] );
		$instance['consumer_key'] = ( ! empty( $new_instance['consumer_key'] ) ) ? sanitize_text_field( $new_instance['consumer_key'] ) : '';
		$instance['consumer_secret'] = ( ! empty( $new_instance['consumer_secret'] ) ) ? sanitize_text_field( $new_instance['consumer_secret'] ) : '';
		$instance['access_token'] = ( ! empty( $new_instance['access_token'] ) ) ? sanitize_text_field( $new_instance['access_token'] ) : '';
		$instance['access_token_secret'] = ( ! empty( $new_instance['access_token_secret'] ) ) ? sanitize_text_field( $new_instance['access_token_secret'] ) : '';

		return $instance;
	}

}

class ET_Authors_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'et_authors',
			esc_html__( 'Extra - Authors', 'extra' ),
			array(
				'description' => esc_html__( 'Display a list of authors.', 'extra' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		global $wp_version;

		$query_args = array(
			'order'   => 'ASC',
			'orderby' => 'display_name',
		);

		// Alternative for deprecated `who` property and backward compatibility.
		if ( version_compare( $wp_version, '5.9-beta', '>=' ) ) {
			$query_args['capability'] = array( 'edit_posts' );
		} else {
			$query_args['who'] = 'authors';
		}

		$authors = get_users( $query_args );

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
		<div class="widget_content">
			<ul class="widget_list">
				<?php
				foreach ( $authors as $author ) {
					$count = count_user_posts( $author->ID );
					$count = sprintf( _n( '%d Post', '%d Posts', $count ), $count );
					$url = get_author_posts_url( $author->ID );
				?>
				<li>
					<a href="<?php echo esc_url( $url ); ?>" class="widget_list_portrait" rel="author">
						<?php echo get_avatar( $author->ID, 150, 'mystery', esc_attr( $author->display_name ) ); ?>
					</a>
					<a href="<?php echo esc_url( $url ); ?>" class="widget_list_author">
						<h3 class="title"><?php echo esc_html( $author->display_name ); ?></h3>
						<span class="post-meta"><?php echo esc_html( $count ); ?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : esc_html__( 'Our Authors', 'extra' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

}

class ET_Login_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'et_login',
			esc_html__( 'Extra - Login', 'extra' ),
			array(
				'description' => esc_html__( 'Display a login form.', 'extra' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		if ( is_user_logged_in() ) {
			return;
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
		<div class="widget_content">
			<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
				<ul class="widget_list">
					<li>
						<input class="input" type="text" name="log" placeholder="<?php esc_attr_e( 'USERNAME', 'extra' ); ?>">
					</li>
					<li>
						<input class="input" type="password" name="pwd" placeholder="<?php esc_attr_e( 'PASSWORD', 'extra' ); ?>">
					</li>
					<li>
						<button type="submit" class="button"><?php esc_html_e( 'Login', 'extra' ); ?></button>
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="post-meta"><?php esc_html_e( 'Lost my Password', 'extra' ); ?></a>
					</li>
				</ul>
			</form>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : esc_html__( 'Login', 'extra' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

}

class ET_Ads_Widget extends WP_Widget {

	function __construct() {
		parent::__construct( 'et_ads', esc_html__( 'Extra - Ads', 'extra' ), array( 'description' => esc_html__( 'Display ads.', 'extra' ) ) );
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
		<div class="widget_content clearfix">
		<?php foreach ( $instance['ads'] as $ad ) { ?>
		<?php $new_line = !empty( $ad['new_line'] ) ? ' new_line' : ''; ?>
			<div class="etad<?php echo esc_attr( $new_line ); ?>">
				<?php if ( !empty( $ad['img_url'] ) && !empty( $ad['link_url'] ) ) { ?>
					<a href="<?php echo esc_url( $ad['link_url'] ); ?>" target="_blank">
						<img src="<?php echo esc_url( $ad['img_url'] ); ?>" alt="<?php echo isset( $ad['img_alt_text'] ) ? esc_attr( $ad['img_alt_text'] ) : esc_attr__( 'Advertisement', 'extra' ); ?>" />
					</a>
				<?php } else if ( !empty( $ad['ad_html'] ) ) { ?>
					<?php echo $ad['ad_html']; ?>
				<?php } ?>
			</div>
		<?php } ?>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function get_ad_field_id( $field_name, $index ) {
		return 'widget-' . $this->id_base . '-' . $this->number . '-[ads][' . $index . '][' . $field_name . ']';
	}

	public function get_ad_field_name( $field_name, $index ) {
		return 'widget-' . $this->id_base . '[' . $this->number . '][ads][' . $index . '][' . $field_name . ']';
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';

		$ads = isset( $instance['ads'] ) ? $instance['ads'] : array(0);
		$ad_count = isset( $instance[ 'ad_count' ] ) ? $instance[ 'ad_count' ] : 1;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		?>
		<div class="et_ads_widget" id="et_ads_widget_<?php echo esc_attr( $this->number ); ?>">
			<input type="hidden" class="et_ads_ad_count" id="<?php echo $this->get_field_id( 'ad_count' ); ?>" name="<?php echo $this->get_field_name( 'ad_count' ); ?>" value="<?php echo esc_attr( $ad_count ); ?>" />

			<input type="hidden" class="et_ads_ad_widget_number" value="<?php echo $this->number; ?>" />

			<div class="et_ads_widget_ads_container">
				<?php

				$index = 0;
				foreach ( $ads as $ad ) {
					$img_url = isset( $ad['img_url'] ) ? $ad['img_url'] : '';
					$img_alt_text = isset( $ad['img_alt_text'] ) ? $ad['img_alt_text'] : esc_attr__( 'Advertisement', 'extra' );
					$link_url = isset( $ad['link_url'] ) ? $ad['link_url'] : '';
					$ad_html = isset( $ad['ad_html'] ) ? $ad['ad_html'] : '';
					$ad_new_line = isset( $ad['new_line'] ) ? intval( $ad['new_line'] ) : 0;
				?>
				<div class="et_ads_ad group" data-ad_id="<?php echo esc_attr( $index ); ?>">
					<div class="header"><?php printf( esc_html__( 'Ad #%d', 'extra' ), $index ); ?></div>
					<div class="content">
						<div class="delete_ad">X</div>
						<p class="field_wrap">
							<label for="<?php echo $this->get_ad_field_id( 'img_url', $index ); ?>"><?php esc_html_e( 'Image Url:', 'extra' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_ad_field_id( 'img_url', $index ); ?>" name="<?php echo $this->get_ad_field_name( 'img_url', $index ); ?>" type="text" value="<?php echo esc_attr( $img_url ); ?>" />
						</p>
						<p class="field_wrap">
							<label for="<?php echo esc_attr( $this->get_ad_field_id( 'img_alt_text', $index ) ); ?>"><?php esc_html_e( 'Image Alt Text:', 'extra' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_ad_field_id( 'img_alt_text', $index ) ); ?>" name="<?php echo esc_attr( $this->get_ad_field_name( 'img_alt_text', $index ) ); ?>" type="text" value="<?php echo esc_attr( $img_alt_text ); ?>" />
						</p>
						<p class="field_wrap">
							<label for="<?php echo $this->get_ad_field_id( 'link_url', $index ); ?>"><?php esc_html_e( 'Link Url:', 'extra' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_ad_field_id( 'link_url', $index ); ?>" name="<?php echo $this->get_ad_field_name( 'link_url', $index ); ?>" type="text" value="<?php echo esc_attr( $link_url ); ?>" />
						</p>

						<p><?php esc_html_e( 'OR', 'extra' ); ?></p>

						<p class="field_wrap">
							<label for="<?php echo $this->get_ad_field_id( 'ad_html', $index ); ?>"><?php esc_html_e( 'Ad HTML:', 'extra' ); ?></label>
							<textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_ad_field_id( 'ad_html', $index ); ?>" name="<?php echo $this->get_ad_field_name( 'ad_html', $index ); ?>"><?php echo esc_textarea( $ad_html ); ?></textarea>
						</p>

						<p class="field_wrap">
							<input id="<?php echo $this->get_ad_field_id( 'new_line', $index ); ?>" name="<?php echo $this->get_ad_field_name( 'new_line', $index ); ?>" value="1" type="checkbox" <?php checked( $ad_new_line === 1 ); ?> />&nbsp;<label for="<?php echo $this->get_ad_field_id( 'new_line', $index ); ?>"><?php esc_html_e( 'Start on New Line', 'extra' ); ?></label>
						</p>
					</div>
				</div>
				<?php $index++; ?>
				<?php } ?>
			</div>

			<button class="et_ads_add_ad"><?php esc_html_e( 'Add Ad', 'extra' ); ?></button>
			<script type="text/javascript">
				( function($) {
					$( document ).ready( function() {
						$(document).trigger({
							type: "et_ads_widget_init",
							$el: $('#et_ads_widget_<?php echo esc_js( $this->number );?>')
						});
					});
				} )(jQuery);
			</script>
		</div>

		<?php
	}

	public function update( $new_instance, $old_instance ) {
		add_filter( 'safe_style_css', array( $this, 'modify_safe_style_css' ) );

		$instance = array();

		$instance['title'] = !empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_html['script'] = array(
			'id'    => array(),
			'class' => array(),
			'type'  => array(),
			'async' => array(),
			'src'   => array(),
		);
		$allowed_html['ins'] = array(
			'id'             => array(),
			'class'          => array(),
			'style'          => array(),
			'data-ad-client' => array(),
			'data-ad-slot'   => array(),
		);

		$ad_count = 0;
		$ads = array();
		foreach ( $new_instance['ads'] as $key => $ad_data ) {
			$ad = array();

			$ad['img_url'] = !empty( $ad_data['img_url'] ) ? esc_url_raw( $ad_data['img_url'] ) : '';
			$ad['img_alt_text'] = !empty( $ad_data['img_alt_text'] ) ? esc_attr( $ad_data['img_alt_text'] ) : '';
			$ad['link_url'] = !empty( $ad_data['link_url'] ) ? esc_url_raw( $ad_data['link_url'] ) : '';
			$ad['ad_html'] = !empty( $ad_data['ad_html'] ) ? wp_kses( $ad_data['ad_html'], $allowed_html ) : '';
			$ad['new_line'] = !empty( $ad_data['new_line'] ) ? 1 : 0;

			$ads[] = $ad;
			$ad_count++;
		}

		$instance['ad_count'] = $ad_count;
		$instance['ads'] = $ads;

		remove_filter( 'safe_style_css', array( $this, 'modify_safe_style_css' ) );

		return $instance;
	}

	public function modify_safe_style_css( $styles ) {
		$styles[] = 'display';
		return $styles;
	}

}

class ET_Recent_Posts_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_et_recent_entries',
			'description' => esc_html__( 'Your site&#8217;s most recent Posts.', 'extra' ),
		);
		parent::__construct( 'et-recent-posts', esc_html__( 'Extra - Recent Posts', 'extra' ), $widget_ops );
		$this->alt_option_name = 'et_widget_recent_entries';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_recent_posts', 'extra' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Posts', 'extra' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
		$show_thumb = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : false;
		$show_categories = isset( $instance['show_categories'] ) ? $instance['show_categories'] : false;

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		) ) );

		if ($r->have_posts()) :
?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) echo $args['before_title'] . $title . $args['after_title']; ?>
		<ul class="widget_list">
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<?php

				$color = extra_get_post_category_color();
				$post_format = et_get_post_format();

				if ( $show_thumb ) {
					if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
						$thumb_src = et_get_post_format_thumb( $post_format );
						$img_style = sprintf( 'background-color:%s', $color );
					} else if ( 'gallery' == $post_format ) {
						$thumb_src = et_get_gallery_post_format_thumb();
					} else if ( !get_post_thumbnail_id() ) {
						$thumb_src = et_get_post_format_thumb( 'text', 'icon' );
						$img_style = sprintf( 'background-color:%s', $color );
					}

					$thumb_args = array(
						'a_class'   => array('widget_list_thumbnail'),
						'size'      => 'extra-image-square-small',
						'thumb_src' => !empty( $thumb_src ) ? $thumb_src : '',
						'img_style' => !empty( $img_style ) ? $img_style : '',
					);

					?>
					<?php echo et_extra_get_post_thumb( $thumb_args ); ?>
				<?php } ?>
				<div class="post_info">
					<a href="<?php the_permalink(); ?>" class="title"><?php get_the_title() ? the_title() : the_ID(); ?></a>
					<?php
					$meta_args = array(
						'post_date'     => $show_date,
						'categories'    => $show_categories,
						'author_link'   => false,
						'comment_count' => false,
						'rating_stars'  => false,
					);
					?>
					<div class="post-meta">
						<?php echo et_extra_display_post_meta( $meta_args ); ?>
					</div>
				</div>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		</ul>
		<?php echo $args['after_widget']; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'et_widget_recent_posts', $cache, 'extra' );
		} else {
			ob_end_flush();
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$instance['show_thumb'] = isset( $new_instance['show_thumb'] ) ? (bool) $new_instance['show_thumb'] : false;
		$instance['show_categories'] = isset( $new_instance['show_categories'] ) ? (bool) $new_instance['show_categories'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['et_widget_recent_entries'] ) ) {
			delete_option( 'et_widget_recent_entries' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'et_widget_recent_posts', 'extra' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : true;
		$show_thumb = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : true;
		$show_categories = isset( $instance['show_categories'] ) ? (bool) $instance['show_categories'] : true;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'extra' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php esc_html_e( 'Display post date?', 'extra' ); ?></label></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_thumb ); ?> id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>"><?php esc_html_e( 'Display post thumbnail?', 'extra' ); ?></label></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_categories ); ?> id="<?php echo $this->get_field_id( 'show_categories' ); ?>" name="<?php echo $this->get_field_name( 'show_categories' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_categories' ); ?>"><?php esc_html_e( 'Display post categories?', 'extra' ); ?></label></p>
<?php
	}

}

class ET_Recent_Reviews_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_et_recent_reviews',
			'description' => esc_html__( 'Your site&#8217;s most recent Reviews (posts which have review).', 'extra' ),
		);
		parent::__construct( 'et-recent-reviews', esc_html__( 'Extra - Recent Reviews', 'extra' ), $widget_ops );
		$this->alt_option_name = 'et_widget_recent_reviews';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_recent_reviews', 'extra' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Reviews', 'extra' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$r = new WP_Query( apply_filters( 'widget_recent_reviews_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'meta_key'            => '_post_review_box_breakdowns_score',
			'meta_value'          => '0',
			'meta_compare'        => '!=',
		) ) );

		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="widget_list">
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<?php
				$color = extra_get_post_category_color();
				$score = round( get_post_meta( get_the_ID(), '_post_review_box_breakdowns_score', true ) );
				?>
				<a href="<?php the_permalink(); ?>" data-video-id="<?php echo esc_attr( get_the_ID() ); ?>" class="title"><?php get_the_title() ? the_title() : the_ID(); ?></a>
				<div class="review-breakdowns">
					<div class="score-bar-bg">
						<span class="score-bar" style="width:<?php echo esc_attr( $score ); ?>%; background-color: <?php echo esc_attr( $color ); ?>;">
							<span class="score-text"><?php echo esc_html( sprintf( __( 'Score: %1$s', 'extra' ), $score . '%' ) );?></span>
						</span>
					</div>
				</div>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'et_widget_recent_reviews', $cache, 'extra' );
		} else {
			ob_end_flush();
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['et_widget_recent_reviews'] ) ) {
			delete_option( 'et_widget_recent_reviews' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'et_widget_recent_reviews', 'extra' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'extra' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}

}

class ET_Recent_Videos_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_et_recent_videos',
			'description' => esc_html__( 'Your site&#8217;s most recent Videos (Posts which use video format).', 'extra' ),
		);
		parent::__construct( 'et-recent-videos', esc_html__( 'Extra - Recent Videos', 'extra' ), $widget_ops );
		$this->alt_option_name = 'et_widget_recent_videos';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_recent_videos', 'extra' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract( $args );

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recent Videos', 'extra' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$recent_video_posts = new WP_Query( apply_filters( 'widget_recent_videos_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'tax_query'           => array(
				array(
					'taxonomy' => ET_POST_FORMAT,
					'field'    => 'slug',
					'terms'    => ET_POST_FORMAT_PREFIX . 'video',
				),
			),
		) ) );

		if ($recent_video_posts->have_posts()) :
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<div class="widget_video_wrapper">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/pagination-loading.gif" alt="<?php esc_html_e( 'Loading...', 'extra' ); ?>" class="widget_video_loading">
		</div><!-- .widget_video_wrapper -->
		<div class="widget_content">
			<?php while ( $recent_video_posts->have_posts() ) : $recent_video_posts->the_post(); ?>
				<script type="text/template" class="widget-video-item widget-video-item-<?php echo esc_attr( get_the_ID() ); ?>">
					<?php
					$video_urls = get_post_meta( get_the_ID(), '_video_format_urls', true );

					if ( !empty( $video_urls ) ) {
						$video_embed = extra_get_video_embed( $video_urls );
						echo $video_embed;

						// Display cover image
						if ( has_post_thumbnail() && $video_embed ) {
							$thumbnail_id = get_post_thumbnail_id();
							$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, extra_get_column_thumbnail_size() );
							if ( isset( $thumbnail_src[0] ) ) {
							?>
							<div class="video-overlay" style="background-image: url(<?php esc_attr_e( $thumbnail_src[0] ); ?>);">
								<div class="video-overlay-hover">
									<a href="#" class="video-play-button"></a>
								</div>
							</div>
							<?php
							}
						}
					}
					?>
				</script><!-- .widget-video-item -->
			<?php endwhile; ?>
			<script type="text/template" class="widget-video-item widget-video-item-empty">
				<h4 class="no-video-title"><?php esc_html_e( 'No Video Found' ); ?></h4>
			</script>
		</div><!-- .widget_content -->
		<?php $recent_video_posts->rewind_posts(); ?>
		<ul class="widget_list">
		<?php while ( $recent_video_posts->have_posts() ) : $recent_video_posts->the_post(); ?>
			<li>
				<?php
				$color = extra_get_post_category_color();
				$post_format = et_get_post_format();
				?>
					<a href="<?php the_permalink(); ?>" data-video-id="<?php echo esc_attr( get_the_ID() ); ?>" class="title"><?php get_the_title() ? the_title() : the_ID(); ?></a>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		</ul>
		<?php echo $after_widget; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'et_widget_recent_videos', $cache, 'extra' );
		} else {
			ob_end_flush();
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['et_widget_recent_videos'] ) ) {
			delete_option( 'et_widget_recent_videos' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'et_widget_recent_videos', 'extra' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'extra' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}

}

class ET_Recent_Comments_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_et_recent_comments',
			'description' => esc_html__( 'Your site&#8217;s most recent comments.', 'extra' ),
		);
		parent::__construct( 'et-recent-comments', esc_html__( 'Extra - Recent Comments', 'extra' ), $widget_ops );
		$this->alt_option_name = 'et_widget_recent_comments';

		add_action( 'comment_post', array($this, 'flush_widget_cache') );
		add_action( 'edit_comment', array($this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
	}

	function flush_widget_cache() {
		wp_cache_delete( 'et_widget_recent_comments', 'extra' );
	}

	function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'et_widget_recent_comments', 'extra' );
		}
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Recent Comments', 'extra' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		/**
		 * Filter the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see get_comments()
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'type'        => 'comment',
			'status'      => 'approve',
			'post_status' => 'publish',
		) ) );

		$output .= $args['before_widget'];
		if ( $title )
			$output .= $args['before_title'] . $title . $args['after_title'];

		$output .= '<ul id="recentcomments" class="widget_list">';
		if ( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( strval( get_option( 'permalink_structure' ) ), '%category%' ), false );

			foreach ( (array) $comments as $comment) {
				$date = '';

				if ( $show_date && isset( $comment->comment_date ) ) {
					$date_format            = strval( get_option( 'date_format' ) );
					$comment_date           = strtotime( strval( $comment->comment_date ) );
					$formatted_comment_date = function_exists( 'wp_date' ) ? wp_date( $date_format, $comment_date ) : date_i18n( $date_format, $comment_date );
					$date                   = sprintf( '<span class="date">%s</span>', $formatted_comment_date );
				}

				$output .= '<li class="recentcomments">' . /* translators: comments widget: 1: comment author, 2: (optional) comment date , 3: post link */ sprintf( et_get_safe_localization( _x( '%1$s %2$s <div class="post-title">on %3$s</div>', 'widgets' ) ), sprintf( '<span class="author">%s</span>', get_comment_author_link() ), $date, '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>' ) . '</li>';
			}
		}
		$output .= '</ul>';
		$output .= $args['after_widget'];

		echo $output;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = $output;
			wp_cache_set( 'et_widget_recent_comments', $cache, 'extra' );
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['et_widget_recent_comments'] ) ) {
			delete_option( 'et_widget_recent_comments' );
		}

		return $instance;
	}

	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : true;
	?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of comments to show:', 'extra' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php esc_html_e( 'Display comment date?', 'extra' ); ?></label></p>
	<?php
	}

}

function extra_widgets_init() {
	register_widget( 'ET_Social_Followers_Widget' );
	register_widget( 'ET_Recent_Tweets_Widget' );
	register_widget( 'ET_Authors_Widget' );
	register_widget( 'ET_Login_Widget' );
	register_widget( 'ET_Ads_Widget' );
	register_widget( 'ET_Recent_Posts_Widget' );
	register_widget( 'ET_Recent_Videos_Widget' );
	register_widget( 'ET_Recent_Reviews_Widget' );
	register_widget( 'ET_Recent_Comments_Widget' );
}

function extra_widgets_init_widgets( $framework_widgets ) {
	foreach ($framework_widgets as $key => $framework_widget) {
		if ( 'ET_Ad_Widget' == $framework_widget ) {
			unset( $framework_widgets[$key] );
		}
	}

	return $framework_widgets;
}

add_action( 'et_widgets_init_widgets', 'extra_widgets_init_widgets' );
add_action( 'widgets_init', 'extra_widgets_init' );
