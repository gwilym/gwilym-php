<?php

class Tests_Gwilym_ArrayObject_ReadOnly extends UnitTestCase
{
    protected $_array;
    
    protected $_object;
    
    public function setUp ()
    {
        $this->_array = array(
            'foo' => 'bar',
            'alpha' => 'beta',
        );
        
        $this->_object = new Gwilym_ArrayObject_ReadOnly($this->_array);
    }
    
    public function testReadingWorks ()
    {
        $this->assertTrue(isset($this->_object['foo']));
        $this->assertTrue(array_key_exists('foo', $this->_object));
        $this->assertEqual($this->_array['foo'], $this->_object['foo']);

        $this->assertTrue(isset($this->_object['alpha']));
        $this->assertTrue(array_key_exists('alpha', $this->_object));
        $this->assertEqual($this->_array['alpha'], $this->_object['alpha']);
        
        $this->assertEqual(count($this->_array), count($this->_object));
    }
    
    public function testDirectWritingFails ()
    {
        $this->expectException('Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly');
        $this->_object['foo'] = 'baz';
    }
    
    public function testSimpleFailures ()
    {
        $methods = array(
            'asort' => null,
            'ksort' => null,
            'natcasesort' => null,
            'natsort' => null,
            'exchangeArray' => array(),
            'uasort' => 'foo',
            'uksort' => 'foo',
            'unserialize' => 'foo',
        );
        
        foreach ($methods as $method => $parameter) {
            try {
                if ($parameter !== null) {
                    $this->_object->$method($parameter);
                } else {
                    $this->_object->$method();
                }
                $this->fail('Calling ' . $method . '() did not trigger exception');
            } catch (Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly $exception) {
                $this->pass();
            }
        }
    }
    
    public function testReferenceError ()
    {
        $this->expectError('Indirect modification of overloaded element of Gwilym_ArrayObject_ReadOnly has no effect');
        $foo = &$this->_object['foo'];
        $foo = 'baz';
    }
    
    public function testUnsetFails ()
    {
        $this->expectException('Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly');
        unset($this->_object['foo']);
    }
    
    public function testDirectAppendFails ()
    {
        $this->expectException('Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly');
        $this->_object[] = 'blah';
    }
    
    public function testAppendMethodFails ()
    {
        $this->expectException('Gwilym_ArrayObject_ReadOnly_Exception_ArrayIsReadOnly');
        $this->_object->append('blah');
    }
    
    public function testIteratorIsNotReference ()
    {
        $values = array();
        foreach ($this->_object as $key => $value) {
            $value .= ' test';
            $values[$key] = $value;
        }
        $this->assertNotEqual($this->_array, $values);
    }
}
