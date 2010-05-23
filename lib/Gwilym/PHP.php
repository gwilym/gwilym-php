<?php

/**
* This class implements various methods for checking on PHP capabilities, the OS we're on, etc.
*/
class Gwilym_PHP
{
	protected static $_isWindows;

	public static function isWindows ()
	{
		if (self::$_isWindows === null)
		{
			self::$_isWindows = substr(PHP_OS, 0, 3) == 'WIN';
		}
		return self::$_isWindows;
	}
}
