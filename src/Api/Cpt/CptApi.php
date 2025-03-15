<?php

namespace Xenioushk\BwlPluginApi\Api\Cpt;

/**
 * Class for custom post type API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class CptApi
{

	/**
	 * Custom post type settings.
	 *
	 * @var array
	 */
	public $cpt_settings = [];

	/**
	 * Taxonomy settings.
	 *
	 * @var array
	 */
	public $tax_settings = [];

	/**
	 * Register custom post type.
	 */
	public function register()
	{
		if (! empty($this->cpt_settings)) {
			$this->add_custom_cpt_api();
		}
	}

	/**
	 * Add custom post type.
	 *
	 * @param array $cpt_settings Custom post type settings.
	 *
	 * @return $this
	 */
	public function add_cpt(array $cpt_settings)
	{
		$this->cpt_settings = $cpt_settings;
		return $this;
	}

	/**
	 * Add taxonomy with custom post type.
	 *
	 * @param array $tax_settings Taxonomy settings.
	 *
	 * @return $this
	 */
	public function with_taxonomy(array $tax_settings = [])
	{

		if (empty($this->cpt_settings) || empty($tax_settings)) {
			return $this;
		}

		/*
		* Get the parent post type.
		*/

		$cpt_info = $this->cpt_settings[0];

		foreach ($tax_settings as $tax) {

			$this->tax_settings[] = [

				$tax['tax_slug'],
				[$cpt_info['post_type']],
				[
					'label'             => $tax['tax_title'] ?? esc_html__('Taxonomy Title', 'bwl_ptmn'),
					'hierarchical'      => $tax['hierarchical'] ?? true,
					'query_var'         => true,
					'public'            => true,
					'rewrite'           => [
						'slug' => $tax['custom_tax_slug'] ?? $cpt_info['post_type'] . '-' . $tax['tax_slug'],
					],
					'show_admin_column' => true,
					'show_in_rest'      => $tax['show_in_rest'] ?? true,
					'labels'            => [
						'singular_name'              => $tax['tax_title'],
						'all_items'                  => esc_html__('All', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'edit_item'                  => esc_html__('Edit', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'view_item'                  => esc_html__('View', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'update_item'                => esc_html__('Update', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'add_new_item'               => esc_html__('Add New', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'new_item_name'              => esc_html__('New Title', 'bwl_ptmn'),
						'search_items'               => esc_html__('Search', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'popular_items'              => esc_html__('Popular', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'separate_items_with_commas' => esc_html__('Separate with comma', 'bwl_ptmn'),
						'choose_from_most_used'      => esc_html__('Choose from most used', 'bwl_ptmn') . ' ' . $tax['tax_title'],
						'not_found'                  => esc_html__('Nothing found', 'bwl_ptmn'),
					],
				],
			];
		}

		return $this;
	}

	/**
	 * Register custom post type.
	 */
	public function add_custom_cpt_api()
	{

		foreach ($this->cpt_settings as $cpt) {

			$labels = [
				'name'               => esc_html__('All', 'bwl_ptmn') . ' ' . $cpt['menu_name'],
				'singular_name'      => $cpt['singular_name'] ?? $cpt['menu_name'],
				'add_new'            => esc_html__('Add New', 'bwl_ptmn') . ' ' . $cpt['singular_name'],
				'add_new_item'       => esc_html__('Add New', 'bwl_ptmn'),
				'edit_item'          => esc_html__('Edit', 'bwl_ptmn') . ' ' . $cpt['singular_name'],
				'new_item'           => esc_html__('New', 'bwl_ptmn') . ' ' . $cpt['singular_name'],
				'all_items'          => esc_html__('All', 'bwl_ptmn') . ' ' . $cpt['singular_name'] . ' ' . esc_html__('Items', 'bwl_ptmn'),
				'view_item'          => esc_html__('View', 'bwl_ptmn') . ' ' . $cpt['menu_name'] . ' ' . esc_html__('Items', 'bwl_ptmn'),
				'search_items'       => esc_html__('Search', 'bwl_ptmn') . ' ' . $cpt['menu_name'] . ' ' . esc_html__('Items', 'bwl_ptmn'),
				'not_found'          => esc_html__('No item found', 'bwl_ptmn'),
				'not_found_in_trash' => esc_html__('No item found in trash', 'bwl_ptmn'),
				'parent_item_colon'  => '',
				'menu_name'          => $cpt['menu_name'],
			];

			$args = [
				'labels'             => $labels,
				'query_var'          => $cpt['query_var'] ?? $cpt['post_type'],
				'show_in_nav_menus'  => true,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => $cpt['show_in_rest'] ?? true,
				'rewrite'            => $cpt['rewrite'] ?? [
					'slug'       => $cpt['slug'] ?? $cpt['post_type'],
					'with_front' => false, // before it was true
				],
				'publicly_queryable' => true, // turn it to false, if you want to disable generate single page
				'capability_type'    => 'post',
				'has_archive'        => $cpt['has_archive'] ?? true,
				'hierarchical'       => $cpt['hierarchical'] ?? true,
				'show_in_admin_bar'  => true,
				'supports'           => $cpt['supports'] ?? ['title', 'editor', 'revisions', 'author', 'thumbnail'],
				'menu_icon'          => $cpt['menu_icon'] ?? 'dashicons-media-document',
			];

			// It's an additional part. It will not be applicable for any other projects.

			if (isset($cpt['additional_args']) && is_array($cpt['additional_args'])) {
				foreach ($cpt['additional_args'] as $key => $value) {
					$args[$key] = $value;
				}
			}

			register_post_type($cpt['post_type'], $args);
		}

		/*
		*  Register all the taxonomies.
		*/

		if (! empty($this->tax_settings)) {
			foreach ($this->tax_settings as $tax) {
				register_taxonomy($tax[0], $tax[1], $tax[2]);
			}
		}
	}
}
