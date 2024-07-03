<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2022 Simple Machines and individual contributors
 * @license https://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1.0
 */

/**
 * This contains the HTML for the menu bar at the top of the admin center.
 */
function template_generic_menu_dropdown_above()
{
	global $context, $txt;

	// Which menu are we rendering?
	$context['cur_menu_id'] = isset($context['cur_menu_id']) ? $context['cur_menu_id'] + 1 : 1;
	$menu_context = &$context['menu_data_' . $context['cur_menu_id']];
	$menu_label = isset($context['admin_menu_name']) ? $txt['admin_center'] : (isset($context['moderation_menu_name']) ? $txt['moderation_center'] : '');

	// Load the menu
	echo '
	<nav id="genericmenu" class="navbar navbar-expand-lg" aria-label="', sprintf($txt['mobile_generic_menu'], $menu_label), '">
		<a class="navbar-brand d-lg-none">', sprintf($txt['mobile_generic_menu'], $menu_label), '</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#genericNavbar" aria-controls="genericNavbar" aria-expanded="false" aria-label="', $txt['st_menu_toggle'], '">
			<i class="fa fa-bars"></i>
		</button>
		', template_generic_menu($menu_context, $menu_label), '
	</nav>
	<script>
		$( ".mobile_generic_menu_', $context['cur_menu_id'], '" ).click(function() {
			$( "#mobile_generic_menu_', $context['cur_menu_id'], '" ).show();
			});
		$( ".hide_popup" ).click(function() {
			$( "#mobile_generic_menu_', $context['cur_menu_id'], '" ).hide();
		});
	</script>';

	echo '
				<div id="admin_content">
					', template_generic_menu_tabs($menu_context);
}

/**
 * Part of the admin layer - used with generic_menu_dropdown_above to close the admin content div.
 */
function template_generic_menu_dropdown_below()
{
	echo '
				</div><!-- #admin_content -->';
}

/**
 * The template for displaying a menu
 *
 * @param array $menu_context An array of menu information
 */
function template_generic_menu(&$menu_context)
{
	global $context;

	echo '
			<div class="collapse navbar-collapse" id="genericNavbar">
				<ul class="navbar-nav dropdown_menu_', $context['cur_menu_id'], '">';

	// Main areas first.
	foreach ($menu_context['sections'] as $section)
	{
		echo '
					<li class="nav-item', !empty($section['areas']) ? ' dropdown' : '', '">
						<a class="nav-link', !empty($section['areas']) ? ' dropdown-toggle' : '', !empty($section['selected']) ? ' active' : '', '" href="', $section['url'], $menu_context['extra_parameters'], '"', !empty($section['areas']) ? ' role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"' : '', '>
							<span class="textmenu">', $section['title'], '</span>
							', !empty($section['amt']) ? ' <span class="amt">' . $section['amt'] . '</span>' : '', '
						</a>
						<ul class="dropdown-menu">';

		// For every area of this section show a link to that area (bold if it's currently selected.)
		// @todo Code for additional_items class was deprecated and has been removed. Suggest following up in Sources if required.
		foreach ($section['areas'] as $i => $area)
		{
			// Not supposed to be printed?
			if (empty($area['label']))
				continue;

			echo '
								<li class="dropend">
									<a class="dropdown-item ', $area['icon_class'], !empty($area['selected']) ? ' active' : '', !empty($area['subsections']) && empty($area['hide_subsections']) ? ' dropdown-toggle' : '', '" href="', (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i), $menu_context['extra_parameters'], '"', !empty($area['subsections']) && empty($area['hide_subsections']) ? ' role="button" data-bs-toggle="dropdown" aria-expanded="false"' : '', '>
										', $area['icon'], '
										<span class="textmenu">', $area['label'],'</span>
										', !empty($area['amt']) ? ' <span class="amt">' . $area['amt'] . '</span>' : '', '
									</a>';

			// Is this the current area, or just some area?
			if (!empty($area['selected']) && empty($context['tabs']))
				$context['tabs'] = isset($area['subsections']) ? $area['subsections'] : array();

			// Are there any subsections?
			if (!empty($area['subsections']) && empty($area['hide_subsections']))
			{
				echo '
									<ul class="dropdown-menu">';

				foreach ($area['subsections'] as $sa => $sub)
				{
					if (!empty($sub['disabled']))
						continue;

					$url = isset($sub['url']) ? $sub['url'] : (isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $i) . ';sa=' . $sa;

					echo '
										<li>
											<a class="dropdown-item', !empty($sub['selected']) ? ' active' : '', '" href="', $url, $menu_context['extra_parameters'], '">
												<span class="textmenu">', $sub['label'], '</span>
												', !empty($sub['amt']) ? ' <span class="amt">' . $sub['amt'] . '</span>' : '', '
											</a>
										</li>';
				}

				echo '
									</ul>';
			}

			echo '
								</li>';
		}
		echo '
							</ul>
					</li>';
	}

	echo '
				</ul>
			</div>';
}

/**
 * The code for displaying the menu
 *
 * @param array $menu_context An array of menu context data
 */
function template_generic_menu_tabs(&$menu_context)
{
	global $context, $settings, $scripturl, $txt;

	// Handy shortcut.
	$tab_context = &$menu_context['tab_data'];

	if (!empty($tab_context['title']))
	{
		echo '
					<div class="cat_bar">
						<h3 class="catbg">';

		// Exactly how many tabs do we have?
		if (!empty($context['tabs']))
		{
			foreach ($context['tabs'] as $id => $tab)
			{
				// Can this not be accessed?
				if (!empty($tab['disabled']))
				{
					$tab_context['tabs'][$id]['disabled'] = true;
					continue;
				}

				// Did this not even exist - or do we not have a label?
				if (!isset($tab_context['tabs'][$id]))
					$tab_context['tabs'][$id] = array('label' => $tab['label']);
				elseif (!isset($tab_context['tabs'][$id]['label']))
					$tab_context['tabs'][$id]['label'] = $tab['label'];

				// Has a custom URL defined in the main admin structure?
				if (isset($tab['url']) && !isset($tab_context['tabs'][$id]['url']))
					$tab_context['tabs'][$id]['url'] = $tab['url'];

				// Any additional parameters for the url?
				if (isset($tab['add_params']) && !isset($tab_context['tabs'][$id]['add_params']))
					$tab_context['tabs'][$id]['add_params'] = $tab['add_params'];

				// Has it been deemed selected?
				if (!empty($tab['is_selected']))
					$tab_context['tabs'][$id]['is_selected'] = true;

				// Does it have its own help?
				if (!empty($tab['help']))
					$tab_context['tabs'][$id]['help'] = $tab['help'];

				// Is this the last one?
				if (!empty($tab['is_last']) && !isset($tab_context['override_last']))
					$tab_context['tabs'][$id]['is_last'] = true;
			}

			// Find the selected tab
			foreach ($tab_context['tabs'] as $sa => $tab)
			{
				if (!empty($tab['is_selected']) || (isset($menu_context['current_subsection']) && $menu_context['current_subsection'] == $sa))
				{
					$selected_tab = $tab;
					$tab_context['tabs'][$sa]['is_selected'] = true;
				}
			}
		}

		// Show an icon and/or a help item?
		if (!empty($selected_tab['icon_class']) || !empty($tab_context['icon_class']) || !empty($selected_tab['icon']) || !empty($tab_context['icon']) || !empty($selected_tab['help']) || !empty($tab_context['help']))
		{
			if (!empty($selected_tab['icon_class']) || !empty($tab_context['icon_class']))
				echo '
								<span class="', !empty($selected_tab['icon_class']) ? $selected_tab['icon_class'] : $tab_context['icon_class'], ' icon"></span>';
			elseif (!empty($selected_tab['icon']) || !empty($tab_context['icon']))
				echo '
								<img src="', $settings['images_url'], '/icons/', !empty($selected_tab['icon']) ? $selected_tab['icon'] : $tab_context['icon'], '" alt="" class="icon">';

			if (!empty($selected_tab['help']) || !empty($tab_context['help']))
				echo '
								<a href="', $scripturl, '?action=helpadmin;help=', !empty($selected_tab['help']) ? $selected_tab['help'] : $tab_context['help'], '" onclick="return reqOverlayDiv(this.href);" class="help"><span class="main_icons help" title="', $txt['help'], '"></span></a>';

			echo $tab_context['title'];
		}
		else
			echo '
								', $tab_context['title'];

		echo '
						</h3>';

		// The function is in Admin.template.php, but since this template is used elsewhere too better check if the function is available
		if (function_exists('template_admin_quick_search'))
			template_admin_quick_search();

		echo '
					</div>';
	}

	// Shall we use the tabs? Yes, it's the only known way!
	if (!empty($selected_tab['description']) || !empty($tab_context['description']))
		echo '
					<p class="information">
						', !empty($selected_tab['description']) ? $selected_tab['description'] : $tab_context['description'], '
					</p>';

	// Print out all the items in this tab (if any).
	if (!empty($context['tabs']))
	{
		// The admin tabs.
		echo '
					<nav id="adm_submenus" class="navbar navbar-expand-lg" aria-label="', sprintf($txt['mobile_generic_menu'], $tab_context['title']), '">
						<a class="navbar-brand d-lg-none">', sprintf($txt['mobile_generic_menu'], $tab_context['title']), '</a>
						<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#genericSubmenu" aria-controls="genericSubmenu" aria-expanded="false" aria-label="', $txt['st_menu_toggle'], '">
							<i class="fa fa-bars"></i>
						</button>
						<div class="collapse navbar-collapse" id="genericSubmenu">
							<ul class="navbar-nav dropdown_menu_', $context['cur_menu_id'], '_tabs">';

		foreach ($tab_context['tabs'] as $sa => $tab)
		{
			if (!empty($tab['disabled']))
				continue;

			echo '

								<li class="nav-item">
									<a class="nav-link', !empty($tab['is_selected']) ? ' active"' : '', '" href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'] . ';sa=' . $sa, $menu_context['extra_parameters'], isset($tab['add_params']) ? $tab['add_params'] : '', '">
										<span class="textmenu">', $tab['label'], '</span>
									</a>
								</li>';
		}

		// The end of tabs
		echo '
							</ul>
						</div>
					</nav><!-- #adm_submenus -->';
	}
}