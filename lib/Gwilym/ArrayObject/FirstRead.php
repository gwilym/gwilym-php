<?php

/**
* Minor extension of PHP's ArrayObject class which can detect when the instance is first accessed for reading (or writing).
*
* This is achieved by simply overriding every ArrayObject method that performs an element write.
*/
class Gwilym_ArrayObject_FirstRead extends Gwilym_ArrayObject_TrackChanged
{
	protected $_accessed = false;

	/**
	* Set accessed flag
	*
	* @return bool true if flag was changed, otherwise false (ie. flag already set)
	*/
	protected function _setAccessed ()
	{
		if ($this->_accessed) {
			return false;
		}

		return $this->_accessed = true;
	}

	/**
	* Override of TrackChanged's _setDirty method to make writes also trigger accessed
	*
	* @return bool true if flag was changed, otherwise false (ie. flag already set)
	*/
	protected function _setDirty ()
	{
		$result = parent::_setDirty();
		if ($result) {
			$this->_setAccessed();
		}
		return $result;
	}

	public function count ()
	{
		$this->_setAccessed();
		return parent::count();
	}
	
	public function getAccessed ()
	{
		return $this->_accessed;
	}

	public function getArrayCopy ()
	{
		$this->_setAccessed();
		return parent::getArrayCopy();
	}

	public function getIterator ()
	{
		$this->_setAccessed();
		return parent::getIterator();
	}

	public function offsetExists ($index)
	{
		$this->_setAccessed($index);
		return parent::offsetExists($index);
	}

	public function offsetGet ($index)
	{
		$this->_setAccessed();
		return parent::offsetGet($index);
	}

	public function serialize ()
	{
		$this->_setAccessed();
		return parent::serialize();
	}
}
