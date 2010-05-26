<?php

define('_GWILYM_PHP_ISWINDOWS', substr(PHP_OS, 0, 3) == 'WIN');

/**
* This class implements various methods for checking on PHP capabilities, the OS we're on, etc.
*/
class Gwilym_PHP
{
	protected static $_isWindows;

	public static function isWindows ()
	{
		return _GWILYM_PHP_ISWINDOWS;
	}
}
