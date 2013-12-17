<?php
$x = include_once('SessionStart.php');

class RouterPluginsTest extends FireflyTestBase {

	public function testPluginRouter() {
		define('ROUTER', 'simple');
		$cv = Router :: factory()->recognize_path("/2009/01/18");
		$this->assertEquals('2009', $cv['year']);
	}

}
?>
