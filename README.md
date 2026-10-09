# Soft Hyphenate

Add curated soft hyphens to content in WordPress.

## Description

Soft Hyphenate adds soft hyphens (U+00AD) to titles and post content before they are displayed on the front end, based on a curated list of hyphenation suggestions.

This is useful for adjusting the visual appearance of long words that are not hyphenated by default.

Text inside `code`, `pre`, `kbd`, and `samp` elements is left alone so that copied code still works. Feeds are not hyphenated.

All major browsers [support](https://caniuse.com/css-hyphens) the `hyphens: auto;` CSS property, which automatically hyphenates words as needed, but Chrome and Firefox do not apply hyphenation to capitalized words. This can create a situation, especially in headings, where long words bleed out of their container.

Once this plugin is activated, a settings page is available at Settings -> Soft Hyphenate in the WordPress admin that allows you to manage a list of hyphenation suggestions.

Because browsers do handle most automatic hyphenation, this plugin does not attempt to use an algorithmic approach to hyphenation. If you're looking for a more comprehensive approach, you might consider using the [wp-Typography](https://wordpress.org/plugins/wp-typography/) plugin.

## Development

Requires Docker, Node 20+, and Composer.

```sh
npm install
npm run env:start
```

`env:start` runs `composer install` (the plugin loads `vendor/autoload.php`), starts WordPress 7.1 at http://localhost:8960 (`admin` / `password`), and seeds:

- Twenty Twenty-Five with pretty permalinks.
- Hyphenation suggestions for German compounds and long English words.
- `/german-compounds/`: compounds in a heading, an all-caps heading, and narrow columns.
- `/long-english-words/`: the same kind of words in prose next to inline code, a code block, and preformatted text, which stay unhyphenated.

`npm run env:seed` re-applies the seed. `npm run env:stop` stops the site.

Checks:

```sh
composer phpcs
composer phpstan
npm run lint:package
npm run env:test:start && npm run test:php
```

`test:php` runs PHPUnit in a separate environment on port 8961. Stop it with `npm run env:test:stop`.

## Changelog

### 1.0.0

Initial release.
