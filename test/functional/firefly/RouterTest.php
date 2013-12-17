<?php
include_once('SessionStart.php');

class RouterTest extends FireflyTestBase {

	public function testUnknowRouterPath() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['fireflypath'] = '/users/1/posts/1/test';
		$this->setExpectedException('FireflyException');
		$request = new Request;
		Router :: recognize($request);
	}

	public function testRecognizeController() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$c = Router :: factory()->recognize_controller('/users/1/posts/1');
		$this->assertEquals('posts', $c);
		$c = Router :: factory()->recognize_controller('/users/1/posts/1/test');
	}

	public function testRecognizePath() {
		$cv = Router :: factory()->recognize_path("/2009/01/18");
		$this->assertEquals('posts', $cv['controller']);
		$this->assertEquals('find_by_date', $cv['action']);
		$this->assertEquals('2009', $cv['year']);
		$rp1 = Router :: factory()->recognize_path("module/test");
		$this->assertEquals('module', $rp1['controller']);
		$rp2 = Router :: factory()->recognize_path("module/test/index");
		$this->assertEquals('module', $rp2['controller']);
		$rp3 = Router :: factory()->recognize_path("module/test/index/index");
		$this->assertEquals('module/test', $rp3['controller']);
		$rp4 = Router :: factory()->recognize_path("module/test/index/index/1");
		$this->assertEquals('module/test/index', $rp4['controller']);
		$rp5 = Router :: factory()->recognize_path("module/test/index/index/index/1");
		$this->assertEquals('index/index/1', $rp5['others']);
	}

	public function testUrlFor() {
		$u1 = url_for(array("controller" => "posts", "action" => "find_by_date", "year" => "2009", "month" => "01", "day" => "18"));
		$u2 = url_for(array("controller" => "admin", "use_route" => "login"));
		$u3 = url_for(array('controller' => 'posts', 'action' => 'show', 'protocol' => 'https', 'port' => 3000, 'trailing_slash' => false, 'only_path' => false, 'anchor' => 'test', "year" => "2009", "month" => "01", "day" => "18", 'other' => 1, 'page' => 2, 'per_page' => 10, 'prefix' => '/threads/222'));
		$u4 = url_for(array('controller' => 'posts', 'action' => 'find_by_date', 'protocol' => 'https', 'port' => 3000, 'trailing_slash' => true, 'only_path' => false, 'anchor' => 'test', "year" => "2009", "month" => "01", "day" => "18", 'other' => 1, 'page' => 2, 'per_page' => 10, 'prefix' => '/users/234'));
		$u5 = url_for(array('controller' => 'posts', 'action' => 'find_by_date', 'protocol' => 'https', 'port' => 3000, 'trailing_slash' => true, 'only_path' => false, 'anchor' => 'test', "year" => "2009", "month" => "01", "day" => "18", 'other' => 1, 'page' => 2, 'per_page' => 10));
		$this->assertEquals('http://localhost/2009/01/18', $u1);
		$this->assertEquals('http://localhost/login', $u2);
		$this->assertEquals('https://localhost:3000/threads/222/users/posts/show?year=2009&month=01&day=18&other=1&page=2&per_page=10#test', $u3);
		$this->assertEquals('https://localhost:3000/users/234/2009/01/18/?other=1&page=2&per_page=10#test', $u4);
		$this->assertEquals('https://localhost:3000/2009/01/18/?other=1&page=2&per_page=10#test', $u5);
	}

	public function testRoutesFor() {
		$r1 = Router :: factory()->routes_for(array("controller" => "posts", "action" => "find_by_date", "year" => "2009", "month" => "01", "day" => "18", "page" => 1));
		$r2 = Router :: factory()->routes_for(array("controller" => "admin"));
		$r3 = Router :: factory()->routes_for(array("controller" => "admin", "action" => "index"));
		$this->assertEquals('/:year/:month/:day', $r1[0]);
		$this->assertEquals('/:controller/:action/:id', $r2[0]);
		$this->assertEquals('/:controller/:action/:id', $r3[0]);
	}

	public function testUrlRewritable() {
		$b = Router :: url_rewritable();
		$this->assertEquals(false, $b);
	}

	public function testAddRoute() {
		$rs = Router :: factory();
		$_GET['a'] = 111;
		$rs->add('test_add_router', array('controller' => 'admin', 'action' => 'login', 'defaults' => array('a' => 1, 'b' => 2), 'method' => 'get'));
		$p = $rs->recognize_path("/test_add_router");
		$this->assertEquals(111, $p['a']);
		$this->assertEquals('admin', $p['controller']);
	}

	public function testGetRoute() {
		$rs = Router :: factory();
		$routes = $rs->get_routes();
	}

	public function testGetResources() {
		$rs = Router :: factory();
		$s = $rs->get_resources();
	}

	public function testGetNamedRoutes() {
		$rs = Router :: factory();
		$nr = $rs->get_named_routes();
		$this->assertNotEquals(0, sizeof($nr));
		$rs->clear();
		$m = $rs->get_map();
		$this->assertEquals(true, empty($m));
		$nr2 = $rs->get_named_routes();
		$this->assertEquals(0, sizeof($nr2));
		$rs->reload();
	}

	public function testHttpVerbsRoute() {
		$rs = Router :: factory();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$c = Router :: factory()->recognize_path('/posts');
		$this->assertEquals('index', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$c = Router :: factory()->recognize_path('/posts/add');
		$this->assertEquals('add', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$c = Router :: factory()->recognize_path('/posts/1');
		$this->assertEquals('show', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$c = Router :: factory()->recognize_path('/posts');
		$this->assertEquals('create', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$c = Router :: factory()->recognize_path('/posts/1');
		$this->assertEquals('create_comment', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$c = Router :: factory()->recognize_path('/posts/1');
		$this->assertEquals('update', $c['action']);
		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$c = Router :: factory()->recognize_path('/posts/1');
		$this->assertEquals('destroy', $c['action']);
		$this->assertEquals(1, $c['id']);
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$c = Router :: factory()->recognize_path('/posts/1/edit');
		$this->assertEquals('edit', $c['action']);
		$this->assertEquals(1, $c['id']);
	}

	public function testChechRouteOptions() {
		$rs = Router :: factory();
		$rs->add('test_add_router', array('controller' => 'none_exists_controller', 'action' => 'login', 'defaults' => array('a' => 1, 'b' => 2), 'method' => 'get'));
		$p = $rs->recognize_path("/test_add_router");
		$this->assertEquals(false, $p);
		$rs->add('test_location', array('location' => '/login'));
		try {
			$p = $rs->recognize_path("/test_location");
		} catch( Exception $e ) {
			$this->assertContains('Cannot modify header information - headers already sent by', $e->getMessage());
		}
		$rs->reload();
	}

	public function testAddRouteException() {
		$this->setExpectedException('FireflyException');
		$rs = Router :: factory();
		$rs->add('test_non_exists_controller', array('no_controller' => 'non_exists_controller'));
		$p = $rs->recognize_path("test_non_exists_controller");
		$this->assertEquals(false, $p);
		$rs->reload();
	}

	public function testParseSymbol() {
		$rs = Router :: factory();
		$p = $rs->recognize_path("/users/1/2008/06/14");
		$this->assertEquals('posts', $p['controller']);
		$this->assertEquals('2009', $p['year']);
	}

}
?>
