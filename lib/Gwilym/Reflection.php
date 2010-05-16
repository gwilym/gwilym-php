<?php

/**
* For ease of use, this class mostly contains static verions of PHP's Reflection functionality.
*/
class Gwilym_Reflection
{
	public static function isClassInstanciable ($className)
	{
		$reflect = new ReflectionClass($className);
		return $reflect->isInstantiable();
	}

	public static function isClosure ($obj)
	{
		if (is_object($obj) && class_exists('Closure') && $obj instanceof Closure)
		{
			return true;
		}
		return false;
	}
}
