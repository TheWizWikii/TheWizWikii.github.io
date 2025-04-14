<?php

namespace Easy_Plugins\Table_Of_Contents\Cord;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Replace `<br />` tags with parameter.
 *
 * @since 2.0.8
 *
 * @param string $string
 * @param string $to
 *
 * @return string
 */
function br2( $string, $to = "\r\n" ) {

	$string = preg_replace( '`<br[/\s]*>`i', $to, $string );

	return $string;
}

/**
 * Replace `<br />` tags with new lines.
 *
 * @link https://stackoverflow.com/a/27509016/5351316
 *
 * @since 2.0.8
 *
 * @param string $string
 *
 * @return string
 */
function br2nl( $string ) {

	return br2( $string );
}

/**
 * Pulled from WordPress formatting functions.
 *
 * Edited to add space before self closing tags.
 *
 * @since 2.0
 *
 * @param string $text
 *
 * @return string|string[]
 */
function force_balance_tags( $text ) {
	$tagstack  = array();
	$stacksize = 0;
	$tagqueue  = '';
	$newtext   = '';
	// Known single-entity/self-closing tags
	$single_tags = array( 'area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source' );
	// Tags that can be immediately nested within themselves
	$nestable_tags = array( 'blockquote', 'div', 'object', 'q', 'span' );

	// WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace( '< !--', '<    !--', $text );
	// WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace( '#<([0-9]{1})#', '&lt;$1', $text );

	/**
	 * Matches supported tags.
	 *
	 * To get the pattern as a string without the comments paste into a PHP
	 * REPL like `php -a`.
	 *
	 * @see https://html.spec.whatwg.org/#elements-2
	 * @see https://w3c.github.io/webcomponents/spec/custom/#valid-custom-element-name
	 *
	 * @example
	 * ~# php -a
	 * php > $s = [paste copied contents of expression below including parentheses];
	 * php > echo $s;
	 */
	$tag_pattern = (
		'#<' . // Start with an opening bracket.
		'(/?)' . // Group 1 - If it's a closing tag it'll have a leading slash.
		'(' . // Group 2 - Tag name.
			// Custom element tags have more lenient rules than HTML tag names.
			'(?:[a-z](?:[a-z0-9._]*)-(?:[a-z0-9._-]+)+)' .
				'|' .
			// Traditional tag rules approximate HTML tag names.
			'(?:[\w:]+)' .
		')' .
		'(?:' .
			// We either immediately close the tag with its '>' and have nothing here.
			'\s*' .
			'(/?)' . // Group 3 - "attributes" for empty tag.
				'|' .
			// Or we must start with space characters to separate the tag name from the attributes (or whitespace).
			'(\s+)' . // Group 4 - Pre-attribute whitespace.
			'([^>]*)' . // Group 5 - Attributes.
		')' .
		'>#' // End with a closing bracket.
	);

	while ( preg_match( $tag_pattern, $text, $regex ) ) {
		$full_match        = $regex[0];
		$has_leading_slash = ! empty( $regex[1] );
		$tag_name          = $regex[2];
		$tag               = strtolower( $tag_name );
		$is_single_tag     = in_array( $tag, $single_tags, true );
		$pre_attribute_ws  = isset( $regex[4] ) ? $regex[4] : '';
		$attributes        = trim( isset( $regex[5] ) ? $regex[5] : $regex[3] );
		$has_self_closer   = '/' === substr( $attributes, -1 );

		$newtext .= $tagqueue;

		$i = strpos( $text, $full_match );
		$l = strlen( $full_match );

		// Clear the shifter.
		$tagqueue = '';
		if ( $has_leading_slash ) { // End Tag.
			// If too many closing tags.
			if ( $stacksize <= 0 ) {
				$tag = '';
				// Or close to be safe $tag = '/' . $tag.

				// If stacktop value = tag close value, then pop.
			} elseif ( $tagstack[ $stacksize - 1 ] === $tag ) { // Found closing tag.
				$tag = '</' . $tag . '>'; // Close Tag.
				array_pop( $tagstack );
				$stacksize--;
			} else { // Closing tag not at top, search for it.
				for ( $j = $stacksize - 1; $j >= 0; $j-- ) {
					if ( $tagstack[ $j ] === $tag ) {
						// Add tag to tagqueue.
						for ( $k = $stacksize - 1; $k >= $j; $k-- ) {
							$tagqueue .= '</' . array_pop( $tagstack ) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag.
			if ( $has_self_closer ) { // If it presents itself as a self-closing tag...
				// ...but it isn't a known single-entity self-closing tag, then don't let it be treated as such and
				// immediately close it with a closing tag (the tag will encapsulate no text as a result)
				if ( ! $is_single_tag ) {
					$attributes = trim( substr( $attributes, 0, -1 ) ) . "></$tag";
				}
			} elseif ( $is_single_tag ) { // ElseIf it's a known single-entity tag but it doesn't close itself, do so
				$pre_attribute_ws = ' ';
				$attributes      .= 0 < strlen( $attributes ) ? ' /' : '/'; // EDIT: If there are attributes, add space before closing tag to match how WP insert br, hr and img tags.
			} else { // It's not a single-entity tag.
				// If the top of the stack is the same as the tag we want to push, close previous tag.
				if ( $stacksize > 0 && ! in_array( $tag, $nestable_tags, true ) && $tagstack[ $stacksize - 1 ] === $tag ) {
					$tagqueue = '</' . array_pop( $tagstack ) . '>';
					$stacksize--;
				}
				$stacksize = array_push( $tagstack, $tag );
			}

			// Attributes.
			if ( $has_self_closer && $is_single_tag ) {
				// We need some space - avoid <br/> and prefer <br />.
				$pre_attribute_ws = ' ';
			}

			$tag = '<' . $tag . $pre_attribute_ws . $attributes . '>';
			// If already queuing a close tag, then put this tag on too.
			if ( ! empty( $tagqueue ) ) {
				$tagqueue .= $tag;
				$tag       = '';
			}
		}
		$newtext .= substr( $text, 0, $i ) . $tag;
		$text     = substr( $text, $i + $l );
	}

	// Clear Tag Queue.
	$newtext .= $tagqueue;

	// Add remaining text.
	$newtext .= $text;

	while ( $x = array_pop( $tagstack ) ) {
		$newtext .= '</' . $x . '>'; // Add remaining tags to close.
	}

	// WP fix for the bug with HTML comments.
	$newtext = str_replace( '< !--', '<!--', $newtext );
	$newtext = str_replace( '<    !--', '< !--', $newtext );

	return $newtext;
}

/**
 * Multibyte substr_replace(). The mbstring library does not come with a multibyte equivalent of substr_replace().
 * This function behaves exactly like substr_replace() even when the arguments are arrays.
 *
 * @link https://gist.github.com/stemar/8287074
 *
 * @since 2.0
 *
 * @param      $string
 * @param      $replacement
 * @param      $start
 * @param null $length
 *
 * @return array|string
 */
if ( ! function_exists( __NAMESPACE__ . '\mb_substr_replace' ) ) :
function mb_substr_replace( $string, $replacement, $start, $length = null ) {

	if ( is_array( $string ) ) {

		$num = count( $string );

		// $replacement
		$replacement = is_array( $replacement ) ? array_slice( $replacement, 0, $num ) : array_pad( array( $replacement ), $num, $replacement );

		// $start
		if ( is_array( $start ) ) {
			$start = array_slice( $start, 0, $num );
			foreach ( $start as $key => $value ) {
				$start[ $key ] = is_int( $value ) ? $value : 0;
			}
		} else {
			$start = array_pad( array( $start ), $num, $start );
		}

		// $length
		if ( ! isset( $length ) ) {
			$length = array_fill( 0, $num, 0 );
		} elseif ( is_array( $length ) ) {
			$length = array_slice( $length, 0, $num );
			foreach ( $length as $key => $value ) {
				$length[ $key ] = isset( $value ) ? ( is_int( $value ) ? $value : $num ) : 0;
			}
		} else {
			$length = array_pad( array( $length ), $num, $length );
		}

		// Recursive call
		return array_map( __FUNCTION__, $string, $replacement, $start, $length );
	}

	preg_match_all( '/./us', (string) $string, $smatches );
	preg_match_all( '/./us', (string) $replacement, $rmatches );

	if ( $length === null ) {

		$length = mb_strlen( $string );
	}

	array_splice( $smatches[0], $start, $length, $rmatches[0] );

	return join( $smatches[0] );
}
endif;
/**
 * Returns a string with all items from the $find array replaced with their matching
 * items in the $replace array.  This does a one to one replacement (rather than globally).
 *
 * This function is multibyte safe.
 *
 * $find and $replace are arrays, $string is the haystack.  All variables are passed by reference.
 *
 * @since  1.0
 *
 * @param bool   $find
 * @param bool   $replace
 * @param string $string
 *
 * @return mixed|string
 */
if ( ! function_exists( __NAMESPACE__ . '\mb_find_replace' ) ) :
function mb_find_replace( &$find = false, &$replace = false, &$string = '' ) {

	if ( is_array( $find ) && is_array( $replace ) && $string ) {

		// check if multibyte strings are supported
		if ( function_exists( 'mb_strpos' ) ) {


			for ( $i = 0; $i < count( $find ); $i ++ ) {

				$needle = $find[ $i ];
				$start  = mb_strpos( $string, $needle );

				// If heading can not be found, let try decoding entities to see if it can be found.
				if ( false === $start ) {

					$needle = html_entity_decode(
						$needle,
						ENT_QUOTES,
						get_option( 'blog_charset' )
					);

					$umlauts = false;
          			$umlauts = apply_filters( 'eztoc_modify_umlauts', $umlauts );
          			if($umlauts){
						$string = html_entity_decode(
							$string,
							ENT_QUOTES,
							get_option( 'blog_charset' )
						);
					}

					$needle = str_replace(array('’','“','”'), array('\'','"','"'), $needle);

                    $start = mb_strpos( $string, $needle );
				}

				/*
				 * `mb_strpos()` can return `false`. Only process `mb_substr_replace()` if position in string is found.
				 */
				if ( is_int( $start ) ) {

					$length = mb_strlen( $needle );
					$apply_new_function = apply_filters('eztoc_mb_subtr_replace',false,$string, $replace[ $i ], $start, $length);
					$string = $apply_new_function?$apply_new_function:mb_substr_replace( $string, $replace[ $i ], $start, $length );
				}

			}

		} else {

			for ( $i = 0; $i < count( $find ); $i ++ ) {

				$start  = strpos( $string, $find[ $i ] );
				$length = strlen( $find[ $i ] );

				/*
				 * `strpos()` can return `false`. Only process `substr_replace()` if position in string is found.
				 */
				if ( is_int( $start ) ) {

					$string = substr_replace( $string, $replace[ $i ], $start, $length );
				}
			}
		}
	}

	return $string;
}
endif;

if( ! function_exists( __NAMESPACE__ . '\insertElementByPTag' ) ):
/**
 * insertElementByPTag Method
 *
 * @since 2.0.36
 * @param $content
 * @param $toc
 * @return false|string
 * @throws \DOMException
*/
function insertElementByPTag($content, $toc)
{
	$find = array('</p>');
	$replace = array('</p>' . $toc);
	return mb_find_replace( $find, $replace, $content );
}
endif;

if( ! function_exists( __NAMESPACE__ . '\insertElementByImgTag' ) ):
/**
 * insertElementByImgTag Method
 *
 * @since 2.0.60
 * @param $content
 * @param $toc
 * @return false|string
 * @throws \DOMException
*/
function insertElementByImgTag($content, $toc)
{
	$find = array('</figure>');
	$replace = array('</figure>' . $toc);
	return mb_find_replace( $find, $replace, $content );
}
endif;