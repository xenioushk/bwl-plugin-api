<?php

namespace Xenioushk\BwlPluginApi\Api\Cmb;

/**
 * Class for registering the CMB Api.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class CmbApi extends CmbFieldsApi
{

	/**
	 * Cmb fields array.
	 *
	 * @var array
	 */
	public $cmb = [];

	/**
	 * Cmb Post Type string.
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Register cmb meta boxes and save actions.
	 */
	public function register()
	{
		if (! empty($this->cmb)) {

			$this->register_cmb_assets();

			foreach ($this->cmb as $cmb_field) {

				add_action('add_meta_boxes', function () use ($cmb_field) {

					add_meta_box(
						$cmb_field['meta_box_id'],
						$cmb_field['meta_box_heading'],
						function ($post) use ($cmb_field) {
							$this->show_meta_box($post, $cmb_field);
						},
						$cmb_field['post_type'],
						$cmb_field['context'],
						$cmb_field['priority']
					);
				});

				add_action("save_post_{$this->post_type}", [$this, 'save_meta_fields_data']);
			}
		}
	}

	/**
	 * Register cmb assets
	 */
	public function register_cmb_assets()
	{

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_style('bptm-cmb-colorpicker-style', BWL_PETITIONS_PLUGIN_LIBS_DIR . 'cmb/styles/colorpicker.css', [], BWL_PETITIONS_VERSION, 'all');
		wp_enqueue_style('bwl-cmb-admin-style', BWL_PETITIONS_PLUGIN_LIBS_DIR . 'cmb/styles/bwl_cmb.css', [], BWL_PETITIONS_VERSION, 'all');
		wp_enqueue_script('bptm-cmb-color-picker-script', BWL_PETITIONS_PLUGIN_LIBS_DIR . 'cmb/scripts/colorpicker.js', ['jquery'], BWL_PETITIONS_VERSION, []);
		wp_enqueue_script('bwl-cmb-admin-main', BWL_PETITIONS_PLUGIN_LIBS_DIR . 'cmb/scripts/bwl_cmb.js', ['jquery', 'jquery-ui-core', 'jquery-ui-sortable'], BWL_PETITIONS_VERSION, []);
	}
	/**
	 * Set CMB post type.
	 *
	 * @param string $post_type cmb post type.
	 * @return $this
	 */
	public function set_post_type($post_type = '')
	{
		if (empty($post_type)) {
			return '';
		}
		$this->post_type = $post_type;
		return $this;
	}

	/**
	 * Add cmb.
	 *
	 * @param array $cmb notices array.
	 *
	 * @return $this
	 */
	public function add_cmb(array $cmb)
	{
		$this->cmb = $cmb;
		return $this;
	}

	/**
	 * Save cmb fields data.
	 *
	 * @param int $post_id post id.
	 */
	public function save_meta_fields_data($post_id)
	{

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

			return $post_id;
		} else {

			$fields = isset($this->cmb[0]['fields']) && ! empty($this->cmb[0]['fields']) ? $this->cmb[0]['fields'] : [];

			if (empty($fields)) {
				return;
			}

			$excluded_fields = [];

			foreach ($fields as $field) {

				if (isset($_POST[$field['name']])) { // phpcs:ignore

					// repeatable field is special and it stored array data.
					if ($field['type'] === 'repeatable') {
						$excluded_fields[] = $field['name'];
					} else {
						$meta_key   = $field['name'];
						$meta_value = wp_kses_post($_POST[$field['name']]); // phpcs:ignore
						$this->update_meta_fields($post_id, $meta_key, $meta_value);
					}
				}
			}

			// Repeatable Fields Data Saving In Here.

			if (! empty($excluded_fields)) {

				foreach ($excluded_fields as $excluded_field) {
					$meta_key   = $excluded_field;
					$meta_value = $_POST[$excluded_field]; // phpcs:ignore

					if (is_array($meta_value)) {
						foreach ($meta_value as &$value) {
							$value = wp_kses_post($value); // Sanitize each element of the array
						}
					} else {
						$meta_value = wp_kses_post($meta_value); // Sanitize the value if it's not an array
					}

					$this->update_meta_fields($post_id, $meta_key, $meta_value);
				}
			}
		}
	}

	/**
	 * Update meta fields data.
	 *
	 * @param int          $post_id post id.
	 * @param string       $meta_key meta key.
	 * @param string|array $meta_value meta value.
	 */
	private function update_meta_fields($post_id, $meta_key, $meta_value)
	{
		update_post_meta($post_id, $meta_key, $meta_value);
	}
}
