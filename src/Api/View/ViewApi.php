<?php

namespace Xenioushk\BwlPluginApi\Api\View;

/**
 * Class for registering the View API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class ViewApi
{
	protected $data = [];

	public function __construct($data = [])
	{
		$this->data = $data;
	}

	public function render($view_file = "", $data = [])
	{

		if (file_exists($view_file)) {
			extract($data);
			include_once $view_file;
		} else {
			echo "View file not found: " . $view_file;
		}
	}
}
