<?php

namespace Xenioushk\BwlPluginApi\Api\WPBakery;

/**
 * Class for registering the WPBShortcodesApi API.
 *
 * @package BwlPluginApi
 * @version 1.0.1
 * @author: Mahbub Alam Khan
 */
class WPBShortcodesApi
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
		if (! empty($this->shortcodes) && function_exists('vc_add_shortcode_param')) {

			foreach ($this->shortcodes as $shortcode) {

				$scripts = isset($shortcode['scripts']) ? $shortcode['scripts'] : null;
				vc_add_shortcode_param($shortcode['tag'], $shortcode['callback'], $scripts);
			}
		}
	}
}
