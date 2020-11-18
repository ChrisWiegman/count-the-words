<?php
/**
 * WP-CLI command for getting initial word counts.
 *
 * @since 1.0.0
 *
 * @author Chris Wiegman <contact@chriswiegman.com>
 *
 * @package ChrisWiegman\Count_the_Words
 */

namespace ChrisWiegman\Count_the_Words;

/**
 * Manage word count functionality such as generating initial word counts.
 */
class CLI_Command {

	/**
	 * Perform initial word counts for all posts
	 *
	 * ## EXAMPLES
	 *
	 *     wp count-the-words
	 *
	 * @when after_wp_load
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of positional arguments.
	 * @param array $assoc_args Array of associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {

		\WP_CLI::line( 'Hello, world' );

	}
}
