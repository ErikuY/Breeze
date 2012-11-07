<?php

/**
 * BreezeTools
 *
 * The purpose of this file is to provide some tools used across the mod
 * @package Breeze mod
 * @version 1.0 Beta 3
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

class BreezeTools
{
	function __construct(){}


	/* @todo move this to the buffer hook, I don't trust $context['html_headers'] anymore */
	public function headers($type = 'profile')
	{
		global $context, $settings, $user_info;
		static $header_done = false;

		$text = Breeze::text();

		if (!$header_done)
		{
			$context['html_headers'] .= '
			<script type="text/javascript">!window.jQuery && document.write(unescape(\'%3Cscript src="http://code.jquery.com/jquery.min.js"%3E%3C/script%3E\'))</script>
			<link rel="stylesheet" href="'. $settings['default_theme_url'] .'/css/sticky.full.css" type="text/css" />';

			$header_done = true;
		}

		/* Define some variables for the ajax stuff */
		if ($type == 'profile')
		{
			$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				var breeze_error_message = "'. $text->getText('error_message') .'";
				var breeze_success_message = "'. $text->getText('success_message') .'";
				var breeze_empty_message = "'. $text->getText('empty_message') .'";
				var breeze_error_delete = "'. $text->getText('error_message') .'";
				var breeze_success_delete = "'. $text->getText('success_delete') .'";
				var breeze_confirm_delete = "'. $text->getText('confirm_delete') .'";
				var breeze_confirm_yes = "'. $text->getText('confirm_yes') .'";
				var breeze_confirm_cancel = "'. $text->getText('confirm_cancel') .'";
				var breeze_already_deleted = "'. $text->getText('already_deleted') .'";
				var breeze_cannot_postStatus = "'. $text->getText('cannot_postStatus') .'";
				var breeze_cannot_postComments = "'. $text->getText('cannot_postComments') .'";
		// ]]></script>';

			/* Let's load jquery from CDN only if it hasn't been loaded yet */
			$context['html_headers'] .= '
			<link href="'. $settings['default_theme_url'] .'/css/breeze.css" rel="stylesheet" type="text/css" />
			<link href="'. $settings['default_theme_url'] .'/css/facebox.css" rel="stylesheet" type="text/css" />
			<script src="'. $settings['default_theme_url'] .'/js/jquery_notification.js" type="text/javascript"></script>
			<script src="'. $settings['default_theme_url'] .'/js/facebox.js" type="text/javascript"></script>
			<script src="'. $settings['default_theme_url'] .'/js/confirm.js" type="text/javascript"></script>
			<script src="'. $settings['default_theme_url'] .'/js/livequery.js" type="text/javascript"></script>
			<script src="'. $settings['default_theme_url'] .'/js/breeze.js" type="text/javascript"></script>';

			/* CSS part */
			/* @todo move this to its own file */
			$context['html_headers'] .= '
			<style type="text/css">
			.breeze_user_comment_avatar
			{
				padding:5px;
			}
			</style>';
		}

		/* Stuff for the notifications */
		if ($type == 'noti' && empty($user_info['is_guest']))
		{
			$notifications = Breeze::notifications();
			$context['insert_after_template'] .= '
			<script type="text/javascript" src="'. $settings['default_theme_url'] .'/js/noty/jquery.noty.js"></script>
			<script type="text/javascript" src="'. $settings['default_theme_url'] .'/js/noty/layouts/top.js"></script>
			<script type="text/javascript" src="'. $settings['default_theme_url'] .'/js/noty/layouts/topLeft.js"></script>
			<script type="text/javascript" src="'. $settings['default_theme_url'] .'/js/noty/layouts/topRight.js"></script>
			<script type="text/javascript" src="'. $settings['default_theme_url'] .'/js/noty/themes/default.js"></script>
			<script type="text/javascript"><!-- // --><![CDATA[
				var breeze_error_message = "'. $text->getText('error_message') .'";
				var breeze_noti_markasread = "'. $text->getText('noti_markasread') .'";
				var breeze_noti_markasread_after = "'. $text->getText('noti_markasread_after') .'";
				var breeze_noti_delete = "'. $text->getText('general_delete') .'";
				var breeze_noti_delete_after = "'. $text->getText('noti_delete_after') .'";
				var breeze_noti_close = "'. $text->getText('noti_close') .'";
				var breeze_noti_cancel = "'. $text->getText('confirm_cancel') .'";
			// ]]></script>';

			$context['insert_after_template'] .= $notifications->doStream($user_info['id']);
		}

		/* Admin bits */
		if($type == 'admin')
			$context['html_headers'] .= '
			<script src="'. $settings['default_theme_url'] .'/js/jquery.zrssfeed.min.js" type="text/javascript"></script>
			<script type="text/javascript">
var breeze_feed_error_message = "'. $text->getText('feed_error_message') .'";

$(document).ready(function ()
{
	$(\'#breezelive\').rssfeed(\'http://missallsunday.com/index.php?action=.xml;type=rss;sa=recent;board=11;limit=5/\',
	{
		limit: 5,
		header: false,
		date: true,
		errormsg: breeze_feed_error_message
   });
});
 </script>
<style type="text/css">
#breezelive
{
	width:95%;
	margin:auto;
}
#breezelive ul
 {
	list-style-type: none;
	padding: 0px;
	margin: 0px;
 }
 #breezelive ul li
 {
	margin-left: 10px;
 }
</style>';

	}

	/* Relative dates  http://www.zachstronaut.com/posts/2009/01/20/php-relative-date-time-string.html */
	public function timeElapsed($ptime)
	{
		$text = Breeze::text();
		$etime = time() - $ptime;

		if ($etime < 1)
			return $text->getText('time_just_now');

		$a = array(
			12 * 30 * 24 * 60 * 60	=> $text->getText('time_year'),
			30 * 24 * 60 * 60		=> $text->getText('time_month'),
			24 * 60 * 60			=> $text->getText('time_day'),
			60 * 60					=> $text->getText('time_hour'),
			60						=> $text->getText('time_minute'),
			1						=> $text->getText('time_second')
		);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . $str . ($r > 1 ? 's ' : ' '). $text->getText('time_ago');
			}
		}
	}

	/* A function to cut-off a string */
	public function truncateString($string, $limit, $break = ' ', $pad = '...')
	{
		if(empty($limit))
			$limit = 30;

		 /* return with no change if string is shorter than $limit */
		if(strlen($string) <= $limit)
			return $string;

		/* is $break present between $limit and the end of the string? */
		if(false !== ($breakpoint = strpos($string, $break, $limit)))
			if($breakpoint < strlen($string) - 1)
				$string = substr($string, 0, $breakpoint) . $pad;

		return $string;
	}

	/* Checks if a value on a multidimencional array exists and return the main key */
	public function returnKey($value, $array)
	{
		if (empty($value) || empty($array))
			return false;

		foreach ($array as $k => $v)
		{
			if (is_array($v))
			{
				if (in_array($value, $v))
					return $k;

					else
						return false;
			}

			else
			{
				if ($v == $value)
					return $k;

				else
					return false;
			}
		}
	}

	public function remove($array, $val, $preserve_keys = true)
	{
		if (empty($array) || empty($val) || !is_array($array))
			return false;

		if (!is_array($val))
		{
			if (!in_array($val, $array))
				return $array;

			foreach($array as $key => $value)
			{
				if ($value == $val)
					unset($array[$key]);
			}
		}

		elseif (is_array($val))
		{
			foreach($val as $find)
				foreach($array as $key => $value)
				{
					if (empty($array) || !is_array($array))
						return false;

					if ($value == $find)
						unset($array[$key]);
				}
		}

		else
			return false;

		return ($preserve_keys === true) ? $array : array_values($array);
	}

	public function loadUserInfo($id)
	{
		global $memberContext;

		/* If this isn't an array, lets change it to one */
		if (!is_array($id))
			$id = array($id);

		/* SMF always return the data as an array */
		$array = loadMemberData($id, false, 'profile');

		/* Load the users data if it wasn't loaded already */
		if (!empty($array) && is_array($array))
			foreach ($array as $u)
			{
				if (empty($memberContext[$u]))
					loadMemberContext($u);

				/* Create the context var */
				BreezeUserInfo::profile($u);
			}

		else
			return false;
	}
}