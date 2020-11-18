<?php

/**
 * Test the primary plugin file
 *
 * @package WPEngine\Count_the_Words
 */

namespace WPEngine\Count_the_Words\Tests;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

/**
 * Test the main plugin file
 */
class PluginFileTest extends \WP_Mock\Tools\TestCase {

	/**
	 * Test loader function
	 */
	public function test_wpe_count_the_words_loader() {

		\WP_Mock::userFunction(
			'load_plugin_textdomain',
			array(
				'times' => 1,
			)
		);

		wpe_count_the_words_loader();

		$this->assertConditionsMet();
		assertTrue( defined( 'WPENGINE_COUNT_THE_WORDS_VERSION' ) );

	}

	public function test_autoloader_registered() {
		$this->assertContains( 'wpe_count_the_words_autoloader', spl_autoload_functions() );
	}

	public function test_autoloader() {

		$test_classes = array(
			'WPEngine\Count_the_Words\Class_One' => '/app/plugin/lib/class-class-one.php',
			'WPEngine\Count_the_Words\Sub_Classes\Class_Two' => '/app/plugin/lib/Sub_Classes/class-class-two.php',
			'Class_Three' => '',
		);

		foreach ( $test_classes as $test_class => $class_file ) {

			$file = wpe_count_the_words_get_class_file( $test_class );

			assertEquals( $class_file, $file );

		}
	}
}
