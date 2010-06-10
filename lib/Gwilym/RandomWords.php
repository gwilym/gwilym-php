<?php

/*
PHP class - Gwilym_RandomWords

Copyright (c) 2010 Gwilym Evans

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

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
			$handle = fopen(dirname(__FILE__) . '/' . $file, 'r');
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
