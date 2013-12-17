<?php
include_once('SessionStart.php');

class RenderTest extends FireflyTestBase {

	public function testActionRender() {
		$html = $this->getRenderResults("test string render");
		$this->assertEquals('test string render', $html);
		$html = $this->getRenderResults(array('text' => "test string render"));
		$this->assertEquals('test string render', $html);

		$html = $this->getRenderResults(array('text' => "test string render", 'layout' => true));
		$this->assertNotEquals('test string render', $html);

		$html = $this->getRenderResults(array('layout' => false));
		$this->assertContains('app/views/test/index.php', $html);
		$html = $this->getRenderResults(array('layout' => 'posts'));
		$this->assertContains('posts layout', $html);
	}

	public function tetActionRender3() {
		$html = $this->getRenderResults(array('nothing' => true));
		$this->assertEquals('', $html);
		$html = $this->getRenderResults(array('nothing' => false));
		$this->assertContains('app/views/test/index.php', $html);
		$html = $this->getRenderResults(array('status' => 202));
		$html = $this->getRenderResults(array('status' => 202, 'layout' => false));

		$html = $this->getRenderResults(array('locals' => array('var1' => 'locals_var1', 'var2' => 'locals_var2')));
		$html = $this->getRenderResults(array('file' => '/Users/yu/Sites/phpfirefly/app/views/test/test.php'));
		$this->assertContains('test.php', $html);
		$html = $this->getRenderResults('/Users/yu/Sites/phpfirefly/app/views/test/test.php');
		$this->assertContains('test.php', $html);
		$html = $this->getRenderResults('posts/index');
		$html = $this->getRenderResults(array('action' => 'posts/index'));
		$this->assertContains('index.php of posts controller', $html);
		$html = $this->getRenderResults(array('action' => '/posts/index'));
		$this->assertContains('index.php of posts controller', $html);

		$html = $this->getRenderResults('test');
		$this->assertContains('test.php', $html);
		$html = $this->getRenderResults(array('template' => 'posts/index'));
		$this->assertContains('index.php of posts controller', $html);
		$html = $this->getRenderResults(array('template' => '/posts/index'));
		$this->assertContains('index.php of posts controller', $html);

		$html = $this->getRenderResults(array('action' => 'posts/index', 'layout' => false));
		$this->assertContains('index.php of posts controller', $html);
		$html = $this->getRenderResults(array('action' => 'test'));
		$this->assertContains('test layout', $html);

		$html = $this->getRenderResults(array('partial' => 'form'));
		$this->assertContains('form', $html);
		$html = $this->getRenderResults(array('partial' => 'form', 'layout' => false));
		$html = $this->getRenderResults(array('partial' => 'form', 'layout' => true));
		$this->assertContains('test layout', $html);
		$html = $this->getRenderResults(array('partial' => 'posts/form'));
		$html = $this->getRenderResults(array('partial' => '/posts/form'));
		$html = $this->getRenderResults(array('partial' => 'posts/form', 'layout' => 'posts'));
		$html = $this->getRenderResults(array('partial' => '/posts/form', 'layout' => 'posts'));
		$this->assertContains('posts layout', $html);
	}

	public function testNonTemplateLayout() {
		$this->setExpectedException('FireflyException');
		$html = $this->getRenderResults(array('template' => 'posts/index2')); // template not exists.
	}

	public function testExceptionLayout() {
		$this->setExpectedException('FireflyException');
		$html = $this->getRenderResults(array('layout' => 'not_exists_layout')); // trigger warning
	}

	public function testActionRenderOutputHeaders() {
//		$html = $this->getRenderResults(array('js' => "alert('" . __METHOD__ . "')"));
//		$this->assertEquals("alert('RenderTest::testActionRender')", $html);
//		$html = $this->getRenderResults(array('json' => "{name:'testJson'}"));
//		$this->assertEquals("{name:'testJson'}", $html);
//		$html = $this->getRenderResults(array('json' => "{name:'json'}", 'callback' => 'show'));
//		$this->assertEquals("show({name:'json'});", $html);
	}

	public function ttestSendFile() {
		//$this->redirect_to("/test/index");
		//$this->send_file(__FILE__);
	}

	public function ttestRedirectTo() {
		//$this->redirect_to("/test/index");
		//$this->send_file(__FILE__);
//		$html = $this->getRenderResults(array('location' => '/', 'status' => 301)); // move permanently redirection 301
	}
}
?>
