<?php

namespace Xenioushk\BwlPluginApi\Api\Shortcodes;

/**
 * Class for registering the shortcodes API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class ShortcodesApi
{

	/**
	 * Shortcodes.
	 *
	 * @var array
	 */
	public $shortcodes = [];

	/**
	 * Add shortcodes.
	 *
	 * @param array $shortcodes Shortcodes to add.
	 *
	 * @return $this
	 */
	public function add_shortcodes(array $shortcodes)
	{ // phpcs:ignore
		$this->shortcodes = $shortcodes;
		return $this;
	}

	/**
	 * Register shortcodes.
	 */
	public function register()
	{
		if (! empty($this->shortcodes)) {

			foreach ($this->shortcodes as $shortcode) {
				add_shortcode($shortcode['tag'], $shortcode['callback']);
			}
		}
	}
}
