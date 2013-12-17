<?php
class ApplicationController extends Controller {
	
	protected $helpers = array('application', 'text');
	
	protected function is_login() {
		if (!isset($_SESSION['user_id'])) {
			$this->redirect_to('/admin/login');
		}
	}
}
?>
