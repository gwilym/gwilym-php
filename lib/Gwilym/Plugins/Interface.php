<?php

interface Gwilym_Plugins_Interface
{
	/** @return string id of plugin, corresponds to directory / file / class names such as 'Example' for Gwilym_Plugins_Example_Plugin */
	public function id ();

	/** @return bool true if activation worked, otherwise false */
	public function activate ();

	/** @return bool true if deactivation worked, otherwise false */
	public function deactivate ();

	/** @return bool true if the plugin is active, otherwise false */
	public function active ();
}
