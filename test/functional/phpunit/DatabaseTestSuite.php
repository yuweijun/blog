<?php
class DatabaseTestSuite extends PHPUnit_Framework_TestSuite {
	protected function setUp() {
		$this->sharedFixture = new PDO('mysql:host=localhost;dbname=test', 'root', '');
	}

	protected function tearDown() {
		$this->sharedFixture = NULL;
	}
}
?>