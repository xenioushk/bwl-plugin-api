<?php
namespace Xenioushk\BwlPluginApi\Api\Database;

use WP_Error;

/**
 * Class for CRUD operations on a database table.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class QueryManagerApi
{

    /**
     * The name of the database table.
     *
     * @var string
     */
    private $table;

    /**
     * Constructor.
     *
     * @param string $table_name The name of the database table.
     */
    public function __construct($table_name)
    {
        global $wpdb;
        $this->table = $table_name;
    }

    /**
     * Insert a new item into the database.
     *
     * @param array $data The data to insert.
     *
     * @return int|WP_Error The ID of the inserted item or a WP_Error object on failure.
     */
    public function insert($data)
    {
        global $wpdb;
        $result = $wpdb->insert($this->table, $data);
        if ($result === false) {
            return new WP_Error('db_insert_error', 'Failed to insert data');
        }
        return $wpdb->insert_id;
    }

    /**
     * Fetch items with pagination and optional filters.
     *
     * @param  array $args array of arguments for fetching items.
     * @return array An array containing the fetched items, total count, current page, and items per page.
     */
    public function get_items($args = [])
    {

        $args = array_merge(
            [
            'selected_fields' => '*',
            'page'            => 1,
            'per_page'        => 1,
            'filters'         => [],
            'order_by'        => 'ID',
            'order_dir'       => 'DESC',
            ], $args
        );

        // Extract the arguments
        $selected_fields = $args['selected_fields'];
        $page            = $args['page'];
        $per_page        = $args['per_page'];
        $filters         = $args['filters'];
        $order_by        = $args['order_by'];
        $order_dir       = $args['order_dir'];

        global $wpdb;

        $offset        = ($page - 1) * $per_page;
        $where_clauses = [];
        $params        = [];

        if (! empty($filters)) {
            foreach ($filters as $key => $value) {
                $operator = '=';
                $field    = $key;

                // If key contains operator (e.g., "age >=")
                if (preg_match('/^(\w+)\s*(>=|<=|<>|!=|=|>|<)$/', $key, $matches)) {
                    $field    = $matches[1];
                    $operator = $matches[2];
                }

                // Support format like ['value' => 10, 'operator' => '>']
                if (is_array($value) && isset($value['value'], $value['operator'])) {
                    $operator = strtoupper($value['operator']);
                    $value    = $value['value'];
                }

                // Special handling for DATE comparison on DATETIME column
                if (in_array($field, ['vote_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $where_clauses[] = "DATE(`$field`) $operator %s";
                } else {
                    $where_clauses[] = "`$field` $operator %s";
                }

                $params[] = $value;
            }
        }

        $where_sql = '';
        if (! empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        // Sanitize order_by and order_dir
        $order_by  = preg_replace('/[^\w_]/', '', $order_by);
        $order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

        // Count total
        $count_sql = "SELECT COUNT(*) FROM {$this->table} $where_sql";
        $total     = $wpdb->get_var($wpdb->prepare($count_sql, ...$params));

        // Paginated data
        $query_sql = "SELECT {$selected_fields} FROM {$this->table} $where_sql ORDER BY `$order_by` $order_dir LIMIT %d OFFSET %d";
        $params[]  = $per_page;
        $params[]  = $offset;

        $rows = $wpdb->get_results($wpdb->prepare($query_sql, ...$params), ARRAY_A);

        return [
            'data'     => $rows,
            'total'    => (int) $total,
            'page'     => $page,
            'per_page' => $per_page,
        ];
    }

    /**
     * Fetch single item by ID.
     *
     * @param  int $args The ID of the item to fetch.
     * @return array|WP_Error The fetched item or a WP_Error object on failure.
     */
    public function get_item($args = [])
    {

        $args = array_merge(
            [
            'selected_fields' => '*',
            'key'             => 'ID',
            'id'              => 0,
            ], $args
        );

        extract($args); // phpcs:ignore
        global $wpdb;
        $sql = "SELECT {$selected_fields} FROM {$this->table} WHERE {$key} = %d LIMIT 1";
        return $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_A);
    }

    /**
     * Update an existing item in the database.
     *
     * @param  int   $id   The ID of the item to update.
     * @param  array $data The data to update.
     * @return array|WP_Error The fetched item or a WP_Error object on failure.
     */
    public function update($id, $data)
    {
        global $wpdb;
        $result = $wpdb->update($this->table, $data, ['ID' => $id]);
        if ($result === false) {
            return new WP_Error('db_update_error', 'Failed to update data');
        }
        return $result;
    }

    /**
     * Delete an existing item in the database.
     *
     * @param  int $id The ID of the item to update.
     * @return array|WP_Error The fetched item or a WP_Error object on failure.
     */
    public function delete($id)
    {
        global $wpdb;
        $result = $wpdb->delete($this->table, ['ID' => $id]);
        if ($result === false) {
            return new WP_Error('db_delete_error', 'Failed to delete data');
        }
        return $result;
    }
}
