<?php
/**
 * Class TestInit
 *
 * @package soft-hyphenate
 */

use HappyPrime\SoftHyphenate;
use HappyPrime\SoftHyphenate\Init;

/**
 * Test the filters that apply hyphenation.
 */
class TestInit extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	public function setUp(): void {
		parent::setUp();

		update_option( SoftHyphenate\OPTION_NAME, 'hyphenat-ion' );
	}

	/**
	 * Tear down the test.
	 */
	public function tearDown(): void {
		delete_option( SoftHyphenate\OPTION_NAME );

		parent::tearDown();
	}

	/**
	 * Test that content is hyphenated outside of feeds.
	 */
	public function test_content_is_hyphenated(): void {
		$this->go_to( home_url( '/' ) );

		$this->assertSame( "hyphenat\u{00AD}ion", Init::hyphenation( 'hyphenation' ) );
	}

	/**
	 * Test that content in feeds is not hyphenated.
	 */
	public function test_feed_content_is_not_hyphenated(): void {
		$this->go_to( get_feed_link() );

		$this->assertTrue( is_feed() );
		$this->assertSame( 'hyphenation', Init::hyphenation( 'hyphenation' ) );
	}

	/**
	 * Test that values other than strings pass through unchanged.
	 */
	public function test_non_string_values_pass_through(): void {
		$this->assertNull( Init::hyphenation( null ) );
		$this->assertSame( [ 'hyphenation' ], Init::hyphenation( [ 'hyphenation' ] ) );
	}

	/**
	 * Test that shortcode attributes reach the shortcode unchanged.
	 */
	public function test_shortcode_attributes_are_not_hyphenated(): void {
		$received = null;

		add_shortcode(
			'soft_hyphenate_test',
			function ( $atts ) use ( &$received ) {
				$received = $atts['word'] ?? null;

				return '<span>hyphenation</span>';
			}
		);

		$this->go_to( home_url( '/' ) );

		$content = apply_filters( 'the_content', '[soft_hyphenate_test word="hyphenation"] hyphenation' );

		remove_shortcode( 'soft_hyphenate_test' );

		$this->assertSame( 'hyphenation', $received );
		$this->assertIsString( $content );
		$this->assertSame( 2, substr_count( $content, "hyphenat\u{00AD}ion" ) );
	}
}
