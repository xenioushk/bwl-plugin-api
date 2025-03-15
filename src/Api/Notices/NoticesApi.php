<?php

namespace Xenioushk\BwlPluginApi\Api\Notices;

/**
 * Class for registering the Notices API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class NoticesApi
{

	/**
	 * Notices array.
	 *
	 * @var array
	 */
	public $notices = [];

	/**
	 * Add notices.
	 *
	 * @param array $notices notices array.
	 *
	 * @return $this
	 */
	public function add_notices(array $notices)
	{
		$this->notices = $notices;
		return $this;
	}

	/**
	 * Register notices.
	 */
	public function register()
	{
		if (! empty($this->notices)) {

			foreach ($this->notices as $notice_data) {

				add_action('admin_notices', function () use ($notice_data) {
					call_user_func($notice_data['callback'], $notice_data['notice']);
				});
			}
		}
	}
}
