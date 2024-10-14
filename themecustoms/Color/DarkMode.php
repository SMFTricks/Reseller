<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

use ThemeCustoms\Config;

class DarkMode
{
	/**
	 * @var int Order position for the dark mode file
	 */
	private int $order = 150;

	/**
	 * @var array Theme settings
	 */
	private array $settings = [];

	/**
	 * Initializes the theme dark mode related features
	 */
	public function __construct()
	{
		// Is dark mode enabled?
		if (empty(Config::$current->darkMode))
			return;

		// Initialize the dark mode
		$this->init();
	}

	/**
	 * Load the dark mode
	 */
	private function init() : void
	{
		global $context, $settings;

		// Set the mode selection
		$this->selection();

		// No need to load if the setting is disabled and default mode is light
		if (!empty($context['theme_can_change_mode']) || (!empty($settings['st_theme_mode_default']) && $settings['st_theme_mode_default'] !== 'light')) {
			// Load the css
			$this->css();

			// Load the javascript
			$this->js();

			// Style sceditor needs extra help
			add_integration_function('integrate_sceditor_options', __CLASS__ . '::sceditor', false, Config::$current->dir . '/themecustoms/Color/DarkMode.php', true);
		}

		// User options
		add_integration_function('integrate_theme_options', __CLASS__ . '::options', false, Config::$current->dir . '/themecustoms/Color/DarkMode.php', true);

		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != Config::$current->id)
			return;

		// Settings
		add_integration_function('integrate_customtheme_settings', __CLASS__ . '::settings', false, Config::$current->dir . '/themecustoms/Color/DarkMode.php', true);
	}

	/**
	 * Set the currently selected mode
	 */
	private function selection() : void
	{
		global $context, $settings, $smcFunc, $options;

		$context['theme_colormode'] = '';
		$context['theme_can_change_mode'] = !empty($settings['st_enable_mode_selection']) || allowedTo('admin_forum');
		$settings['theme_colormodes'] = ['light', 'dark', 'system'];

		// Overriding - for previews and that.
		if (!empty($_REQUEST['mode'])) {
			$_SESSION['theme_colormode'] = $_REQUEST['mode'];

			// If the user is logged, save this to their profile
			if ($context['user']['is_logged'] && in_array($_SESSION['theme_colormode'], $settings['theme_colormodes'])) {
				$smcFunc['db_insert'](
					'replace',
					'{db_prefix}themes',
					['id_theme' => 'int', 'id_member' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'],
					[Config::$current->id, $context['user']['id'], 'st_theme_mode', $_SESSION['theme_colormode']],
					['id_theme', 'id_member', 'variable'],
				);
			}
		}

		// User selection?
		if (!empty($context['theme_can_change_mode'])) {
			$context['theme_colormode'] = !empty($_SESSION['theme_colormode']) && in_array($_SESSION['theme_colormode'], $settings['theme_colormodes']) && $context['user']['is_guest'] ? $_SESSION['theme_colormode'] : (!empty($options['st_theme_mode']) && in_array($options['st_theme_mode'], $settings['theme_colormodes']) ? $options['st_theme_mode'] : '');
		}

		// If no color mode, set a default
		if (empty($context['theme_colormode']) || !in_array($context['theme_colormode'], $settings['theme_colormodes'])) {
			$context['theme_colormode'] = !empty($settings['st_theme_mode_default']) && in_array($settings['st_theme_mode_default'], $settings['theme_colormodes']) ? $settings['st_theme_mode_default'] : $settings['theme_colormodes'][0];
		}
	}

	/**
	 * Loads the dark CSS.
	 */
	private function css()
	{
		global $settings, $context;

		// Add the HTML data attribute for color mode
		$settings['themecustoms_html']['attributes'][] = 'data-mode="' . $context['theme_colormode'] . '"';

		// Load the dark CSS
		loadCSSFile('custom/dark.css', ['order_pos' => $this->order, 'attributes' => (isset($context['theme_colormode']) && $context['theme_colormode'] == 'system' ? ['media' => '(prefers-color-scheme: dark)'] : [])], 'smf_darkmode');
	}

	/**
	 * Loads the dark mode JS.
	 */
	private function js()
	{
		global $context;

		// Theme Mode
		addJavaScriptVar('smf_theme_colormode', $context['theme_colormode'], true);

		// Load the javascript file
		loadJavascriptFile('custom/dark.js', ['async' => false, 'defer' => true, 'minimize' => true,],'smf_darkmode');
	}

	/**
	 * The sceditor styling is extremely stupid, so you need to overengineer things to style it accordingly.
	 * @param array $sce_options The current sceditor options
	 */
	public function sceditor(array &$sce_options) : void
	{
		global $context, $settings;

		$sce_options['style'] = $sce_options['style'] . '"/><link rel="stylesheet" href="' . $settings['theme_url'] . '/css/custom/dark.css' . (isset($context['theme_colormode']) && $context['theme_colormode'] == 'system' ? '" media="(prefers-color-scheme: dark)' : '');

		// Add the data attribute
		addInlineJavaScript('
			$(document).ready(function() {
				$(\'.sceditor-container iframe\').each(function() {
					$(this).contents().find(\'html\').attr(\'data-mode\', "' . $context['theme_colormode'] . '");
				});
			});
		', true);
	}

	/**
	 * Adds the mode selection to the theme options
	 */
	public function options() : void
	{
		global $context, $txt, $settings;

		if (!empty($context['current_action']) && $context['current_action'] == 'admin' && isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != Config::$current->id)
			return;

		// Insert the theme options
		$context['theme_options'] = array_merge(
			[
				$txt['st_theme_mode'],
				[
					'id' => 'st_theme_mode',
					'label' => $txt['st_theme_mode_select'],
					'options' => [
						'light' => $txt['st_light_mode'],
						'dark' => $txt['st_dark_mode'],
						'system' => $txt['st_system_mode'],
					],
					'default' => $settings['st_theme_mode_default'] ?? 'light',
					'enabled' => $context['theme_can_change_mode'],
				],
			],
			$context['theme_options']
		);
	}

	/**
	 * Inserts the theme settings for the dark mode
	 * @param array $theme_settings The current theme settings
	 * @param array $settings_types The current types of settings
	 */
	public function settings(array &$theme_settings, array &$settings_types) : void
	{
		global $txt;

		// Add color setting type
		$settings_types[] = 'color';

		// Settings
		$this->settings = [
			[
				'section_title' => $txt['st_dark_mode'],
				'id' => 'st_theme_mode_default',
				'label' => $txt['st_theme_mode_default'],
				'description' => $txt['st_theme_mode_default_desc'],
				'options' => [
					'light' => $txt['st_light_mode'],
					'dark' => $txt['st_dark_mode'],
					'system' => $txt['st_system_mode'],
				],
				'type' => 'list',
				'theme_type' => 'color',
			],
			[
				'id' => 'st_enable_mode_selection',
				'label' => $txt['st_enable_mode_selection'],
				'type' => 'checkbox',
				'theme_type' => 'color',
			]
		];

		// Add them to the settings
		$theme_settings = array_merge($this->settings, $theme_settings);
	}
}