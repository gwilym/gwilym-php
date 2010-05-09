<?php

class Gwilym_Autoloader
{
	protected static $_paths = array();

	public static function autoload ($class)
	{
		foreach (self::$_paths as $path)
		{
			$path = $path . '/' . str_replace('_', '/', $class) . '.php';
			if (is_readable($path))
			{
				require_once($path);
				if (class_exists($class, false))
				{
					break;
				}
			}
		}
	}

	public static function addPath ($path)
	{
		self::$_paths[] = $path;
	}

	public static function register ()
	{
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}

	public static function init ()
	{
		Gwilym_Autoloader::register();
	}
}
