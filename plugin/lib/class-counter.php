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
	 * The meta key when saving word counts
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $meta_key = 'cw-count-the-words-count';

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

		$this->save_post( $post );

	}

	/**
	 * Saves the word count for a given post
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The WP_Post object.
	 */
	public function save_post( $post ) {

		$count = $this->count_in_content( $post->post_content );

		update_post_meta( $post->ID, $this->meta_key, $count );

	}

	/**
	 * Gets the word count for a given post or array of posts
	 *
	 * @param int|array $posts Post ID or array of post IDs to check.
	 *
	 * @return int|array Int of word count for single post or array keyed to post ID for multiple posts.
	 */
	public function get_count( $posts ) {

		if ( is_array( $posts ) ) {

			$counts = array();

			foreach ( $posts as $id ) {
				$counts[ $id ] = $this->get_word_count( $id );
			}

			return $counts;
		}

		return $this->get_word_count( $posts );

	}

	/**
	 * Gets the word count for a given post or array of posts
	 *
	 * @param int $post_ID Post ID of post to retrieve.
	 *
	 * @return int Int of word count.
	 */
	protected function get_word_count( $post_ID ) {

		$count = get_post_meta( $post_ID, $this->meta_key, true );

		// Save the word count if we don't have it already.
		if ( false === $count ) {

			$post = get_post( $post_ID );

			$count = $this->count_in_content( $post->post_content );

			update_post_meta( $post_ID, $this->meta_key, $count );

		}

		return $count;

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

	/**
	 * Get the word counts for a group of posts
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $post_type The post type to count (Default: post).
	 * @param string       $date The date to count. Default(all).
	 * @param string       $period The period to count (month, day or year). Only applies if date is not "all" (Default: month).
	 * @param bool         $include_post_count True to include the post count in array format or false (default: true).
	 *
	 * @return array|int|bool Count of words in available posts. Integer of wordcount if $include_post_count is false or array containg word and post count. Returns false on error.
	 */
	public function get_counts( $post_type = 'post', $date = 'all', $period = 'month', $include_post_count = true ) {

		$word_count = 0;

		$query_args = array(
			'post_type'   => $post_type,
			'numberposts' => -1,
			'post_status' => 'publish',
		);

		if ( 'all' !== $date ) {

			$timestamp = strtotime( $date );

			if ( false === $timestamp ) {
				return false;
			}

			$date_query = array();

			$date_query['year'] = gmdate( 'Y', $timestamp );

			switch ( $period ) {
				case 'day':
					$date_query['month'] = gmdate( 'm', $timestamp );
					$date_query['day']   = gmdate( 'd', $timestamp );
					break;
				case 'month':
					$date_query['month'] = gmdate( 'm', $timestamp );
					break;
				case 'year':
					break;
				default:
					return false;

			}

			// Setup a date query.
			$query_args['date_query'] = $date_query;

		}//end if

		$posts = get_posts( $query_args );

		foreach ( $posts as $post ) {

			$post_count = get_post_meta( $post->ID, $this->meta_key, true );

			if ( false !== $post_count ) {
				$word_count = $word_count + $post_count;
			}
		}

		if ( true === $include_post_count ) {

			return array(
				'post_count' => count( $posts ),
				'word_count' => $word_count,
			);
		}

		return $word_count;

	}
}
