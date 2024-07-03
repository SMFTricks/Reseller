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
 * Displays a sortable listing of all members registered on the forum.
 */
function template_main()
{
	global $context, $settings, $scripturl, $txt;

	echo '
	<div class="main_section" id="memberlist">
		<div class="pagesection">
			<div class="pagelinks">', $context['page_index'], '</div>
			', template_button_strip($context['memberlist_buttons'], 'right'), '
		</div>
		<div class="cat_bar">
			<h3 class="catbg">
				', $txt['members_list'], '
			</h3>
		</div>';

	echo '
		<div id="mlist">';

	// Assuming there are members loop through each one displaying their data.
	if (!empty($context['members']))
	{
		foreach ($context['members'] as $member)
		{
			echo '
			<div class="block-member windowbg">
				', !empty($settings['st_enable_avatars_mlist']) && !empty($member['avatar']) ? themecustoms_avatar($member['avatar']['href'], $member['id']) : '', '
				<h4>
					', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['text'] . '">' : '', $settings['use_image_buttons'] ? '<span class="' . ($member['online']['is_online'] == 1 ? 'on' : 'off') . '" title="' . $member['online']['text'] . '"></span>' : $member['online']['label'], $context['can_send_pm'] ? '</a>' : '', '
					', $member['link'], '
				</h4>
				<span class="group">
					', empty($member['group']) ? $member['post_group'] : $member['group'], '
				</span>
				<span class="registered">
				', $txt['date_registered'], ': ', $member['registered_date'], '
				</span>
				', !isset($context['disabled_fields']['posts']) ? '
				<span class="posts">'. $txt['posts'] . ': ' . $member['posts'] . '</span>' : '', '
				', (!isset($context['disabled_fields']['website']) && !empty($member['website']['url'])) ? '
				<span class="website">
					<a href="' . $member['website']['url'] . '" target="_blank" rel="noopener">
						<span class="main_icons www"></span> ' . $member['website']['title'] . '
					</a>
				</span>' : '';

			// Show custom fields marked to be shown here
			if (!empty($context['custom_profile_fields']['columns']))
				foreach ($context['custom_profile_fields']['columns'] as $key => $column)
					echo '
					<span class="', $key, '">', $member['options'][$key], '</span>';

			echo '
			</div>';
		}
	}
	// No members?
	else
		echo '
			<div class="roundframe noup">
				', $txt['search_no_results'], '
			</div>';

	echo '
		</div>
		<div class="pagesection">
			<div class="pagelinks floatleft">', $context['page_index'], '</div>';

	// If it is displaying the result of a search show a "search again" link to edit their criteria.
	if (isset($context['old_search']))
		echo '
			<div class="buttonlist">
				<a class="button" href="', $scripturl, '?action=mlist;sa=search;search=', $context['old_search_value'], '">', $txt['mlist_search_again'], '</a>
			</div>';
	echo '
		</div>
	</div><!-- #memberlist -->';

}

/**
 * A page allowing people to search the member list.
 */
function template_search()
{
	global $context, $scripturl, $txt;

	// Start the submission form for the search!
	echo '
	<form action="', $scripturl, '?action=mlist;sa=search" method="post" accept-charset="', $context['character_set'], '">
		<div id="memberlist">
			<div class="pagesection">
				', template_button_strip($context['memberlist_buttons'], 'right'), '
			</div>
			<div class="cat_bar">
				<h3 class="catbg mlist">
					<span class="main_icons filter"></span>', $txt['mlist_search'], '
				</h3>
			</div>
			<div id="advanced_search" class="roundframe">
				<dl id="mlist_search" class="settings">
					<dt>
						<label><strong>', $txt['search_for'], ':</strong></label>
					</dt>
					<dd>
						<input type="text" name="search" value="', $context['old_search'], '" size="40">
					</dd>
					<dt>
						<label><strong>', $txt['mlist_search_filter'], ':</strong></label>
					</dt>
					<dd>
						<ul>';

	foreach ($context['search_fields'] as $id => $title)
		echo '
							<li>
								<input type="checkbox" name="fields[]" id="fields-', $id, '" value="', $id, '"', in_array($id, $context['search_defaults']) ? ' checked' : '', '>
								<label for="fields-', $id, '">', $title, '</label>
							</li>';

	echo '
						</ul>
					</dd>
				</dl>
				<input type="submit" name="submit" value="' . $txt['search'] . '" class="button floatright">
			</div><!-- #advanced_search -->
		</div><!-- #memberlist -->
	</form>';
}