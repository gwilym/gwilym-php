<?php

/**
* Implementation of keystore class using this PECL mongodb driver. Note that this doesn't use advanced mongodb features for atomicity so maybe this could be improved depending on usage.
*/
class Gwilym_KeyStore_Mongodb implements Gwilym_KeyStore_Interface
{
	public static function patternToRegularExpresion ($pattern)
	{
		// todo: this is really basic and may need improving

		$pattern = strtr($pattern, array(
			'*' => '.*',
			'?' => '.{1}',
		));

		return '/^' . $pattern . '$/';
	}

	public static function patternToQuery ($pattern)
	{
		return array(
			'_id' => new MongoRegex(self::patternToRegularExpresion($pattern)),
		);
	}

	/** @var Mongo */
	protected $_mongo;

	/** @var MongoCollection */
	protected $_mongo_collection;

	protected function _collection ()
	{
		if ($this->_mongo_collection === null) {
			$this->_mongo_collection = $this->_mongo->selectDB(Gwilym_Config_KeyStore_Mongodb::$database)->selectCollection(Gwilym_Config_KeyStore_Mongodb::$collection);
		}
		$this->_mongo->connect();
		return $this->_mongo_collection;
	}

	public function __construct ()
	{
		$this->_mongo = new Mongo(Gwilym_Config_KeyStore_Mongodb::$server, false);
	}

	/**
	* Set key $key to value $value
	*
	* @param string $key
	* @param string $value
	* @return bool true
	* @throws Gwilym_KeyStore_Exception
	*/
	public function set ($key, $value)
	{
		$criteria = array(
			'_id' => $key,
		);

		$obj = array(
			'_id' => $key,
			'value' => $value
		);

		return $this->_collection()->update($criteria, $obj, array('upsert' => true));
	}

	/**
	* Get value of key $key
	*
	* @param string $key
	* @return string value of key $key or false if not exists
	* @throws Gwilym_KeyStore_Exception
	*/
	public function get ($key)
	{
		$doc = $this->_collection()->findOne(array('_id' => $key), array('value'));
		if ($doc === null) {
			return false;
		}
		return $doc['value'];
	}

	/**
	* Checks if key $key exists
	*
	* @param string $key
	* @return bool true if key $key exists otherwise false
	* @throws Gwilym_KeyStore_Exception
	*/
	public function exists ($key)
	{
		return $this->_collection()->count(array('_id' => $key)) > 0;
	}

	/**
	* Deletes key $key
	*
	* @param string $key
	* @return bool true
	* @throws Gwilym_KeyStore_Exception
	*/
	public function delete ($key)
	{
		return $this->_collection()->remove(array('_id' => $key), array('justOne' => true));
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function multiSet ($keyValues)
	{
		$batch = array();
		foreach ($keyValues as $key => $value)
		{
			$batch[] = array(
				'_id' => $key,
				'value' => $value,
			);
		}
		unset($keyValues);

		return $this->_collection()->batchInsert($batch);
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function multiGet ($pattern)
	{
		$cursor = $this->_collection()->find(self::patternToQuery($pattern), array('value'));
		if (!$cursor) {
			return false;
		}

		$results = array();
		while ($result = $cursor->getNext()) {
			$results[$result['_id']] = $result['value'];
		}
		return $results;
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function multiDelete ($pattern)
	{
		return $this->_collection()->remove(self::patternToQuery($pattern));
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function increment ($key, $value = 1)
	{
		$this->_collection()->update(array('_id' => $key), array('$inc' => array('value' => (int)$value)), array('upsert' => true));
		return $this->get($key);
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function decrement ($key, $value = 1)
	{
		return $this->increment($key, 0-(int)$value);
	}

	/**
	* @throws Gwilym_KeyStore_Exception
	*/
	public function append ($key, $value)
	{
		// can't find a server-side append operation for mongo
		return $this->set($key, $this->get($key) . $value);
	}
}
