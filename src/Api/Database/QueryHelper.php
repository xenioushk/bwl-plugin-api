<?php

namespace Xenioushk\BwlPluginApi\Api\Database;

/**
 * Class handles the database queries.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class QueryHelper
{

	/**
	 * Table name.
	 *
	 * @var string $table
	 */
	public $table;

	/**
	 * Select fields.
	 *
	 * @var string $selectFields
	 */
	public $selectFields = '*';

	/**
	 * Query arguments.
	 *
	 * @var array $args
	 */
	public $args;

	/**
	 * Placeholders.
	 *
	 * @var array $placeholders
	 */
	public $placeholders;

	/**
	 * Date time column.
	 *
	 * @var string $dateTimeColumn
	 */
	public $dateTimeColumn;

	/**
	 * Order by string.
	 *
	 * @var string $orderByString
	 */
	public $orderByString = '';

	/**
	 * Group by string.
	 *
	 * @var string $groupByString
	 */
	public $groupByString = '';


	/**
	 * Get the data from the database.
	 */
	public function get_data()
	{
		global $wpdb;

		$this->args = $this->get_args();

		$query = "SELECT {$this->selectFields} FROM {$this->table} ";

		if (count($this->args)) {
			$this->placeholders = $this->create_placeholders();
			$query             .= $this->create_where_condition();
			$query             .= $this->groupByString;
			$query             .= $this->orderByString;
			$data               = $wpdb->get_results($wpdb->prepare($query, $this->placeholders), ARRAY_A);
		} else {
			$query .= $this->groupByString;
			$query .= $this->orderByString;
			$data   = $wpdb->get_results($query, ARRAY_A);
		}
		return $data;
	}


	/**
	 * Get the query arguments
	 */
	public function get_args()
	{

		$urlFields = [];

		if (isset($_GET['faq_id']) && ! empty($_GET['faq_id']) && is_numeric($_GET['faq_id'])) {
			$urlFields['post_id'] = sanitize_text_field($_GET['faq_id']) ?? '';
		}

		if (isset($_GET['date_range']) && ! empty($_GET['date_range']) && is_numeric($_GET['date_range'])) {
			$urlFields['date_range'] = sanitize_text_field($_GET['date_range']) ?? 7;
		} else {
			// Last 7 days views and likes.
			$urlFields['date_range'] = 7;
		}

		return array_filter($urlFields, function ($x) {
			return $x;
		});
	}

	/**
	 * Create query placeholders
	 */
	public function create_placeholders()
	{
		return array_map(function ($x) {
			return $x;
		}, $this->args);
	}

	/**
	 * Create where condition text.
	 */
	public function create_where_condition()
	{
		$whereQuery = '';

		if (count($this->args)) {
			$whereQuery = 'WHERE ';

			$currentPos = 0;

			foreach ($this->args as $index => $item) {
				$whereQuery .= $this->specifc_query($index);
				if ($currentPos != count($this->args) - 1) {
					$whereQuery .= ' AND ';
				}
				++$currentPos;
			}
		}

		return $whereQuery;
	}
	/**
	 * Generate specific query.
	 *
	 * @param string $index query index.
	 */
	public function specifc_query($index)
	{

		switch ($index) {
			case 'post_id':
				return 'post_id = %d';
			case 'date_range':
				return "$this->dateTimeColumn BETWEEN CURDATE() - INTERVAL %d DAY AND CURDATE()";
			default:
				return $index . ' = %s';
		}
	}
}
