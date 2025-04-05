<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

use ThemeCustoms\Config;

class Main
{
	/**
	 * The common theme settings
	 */
	private array $settings = [];

	/**
	 * Will allow to separate the settings if needed.
	 * No type means the setting is either a default setting or a main setting.
	 */
	private array $types = [];

	/**
	 * Unwanted settings from the default theme (or custom theme even).
	 */
	private array $removed = [
		'site_slogan',
		'enable_news',
		'forum_width',
	];

	/**
	 * The custom settings that are not listed here
	 */
	private array $custom = [];

	/**
	 * Build the theme settings
	 */
	public function settings() : void
	{
		// Create the theme settings
		$this->create();

		// Remove unwanted settings
		$this->remove();

		// Add theme settings
		$this->add();
	}

	/**
	 * Adds settings to the theme
	 */
	private function create() : void
	{
		global $txt, $context;

		// Insert forum width setting at the beginning
		$context['theme_settings'] = array_merge([
			[
				'id' => 'st_custom_width',
				'label' => $txt['st_custom_width'],
				'description' => $txt['st_custom_width_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_site_color',
				'label' => $txt['st_site_color'],
				'description' => $txt['st_site_color_desc'],
				'type' => 'color',
			]
		], $context['theme_settings']);

		// Theme Settings
		$this->settings = [
			// Menu Settings
			[
				'section_title' => $txt['st_menu_settings'],
				'id' => 'st_disable_menu_icons',
				'label' => $txt['st_disable_menu_icons'],
				'description' => $txt['st_disable_menu_icons_desc'],
				'type' => 'checkbox',
			],
			[
				'id' => 'st_loginlogout_menu',
				'label' => sprintf($txt['st_loginlogout_menu'], $txt['logout'], $txt['login'], $txt['register']),
			],
			[
				'id' => 'st_remove_items',
				'label' => $txt['st_remove_items'],
				'description' => $txt['st_remove_items_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_enable_community',
				'label' => $txt['st_enable_community'],
				'description' => $txt['st_enable_community_desc'],
			],
			[
				'id' => 'st_not_community',
				'label' => $txt['st_not_community'],
				'description' => $txt['st_not_community_desc'],
				'type' => 'text',
			],
			[
				'id' => 'st_community_forum',
				'label' => $txt['st_community_forum'],
				'description' => $txt['st_community_forum_desc'],
			],
			// Additional settings
			[
				'section_title' => $txt['st_additional_settings'],
				'id' => 'st_separate_sticky_locked',
				'label' => $txt['st_separate_sticky_locked'],
				'description' => $txt['st_separate_sticky_locked_desc'],
				'type' => 'checkbox'
			],
			[
				'id' => 'st_disable_info_center',
				'label' => $txt['st_disable_info_center'],
				'description' => $txt['st_disable_info_center_desc'],
				'type' => 'checkbox'
			],
		];

		// New Topic Button?
		if (!empty(Config::$current->quickNewTopic))
		{
			$this->settings[] = [
				'id' => 'st_new_topic_button',
				'label' => $txt['st_new_topic_button'],
				'description' => $txt['st_new_topic_button_desc'],
				'type' => 'checkbox'
			];
		}

		/** CDN Selection */
		$this->cdn();

		/** Socials **/
		$this->socials();
		
		/** Avatars **/
		$this->avatars();

		/** Custom Links **/
		$this->links();

		// Any custom changes?
		call_integration_hook('integrate_customtheme_settings', [&$this->custom, &$this->types, &$this->removed]);

		// Add any custom settings
		if (!empty($this->custom) && is_array($this->custom)) {
			$this->settings[] = '';
			$this->settings = array_merge($this->settings, $this->custom);
		}

		// Do not duplicate the setting types
		$this->types = array_unique($this->types);

		// Remove the values from undesired settings
		$this->undo();
	}

	/**
	 * Add settings for CDN
	 */
	private function cdn() : void
	{
		global $txt;

		// Font Awesome CDN
		$this->settings[] = [
			'section_title' => $txt['st_cdn_source'],
			'id' => 'st_fontawesome_source',
			'label' => $txt['st_fontawesome'],
			'description' => $txt['st_cdn_source_desc'],
			'type' => 'list',
			'options' => [
				0 => $txt['st_cdn_local'],
				1 => $txt['st_cdn_cloudflare'],
			]
		];

		// Fonts
		if (!empty(Config::$current->customFonts)) {
			$this->settings[] = [
				'id' => 'st_fonts_source',
				'label' => $txt['st_fonts'],
				'description' => $txt['st_fonts_desc'],
				'type' => 'list',
				'options' => [
					0 => $txt['st_cdn_local'],
					1 => $txt['st_cdn_google'],
				]
			];
		}

		// jQuery UI
		if (!empty(Config::$current->jqueryUI)) {
			$this->settings[] = [
				'id' => 'st_jquery_ui_source',
				'label' => $txt['st_jqueryui'],
				'description' => $txt['st_cdn_source_desc'],
				'type' => 'list',
				'options' => [
					0 => $txt['st_cdn_local'],
					1 => $txt['st_cdn_google'],
				]
			];
		}
	}

	/**
	 * Add settings for socials
	 */
	private function socials() : void
	{
		global $txt, $scripturl;

		// Add the type
		$this->types[] = 'social';

		// Social settings
		array_push($this->settings, 
			[
				'id' => 'st_facebook',
				'label' => $txt['st_facebook_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_twitter',
				'label' => $txt['st_twitter_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_instagram',
				'label' => $txt['st_instagram_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_youtube',
				'label' => $txt['st_youtube_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_tiktok',
				'label' => $txt['st_tiktok_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_twitch',
				'label' => $txt['st_twitch_username'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_discord',
				'label' => $txt['st_discord_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_steam',
				'label' => $txt['st_steam_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_github',
				'label' => $txt['st_github_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_linkedin',
				'label' => $txt['st_linkedin_link'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_threads',
				'label' => $txt['st_threads'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_bluesky',
				'label' => $txt['st_bluesky'],
				'description' => $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			],
			[
				'id' => 'st_rss_url',
				'label' => $txt['st_rss_url'],
				'description' => sprintf($txt['st_rss_url_desc'], $scripturl) . '<br>' . $txt['st_social_desc'],
				'type' => 'text',
				'theme_type' => 'social',
			]
		);
	}

	/**
	 * Add settings for avatars
	 */
	private function avatars() : void
	{
		global $txt;

		// Are avatars enabled?
		if (empty(Config::$current->avatarOptions))
			return;

		array_push($this->settings,
			// Boards
			[
				'section_title' => $txt['st_avatar_settings'],
				'id' => 'st_enable_avatars_boards',
				'label' => $txt['st_enable_avatars_boards'],
				'type' => 'checkbox',
			],
			// Topics
			[
				'id' => 'st_enable_avatars_topics',
				'label' => $txt['st_enable_avatars_topics'],
				'type' => 'checkbox',
			],
			// Recent Posts
			[
				'id' => 'st_enable_avatars_recent',
				'label' => $txt['st_enable_avatars_recent'],
				'type' => 'checkbox',
			],
			// Users Online
			[
				'id' => 'st_enable_avatars_online',
				'label' => $txt['st_enable_avatars_online'],
				'type' => 'checkbox',
			],
			// Member List
			[
				'id' => 'st_enable_avatars_mlist',
				'label' => $txt['st_enable_avatars_mlist'],
				'type' => 'checkbox',
			],
		);
	}

	/**
	 * Add custom links settings
	 */
	private function links() : void
	{
		global $txt;

		// Adding links settings?
		if (empty(Config::$current->customLinks))
			return;

		// Add the type
		$this->types[] = 'custom_links';

		// Enable custom links
		$this->settings[] = [
			'id' => 'st_custom_links_enabled',
			'label' => $txt['st_custom_links_enabled'],
			'theme_type' => 'custom_links',
		];

		// Add the links settings
		for ($link = 1; $link <= Config::$current->customLinks; $link++)
		{
			// Title
			$this->settings[] = [
				'id' => 'st_custom_link' . $link. '_title',
				'label' => $txt['st_custom_link_title'],
				'type' => 'text',
				'theme_type' => 'custom_links',
			];
			// Link
			$this->settings[] = [
				'id' => 'st_custom_link' . $link,
				'label' => sprintf($txt['st_custom_link'], $link),
				'description' => $txt['st_custom_link_url'],
				'type' => 'text',
				'theme_type' => 'custom_links',
			];
		}
	}

	/**
	 * Remove any unwanted settingss
	 */
	private function remove() : void
	{
		global $context;

		if (empty($this->removed)) {
			return;
		}

		// Remove Settings
		foreach ($context['theme_settings'] as $key => $theme_setting) {
			if (isset($theme_setting['id']) && in_array($theme_setting['id'], $this->removed)) {
				unset($context['theme_settings'][$key]);
			}
		}
	}

	/**
	 * Inserts the theme settings in the array
	 */
	private function add() : void
	{
		global $context;

		// Add the setting types
		if (!empty($this->types)) {
			$context['st_themecustoms_setting_types'] = array_merge(['main'], $this->types);
		}

		// Insert the new theme settings in the array
		$context['theme_settings'] = array_merge($context['theme_settings'], $this->settings);
	}

	/**
	 * Prevents undesired settings from affecting the forum.
	 * It obviously doesn't remove any setting from the database, just "disables" them.
	 */
	private function undo() : void
	{
		// Good riddance!
		if (!empty($this->removed) && isset($_POST)) {
			foreach ($this->removed as $remove_setting) {
				$_POST['options'][$remove_setting] = '';
			}
		}
	}
}