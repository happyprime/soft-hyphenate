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
	 * @param string $content Content to be soft-hyphenated.
	 *
	 * @return string The post content with soft hyphens added.
	 */
	public static function hyphenation( string $content ): string {
		$hyphenation = new Hyphenate();

		return $hyphenation->content( $content );
	}
}
