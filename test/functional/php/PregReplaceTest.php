<?php
class PregReplaceTest extends PHPUnit_Framework_TestCase {

	protected $fixture;

	protected function setUp() {
		// Create the Array fixture.
		$this->fixture = array();
	}

	public function testPregReplaceLastSlash() {
		$path = 'test/test/?test=1#test';
		$path = preg_replace('/\/*(\?|\z)/', '/\1', $path, 1);
		$this->assertEquals('test/test/?test=1#test', $path);

		$path = 'test/test?test=1#test';
		$path = preg_replace('/\/*(\?|\z)/', '/\1', $path, 1);
		$this->assertEquals('test/test/?test=1#test', $path);

		$path = 'test/test/';
		$path = preg_replace('/\/*(\?|\z)/', '/\1', $path, 1);
		$this->assertEquals('test/test/', $path);

		$path = 'test/test';
		$path = preg_replace('/\/*(\?|\z)/', '/\1', $path, 1);
		$this->assertEquals('test/test/', $path);
	}

	public function testPregReplaceCallback() {
		function t($matches) {
			$name = 'David';
			$pwd = '111111';
			$v = $matches[1];
			return "'" . ${$v} . "'";
		}
		$sql = "select * from users where name = :name and password = :pwd";
		$sql = preg_replace_callback('/:(\w+)/', 't', $sql);
		$this->assertEquals("select * from users where name = 'David' and password = '111111'", $sql);
	}

	public function testPregMatchAllAndReplace() {
		$name = 'David';
		$pwd = '111111';
		$sql = "select * from users where name = :name and password = :pwd";
		$a = preg_match_all('/:(\w+)/', $sql, $matches);
		$this->assertEquals(2, $a);
		$replacements = array();
		$patterns = array();
		foreach ( $matches[0] as $match ) {
            $patterns[] = "/$match/";
		}
		foreach ( $matches[1] as $match ) {
			$replacements[] = "'" . ${$match} . "'";
		}
		$this->assertEquals(2, sizeof($patterns));
		$this->assertEquals(2, sizeof($replacements));
		$sql = preg_replace($patterns, $replacements, $sql);
		$this->assertEquals("select * from users where name = 'David' and password = '111111'", $sql);
	}

}
?>