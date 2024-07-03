<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

use ThemeCustoms\Config;

class Changer
{
	/**
	 * @var array Theme settings
	 */
	private array $settings = [];

	/**
	 * @var array The color changer options
	 */
	private $_color_changes;

	/**
	 * @var array The color palettes
	 */
	private $_color_palettes = [];

	/**
	 * @var array Root selectors
	 * The exptected dark mode is added by default
	 */
	private array $root = [
		'[data-mode="dark"]',
		'[data-mode="system"]',
	];

	/**
	 * Initializes the theme color changer related features
	 */
	public function __construct()
	{
		// Are there any palettes or color changes?
		if (empty(Config::$current->colorChanges) || Config::$current->variants !== [])
			return;

		// Initialize the color changer
		$this->init();
	}

	/**
	 * Load the color changer
	 */
	private function init() : void
	{
		global $context, $settings;

		// Load the javascript
		$this->js();

		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != Config::$current->id)
			return;

		add_integration_function('integrate_customtheme_settings', __CLASS__ . '::settings', false, Config::$current->dir . '/themecustoms/Color/Changer.php', true);
	}

	/**
	 * Changer::setChanges()
	 * 
	 * Sets the color changes
	 * 
	 * @return void
	 */
	private function setChanges()
	{
		global $settings;

		// Hook the color changes
		call_integration_hook('integrate_customtheme_color_changes', [&$this->_color_changes, &$this->_color_palettes, &$this->_root_selectors]);

		$settings['color_changes'] = $this->_color_changes;
	}

	/**
	 * Changer::colorChanges()
	 * 
	 * Adds the color changes array to the theme
	 * 
	 * @return void
	 */
	public function colorChanges()
	{
		global $settings;

		// Add the color changes... Again?
		$settings['color_changes'] = $this->_color_changes;

		// Color palettes
		if (!empty($this->_color_palettes))
			$settings['color_palettes'] = $this->_color_palettes;
		
		// Add additional root selectors for higher specificity
		if (!empty($this->_root_selectors))
			$settings['color_changes_root'] = $this->_root_selectors;
	}

	/**
	 * Adds the color changer settings to the theme
	 * @param array $theme_settings The current theme settings
	 * @param array $settings_types The current types of settings
	 */
	public function settings(array &$theme_settings, array &$settings_types) : void
	{
		global $context, $txt, $settings;
		
		// Load the Color Changer language file
		loadLanguage('ColorChanger');

		// Load the color changer js... Sometimes is not loaded
		loadJavaScriptFile('ColorChanger.js', ['defer' => true, 'default_theme' => true, 'minimize' => true,], 'smf_color_changer');

		// Add color setting type
		$settings_types[] = 'color';

		// Settings
		$this->settings = [
			// Admin only?
			$context['theme_settings'][] = [
				'section_title' => $txt['cc_color_changer'] . (!empty($this->_color_palettes['default']) ? ' <a onclick="return applyColorPalette(\'default\')" id="cc_reset_all">[' . $txt['cc_reset_all'] . ']</a>' : ''),
				'id' => 'cc_admin_only',
				'label' => $txt['cc_admin_only'],
				'description' => $txt['cc_admin_only_help'],
				'theme_type' => 'color',
			],
			// Remove Shadows
			$context['theme_settings'][] = [
				'id' => 'cc_remove_shadows',
				'label' => $txt['cc_remove_shadows'],
				'theme_type' => 'color',
			]
		];

		// Add them to the settings
		$theme_settings = array_merge($this->settings, $theme_settings);
	}

	/**
	 * Changer::control()
	 * 
	 * Set the description based on the palette
	 * 
	 * @param string The possible color id
	 * @return string The description
	 */
	private function control($color_id)
	{
		global $txt;

		// Check if the color is in the palette
		if (!isset($this->_color_palettes['default'][$color_id]))
			return;

		// Return the description
		return '
			<a onclick="$(\'#cc_' . $color_id . '\').attr(\'type\', \'text\').val(\'' . $this->_color_palettes['default'][$color_id] . '\')">
				' . $txt['cc_default_color'] . '
			</a>';
	}

	/**
	 * Loads the color changer JS.
	 */
	private function js() : void
	{
		global $txt;

		addJavaScriptVar('color_palettes', json_encode($this->_color_palettes));
		addJavaScriptVar('txt_cc_palettes', $txt['cc_palettes_title'], true);
	}
}