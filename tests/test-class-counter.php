<?php
/**
 * Test the Counter Class
 *
 * @package ChrisWiegman\Count_the_Words
 */

namespace ChrisWiegman\Count_the_Words\Tests;

use ChrisWiegman\Count_the_Words\Counter;

/**
 * Test the counter class
 */
class CounterTest extends \WP_Mock\Tools\TestCase {

	protected $version    = '1.0.0';
	protected $plugin_uri = 'https://chriswiegman.com';

	protected $counter;

	/**
	 * Setup a test object
	 */
	public function setUp(): void {

		$this->counter = new Counter( $this->version, $this->plugin_uri );

	}

	/**
	 * Test loader function
	 */
	public function test_constructor() {

		$this->assertEquals( $this->counter->plugin_version, $this->version );
		$this->assertEquals( $this->counter->plugin_url, $this->plugin_uri );

	}
}
