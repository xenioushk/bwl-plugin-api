<?php

namespace Xenioushk\BwlPluginApi\Api\PluginUpdate;

/**
 * Class for registering the Plugin Auto Updater API.
 *
 * @package BwlPluginApi
 * @version 1.0.0
 * @author: Mahbub Alam Khan
 */
class WpAutoUpdater
{

	/**
	 * Current version.
	 *
	 * @var string
	 */
	public $current_version;

	/**
	 * Update path.
	 *
	 * @var string
	 */
	public $update_path;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Constructor.
	 *
	 * @param string $current_version current version.
	 * @param string $update_path update path.
	 * @param string $plugin_slug plugin slug.
	 */
	public function __construct($current_version, $update_path, $plugin_slug)
	{
		// Set the class public variables
		$this->current_version = $current_version;
		$this->update_path     = $update_path;
		$this->plugin_slug     = $plugin_slug;

		list($t1, $t2) = explode('/', $plugin_slug);
		$this->slug    = str_replace('.php', '', $t2);

		// define the alternative API for updating checking
		add_filter('pre_set_site_transient_update_plugins', [&$this, 'check_update']);

		// Define the alternative response for information checking
		add_filter('plugins_api', [&$this, 'check_info'], 10, 3);
	}

	/**
	 * Check for updates.
	 *
	 * @param object $transient transient.
	 *
	 * @return object
	 */
	public function check_update($transient)
	{

		if (empty($transient->checked)) {
			return $transient;
		}

		// Get the remote version
		$remote_version = $this->getRemote_version();

		// If a newer version is available, add the update
		if (version_compare($this->current_version, $remote_version, '<')) {
			$obj                                       = new \stdClass();
			$obj->slug                                 = $this->slug;
			$obj->new_version                          = $remote_version;
			$obj->url                                  = $this->update_path;
			$obj->package                              = $this->update_path;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}

	/**
	 * Check for information.
	 *
	 * @param object $false false.
	 * @param string $action action.
	 * @param object $arg arg.
	 *
	 * @return object
	 */
	public function check_info($false, $action, $arg)
	{
		if (is_object($arg) && property_exists($arg, 'slug')) {
			if ($arg->slug === $this->slug) {
				$information = $this->getRemote_information();
				return $information;
			}
		}
		return $false;
	}

	/**
	 * Get remote version.
	 *
	 * @return string
	 */
	public function getRemote_version()
	{ // phpcs:ignore
		$request = \wp_remote_post($this->update_path, ['body' => ['action' => 'version']]);
		if (! is_wp_error($request) || \wp_remote_retrieve_response_code($request) === 200) {
			return $request['body'];
		}
		return false;
	}

	/**
	 * Get remote information.
	 *
	 * @return mixed
	 */
	public function getRemote_information()
	{ // phpcs:ignore
		$request = \wp_remote_post($this->update_path, ['body' => ['action' => 'info']]);
		if (! \is_wp_error($request) || \wp_remote_retrieve_response_code($request) === 200) {
			return unserialize($request['body']);
		}
		return false;
	}

	/**
	 * Get remote license.
	 *
	 * @return mixed
	 */
	public function getRemote_license()
	{ // phpcs:ignore
		$request = wp_remote_post($this->update_path, ['body' => ['action' => 'license']]);
		if (! \is_wp_error($request) || \wp_remote_retrieve_response_code($request) === 200) {
			return $request['body'];
		}
		return false;
	}
}
