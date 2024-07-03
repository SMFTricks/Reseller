<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

use ThemeCustoms\Config;

class Styles
{
	/**
	 * The settings that affect or add styles to the theme.
	 * Use the name of the setting for each element
	 */
	private array $settings = [
		'st_custom_width' => false,
	];

	/**
	 * The inline CSS output
	 */
	private string $css = '';

	/**
	 * Exclude these specific files from various functions
	 */
	private array $exclude = [
		'custom_edits',
	];

	/**
	 * Build the style settings array and the CSS
	 */
	public function buildCSS() : void
	{
		global $settings;

		// Other settings can hook into here as well.
		call_integration_hook('integrate_customtheme_style_settings', array(&$this->settings));

		// Fire up the function if the setting is set or enabled
		foreach ($this->settings as $style_setting => $style_function) {
			if (!empty($settings[$style_setting])) {
				if (is_array($style_function) && is_callable($style_function)) {
					$this->css .= call_user_func($style_function);
				} else {
					$this->css .= (empty($style_function) ? $this->$style_setting($settings[$style_setting]) : call_user_func($style_function));
				}
			}
		}

		// Add the CSS to the theme
		$this->printCSS();
	}

	/**
	 * Output the inline CSS to the theme
	 */
	public function printCSS() : void
	{
		addInlineCss($this->css);
	}

	/**
	 * It adjusts the forum width to match the setting
	 * Thanks to Sycho for the idea from his Forum Width Mod
	 * https://custom.simplemachines.org/index.php?mod=4223
	 * 
	 * @param string $setting The setting to use
	 * @return string The CSS output
	 */
	public function st_custom_width(string $setting) : string
	{
		// Adjust the max-width accorrdinly
		return '
			.content-wrapper, #top_section .inner_wrap, #wrapper, #header,
			footer .inner_wrap, #nav_wrapper, .main-wrapper
			{
				max-width: ' . $setting. ';
				width: ' . $setting. ';
			}
			@media screen and (max-width: 991px)
			{
				.content-wrapper, #top_section .inner_wrap, #wrapper, #header,
				footer .inner_wrap, #nav_wrapper, .main-wrapper
				{
					max-width: 95%;
					width: 100%;
				}
			}';
	}

	/**
	 * Style the SCEditor
	 * @param array $sce_options The current sceditor options
	 */
	public function sceditor(array &$sce_options) : void
	{
		global $settings, $context;

		// Load the index.css if included
		if (file_exists(Config::$current->dir . '/css/index.css')) {
			$sce_options['style'] = $sce_options['style'] . '" /><link rel="stylesheet" href="' . $settings['theme_url'] . '/css/index.css';
		}

		// Sort the styles by 'order_pos'
		uasort($context['css_customfiles'], function ($a, $b) {
			if (!isset($a['order_pos']) || !isset($b['order_pos'])) {
				return 0;
			}
			return $a['order_pos'] <=> $b['order_pos'];
		});

		$style = '';
		foreach ($context['css_customfiles'] as $file => $options) {
			if ($file === 0 || in_array($file, $this->exclude))
				continue;

			// Add the file to the list
			$style .= '" /><link rel="stylesheet" href="' . $settings['theme_url'] . '/css/custom/' . $file . '.css';
		}

		// Add the style to the options
		$sce_options['style'] = $sce_options['style'] . $style;
	}
}