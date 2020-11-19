<?php
/**
 * Handles all shortcode options for the word counts.
 *
 * @since 1.0.0
 *
 * @author Chris Wiegman <contact@chriswiegman.com>
 *
 * @package ChrisWiegman\Count_the_Words
 */

namespace ChrisWiegman\Count_the_Words;

/**
 * Shortcode class
 */
class Shortcode {

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
	 * Register all hooks for the counter.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		add_action( 'init', array( $this, 'action_init' ) );

	}

	/**
	 * Action init
	 *
	 * Register the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function action_init() {

		add_shortcode( 'word-count', array( $this, 'shortcode_word_count' ) );

	}

	/**
	 * Render the word-count shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts Array of shortcode attributes.
	 * @param string|null $content Content of shortcode.
	 */
	public function shortcode_word_count( $atts, $content = null ) {

		$counts = $this->counter->get_counts();

		return sprintf( 'Posts: %d, Words: %d', $counts['post_count'], $counts['word_count'] );

	}
}
