<?php
/**
 * A counter object to handle word counts throughout
 *
 * @since 1.0.0
 *
 * @author Chris Wiegman <contact@chriswiegman.com>
 *
 * @package ChrisWiegman\Count_the_Words
 */

namespace ChrisWiegman\Count_the_Words;

/**
 * Counter class
 */
class Counter {

	/**
	 * The plugin URL
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * The plugin version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_version;

	/**
	 * The meta key when saving word counts
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'cw-count-the-words-count';

	/**
	 * Setup the counter
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_version The current plugin version.
	 * @param string $plugin_url The url of the plugin.
	 */
	public function __construct( $plugin_version, $plugin_url ) {

		$this->plugin_version = $plugin_version;
		$this->plugin_url     = $plugin_url;

	}

	/**
	 * Register all hooks for the counter.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		add_action( 'save_post', array( $this, 'action_save_post' ), 10, 3 );

	}

	/**
	 * Action save_post
	 *
	 * Generate and save the wordcount to post_meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_ID The post ID.
	 * @param WP_Post $post The WP_Post object.
	 * @param bool    $update True if post is being updated or false.
	 */
	public function action_save_post( $post_ID, $post, $update ) {

		$count = $this->count_in_content( $post->post_content );

		update_post_meta( $post_ID, $this->meta_key, $count );

	}

	/**
	 * Count the words in a given block of content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The content to process and count.
	 *
	 * @return int The number of words in the content.
	 */
	public function count_in_content( $content ) {

		$decode_content   = html_entity_decode( $content );
		$filter_shortcode = do_shortcode( $decode_content );
		$strip_tags       = wp_strip_all_tags( $filter_shortcode, true );
		$count            = str_word_count( $strip_tags );

		return $count;

	}
}