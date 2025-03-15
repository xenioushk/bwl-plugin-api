<?php

namespace Xenioushk\BwlPluginApi\Api\DashboardWidgets;

/**
 * Class for registering the Dashboard Widgets Api.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class DashboardWidgetsApi
{

	/**
	 * Dashboard Widgets.
	 *
	 * @var array
	 */
	public $dash_widgets = [];

	/**
	 * Register Dashboard Widgets.
	 */
	public function register()
	{
		add_action('admin_init', [$this, 'initialize']);
	}

	/**
	 * Initialize Dashboard Widgets.
	 */
	private function initialize()
	{
		add_action('wp_dashboard_setup', [$this, 'register_all_dashboard_widgets']);
	}

	/**
	 * Add Dashboard Widgets.
	 *
	 * @param array $dash_widgets Dashboard Widgets to add.
	 *
	 * @return $this
	 */
	public function add_dash_widgets(array $dash_widgets)
	{
		$this->dash_widgets = $dash_widgets;
		return $this;
	}

	/**
	 * Register Dashboard Widgets.
	 */
	public function register_all_dashboard_widgets()
	{
		if (! empty($this->dash_widgets)) {

			foreach ($this->dash_widgets as $widget) {

				wp_add_dashboard_widget(
					$widget['slug'],
					$widget['title'],
					$widget['cb']
				);
			}
		}
	}
}
