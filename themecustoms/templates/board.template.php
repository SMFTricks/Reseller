<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

/**
 * Outputs the board icon for a standard board.
 *
 * @param array $board Current board information.
 */
function template_bi_board_icon(array $board) : void
{
	global $context, $scripturl;

	if (function_exists('themecustoms_bi_' . $board['type'] . '_icon'))
	{
		call_user_func('themecustoms_bi_' . $board['type'] . '_icon', $board);

		return;
	}

	echo '
			<a href="', ($context['user']['is_guest'] || $board['type'] == 'redirect' ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '" class="board_', $board['board_class'], '"', !empty($board['board_tooltip']) ? ' title="' . $board['board_tooltip'] . '"' : '', '></a>';
}

 /**
 * Outputs the board info for a standard board or redirect.
 *
 * @param array $board Current board information.
 */
function template_bi_board_info(array $board) : void
{
	global $context, $scripturl, $txt;

	if (function_exists('themecustoms_bi_' . $board['type'] . '_info'))
	{
		call_user_func('themecustoms_bi_' . $board['type'] . '_info', $board);

		return;
	}

	echo '
			<a class="subject" href="', $board['href'], '" id="b', $board['id'], '">
				', $board['name'], '
			</a>';

	// Has it outstanding posts for approval?
	if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
		echo '
			<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link amt">!</a>';

	echo '
			<div class="board_description">', $board['description'], '</div>';

	// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
	if (!empty($board['moderators']) || !empty($board['moderator_groups']))
		echo '
			<p class="moderators">
				', count($board['link_moderators']) === 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '
			</p>';
}

/**
 * Outputs the board stats for a standard board.
 *
 * @param array $board Current board information.
 */
function template_bi_board_stats(array $board) : void
{
	global $txt;

	if (function_exists('themecustoms_bi_' . $board['type'] . '_stats')) {
		call_user_func('themecustoms_bi_' . $board['type'] . '_stats', $board);

		return;
	}

	echo '
			<p>
				', ($board['type'] != 'redirect' ? '
					<strong class="posts">' . comma_format($board['posts']) . '</strong> ' . $txt['posts'] . '<br>
					<strong class="topics">' . comma_format($board['topics']) . '</strong> ' . $txt['board_topics'] : '
					<strong class="redirects">' . comma_format($board['posts']) . '</strong> ' . $txt['redirects']), '
			</p>';
}

/**
 * Outputs the board lastposts for a standard board or a redirect.
 * When on a mobile device, this may be hidden if no last post exists.
 *
 * @param array $board Current board information.
 */
function template_bi_board_lastpost(array $board) : void
{
	global $settings, $txt;

	if (function_exists('themecustoms_bi_' . $board['type'] . '_lastpost')) {
		call_user_func('themecustoms_bi_' . $board['type'] . '_lastpost', $board);

		return;
	}

	// Will still add the class, in case the design depends on it.
	if (!empty($board['last_post']['id']))
		echo '
			', !empty($settings['st_enable_avatars_boards']) && !empty($board['last_post']['member']['avatar']) ? themecustoms_avatar($board['last_post']['member']['avatar']['href'], $board['last_post']['member']['id']) : '', '
			<p>
				<span class="last_post">
					', sprintf($txt['last_post_topic'], $board['last_post']['link'], $board['last_post']['member']['link']), '
				</span>
				<span class="time">', themecustoms_icon('far fa-clock'), ' ', timeformat($board['last_post']['timestamp']), '</span>
			</p>';
}

/**
 * Outputs the board children for a standard board.
 *
 * @param array $board Current board information.
 * @param string $style Which default style is being applied
 */
function template_bi_board_children(array $board, string $style = 'normal') : void
{
	global $txt, $scripturl, $context;

	if (empty($board['children']))
		return;

	if (function_exists('themecustoms_bi_' . $board['type'] . '_children'))
	{
		call_user_func('themecustoms_bi_' . $board['type'] . '_children', $board);

		return;
	}

	// Sort the links into an array with new boards bold so it can be imploded.
	$children = array();

	/* 
		Each child in each board's children has:
		id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post.
	*/
	foreach ($board['children'] as $child)
	{
		if (!$child['is_redirect'])
			$child['link'] = '' . ($child['new'] ? '<a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')" class="new_posts">' . $txt['new'] . '</a> ' : '') . '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="board_new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . '</a>';
		else
			$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . ' - ' . $child['short_description'] . '">' . $child['name'] . '</a>';

		// Has it posts awaiting approval?
		if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
			$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link amt">!</a>';

		$children[] = $child['new'] ? '<span class="strong">' . $child['link'] . '</span>' : '<span>' . $child['link'] . '</span>';
	}

	// Template style
	$func = 'board_children_' . $style;
	echo '
		<div id="board_', $board['id'], '_children" class="children board_children">
			', $func($board, $children), '
		</div>';
}

/**
 * Child boards, standard and default view
 * 
 * @param array $board Current board information.
 * @param array $children The subboards
 */
function board_children_normal(array $board, array $children) : void
{
	global $txt;

	echo '
	<strong id="child_list_', $board['id'], '">', $txt['sub_boards'], '</strong>
	<p id="child_boards_', $board['id'], '" class="sub_boards">
		', implode($children), '
	</p>';
}

/**
 * 
 * Child boards using the 'Details' tag.
 * 
 * @param array $board Current board information.
 * @param array $children The subboards
 */
function board_children_details(array $board, array $children) : void
{
	global $txt;

	echo '
	<details class="child_container">
		<summary>
			<span class="title_bar">
				<strong id="child_list_', $board['id'], '" class="titlebg">
					', $txt['sub_boards'], themecustoms_icon('fa fa-turn-down'), '
				</strong>
			</span>
		</summary>
		<ul id="child_boards_', $board['id'], '" class="sub_boards">';

	// The child boards
	foreach ($children as $child)
	{
		echo '
			<li>', $child, '</li>';
	}

	echo '
		</ul>
	</details>
	<span class="divider"></span>';
}

/**
 * 
 * Child boards using the 'Dropdown' tag.
 * 
 * @param array $board Current board information.
 * @param array $children The subboards
 */
function board_children_dropdown(array $board, array $children) : void
{
	global $txt;

	echo '
	<div class="child_container">
		<div class="dropdown">
			<button class="button dropdown-toggle" type="button" id="subboards_dropdownb_', $board['id'], '" data-bs-toggle="dropdown" aria-expanded="false">
				', $txt['sub_boards'], '
			</button>
			<div class="dropdown-menu dropdown-menu-end" aria-labelledby="subboards_dropdownb_', $board['id'], '">';

	// The child boards
	foreach ($children as $child)
	{
		echo '
				<span class="dropdown-item">', $child, '</span>';
	}

	echo '
			</div>
		</div>
	</div>
	<span class="divider"></span>';
}