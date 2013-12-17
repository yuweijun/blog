<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordObserverTest extends FireflyTestBase {

	public function testAddObserver() {
		$c = Comment :: find(1);
		$o = new MessageObserver;
		$c->add_observer($o);
		$c->save();
		$c->add_observer('MessageObserver');
		$c->save();
	}

	public function testDeleteObserver() {
		$c = Comment :: find(1);
		$o = new CommentObserver;
		$c->delete_observer($o);
		$c->save();
		$c->delete_observer('CommentObserver');
		$c->save();
	}

}
?>
