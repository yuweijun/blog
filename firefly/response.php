<?php
include_once('mime_type.php');

class Response {
	private $headers = array();
	private $content_type = 'text/html';

	public function __construct() {
	}

	public function add_header($header) {
		array_push($this->headers, $header);
	}

	public function send_headers() {
		foreach($this->headers as $header) {
			header($header);
		}
	}

	public function set_header_status($status_code) {
		$header = Headers :: set_header_status($status_code);
		array_push($this->headers, empty($header) ? false : $header);
	}

	public function set_content_type($content_type) {
		$this->content_type = $content_type;
		array_push($this->headers, 'Content-Type: ' . $content_type);
	}

	public function set_content_type_by_extension($extension) {
		$find = false;
		foreach(MimeType :: get_mime_types() as $key => $value) {
			if($extension == $key) {
				$this->content_type = $value;
				if($value != 'text/html') {
					array_push($this->headers, 'Content-Type: ' . $value);
				}
				$find = true;
				break;
			}
		}
		if(!$find) {
			array_push($this->headers, 'Content-Type: application/force-download');
		}
	}

	public function get_content_type() {
		return $this->content_type;
	}

	public function redirect_to($url, $status = 302) {
		$this->set_header_status($status);
		array_push($this->headers, 'Location: ' . $url);
		$this->send_headers();
	}

	public function send_file($file) {
		if(!is_file($file)) {
			$this->set_header_status(404);
			exit;
		}

		$len = filesize($file);
		$info = pathinfo($file);
		$extension = strtolower($info['extension']);
		$filename = strtolower($info['basename']);

		header('Pragma: no-cache');
		header('Expires: Thu, 25 Dec 2008 23:17:14 GMT');
		header('Last-Modified: ' . date('r'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		// header("Cache-control: private"); // fix a bug for IE 6.x
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . $filename . ';');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $len);
		$this->set_content_type_by_extension($extension);
		readfile($file);
	}

	public function __toString() {
		return 'response';
	}

}
?>
