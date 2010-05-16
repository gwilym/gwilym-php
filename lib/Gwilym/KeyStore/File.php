<?php

/**
* Implementation of keystore class using files. For development purposes only.
*/
class Gwilym_KeyStore_File implements Gwilym_KeyStore_Interface
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

	public static $dir;

	protected static function patternToRegularExpresion ($pattern)
	{
		// todo: this is really basic and needs improving
		return '#^' . str_replace('*', '.*', $pattern) . '$#';
	}

	protected static function testFilenameAgainstPattern ($filename, $pattern)
	{
		$pattern = self::patternToRegularExpresion($pattern);
		return (bool)preg_match($pattern, $filename);
	}

	public static function keyToFilename ($key)
	{
		$filename = str_replace(self::$_badCharacters, '', $key);
		if ($filename !== $key)
		{
			throw new Gwilym_KeyStore_File_Exception_InvalidKeyName($key);
		}

		if (!file_exists(self::$dir))
		{
			if (!mkdir(self::$dir))
			{
				throw new Gwilym_KeyStore_File_Exception_DirectoryCreateError(self::$dir);
			}
		}

		return self::$dir . '/' . $filename;
	}

	public static function set ($key, $value)
	{
		$file = self::keyToFilename($key);

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

	public static function get ($key)
	{
		$file = self::keyToFilename($key);

		if (!file_exists($file))
		{
			throw new Gwilym_KeyStore_Exception_KeyDoesntExist($key);
		}

		if (!is_readable($file))
		{
			throw new Gwilym_KeyStore_File_Exception_FileNotReadable($file);
		}

		return file_get_contents($file);
	}

	public static function exists ($key)
	{
		$file = self::keyToFilename($key);
		return file_exists($file);
	}

	public static function delete ($key)
	{
		$file = self::keyToFilename($key);

		if (!file_exists($file))
		{
			throw new Gwilym_KeyStore_Exception_KeyDoesntExist($key);
		}

		if (!unlink($file))
		{
			throw new Gwilym_KeyStore_File_Exception_FileDeleteError();
		}

		return true;
	}

	public static function multiSet ($keyValues)
	{
		throw new Exception();
	}

	public static function multiGet ($pattern)
	{
		$dir = dir(self::$dir);
		$results = array();
		while ($file = $dir->read())
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			if (!self::testFilenameAgainstPattern($file, $pattern))
			{
				continue;
			}

			$results[$file] = self::get($file);
		}
		return $results;
	}

	public static function increment ($key, $value = null)
	{
		throw new Exception();
	}

	public static function decrement ($key, $value = null)
	{
		throw new Exception();
	}

	public static function append ($key, $value)
	{
		throw new Exception();
	}
}

Gwilym_KeyStore_File::$dir = GWILYM_VAR_DIR . '/keystore';
