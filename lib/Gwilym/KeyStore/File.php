<?php

/**
* Implementation of keystore class using files. For development purposes only.
*/
class Gwilym_KeyStore_File extends Gwilym_KeyStore implements Gwilym_KeyStore_Interface
{
	/**
	* List of characters disallowed as key names when this implementation is used.
	*
	* @var string
	*/
	protected static $_badCharacters = array(
		"\\",
		"/",
		":",
		"*",
		"?",
		'"',
		"<",
		">",
		"|",
		"\0"
	);

	protected $_dir;

	public function __construct ($dir = null)
	{
		if ($dir === null)
		{
			$this->_dir = Gwilym_Config_KeyStore_File::$storage;
		}
		else
		{
			$this->_dir = $dir;
		}
	}

	public function keyToFilename ($key)
	{
		$filename = str_replace(self::$_badCharacters, '', $key);
		if ($filename !== $key) {
			throw new Gwilym_KeyStore_File_Exception_InvalidKeyName($key);
		}

		if (!file_exists($this->_dir)) {
			if (!@mkdir($this->_dir)) {
				throw new Gwilym_KeyStore_File_Exception_DirectoryCreateError($this->_dir);
			}
		}

		return $this->_dir . '/' . $filename;
	}

	public function set ($key, $value)
	{
		$file = $this->keyToFilename($key);

		if (file_exists($file) && !is_writable($file))
		{
			throw new Gwilym_KeyStore_File_Exception_FileNotWritable($file);
		}

		if (!file_put_contents($file, $value))
		{
			throw new Gwilym_KeyStore_File_Exception_FileWriteError($file);
		}

		return true;
	}

	public function get ($key)
	{
		$file = $this->keyToFilename($key);

		if (!file_exists($file))
		{
			return false;
		}

		if (!is_readable($file))
		{
			throw new Gwilym_KeyStore_File_Exception_FileNotReadable($file);
		}

		return file_get_contents($file);
	}

	public function exists ($key)
	{
		$file = $this->keyToFilename($key);
		return file_exists($file);
	}

	public function delete ($key)
	{
		$file = $this->keyToFilename($key);

		if (!file_exists($file))
		{
			return true;
		}

		if (!unlink($file))
		{
			throw new Gwilym_KeyStore_File_Exception_FileDeleteError;
		}

		return true;
	}

	public function multiSet ($keyValues)
	{
		foreach ($keyValues as $key => $value)
		{
			if (!$this->set($key, $value))
			{
				return false;
			}
		}
		return true;
	}

	public function multiGet ($pattern)
	{
		return new Gwilym_KeyStore_File_GlobIterator($this->_dir . '/' . $pattern);
	}

	public function multiDelete ($pattern)
	{
		$glob = new GlobIterator($this->_dir . '/' . $pattern, FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);

		foreach ($glob as $pathname) {
			if (!unlink($pathname)) {
				return false;
			}
		}

		return true;
	}

	public function increment ($key, $value = 1)
	{
		$value = (int)$this->get($key) + $value;
		if ($this->set($key, $value)) {
			return $value;
		}
		return false;
	}

	public function decrement ($key, $value = 1)
	{
		return $this->increment($key, 0-(int)$value);
	}

	public function append ($key, $value)
	{
		$current = $this->get($key);
		if ($current === false) {
			$current = $value;
		} else {
			$current .= $value;
		}
		if (!$this->set($key, $current)) {
			return false;
		}
		return strlen($current);
	}
}
