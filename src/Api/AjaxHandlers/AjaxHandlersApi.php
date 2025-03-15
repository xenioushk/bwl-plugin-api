<?php

namespace Xenioushk\BwlPluginApi\Api\AjaxHandlers;

/**
 * Class for registering the Ajax Handlers Api.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class AjaxHandlersApi
{

	/**
	 * Ajax requests.
	 *
	 * @var array
	 */
	public $ajaxrequests = [];

	/**
	 * Add ajax handlers.
	 *
	 * @param array $ajaxrequests ajax requests to add.
	 *
	 * @return $this
	 */
	public function add_ajax_handlers(array $ajaxrequests)
	{
		$this->ajaxrequests = $ajaxrequests;
		return $this;
	}

	/**
	 * Register ajax handlers.
	 */
	public function register()
	{
		if (! empty($this->ajaxrequests)) {

			foreach ($this->ajaxrequests as $ajaxrequest) {
				add_action("wp_ajax_{$ajaxrequest['tag']}", $ajaxrequest['callback']);
				add_action("wp_ajax_nopriv_{$ajaxrequest['tag']}", $ajaxrequest['callback']);
			}
		}
	}
}