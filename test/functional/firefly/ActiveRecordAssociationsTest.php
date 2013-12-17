<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordAssociationsTest extends FireflyTestBase {

	public function testSelect0() {
		$u = User :: all(array('include' => array('posts'), 'limit' => 2));
		$ps = $u[0]->posts;
	}

	public function testSelect1() {
		$u = User :: find(1, array('include' => array('posts', 'userinfo', 'comments')));
		$this->assertEquals('admin', $u->userinfo->nickname);
		$this->assertEquals('admin', $u->user_login);
		$this->assertEquals('2', $u->posts[1]->id());
		$this->assertEquals('array', gettype($u->posts));
	}

	public function testSelect2() {
		Post :: clear_cache();
		$ps = Post :: select(array('include' => array('user', 'comments', 'userinfo')));
	}

	public function testSelect3() {
		Post :: clear_cache();
		$ps = Post :: select(array('include' => 'user'));
		$this->assertEquals('admin', $ps[0]->user->user_login);
	}

	public function testSelect4() {
		$u = User :: find(1, array('include' => 'userinfo'));
		$this->assertEquals('admin', $u->userinfo->nickname);
	}

	public function testHasManyAndIncludeString() {
		User :: clear_cache();
		$u = User :: find(1, array('include' => 'posts'));
	}

	public function testHasManyAndIncludeArray() {
		User :: clear_cache();
		$u = User :: find(1, array('include' => array('posts', 'nonexists_model_name')));
	}

	public function testHasManyByFindReadonly() {
		User :: clear_cache();
		$u = User :: find(1, array('include' => 'posts', 'readonly' => true));
		$this->assertNotEquals(0, count($u->posts));
		$this->assertEquals($u->user_login, $u->posts[0]->user->user_login);
		$u->user_login = 'david';
		$this->assertEquals($u->user_login, $u->posts[0]->user->user_login);
	}

	public function testHasManyByFind() {
		User :: clear_model_cache();
		$u = User :: find(1, array('include' => 'posts'));
		$this->assertNotEquals(0, count($u->posts));
		$this->assertEquals($u->user_login, $u->posts[0]->user->user_login);
		$u->user_login = 'david';
		$this->assertEquals($u->user_login, $u->posts[0]->user->user_login);
	}

	public function testBelongsTo() {
		User :: clear_cache();
		$ps = Post :: all(array('include' => 'user'));
		$this->assertEquals('admin', $ps[0]->user->user_login);
	}

	public function testHasOne() {
		User :: clear_model_cache();
		$u = User :: find(1);
		$this->assertEquals('admin', $u->userinfo->nickname);
		$this->assertEquals('admin', $u->userinfo->user->user_login);
	}

	public function testFinderSql() {
		$u = User :: find(1);
		$c = $u->comments;
		$this->assertEquals(true, $c[0]->is_readonly());
	}

	public function testHasManyComments() {
		$p = Post :: find(1);
		$p->comments;
		$p->save();
	}

	public function testInhefitAssociations() {
		$p = Posts :: find(1);
		$c = $p->comments;
		$u = Users :: find(1);
		$c = $u->comments;
		$ps = $u->posts;
	}

	public function testManyToManyAssociations() {
		$c = Client :: find(1);
		$s = $c->search_engines;
		$s0 = $s[0];
		$cls = $s0->clients;
	}

	public function testManyToManyAssociationsWithIncludes() {
		Client :: clear_cache();
		$c = Client :: find(1, array('include' => 'search_engines'));
		$s = $c->search_engines;
		$s0 = $s[0];
		$cls = $s0->clients;
	}

}
?>
