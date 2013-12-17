<?php
require_once('ActiveRecordFindTest.php');
require_once('ActiveRecordCreateTest.php');
require_once('ActiveRecordUpdateTest.php');
require_once('ActiveRecordDeleteTest.php');
require_once('ActiveRecordInheritTest.php');
require_once('ActiveRecordConnectionTest.php');
require_once('ActiveRecordAssociationsTest.php');
require_once('ActiveRecordObserverTest.php');
require_once('ActiveRecordErrorTest.php');
require_once('ControllerTest.php');
require_once('RenderTest.php');
require_once('RouterTest.php');
require_once('ViewTest.php');

class FireflyTestSuite extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new FireflyTestSuite('ActiveRecordFindTest');
		$suite->addTestSuite("ActiveRecordCreateTest");
		$suite->addTestSuite("ActiveRecordUpdateTest");
		$suite->addTestSuite("ActiveRecordDeleteTest");
		$suite->addTestSuite("ActiveRecordInheritTest");
		$suite->addTestSuite("ActiveRecordConnectionTest");
		$suite->addTestSuite("ActiveRecordAssociationsTest");
		$suite->addTestSuite("ActiveRecordObserverTest");
		$suite->addTestSuite("ActiveRecordErrorTest");
		$suite->addTestSuite('ControllerTest');
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
