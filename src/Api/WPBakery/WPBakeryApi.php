<?php

namespace Xenioushk\BwlPluginApi\Api\WPBakery;

/**
 * Class for registering the WPBakery API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class WPBakeryApi
{

	/**
	 * WPB elements array.
	 *
	 * @var array
	 */
	public $wpb_elem = [];

	/**
	 * Register actions.
	 */
	public function register()
	{
		if (! empty($this->wpb_elem)) {

			add_action('vc_before_init', [$this, 'set_wpb_elem']);
		}
	}

	/**
	 * Add wp bakery elements.
	 *
	 * @param array $wpb_elem wp bakery elements array.
	 *
	 * @return $this
	 */
	public function add_wpb_elem(array $wpb_elem)
	{
		$this->wpb_elem = $wpb_elem;
		return $this;
	}

	/**
	 * Set WPBakery Elements
	 */
	public function set_wpb_elem()
	{

		vc_map([
			'name'                    => isset($this->wpb_elem['name']) ? $this->wpb_elem['name'] : '',
			'base'                    => isset($this->wpb_elem['base']) ? $this->wpb_elem['base'] : '',
			'category'                => isset($this->wpb_elem['category']) ? $this->wpb_elem['category'] : '',
			'content_element'         => true,
			'show_settings_on_create' => true,
			'controls'                => 'full',
			'icon'                    => isset($this->wpb_elem['icon']) ? $this->wpb_elem['icon'] : 'icon-bptm-vc-addon',
			'description'             => isset($this->wpb_elem['description']) ? $this->wpb_elem['description'] : '',
			'params'                  => isset($this->wpb_elem['category']) ? $this->wpb_elem['params'] : [],
		]);
	}
}
