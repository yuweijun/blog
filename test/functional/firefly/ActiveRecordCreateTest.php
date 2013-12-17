<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordCreateTest extends FireflyTestBase {

	public function setUp() {
		Post :: clear_cache();
	}

	public function testCreateMethod() {
		$count_sql = 'select count(*) n from posts';
		$o = Post :: find_by_sql($count_sql);
		$size1 = $o[0]->n;
		$id = Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		$o = Post :: find_by_sql($count_sql);
		$size2 = $o[0]->n;
		$this->assertEquals(1, $size2 - $size1);
	}

	public function testCreateEmptyStatement() {
		$o = UserInfo :: create(array());
		$this->assertNotEquals(0, $o->id());
	}


	/**
	 * @expectedException FireflyException
	 */
	public function testGetModelNameException() {
		// don't merge array to one line.
		$id = Post :: create(array(
								'post_author' => 1,
								'post_date' => date('Y-m-d H:s:i'),
								'post_date_gmt' => date('Y-m-d H:s:i'),
								'post_content' => 'xxxx',
								'post_title' => 'yy',
								'post_name' => 'yy'
								)
							);
	}
}
?>
