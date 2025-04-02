<?php
/**
 * Plugin Name:  Soft Hyphenate
 * Description:  Add curated soft hyphens to content in WordPress.
 * Version:      1.0.0
 * Plugin URI:   https://github.com/happyprime/soft-hyphenate/
 * Author:       Happy Prime
 * Author URI:   https://happyprime.co
 * License:      GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:  hp-soft-hyphenate
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
 * @package HappyPrime\SoftHyphenate
 */

namespace HappyPrime\SoftHyphenate;

/**
 * A common prefix used with settings pages, fields, sections, and other
 * components to help with uniquity.
 *
 * @var string
 */
const PREFIX = 'hp-sh-';

/**
 * A common slug combined with the prefix and used to build the names of
 * settings pages, fields, sections, and other components.
 *
 * @var string
 */
const SLUG = 'soft-hyphenate';

/**
 * The main option key used for the plugin.
 *
 * @var string
 */
const OPTION_NAME = 'hp_soft_hyphenate';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

add_action( 'plugins_loaded', [ Init::class, 'init' ] );
