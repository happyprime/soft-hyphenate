# Soft Hyphenate

Add curated soft hyphens to content in WordPress.

## Description

Soft Hyphenate adds soft hyphens (U+00AD) to titles and post content before they are displayed on the front end, based on a curated list of hyphenation suggestions.

This is useful for adjusting the visual appearance of long words that are not hyphenated by default.

All major browsers [support](https://caniuse.com/css-hyphens) the `hyphens: auto;` CSS property, which automatically hyphenates words as needed, but Chrome and Firefox do not apply hyphenation to capitalized words. This can create a situation, especially in headings, where long words bleed out of their container.

Once this plugin is activated, a settings page is available at Settings -> Soft Hyphenate in the WordPress admin that allows to you to manage a list of hyphenation suggestions.

Because browsers do handle most automatic hyphenation, this plugin does not attempt to use an algorithmic approach to hyphenation. If you're looking for a more comprehensive approach, you might consider using the [wp-Typography](https://wordpress.org/plugins/wp-typography/) plugin.

## Changelog

### 1.0.0

Initial release.
