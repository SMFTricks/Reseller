<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

use ThemeCustoms\Config;

/**
 * Carousel main layout
 * @param bool $carousel If the carousel is being displayed. This doesn't need to be set ever, it's just for internal use.
 */
function themecustoms_carousel($carousel = false) : void
{
	global $settings, $txt, $context, $board;

	// Is the carousel enabled?
	if (empty($settings['st_enable_carousel']) || empty($settings['st_carousel_slides']) )
		return;

	// Always enabled?
	if (empty($settings['st_carousel_index']) || (empty($context['current_action']) && empty($board)))
		$carousel = true;

	// Check if the carousel should be displayed
	if (!$carousel)
		return;

	// Normal carousel
	if (empty($context['st_carousel_full']))
		carousel_settings();

	// Check if there are at least 2 slides
	if (empty($context['st_carousel_items']))
		return;

	echo '
	<div id="themecustoms-carousel" class="carousel slide st_carousel"', (!empty($settings['st_carousel_speed']) ? ' data-bs-ride="carousel"' : ''), '>
		<div class="carousel-indicators">';

	// Carousel indicators
	foreach ($context['st_carousel_items'] as $number => $item)
	{
		// Remove it if there's no title
		if (empty($item['title']))
			continue;

		echo '
			<a role="button" data-bs-target="#themecustoms-carousel" data-bs-slide-to="', ($number - 1), '"', (!empty($item['active']) ? ' class="active"' : ''), ' aria-current="true" aria-label="', $txt['st_slide'], ' ', $number, '">
				<span class="title">', $item['title'], '</span>
			</a>';
	}

	echo  '
		</div>
		<div class="carousel-inner">';

	// Carousel slides
	foreach ($context['st_carousel_items'] as $number => $slide)
	{
		// Remove it if there's no title
		if (empty($slide['title']))
			continue;

		echo '
			<div class="carousel-item', (!empty($slide['active']) ? ' active' : ''), '" ', (!empty($settings['st_carousel_speed']) ? ' data-bs-interval="' . $settings['st_carousel_speed'] . '"' : ''), (!empty($slide['image']) ? ' style="background-repeat: no-repeat; background-size: cover; background-image: url(' . $slide['image'] . ');"' : ''), '>
				<div class="carousel-block">
					<div class="carousel-caption">
						<h5>', $slide['title'], '</h5>
						', !empty($slide['text']) ? '<p>' . $slide['text'] . '</p>' : '', '
						', (!empty($slide['link']) ? '<a class="button" href="' . $slide['link'] . '">' . (!empty($settings['st_carousel_button_text']) ? $settings['st_carousel_button_text'] : $txt['st_carousel_go_to_link']) . '</a>' : ''), '
					</div>
				</div>
			</div>';
	}

	echo '
		</div>';

	// No buttons or arrows without slides
	if (count($context['st_carousel_items']) < 2)
		return;

	echo '
		<a role="button" class="carousel-control carousel-control-prev" data-bs-target="#themecustoms-carousel" data-bs-slide="prev" title="', $txt['st_previous'], '">
			<span class="fa fa-chevron-left" aria-hidden="true"></span>
			<span class="visually-hidden">', $txt['st_previous'], '</span>
		</a>
		<a role="button" class="carousel-control carousel-control-next" data-bs-target="#themecustoms-carousel" data-bs-slide="next" title="', $txt['st_next'], '">
			<span class="fa fa-chevron-right" aria-hidden="true"></span>
			<span class="visually-hidden">', $txt['st_next'], '</span>
		</a>
	</div>';
}

/**
 * Build the carousel settings for the normal carousel
 */
function carousel_settings() : void
{
	global $settings, $context;

	$context['carousel_items'] = [];
	// Loop through the number of slides
	for ($slide = 1; $slide <= $settings['st_carousel_slides']; $slide++)
	{
		// Need a title.
		if (empty($settings['st_carousel_title_' . $slide]))
			continue;

		// Fill the carousel
		$context['st_carousel_items'][$slide] = [
			'title' => $settings['st_carousel_title_' . $slide],
			'text' => $settings['st_carousel_text_' . $slide],
			'link' => $settings['st_carousel_link_' . $slide],
			'image' => $settings['st_carousel_image_url_' . $slide],
		];

		if ($slide === 1)
			$context['st_carousel_items'][$slide]['active'] = true;
	}
}