<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Settings;

class Carousel
{
	/**
	 * @var int @Number of carousel slides.
	 */
	private int $slides = 1;

	/**
	 * @var int Slides limit
	 */
	private int $limit = 5;

	/**
	 * @var array Theme settings
	 */
	private array $settings = [];

	/**
	 * Add the main carousel settings
	 * @param array $theme_settings The current theme settings
	 * @param array $settings_types The current types of settings
	 */
	public function settings(array &$theme_settings, array &$settings_types) : void
	{
		global $txt;

		// Add carousel setting type
		if (!empty($settings_types)) {
			$settings_types[] = 'carousel';
		}

		// Settings
		$this->settings = [
			// Enable carousel
			[
				'id' => 'st_enable_carousel',
				'label' => $txt['st_enable_carousel'],
				'description' => $txt['st_enable_carousel_desc'],
				'theme_type' => 'carousel',
			],
			// Carousel only in index
			[
				'id' => 'st_carousel_index',
				'label' => $txt['st_carousel_index'],
				'theme_type' => 'carousel',
			],
			// Link text
			[
				'id' => 'st_carousel_button_text',
				'label' => $txt['st_carousel_link_text'],
				'description' => $txt['st_carousel_link_text_desc'],
				'type' => 'text',
				'theme_type' => 'carousel',
			],
			// Carousel Speed
			[
				'id' => 'st_carousel_speed',
				'label' => $txt['st_carousel_speed'],
				'description' => $txt['st_carousel_speed_desc'],
				'type' => 'number',
				'step' => '250',
				'theme_type' => 'carousel',
			],
		];

		// How many slides?
		$this->settings[] = [
			'id' => 'st_carousel_slides',
			'label' => $txt['st_carousel_slides'],
			'description' => $txt['st_carousel_slides_desc'],
			'type' => 'number',
			'max' => $this->limit,
			'step' => '1',
			'theme_type' => 'carousel',
		];

		// Slides
		$this->slides();

		// Add them to the settings
		$theme_settings = array_merge($this->settings, $theme_settings);
	}

	/**
	 * Add the slide options based on the number set.
	 */
	private function slides() : void
	{
		global $txt, $settings;

		// Is the carousel enabled?
		if (empty($settings['st_enable_carousel']))
			return;

		// Set the number
		$this->slides = (!empty($settings['st_carousel_slides']) && $settings['st_carousel_slides'] <= $this->limit ? $settings['st_carousel_slides'] : 0);

		// Add the slides settings
		for ($i = 1; $i <= $this->slides; $i++)
		{
			// Title
			$this->settings[] = [
				'section_title' => sprintf($txt['st_slider_x'], $i),
				'id' => 'st_carousel_title_' . $i,
				'label' => $txt['st_carousel_title'],
				'description' => $txt['st_carousel_title_desc'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
			// Caption
			$this->settings[] = [
				'id' => 'st_carousel_text_' . $i,
				'label' => $txt['st_carousel_text'],
				'type' => 'textarea',
				'theme_type' => 'carousel',
			];
			// Link
			$this->settings[] = [
				'id' => 'st_carousel_link_' . $i,
				'label' => $txt['st_carousel_link'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
			// Image
			$this->settings[] = [
				'id' => 'st_carousel_image_url_' . $i,
				'label' => $txt['st_carousel_image_url'],
				'type' => 'text',
				'theme_type' => 'carousel',
			];
		}
	}
}