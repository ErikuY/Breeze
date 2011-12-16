<?php

/**
 * Breeze_
 *
 * The purpose of this file is
 * @package Breeze mod
 * @version 1.0
 * @author Jessica Gonz�lez <missallsunday@simplemachines.org>
 * @copyright Copyright (c) 2011, Jessica Gonz�lez
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
 * Portions created by the Initial Developer are Copyright (C) 2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class Breeze_Globals
{
	private static $instances = array();
	private $request;

	public static function factory($var)
	{
		if (array_key_exists($var, self::$instances))
		{
			return self::$instances[$var];
		}
		return self::$instances[$var] = new Breeze_Globals($var);
	}

	function __construct($var)
	{
		switch($var)
		{
			case 'get':
				$this->request = $_GET;
				break;
			case 'post':
				$this->request = $_POST;
				break;
			case 'request':
				$this->request = $_REQUEST;
				break;
		}

	}

	function see($value)
	{
		if (isset($this->request[$value]) && self::validate($this->request[$value]))
			return self::Sanitize($this->request[$value]);
		else
			return 'error_' . $value;
	}

	function raw($value)
	{
		if (isset($this->request[$value]))
			return $this->request[$value];

		else
			return false;
	}

	public static function validate($var)
	{
		if (!empty($var))
			return true;
		else
			return false;
	}

	function UnsetVar($var)
	{
		unset($this->request[$var]);
	}

	public static function Sanitize($var)
	{
		if (get_magic_quotes_gpc())
			$var = stripslashes($var);

		if (is_numeric($var))
			$var = (int) $var;

		elseif (is_string($var))
		{
			$var = strtr(htmlspecialchars($var, ENT_QUOTES), array("\r" => '<br />', "\n" => '<br />', "\t" => '<br />'));
			$var = trim($var);
		}

		else
			$var = 'error_' . $var;

		return $var;
	}
}