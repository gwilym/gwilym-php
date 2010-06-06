<?php

class Gwilym_KeyStore_File_GlobIterator extends GlobIterator
{
	/**
	* @param string $path
	* @param int $flags ignored, do not use
	* @return Gwilym_KeyStore_File_GlobIterator
	*/
	public function __construct ($path, $flags = 0)
	{
		parent::__construct($path, FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);
	}

	public function current ()
	{
		return file_get_contents(parent::current());
	}
}
