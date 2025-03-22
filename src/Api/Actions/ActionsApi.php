<?php

namespace Xenioushk\BwlPluginApi\Api\Actions;

/**
 * Class for registering the Actions API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class ActionsApi
{

	/**
	 * Actions.
	 *
	 * @var array
	 */
	public $actions = [];

	/**
	 * Add actions.
	 *
	 * @param array $actions actions to add.
	 *
	 * @return $this
	 */
	public function add_actions(array $actions)
	{
		$this->actions = $actions;
		return $this;
	}

	/**
	 * Register actions.
	 */
	public function register()
	{
		if (! empty($this->actions)) {

			foreach ($this->actions as $action) {
				$priority = isset($action['priority']) ? $action['priority'] : 10;
				$args_count = isset($action['args_count']) ? $action['args_count'] : 1;
				add_action($action['tag'], $action['callback'], $priority, $args_count);
			}
		}
	}
}
