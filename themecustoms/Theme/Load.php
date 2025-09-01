<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Theme;

use ThemeCustoms\Color\DarkMode;
use ThemeCustoms\Color\Variants;
use ThemeCustoms\Config;

class Load
{
	/**
	 * @var const Font Awesome version
	 */
	const FONTAWESOME_VERSION = '7.0.0';

	/**
	 * @var const jQuery UI version
	 */
	const JQUERYUI_VERSION = '1.13.2';

	/**
	 * @var array The libraries or frameworks to load, populated in libOptions()
	 */
	private array $libraries = [];

	/**
	 * @var int The initial order for the css files
	 */
	private int $order = -200;

	/**
	 * @var array The theme custom css files
	 */
	private array $cssFiles = [];

	/**
	 * @var array The theme custom js files
	 */
	private array $jsFiles = [];

	/**
	 * Setup the list of CSS files
	 */
	public function cssFiles() : void
	{
		$this->cssFiles = [
			'app' => [
				'order_pos' => Config::$current->stylesCompat ? 9001 : 3000,
			],
			'custom_edits' => [
				'order_pos' => 9003,
			],
			'icons',
			'theme_responsive' => [
				'order_pos' => 9002,
			],
			'theme_colors' => [
				'order_pos' => 100,
			],
		];
	}

	/**
	 * Setup the list of JS files
	 */
	public function jsFiles() : void
	{
		$this->jsFiles = [
			'main',
		];
	}

	/**
	 * Load the theme essentials
	 */
	public function theme() : void
	{
		// Include any libraries or frameworks
		$this->libraries();

		// Load any custom templates
		$this->templates();

		// Load the CSS
		$this->css();

		// Inline styles
		$this->cssInline();

		// Load the JS
		$this->js();

		// Theme JS Vars
		$this->jsVars();

		// SCEditor styling
		$this->sceditor();
		
		// Dark Mode
		new DarkMode;

		// Theme Variants
		new Variants;

		// Color Changer
		// new Changer;
		// $this->color_changer();

		// Carousel
		$this->carousel();
	}

	/**
	 * Load the default libraries and frameworks.
	 * Some can be disabled with theme settings.
	 */
	private function libraries() : void
	{
		global $settings;

		$this->libraries = [
			// Animate
			'animate' => [
				'include' => $settings['st_disable_theme_effects'] ?? false,
				'css' => [
					'minified' => true,
				],
			],
			// Bootstrap
			'bootstrap' => [
				'include' => Config::$current->bootstrap,
				'css' => [
					'minified' => true,
					'order_pos' => -100
				],
				'js' => [
					'file' => 'bootstrap.bundle',
					'minified' => true,
					'defer' => true,
				],
			],
			// Coloris
			'coloris' => [
				'css' => [
					'minified' => true,
				],
				'js' => [
					'defer' => true,
					'minified' => true,
				],
			],
			// FontAwesome
			'fontawesome' => [
				'css' => [
					'file' => (!empty($settings['st_fontawesome_source']) ? 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . self::FONTAWESOME_VERSION . '/css/all.min.css' : 'all'),
					'external' => !empty($settings['st_fontawesome_source']),
					'minified' => empty($settings['st_fontawesome_source']),
				],
			],
			// jQuery UI
			'jqueryui' => [
				'include' => Config::$current->jqueryUI,
				'js' => [
					'file' => (!empty($settings['st_jquery_ui_source']) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . self::JQUERYUI_VERSION . '/jquery-ui.min.js' : 'jquery-ui'),
					'external' => !empty($settings['st_jquery_ui_source']),
					'defer' => true,
					'minified' => empty($settings['st_jquery_ui_source']),
				]
			],
		];
	}

	/**
	 * Load the custom templates
	 */
	private function templates() : void
	{
		global $context, $topic;

		// Load templates depending on our current action
		if (!empty($context['current_action']))
		{
			switch ($context['current_action'])
			{
				case 'forum':
					loadTemplate('themecustoms/templates/board');
					break;
				case 'profile':
					loadTemplate('themecustoms/templates/profile');
					break;
				default:
					break;
			}
		}
		// Board
		elseif (empty($topic))
			loadTemplate('themecustoms/templates/board');
	}

	/**
	 * Add the theme css files
	 */
	private function css() : void
	{
		// Load the custom css files
		$this->cssFiles();

		// Add the css libraries first
		if (!empty($this->libraries))
		{
			foreach ($this->libraries as $file => $options)
			{
				if ((!empty($options['include']) || !isset($options['include'])) && !empty($options['css']))
				{
					loadCSSFile(
						(!empty($options['css']['file']) ? (!empty($options['css']['external']) ? $options['css']['file'] : ('custom/' . $options['css']['file'] . (!empty($options['css']['minified']) ? '.min' : '') . '.css')) : ('custom/' . $file . (!empty($options['css']['minified']) ? '.min' : '') . '.css')),
						[
							'attributes' => $options['css']['attributes'] ?? [],
							'external' => $options['css']['external'] ?? false,
							'minimize' => $options['css']['minimize'] ?? true,
							'order_pos' => $options['css']['order_pos'] ?? abs($this->order--),
						],
						'customtheme_' . $file
					);
				}
			}
		}

		// Now add the theme css files
		if (!empty($this->cssFiles))
		{
			foreach ($this->cssFiles as $file => $options)
			{
				loadCSSFile(
					(empty($options['default']) ? 'custom/' : '') . (!is_array($options) ? $options : $file) . '.css',
					[
						'attributes' => $options['attributes'] ?? [],
						'minimize' => $options['minimize'] ?? true,
						'order_pos' => $options['order_pos'] ?? abs($this->order--),
					],
					'customtheme_' . (!is_array($options) ? $options : $file)
				);
			}
		}
	}

	/**
	 * Add the inline styles
	 */
	private function cssInline() : void
	{
		// Add inline styles for any setting that requires it
		add_integration_function('integrate_pre_css_output', 'ThemeCustoms\Settings\Styles::buildCSS', false, Config::$current->dir . '/themecustoms/Settings/Styles.php', true);
	}

	/**
	 * Style the SCEditor
	 */
	private function sceditor() : void
	{
		global $context;

		// Style the SCEditor
		$context['css_customfiles'] = $this->cssFiles;
		add_integration_function('integrate_sceditor_options', 'ThemeCustoms\Settings\Styles::sceditor', false, Config::$current->dir . '/themecustoms/Settings/Styles.php', true);
	}

	/**
	 * Add the theme js files
	 */
	private function js() : void
	{
		// Load the custom js files
		$this->jsFiles();

		// Add the js libraries first
		if (!empty($this->libraries))
		{
			foreach ($this->libraries as $file => $options)
			{
				if ((!empty($options['include']) || !isset($options['include'])) && !empty($options['js']))
					loadJavaScriptFile(
						(!empty($options['js']['file']) ? (!empty($options['js']['external']) ? $options['js']['file'] : ('custom/' . $options['js']['file'] . (!empty($options['js']['minified']) ? '.min' : '') . '.js')) : ('custom/' . $file . (!empty($options['js']['minified']) ? '.min' : '') . '.js')),
						[
							'async' => $options['js']['async'] ?? false,
							'attributes' => $options['js']['attributes'] ?? [],
							'defer' => $options['js']['defer'] ?? false,
							'external' => $options['js']['external'] ?? false,
							'minimize' => $options['js']['minimize'] ?? true,
						],
						'customtheme_' . $file
					);
			}
		}

		// Now add the theme js files
		if (!empty($this->jsFiles))
		{
			foreach ($this->jsFiles as $file => $options)
			{
				loadJavaScriptFile(
					'custom/' . (!is_array($options) ? $options : $file) . '.js',
					[
						'async' => $options['async'] ?? false,
						'attributes' => $options['attributes'] ?? false,
						'defer' => $options['defer'] ?? false,
						'minimize' => $options['minimize'] ?? true,
					],
					'customtheme_' . (!is_array($options) ? $options : $file)
				);
			}
		}
	}

	/**
	 * Add the theme js variables
	 */
	private function jsVars() : void
	{
		global $settings;

		addJavaScriptVar('smf_theme_id', $settings['theme_id']);
		addJavaScriptVar('smf_newsfader_time', !empty($settings['newsfader_time']) ? $settings['newsfader_time'] : 5000);
	}

	/**
	 * It does nasty things to the theme footer
	 * @param string $buffer The content
	 * @param bool $return If returning the information in a different page
	 * @return mixed Surprise!
	 * @todo add return type
	 */
	public function unspeakable(string &$buffer, bool $return = false)
	{
		global $settings;

		// Do not remove the copyright without permission!
		$ST = 'Theme by <a href="https://smftricks.com">SMF Tricks</a>';
		// Return it
		if ($return)
			return (!empty($settings['theme_name']) ? $settings['theme_name'] . ' | ' : '') . $ST;
		// Stick it
		elseif (!isset($settings['theme_remove_copyright']) || empty($settings['theme_remove_copyright']))
			$buffer = preg_replace(
				'~(<li class="smf_copyright">)~',
				'<li>'. $ST . '</li>' . "$1 ",
				$buffer
			);
	}

	/**
	 * Load the theme carousel
	 */
	private function carousel() : void
	{
		// Carousel enabled?
		if (empty(Config::$current->addonCarousel))
			return;

		// Carousel language file
		loadLanguage('ThemeCustoms/carousel');

		// Carousel template file
		loadTemplate('themecustoms/templates/carousel');

		// Carousel settings
		add_integration_function('integrate_customtheme_settings', 'ThemeCustoms\Settings\Carousel::settings', false, Config::$current->dir . '/themecustoms/Settings/Carousel.php', true);
	}

	/**
	 * Theme::color_changer()
	 * 
	 * Add the color changer to the theme
	 * 
	 * @return void
	 */
	private function color_changer() : void
	{
		global $settings, $maintenance, $modSettings, $user_info;

		// // Maintenance Mode or Kicking guests?
		// if ((!empty($maintenance) && !allowedTo('admin_forum')) || (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']))
		// 	return;

		// // Theme Color Changer enabled?
		// if (!isset(Config::$config->colorOptions['colorchanger']) || empty(Config::$config->colorOptions['colorchanger']))
		// 	return;

		// // Add the settings for the color changer
		// if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] == $settings['theme_id'])
		// 	add_integration_function('integrate_theme_settings', 'ThemeCustoms\Color\Changer::settings#', false, '$themedir/themecustoms/Color/Changer.php');

		// // Add the color changes
		// add_integration_function('integrate_theme_context', 'ThemeCustoms\Color\Changer::colorChanges#', false, '$themedir/themecustoms/Color/Changer.php');
	}
}