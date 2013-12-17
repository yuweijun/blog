<?php
require_once 'PHPUnit/Framework.php';

class ArrayTest extends PHPUnit_Framework_TestCase {

	protected $fixture;

	protected function setUp() {
		// Create the Array fixture.
		$this->fixture = array();
	}

	public function testNewArrayIsEmpty() {
		// Assert that the size of the Array fixture is 0.
		$this->assertEquals(0, sizeof($this->fixture));
	}

	public function testArrayContainsAnElement() {
		// Add an element to the Array fixture.
		$this->fixture[] = 'Element';

		// Assert that the size of the Array fixture is 1.
		$this->assertEquals(1, sizeof($this->fixture));
	}
}
?>