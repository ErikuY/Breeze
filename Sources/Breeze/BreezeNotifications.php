<?php

/**
 * BreezeNotifications
 *
 * The purpose of this file is to fetch all notifications for X user
 * @package Breeze mod
 * @version 1.0 Beta 2
 * @author Jessica Gonz�lez <missallsunday@simplemachines.org>
 * @copyright Copyright (c) 2012, Jessica Gonz�lez
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

/*
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://missallsunday.com code.
 *
 * The Initial Developer of the Original Code is
 * Jessica Gonz�lez.
 * Portions created by the Initial Developer are Copyright (c) 2012
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class BreezeNotifications
{
	protected $types = array();
	protected $params = array();
	private $user = 0;
	private $settings = '';
	private $query = '';
	private $ReturnArray = array();
	private $usersData = array();
	private $content = array();

	function __construct()
	{
		$this->types = array(
			'comment',
			'status',
			'like',
			'buddy',
			'mention'
		);

		/* We kinda need all this stuff, dont' ask why, just nod your head... */
		$this->settings = BreezeSettings::getInstance();
		$this->query = BreezeQuery::getInstance();
	}

	public function Create($params)
	{
		global $user_info;

		/* Set this as false by default */
		$double_request = false;

		/* if the type is buddy then let's do a check to avoid duplicate entries */
		if (!empty($params) && in_array($params['type'], $this->types))
		{
			/* Load all the Notifications */
			$temp = $this->query->GetNotifications();

			if (!empty($temp))
				foreach ($temp as $t)
					if ($t['user'] == $params['user'] && $t['content']->from_id == $user_info['id'] && $t['type'] != 'mention')
						$double_request = true;
		}

		if ($double_request)
			fatal_lang_error('BreezeMod_buddyrequest_error_doublerequest', false);

		elseif (!empty($params) && in_array($params['type'], $this->types) && !$double_request)
		{
			$this->params = $params;

			/* Convert to a json string */
			$this->params['content'] = json_encode($this->params['content']);

			$this->query->InsertNotification($this->params);
		}

		else
			return false;
	}

	public function Count()
	{
		return count($this->query->GetNotifications());
	}

	protected function GetByUser($user)
	{
		/* Dont even bother... */
		if (empty($user))
			return;

		$user = (int) $user;

		return $this->query->GetNotificationByUser($user);
	}

	public function doStream($user)
	{
		global $context;

		$this->all = $this->GetByUser($user);

		/* Load users data */
		if (!empty($this->all))
			foreach ($this->all as $g)
				$this->content[] = get_object_vars($g['content']);

			echo '<pre>';
			print_r($this->content);
			echo '</pre>';

		foreach ($this->content as $k => $v)
			$this->usersData[$v] = BreezeSubs::LoadUserInfo($v, true);

		$context['insert_after_template'] .= '
		<script type="text/javascript"><!-- // --><![CDATA[
$(document).ready(function()
{';

		/* Check for the type and act in accordance */
		foreach($this->all as $all)
			if (in_array($all['type'], $this->types))
			{
				$call = 'do' . ucfirst($this->types[$all['type']]);

				if (method_exists($call))
					$context['insert_after_template'] .= $this->$call($all) == false ? '' : $this->$call($all);
			}

		$context['insert_after_template'] .= '
});

// ]]></script>';
	}

	protected function doComments($noti)
	{
		global $user_info;

		/* No notification no fun for you! */
		if (empty($noti))
			return false;

		/* Don't send the notification to the user who posted the comment */
		if ($noti['content']['user_who_commented'] == $user_info['id'])
			return false;

		/* No users data no fun for you! */
		if (empty($this->usersData))
			return false;

		/* Send the notification to the person who created the status */
		elseif ($noti['content']->user_who_created_the_status == $user_info['id'])
			$message = '$.sticky(\''. JavaScriptEscape(printf($this->settings->GetText('noti_comment_message_statusOwner'), $this->usersData[$noti['content']->user_who_commented]['link'], $this->usersData[$noti['content']->user_who_owns_the_profile]['link'])) .'\');';

		/* Send the notification to the wall owner */
		elseif ($noti['content']->user_who_owns_the_profile == $user_info['id'])
			$message = '$.sticky(\''. JavaScriptEscape(printf($this->settings->GetText('noti_comment_message_wallOwner'), $this->usersData[$noti['content']->user_who_commented]['link'], $this->usersData[$noti['content']->user_who_created_the_status]['link'])) .'\');';

		/* Send the generic message */
		else
			$message = '$.sticky(\''. JavaScriptEscape(printf($this->settings->GetText('noti_comment_message'), $this->usersData[$noti['content']->user_who_commented]['link'], $this->usersData[$noti['content']->user_who_created_the_status]['link'], $this->usersData[$noti['content']->user_who_owns_the_profile]['link'])) .'\');';

		return $message;
	}

	protected function doMention($noti)
	{
		/* No notification no fun for you! */
		if (empty($noti))
			return false;

		/* No users data no fun for you! */
		if (empty($this->usersData))
			return false;

		/* Print the notification */
		else
			$message = '$.sticky(\''. JavaScriptEscape($this->mention_info[1] == $this->mention_info[0] ? sprintf($this->settings->GetText('mention_message_own_wall'), $temp_users_load[$this->mention_info[1]]['link']) : sprintf($this->settings->GetText('mention_message'), $temp_users_load[$this->mention_info[1]]['link'], $temp_users_load[$this->mention_info[0]]['link'])) .'\');';
	}

	protected function Delete($id)
	{
		$this->query->DeleteNotification($id);
	}

	protected function MarkAsRead($id)
	{
		$this->query->MarkAsReadNotification($id);
	}
}