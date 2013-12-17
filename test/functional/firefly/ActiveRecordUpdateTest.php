<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordUpdateTest extends FireflyTestBase {

	public function setUp() {
		Post :: clear_cache();
	}

	public function testUpdateMethods() {
		$o1 = Post :: last();
		$rs = Post :: update($o1->id(), array('post_content' => 'test-content', 'post_title' => 'test-title', 'post_name' => 'test-name'));
		$this->assertEquals(true, $rs);
		$o2 = Post :: last();
		$name1 = $o1->post_name;
		$name2 = $o2->post_name;
		$o1->reload();
		$name3 = $o1->post_name;
		$this->assertEquals(true, $name2 === $name3);
		Logger :: get_reference()->info('old post title is ' . $name1 . ', new post title is ' . $name2 . ', title after reload is: ' . $name3);
		$o2->update_attribute('post_modified', date('Y-m-d H:i:s'));
		Logger :: get_reference()->info($o1->post_modified);
		Logger :: get_reference()->info($o2->post_modified);
		$o2->update_attributes(array('pinged' => 1, 'post_modified_gmt' => date('Y-m-d H:i:s')));

		$num = Post :: update_all(array('post_title' => 'is not yy', 'post_content' => 'updated post contents.'), array('conditions' => array('post_title' => 'is yy'), 'limit' => 100, 'order' => 'ID DESC'));
		Logger :: get_reference()->info("update sql effects rows: " . $num);
	}

	public function testReadOnlyAttribute() {
		$this->setExpectedException("FireflyException");
		$o1 = Post :: find(1);
		$this->assertEquals(1, $o1->id());
		$o1->update_attribute('ID', 100000);
		$this->assertEquals(1, $o1->id());
	}

	public function testUpdateReadonlyAttribute() {
		$this->setExpectedException("FireflyException");
		$o1 = Post :: last(array('readonly' => true));
		$this->assertEquals(true, $o1->is_readonly());
		$o1->update_attributes(array('post_content' => 'test-content2', 'post_title' => 'test-title2', 'post_name' => 'test-name2'));
	}

	public function testZeroUpdate() {
		$o1 = Post :: find(1);
		$z = $o1->update_attributes(array());
		$this->assertEquals(true, $z);
	}
}
?>
