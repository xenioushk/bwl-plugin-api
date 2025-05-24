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
    public function get_items($args = []) {
        $args = array_merge(
            [
                'selected_fields' => '*',
                'page'            => 1,
                'per_page'        => 999999999,
                'filters'         => [],
                'having'          => [],
                'order_by'        => 'ID',
                'order_dir'       => 'DESC',
                'group_by'        => [],
            ],
            $args
        );

        $selected_fields = $args['selected_fields'];
        $page            = $args['page'];
        $per_page        = $args['per_page'];
        $filters         = $args['filters'];
        $having          = $args['having'];
        $order_by        = $args['order_by'];
        $order_dir       = $args['order_dir'];
        $group_by        = $args['group_by'];

        global $wpdb;

        $offset        = ($page - 1) * $per_page;
        $where_clauses = [];
        $where_params  = [];

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $operator = '=';
                $field    = $key;

                if (preg_match('/^(\w+)\s*(>=|<=|<>|!=|=|>|<)$/', $key, $matches)) {
                    $field    = $matches[1];
                    $operator = $matches[2];
                }

                if (is_array($value) && isset($value['value'], $value['operator'])) {
                    $operator = strtoupper($value['operator']);
                    $value    = $value['value'];
                }

                if (in_array($field, ['vote_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $where_clauses[] = "DATE(`$field`) $operator %s";
                } else {
                    $where_clauses[] = "`$field` $operator %s";
                }

                $where_params[] = $value;
            }
        }

        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        $group_by_sql = '';
        if (!empty($group_by)) {
            if (is_array($group_by)) {
                $sanitized_group_by = array_map(fn($field) => "`" . preg_replace('/[^\w_]/', '', $field) . "`", $group_by);
                $group_by_sql = 'GROUP BY ' . implode(', ', $sanitized_group_by);
            } else {
                $group_by_sql = 'GROUP BY `' . preg_replace('/[^\w_]/', '', $group_by) . '`';
            }
        }

        $having_clauses = [];
        $having_params  = [];

        foreach ($having as $key => $value) {
            $operator = '=';
            $field    = $key;

            if (preg_match('/^(SUM|AVG|COUNT|MIN|MAX)?\(?(\w+)\)?\s*(>=|<=|<>|!=|=|>|<)$/i', $key, $matches)) {
                $aggregate_func = strtoupper($matches[1]);
                $field          = $matches[2];
                $operator       = $matches[3];

                $field_expr = $aggregate_func ? "$aggregate_func(`$field`)" : "`$field`";

                if (is_array($value) && isset($value['value'], $value['operator'])) {
                    $operator = strtoupper($value['operator']);
                    $value    = $value['value'];
                }

                $having_clauses[] = "$field_expr $operator %s";
                $having_params[]  = $value;
            }
        }

        $having_sql = '';
        if (!empty($having_clauses)) {
            $having_sql = 'HAVING ' . implode(' AND ', $having_clauses);
        }

        $order_by  = preg_replace('/[^\w_]/', '', $order_by);
        $order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

        $params_combined = array_merge($where_params, $having_params);

        $count_sql = "SELECT COUNT(*) FROM (
            SELECT 1 FROM {$this->table} $where_sql $group_by_sql $having_sql
        ) AS grouped_result";

        $count_sql_prepared = !empty($params_combined)
            ? $wpdb->prepare($count_sql, ...$params_combined)
            : $count_sql;

        $total = $wpdb->get_var($count_sql_prepared);

        $query_sql = "SELECT {$selected_fields} FROM {$this->table} $where_sql $group_by_sql $having_sql ORDER BY `$order_by` $order_dir LIMIT %d OFFSET %d";
        $query_params = array_merge($params_combined, [$per_page, $offset]);

        $rows = $wpdb->get_results($wpdb->prepare($query_sql, ...$query_params), ARRAY_A);

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
