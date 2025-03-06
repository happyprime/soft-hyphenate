<?php
/**
 * Plugin Name:  Soft Hyphenate
 * Description:  Add curated soft hyphens to content in WordPress.
 * Version:      1.0.0
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

require_once __DIR__ . '/vendor/autoload.php';

add_action( 'plugins_loaded', [ Init::class, 'init' ] );
