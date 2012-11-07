<?php

/**
 * Breeze_
 *
 * The purpose of this file is to handle the ajax action and display the response form the server
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

function template_breeze_post()
{
	global  $context;

	switch ($context['Breeze']['ajax']['ok'])
	{
		case 'error':
			echo 'error_';
		case '':
			echo 'error_';
			break;
		case 'deleted':
			echo 'deleted_';
			break;
		case 'ok':
			echo $context['Breeze']['ajax']['data'];
			break;
		default:
			echo 'error_';
	}
}