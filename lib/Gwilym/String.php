<?php

/**
* For ease of use, this class mostly contains static methods that build on PHP's string functions.
*/
class Gwilym_String
{
	/**
	* Returns $str with $left inserted at the beginning of the string, if it does not already start with $left
	*
	* @param mixed $str
	* @param str $left
	*/
	public static function unltrim ($str, $left)
	{
		if (substr($str, 0, strlen($left)) !== $left)
		{
			return $left . $str;
		}
		return $str;
	}
}
