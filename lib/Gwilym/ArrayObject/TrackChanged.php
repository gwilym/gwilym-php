<?php

/**
* Minor extension of PHP's ArrayObject class which tracks whether any values have been changed, removed or re-ordered since construction.
*
* This is achieved by simply overriding every ArrayObject method that performs an element write.
*/
class Gwilym_ArrayObject_TrackChanged extends ArrayObject
{
	/** @var bool true if any elements have changed */
	protected $_dirty = false;

	/**
	* Set dirty flag
	*
	* @return bool true if flag was changed, otherwise false (ie. flag already set)
	*/
	protected function _setDirty ()
	{
		if ($this->_dirty) {
			return false;
		}

		return $this->_dirty = true;
	}

	/** @return bool true if any elements have changed */
	public function dirty ()
	{
		return $this->_dirty;
	}

	/**
	* Clear dirty flag
	*
	* @return bool true if flag was changed, otherwise false (ie. flag already cleared)
	*/
	public function clearDirty ()
	{
		if (!$this->_dirty) {
			return false;
		}

		$this->_dirty = false;
		return true;
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $value
	* @return void
	*/
	public function append ($value)
	{
		$this->_dirty = true;
		return parent::append($value);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function asort ()
	{
		$this->_setDirty();
		return parent::asort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $input
	* @return array
	*/
	public function exchangeArray ($input)
	{
		$this->_setDirty();
		return parent::exchangeArray($input);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function ksort ()
	{
		$this->_setDirty();
		return parent::ksort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function natcasesort ()
	{
		$this->_setDirty();
		return parent::natcasesort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function natsort ()
	{
		$this->_setDirty();
		return parent::natsort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $index
	* @param mixed $newval
	* @return void
	*/
	public function offsetSet ($index, $newval)
	{
		$this->_setDirty();
		return parent::offsetSet($index, $newval);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $index
	* @return void
	*/
	public function offsetUnset ($index)
	{
		$this->_setDirty();
		return parent::offsetUnset($index);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $cmp_function
	* @return void
	*/
	public function uasort ($cmp_function)
	{
		$this->_setDirty();
		return parent::uasort($cmp_function);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $cmp_function
	* @return void
	*/
	public function uksort ($cmp_function)
	{
		$this->_setDirty();
		return parent::uksort($cmp_function);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $serialize
	*/
	public function unserialize ($serialize)
	{
		$this->_setDirty();
		return parent::unserialize($serialize);
	}
}
