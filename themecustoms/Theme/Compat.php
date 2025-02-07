<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Theme;

use ThemeCustoms\Config;

class Compat
{
	/**
	 * @var array List of SMF 3.0 compat files
	 */
	private array $cssFiles = [
		'admin.css',
		'attachments.css',
		'calendar.css',
		'index.css',
		'jquery.sceditor.css',
		'jquery.sceditor.default.css',
		'jquery.timepicker.css',
		'jquery-ui.datepicker.css',
		'responsive.css',
		'rtl.css',
		'slider.min.css',
	];

	/**
	 * SMF 3.0 compat files for a smooth transition later on
	 */
	public function styles() : void
	{
		global $settings, $context;

		// Profile
		loadCSSFile('compat/profile.css', ['minimize' => true], 'smf_profile');

		// Postbit
		loadCSSFile('compat/postbit.css', ['minimize' => true], 'smf_post');

		// Variables file
		loadCSSFile('compat/variables.css', ['minimize' => true, 'order_pos' => -2], 'smf_variables');

		// Dark Mode?
		if (!empty(Config::$current->darkMode) && (!empty($context['theme_can_change_mode']) || (!empty($settings['st_theme_mode_default']) && $settings['st_theme_mode_default'] !== 'light'))) {
			loadCSSFile('compat/dark.css', ['order_pos' => 2, 'attributes' => (isset($context['theme_colormode']) && $context['theme_colormode'] == 'system' ? ['media' => '(prefers-color-scheme: dark)'] : [])], 'smf_dark');
		}

		
		// Modify the URL and path of the files
		foreach ($context['css_files'] as $id => $file) {
			if (in_array($file['fileName'], $this->cssFiles)) {
				$context['css_files'][$id]['fileUrl'] = $settings['theme_url'] . '/css/compat/' . $file['fileName'];
				$context['css_files'][$id]['filePath'] = $settings['theme_dir'] . '/css/compat/' . $file['fileName'];
			}
		}

		// Remove rtl calendar
		$settings['disable_files'][] = 'calendar.rtl_css';
	}

	/**
	 * Add loaded files to the sceditor
	 * @param array $sce_options The current sceditor options
	 */
	public function sceditor(array &$sce_options) : void
	{
		global $context, $settings;

		// Use the comapt directory
		$sce_options['style'] = $settings['theme_url'] . '/css/compat/jquery.sceditor.default.css';

		// Index.css
		$sce_options['style'] =  $settings['theme_url'] . '/css/compat/index.css"/><link rel="stylesheet" href="' . $sce_options['style'];

		// Dark Mode?
		if (!empty($context['theme_can_change_mode']) || (!empty($settings['st_theme_mode_default']) && $settings['st_theme_mode_default'] !== 'light')) {
			$sce_options['style'] =  $settings['theme_url'] . '/css/compat/dark.css"' . (isset($context['theme_colormode']) && $context['theme_colormode'] == 'system' ? ' media="(prefers-color-scheme: dark)"' : '') . '/><link rel="stylesheet" href="' . $sce_options['style'];
		}

		// Variables
		$sce_options['style'] =  $settings['theme_url'] . '/css/compat/variables.css"/><link rel="stylesheet" href="' . $sce_options['style'];
	}
}