<?php

class Gwilym_RandomWords
{
	protected static $_dictionary = null;
	protected static $_dictionaryLength = 0;

	protected static function _load ()
	{
		if (self::$_dictionary !== null)
		{
			return;
		}

		self::$_dictionary = array();
		self::$_dictionaryLength = 0;

		$files = array(
			'english-words.10',
			'english-words.20',
		);

		foreach ($files as $file)
		{
			$handle = fopen(__DIR__ . '/' . $file, 'r');
			while ($line = fgets($handle))
			{
				$line = trim($line);
				if (!$line) {
					continue;
				}
				self::$_dictionary[] = $line;
				self::$_dictionaryLength++;
			}
			fclose($handle);
		}
	}

	public static function word ()
	{
		self::_load();
		return self::$_dictionary[mt_rand(0, self::$_dictionaryLength - 1)];
	}

	public static function phrase ($max = 6, $min = 2)
	{
		$phrase = array();
		$length = mt_rand($min, $max);
		while ($length)
		{
			$phrase[] = self::word();
			$length--;
		}
		return implode(' ', $phrase);
	}

	public static function sentence ($max = 3, $min = 1)
	{
		$phrase = array();
		$length = mt_rand($min, $max);
		while ($length)
		{
			$phrase[] = self::phrase();
			$length--;
		}

		$punctuation = mt_rand() / mt_getrandmax();
		if ($punctuation > .1)
		{
			$punctuation = '.';
		}
		else if ($punctuation > .05)
		{
			$punctuation = '?';
		}
		else
		{
			$punctuation = '!';
		}

		return ucfirst(implode(', ', $phrase)) . $punctuation;
	}

	public static function paragraph ($max = 6, $min = 1)
	{
		$phrase = array();
		$length = mt_rand($min, $max);
		while ($length)
		{
			$phrase[] = self::sentence();
			$length--;
		}
		return implode(' ', $phrase);
	}

	public static function article ($max = 10, $min = 1)
	{
		$phrase = array();
		$length = mt_rand($min, $max);
		while ($length)
		{
			$phrase[] = self::paragraph();
			$length--;
		}
		return implode("\n\n", $phrase);
	}
}
