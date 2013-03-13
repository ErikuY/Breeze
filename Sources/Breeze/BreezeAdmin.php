<?php

/**
 * BreezeAdmin
 *
 * The purpose of this file is, a procedural set of functions that handles the admin pages for Breeze
 * @package Breeze mod
 * @version 1.0 Beta 3
 * @author Jessica Gonz�lez <missallsunday@simplemachines.org>
 * @copyright Copyright (c) 2013 Jessica Gonz�lez
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
	die('No direct access...');

function Breeze_Admin_Index()
{
		global $txt, $scripturl, $context, $sourcedir;

		loadLanguage('Breeze');

		require_once($sourcedir . '/ManageSettings.php');

		$context['page_title'] = $txt['Breeze_admin_settings_admin_panel'];

		$subActions = array(
			'general' => 'Breeze_Admin_Main',
			'settings' => 'Breeze_Admin_Settings',
			'permissions' => 'Breeze_Admin_Permissions',
			'style' => 'Breeze_Admin_Style',
			'donate' => 'Breeze_Admin_Donate',
		);

		loadGeneralSettingParameters($subActions, 'general');

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['Breeze_admin_settings_admin_panel'],
			'description' => $txt['Breeze_admin_welcome'],
			'tabs' => array(
				'general' => array(),
				'settings' => array(),
				'permissions' => array(),
				'style' => array(),
				'donate' => array(),
			),
		);

		call_user_func($subActions[$_REQUEST['sa']]);
}

	function Breeze_Admin_Main()
	{
		global $scripturl, $context, $breezeController;

		loadtemplate('BreezeAdmin');

		$text = $breezeController->get('text');
		$headers = $breezeController->get('tools');

		/* Get the version */
		$context['Breeze']['version'] = Breeze::$version;

		/* The support site RSS feed */
		$context['Breeze']['support'] = Breeze::$supportStite;

		/* Set all the page stuff */
		$context['page_title'] = $text->getText('admin_settings_main');
		$context['sub_template'] = 'admin_home';

		/* Headers */
		Breeze::headersHook('admin');
	}

	function Breeze_Admin_Settings()
	{
		global $scripturl, $context, $sourcedir, $breezeController;

		/* Load stuff */
		$text = $breezeController->get('text');
		$globals = Breeze::sGlobals('request');
		$context['sub_template'] = 'show_settings';
		$context['page_title'] = $text->getText('admin_settings_main');

		require_once($sourcedir . '/ManageServer.php');

		$config_vars = array(
			array('check', Breeze::$txtpattern .'admin_settings_enable', 'subtext' => $text->getText('admin_settings_enable_sub')),
			array('check', Breeze::$txtpattern .'admin_enable_limit', 'subtext' => $text->getText('admin_enable_limit_sub')),
			array('select', Breeze::$txtpattern .'admin_limit_timeframe', array('hour' => $text->getText('user_settings_time_hour'), 'day' => $text->getText('user_settings_time_day'), 'week' => $text->getText('user_settings_time_week'), 'month' => $text->getText('user_settings_time_month'), 'year' => $text->getText('user_settings_time_year')), 'subtext' => $text->getText('admin_limit_timeframe_sub')),
			array('text', Breeze::$txtpattern .'allowedActions', 'size' => 56, 'subtext' => $text->getText('allowedActions_sub')),
			array('int', Breeze::$txtpattern .'admin_mention_limit', 'size' => 3, 'subtext' => $text->getText('admin_mention_limit_sub')),
		);

		$context['post_url'] = $scripturl . '?action=admin;area=breezesettings;save';

		/* Saving? */
		if ($globals->validate('save') == true)
		{
			checkSession();
			saveDBSettings($config_vars);
			redirectexit('action=admin;area=breezesettings');
		}

		prepareDBSettingContext($config_vars);
	}

	/* Pay no attention to the girl behind the curtain */
	function Breeze_Admin_Donate()
	{
		global $context, $scripturl, $breezeController;

		loadtemplate('BreezeAdmin');

		/* Headers */
		$headers = $breezeController->get('tools');
		Breeze::headersHook('admin');

		/* Text strings */
		$text = $breezeController->get('text');

		/* Page stuff */
		$context['page_title'] = $text->getText('admin_settings_donate_title');
		$context['sub_template'] = 'admin_donate';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=admin;area=breezedonate',
			'name' => $text->getText('admin_settings_donate_title')
		);
		$context['Breeze']['donate'] = $text->getText('donate');
	}