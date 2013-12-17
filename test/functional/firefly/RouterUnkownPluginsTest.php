<?php
include_once('SessionStart.php');

class RouterUnkownPluginsTest extends FireflyTestBase {

	public function testUnkownRouter() {
		define('ROUTER', 'unkown');
		$this->setExpectedException('FireflyException');
		$cv = Router :: factory()->recognize_path("/2009/01/18");
	}

}
?>
