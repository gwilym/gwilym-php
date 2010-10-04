<?php

class Tests_Gwilym_ArrayObject_FirstRead extends UnitTestCase
{
	protected $_array;
	
	protected $_object;
	
	public function setUp ()
	{
		$this->_array = array(
			'foo' => 'bar',
			'alpha' => 'beta',
		);
		
		$this->_object = new Gwilym_ArrayObject_FirstRead($this->_array);
		
		$this->assertFalse($this->_object->getAccessed());
		$this->assertFalse($this->_object->getAccessed());
	}
	
	public function testDirectRead ()
	{
		$foo = $this->_object['foo'];
		$this->assertTrue($this->_object->getAccessed());
		$foo = $this->_object['foo'];
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testDirectWrite ()
	{
		$this->_object['foo'] = 'baz';
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testCount ()
	{
		count($this->_object);
		$this->assertTrue($this->_object->getAccessed());
	}

	public function testIsset ()
	{
		isset($this->_object['bar']);
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testIterator ()
	{
		foreach ($this->_object as $key => $value) {
			// nothing
		}
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testSerialize ()
	{
		serialize($this->_object);
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testGetArrayCopy ()
	{
		$this->_object->getArrayCopy();
		$this->assertTrue($this->_object->getAccessed());
	}
	
	public function testClone ()
	{
		$foo = clone $this->_object;
		$this->assertFalse($this->_object->getAccessed());
		$this->assertFalse($foo->getAccessed());
		$foo['foo'];
		$this->assertFalse($this->_object->getAccessed());
		$this->assertTrue($foo->getAccessed());
	}
	
	public function testReference ()
	{
		$foo = &$this->_object;
		$this->assertFalse($this->_object->getAccessed());
		$this->assertFalse($foo->getAccessed());
		
		$bar = $foo['foo'];
		$this->assertTrue($this->_object->getAccessed());
		$this->assertTrue($foo->getAccessed());
	}
}
