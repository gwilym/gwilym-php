<?php

/**
* Presently this is little more than a wrapper for the Mongo PHP extension which supports configuration through a matching Gwilym_Config class.
*/
class Gwilym_Mongo extends Mongo
{
	/**
	* put your comment there...
	*
	* @param mixed $server ignored for now -- use Gwilym_Config_Mongo::$server
	* @param mixed $options ignored for now -- this overridden and handled internally
	* @return Gwilym_Mongo
	*/
	public function __construct ($server = null, $options = array())
	{
		parent::__construct(Gwilym_Config_Mongo::$server, array(
			'connect' => false,
			'persist' => Gwilym_Config_Mongo::$server,
		));

		$this->selectDB(Gwilym_Config_Mongo::$db);
	}
}
