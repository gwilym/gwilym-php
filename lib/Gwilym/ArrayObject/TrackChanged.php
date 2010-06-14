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

	/** @return bool true if any elements have changed */
	public function dirty ()
	{
		return $this->_dirty;
	}

	/**
	* Clear dirty flag
	*
	* @return void
	*/
	public function clearDirty ()
	{
		$this->_dirty = false;
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
		$this->_dirty = true;
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
		$this->_dirty = true;
		return parent::exchangeArray($input);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function ksort ()
	{
		$this->_dirty = true;
		return parent::ksort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function natcasesort ()
	{
		$this->_dirty = true;
		return parent::natcasesort();
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	*/
	public function natsort ()
	{
		$this->_dirty = true;
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
		$this->_dirty = true;
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
		$this->_dirty = true;
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
		$this->_dirty = true;
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
		$this->_dirty = true;
		return parent::uksort($cmp_function);
	}

	/**
	* Wrapper for ArrayObject method which sets internal dirty flag.
	*
	* @param mixed $serialize
	*/
	public function unserialize ($serialize)
	{
		$this->_dirty = true;
		return parent::unserialize($serialize);
	}
}
