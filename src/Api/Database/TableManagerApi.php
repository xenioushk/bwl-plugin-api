<?php

namespace Xenioushk\BwlPluginApi\Api\Database;

/**
 * Class handles the database settings.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class TableManagerApi
{

	/**
	 *  Instance of the WPDB.
	 *
	 * @var object $wpdb
	 */
	private $wpdb;

	/**
	 * Initalize tables info.
	 *
	 * @var array $tables_info
	 */
	public $tables_info = [];

	/**
	 * Constructor for the class.
	 *
	 * @param object $wpdb  Instance of the WPDB.
	 */
	public function __construct($wpdb)
	{
		$this->wpdb = $wpdb;
	}

	/**
	 * Register tables data.
	 *
	 * @param array $tables_info tables info.
	 */
	public function register_tables_info(array $tables_info)
	{
		$this->tables_info = $tables_info;
		return $this;
	}

	/**
	 * Trigger database table creation only plugin activation time.
	 */
	public function register()
	{

		$this->create_tables();
	}

	/**
	 * Create tables.
	 */
	public function create_tables()
	{

		foreach ($this->tables_info as $table) {
			$this->create_custom_table($table['table_name'], $table['schema']);
		}
	}

	/**
	 * Create custom table.
	 *
	 * @param string $table_name table name.
	 * @param string $schema table schema.
	 */
	private function create_custom_table($table_name, $schema)
	{

		// Check if table exists
		$table_exists = $this->wpdb->get_var($this->wpdb->prepare('SHOW TABLES LIKE %s', $table_name)); // phpcs:ignore

		if ($table_exists !== $table_name) {
			$sql = "CREATE TABLE $table_name ( $schema );";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		}
	}
}
