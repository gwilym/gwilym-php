<?php

abstract class Gwilym_Plugins implements Gwilym_Plugins_Interface
{
	/** @var Gwilym_KeyStore_Interface */
	private static $_staticKeyStore;

	private static function _staticKeyStore ()
	{
		if (self::$_staticKeyStore === null)
		{
			// note: make sure this uses the same keystore configuration as __construct (except the prefix)
			self::$_staticKeyStore = Gwilym_KeyStore::factory();
		}
		return self::$_staticKeyStore;
	}

	public static function classNameToPluginId ($className)
	{
		return preg_replace('#^Gwilym_Plugins_(.*?)_Plugin$#', '$1', $className);
	}

	/**
	* Discover and return a list of all available plugins based on available files. This assumes that plugins are all located under /Gwilym/Plugins and have corresponding classes named Gwilym_Plugins_{id}_Plugin
	*
	* @return Traversable list of all file-wise available plugins or false if file scanning failed
	*/
	public static function plugins ($instanciate = false)
	{
		$plugins = Gwilym_Autoloader::findClasses('Gwilym_Plugins_*_Plugin');
		if (!$instanciate) {
			return $plugins;
		}
		return new Gwilym_Iterator_Instantiator($plugins);
	}

	/** @return array<string> list of currently active plugins */
	public static function activePlugins ()
	{
		// note: make sure this uses the same keystore configuration as __construct
		$keystore = Gwilym_KeyStore::factory();

		return $keystore->multiGet('gwilym,plugin,active,*');
	}

	/** @var Gwilym_keyStore_Interface */
	protected $_keystore;

	protected $_id;

	public function __construct ()
	{
		// note: make sure this uses the same keystore configuration as _staticKeyStore (except the prefix)
		$this->_keystore = Gwilym_KeyStore::factory();
		$this->_keystore->prefix('gwilym,plugin,' . $this->id() . ',');
		$this->_keystore->lockPrefix();
	}

	public function id ()
	{
		if ($this->_id === null) {
			$this->_id = self::classNameToPluginId(get_class($this));
		}
		return $this->_id;
	}

	public function active ()
	{
		return (bool)self::_staticKeyStore()->get('gwilym,plugin,active,' . $this->id());
	}

	public function activate ()
	{
		if ($this->active()) {
			return true;
		}
		return (bool)self::_staticKeyStore()->set('gwilym,plugin,active,' . $this->id(), $this->id());
	}

	public function deactivate ()
	{
		if (!$this->active()) {
			return true;
		}
		return (bool)self::_staticKeyStore()->delete('gwilym,plugin,active,' . $this->id());
	}
}
