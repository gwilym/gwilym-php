<?php

interface Gwilym_KeyStore_Interface
{
	/**
	* Set key $key to value $value
	*
	* @param string $key
	* @param string $value
	* @return bool true
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function set ($key, $value);

	/**
	* Get value of key $key
	*
	* @param string $key
	* @return mixed string value of key $key
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function get ($key);

	/**
	* Checks if key $key exists
	*
	* @param string $key
	* @return bool true if key $key exists otherwise false
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function exists ($key);

	/**
	* Deletes key $key
	*
	* @param string $key
	* @return bool true
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function delete ($key);

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function multiSet ($keyValues);

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function multiGet ($pattern);

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function increment ($key, $value = null);

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function decrement ($key, $value = null);

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public static function append ($key, $value);
}
