<?php

namespace Xenioushk\BwlPluginApi\Api\OptionsPanel;

/**
 *  Class for registering the Options panel API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class OptionsPanelApi
{

	/**
	 * Options panel settings.
	 *
	 * @var array $options
	 */
	public $options = [];

	/**
	 * Register options panel.
	 */
	public function register()
	{
		if (! empty($this->options)) {
			add_action('admin_init', [$this, 'add_options_components']);
		}
	}

	/**
	 * Add options.
	 *
	 * @param array $options Options settings.
	 *
	 * @return $this
	 */
	public function add_options(array $options)
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * Add options components.
	 */
	public function add_options_components()
	{

		foreach ($this->options as $optionGroup => $optionData) {

			$optionName = $optionData['option_name']; // The key we use to retrive data. eg.g get_option('option_name');
			$page       = $optionData['page']; // slug of the view page. e.g. page=bptm-template-settings

			register_setting($optionGroup, $optionName);

			if (! empty($optionData['option_section'])) {

				foreach ($optionData['option_section'] as $section) {

					$sectionTag    = $section['section_tag'];
					$sectionTitle  = $section['title'];
					$sectionCb     = $section['cb'];
					$sectionFields = $section['fields']; // multi-dimensional array.

					// Register settings section.
					add_settings_section(
						$sectionTag,
						$sectionTitle,
						$sectionCb,
						$page
					);

					if (! empty($sectionFields)) {
						foreach ($sectionFields as $fieldTag => $field) {
							// Register Fields.
							add_settings_field(
								$fieldTag,
								$field['title'],
								$field['cb'],
								$page,
								$sectionTag
							);
						}
					}
				}
			}
		}
	}
}
