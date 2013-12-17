<?php
require_once 'PHPUnit/Extensions/PerformanceTestCase.php';

class PerformanceTest extends PHPUnit_Extensions_PerformanceTestCase {
	public function testPerformance() {
		$this->setMaxRunningTime(2);
		sleep(1);
	}
}
?>