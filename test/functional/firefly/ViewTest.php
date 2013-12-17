<?php
include_once('SessionStart.php');
// include_once(FIREFLY_APP_DIR . DS. 'controllers' . DS . 'application_controller.php');

class ViewTest extends FireflyTestBase {

	public function testAdminTestAction() {
		$html = $this->getHttpResponseBody('admin/test.php');
		preg_match('/<title>(.*)<\/title>/is', $html, $matches);
		$title = trim($matches[1]);
		$this->assertNotEquals('PHP Exception Caught', $title);
		$this->assertEquals('admin :: test', $title);
		Logger :: get_reference()->info($html);
	}

	public function testViewParial() {
		$html = $this->getHttpResponseBody('/test/index.php');
		preg_match('/<title>(.*)<\/title>/is', $html, $matches);
		$title = trim($matches[1]);
		$this->assertNotEquals('PHP Exception Caught', $title);
		$this->assertEquals('test', $title);
		$html = $this->getHttpResponseBody('test');
		preg_match('/<title>(.*)<\/title>/is', $html, $matches);
		$title = trim($matches[1]);
		$this->assertNotEquals('PHP Exception Caught', $title);
		$this->assertEquals('test', $title);
		Logger :: get_reference()->info($html);
	}

	public function testFlashMessages() {
		$html = $this->getHttpResponseBody('/test/test4');
	}

	public function testActionMissing() {
		$html = $this->getHttpResponseBody('/admin/non_exists_action');
		$this->assertContains('action missing', $html);
	}

	public function testActionException() {
		$html = $this->getHttpResponseBody('/non_controller/non_exists_action');
		$this->assertContains('PHP Exception Caught', $html);
	}

	public function testRegisterMimeType() {
		MimeType :: register('js', 'application/javascript');
	}
}

?>
