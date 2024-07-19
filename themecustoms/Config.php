<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms;

use ThemeCustoms\Theme\Init;

class Config extends Init
{
	/**
	 * @var string Theme Name
	 */
	protected string $name = 'Reseller';

	/**
	 * @var string Theme Version
	 */
	protected string $version = '2.0';

	/**
	 * @var string Theme Author
	 */
	protected string $author = 'Diego Andrés';

	/**
	 * @var int Theme Author SMF ID
	 */
	protected int $authorId = 254071;

	/**
	 * @var string Theme Default Color
	 */
	protected string $color = '#3498DB';

	/**
	 * @var string GitHub URL
	 */
	protected string $github = 'https://github.com/SMFTricks/Reseller';

	/**
	 * @var int SMF Customization Site ID
	 */
	protected int $customizationId = 2806;

	/**
	 * @var string Theme Support Topic ID
	 */
	protected int $customizationSupport = 525855;

	/**
	 * @var string Custom Support URL
	 */
	protected string $supportURL = 'https://smftricks.com/index.php?topic=559.0';

	/**
	 * @var bool Add the quick new topic button
	 */
	public bool $quickNewTopic = true;

	/**
	 * @var bool Wheter to include bootstrap
	 */
	public bool $bootstrap = true;

	/** 
	 * @var bool Enable Compat with SMF 3.0 styles
	 */
	public bool $stylesCompat = true;

	/**
	 * @var bool Using custom for the theme
	 */
	public bool $customFonts = false;

	/**
	 * Init::loadHooks()
	 */
	public function loadHooks() : void
	{
		// // Load Custom JS
		// add_integration_function('integrate_pre_javascript_output', __CLASS__ . '::js', false, '$themedir/themecustoms/Init.php');

	}

	/**
	 * Load custom javascript
	 */
	public static function js() : void
	{
		// Custom js
		loadJavascriptFile('custom.js', [
			'force_current' => true,
			'defer' => true,
		], 'themecustom_js');
	}
}