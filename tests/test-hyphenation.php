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

	public function setUp(): void {
		parent::setUp();

		$suggestions = implode(
			"\n",
			[
				'hyphenat-ion',
				'pascal-case',
				'pascalcase-word',
				'elem-ent',
			]
		);

		update_option( 'hp-soft-hyphenate', $suggestions );
	}

	public function tearDown(): void {
		parent::tearDown();

		delete_option( 'hp-soft-hyphenate' );
	}

	/**
	 * Add a data provider for the test.
	 *
	 * @return array
	 */
	public function data_provider_for_test_hyphenation_of_content(): array {
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
				'PascalCase is unlikely, but it should be accounted for.',
				'Pascal&shy;Case is unlikely, but it should be accounted for.',
				'pascal-case',
			],
			[
				'Something PascalCase should be handled anywhere in a string.',
				'Something Pascal&shy;Case should be handled anywhere in a string.',
				'pascal-case',
			],
			[
				'A three-hump PascalCaseWord should be handled anywhere in a string.',
				'A three-hump PascalCase&shy;Word should be handled anywhere in a string.',
				'pascalcase-word',
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
	 * @dataProvider data_provider_for_test_hyphenation_of_content
	 *
	 * @param string $original The original text.
	 * @param string $expected The expected text.
	 * @param string $word The word to hyphenate.
	 */
	public function test_hyphenation_of_content( string $original, string $expected, string $word ): void {
		$this->assertEquals( $expected, SoftHyphenate\hyphenate_content( $original ) );
	}
}
