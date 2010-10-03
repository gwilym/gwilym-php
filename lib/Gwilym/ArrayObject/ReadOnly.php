<?php

/**
* Minor extension of PHP's ArrayObject class which prevents changes to the array.
*
* This is achieved by simply overriding every ArrayObject method that performs an element write.
*/
class Gwilym_ArrayObject_ReadOnly extends ArrayObject
{
	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function append ($value)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function asort ()
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function exchangeArray ($input)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}
	
	/**
	 * Only way I can think of preventing foreach (... as $key => &$val) from being able to alter array contents,
	 * while keeping support for setIteratorClass.
	 *
	 * @return Iterator
	 */
	public function getIterator ()
	{
		$class = $this->getIteratorClass();
		return new $class($this->getArrayCopy());
	}
	
	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function ksort ()
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function natcasesort ()
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function natsort ()
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}
	
	/**
	 * This prevents external code from obtaining references directly to the internal elements, thereby being able
	 * to alter them.
	 *
	 * In this case, a notice will be issued if this is attempted:
	 *
	 * Notice: Indirect modification of overloaded element of Gwilym_ArrayObject_ReadOnly has no effect in ...
	 *
	 * @return mixed
	 */
	public function offsetGet ($index)
	{
		return parent::offsetGet($index);
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function offsetSet ($index, $newval)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function offsetUnset ($index)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function uasort ($cmp_function)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function uksort ($cmp_function)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}

	/**
	* Override of ArrayObject method to prevent array changes.
	*/
	public function unserialize ($serialize)
	{
		throw new Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly;
	}
}
