<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Integrations;

class Packages
{
	/**
	 * The types of packages
	 */
	private array $types = [
		'themecustoms_addon'
	];

	/**
	 * Add the addons to the list of modifications
	 */
	public function types() : void
	{
		global $context;

		// Your theme comes first
		$context['modification_types'] = array_merge($this->types, $context['modification_types']);
	}

	/**
	 * Add the sorting list, which will actually and ultimately add it to the list of modifications
	 * @param array $sort_id The type of modification
	 */
	public function sort(array &$sort_id) : void
	{
		foreach ($this->types as $type)
			$sort_id[$type] = 1;
	}
}