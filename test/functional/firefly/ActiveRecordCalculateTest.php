<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordCalculateTest extends FireflyTestBase {

	public function testCount() {
		$c = Post :: count();
		$this->assertGreaterThan(0, $c);
	}

	public function testCounter() {
		Post :: counter(2, 'comment_count', 1);
		$p = Post :: counter(2, 'comment_count', 1);
		$po = Post :: find(2);
		$c = $po->comment_count;
		Post :: counter(2, 'comment_count', 1);
		$this->assertEquals($c + 1, $po->comment_count);
		Post :: counter(2, 'comment_count', 2);
		$this->assertEquals($c + 3, $po->comment_count);
	}

	public function testAVG() {
		$a = Posts :: avg('comment_count');
	}

	public function testMin() {
		$min = Posts :: min('comment_count');
	}

	public function testMax() {
		$max = Post :: max('comment_count');
	}

	public function testSum() {
		$sum = Post :: sum('comment_count');
	}

}
?>
