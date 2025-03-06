<?php
/**
 * Manage hyphenation of content.
 *
 * @package soft-hyphenate
 */

namespace SoftHyphenate;

/**
 * Manage the hyphenation of content.
 */
class Hyphenate {
	/**
	 * The content to be hyphenated.
	 *
	 * @var string
	 */
	public $content;

	/**
	 * A list of suggested hyphenations.
	 *
	 * @var array<string>
	 */
	public $suggestions = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_suggestions();
	}

	/**
	 * Load the suggestions from the database.
	 */
	public function load_suggestions(): void {
		$suggestions = get_option( 'hp-soft-hyphenate' );

		if ( ! is_scalar( $suggestions ) || empty( $suggestions ) ) {
			return;
		}

		$suggestions = explode( "\n", (string) $suggestions );
		$suggestions = array_map( 'trim', $suggestions );
		$suggestions = array_filter( $suggestions );

		$this->suggestions = $suggestions;
	}

	/**
	 * Hyphenate the content.
	 *
	 * @param string $content The content to be hyphenated.
	 *
	 * @return string The hyphenated content.
	 */
	public function content( string $content ): string {
		$processor = new \WP_HTML_Tag_Processor( $content );

		while ( $processor->next_token() ) {
			if ( '#text' !== $processor->get_token_name() ) {
				continue;
			}

			$chunk = $processor->get_modifiable_text();

			// Capture one or more whitespace characters from the front of the chunk.
			preg_match( '/^\s+/', $chunk, $matches );
			$leading_whitespace = $matches[0] ?? '';

			// Capture one or more whitespace characters from the back of the chunk.
			preg_match( '/(\s+)$/', $chunk, $matches );
			$trailing_whitespace = $matches[0] ?? '';

			$chunk = trim( $chunk );
			$chunk = $this->chunk( $chunk );
			$chunk = $leading_whitespace . $chunk . $trailing_whitespace;

			$processor->set_modifiable_text( $chunk );
		}

		$content = $processor->get_updated_html();

		// Do we need to do this, or can we just let them be?
		$content = str_replace( "\u{00AD}", '&shy;', $content );

		return $content;
	}

	/**
	 * Add soft hyphens to a chunk of text based on a suggestion library.
	 *
	 * @param string $chunk The chunk of text to add soft hyphens to.
	 *
	 * @return string The chunk of text with soft hyphens added.
	 */
	public function chunk( string $chunk ): string {
		preg_match_all( '/([^\s\p{P}]+)([\s\p{P}]*)/', $chunk, $matches, PREG_SET_ORDER );

		$result = '';
		foreach ( $matches as $match ) {
			$hyphenated_match = $match[1];

			foreach ( $this->suggestions as $suggestion ) {
				$hyphenated_match = $this->word( $hyphenated_match, $suggestion );
			}

			$result .= $hyphenated_match . $match[2];
		}

		return $result;
	}

	/**
	 * Add soft hyphens to a string based on a similar string containing
	 * suggestive hyphenation.
	 *
	 * Example: hyphenate( 'hyphenation', 'hyphenat-ion' );
	 * Returns: "hyphenat\u{00AD}ion"
	 *
	 * @param string $word       The string to add soft hyphens to.
	 * @param string $suggestion The string suggesting soft hyphen placement.
	 *
	 * @return string The string with soft hyphens added.
	 */
	public function word( string $word, string $suggestion ): string {
		$without_hyphens = str_replace( '-', '', $suggestion );

		if ( strtolower( $word ) !== strtolower( $without_hyphens ) ) {
			return $word;
		}

		// Create the soft-hyphenated version.
		$soft_hyphenated = str_replace( '-', "\u{00AD}", $suggestion );

		// Break each string into an array of characters so that we can
		// inject soft hyphens in a case-sensitive friendly way.
		$shy_chars  = mb_str_split( $soft_hyphenated );
		$word_chars = mb_str_split( $word );

		$spots = [];
		foreach ( $shy_chars as $key => $char ) {
			if ( "\u{00AD}" === $char ) {
				$spots[] = $key - count( $spots );
			}
		}

		// Inject soft hyphens from the back forward to preserve key positions.
		$spots = array_reverse( $spots );
		foreach ( $spots as $spot ) {
			array_splice( $word_chars, $spot, 0, "\u{00AD}" );
		}

		// Return the re-assembled word.
		return implode( '', $word_chars );
	}
}
