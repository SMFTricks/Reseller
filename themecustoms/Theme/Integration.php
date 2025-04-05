<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Theme;

use ThemeCustoms\Config;
use ThemeCustoms\Theme\Load;

class Integration
{
	/**
	 * The theme main file
	 */
	protected object $theme;

	/**
	 * The theme config file
	 */
	protected object $config;

	public function __construct()
	{
		// Load Theme Strings
		loadLanguage('ThemeCustoms/main');

		// Autoloader
		spl_autoload_register(__CLASS__ . '::autoload');

		// Configuration
		$this->config = new Config;

		// Configialize Theme
		$this->theme = new Load;
	}

	/**
	 * Initiallize the custom theme configuration
	 */
	public function initialize() : void
	{
		// Theme Details
		$this->config->details();

		// Theme Configuration
		$this->config->configuration();

		// Theme Settings
		$this->config->settings();

		// SMF 3.0 Styles Compat
		$this->config->forwardCompat();

		// Main hooks
		$this->loadHooks();

		// Custom Hooks
		$this->config->loadHooks();

		// Load Theme
		$this->theme->theme();
	}

	/**
	 * Autoloader using SMF function, with theme_dir
	 * @param string $class The fully-qualified class name.
	 */
	protected function autoload(string $class) : void
	{
		global $settings;

		$classMap = array(
			'ThemeCustoms\\' => 'themecustoms/',
		);
		call_integration_hook('integrate_customtheme_autoload', array(&$classMap));
	
		foreach ($classMap as $prefix => $dirName)
		{
			// does the class use the namespace prefix?
			$len = strlen($prefix);
			if (strncmp($prefix, $class, $len) !== 0)
				continue;
	
			$relativeClass = substr($class, $len);
			$fileName = $dirName . strtr($relativeClass, '\\', '/') . '.php';
	
			// if the file exists, require it
			if (file_exists($fileName = $settings['theme_dir']. '/' . $fileName))
			{
				require_once $fileName;
				return;
			}
		}
	}

	/**
	 * Load the main hooks
	 */
	private function loadHooks() : void
	{
		$hooks = [
			'buffer' => 'buffer',
			'credits' => 'credits',
			'current_action' => 'strip_menu',
			'menu_buttons' => 'main_menu',
			'theme_context' => 'htmlAttributes',
		];
		// General purpose hooks
		foreach ($hooks as $point => $callable) {
			add_integration_function('integrate_' . $point, __CLASS__ . '::' . $callable, false, $this->config->dir . '/themecustoms/Theme/Integration.php', true);
		}

		// Other Hooks
		add_integration_function('integrate_display_buttons', 'ThemeCustoms\Integrations\Buttons::display', false, $this->config->dir . '/themecustoms/Integrations/Buttons.php', true);
		add_integration_function('integrate_prepare_display_context', 'ThemeCustoms\Integrations\Buttons::quick', false, $this->config->dir . '/themecustoms/Integrations/Buttons.php', true);
		add_integration_function('integrate_messageindex_buttons', 'ThemeCustoms\Integrations\Buttons::message', false, $this->config->dir . '/themecustoms/Integrations/Buttons.php', true);
		add_integration_function('integrate_recent_buttons', 'ThemeCustoms\Integrations\Buttons::unread', false, $this->config->dir . '/themecustoms/Integrations/Buttons.php', true);
		add_integration_function('integrate_memberlist_buttons', 'ThemeCustoms\Integrations\Buttons::memberlist', false, $this->config->dir . '/themecustoms/Integrations/Buttons.php', true);
		add_integration_function('integrate_mark_read_button', 'ThemeCustoms\Integrations\InfoCenter::init', false, $this->config->dir . '/themecustoms/Integrations/InfoCenter.php', true);
		add_integration_function('integrate_modification_types', 'ThemeCustoms\Integrations\Packages::types', false, $this->config->dir . '/themecustoms/Integrations/Packages.php', true);
		add_integration_function('integrate_packages_sort_id', 'ThemeCustoms\Integrations\Packages::sort', false, $this->config->dir . '/themecustoms/Integrations/Packages.php', true);
	}

	/**
	 * Add or change menu buttons
	 * @param array $buttons The menu buttons
	 */
	public function main_menu(array &$buttons) : void
	{
		global $txt, $scripturl, $settings, $context;

		// Add the theme settings to the admin button
		$current_theme = [
			'title' => $txt['current_theme'],
			'href' => $scripturl . '?action=admin;area=theme;sa=list;th=' . $this->config->id,
			'show' => allowedTo('admin_forum'),
		];
		$buttons['admin']['sub_buttons'] = array_merge([$current_theme], $buttons['admin']['sub_buttons']);

		// Add the community button if it's enabled.
		$temp_buttons = [];
		foreach ($buttons as $k => $v)
		{
			$temp_buttons[$k] = $v;
			if ($k == 'home')
			{
				$temp_buttons['community'] = [
					'title' => $txt['st_community'],
					'href' => $scripturl . (!empty($settings['st_community_forum']) ? '?action=forum' : ''),
					'icon' => 'members',
					'show' => !empty($settings['st_enable_community']),
				];
			}
		}
		$buttons = $temp_buttons;
	}

	/**
	 * Hook our menu icons setting for enabling/disabling.
	 * Will also remove buttons using the provided setting.
	 * This includes some additional checks for portal mods.
	 * @param string $current_action The current action
	 */
	public function strip_menu(string &$current_action) : void
	{
		global $context, $settings, $txt;

		// Check for Ultimate Menu doing witchcraft?
		if (!empty($current_action) && $current_action == 'admin' && isset($_REQUEST['area']) && $_REQUEST['area'] == 'umen') {
			return;
		}

		// Remove elements?
		$remove = !empty($settings['st_remove_items']) ? explode(',', $settings['st_remove_items']) : [];

		$current_menu = $context['menu_buttons'];
		foreach ($context['menu_buttons'] as $key => $button) {
			// Disable menu icons?
			$current_menu[$key]['icon'] = (isset($settings['st_disable_menu_icons']) && !empty($settings['st_disable_menu_icons']) ? '' : '<span class="main_icons ' . $key . '"></span>');

			// Remove the element if it's in the setting.
			// Community shouldn't be removed
			if (in_array($key, $remove) && $key !== 'community') {
				unset($current_menu[$key]);
			}
		}
		$context['menu_buttons'] = $current_menu;

		// Community button
		$this->community($current_action);
	}

	/**
	 * Group menu items inside a 'Community' button.
	 * Excludes the desired items from it.
	 * @param string $current_action The current action
	 */
	private function community(&$current_action) : void
	{
		global $context, $settings;

		// Is community enabled?
		if (empty($settings['st_enable_community'])) {
			return;
		}

		// Alright, put into community those that are not outside?
		$temp_menu = [];
		foreach ($context['menu_buttons'] as $action => $button) {
			// Don't check for home, community or excluded items
			if ($action == 'home' || $action == 'community' || (!empty($settings['st_not_community']) && in_array($action, explode(',', $settings['st_not_community'])))) {
				$temp_menu[$action] = $button;
			}
			// Add the buttons?
			elseif (!in_array($action, explode(',', $settings['st_not_community']))) {
				$temp_menu['community']['sub_buttons'][$action] = $button;
			}
		}
		$context['menu_buttons'] = $temp_menu;

		// Update the active button
		if (isset($context['menu_buttons']['community']['sub_buttons'][$current_action]) && array_key_exists($current_action, $context['menu_buttons']['community']['sub_buttons'])) {
			$context['menu_buttons']['community']['active_button'] = true;
		}
	}

	/**
	 * Add a little surprise to the credits page
	 */
	public function credits() : void
	{
		global $context;

		// Theme copyright
		$copyright = true;

		// Lelelelele?
		$context['copyrights']['mods'][] = $this->theme->unspeakable($copyright, true);
	}

	/**
	 * Do some black magic with the buffer hook
	 * @param string $buffer The current content
	 * @return string The changed content
	 */
	public function buffer(string $buffer) : string
	{
		// Do unspeakable things to the footer
		$this->theme->unspeakable($buffer);

		// Return the buffer
		return $buffer;
	}

	/**
	 * Add the global html attributes
	 */
	public function htmlAttributes() : void
	{
		global $settings, $context;

		// HTML Attributes
		$settings['themecustoms_html_attributes'] = (!empty($settings['themecustoms_html']['attributes']) && is_array($settings['themecustoms_html']['attributes']) ? ' ' . implode(' ', $settings['themecustoms_html']['attributes']) : '');

		// Disable the info center?
		if (isset($settings['st_disable_info_center']) && !empty($settings['st_disable_info_center']) && !empty($context['info_center'])) {
			unset($context['info_center']);
		}
	}
}