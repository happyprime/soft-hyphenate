<?php
/**
 * Plugin Name:  Soft Hyphenate
 * Description:  Maintain a library of hyphenation suggestions for long words used throughout your site.
 * Version:      0.0.1
 * Plugin URI:   https://github.com/happyprime/soft-hyphenate/
 * Author:       Happy Prime
 * Author URI:   https://happyprime.co
 * Text Domain:  soft-hyphenate
 * Requires PHP: 7.4
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package soft-hyphenate
 */

namespace SoftHyphenate;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'admin_menu', __NAMESPACE__ . '\add_settings_page' );
add_action( 'admin_init', __NAMESPACE__ . '\settings_init' );

add_filter( 'the_title', __NAMESPACE__ . '\hyphenate_content' );
add_filter( 'the_content', __NAMESPACE__ . '\hyphenate_content' );

/**
 * Adds the settings page.
 */
function add_settings_page(): void {
	add_options_page(
		__( 'Soft Hyphenate', 'soft-hyphenate' ),
		__( 'Soft Hyphenate', 'soft-hyphenate' ),
		'manage_options',
		'soft-hyphenate',
		__NAMESPACE__ . '\display_settings_page'
	);
}

/**
 * Initializes the settings.
 */
function settings_init(): void {
	register_setting( 'soft-hyphenate', 'hp-soft-hyphenate' );

	add_settings_section(
		'hyphenation-suggestion-section',
		__( 'Hyphenation Suggestions', 'soft-hyphenate' ),
		__NAMESPACE__ . '\section_callback',
		'soft-hyphenate'
	);

	add_settings_field(
		'hyphenation-suggestions',
		'',
		__NAMESPACE__ . '\display_hyphenation_suggestion_field',
		'soft-hyphenate',
		'hyphenation-suggestion-section'
	);
}

/**
 * Displays the Soft Hyphenate settings page.
 */
function display_settings_page(): void {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Soft Hyphenate Settings', 'soft-hyphenate' ); ?></h1>
		<form action="options.php" method="post">
		<?php
			settings_fields( 'soft-hyphenate' );

			do_settings_sections( 'soft-hyphenate' );

			submit_button();
		?>
		</form>
	</div>
	<?php
}

/**
 * Displays the hyphenation suggestions section.
 */
function section_callback(): void {
	printf( '<p>%s</p>', esc_html__( 'Enter your hyphenation suggestions (e.g. "hyph-en-ate") below, one word per line.', 'soft-hyphenate' ) );
}

/**
 * Displays the field for capturing hyphenation suggestions.
 */
function display_hyphenation_suggestion_field(): void {
	$hyphenation_suggestion = get_option( 'hp-soft-hyphenate', '' );

	if ( ! is_scalar( $hyphenation_suggestion ) ) {
		$hyphenation_suggestion = '';
	}

	printf(
		'<textarea name="hp-soft-hyphenate" id="hp-soft-hyphenate" rows="20" cols="50">%s</textarea>',
		esc_textarea( (string) $hyphenation_suggestion )
	);
}

/**
 * Adds soft hypens to any suggestion library words in post content.
 *
 * @param string $content The post content.
 *
 * @return string The post content with soft hyphens added.
 */
function hyphenate_content( string $content ): string {
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
		$chunk = hyphenate_chunk( $chunk );
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
function hyphenate_chunk( string $chunk ): string {
	$hyphenation_suggestions = get_option( 'hp-soft-hyphenate' );

	if ( ! is_scalar( $hyphenation_suggestions ) || empty( $hyphenation_suggestions ) ) {
		return $chunk;
	}

	$words = explode( "\n", (string) $hyphenation_suggestions );
	$words = array_map( 'trim', $words );

	preg_match_all( '/([^\s\p{P}]+)([\s\p{P}]*)/', $chunk, $matches, PREG_SET_ORDER );

	$result = '';
	foreach ( $matches as $match ) {
		$hyphenated_match = $match[1];

		foreach ( $words as $word ) {
			$hyphenated_match = hyphenate( $hyphenated_match, $word );
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
function hyphenate( string $word, string $suggestion ): string {
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
