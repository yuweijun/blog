<?php
/*
 * Created on May 31, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class ArrayTest extends PHPUnit_Framework_TestCase {

	public function testArrayMap() {
		$a = array('Post', 'Posts', 'activerecord', 'UserInfo');
		$v = array_map('strtolower', $a);
		$this->assertEquals('post', $v[0]);
		$this->assertEquals('userinfo', $v[3]);
	}

	public function testArrayDiff() {
		$a = array('Post', 'Posts', 'activerecord', 'UserInfo', 'except' => array('index'), 'Test');
		$b = array_diff($a, array('except' => $a['except']));
	}

}
?>
