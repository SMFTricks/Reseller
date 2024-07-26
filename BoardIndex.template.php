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
 * The top part of the outer layer of the boardindex
 */
function template_boardindex_outer_above()
{
	template_newsfader();
}

/**
 * This shows the newsfader
 */
function template_newsfader()
{
	global $context, $settings;

	// Show the news fader?  (assuming there are things to show...)
	if (!empty($settings['show_newsfader']) && !empty($context['news_lines']))
	{
		echo '
		<div class="news_section">
			<div class="icon"><i class="fa-solid fa-exclamation"></i></div>
			<ul id="smf_slider">';

		foreach ($context['news_lines'] as $news)
			echo '
				<li>', $news, '</li>';

		echo '
			</ul>
		</div>
		<script>
			jQuery("#smf_slider").slippry({
				pause: ', $settings['newsfader_time'], ',
				adaptiveHeight: 0,
				captions: 0,
				controls: 0,
			});
		</script>';
	}
}

/**
 * This actually displays the board index
 */
function template_main()
{
	global $context, $txt, $scripturl;

	echo '
	<div id="boardindex_table" class="boardindex_table">';

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		echo '
		<div class="main_container">
			<div class="cat_bar ', $category['is_collapsed'] ? 'collapsed' : '', '" id="category_', $category['id'], '">
				<h3 class="catbg">
					', $category['link'], '
				</h3>';

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
				<span id="category_', $category['id'], '_upshrink" class="', $category['is_collapsed'] ? 'toggle_down' : 'toggle_up', ' floatright" data-collapsed="', (int) $category['is_collapsed'], '" title="', !$category['is_collapsed'] ? $txt['hide_category'] : $txt['show_category'], '" style="display: none;"></span>';

		echo '
			</div>
			', !empty($category['description']) ? '<div class="category_description">' . $category['description'] . '</div>' : '', '
			<div id="category_', $category['id'], '_boards" ', (!empty($category['css_class']) ? ('class="' . $category['css_class'] . '"') : ''), $category['is_collapsed'] ? ' style="display: none;"' : '', '>';

		/* Each board in each category's boards has:
		new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
		children (see below.), link_children (easier to use.), children_new (are they new?),
		topics (# of), posts (# of), link, href, and last_post. (see below.) */
		foreach ($category['boards'] as $board)
		{
			echo '
				<div id="board_', $board['id'], '" class="board_container sboard_', $board['board_class'], ' ', (!empty($board['css_class']) ? $board['css_class'] : ''), '">
					<div class="board_icon">
						', function_exists('template_bi_' . $board['type'] . '_icon') ? call_user_func('template_bi_' . $board['type'] . '_icon', $board) : template_bi_board_icon($board), '
					</div>
					<div class="board_info">
						', function_exists('template_bi_' . $board['type'] . '_info') ? call_user_func('template_bi_' . $board['type'] . '_info', $board) : template_bi_board_info($board), '
					</div><!-- .info -->';

			// Show some basic information about the number of posts, etc.
			echo '
					<div class="board_stats">
						', function_exists('template_bi_' . $board['type'] . '_stats') ? call_user_func('template_bi_' . $board['type'] . '_stats', $board) : template_bi_board_stats($board), '
					</div>';

			// Show the last post if there is one.
			echo'
					<div class="board_lastpost">
						', function_exists('template_bi_' . $board['type'] . '_lastpost') ? call_user_func('template_bi_' . $board['type'] . '_lastpost', $board) : template_bi_board_lastpost($board), '
					</div>';

			// Won't somebody think of the children!
			function_exists('template_bi_' . $board['type'] . '_children') ? call_user_func('template_bi_' . $board['type'] . '_children', $board) : template_bi_board_children($board);

			echo '
				</div><!-- #board_[id] -->';
		}

		echo '
			</div><!-- #category_[id]_boards -->
		</div><!-- .main_container -->';
	}

	echo '
	</div><!-- #boardindex_table -->';

	// Show the mark all as read button?
	if ($context['user']['is_logged'] && !empty($context['categories']))
		echo '
	<div class="mark_read">
		', template_button_strip($context['mark_read_button'], 'right'), '
	</div>';
}

/**
 * The lower part of the outer layer of the board index
 */
function template_boardindex_outer_below()
{
	template_info_center();
}

/**
 * Displays the info center
 */
function template_info_center()
{
	global $context, $options, $txt;

	if (empty($context['info_center']))
		return;

	// Here's where the "Info Center" starts...
	echo '
	<div id="info_center">
		<div class="title_bar">
			<h3 class="titlebg">
				<a href="#" id="upshrink_link">', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), '</a>
			</h3>
			<span class="toggle_up floatright" id="upshrink_ic" title="', $txt['hide_infocenter'], '" style="display: none;"></span>
		</div>
		<div id="upshrink_stats"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>';

	foreach ($context['info_center'] as $block)
	{
		$func = 'template_ic_block_' . $block['tpl'];
		echo '
			<div class="info_block">
				', $func(), '
			</div>';
	}

	echo '
		</div><!-- #upshrink_stats -->
	</div><!-- #info_center -->';

	// Info center collapse object.
	echo '
	<script>
		var oInfoCenterToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header_ic']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'upshrink_stats\'
			],
			aSwapImages: [
				{
					sId: \'upshrink_ic\',
					altExpanded: ', JavaScriptEscape($txt['hide_infocenter']), ',
					altCollapsed: ', JavaScriptEscape($txt['show_infocenter']), '
				}
			],
			aSwapLinks: [
				{
					sId: \'upshrink_link\',
					msgExpanded: ', JavaScriptEscape(sprintf($txt['info_center_title'], $context['forum_name_html_safe'])), ',
					msgCollapsed: ', JavaScriptEscape(sprintf($txt['info_center_title'], $context['forum_name_html_safe'])), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header_ic\',
				sSessionId: smf_session_id,
				sSessionVar: smf_session_var,
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrinkIC\'
			}
		});
	</script>';
}

/**
 * The recent posts section of the info center
 */
function template_ic_block_recent()
{
	global $context, $scripturl, $settings, $txt;

	// This is the "Recent Posts" bar.
	echo '
		<div class="info_block_icon">
			<a href="', $scripturl, '?action=recent"><span class="main_icons recent_posts"></span></a>
			<span>', $txt['recent_posts'], '</span>
		</div>
		<div class="info_block_information">
			<div id="recent_posts_content">';

	// Only show one post.
	if ($settings['number_recent_posts'] == 1)
	{
		// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
		echo '
				<p id="infocenter_onepost" class="inline">
					<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a> ', sprintf($txt['is_recent_updated'], '&quot;' . $context['latest_post']['link'] . '&quot;'), ' (', $context['latest_post']['time'], ')<br>
				</p>';
	}
	// Show lots of posts.
	elseif (!empty($context['latest_posts']))
	{
		echo '
				<table class="table_grid" id="ic_recentposts">
					<tr class="windowbg">
						<th class="recentpost">', $txt['message'], '</th>
						<th class="recentposter">', $txt['author'], '</th>
						<th class="recentboard">', $txt['board'], '</th>
						<th class="recenttime">', $txt['date'], '</th>
					</tr>';

		/* Each post in latest_posts has:
			board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
			subject, short_subject (shortened with...), time, link, and href. */
		foreach ($context['latest_posts'] as $post)
			echo '
					<tr class="windowbg">
						<td class="recentpost">', $post['link'], '</td>
						<td class="recentposter">
							', (!empty($post['poster']['avatar']) && !empty($post['poster']['avatar']) ? themecustoms_avatar($post['poster']['avatar']['href'], $post['poster']['id']) : ''), '
							<span>', $post['poster']['link'], '</span>
						</td>
						<td class="recentboard">', $post['board']['link'], '</td>
						<td class="recenttime">', $post['time'], '</td>
					</tr>';
		echo '
				</table>';
	}
	echo '
			</div><!-- #recent_posts_content -->
		</div>';
}

/**
 * The calendar section of the info center
 */
function template_ic_block_calendar()
{
	global $context, $scripturl, $txt;

	// Show information about events, birthdays, and holidays on the calendar.
	echo '
		<div class="info_block_icon">
			<a href="', $scripturl, '?action=calendar' . '"><span class="main_icons calendar"></span> ', '</a>
			<span>', $txt['calendar'], '</span>
		</div>
		<div class="info_block_information">';

	// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P
	if (!empty($context['calendar_holidays']))
		echo '
			<p class="inline holiday">
				<span>', $txt['calendar_prompt'], '</span> ', implode(', ', $context['calendar_holidays']), '
			</p>';

	// People's birthdays. Like mine. And yours, I guess. Kidding.
	if (!empty($context['calendar_birthdays']))
	{
		echo '
			<p class="inline">
				<span class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span>';

		// Each member in calendar_birthdays has: id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?)
		foreach ($context['calendar_birthdays'] as $member)
			echo '
				<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong class="fix_rtl_names">' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '' : ', ';

		echo '
			</p>';
	}

	// Events like community get-togethers.
	if (!empty($context['calendar_events']))
	{
		echo '
			<p class="inline">
				<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';

		// Each event in calendar_events should have:
		//		title, href, is_last, can_edit (are they allowed?), modify_href, and is_today.
		foreach ($context['calendar_events'] as $event)
			echo '
				', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><span class="main_icons calendar_modify"></span></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br>' : ', ';
		echo '
			</p>';
	}

	echo '
		</div>';
}

/**
 * The stats section of the info center
 */
function template_ic_block_stats()
{
	global $scripturl, $txt, $context, $settings;

	// Show statistical style information...
	echo '
		<div class="info_block_icon">
			', $context['show_stats'] ? '<a href="' . $scripturl . '?action=stats" title="' . $txt['more_stats'] . '">' : '', '<span class="main_icons stats"></span> ', $context['show_stats'] ? '</a>' : '', '
			<span>', $txt['forum_stats'], '</span>
		</div>
		<div class="info_block_information">
			<p class="inline">
				', $context['common_stats']['boardindex_total_posts'], '', !empty($settings['show_latest_member']) ? ' - ' . $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : '', '<br>
				', (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  (' . $context['latest_post']['time'] . ')<br>' : ''), '
				<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>
			</p>
		</div>';
}

/**
 * The who's online section of the info center
 */
function template_ic_block_online()
{
	global $context, $scripturl, $txt, $modSettings, $settings;

	// "Users online" - in order of activity.
	echo '
		<div class="info_block_icon">
			', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<span class="main_icons people"></span>', $context['show_who'] ? '</a>' : '', '
			<span>', $txt['online_users'], '</span>
		</div>
		<div class="info_block_information">
			<p class="inline">
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', '<strong>', $txt['online'], ': </strong>', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ', comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();

	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);

	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);

	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . ($context['num_spiders'] == 1 ? $txt['hidden'] : $txt['hidden_s']);

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '

				&nbsp;-&nbsp;', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>&nbsp;-&nbsp;
				', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')<br>';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				<span class="smalltext d-block">', sprintf($txt['users_active'], $modSettings['lastActive']), ':</span>';

		// Show the regular list
		if (empty($settings['st_enable_avatars_online'])) {
			echo implode(',&nbsp;', $context['list_users_online']);
		} else {
			echo '
			<div class="online_list">';
			foreach ($context['list_users_online'] as $user) {
				echo $user;
			}
			echo '
			</div>';
		}

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<span class="membergroups">' . implode(', ', $context['membergroups']) . '</span>';
	}

	echo '
			</p>
		</div>';
}

/**
 * Outputs the board lastposts for a standard board or a redirect.
 * When on a mobile device, this may be hidden if no last post exists.
 *
 * @param array $board Current board information.
 */
function themecustoms_bi_board_lastpost(array $board) : void
{
	global $settings, $txt;

	// Will still add the class, in case the design depends on it.
	if (!empty($board['last_post']['id']))
		echo '
			', !empty($settings['st_enable_avatars_boards']) && !empty($board['last_post']['member']['avatar']) ? themecustoms_avatar($board['last_post']['member']['avatar']['href'], $board['last_post']['member']['id']) : '', '
			<p>
				<span class="post">', sprintf($txt['post_by_member'], $board['last_post']['link'], $board['last_post']['member']['link']), '</span>
				<span class="time">', timeformat($board['last_post']['timestamp']), '</span>
			</p>';
}

?>