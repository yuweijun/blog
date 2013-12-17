<?php
if (defined('DEBUG_LEVEL')) {
	error_reporting(E_ALL);
} else {
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);
}

// php exception don't catch FETAL ERROR.
// function exception_error_handler($message, $code, $severity, $filename, $lineno) {
//	 throw new FireflyException($message);
// }
// set_error_handler("exception_error_handler");

class FireflyException extends Exception {

	public function __toString() {
		return __CLASS__;
	}

	/**
	 * Exception report has an uniform format.
	 */
	public static function exception_report($exception) {
		if(file_exists(FIREFLY_APP_DIR . DS . 'views' . DS . 'shares' . DS . 'exception_report.php')) {
			include_once(FIREFLY_APP_DIR . DS . 'views' . DS . 'shares' . DS . 'exception_report.php');
		} else {
			include_once(FIREFLY_LIB_DIR . DS . 'view' . DS . 'exception_report.php');
		}
		Logger::output();
	}

}
?>
