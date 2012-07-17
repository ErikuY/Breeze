<?php

/**
 * BreezeGlobals
 *
 * The purpose of this file is
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

class BreezeGlobals
{
	protected $_request;

	public function __construct($var)
	{
		switch($var)
		{
			case 'get':
				$this->_request = $_GET;
				break;
			case 'post':
				$this->_request = $_POST;
				break;
			case 'request':
				$this->_request = $_REQUEST;
				break;
		}
	}

	public function getValue($value)
	{
		if ($this->validate($value))
			return $this->sanitize($this->_request[$value]);
		else
			return 'error_' . $value;
	}

	public function getRaw($value)
	{
		if (isset($this->_request[$value]))
			return $this->_request[$value];

		else
			return false;
	}

	public function validate($var)
	{
		if (isset($this->_request[$var]))
			return true;
		else
			return false;
	}

	public function validateBody($var)
	{
		/* You cannot post just spaces */
		if(ctype_space($this->_request[$var]) || $this->_request[$var] == '')
			return false;

		elseif (isset($this->_request[$var]) && !empty($this->_request[$var]) && !ctype_space($this->_request[$var]))
			return true;

		else
			return false;
	}

	public function unsetVar($var)
	{
		unset($this->_request[$var]);
	}

	public function sanitize($var)
	{
		if (get_magic_quotes_gpc())
			$var = stripslashes($var);

		if (is_numeric($var))
			$var = (int) trim($var);

		elseif (is_string($var))
			$var = trim(strtr(htmlspecialchars($var, ENT_QUOTES), array("\r" => '<br />', "\n" => '<br />', "\t" => '&nbsp;&nbsp;&nbsp;&nbsp;')));

		else
			$var = 'error_' . $var;

		return $var;
	}
}