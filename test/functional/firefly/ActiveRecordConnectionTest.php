<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordConnectionTest extends FireflyTestBase {

	public function testReadonlyConnections() {
		$count_sql = 'SELECT count(*) n FROM posts';
		Post :: find_by_sql($count_sql, true);
		Post :: find(1, array('readonly' => 1));
	}

	public function testAnotherConnection() {
		$another = array (
					'adapter' => 'mysql',
					'encoding' => 'utf8',
					'database' => 'firefly_development',
					'username' => 'firefly',
					'password' => 'phpfirefly',
					'host' => 'localhost:/tmp/mysql.sock'
				);
		Post :: get_readonly_connection($another);
	}

	public function testGetTableColumns() {
		$columns = Post :: get_columns();
		$this->assertEquals('ID', $columns[0]);
		$inspect = Post :: inspect();
	}

	public function testConfigException1() {
		$this->setExpectedException("FireflyException");
		$config = array('host' => 'localhost', 'database' => 'firefly_development', 'username' => 'root', 'password' => '');
		Post :: establish_connection($config);
	}

	public function testConfigException2() {
		$this->setExpectedException("FireflyException");
		$config = array('adapter' => 'mysql', 'database' => 'firefly_development', 'username' => 'root', 'password' => '');
		Post :: establish_connection($config);
	}

	public function testConfigException3() {
		$this->setExpectedException("FireflyException");
		$config = array('adapter' => 'mysql', 'host' => 'localhost', 'username' => 'root', 'password' => '');
		Post :: establish_connection($config);
	}

	public function testConfigException4() {
		$this->setExpectedException("FireflyException");
		$config = array('adapter' => 'mysql', 'database' => 'firefly_development', 'host' => 'localhost', 'password' => '');
		Post :: establish_connection($config);
	}

	public function testConfigException5() {
		$this->setExpectedException("FireflyException");
		$config = array('adapter' => 'mysql', 'database' => 'firefly_development', 'host' => 'localhost', 'username' => 'root');
		Post :: establish_connection($config);
	}

}
?>
