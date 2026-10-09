<?php
/**
 * Class TestHyphenation
 *
 * Tests the hyphenation of text.
 *
 * @package soft-hyphenate
 */

use HappyPrime\SoftHyphenate\Hyphenate;
use HappyPrime\SoftHyphenate;

/**
 * Test the hyphenation of text.
 */
class TestHyphenation extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	public function setUp(): void {
		parent::setUp();

		$suggestions = implode(
			"\n",
			[
				'hyphenat-ion',
				'pascal-case',
				'pascalcase-word',
				'elem-ent',
				'funky🥃whisk-ey',
			]
		);

		update_option( SoftHyphenate\OPTION_NAME, $suggestions );
	}

	/**
	 * Tear down the test.
	 */
	public function tearDown(): void {
		parent::tearDown();

		delete_option( SoftHyphenate\OPTION_NAME );
	}

	/**
	 * Add a data provider for the test.
	 *
	 * @return array<array<string>>
	 */
	public function data_provider_for_test_hyphenation_of_content(): array {
		return [
			[
				'A string of text with the word that expects hyphenation.',
				'A string of text with the word that expects hyphenat' . "\u{00AD}" . 'ion.',
			],
			[
				'Multiple hyphenation instances of hyphenation should be handled.',
				'Multiple hyphenat' . "\u{00AD}" . 'ion instances of hyphenat' . "\u{00AD}" . 'ion should be handled.',
			],
			[
				'Hyphenation is likely to be capitalized at the beginning of a sentence.',
				'Hyphenat' . "\u{00AD}" . 'ion is likely to be capitalized at the beginning of a sentence.',
			],
			[
				'Someone may stress HYPHENATION with all caps.',
				'Someone may stress HYPHENAT' . "\u{00AD}" . 'ION with all caps.',
			],
			[
				'PascalCase is unlikely, but it should be accounted for.',
				'Pascal' . "\u{00AD}" . 'Case is unlikely, but it should be accounted for.',
			],
			[
				'Something PascalCase should be handled anywhere in a string.',
				'Something Pascal' . "\u{00AD}" . 'Case should be handled anywhere in a string.',
			],
			[
				'A three-hump PascalCaseWord should be handled anywhere in a string.',
				'A three-hump PascalCase' . "\u{00AD}" . 'Word should be handled anywhere in a string.',
			],
			[
				'Attributes <a href="https://example.org/hyphenation">hyphenation</a> should be ignored.',
				'Attributes <a href="https://example.org/hyphenation">hyphenat' . "\u{00AD}" . 'ion</a> should be ignored.',
			],
			[
				'<CustomElement>custom element tags</CustomElement> should be ignored.',
				'<CustomElement>custom elem' . "\u{00AD}" . 'ent tags</CustomElement> should be ignored.',
			],
			[
				'Strings with 🕺 should be processed for hyphenation properly. 👋🏻',
				'Strings with 🕺 should be processed for hyphenat' . "\u{00AD}" . 'ion properly. 👋🏻',
			],
			[
				'Plan on strange things like funky🥃whiskey.',
				'Plan on strange things like funky🥃whisk' . "\u{00AD}" . 'ey.',
			],
			[
				'<p>I remember being blown away in the 90s by the hacker battle of Kevin Mitnick and Tsutomu Shimomura in <a href="https://archive.org/details/takedownpursuita00shim/mode/1up"><em>Takedown</em></a>. It was an exciting early glimpse into how anything could be possible with computers.</p>',
				'<p>I remember being blown away in the 90s by the hacker battle of Kevin Mitnick and Tsutomu Shimomura in <a href="https://archive.org/details/takedownpursuita00shim/mode/1up"><em>Takedown</em></a>. It was an exciting early glimpse into how anything could be possible with computers.</p>',
			],
		];
	}

	/**
	 * Test the hyphenation of text.
	 *
	 * @dataProvider data_provider_for_test_hyphenation_of_content
	 *
	 * @param string $original The original text.
	 * @param string $expected The expected text.
	 */
	public function test_hyphenation_of_content( string $original, string $expected ): void {
		$hyphenation = new Hyphenate();

		$this->assertEquals( $expected, $hyphenation->content( $original ) );
	}

	/**
	 * Provide markup with text inside tags that are left untouched.
	 *
	 * @return array<array<string>>
	 */
	public function data_provider_for_test_skipped_tags(): array {
		$shy = "\u{00AD}";

		return [
			[
				'<p>Call <code>hyphenation()</code> for hyphenation.</p>',
				'<p>Call <code>hyphenation()</code> for hyphenat' . $shy . 'ion.</p>',
			],
			[
				"<pre class=\"wp-block-code\"><code>// hyphenation\nhyphenation();</code></pre><p>hyphenation</p>",
				"<pre class=\"wp-block-code\"><code>// hyphenation\nhyphenation();</code></pre><p>hyphenat" . $shy . 'ion</p>',
			],
			[
				'<pre>hyphenation</pre><kbd>Hyphenation</kbd><samp>HYPHENATION</samp>',
				'<pre>hyphenation</pre><kbd>Hyphenation</kbd><samp>HYPHENATION</samp>',
			],
			[
				'<pre><code>hyphenation</code> hyphenation</pre> hyphenation',
				'<pre><code>hyphenation</code> hyphenation</pre> hyphenat' . $shy . 'ion',
			],
			[
				'<CODE>hyphenation</CODE> hyphenation',
				'<CODE>hyphenation</CODE> hyphenat' . $shy . 'ion',
			],
			[
				'</code>hyphenation <code>hyphenation</code> hyphenation',
				'</code>hyphenat' . $shy . 'ion <code>hyphenation</code> hyphenat' . $shy . 'ion',
			],
		];
	}

	/**
	 * Test that text inside code, pre, kbd, and samp is not hyphenated.
	 *
	 * @dataProvider data_provider_for_test_skipped_tags
	 *
	 * @param string $original The original markup.
	 * @param string $expected The expected markup.
	 */
	public function test_skipped_tags( string $original, string $expected ): void {
		$hyphenation = new Hyphenate();

		$this->assertSame( $expected, $hyphenation->content( $original ) );
	}

	/**
	 * Test that text around words survives when a text node spans lines.
	 */
	public function test_multiline_text_keeps_leading_punctuation(): void {
		$hyphenation = new Hyphenate();
		$shy         = "\u{00AD}";

		$this->assertSame(
			"<div>\n\t(Hyphenation,\nhyphenation.)\n</div>",
			str_replace( $shy, '', $hyphenation->content( "<div>\n\t(Hyphenation,\nhyphenation.)\n</div>" ) )
		);
		$this->assertSame(
			"<div>// hyphenat{$shy}ion\nhyphenat{$shy}ion</div>",
			$hyphenation->content( "<div>// hyphenation\nhyphenation</div>" )
		);
	}

	/**
	 * Test that text with nothing to hyphenate keeps its original encoding.
	 */
	public function test_unchanged_text_keeps_entities(): void {
		$hyphenation = new Hyphenate();
		$original    = '<p>Fish &amp; chips&nbsp;&#8211; &lt;today&gt;</p>';

		$this->assertSame( $original, $hyphenation->content( $original ) );
	}
}
