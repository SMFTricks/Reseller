<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Theme;

abstract class Init
{
	/**
	 * @var string Theme Name
	 */
	protected string $name;

	/**
	 * @var string Theme Version
	 */
	protected string $version;

	/**
	 * @var string Theme Author
	 */
	protected string $author;

	/**
	 * @var int Theme Author SMF ID
	 */
	protected int $authorId = 0;

	/**
	 * @var string Theme Default Color
	 */
	protected string $color = '#000000';

	/**
	 * @var string GitHub URL
	 */
	protected string $github;

	/**
	 * @var int SMF Customization Site ID
	 */
	protected int $customizationId = 0;

	/**
	 * @var int Theme Support Topic ID
	 */
	protected int $customizationSupport = 0;

	/**
	 * @var string Custom Suport URL
	 */
	protected string $supportURL;

	/**
	 * @var int Theme directory path
	 */
	public string $dir;

	/**
	 * @var int Theme ID
	 */
	public int $id;

	/**
	 * @var bool Include avatar options
	 */
	public bool $avatarOptions = true;

	/**
	 * @var bool Include animate.css (deprecated)
	 */
	public bool $animateCSS = false;

	/**
	 * @var array Color Variants
	 */
	public array $variants = [];

	/**
	 * @var bool Enable Dark Mode
	 */
	public bool $darkMode = false;

	/**
	 * @var array Add support for Color Changer MOD
	 */
	public array $colorChanges = [];

	/**
	 * @var bool Add the like button to the quickbuttons
	 */
	public bool $quickLikes = false;

	/**
	 * @var bool Using custom for the theme
	 */
	public bool $customFonts = true;

	/**
	 * @var bool Add the quick new topic button
	 */
	public bool $quickNewTopic = false;

	/** 
	 * @var bool Enable Compat with SMF 3.0 styles
	 */
	public bool $stylesCompat = false;

	/**
	 * @var int Number of Custom Links
	 */
	public int $customLinks = 0;

	/**
	 * @var bool Wheter to include bootstrap
	 */
	public bool $bootstrap = false;

	/**
	 * @var bool Wheter to include jQuery UI
	 */
	public bool $jqueryUI = false;

	/**
	 * @var bool Wheter to enable Carousel Addon
	 */
	public bool $addonCarousel = false;

	/**
	 * @var bool Wheter to enable Profile Cover Addon
	 */
	public bool $addonProfileCover = false;

	/**
	 * @var object Self instance
	 */
	public static object $current;

	/**
	 * Set the current instance for usage in other places
	 */
	final public function __construct()
	{
		self::$current = $this;
	}

	/**
	 * Init::details()
	 * 
	 * Load the theme details
	 * 
	 */
	public function details() : void
	{
		global $settings;

		// Theme Path
		$this->dir = $settings['theme_dir'];

		// Theme Id
		$this->id = $settings['theme_id'];

		// Theme Name
		$settings['theme_name'] = $this->name;

		// Theme Version
		$settings['theme_version'] = $this->version;

		// Theme Author
		$settings['theme_author'] = $this->author;

		// Theme Author
		$settings['theme_authorId'] = $this->authorId;

		// Theme Color
		$settings['theme_default_color'] = $this->color;

		// Theme GitHub
		$settings['theme_github'] = $this->github;

		// Customization ID
		$settings['theme_custId'] = $this->customizationId;

		// Support Topic
		$settings['theme_suppoprt'] = $this->customizationSupport;

		// Custom Support URL
		$settings['theme_support_url'] = $this->supportURL;
	}

	/**
	 * Init::configuration()
	 *
	 * Start some of the minimal and default settings
	 * 
	 */
	public function configuration() : void
	{
		global $settings;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
		$settings['avatars_on_indexes'] = $this->avatarOptions;

		// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
		$settings['avatars_on_boardIndex'] = $this->avatarOptions;

		// Set the following variable to true if this theme wants to display the login and register buttons in the main forum menu.
		$settings['login_main_menu'] = $settings['st_loginlogout_menu'] ?? false;

		// Allow css/js files to be disabled for this specific theme.
		// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
		if (!isset($settings['disable_files']))
			$settings['disable_files'] = [];

		// Add any custom attribute to the html tag
		// This is useful for things like variants, dark mode, etc.
		$settings['themecustoms_html_attributes'] = '';

		// Define the total amount of custom links to use.
		$settings['st_custom_links_limit'] = $this->customLinks;

		// This defines the formatting for the page indexes used throughout the forum.
		$settings['page_index'] = themecustoms_page_index();

		// Set the variants
		$settings['theme_variants'] = $this->variants;

		// Dark Mode
		$settings['has_dark_mode'] = $this->darkMode;

		// Color Changes
		$settings['color_changes'] = $this->colorChanges;
	}

	/**
	 * Init::settings()
	 * 
	 * Load the main theme settings
	 * 
	 */
	final public function settings() : void
	{
		// Are we viewing this theme?
		if (isset($_REQUEST['th']) && !empty($_REQUEST['th']) && $_REQUEST['th'] != $this->id)
			return;

		// Load the theme settings
		add_integration_function('integrate_theme_settings', 'ThemeCustoms\Settings\Main::settings', false, $this->dir . '/themecustoms/Settings/Main.php', true);
	}

	/**
	 * Init::forwardCompat
	 * 
	 * Checks for compat with SMF 3.0 styles
	 */
	final public function forwardCompat() : void
	{
		if (!empty($this->stylesCompat)) {
			add_integration_function('integrate_pre_css_output', 'ThemeCustoms\Theme\Compat::styles', false, $this->dir . '/themecustoms/Theme/Compat.php', true);
			add_integration_function('integrate_sceditor_options', 'ThemeCustoms\Theme\Compat::sceditor', false, $this->dir . '/themecustoms/Theme/Compat.php', true);
		}
	}

	/**
	 * Custom Hooks
	 */
	abstract public function loadHooks(); 
}
