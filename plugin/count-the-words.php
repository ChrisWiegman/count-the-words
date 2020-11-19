<?php
/**
 * Plugin Name: Count the Words
 * Plugin URI: https://chriswiegman.com/
 * Description: Word counts and statistics for WordPress authors of all types.
 * Version: 0.0.1
 * Text Domain: count-the-words
 * Domain Path: /languages
 * Author: Chris Wiegman
 * Author URI: https://chriswiegman.com/
 * License: GPLv2
 *
 * @package ChrisWiegman\Count_the_Words
 */

use ChrisWiegman\Count_the_Words\CLI_Command;
use ChrisWiegman\Count_the_Words\Counter;

/**
 * Load plugin functionality.
 *
 * @since 1.0.0
 */
function cw_count_the_words_loader() {

	// Load the text domain.
	load_plugin_textdomain( 'count-the-words', false, dirname( dirname( __FILE__ ) ) . '/languages' );

	// Load the counter.
	$counter = new Counter();
	$counter->register_hooks();

	if ( defined( 'WP_CLI' ) && WP_CLI ) {

		$cli_command = new CLI_Command( $counter );
		\WP_CLI::add_command( 'count-the-words', $cli_command );

	}

	$counter->get_counts();
}

/**
 * Builds the class file name for the plugin
 *
 * @since 1.0.0
 *
 * @param string $class The name of the class to get.
 * @return string
 */
function cw_count_the_words_get_class_file( $class ) {

	$prefix   = 'ChrisWiegman\\Count_the_Words\\';
	$base_dir = __DIR__ . '/lib/';

	$len = strlen( $prefix );

	if ( 0 !== strncmp( $prefix, $class, $len ) ) {
		return '';
	}

	$relative_class = substr( $class, $len );
	$file           = $base_dir . str_replace( '\\', '/', 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) ) . '.php';

	$relative_class_parts = explode( '\\', $relative_class );

	if ( 1 < count( $relative_class_parts ) ) {

		$class_file = $relative_class_parts[0] . '/class-' . strtolower( str_replace( '_', '-', $relative_class_parts[1] ) );
		$file       = $base_dir . str_replace( '\\', '/', $class_file ) . '.php';

	}

	return $file;

}

/**
 * Auto-loading functionality for the plugin features
 *
 * @since 1.0.0
 *
 * @param object $class The class to load.
 */
function cw_count_the_words_autoloader( $class ) {

	$file = cw_count_the_words_get_class_file( $class );

	if ( ! empty( $file ) && file_exists( $file ) ) {
		include $file;
	}
}

spl_autoload_register( 'cw_count_the_words_autoloader' );

add_action( 'plugins_loaded', 'cw_count_the_words_loader' );
