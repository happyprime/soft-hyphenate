<?php
/**
 * Remove plugin data on uninstall.
 *
 * @package HappyPrime\SoftHyphenate
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// The plugin is not loaded during uninstall, so its OPTION_NAME constant is not available.
delete_option( 'hp_soft_hyphenate' );
