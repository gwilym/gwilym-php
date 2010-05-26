<?php

class Gwilym_KeyStore extends Gwilym_KeyStore_File
{
	protected static $_instance;

	public static function factory ()
	{
		if (self::$_instance === null)
		{
			self::$_instance = new Gwilym_KeyStore_File();
		}
		return self::$_instance;
	}
}
