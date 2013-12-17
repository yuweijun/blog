<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordDeleteTest extends FireflyTestBase {

	public function setUp() {
		Post :: clear_cache();
	}

	public function testDeleteMethods() {
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		$o1 = Post :: last();
		$b = Post :: delete($o1->id());
		Logger :: get_reference()->info('Results of delete action is: ' . $b);
		$o2 = Post :: find('all', array('conditions' => array('post_title' => 'yy'), 'limit' => 3, 'order' => 'ID DESC'));
		$c = Post :: delete(array($o2[0]->id()));
		$c = Post :: delete(array($o2[1]->id(), $o2[2]->id()));
		Logger :: get_reference()->info('Results of delete action is: ' . $c);

		$num = Post :: delete_all(array('conditions' => array('post_title' => 'yy'), 'limit' => 1, 'order' => 'ID DESC'));
		Logger :: get_reference()->info("delete sql effects rows: " . $num);
	}

	public function testDestroyMethods() {
		Post :: create(array('post_author' => 1, 'post_date' => date('Y-m-d H:s:i'), 'post_date_gmt' => date('Y-m-d H:s:i'), 'post_content' => 'xxxx', 'post_title' => 'yy', 'post_name' => 'yy'));
		$p2 = Post :: last();
		$b = $p2->destroy();
		$this->assertEquals(true, $b);
	}

}
?>
