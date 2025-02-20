<?php
/**
 * Plugin Name:  Soft Hyphenate
 * Description:  Maintain a library of hyphenation suggestions for long words used throughout your site.
 * Version:      0.0.1
 * Plugin URI:   https://github.com/happyprime/soft-hyphenate/
 * Author:       Happy Prime
 * Author URI:   https://happyprime.co
 * Text Domain:  soft-hyphenate
 * Requires PHP: 8
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
