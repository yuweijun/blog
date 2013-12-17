<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ControllerTest extends FireflyTestBase {

	public function testControllerNameSplit() {
		$str = "testApplicationController";
		$str2 = preg_replace('/([a-z0-9])([A-Z])/', '\1_\2', $str);
		$this->assertEquals('test_Application_Controller', $str2);

		$prefix = "/users/234/";
		$prefix = preg_replace('/(^\s*\/?|\/?\s*$)/', '', $prefix);
		$this->assertEquals('users/234', $prefix);
	}

	public function testAppendSlashToUrl() {
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

	public function testAvailableControllers() {
		$cs = Router :: available_controllers();
		$this->assertNotEquals(0, sizeof($cs));
	}

	public function testRoutesByController() {
		$rc = Router :: factory()->routes_by_controller("posts");
		$this->assertEquals('/:year/:month/:day', $rc[0]);
	}

	public function testRoutesByControllerAndAction() {
		$ca = Router :: factory()->routes_by_controller_and_action("posts", "find_by_date");
		$this->assertEquals('/:year/:month/:day', $ca[0]);
	}

}
?>
