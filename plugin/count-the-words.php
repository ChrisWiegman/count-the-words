<?php
/**
 * Plugin Name: Count the Words
 * Plugin URI: https://chriswiegman.com/
 * Description: Word counts and statistics for WordPress authors of all types.
 * Version: 0.0.1
 * Text Domain: count-the-words
 * Domain Path: /languages
 * Author: WP Engine
 * Author URI: https://wpengine.com/
 * License: GPLv2
 *
 * @package WPEngine\Count_the_Words
 */

define( 'WPENGINE_COUNT_THE_WORDS_VERSION', '0.0.1' );

/**
 * Load plugin functionality.
 *
 * @since 1.0.0
 */
function wpe_count_the_words_loader() {

	// Load the text domain.
	load_plugin_textdomain( 'count-the-words', false, dirname( dirname( __FILE__ ) ) . '/languages' );

}

/**
 * Builds the class file name for the plugin
 *
 * @since 1.0.0
 *
 * @param string $class The name of the class to get.
 * @return string
 */
function wpe_count_the_words_get_class_file( $class ) {

	$prefix   = 'WPEngine\\Count_the_Words\\';
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
function wpe_count_the_words_autoloader( $class ) {

	$file = wpe_count_the_words_get_class_file( $class );

	if ( ! empty( $file ) && file_exists( $file ) ) {
		include $file;
	}
}

spl_autoload_register( 'wpe_count_the_words_autoloader' );

add_action( 'plugins_loaded', 'wpe_count_the_words_loader' );
