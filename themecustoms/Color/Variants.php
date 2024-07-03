<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Color;

use ThemeCustoms\Config;

class Variants
{
	/**
	 * @var int Order position for the variants file
	 */
	private int $order = 151;

	/**
	 * @var array Theme settings
	 */
	private array $settings = [];

	/**
	 * @var array Variants listed for selection
	 */
	private array $selectVariants = [];

	/**
	 * Initializes the theme color variants features
	 */
	public function __construct()
	{
		// Are there any variants?
		if (Config::$current->variants === []) {
			return;
		}

		// Initialize the variants
		$this->init();
	}

	/**
	 * Load the variants
	 */
	private function init() : void
	{
		global $context, $settings;

		// Set the variants selection
		$this->selection();

		// No need to load if the setting is disabled and the default variant is selected
		if (!empty($context['theme_can_change_variants']) || $settings['default_variant'] !== 'default') {
			// Load the css
			$this->css();

			// Load the javascript
			$this->js();

			// Style sceditor needs extra help
			add_integration_function('integrate_sceditor_options', __CLASS__ . '::sceditor', false, Config::$current->dir . '/themecustoms/Color/Variants.php', true);
		}

		// User picking theme
		add_integration_function('integrate_theme_context', __CLASS__ . '::pickTheme', false, Config::$current->dir . '/themecustoms/Color/Variants.php', true);

		// User options
		add_integration_function('integrate_theme_options', __CLASS__ . '::options', false, Config::$current->dir . '/themecustoms/Color/Variants.php', true);

		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != Config::$current->id)
			return;

		add_integration_function('integrate_customtheme_settings', __CLASS__ . '::settings', false, Config::$current->dir . '/themecustoms/Color/Variants.php', true);
	}

	/**
	 * Set the currently selected variant
	 */
	private function selection() : void
	{
		global $context, $settings, $smcFunc, $options, $txt;

		$context['theme_can_change_variants'] = empty($settings['disable_user_variant']) || allowedTo('admin_forum');
		$settings['theme_colorvariants'] = array_unique(array_merge(['default'], Config::$current->variants));

		// Create the variant options, using text strings
		foreach($settings['theme_colorvariants'] as $variant) {
			$this->selectVariants[$variant] = $txt['variant_' . $variant];
		}

		// Overriding - for previews and that.
		if (!empty($_REQUEST['variant'])) {
			$_SESSION['variant'] = $_REQUEST['variant'];

			// If the user is logged, save this to their profile
			if ($context['user']['is_logged'] && in_array($_SESSION['variant'], $settings['theme_colorvariants'])) {
				$smcFunc['db_insert'](
					'replace',
					'{db_prefix}themes',
					['id_theme' => 'int', 'id_member' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'],
					[Config::$current->id, $context['user']['id'], 'theme_variant', $_SESSION['variant']],
					['id_theme', 'id_member', 'variable'],
				);
			}
		}

		// User selection?
		if (!empty($context['theme_can_change_variants'])) {
			$context['theme_variant'] = !empty($_SESSION['variant']) && in_array($_SESSION['variant'], $settings['theme_colorvariants']) ? $_SESSION['variant'] : (!empty($options['theme_variant']) && in_array($options['theme_variant'], $settings['theme_colorvariants']) ? $options['theme_variant'] : '');
		}

		// If not a user variant, select the default
		if ($context['theme_variant'] == '' || !in_array($context['theme_variant'], $settings['theme_colorvariants'])) {
			$context['theme_variant'] = !empty($settings['default_variant']) && in_array($settings['default_variant'], $settings['theme_colorvariants']) ? $settings['default_variant'] : $settings['theme_colorvariants'][0];
		}
	}

	/**
	 * Loads the color variants CSS.
	 */
	private function css() : void
	{
		global $context, $settings;

		// Add the HTML data attribute for color variant
		$settings['themecustoms_html_attributes']['data']['variant'] = 'data-variant="' . $context['theme_variant'] . '"';

		// Add the CSS file for the variants
		loadCSSFile('custom/variants.css', ['order_pos' => $this->order], 'smf_variants');

		// Load the individual variant file just for compatibility or preference
		loadCSSFile('custom/index_' . $context['theme_variant'] . '.css', ['order_pos' => 300], 'smf_index_' . $context['theme_variant']);
	}

	/**
	 * Loads the variants JS.
	 */
	private function js() : void
	{
		global $context;

		// Theme Variant
		addJavaScriptVar('smf_theme_variant', $context['theme_variant'], true);

		// Load the javascript file
		loadJavascriptFile('custom/variants.js', ['async' => true, 'defer' => true, 'minimize' => true,], 'smf_variants');
	}

	/**
	 * The sceditor styling is extremely stupid, so you need to overengineer things to style it accordingly.
	 * @param array $sce_options The current sceditor options
	 */
	public function sceditor(array &$sce_options) : void
	{
		global $context, $settings;

		// Check if the file exists
		$variantStyle = file_exists($settings['theme_dir'] . '/css/custom/index_' . $context['theme_variant'] . '.css') ? '<link rel="stylesheet" href="' . $settings['theme_url']. '/css/custom/index_' . $context['theme_variant'] . '.css" />' : '';

		$sce_options['style'] = $sce_options['style'] . '"/>' . $variantStyle . '<link rel="stylesheet" href="' . $settings['theme_url']. '/css/custom/variants.css';

		// Add the data attribute
		addInlineJavaScript('
			$(document).ready(function() {
				$(\'.sceditor-container iframe\').each(function() {
					$(this).contents().find(\'html\').attr(\'data-variant\', "' . $context['theme_variant'] . '");
				});
			});
		', true);
	}

	/**
	 * Insert the color variants into the available themes list.
	 * This is needed so that the user can see the variants available in the pick theme page.
	 * I don't actually care for this, but SMF displays it by default.
	 */
	public function pickTheme() : void
	{
		global $context, $settings, $options, $txt, $options;

		// Can you change the variant?
		if (empty($context['theme_can_change_variants']))
			return;

		// Check only for the themes page
		if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'theme')
		{
			// Add the color variants to this theme
			foreach($context['available_themes'] as $id => $theme)
			{
				// Add the variants to the theme
				if ($theme['id'] == Config::$current->id)
				{
					// Add the variants with their label and thumbnail
					foreach($settings['theme_colorvariants'] as $variant)
						$context['available_themes'][$id]['variants'][$variant] = [
							'label' => isset($txt['variant_' . $variant]) ? $txt['variant_' . $variant] : $variant,
							'thumbnail' => !file_exists($theme['theme_dir'] . '/images/thumbnail.png') || file_exists($theme['theme_dir'] . '/images/thumbnail_' . $variant . '.png') ? $theme['images_url'] . '/thumbnail_' . $variant . '.png' : ($theme['images_url'] . '/thumbnail.png'),
						];

					// The selected variant
					$context['available_themes'][$id]['selected_variant'] = isset($_GET['vrt']) ? $_GET['vrt'] : (!empty($options['theme_variant']) && in_array($options['theme_variant'], $settings['theme_colorvariants']) ? $options['theme_variant'] : (!empty($settings['default_variant']) ? $settings['default_variant'] : $settings['theme_colorvariants'][0]));
					if (!isset($context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail']))
						$context['available_themes'][$id]['selected_variant'] = $settings['theme_colorvariants'][0];
					// Thumbnail
					$context['available_themes'][$id]['thumbnail_href'] = $context['available_themes'][$id]['variants'][$context['available_themes'][$id]['selected_variant']]['thumbnail'];
					// Allow themes to override the text.
					$context['available_themes'][$id]['pick_label'] = isset($txt['variant_pick']) ? $txt['variant_pick'] : $txt['theme_pick_variant'];
				}
			}
		}
	}

	/**
	 * Adds the color variants to the theme options.
	 * No idea why SMF doesn't do this by default.
	 */
	public function options() : void
	{
		global $context, $txt, $settings;

		// Insert the theme options
		$context['theme_options'] = array_merge(
			[
				$txt['st_color_variants'],
				[
					'id' => 'theme_variant',
					'label' => isset($txt['variant_pick']) ? $txt['variant_pick'] : $txt['theme_pick_variant'],
					'options' => $this->selectVariants,
					'default' => $settings['default_variant'] ?? $settings['theme_colorvariants'][0],
					'enabled' => $context['theme_can_change_variants'],
				]
			],
			$context['theme_options']
		);
	}

	/**
	 * Inserts the theme settings for the variants.
	 * @param array $theme_settings The current theme settings
	 * @param array $settings_types The current types of settings
	 */
	public function settings(array &$theme_settings, array &$settings_types) : void
	{
		global $txt, $settings;

		// Add color setting type
		$settings_types[] = 'color';

		// Settings
		$this->settings = [
			[
				'section_title' => $txt['theme_variants'],
				'id' => 'variant',
				'label' => $txt['theme_variants_default'],
				'description' => '<img src="' . $settings['images_url'] . '/thumbnail' . (!empty($settings['default_variant']) && $settings['default_variant'] !== 'default' ? '_' . $settings['default_variant'] : '') . '.png" id="variant_preview" class="theme_thumbnail" alt="">',
				'options' => $this->selectVariants,
				'type' => 'list',
				'default' => $settings['theme_colorvariants'][0],
				'theme_type' => 'color',
			],
			[
				'id' => 'disable_user_variant',
				'label' => $txt['theme_variants_user_disable'],
				'type' => 'checkbox',
				'theme_type' => 'color',
			]
		];

		// Change the thumbnail on selection
		addInlineJavaScript('
			document.querySelector(\'#options_variant\').addEventListener(\'change\', function() {
				let thumbnail = document.querySelector(\'#variant_preview\');
				let variant = \'_\' + this.value;
				thumbnail.src = smf_images_url + \'/thumbnail\' + (this.value === \'default\' ? \'\' : variant) + \'.png\';
			});
		', true);

		// Add them to the settings
		$theme_settings = array_merge($this->settings, $theme_settings);
	}
}