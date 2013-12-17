<?php
class ActiveRecord {
	protected static $table_name = '';
}
class Post extends ActiveRecord {
	protected static $table_name = 'post';
	protected static $primary_key = 'post_id';
}
class Comment extends ActiveRecord {
	protected static $primary_key = 'comment_id';
}

class ReflectionClassTest extends PHPUnit_Framework_TestCase {

	public function testReflectionClass() {
		$model_name = 'comment';
		$model_class = new ReflectionClass($model_name);
		$props = $model_class->getStaticProperties();
		var_dump($props);
		$props['table_name'] = 'comments';
		var_dump($props);

		$props['table_name'] = null;
		$model_name1 = 'post';
		$model_class1 = new ReflectionClass($model_name1);
		$props1 = $model_class1->getStaticProperties();
		var_dump($props1);
		echo "===================\n";
	}

	public function testReflectionClassReference() {
		$model_name = 'comment';
		$model_class = new ReflectionClass($model_name);
		$props = $model_class->getStaticProperties();
		var_dump($props);
		$props['table_name'] = 'comments';

		// problem of Reflection!!!
		$model_name1 = 'post';
		$model_class1 = new ReflectionClass($model_name1);
		$props1 = $model_class1->getStaticProperties();
		var_dump($props1); // $props['table_name'] = 'comments';
	}

	public function testReflectionClassReference() {
		$model_name1 = 'post';
		$model_class1 = new ReflectionClass($model_name1);
		$props1 = $model_class1->getStaticProperties();
		var_dump($props1);

		$model_name = 'comment';
		$model_class = new ReflectionClass($model_name);
		$props = $model_class->getStaticProperties();
		var_dump($props);
	}
}
?>
