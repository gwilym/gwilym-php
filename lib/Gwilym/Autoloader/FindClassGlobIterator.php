<?php

class Gwilym_Autoloader_FindClassGlobIterator extends GlobIterator
{
	protected $_base;

	public function __construct ($base, $pattern)
	{
		$this->_base = $base;
		parent::__construct($base . $pattern, FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);
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

	public function key ()
	{
		return $this->current();
	}

	/** @return string */
	public function current ()
	{
		return self::pathToClassName(substr(parent::current(), strlen($this->_base)));
	}
}
