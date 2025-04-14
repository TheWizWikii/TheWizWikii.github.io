<?php

/**
 * Provide utilities to interact and replace the current post akin to setup_postdata()
 * but in a stacking manner.
 *
 * @since 4.0
 */
class ET_Post_Stack {
	/**
	 * Post override stack.
	 *
	 * @var (WP_Post|null)[]
	 */
	protected static $stack = array();

	/**
	 * Get the top post from the stack or the current one if the stack is empty.
	 *
	 * @since 4.0
	 *
	 * @param integer $offset Offset from the end of the array, 0 being the last post. Use negative integers.
	 *
	 * @return WP_Post|null
	 */
	public static function get( $offset = 0 ) {
		global $post;

		$index = count( self::$stack ) - 1 + $offset;

		if ( empty( self::$stack ) && 0 === $index ) {
			return $post;
		}

		return isset( self::$stack[ $index ] ) ? self::$stack[ $index ] : null;
	}

	/**
	 * Pop the top post off of the stack.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public static function pop() {
		array_pop( self::$stack );
	}

	/**
	 * Setup a post as the global $post.
	 *
	 * @since 4.0
	 *
	 * @param WP_Post|null $with
	 * @param boolean      $force
	 *
	 * @return void
	 */
	protected static function setup( $with, $force = false ) {
		global $post;

		if ( $force || ! ( $with && $post && $with->ID === $post->ID ) ) {
			$post = $with;
			setup_postdata( $post );
		}
	}

	/**
	 * Equivalent to setup_postdata() but keeps a stack of posts so it can be nested.
	 * Pushes the specified post on the stack.
	 *
	 * @since 4.0
	 *
	 * @param WP_Post $with
	 *
	 * @return void
	 */
	public static function replace( $with ) {
		global $post;

		$force = empty( self::$stack );

		if ( empty( self::$stack ) ) {
			// Add the current post as the first in the stack even if it does not exist.
			self::$stack[] = $post;
		}

		self::$stack[] = $with;

		self::setup( $with, $force );
	}

	/**
	 * Restores the last post from the stack.
	 * The final restore will setup the post that was setup when the stack was last empty.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public static function restore() {
		self::pop();
		self::reset();
	}

	/**
	 * Resets the post to the one at the top of the stack.
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public static function reset() {
		if ( ! empty( self::$stack ) ) {
			self::setup( self::get() );
		}
	}

	/**
	 * Returns the $post for the current true WordPress query post ignoring any posts that may be
	 * forced using setup_postdata().
	 *
	 * @since 4.0
	 *
	 * @return WP_Post|null
	 */
	public static function get_main_post() {
		global $wp_query;

		if ( ! $wp_query || 0 === $wp_query->post_count ) {
			// Handle special case where there is no current post but once the_content()
			// gets called the global $post is polluted and will no longer reset to null.
			return null;
		}

		if ( empty( $wp_query->post ) ) {
			return null;
		}

		return $wp_query->post;
	}

	/**
	 * Returns the post ID for the current true WordPress query post.
	 * See ::get_main_post() for more information.
	 *
	 * @since 4.0
	 *
	 * @return integer
	 */
	public static function get_main_post_id() {
		$post = self::get_main_post();

		return $post ? (int) $post->ID : 0;
	}
}
