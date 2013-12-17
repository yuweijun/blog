<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class ActiveRecordInheritTest extends FireflyTestBase {

	public function setUp() {
		Posts :: clear_cache();
	}

	public function testFindById() {
		$p = Posts :: find(1);
		$this->assertEquals(1, $p->id());
		$this->assertEquals(1, $p->attributes['ID']);
		User :: find(1);
	}

	public function testFindByIds() {
		$p = Posts :: find(array(1, 2));
		$this->assertEquals(2, sizeof($p));
		$p = Posts :: find(array(1));
		$this->assertEquals(1, sizeof($p));
	}

	/**
	 * expectedException FireflyException
	 */
	public function testFindByNonExistsId() {
		$this->setExpectedException('FireflyException');
		$p = Posts :: find(4);
	}

	public function testFindByConditionsArray() {
		$p = Posts :: find('all', array('conditions' => 'id < 10'));
		$this->assertEquals(6, count($p));
	}

	public function testFindByConditionsHash() {
		$p = Posts :: find('all', array('conditions' => array('post_parent' => 120)));
		$this->assertEquals(3, count($p));
	}

	public function testFindFirst() {
		$p = Posts :: find('first', array('conditions' => array('post_parent' => 120)));
		$o = clone $p;
		$this->assertEquals(121, $p->id());
		$this->assertEquals(null, $o->id());
	}

	public function testFindLast() {
		$p = Posts :: find('last', array('conditions' => array('post_parent' => 120)));
		$this->assertEquals(127, $p->id());
		$p1 = Posts :: last(array('order' => 'ID asc, post_type desc'));
		$u = $p1->becomes('Posts');
		$p2 = Posts :: last(array('order' => 'ID'));
	}

	public function testFindAndLimit() {
		$p = Posts :: find('all', array('conditions' => array('post_parent' => 120), 'limit' => 2, 'offset' => 2));
		$this->assertEquals(1, count($p));
	}

	public function testFindAndGroup() {
		$p = Posts :: find('all', array('select' => 'post_type, count(*) total', 'group' => 'post_type'));
		$this->assertEquals(1, $p[0]->total);
	}

	public function testFindAndGroupAndOrder() {
		$p = Posts :: find('all', array('select' => 'post_type, count(*) total', 'group' => 'post_type', 'order' => 'total'));
		$this->assertEquals(1, $p[0]->total);
	}

	public function testFirst() {
		$p = Posts :: first();
		$this->assertEquals(1, $p->id());
		$p->attribute_names();
		$r = $p->is_readonly();
		$this->assertEquals(false, $r);
		$n = $p->is_new_object();
		$this->assertEquals(false, $n);
	}

	public function testSlaveFirst() {
		$p = Posts :: first(array('readonly' => true));
		$this->assertEquals(1, $p->id());
		$p->attribute_names();
		$this->assertEquals(true, $p->is_readonly());
		$this->assertEquals(false, $p->is_new_object());
		$o = new Posts();
		$this->assertEquals(true, $o->is_new_object());
	}

	public function testFirstAndHashConditions() {
		$p = Posts :: first(array('conditions' => array('post_date' => '2008-08-25 14:49:00')));
		$this->assertEquals(8, $p->id());
	}

	public function testFirstAndArrayConditions() {
		$p = Posts :: first(array('conditions' => array("post_date <= %s", '2008-08-25 14:49:00')));
		$this->assertEquals(8, $p->id());
	}

	public function testLastAndStringConditions() {
		$p = Posts :: last(array('conditions' => "post_date >= '2008-08-25 14:49:00'"));
	}

	public function testSetter() {
		$p = Posts :: last();
		$p->name = 'test';
	}

	public function testGetterException() {
		$this->setExpectedException("FireflyException");
		$p = Posts :: last();
		echo $p->name;
	}

	public function testMethodMissingException() {
		$this->setExpectedException("FireflyException");
		$p = Posts :: last();
		echo $p->name();
	}

	public function testLastDesc() {
		$p = Posts :: last(array('order' => 'id DESC'));
		$this->assertEquals(1, $p->id());
	}

	public function testFindAll() {
		$p = Posts :: all();
		$this->assertEquals(1, $p[0]->id());
	}

	public function testFindAndSelectAndJoins() {
		$p = Posts :: first(array('joins' => 'LEFT JOIN users ON users.ID = Posts.post_author'));
		$p = Posts :: first(array('select' => 'Posts.ID, users.user_nicename', 'joins' => 'LEFT JOIN users ON users.ID = Posts.post_author'));
		$this->assertEquals(1, $p->id());
		$this->assertEquals('admin', $p->user_nicename);
	}

	public function testFindAndFrom() {
		$p = Posts :: first(array('select' => 'Posts.ID, users.user_nicename', 'from' => 'Posts', 'joins' => 'LEFT JOIN users ON users.ID = Posts.post_author'));
		$this->assertEquals(1, $p->id());
		$this->assertEquals('admin', $p->user_nicename);
	}

	public function testQuoteValue() {
		$p = Posts :: first(array('conditions' => array('post_date' => '2008-08-25 14:49:00', 'post_author' => 1)));
		$this->assertEquals(8, $p->id());
		$p = Posts :: first(array('conditions' => array('pinged' => null)));
		$this->assertEquals(null, $p);
		$p = Posts :: first(array('conditions' => array('id' => array(1, 2, 3))));
		$this->assertEquals(1, $p->id());
		$p = Posts :: first(array('conditions' => array('post_name' => array('hello-world', 'about'))));
		$this->assertEquals(2, $p->id());
		Posts :: find(array(1, 2, 3), array('conditions' => "post_title like '%h%'"));
	}

	public function testQuoteValueException() {
		$this->setExpectedException('FireflyException');
		Posts :: find('all', array('conditions' => array('id' => array())));
	}

	public function testFindBySqlAndFilter() {
		$p = Posts :: find_by_sql("select * from Posts where post_name like '%h%'");
		$this->assertEquals(28, sizeof($p));
		$this->assertEquals(1, $p[0]->id());
		$p1 = Posts :: filter($p, array('post_name' => 'hello-world'));
		$this->assertEquals(1, count($p1));
		$p2 = Posts :: filter($p, array('post_name' => 'hello-world', 'post_author' => 1));
		$this->assertEquals(1, count($p2));
		$p3 = Posts :: filter($p, array('post_name' => 'hello-world', 'post_author' => 0));
		$this->assertEquals(0, count($p3));
		$p4 = Posts :: filter($p, array('post_name' => 'hello-world', 'post_author' => 0), true);
		$this->assertEquals(28, count($p4));
	}

	public function testSumAndAvgAndCount() {
		$c = Posts :: find('all', array('select' => 'count(*) as cc', 'conditions' => array('post_name' => 'hello-world')));
		$this->assertEquals(1, $c[0]->cc);
		$s = Posts :: find('all', array('select' => 'sum(comment_count) as cc'));
		$a = Posts :: find('all', array('select' => 'avg(comment_count) as cc'));
		$b = Posts :: find('all', array('select' => 'id, count(*) rows', 'group' => 'Posts.ID', 'joins' => 'INNER JOIN comments ON comments.comment_post_ID=Posts.ID', 'having' => 'rows > 0'));
	}

	public function testGetColumn() {
		$columns = Posts :: get_columns();
		$this->assertEquals('ID', $columns[0]);
		$this->assertEquals('post_author', $columns[1]);
		$inspects = Posts :: inspect();
	}

	public function testFindIdsException() {
		$this->setExpectedException('FireflyException');
		Posts :: find(new StdClass());
	}

	public function testFindOptionsException() {
		$this->setExpectedException('FireflyException');
		Posts :: find('first', new StdClass());
	}

	public function testFindNonIdException() {
		$this->setExpectedException('FireflyException');
		Posts :: find(array());
	}

	public function testGetTableNameAndPrimaryKey() {
		$table_name = User :: get_table_name();
		$this->assertEquals('users', $table_name);
		$primary_key = User :: get_primary_key();
		$this->assertEquals('ID', $primary_key);
	}
}

?>