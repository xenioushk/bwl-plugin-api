<?php

namespace Xenioushk\BwlPluginApi\Api\Cmb;

/**
 * Class for registering the CMB Fields Api.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class CmbFieldsApi
{

	/**
	 * Cmb row start tag.
	 *
	 * @var string
	 */
	public $cmb_row_start = '<p class="bwl_cmb_row">';

	/**
	 * Cmb row end tag.
	 *
	 * @var string
	 */
	public $cmb_row_end = '</p>';

	/**
	 * Constructor method
	 */
	public function __construct() {}

	/**
	 * Sanitize Field Value.
	 *
	 * @param string $field_type field type.
	 * @param string $field_value field value.
	 */
	private function sanitize_field_value($field_type, $field_value)
	{
		switch ($field_type) {
			case 'number':
				return intval($field_value);
			case 'url':
				return esc_url($field_value);
			case 'email':
				return sanitize_email($field_value);
			default:
				return wp_kses_post($field_value);
		}
	}

	/**
	 * Get the  field label
	 *
	 * @param array $custom_field field data.
	 */
	private function get_field_label($custom_field = [])
	{

		$desc = (isset($custom_field['desc']) && ! empty($custom_field['desc'])) ?
			"<small class='small-text'>{$custom_field['desc']}</small>" : '';

		$output = "<label for='{$custom_field['id']}'>{$custom_field['title']}{$desc}</label>";

		return $output;
	}

	/**
	 * Get the text field
	 *
	 * @param array  $custom_field field data.
	 * @param string $field_value field value.
	 */
	private function get_text_field($custom_field = [], $field_value = '')
	{

		$field_type  = isset($custom_field['type']) ? $custom_field['type'] : 'text';
		$field_value = $this->sanitize_field_value($field_type, $field_value);

		$field = sprintf(
			"<input type='%s' id='%s' name='%s' class='%s' value='%s' placeholder='%s'>",
			esc_attr($field_type),
			esc_attr($custom_field['id']),
			esc_attr($custom_field['name']),
			esc_attr($custom_field['class']),
			esc_attr($field_value),
			esc_attr($custom_field['placeholder'])
		);

		$output = $this->cmb_row_start . $this->get_field_label($custom_field) . $field . $this->cmb_row_end;
		echo $output; // phpcs:ignore
	}

	/**
	 * Get the textarea field
	 *
	 * @param array  $custom_field field data.
	 * @param string $field_value field value.
	 */
	private function get_textarea_field($custom_field = [], $field_value = '')
	{

		$field = sprintf(
			"<textarea id='%s' name='%s' class='%s' placeholder='%s'>%s</textarea>",
			esc_attr($custom_field['id']),
			esc_attr($custom_field['name']),
			esc_attr($custom_field['class']),
			esc_attr($custom_field['placeholder']),
			esc_textarea($field_value)
		);

		$output = $this->cmb_row_start . $this->get_field_label($custom_field) . $field . $this->cmb_row_end;
		echo $output; // phpcs:ignore
	}

	/**
	 * Show meta box.
	 *
	 * @param object $post post object.
	 * @param array  $cmb_field cmb field array.
	 */
	public function show_meta_box($post, $cmb_field)
	{

		$bwl_cmb_custom_fields = $cmb_field;

		foreach ($bwl_cmb_custom_fields['fields'] as $custom_field) :

			$field_value = get_post_meta($post->ID, $custom_field['id'], true);

?>

			<?php
			if ($custom_field['type'] === 'text') {
				$this->get_text_field($custom_field, $field_value);
			} elseif ($custom_field['type'] === 'textarea') {

				$this->get_textarea_field($custom_field, $field_value);
			}
			?>


			<?php if ($custom_field['type'] == 'repeatable') : ?>
				<p class="bwl_cmb_row bwl_cmb_db">
					<label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['title']; ?></label>

					<?php if (isset($custom_field['desc']) && $custom_field['desc'] != '') : ?>
						<small class="small-text"><?php echo $custom_field['desc']; ?></small>
					<?php endif; ?>
				</p>

				<ul class="bwl_cmb_repeat_field_container">

					<?php

					$i = 0;

					if (! empty($field_value) && is_array($field_value)) {

						foreach ($field_value as $data) {
					?>

							<li class="bwl_cmb_repeat_row" data-row_count="<?php echo $i; ?>">
								<span class="label"><?php echo $custom_field['label_text']; ?></span>
								<input name="<?php echo $custom_field['name'] . '[' . $i . ']'; ?>" type="text"
									value="<?php if (! empty($data)) {
														echo $data;
													} ?>">
								<div class="clear"></div>
								<a class="delete_row"
									title="<?php esc_attr_e('Delete', 'bwl_ptmn'); ?>"><?php esc_attr_e('Delete', 'bwl_ptmn'); ?></a>
							</li>

					<?php

							++$i;
						}
					}

					?>
				</ul>

				<input id="add_new_row" type="button" class="button" value="<?php echo $custom_field['btn_text']; ?>"
					data-delete_text="<?php esc_attr_e('Delete', 'bwl_ptmn'); ?>"
					data-field_type="<?php echo $custom_field['field_type']; ?>" data-field_name="<?php echo $custom_field['name']; ?>"
					data-label_text="<?php echo $custom_field['label_text']; ?>">


			<?php endif; ?>


			<?php if ($custom_field['type'] == 'wpeditor') : ?>

				<p class="bwl_cmb_row">
					<label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['title']; ?></label>
					<?php echo wp_editor($field_value, $custom_field['id'], ['textarea_name' => $custom_field['id'], 'media_buttons' => true]); ?>
					<?php if (isset($custom_field['desc']) && $custom_field['desc'] != '') : ?>
						<small class="small-text"><?php echo $custom_field['desc']; ?></small>
					<?php endif; ?>
				</p>

			<?php endif; ?>

			<?php if ($custom_field['type'] == 'bgcolor') : ?>

				<p class="bwl_cmb_row">
					<label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['title']; ?></label>
					<input type="text" id="<?php echo $custom_field['id']; ?>" name="<?php echo $custom_field['name']; ?>"
						class="<?php echo $custom_field['class']; ?> bgcolor" value="<?php echo esc_attr($field_value); ?>"
						placeholder="<?php echo $custom_field['placeholder']; ?>" style="width: 100px;">

					<?php if (isset($custom_field['desc']) && $custom_field['desc'] != '') : ?>
						<small class="small-text"><?php echo $custom_field['desc']; ?></small>
					<?php endif; ?>
				</p>

			<?php endif; ?>



			<?php if ($custom_field['type'] == 'upload') : ?>

				<p class="bwl_cmb_row">
					<label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['title']; ?></label>

					<input id="<?php echo $custom_field['id']; ?>" class="img-path" type="text" style="direction:ltr; text-align:left"
						name="<?php echo $custom_field['name']; ?>" value="<?php if (! empty($field_value)) {
																																	echo $field_value;
																																} ?>" />
					<input id="upload_<?php echo $custom_field['id']; ?>_button" type="button" class="button"
						value="<?php esc_attr_e('Upload', 'bwl_ptmn'); ?>" data-bg_img_box_id='<?php echo $custom_field['id']; ?>' />
					<input data-parent_field="<?php echo $custom_field['id']; ?>" type="button" class="bwl_cmb_remove_file button"
						value="<?php esc_attr_e('Remove', 'bwl_ptmn'); ?>">
					<script type='text/javascript'>
						bwl_cmb_set_uploader('<?php echo $custom_field['id']; ?>');
					</script>

					<?php if (isset($custom_field['desc']) && $custom_field['desc'] != '') : ?>
						<small class="small-text"><?php echo $custom_field['desc']; ?></small>
					<?php endif; ?>
				</p>

			<?php endif; ?>

			<?php if ($custom_field['type'] == 'select') : ?>

				<?php

				$values = get_post_custom($post->ID);

				$selected = isset($values[$custom_field['name']]) ? esc_attr($values[$custom_field['name']][0]) : $custom_field['default_value'];

				?>

				<p class="bwl_cmb_row post-attributes-label-wrapper">
					<label for="<?php echo $custom_field['id']; ?>" class="post-attributes-label"><?php echo $custom_field['title']; ?>
					</label>
					<select name="<?php echo $custom_field['name']; ?>" id="<?php echo $custom_field['id']; ?>" class="widefat">

						<option value="" selected="selected">- Select -</option>

						<?php foreach ($custom_field['value'] as $key => $value) : ?>
							<option value="<?php echo $key; ?>" <?php selected($selected, $key); ?>><?php echo $value; ?></option>
						<?php endforeach; ?>

					</select>

					<?php if (isset($custom_field['desc']) && $custom_field['desc'] != '') { ?>
						<i><?php echo $custom_field['desc']; ?></i>
					<?php } ?>
				</p>

			<?php endif; ?>

			<?php if ($custom_field['type'] == 'checkbox') : ?>

				<p>
					<input type="checkbox" id="my_meta_box_check" name="my_meta_box_check" <?php checked($check, 'on'); ?>>
					<label for="my_meta_box_check">Do not check this</label>
				</p>

			<?php endif; ?>


			<?php if ($custom_field['type'] == 'petition_stats') : ?>

				<p>

					<label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['title']; ?> </label>


					<?php

					global $post;

					$bwl_petition_sign_lists = get_post_meta($post->ID, 'bwl_petition_sign_lists');  // Get voters'IPs for the current post

					?>

					<?php if (! empty($bwl_petition_sign_lists)) : ?>

				<ol>
					<?php foreach ($bwl_petition_sign_lists[0] as $sign_info) : ?>

						<li><?php echo $sign_info['user_name']; ?> <br /><?php echo ucfirst($sign_info['user_country']); ?>
							<br /><?php echo $sign_info['sign_date_time']; ?>
						</li>

					<?php endforeach; ?>
				</ol>

			<?php else : ?>
				<p>No <?php echo $custom_field['title']; ?> Found!</p>
			<?php endif; ?>
			</p>

		<?php endif; ?>

<?php

		endforeach;
	}
}
