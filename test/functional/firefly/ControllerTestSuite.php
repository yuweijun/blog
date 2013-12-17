<?php
require_once('ControllerTest.php');
require_once('RenderTest.php');
require_once('RouterTest.php');
require_once('ViewTest.php');

class ControllerTestSuite extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new ControllerTestSuite('ControllerTest');
		$suite->addTestSuite('RenderTest');
		$suite->addTestSuite('ViewTest');
		$suite->addTestSuite('RouterTest');
		return $suite;
	}

	protected function setUp() {
		echo "\n";
		echo "FireflyTestSuite :: setUp()";
		echo "\n";
	}

	protected function tearDown() {
		echo "\n";
		echo "FireflyTestSuite :: tearDown()";
		echo "\n";
	}
}
?>
