<?php

class Gwilym_Autoloader_FindClassGlobIterator extends GlobIterator
{
	protected $_base;

	public function __construct ($base, $pattern, $flags = 0)
	{
		$this->_base = $base;
		parent::__construct($base . $pattern, $flags);
	}

	/**
	* Given a path, relative to any autoload path, returns a class name that should be defined by that file (if it exists)
	*
	* @param string $path
	* @return string
	*/
	protected static function pathToClassName ($path) {
		return str_replace('/', '_', substr($path, 1, strlen($path) - 5)); // 5 being the length of '.php' plus a leading '/'
	}

	/** @return string */
	public function current ()
	{
		return self::pathToClassName(substr(str_replace('\\', '/', parent::current()), strlen($this->_base)));
	}
}
