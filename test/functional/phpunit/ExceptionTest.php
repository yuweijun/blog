<?php
class ExceptionTest extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testException() {
		// how to use the @expectedException annotation to test whether an exception is thrown inside the tested code.
		throw new InvalidArgumentException("error arguments.");
	}

	public function testCatchException() {
		try {
			// ... Code that is expected to raise an exception ...
			 throw new Exception("exception test.");
		} catch(Exception $expected) {
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	/**
	* @expectedException PHPUnit_Framework_Error
	*/
	public function testFailingInclude() {
		// PHPUnit_Framework_Error_Notice and
		// PHPUnit_Framework_Error_Warning represent PHP notices and warning, respectively.
		include 'not_existing_file.php';
	}
}
?>