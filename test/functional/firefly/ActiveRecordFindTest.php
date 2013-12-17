<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordFindTest extends FireflyTestBase {

	public function setUp() {
		Post :: clear_cache();
	}

	public function testFindById() {
		$p = Post :: find(1);
		$this->assertEquals(1, $p->id());
		$this->assertEquals(1, $p->attributes['ID']);
		User :: find(1);
	}

	public function testFindByIds() {
		$p = Post :: find(array(1, 2));
		$this->assertEquals(2, sizeof($p));
		$p = Post :: find(array(1));
		$this->assertEquals(1, sizeof($p));
	}

	/**
	 * expectedException FireflyException
	 */
	public function testFindByNonExistsId() {
		$this->setExpectedException('FireflyException');
		$p = Post :: find(4);
	}

	public function testFindConditions() {
		$q = Comment :: find('all', array('conditions' => array('comment_post_ID' => array(1)), 'lock' => true));
		$q = Comment :: find('all', array('conditions' => array('comment_post_ID' => array(1)), 'lock' => 'LOCK IN SHARE MODE'));
	}

	public function testFindByConditionsArray() {
		$p = Post :: find('all', array('conditions' => 'id < 10'));
		$this->assertEquals(6, count($p));
	}

	public function testFindByConditionsHash() {
		$p = Post :: find('all', array('conditions' => array('post_parent' => 120)));
		$this->assertEquals(3, count($p));
	}

	public function testFindFirst() {
		$p = Post :: find('first', array('conditions' => array('post_parent' => 120)));
		$o = clone $p;
		$this->assertEquals(121, $p->id());
		$this->assertEquals(null, $o->id());
	}

	public function testFindLast() {
		$p = Post :: find('last', array('conditions' => array('post_parent' => 120)));
		$this->assertEquals(127, $p->id());
		$p1 = Post :: last(array('order' => 'ID asc, post_type desc'));
		$u = $p1->becomes('Post');
		$p2 = Post :: last(array('order' => 'ID'));
	}

	public function testFindAndLimit() {
		$p = Post :: find('all', array('conditions' => array('post_parent' => 120), 'limit' => 2, 'offset' => 2));
		$this->assertEquals(1, count($p));
	}

	public function testFindAndGroup() {
		$p = Post :: find('all', array('select' => 'post_type, count(*) total', 'group' => 'post_type'));
		$this->assertEquals(1, $p[0]->total);
	}

	public function testFindAndGroupAndOrder() {
		$p = Post :: find('all', array('select' => 'post_type, count(*) total', 'group' => 'post_type', 'order' => 'total'));
		$this->assertEquals(1, $p[0]->total);
	}

	public function testFirst() {
		$p = Post :: first();
		$this->assertEquals(1, $p->id());
		$p->attribute_names();
		$r = $p->is_readonly();
		$this->assertEquals(false, $r);
		$n = $p->is_new_object();
		$this->assertEquals(false, $n);
	}

	public function testSlaveFirst() {
		$p = Post :: first(array('readonly' => true));
		$this->assertEquals(1, $p->id());
		$p->attribute_names();
		$this->assertEquals(true, $p->is_readonly());
		$this->assertEquals(false, $p->is_new_object());
		$o = new Post();
		$this->assertEquals(true, $o->is_new_object());
	}

	public function testFirstAndHashConditions() {
		$p = Post :: first(array('conditions' => array('post_date' => '2008-08-25 14:49:00')));
		$this->assertEquals(8, $p->id());
	}

	public function testFirstAndArrayConditions() {
		$p = Post :: first(array('conditions' => array("post_date <= %s and post_author = %d", '2008-08-25 14:49:00', 1)));
		$p = Post :: first(array('conditions' => array("post_date <= ? and post_author = ?", '2008-08-25 14:49:00', 1)));
		$this->assertEquals(8, $p->id());
	}

	public function testLastAndStringConditions() {
		$p = Post :: last(array('conditions' => "post_date >= '2008-08-25 14:49:00'"));
	}

	public function testSetter() {
		$p = Post :: last();
		$p->name = 'test';
	}

	public function testGetterException() {
		$this->setExpectedException("FireflyException");
		$p = Post :: last();
		echo $p->name;
	}

	public function testMethodMissingException() {
		$this->setExpectedException("FireflyException");
		$p = Post :: last();
		echo $p->name();
	}

	public function testLastDesc() {
		$p = Post :: last(array('order' => 'id DESC'));
		$this->assertEquals(1, $p->id());
	}

	public function testFindAll() {
		$p = Post :: all();
		$this->assertEquals(1, $p[0]->id());
	}

	public function testFindAndSelectAndJoins() {
		$p = Post :: first(array('joins' => 'LEFT JOIN users ON users.ID = posts.post_author'));
		$p = Post :: first(array('select' => 'posts.ID, users.user_nicename', 'joins' => 'LEFT JOIN users ON users.ID = posts.post_author'));
		$this->assertEquals(1, $p->id());
		$this->assertEquals('admin', $p->user_nicename);
	}

	public function testFindAndFrom() {
		$p = Post :: first(array('select' => 'posts.ID, users.user_nicename', 'from' => 'posts', 'joins' => 'LEFT JOIN users ON users.ID = posts.post_author'));
		$this->assertEquals(1, $p->id());
		$this->assertEquals('admin', $p->user_nicename);
	}

	public function testQuoteValue() {
		$p = Post :: first(array('conditions' => array('post_date' => '2008-08-25 14:49:00', 'post_author' => 1)));
		$this->assertEquals(8, $p->id());
		$p = Post :: first(array('conditions' => array('pinged' => null)));
		$this->assertEquals(null, $p);
		$p = Post :: first(array('conditions' => array('id' => array(1, 2, 3))));
		$this->assertEquals(1, $p->id());
		$p = Post :: first(array('conditions' => array('post_name' => array('hello-world', 'about'))));
		$this->assertEquals(2, $p->id());
		Post :: find(array(1, 2, 3), array('conditions' => "post_title like '%h%'"));
	}

	public function testQuoteValueException() {
		$this->setExpectedException('FireflyException');
		Post :: find('all', array('conditions' => array('id' => array())));
	}

	public function testFindBySqlAndFilter() {
		$p = Post :: find_by_sql("select * from posts where post_name like '%h%'");
		$this->assertEquals(28, sizeof($p));
		$this->assertEquals(1, $p[0]->id());
		$p1 = Post :: filter($p, array('post_name' => 'hello-world'));
		$this->assertEquals(1, count($p1));
		$p2 = Post :: filter($p, array('post_name' => 'hello-world', 'post_author' => 1));
		$this->assertEquals(1, count($p2));
		$p3 = Post :: filter($p, array('post_name' => 'hello-world', 'post_author' => 0));
		$this->assertEquals(0, count($p3));
		$p4 = Post :: filter($p, array('post_name' => 'hello-world', 'post_author' => 0), true);
		$this->assertEquals(28, count($p4));
	}

	public function testSumAndAvgAndCount() {
		$c = Post :: find('all', array('select' => 'count(*) as cc', 'conditions' => array('post_name' => 'hello-world')));
		$this->assertEquals(1, $c[0]->cc);
		$s = Post :: find('all', array('select' => 'sum(comment_count) as cc'));
		$a = Post :: find('all', array('select' => 'avg(comment_count) as cc'));
		$b = Post :: find('all', array('select' => 'id, count(*) rows', 'group' => 'posts.ID', 'joins' => 'INNER JOIN comments ON comments.comment_post_ID=posts.ID', 'having' => 'rows > 0'));
	}

	public function testGetColumn() {
		$columns = Post :: get_columns();
		$this->assertEquals('ID', $columns[0]);
		$this->assertEquals('post_author', $columns[1]);
		$inspects = Post :: inspect();
	}

	public function testFindIdsException() {
		$this->setExpectedException('FireflyException');
		Post :: find(new StdClass());
	}

	public function testFindOptionsException() {
		$this->setExpectedException('FireflyException');
		Post :: find('first', new StdClass());
	}

	public function testFindNonIdException() {
		$this->setExpectedException('FireflyException');
		Post :: find(array());
	}

	public function testGetTableNameAndPrimaryKey() {
		$table_name = User :: get_table_name();
		$this->assertEquals('users', $table_name);
		$primary_key = User :: get_primary_key();
		$this->assertEquals('ID', $primary_key);
	}
}

?>