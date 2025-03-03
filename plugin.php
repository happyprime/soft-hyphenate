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

add_filter( 'the_title', __NAMESPACE__ . '\hyphenate_title' );
add_filter( 'the_content', __NAMESPACE__ . '\hyphenate_content' );

/**
 * Adds the settings page.
 */
function add_settings_page() {
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
function settings_init() {
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
function display_settings_page() {
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
function section_callback() {
	printf( '<p>%s</p>', esc_html__( 'Enter your hyphenation suggestions (e.g. "hyph-en-ate") below, one word per line.', 'soft-hyphenate' ) );
}

/**
 * Displays the field for capturing hyphenation suggestions.
 */
function display_hyphenation_suggestion_field() {
	$hyphenation_suggestion = get_option( 'hp-soft-hyphenate' );

	printf(
		'<textarea name="hp-soft-hyphenate" id="hp-soft-hyphenate" rows="20" cols="50">%s</textarea>',
		esc_textarea( $hyphenation_suggestion )
	);
}

/**
 * Adds soft hypens to any suggestion library words in post titles.
 *
 * @param string $title The post title.
 *
 * @return string The post title with soft hyphens added.
 */
function hyphenate_title( string $title ) {
	return add_soft_hyphens( $title );
}

/**
 * Adds soft hypens to any suggestion library words in post content.
 *
 * @param string $content The post content.
 *
 * @return string The post content with soft hyphens added.
 */
function hyphenate_content( string $content ) {
	return add_soft_hyphens( $content );
}

/**
 * Add soft hyphens to a string based on a suggestion.
 *
 * @param string $value The string to add soft hyphens to.
 * @param string $word The word to add soft hyphens to.
 *
 * @return string The string with soft hyphens added.
 */
function hyphenate( string $value, string $word ) {
	// Get the word without hyphens.
	$without_hyphens = str_replace( '-', '', $word );

	// Create the soft-hyphenated version.
	$soft_hyphenated = str_replace( '-', '&shy;', $word );

	// Create an all-caps soft-hyphenated version (&shy; needs to remain lowercase).
	$soft_hyphenated_upper = str_replace( '-', '&shy;', strtoupper( $word ) );

	// Use case-insensitive regular expression to find matches.
	$pattern = '/(' . preg_quote( $without_hyphens, '/' ) . ')/i';

	// Replace matches while preserving original case.
	$value = preg_replace_callback(
		$pattern,
		function ( $matches ) use ( $soft_hyphenated, $soft_hyphenated_upper ) {
			if ( strtoupper( $matches[0] ) === $matches[0] ) {
				return $soft_hyphenated_upper;
			}

			if ( ucfirst( strtolower( $matches[0] ) ) === $matches[0] ) {
				return ucfirst( strtolower( $soft_hyphenated ) );
			}

			return strtolower( $soft_hyphenated );
		},
		$value
	);

	return $value;
}

/**
 * Adds soft hypens to any suggestion library words in the given string.
 *
 * @param string $value The string to add soft hyphens to.
 *
 * @return string The string with soft hyphens added.
 */
function add_soft_hyphens( string $value ) {
	$hyphenation_suggestions = get_option( 'hp-soft-hyphenate' );

	if ( $hyphenation_suggestions ) {
		$words = explode( "\n", $hyphenation_suggestions );

		foreach ( $words as $word ) {
			$word = trim( $word );

			if ( empty( $word ) ) {
				continue;
			}

			$value = hyphenate( $value, $word );
		}
	}

	return $value;
}
