<?php
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/CsvDataSet.php';

class DatabaseTest extends PHPUnit_Extensions_Database_TestCase {
	protected function getConnection() {
		$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
		return $this->createDefaultDBConnection($pdo, 'test');
	}

	protected function getDataSet() {
		// return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../account-seed.xml');
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
		// $dataSet->addTable('post', '../fixtures/post.csv');
		// $dataSet->addTable('post_comment', '../fixtures/post_comment.csv');
		// $dataSet->addTable('current_visitors', '../fixtures/current_visitors.csv');
		return $dataSet;
	}

	public function testCreateTable() {
		return;
	}

	public function testSkippedFunc() {
		$this->markTestSkipped('The MySQLi extension is not available.');
	}

	public function testSomething() {
		// Optional: Test anything here, if you want.
		$this->assertTrue(TRUE, 'This should already work.');

		// Stop here and mark this test as incomplete.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
?>