<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2024 Simple Machines and individual contributors
 * @license https://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1.4
 */

/**
 * Initialize Theme Customs
 */
function template_init() : void
{
	// Initialize theme customs
	template_customs_init();
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $scripturl, $txt, $modSettings, $settings;

	// Show right to left, the language code, and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', !empty($txt['lang_locale']) ? ' lang="' . str_replace("_", "-", substr($txt['lang_locale'], 0, strcspn($txt['lang_locale'], "."))) . '"' : '', $settings['themecustoms_html_attributes'] ?? '', '>
<head>
	<meta charset="', $context['character_set'], '">';

	template_css();
	template_javascript();

	echo '
	<title>', $context['page_title_html_safe'], '</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">';

	// Content related meta tags, like description, keywords, Open Graph stuff, etc...
	foreach ($context['meta_tags'] as $meta_tag)
	{
		echo '
	<meta';

		foreach ($meta_tag as $meta_key => $meta_value)
			echo ' ', $meta_key, '="', $meta_value, '"';

		echo '>';
	}

	// Theme color (mobile and tabs)
	echo '
	<meta name="theme-color" content="', $settings['st_site_color'] ?? $settings['theme_default_color'], '">';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex">';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '">';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help">
	<link rel="contents" href="', $scripturl, '">', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search">' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?action=.xml;type=rss2', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">
	<link rel="alternate" type="application/atom+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?action=.xml;type=atom', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '">';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '">';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0">';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? $context['current_action'] : (!empty($context['current_board']) ?
		'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')), !empty($context['current_board']) ? ' board_' . $context['current_board'] : '', '">
<div id="footerfix">';
}

/**
 * The upper part of the main template layer. This is the stuff that shows above the main forum content.
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance;

	// Show the menu here
	template_menu();

	// Header
	echo '
	<header id="header">
		<div class="content-wrapper">
			<h1 class="forumtitle">
				<a id="top" href="', $scripturl, '">
					<img src="', empty($context['header_logo_url_html_safe']) ? $settings['images_url'] . '/theme/logo.png' : $context['header_logo_url_html_safe'],  '" alt="' . $context['forum_name_html_safe'] . '">', '
				</a>
				', empty($settings['site_slogan']) ? '' : '<span id="siteslogan">' . $settings['site_slogan'] . '</span>', '
			</h1>';

	//User Panel
	// If the user is logged in, display some things that might be useful.
	echo '
		<div class="user_panel">
			<ul id="top_info">';

				// New topic button
				themecustoms_quicknewtopic();

	// If the user is logged in, display some things that might be useful.
	if ($context['user']['is_logged'])
	{
		// PMs if we're doing them
		if ($context['allow_pm'])
			echo '
				<li>
					<a href="', $scripturl, '?action=pm"', !empty($context['self_pm']) ? ' class="active"' : '', ' id="pm_menu_top" title="', $txt['pm_short'], '">
						<span class="main_icons inbox"></span>
						<span class="text-label">', $txt['pm_short'], '</span>', !empty($context['user']['unread_messages']) ? '
						<span class="amt">' . $context['user']['unread_messages'] . '</span>' : '', '
					</a>
					<div id="pm_menu" class="top_menu scroll"></div>
				</li>';

		// Alerts
		echo '
				<li>
					<a href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '"', !empty($context['self_alerts']) ? ' class="active"' : '', ' id="alerts_menu_top" title="', $txt['alerts'], '">
						<span class="main_icons alerts"></span>
						<span class="text-label">', $txt['alerts'], '</span>', !empty($context['user']['alerts']) ? '
						<span class="amt">' . $context['user']['alerts'] . '</span>' : '', '
					</a>
					<div id="alerts_menu" class="top_menu scroll"></div>
				</li>';

		// Unread and Unread Replies
		echo '
				<li>
					<a href="', $scripturl, '?action=unread"', $context['current_action'] == 'unread' ? ' class="active"' : '',' title="', $txt['unread_since_visit'],'">
						<span class="main_icons unread"></span>
						<span class="text-label">', $txt['view_unread_category'], '</span>
					</a>
				</li>
				<li>
					<a href="', $scripturl, '?action=unreadreplies"', $context['current_action'] == 'unreadreplies' ? ' class="active"' : '',' title="', $txt['show_unread_replies'],'">
						<span class="main_icons replies"></span>
						<span class="text-label">', $txt['unread_replies'], '</span>
					</a>
				</li>';
		

		// The user's menu
		echo '
				<li>
					<a href="', $scripturl, '?action=profile"', !empty($context['self_profile']) ? ' class="active"' : '', ' id="profile_menu_top" title="', $txt['profile'], '">
						', $context['user']['avatar']['image'], '
						<span class="text-label">', $context['user']['name'], '</span>
					</a>
					<div id="profile_menu" class="top_menu"></div>
				</li>';

		// A logout button for people without JavaScript.
		if (empty($settings['login_main_menu'])) {
			echo '
				<li id="nojs_logout">
					<a href="', $scripturl, '?action=logout;', $context['session_var'], '=', $context['session_id'], '" title="', $txt['logout'], '">
						<span class="main_icons logout"></span>
						<span class="text-label">', $txt['logout'], '</span>
					</a>
					<script>document.getElementById("nojs_logout").style.display = "none";</script>
				</li>';
		}
	}
	// Otherwise they're a guest. Ask them to either register or login.
	elseif (empty($maintenance)) {
		// Some people like to do things the old-fashioned way.
		if (!empty($settings['login_main_menu'])) {
			echo '
				<li class="welcome">
					', sprintf($txt[$context['can_register'] ? 'welcome_guest_register' : 'welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ', \'login\');', $scripturl . '?action=signup'), '
				</li>';
		} else {
			echo '
				<li class="button_login">
					<a href="', $scripturl, '?action=login" class="', $context['current_action'] == 'login' ? 'active' : '','" onclick="return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ', \'login\');" title="', $txt['login'],'">
						<span class="main_icons login"></span>
						<span class="text-label">', $txt['login'], '</span>
					</a>
				</li>';

			if ($context['can_register'])
				echo '
				<li class="button_signup">
					<a href="', $scripturl, '?action=signup" class="', $context['current_action'] == 'signup' ? 'active' : 'open','" title="', $txt['register'],'">
						<span class="main_icons signup"></span>
						<span class="text-label">', $txt['register'], '</span>
					</a>
				</li>';
		}
	}
	else
		// In maintenance mode, only login is allowed and don't show OverlayDiv
		echo '
				<li>', sprintf($txt['welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return true;'), '</li>>';

	echo '
			</ul>';

		echo '
			</div>
		</div><!-- .content-wrapper -->
	</header><!-- header -->';

	theme_linktree();

	echo '
	<div class="content-wrapper">';

		!function_exists('themecustoms_carousel') ? '' : themecustoms_carousel();

	// The main content should go here.
	echo '
		<div id="content_section">
			<div id="main_content_section">';
}

/**
 * The stuff shown immediately below the main content, including the footer
 */
function template_body_below()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
			</div><!-- #main_content_section -->
		</div><!-- #content_section -->
	</div><!-- .content-wrapper -->
</div><!-- #footerfix -->';

	// Show the footer with copyright, terms and help links.
	echo '
	<footer id="footer">
		<div class="content-wrapper">';

	// Socials
	themecustoms_socials();

	// There is now a global "Go to top" link at the right.
	echo '
			<ul class="copyright">
				<li class="smf_copyright">', theme_copyright(), '</li>
			</ul>
			<ul>
				<li class="helplinks">
					<a href="', $scripturl, '?action=help">', $txt['help'], ' <i class="fa-solid fa-circle-question"></i></a>', (!empty($modSettings['requireAgreement'])) ? '
					<a href="' . $scripturl . '?action=agreement">' . $txt['terms_and_rules'] . ' <i class="fa-solid fa-list-ul"></i></a>' : '', '
					<a href="#header">', $txt['go_up'], ' &#9650;</a>
				</li>
			</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';

	echo '
		</div>
	</footer><!-- #footer -->';
}

/**
 * This shows any deferred JavaScript and closes out the HTML
 */
function template_html_below()
{
	// Load in any javascipt that could be deferred to the end of the page
	template_javascript(true);

	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 *
 * @param bool $force_show Whether to force showing it even if settings say otherwise
 */
function theme_linktree($force_show = false)
{
	global $context, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
		<nav aria-label="', $txt['st_breadcrumb'], '" class="navigate_section">
			<div class="content-wrapper">
				<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
					<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the first one.
		// Better here. Always points to the next level when the linktree breaks to a second line.
		// Picked a better looking HTML entity, and added support for RTL plus a span for styling.
		if ($link_num != 0)
			echo '
						<span class="dividers"><i class="fa-solid fa-angle-', $context['right_to_left'] ? 'left' : 'right', '"></i></span>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		if (isset($tree['url']))
			echo '
						<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
		else
			echo '
						<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
					</li>';
	}

	echo '
				</ul>
			</div>
		</nav><!-- .navigate_section -->';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context, $scripturl, $txt;

	echo '
	<nav id="mainNav" class="navbar navbar-expand-lg" aria-label="', $txt['mobile_user_menu'], '">
		<div class="content-wrapper">
			<span class="d-lg-none">
				<a class="navbar-brand" href="', $scripturl, '">', $txt['mobile_user_menu'], '</a>
			</span>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="', $txt['st_menu_toggle'], '">
				<i class="fa-solid fa-bars"></i>
			</button>
			<div class="collapse navbar-collapse" id="mainNavbar">
				<ul class="navbar-nav">';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
					<li class="nav-item button_', $act, !empty($button['sub_buttons']) ? ' dropdown' : '', '">
						<a class="nav-link', $button['active_button'] ? ' active' : '', !empty($button['sub_buttons']) ? ' dropdown-toggle' : '', '"', $button['active_button'] ? ' aria-current="page"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', isset($button['onclick']) ? ' onclick="' . $button['onclick'] . '"' : '', !empty($button['sub_buttons']) ? ' role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"' : '', '>
							', $button['icon'], '
							<span class="textmenu">
								', $button['title'], '
							</span>
							', !empty($button['amt']) ? ' <span class="amt">' . $button['amt'] . '</span>' : '', '
						</a>';

		// 2nd level menus
		if (!empty($button['sub_buttons']))
		{
			echo '
						<ul class="dropdown-menu dropdown-menu-end">';

			foreach ($button['sub_buttons'] as $childbutton)
			{
					echo '
							<li class="dropend">
								<a class="dropdown-item', !empty($childbutton['sub_buttons']) ? ' dropdown-toggle' : '', '" href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', isset($childbutton['onclick']) ? ' onclick="' . $childbutton['onclick'] . '"' : '', !empty($childbutton['sub_buttons']) ? ' role="button" data-bs-toggle="dropdown" aria-expanded="false"' : '', '>
									<span class="textmenu">
										', $childbutton['title'], '
									</span>
									', !empty($childbutton['amt']) ? ' <span class="amt">' . $childbutton['amt'] . '</span>' : '', '
								</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
								<ul class="dropdown-menu dropdown-menu-end">';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
									<li>
										<a class="dropdown-item" href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', isset($grandchildbutton['onclick']) ? ' onclick="' . $grandchildbutton['onclick'] . '"' : '', '>
											<span class="textmenu">
												', $grandchildbutton['title'], '
											</span>
											', !empty($grandchildbutton['amt']) ? ' <span class="amt">' . $grandchildbutton['amt'] . '</span>' : '', '
										</a>
									</li>';

					echo '
								</ul>';
				}

				echo '
							</li>';
			}
			echo '
						</ul>';
		}
		echo '
					</li>';
	}

	echo '
				</ul><!-- .navbar-nav -->
			</div>
		</div>
	</nav>';
}

/**
 * Generate a strip of buttons.
 *
 * @param array $button_strip An array with info for displaying the strip
 * @param string $direction The direction
 * @param array $strip_options Options for the button strip
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	$button_count = 0;
	foreach ($button_strip as $key => $value) {
		$button_count++;
		// As of 2.1, the 'test' for each button happens while the array is being generated. The extra 'test' check here is deprecated but kept for backward compatibility (update your mods, folks!)
		if (!isset($value['test']) || !empty($context[$value['test']])) {
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
			<li' . (isset($value['content']) ? ' class="' . $value['class'] . '"' : '') . '>';

			if (isset($value['content'])) {
				$button .= $value['content'];
			} else {
				$button .= (empty($value['url']) ? '<button' : '<a');
				$button .= ' class="button_item_'. $key . (!empty($value['sub_buttons']) ? ' sub_buttons' : '') . (!empty($value['active']) ? ' active' : '') . (isset($value['class']) ? ' ' . $value['class'] : '') . '"' . (!empty($value['url']) ? ' href="' . $value['url'] . '"' : '') . (isset($value['custom']) ? ' ' . $value['custom'] : '') . ' aria-label="' . $txt[$value['text']] . '" title="' . $txt[$value['text']] . '">
					' . (!empty($value['icon']) ? '<span class="main_icons ' . $value['icon'] . '"></span>' : '') . '
					<span class="text-label">' . $txt[$value['text']] . '</span>';
				$button .= '</' . (empty($value['url']) ? 'button' : 'a') . '>';
			}

			if (!empty($value['sub_buttons']) && !empty($value['options'])) {
				$button .= '
				<button aria-label="' . $txt['st_options'] . '" class="reset' . (!empty($value['active']) ? ' active ' : '') . ' dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>';
			}

			if (!empty($value['sub_buttons'])) {
				$button .= '
				<div class="top_menu" data-button-dropdown="' . $key . '_dropdown">
					<div class="viewport dropmenu">
						<div class="title_bar">
							<h4 class="titlebg">' . $txt[$value['text']] . '</h4>
							<button class="reset">
								<span class="main_icons close"></span>
							</button>
						</div>
						<ul class="overview">';
				foreach ($value['sub_buttons'] as $element) {
					if (isset($element['test']) && empty($context[$element['test']]))
						continue;

					$button .= '
							<li>
								<a href="' . $element['url'] . '"'. (!empty($element['active']) ? ' class="active"' : '') . '>
									'. (!empty($element['icon']) ? '<span class="main_icons ' . $element['icon'] . '"></span>' : '') . '
									<span class="text-label">' . ($element['label'] ?? $txt[$element['text']]) . '</span>';

					if (isset($element['text']) && isset($txt[$element['text'] . '_desc']))
						$button .= '
									<span>' . $txt[$element['text'] . '_desc'] . '</span>';

						$button .= '
								</a>
							</li>';
				}
				$button .= '
						</ul><!-- .overview -->
					</div><!-- .viewport -->
				</div><!-- .top_menu -->';
			}

			$button .= '
			</li>';

			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<ul class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
			', implode('', $buttons), '
		</ul>';
}

/**
 * Generate a list of quickbuttons.
 *
 * @param array $list_items An array with info for displaying the strip
 * @param string $list_class Used for integration hooks and as a class name
 * @param string $output_method The output method. If 'echo', simply displays the buttons, otherwise returns the HTML for them
 * @return void|string Returns nothing unless output_method is something other than 'echo'
 */
function template_quickbuttons($list_items, $list_class = null, $output_method = 'echo')
{
	global $txt;

	// Enable manipulation with hooks
	if (!empty($list_class))
		call_integration_hook('integrate_' . $list_class . '_quickbuttons', array(&$list_items));

	// Make sure the list has at least one shown item
	foreach ($list_items as $key => $li)
	{
		// Is there a sublist, and does it have any shown items
		if ($key == 'more')
		{
			foreach ($li as $subkey => $subli)
				if (isset($subli['show']) && !$subli['show'])
					unset($list_items[$key][$subkey]);

			if (empty($list_items[$key]))
				unset($list_items[$key]);
		}
		// A normal list item
		elseif (isset($li['show']) && !$li['show'])
			unset($list_items[$key]);
	}

	// Now check if there are any items left
	if (empty($list_items))
		return;

	// Print the quickbuttons
	$output = '
		<ul class="quickbuttons' . (!empty($list_class) ? ' quickbuttons_' . $list_class : '') . '">';

	// This is used for a list item or a sublist item
	$list_item_format = function($li)
	{
		$html = '
			<li' . (!empty($li['class']) ? ' class="' . $li['class'] . '"' : '') . (!empty($li['id']) ? ' id="' . $li['id'] . '"' : '') . (!empty($li['custom']) ? ' ' . $li['custom'] : '') . '>';

		if (isset($li['content']))
			$html .= $li['content'];
		else
			$html .= '
				<a href="' . (!empty($li['href']) ? $li['href'] : 'javascript:void(0);') . '"' . (!empty($li['javascript']) ? ' ' . $li['javascript'] : '') . '>
					' . (!empty($li['icon']) ? '<span class="main_icons ' . $li['icon'] . '"></span>' : '') . (!empty($li['label']) ? '<span class="text-label">' . $li['label'] . '</span>' : '') . '
				</a>';

		$html .= '
			</li>';

		return $html;
	};

	foreach ($list_items as $key => $li)
	{
		// Handle the sublist
		if ($key == 'more')
		{
			$output .= '
			<li class="quickoptions">
				<a href="javascript:void(0);">
					<span class="main_icons more"></span>
					<span class="text-label">' . $txt['post_options'] . '</span>
				</a>
				<ul>';

			foreach ($li as $subli)
				$output .= $list_item_format($subli);

			$output .= '
				</ul>
			</li>';
		}
		// Ordinary list item
		else
			$output .= $list_item_format($li);
	}

	$output .= '
		</ul><!-- .quickbuttons -->';

	// There are a few spots where the result needs to be returned
	if ($output_method == 'echo')
		echo $output;
	else
		return $output;
}

/**
 * The upper part of the maintenance warning box
 */
function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintenance'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

/**
 * The lower part of the maintenance warning box.
 */
function template_maint_warning_below()
{

}