<?php
/**
 * Seed demo content for local Soft Hyphenate testing.
 *
 * Run with `npm run env:seed`. Safe to run again: it updates the same posts.
 *
 * @package HappyPrime\SoftHyphenate
 */

namespace HappyPrime\SoftHyphenate\Dev;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates or updates a published post by slug and returns its ID.
 *
 * @param string $post_type Post type.
 * @param string $slug      Post slug.
 * @param string $title     Post title.
 * @param string $content   Block markup.
 * @return int Post ID, or zero on failure.
 */
function upsert_post( string $post_type, string $slug, string $title, string $content ): int {
	$existing = get_page_by_path( $slug, OBJECT, $post_type );

	$admin = get_user_by( 'login', 'admin' );

	$post = [
		'post_author'  => $admin ? $admin->ID : 0,
		'post_type'    => $post_type,
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_status'  => 'publish',
		// wp_insert_post() unslashes its input, which would strip backslashes from the markup.
		'post_content' => wp_slash( $content ),
	];

	if ( $existing ) {
		$post['ID'] = $existing->ID;
	}

	$post_id = wp_insert_post( $post, true );

	return is_wp_error( $post_id ) ? 0 : $post_id;
}

/**
 * Seeds the theme, permalinks, suggestions, and demo posts.
 */
function seed(): void {
	switch_theme( 'twentytwentyfive' );

	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );

	update_option(
		'hp_soft_hyphenate',
		implode(
			"\n",
			[
				'Donau-dampf-schiff-fahrts-gesell-schafts-kapitän',
				'Kraft-fahr-zeug-haft-pflicht-ver-sicherung',
				'Bundes-ausbildungs-förderungs-gesetz',
				'Rind-fleisch-etikettierungs-überwachungs-aufgaben-übertragungs-gesetz',
				'anti-dis-establish-ment-arian-ism',
				'in-com-pre-hen-si-bil-i-ties',
				'pneumono-ultra-micro-scopic-silico-volcano-coniosis',
				'super-cali-fragi-listic-expi-ali-docious',
			]
		)
	);

	$german = <<<'HTML'
<!-- wp:paragraph -->
<p>Long German compounds stay on one line unless the browser knows where to break them. Each word below has a suggestion under Settings → Soft Hyphenate, so it breaks at those points when its column is too narrow.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kraftfahrzeughaftpflichtversicherung</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Donaudampfschifffahrtsgesellschaftskapitän</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Der Donaudampfschifffahrtsgesellschaftskapitän prüft die Kraftfahrzeughaftpflichtversicherung.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">BUNDESAUSBILDUNGSFÖRDERUNGSGESETZ</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Das Bundesausbildungsförderungsgesetz regelt die Förderung von Studierenden.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Rindfleischetikettierungsüberwachungsaufgabenübertragungsgesetz</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Das Rindfleischetikettierungsüberwachungsaufgabenübertragungsgesetz wurde 2013 aufgehoben.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
HTML;

	$english = <<<'HTML'
<!-- wp:paragraph -->
<p>Antidisestablishmentarianism, incomprehensibilities, and Pneumonoultramicroscopicsilicovolcanoconiosis break at their suggested points. The code below uses the same words and stays exactly as typed, so copying it gives working code.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>Supercalifragilisticexpialidocious in prose.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>Inline: <code>supercalifragilisticexpialidocious()</code> and <kbd>Antidisestablishmentarianism</kbd>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:code -->
<pre class="wp-block-code"><code>// Supercalifragilisticexpialidocious stays intact inside code.
function supercalifragilisticexpialidocious() {
	return 'antidisestablishmentarianism';
}</code></pre>
<!-- /wp:code -->

<!-- wp:preformatted -->
<pre class="wp-block-preformatted">    Pneumonoultramicroscopicsilicovolcanoconiosis
    keeps its indentation and its letters in preformatted text.</pre>
<!-- /wp:preformatted -->
HTML;

	upsert_post( 'post', 'german-compounds', 'Donaudampfschifffahrtsgesellschaftskapitän', $german );
	upsert_post( 'post', 'long-english-words', 'Antidisestablishmentarianism and code', $english );
}

seed();
