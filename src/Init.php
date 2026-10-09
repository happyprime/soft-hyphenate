<?php
/**
 * Initialize the plugin.
 *
 * @package HappyPrime\SoftHyphenate
 */

namespace HappyPrime\SoftHyphenate;

/**
 * Initialize the plugin.
 */
class Init {
	/**
	 * Add hooks.
	 */
	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'admin' ] );
		add_filter( 'the_title', [ __CLASS__, 'hyphenation' ] );
		add_filter( 'the_content', [ __CLASS__, 'hyphenation' ] );
	}

	/**
	 * Initialize the admin.
	 */
	public static function admin(): void {
		if ( is_admin() ) {
			Admin::init();
		}
	}

	/**
	 * Add soft hyphens to content.
	 *
	 * Values other than strings are returned unchanged. Other code applies
	 * these filters too, and a string type declaration turns a stray null
	 * into a fatal error.
	 *
	 * @param mixed $content Content to be soft-hyphenated.
	 *
	 * @return mixed The content with soft hyphens added.
	 */
	public static function hyphenation( $content ) {
		if ( ! is_string( $content ) ) {
			return $content;
		}

		// Suggestions suit the site's own layout. Feed readers lay text out their own way.
		if ( is_feed() ) {
			return $content;
		}

		$hyphenation = new Hyphenate();

		return $hyphenation->content( $content );
	}
}
