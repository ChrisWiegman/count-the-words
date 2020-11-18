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
}
