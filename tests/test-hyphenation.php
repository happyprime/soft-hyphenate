<?php
/**
 * Class TestHyphenation
 *
 * Tests the hyphenation of text.
 *
 * @package soft-hyphenate
 */

use SoftHyphenate;

/**
 * Test the hyphenation of text.
 */
class TestHyphenation extends WP_UnitTestCase {
	/**
	 * Add a data provider for the test.
	 *
	 * @return array
	 */
	public function data_provider_for_test_hyphenation_of_text(): array {
		return [
			[
				'A string of text with the word that expects hyphenation.',
				'A string of text with the word that expects hyphenat&shy;ion.',
				'hyphenat-ion',
			],
			[
				'Multiple hyphenation instances of hyphenation should be handled.',
				'Multiple hyphenat&shy;ion instances of hyphenat&shy;ion should be handled.',
				'hyphenat-ion',
			],
			[
				'Hyphenation is likely to be capitalized at the beginning of a sentence.',
				'Hyphenat&shy;ion is likely to be capitalized at the beginning of a sentence.',
				'hyphenat-ion',
			],
			[
				'Someone may stress HYPHENATION with all caps.',
				'Someone may stress HYPHENAT&shy;ION with all caps.',
				'hyphenat-ion',
			],
			[
				'CamelCase is unlikely, but it should be accounted for.',
				'Camel&shy;Case is unlikely, but it should be accounted for.',
				'camel-case',
			],
			[
				'Attributes <a href="https://example.org/hyphenation">hyphenation</a> should be ignored.',
				'Attributes <a href="https://example.org/hyphenation">hyphenat&shy;ion</a> should be ignored.',
				'hyphenat-ion',
			],
			[
				'<CustomElement>custom element tags</CustomElement> should be ignored.',
				'<CustomElement>custom elem&shy;ent tags</CustomElement> should be ignored.',
				'elem-ent',
			],
		];
	}

	/**
	 * Test the hyphenation of text.
	 *
	 * @dataProvider data_provider_for_test_hyphenation_of_text
	 *
	 * @param string $original The original text.
	 * @param string $expected The expected text.
	 * @param string $word The word to hyphenate.
	 */
	public function test_hyphenation_of_text( string $original, string $expected, string $word ): void {
		$this->assertEquals( $expected, SoftHyphenate\hyphenate( $original, $word ) );
	}
}
