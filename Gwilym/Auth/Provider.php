<?php

abstract class Gwilym_Auth_Provider extends Gwilym_Auth
{
	/**
	* Return an array of Gwilym_Auth_Provider instances representing enabled auth providers.
	*
	* @return array
	*/
	public static function getEnabled ()
	{
		$providers = array();

		return $providers;
	}
}
