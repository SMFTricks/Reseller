<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2023, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Integrations;

use ThemeCustoms\Config;

class Buttons
{
	/**
	 * Notify Icon
	 */
	private string $notifyIcon = 'notify';

	/**
	 * Insert or change quick buttons
	 * @param array $output It receives the message information
	 */
	public function quick(array &$output) : void
	{
		global $context, $scripturl, $txt, $modSettings;
		
		// Modify button
		if (isset($output['quickbuttons']['more']['modify']) && !empty($output['quickbuttons']['more']['modify']))
			$output['quickbuttons']['more']['modify']['icon'] = 'modify_button';

		// Like/Unlike button
		// It doesn't make sense to me that you'd like their post if it's ignored, even if you decide to see it.
		if (!$output['is_ignored'] && !empty($modSettings['enable_likes']) && !empty(Config::$current->quickLikes) && (!empty($output['likes']['count']) || $output['likes']['can_like']))
		{
			$output['quickbuttons'] = array_merge([
				'likes' => [
					'label' => $output['likes']['can_like'] ? ($output['likes']['you'] ? $txt['unlike'] : $txt['like']) : '',
					'icon' => $output['likes']['you'] ? 'unlike' : 'like',
					'class' => 'post_like_button' . (!empty($output['likes']['count']) && !$output['likes']['can_like'] ? ' disabled' : ''),
					'id' => 'msg_' . $output['id'] . '_quicklikes',
					'href' => $output['likes']['can_like'] ? ($scripturl . '?action=likes;quickbuttonlike;ltype=msg;sa=like;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id']) : '',
					'show' => $output['likes']['can_like'] || !empty($modSettings['enable_likes']),
					'extra_content' => (!empty($output['likes']['count']) ? '
						<span class="amt">
							<a class="buttonlike_count" href="' . $scripturl . '?action=likes;sa=view;ltype=msg;js=1;like=' . $output['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><em style="display: none;">'. $txt['likes'] . '</em>' . $output['likes']['count'] . '</a>
						</span>' : ''),
				],
			], $output['quickbuttons']);
		}
	}

	/**
	 * Insert or change normal buttons
	 * @param array $buttons It receives the regular buttons for the respetive action.
	 */
	public function display(array &$buttons) : void
	{
		global $context, $txt;

		// Notifiy button
		if (isset($buttons['notify']) && !empty($buttons['notify']['sub_buttons']))
		{
			// Simplify the text
			$buttons['notify']['text'] = ($context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'] > 1 ? 'unnotify' : 'notify');

			// Sub-Buttons
			foreach ($buttons['notify']['sub_buttons'] as $key => $sub_notify)
			{
				// Add the status for the button
				$buttons['notify']['sub_buttons'][$key]['button_status'] = ($sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_1') || $sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_0') ? $txt['notify'] : $txt['unnotify']);

				// Add active status
				if ($sub_notify['text'] === 'notify_' . (!empty($context['current_topic']) ? 'topic_' : 'board_') . $context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'])
					$buttons['notify']['sub_buttons'][$key]['active'] = true;
			}
		}

		// When using stylesCompat only...
		if (Config::$current->stylesCompat) {

			// Mark Read Button
			if (isset($buttons['mark_unread'])) {
				$buttons['mark_unread']['icon'] = 'unread_button';
			}

			// Notify Button
			if (isset($buttons['notify'])) {
				$buttons['notify']['text']	= 'notify';
				$buttons['notify']['icon'] = $this->notifyIcon();

				// Icon and status
				foreach ($buttons['notify']['sub_buttons'] as $index => $sub_button) {
					$buttons['notify']['sub_buttons'][$index]['icon'] = $this->notifyIcon($index);
					$buttons['notify']['sub_buttons'][$index]['active'] = $index === $context['topic_notification_mode'];
				}
			}

			// Add Poll Button
			if (isset($buttons['add_poll'])) {
				$buttons['add_poll']['icon'] = 'poll';
			}

			// Print Button
			if (isset($buttons['print'])) {
				$buttons['print']['icon'] = 'print';
			}

			// Reply Button
			if (isset($buttons['reply'])) {
				$buttons['reply']['icon'] = 'reply_button';
				$buttons['reply']['class'] = 'w-label';
				$buttons['reply']['options'] = true;

				foreach ($buttons as $key => $button) {
					if ($key === 'reply' || $key === 'notify' || $key === 'mark_unread') {
						continue;
					}
	
					$buttons['reply']['sub_buttons'][] = $button;
					unset($buttons[$key]);
				}
			}
		}
	}

	/**
	 * Insert or change the messageindex buttons
	 * @param array $buttons It receives the message index buttons
	 */
	public function message(array &$buttons) : void
	{
		global $context, $options, $txt;

		// Notifiy button
		if (isset($buttons['notify']) && !empty($buttons['notify']['sub_buttons']))
		{
			// Simplify the text
			$buttons['notify']['text'] = ($context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'] > 1 ? 'unnotify' : 'notify');

			// Sub-Buttons
			foreach ($buttons['notify']['sub_buttons'] as $key => $sub_notify)
			{
				// Add the status for the button
				$buttons['notify']['sub_buttons'][$key]['button_status'] = ($sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_1') || $sub_notify['text'] === ('notify_' . (!empty($context['current_topic']) ? 'topic' : 'board') . '_0') ? $txt['notify'] : $txt['unnotify']);

				// Add active status
				if ($sub_notify['text'] === 'notify_' . (!empty($context['current_topic']) ? 'topic_' : 'board_') . $context[(!empty($context['current_topic']) ? 'topic' : 'board') . '_notification_mode'])
					$buttons['notify']['sub_buttons'][$key]['active'] = true;
			}
		}

		// When using stylesCompat only...
		if (Config::$current->stylesCompat) {

			// Mark Read Button
			if (isset($buttons['markread'])) {
				$buttons['markread']['icon'] = 'markread';
			}

			// New Poll Button
			if (isset($buttons['post_poll'])) {
				$buttons['post_poll']['icon'] = 'poll';
			}

			// Notify Button
			if (isset($buttons['notify'])) {
				$buttons['notify']['text']	= 'notify';
				$buttons['notify']['icon'] = $this->notifyIcon();

				// Icon and status
				foreach ($buttons['notify']['sub_buttons'] as $index => $sub_button) {
					$buttons['notify']['sub_buttons'][$index]['icon'] = $this->notifyIcon($index+1);
					$buttons['notify']['sub_buttons'][$index]['active'] = $index+1 === $context['board_notification_mode'];
				}
			}

			// New Topic Button
			if (isset($buttons['new_topic'])) {
				$buttons['new_topic']['icon'] = 'newtopic';
				$buttons['new_topic']['class'] = 'w-label';
				$buttons['new_topic']['options'] = true;

				foreach ($buttons as $key => $button) {
					if ($key === 'new_topic' || $key === 'notify' || $key === 'markread') {
						continue;
					}
	
					$buttons['new_topic']['sub_buttons'][] = $button;
					unset($buttons[$key]);
				}
			}

			// Add a sorting button
			if (!empty($context['topics_headers'])) {
				$buttons['topics_sort'] = [
					'lang' => true,
					'icon' => 'sort',
					'text' => 'sort_by',
					'class' => empty($buttons) ? 'w-label' : '',
					'url' => '',
					'sub_buttons' => $this->sortMessage()
				];

			}
			// Add a checkbox to select all
			if (!empty($context['can_quick_mod']) && $options['display_quick_mod'] === 1) {
				$buttons['select_all'] = [
					'class' => 'inline_mod_check',
					'content' => '<input type="checkbox" onclick="invertAll(this, document.getElementById(\'quickModForm\'), \'topics[]\');">',
					'show' => !empty($context['can_quick_mod']) && $options['display_quick_mod'] === 1
				];
			}
		}
	}

	/**
	 * Setup the Notification Iccon
	 */
	private function notifyIcon(int $icon = -1) : string
	{
		global $context;

		switch ($icon !== -1 ? $icon : ($context['current_topic'] ? $context['topic_notification_mode'] : $context['board_notification_mode']))
		{
			case 0:
				$this->notifyIcon = 'notify regular';
				break;
			case 1:
				$this->notifyIcon = 'notify-off';
				break;
			case 2:
				$this->notifyIcon = 'notify';
				break;
			default:
				$this->notifyIcon = 'notify-email';
				break;
		}

		return $this->notifyIcon;
	}

	/**
	 * Add a sorting button
	 */
	private function sortMessage() : array
	{
		global $context, $scripturl;

		$sorting_items = [];
		foreach ($context['topics_headers'] as $key => $header) {
			$sorting_items[] = [
				'text' => $key,
				'url' => $scripturl . '?board=' . $context['current_board'] . '.' . $context['start'] . ';sort=' . $key . ($context['sort_by'] == $key && $context['sort_direction'] == 'up' ? ';desc' : ''),
				'active' => $context['sort_by'] == $key,
				'icon' => ($context['sort_by'] == $key ? ($context['sort_direction'] == 'up' ? 'sort_up' : 'sort_down') : 'sort_down')
			];
		}

		return $sorting_items;
	}

	/**
	 * Insert or change the recent/unread buttons
	 */
	public function unread() : void
	{
		global $context, $scripturl;

		// When using stylesCompat only...
		if (Config::$current->stylesCompat) {

			// Mark Read Button
			if (isset($context['recent_buttons']['markread'])) {
				$context['recent_buttons']['markread']['icon'] = 'markread';
			}

			// Mark Select Read Button
			if (isset($context['recent_buttons']['markselectread'])) {
				$context['recent_buttons']['markselectread']['icon'] = 'markselected';
			}

			// Read All Button
			if (isset($context['recent_buttons']['readall'])) {
				$context['recent_buttons']['readall']['icon'] = 'unread';
				$context['recent_buttons']['readall']['class'] = 'w-label';
			}

			// Add a sorting button
			$context['recent_buttons']['topics_sort'] = [
				'lang' => true,
				'icon' => 'sort',
				'text' => 'sort_by',
				'class' => '',
				'url' => '',
				'sub_buttons' => [
					[
						'text' => 'subject',
						'url' => $scripturl . '?action=unread' . ($context['showing_all_topics'] ? ';all' : '') . $context['querystring_board_limits'] . ';sort=subject' . ($context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : ''),
						'active' => $context['sort_by'] == 'subject',
						'icon' => $context['sort_by'] == 'subject' ? ($context['sort_direction'] == 'up' ? 'sort_up' : 'sort_down') : 'sort_down'
					],
					[
						'text' => 'replies',
						'url' => $scripturl . '?action=unread' . ($context['showing_all_topics'] ? ';all' : '') . $context['querystring_board_limits'] . ';sort=replies' . ($context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : ''),
						'active' => $context['sort_by'] == 'replies',
						'icon' => $context['sort_by'] == 'replies' ? ($context['sort_direction'] == 'up' ? 'sort_up' : 'sort_down') : 'sort_down'
					],
					[
						'text' => 'last_post',
						'url' => $scripturl . '?action=unread' . ($context['showing_all_topics'] ? ';all' : '') . $context['querystring_board_limits'] . ';sort=last_post' . ($context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : ''),
						'active' => $context['sort_by'] == 'last_post',
						'icon' => $context['sort_by'] == 'last_post' ? ($context['sort_direction'] == 'up' ? 'sort_up' : 'sort_down') : 'sort_down'
					]
				]
			];

			// Moderation checkbox
			if ($context['showCheckboxes']) {
				$context['recent_buttons']['select_all'] = [
					'class' => 'inline_mod_check',
					'content' => '<input type="checkbox" onclick="invertAll(this, document.getElementById(\'quickModForm\'), \'topics[]\');">',
					'show' => $context['showCheckboxes']
				];
			}
		}
	}

	/**
	 * Insert or change the memberlist buttons
	 */
	public function memberlist() : void
	{
		global $context;

		// When using stylesCompat only...
		if (Config::$current->stylesCompat) {

			// View All Members Button
			if (isset($context['memberlist_buttons']['view_all_members'])) {
				$context['memberlist_buttons']['view_all_members']['icon'] = 'mlist';
			}

			// Search Button
			if (isset($context['memberlist_buttons']['mlist_search'])) {
				$context['memberlist_buttons']['mlist_search']['icon'] = 'search';
			}

			// Add a sorting button
			if (!empty($context['columns'])) {
				$context['memberlist_buttons']['members_sort'] = [
					'lang' => true,
					'icon' => 'sort',
					'text' => 'sort_by',
					'class' => '',
					'url' => '',
					'custom' => isset($_REQUEST['sa']) && $_REQUEST['sa'] === 'search' ? 'style="display: none;"' : '',
					'sub_buttons' => $this->sortMembers()
				];
			}
		}
	}

	/**
	 * Sort the memberlist
	 */
	private function sortMembers() : array
	{
		global $context, $scripturl;

		$context['sort_direction'] = !isset($_REQUEST['desc']) ? 'up' : 'down';
		$context['sort_by'] = $_REQUEST['sort'] ?? 'real_name';
		$sorting_items = [];
		foreach ($context['columns'] as $key => $column) {
			$sorting_items[] = [
				'label' => $column['label'],
				'url' => $scripturl . '?action=mlist;sort=' . $key . ($context['sort_by'] == $key && $context['sort_direction'] == 'up' ? ';desc' : ''),
				'active' => $context['sort_by'] == $key,
				'icon' => ($context['sort_by'] == $key ? ($context['sort_direction'] == 'up' ? 'sort_up' : 'sort_down') : 'sort_down')
			];
		}

		return $sorting_items;
	}
}