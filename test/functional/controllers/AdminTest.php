<?php
include_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly_test_base.php');

class AdminTest extends FireflyTestBase {

	public function testAdminLogin() {
		$c = file_get_contents("http://localhost/admin/login");
	}

}
?>
