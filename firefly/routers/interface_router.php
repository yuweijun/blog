<?php
interface InterfaceRouter {

	/**
	 * add a new route.
	 */
	public function add($path, $route = array());

	/**
	 * return routes map array.
	 */
	public function get_map();

	/**
	 * recoginze request parameters and return parameters array.
	 */
	public function recognize_path($path, $map = array());

	/**
	 * generate url for specified options array.
	 */
	public function generate($options = array());
}
?>
