<?php

class Gwilym_Autoloader
{
	protected static $_paths = array();

	/**
	* Given a class name, returns a path, relative to each autoload path, which should contain the specified class (if it exists at all)
	*
	* @param string $className
	* @return string
	*/
	protected static function classNameToPath ($className) {
		return '/' . str_replace('_', '/', $className) . '.php';
	}

	/**
	* Returns a list of all class names that match a given pattern, based off all available autoloading paths. Does not actually attempt to load the respective class files or access the class, only checks for their existence as filenames.
	*
	* For portability, this is case-sentive, and brace patterns such as {a,b,c} may not available.
	*
	* @param string $pattern Glob-like pattern string, such as Gwilym_Cms_Plugins_*_Plugin, to obtain a list of all available cms plugins
	* @return Traversable results if successful, empty array for no results, or false on error
	*/
	public static function findClasses ($pattern)
	{
		$iterator = new AppendIterator();

		foreach (self::$_paths as $path) {
			$iterator->append(new Gwilym_Autoloader_FindClassGlobIterator($path, self::classNameToPath($pattern), GLOB_NOSORT));
		}

		return $iterator;
	}

	public static function autoload ($class)
	{
		foreach (self::$_paths as $path) {
			$path = $path . self::classNameToPath($class);
			if (is_readable($path)) {
				require_once($path);
				if (class_exists($class, false)) {
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
