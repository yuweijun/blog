<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordErrorTest extends FireflyTestBase {

	public function testAddErrors() {
		$o1 = Post :: last();
		$e = $o1->get_errors_on('post_author');
		$o1->add_error('post_author', 'test');
		$e = $o1->get_errors_on('post_author');
		$this->assertEquals('test', $e);
		$o1->add_error('post_author', 'test1');
		$e = $o1->get_errors_on('post_author');
		$this->assertEquals('test1', $e[1]);
	}

}
?>
