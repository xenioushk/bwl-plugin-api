<?php

namespace Xenioushk\BwlPluginApi\Api\Pages;

/**
 * Class for registering the Plugin Pages API.
 *
 * @package BwlPluginApi
 * @version 1.0.1
 * @author: Mahbub Alam Khan
 */
class PluginPagesApi
{

	/**
	 * Parent menu from the admin panel.
	 *
	 * @var string
	 */
	public $parent_menu;

	/**
	 * Plugin page settings.
	 *
	 * @var array
	 */
	public $plugin_pages_settings = [];

	/**
	 * Plugin taxonomy settings.
	 *
	 * @var array
	 */
	public $tax_settings = [];

	/**
	 * Plugin menu link.
	 *
	 * @var string
	 */
	public $plugin_menu_link;
	public $specific;

	/**
	 * Constructor.
	 *
	 * @param string $parent_menu Could be a Post type or it could be specific page.
	 * @param bool $specific Specific page or not.
	 * @example $parent_menu="settings.php"
	 */
	public function __construct($parent_menu = '', $specific = false)
	{
		$this->parent_menu = $parent_menu;
		$this->specific = $specific;
	}

	/**
	 * Register plugin pages.
	 */
	public function register()
	{
		if (! empty($this->plugin_pages_settings)) {

			if ($this->specific) {
				$this->plugin_menu_link = $this->parent_menu;
			} else {
				$this->plugin_menu_link = 'edit.php?post_type=' . $this->parent_menu;
			}

			add_action('admin_menu', [$this, 'add_plugin_pages_api']);
		}
	}

	/**
	 * Add plugin pages.
	 *
	 * @param array $plugin_pages_settings Plugin pages settings.
	 *
	 * @return $this
	 */
	public function add_plugin_pages(array $plugin_pages_settings)
	{
		$this->plugin_pages_settings = $plugin_pages_settings;
		return $this;
	}

	/**
	 * Add plugin pages.
	 */
	public function add_plugin_pages_api()
	{

		foreach ($this->plugin_pages_settings as $page) {

			add_submenu_page(
				$this->plugin_menu_link,
				$page['page_title'],
				$page['menu_title'],
				'manage_options',
				$page['menu_slug'],
				$page['cb']
			);
		}
	}
}
