<?php

/**
 * Test the primary plugin file
 *
 * @package ChrisWiegman\Count_the_Words
 */

namespace ChrisWiegman\Count_the_Words\Tests;

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
			'plugin_dir_url',
			array(
				'times' => 1,
				'return' => 'https://wpengine.com/'
			)
		);

		\WP_Mock::userFunction(
			'get_file_data',
			array(
				'times' => 1,
				'return' => array(
					'version' => '1.0.0',
				)
			)
		);

		\WP_Mock::userFunction(
			'load_plugin_textdomain',
			array(
				'times' => 1,
			)
		);

		cw_count_the_words_loader();

		$this->assertConditionsMet();

	}

	public function test_autoloader_registered() {
		$this->assertContains( 'cw_count_the_words_autoloader', spl_autoload_functions() );
	}

	public function test_autoloader() {

		$test_classes = array(
			'ChrisWiegman\Count_the_Words\Class_One' => '/app/plugin/lib/class-class-one.php',
			'ChrisWiegman\Count_the_Words\Sub_Classes\Class_Two' => '/app/plugin/lib/Sub_Classes/class-class-two.php',
			'Class_Three' => '',
		);

		foreach ( $test_classes as $test_class => $class_file ) {

			$file = cw_count_the_words_get_class_file( $test_class );

			assertEquals( $class_file, $file );

		}
	}
}
