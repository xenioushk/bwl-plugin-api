## Database Query API

1. Use the class.

```php
use Xenioushk\BwlPluginApi\Api\Database\QueryManagerApi;
```

2. Initalize the class.

```php
$query_manager_api = new QueryManagerApi(<DB_TABLE_NAME>);
```

3. Insert Data.

```php
$data = [
  'name'=> "mahbub",
  'age' => 39
];

$result = $query_manager_api->insert($data);
```

@Return: Newly created row id.

4. Read Data. (Multiple With Complex Condition)

```php
/**
 * Example usage: Simple
 */

$filters = [
  'name' => 'Test',
  'id' => 8,
  'age >=' => 10,
  'age <=' => 15,
];

$data = $crud->get_items(1, 10, $filters, 'age', 'ASC');

/**
 * Example usage: Advanced
 */

$filters = [
  'name' => 'Test',
  'age' => ['value' => 20, 'operator' => '>='],
];

$data = $crud->get_items(1, 5, $filters, 'created_at', 'DESC');

/**
* More example.
*/

// Default is equal.
$filters = [
  'postid'    => $post_id,
  'post_type' => $post_type,
];

// You can use  Greater than, less than
$filters['vote_date >='] = [ 'value' => $pvm_filter_start_date, 'operator' => '>=' ];
$filters['vote_date <='] = [ 'value' => $pvm_filter_end_date, 'operator' => '<=' ];

// Default ordering
$order_by  = 'id';
$order_dir = 'DESC';

$args = [
  'page'            => 1, // for pagination support
  'per_page'        => 5, // no of item per page.
  'filters'         => $filters,
  'selected_fields' => "", // default: *. Example: 'id,name,email,age'
  'order_by'        => $order_by, // Or, any of your table column
  'order_dir'       => $order_dir, // ASC/DESC
];

$results = $crud->get_items( $args );
```

## More arguments example

```php
// ✅ Example Usage with MIN & MAX
$args = [
    'selected_fields' => 'category, SUM(points) as total_points, MAX(points) as max_score, MIN(points) as min_score',
    'group_by' => ['category'],
    'having' => [
        'SUM(points) >'  => ['value' => 100, 'operator' => '>'],
        'MAX(points) >=' => ['value' => 50, 'operator' => '>='],
        'MIN(points) <=' => ['value' => 5, 'operator' => '<='],
    ],
    'order_by' => 'total_points',
    'order_dir' => 'DESC',
    'page' => 1,
    'per_page' => 20,
];
```

```php
✅ Example Usage with SUM and AVG
$args = [
    'selected_fields' => 'category, SUM(points) as total_points, AVG(points) as avg_points',
    'group_by' => ['category'],
    'having' => [
        'SUM(points) >' => ['value' => 100, 'operator' => '>'],
        'AVG(points) >=' => ['value' => 20, 'operator' => '>='],
    ],
    'order_by' => 'total_points',
    'order_dir' => 'DESC',
    'page' => 1,
    'per_page' => 10,
];
```

@Return: Multiple Rows data.

5. Read Data. (Single With ID)

```php
$args = [
  'selected_fields' => "", // default: *. Example: 'id,name,email,age'
  'key'=> 'age', // default is id
  'id' => 39
];

$result = $query_manager_api->get_item($args);
```

This create a query like the following example

```sql
SELECT * FROM <YOUR_DB_TABLE> WHERE age=39 LIMIT 1;
```

@Return: Single Row data.

6. Update Data.

```php
$data = [
  'name'=> "mahbub",
  'age' => 39
];
$id = 5;
$result = $query_manager_api->update($id,$data);
```

This Query update the table data where id is equal to 5

@Return: 1 Success.
@Return: 0 No changes.

7. Delete Data.

```php
$id = 10;
$result = $query_manager_api->delete($id);
```

@Return: 1 Success.
@Return: 0 Already deleted.

## Changelog

👉 **1.0.9 (24.05.25)**

- 🔥 Updated: Database Query API to support SUM,HAVING, MAX, MIN, COUNT.

👉 **1.0.8 (23.05.25)**

- 🚀 Added: Database Query API.

👉 **1.0.7 (08.05.25)**

- 🚀 Added: Custom post type taxonomy callback.

👉 **1.0.6 (01.05.25)**

- 🔥 Updated: Plugin cpt publicly_queryable parameter.

👉 **1.0.5 (06.04.25)**

- ✅ Fixed: Filters API bug.
- ❌ Removed: CMB API.

👉 **1.0.4 (22.03.25)**

- Upgraded Plugin Pages API. 🙌
- Added View API. 🎉

👉 **1.0.3 (22.03.25)**

- Upgraded Actions and Filters API. 🙌

👉 **1.0.2 (17.03.25)**

- Fixed Custom post type API bug. 🐞

👉 **1.0.1 (16.03.25)**

- Fixed Dashboard widget API bug. 🐞

👉 **1.0.0 (15.03.25)**

- Initial release. 🚀
- Updated namespaces of the classes. 🙌
