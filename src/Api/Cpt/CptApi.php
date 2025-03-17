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

			$custom_labels = $tax['labels'] ?? [];

			$labels = [
				'singular_name'              => $custom_labels['singular_name'] ?? $tax['tax_title'],
				'all_items'                  => $custom_labels['all_items'] ?? "All {$tax['tax_title']}",
				'edit_item'                  => $custom_labels['edit_item'] ?? "Edit {$tax['tax_title']}",
				'view_item'                  => $custom_labels['view_item'] ?? "View {$tax['tax_title']}",
				'update_item'                => $custom_labels['update_item'] ?? "Update {$tax['tax_title']}",
				'add_new_item'               => $custom_labels['add_new_item'] ?? "Add New {$tax['tax_title']}",
				'new_item_name'              => $custom_labels['new_item_name'] ?? "New Title",
				'search_items'               => $custom_labels['search_items'] ?? "Search {$tax['tax_title']}",
				'popular_items'              => $custom_labels['popular_items'] ?? "Popular {$tax['tax_title']}",
				'separate_items_with_commas' => $custom_labels['separate_items_with_commas'] ?? "Separate with comma",
				'choose_from_most_used'      => $custom_labels['choose_from_most_used'] ?? "Choose from most used {$tax['tax_title']}",
				'not_found'                  => $custom_labels['not_found'] ??  "Nothing found",
			];


			$this->tax_settings[] = [

				$tax['tax_slug'],
				[$cpt_info['post_type']],
				[
					'label'             => $tax['tax_title'] ?? 'Taxonomy Title',
					'hierarchical'      => $tax['hierarchical'] ?? true,
					'query_var'         => $tax['query_var'] ?? true,
					'show_in_rest'      => $tax['show_in_rest'] ?? true,
					'public'            => $tax['public'] ?? true,
					'rewrite'           => [
						'slug' => $tax['custom_tax_slug'] ?? $cpt_info['post_type'] . '-' . $tax['tax_slug'],
					],
					'show_admin_column' => true,
					'labels'            => $labels,
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

			$custom_labels = $cpt['labels'] ?? [];

			$labels = [
				'name'               =>  $custom_labels['name'] ?? "All {$cpt['menu_name']}",
				'singular_name'      => $cpt['singular_name'] ?? $cpt['menu_name'],
				'add_new'            =>  $custom_labels['add_new'] ?? "Add New {$cpt['singular_name']}",
				'add_new_item'       => $custom_labels['add_new_item'] ?? "Add New {$cpt['singular_name']}",
				'edit_item'       => $custom_labels['edit_item'] ?? "Edit {$cpt['singular_name']}",
				'new_item'       => $custom_labels['new_item'] ?? "New {$cpt['singular_name']}",
				'all_items'       => $custom_labels['all_items'] ?? "All {$cpt['singular_name']} Items",
				'view_item'       => $custom_labels['view_item'] ?? "View {$cpt['menu_name']} Items",
				'search_items'       => $custom_labels['search_items'] ?? "Search {$cpt['menu_name']} Items",
				'not_found'       => $custom_labels['not_found'] ?? "No item found",
				'not_found_in_trash'       => $custom_labels['not_found_in_trash'] ?? "No item found in trash",
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