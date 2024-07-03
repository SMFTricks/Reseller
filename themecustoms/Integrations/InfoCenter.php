<?php

/**
 * @package Theme Customs
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2024, SMF Tricks
 * @license MIT
 */

namespace ThemeCustoms\Integrations;

use ThemeCustoms\Config;

class InfoCenter
{
	/**
	 * The members id's
	 */
	private array $members = [];

	/**
	 * It will load the avatars for the info center
	 */
	public function init() : void
	{
		global $settings;

		// Check if the info center is even there...
		if (!empty($settings['st_disable_info_center'])) {
			return;
		}

		// Recent Posts
		if (!empty($settings['st_enable_avatars_recent'])) {
			$this->get_latest_posts();
		}

		// Users Online
		if (!empty($settings['st_enable_avatars_online'])) {
			$this->get_online_users();
		}
		
		// Unique ids
		$this->members = array_unique($this->members);

		// Any members then?
		if (empty($this->members)) {
			return;
		}

		// Get them!
		loadMemberData($this->members);

		// While we are here, insert avatars in the online list
		if (!empty($settings['st_enable_avatars_online'])) {
			$this->online_users();
		}

		// And now the recent posts
		if (!empty($settings['st_enable_avatars_recent'])) {
			$this->recent_posts();
		}
	}

	/**
	 * Will get the user ids from the latest posts
	 */
	private function get_latest_posts() : void
	{
		global $context, $settings;

		// Check if there's anything to do
		if (empty($settings['number_recent_posts']) || empty(Config::$current->avatarOptions) || empty($context['latest_posts']))
			return;

		foreach ($context['latest_posts'] as $post) {
			$this->members[] = $post['poster']['id'];
		}
	}

	/**
	 * Will get the user ids from the online list
	 */
	private function get_online_users() : void
	{
		global $context;

		// Check if there's anything to do
		if (empty(Config::$current->avatarOptions) || empty($context['users_online']))
			return;

		foreach ($context['users_online'] as $user) {
			$this->members[] = $user['id'];
		}
	}

	/**
	 * This will add the avatars to the online list
	 */
	private function online_users() : void
	{
		global $context;

		// Need a list of users online...
		if (empty($context['list_users_online'])) {
			return;
		}

		foreach ($context['users_online'] as $item => $user_online)
		{
			// Search the id in the user link using a regular expression
			if (empty($user_online['id'])) {
				continue;
			}

			// Check if the user was in our list before
			if (!in_array($user_online['id'], $this->members)) {
				continue;
			}

			// Add the avatar
			loadMemberContext($user_online['id']);
			$context['list_users_online'][$item] = themecustoms_useronline($user_online);
		}
	}

	/**
	 * This will add the avatars to the array of recent posts
	 */
	private function recent_posts() : void
	{
		global $context, $settings, $memberContext;

		// Need a list of recent posts...
		if (empty($context['latest_posts']) || empty($settings['number_recent_posts'])) {
			return;
		}

		foreach ($context['latest_posts'] as $post_id => $post)
		{
			// Check for guests posts making their way in here
			if (empty($post['poster']['id'])) {
				continue;
			}

			loadMemberContext($post['poster']['id']);
			$context['latest_posts'][$post_id]['poster']['avatar'] = $memberContext[$post['poster']['id']]['avatar'];
		}
	}
}