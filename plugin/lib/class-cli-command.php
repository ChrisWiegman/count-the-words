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
	 * A counter object to build the counts
	 *
	 * @since 1.0.0
	 *
	 * @var Counter
	 */
	protected $counter;

	/**
	 * Setup the command object
	 *
	 * @since 1.0.0
	 *
	 * @param Counter $counter Counter object for handling posts.
	 */
	public function __construct( $counter ) {

		$this->counter = $counter;

	}

	/**
	 * Perform initial word counts for all posts
	 *
	 * ## OPTIONS
	 *
	 * [--posts-per-query=<size>]
	 * : Number of posts to query at a time (default: 50)
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

		\WP_CLI::confirm( esc_html__( 'Are you sure you want to generate counts for all content? This could take a while', 'count-the-words' ) );

		$total_posts = wp_count_posts( 'post' );
		$offset      = 0;
		$post_count  = 50;
		$progress    = \WP_CLI\Utils\make_progress_bar( esc_html__( 'Counting post words', 'count-the-words' ), $total_posts->publish );

		if ( isset( $assoc_args['size'] ) ) {
			$post_count = intval( $assoc_args['size'] );
		}

		while ( true ) {

			$query_args = array(
				'numberposts' => $post_count,
				'post_status' => 'publish',
				'category'    => 0,
				'include'     => array(),
				'exclude'     => array(),
				'post_type'   => 'any',
				'offset'      => $offset,
			);

			$posts = get_posts( $query_args );

			if ( empty( $posts ) ) {
				$progress->finish();
				break;
				// Stop looking for new posts.
			}

			foreach ( $posts as $post ) {
				$this->counter->action_save_post( $post->ID, $post, false );
				$progress->tick();
			}

			$offset = $offset + $post_count;

		}//end while
	}
}
