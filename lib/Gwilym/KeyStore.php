<?php

abstract class Gwilym_KeyStore
{
	protected static $_instance;

	/** @return Gwilym_KeyStore_Interface */
	public static function factory ()
	{
		if (self::$_instance === null)
		{
			self::$_instance = new Gwilym_Config_KeyStore::$defaultKeyStore;
		}
		return self::$_instance;
	}

	protected $_prefix;

	protected $_prefixLocked = false;

	public function prefix ($prefix = null)
	{
		if ($prefix === null) {
			return $this->_prefix;
		}

		if ($this->_prefixLocked) {
			return false;
		}

		$this->_prefix = (string)$prefix;
		return true;
	}

	public function lockPrefix ()
	{
		$this->_prefixLocked = true;
	}
}
