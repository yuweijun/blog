<?php
require_once 'ArrayTest.php';
require_once 'ExceptionTest.php';

class MySuite extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new MySuite('ArrayTest');
		$suite->addTestSuite("ExceptionTest");
		return $suite;
	}

	protected function setUp() {
		print "\nMySuite::setUp()\n";
	}

	protected function tearDown() {
		print "\nMySuite::tearDown()\n";
	}
}
?>
