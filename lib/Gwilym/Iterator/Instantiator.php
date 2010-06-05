<?php

/**
* This wraps another iterator with a class instantiator, setup so that it will create instance based on class names returned by the inner iterator.
*/
class Gwilym_Iterator_Instantiator extends IteratorIterator
{
	protected $_cache;
	protected $_instances = array();

	/**
	* @param Traversable $iterator
	* @param bool $cache cache instances over multiple iterations so only one instance is created per class name per Gwilym_Iterator_Instantiator
	* @return Gwilym_Iterator_Instantiator
	*/
	public function __construct (Traversable $iterator, $cache = true)
	{
		$this->_cache = true;
		parent::__construct($iterator);
	}

	public function current ()
	{
		// fetch class name from inner iterator and transform it to an instance instead
		$class = parent::current();

		if (!$this->_cache) {
			return new $class;
		}

		if (!isset($this->_instances[$class])) {
			return $this->_instances[$class] = new $class;
		}

		return $this->_instances[$class];
	}
}
