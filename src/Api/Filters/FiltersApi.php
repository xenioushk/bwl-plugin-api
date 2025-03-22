<?php

namespace Xenioushk\BwlPluginApi\Api\Filters;

/**
 * Class for registering the Filters API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class FiltersApi
{

	/**
	 * Filters.
	 *
	 * @var array
	 */
	public $filters = [];

	/**
	 * Add filters.
	 *
	 * @param array $filters filters to add.
	 *
	 * @return $this
	 */
	public function add_filters(array $filters)
	{
		$this->filters = $filters;
		return $this;
	}

	/**
	 * Register filters.
	 */
	public function register()
	{
		if (! empty($this->filters)) {

			foreach ($this->filters as $filter) {
				$priority = isset($action['priority']) ? $action['priority'] : 10;
				$args_count = isset($action['args_count']) ? $action['args_count'] : 1;
				add_filter($filter['tag'], $filter['callback'], $priority, $args_count);
			}
		}
	}
}
