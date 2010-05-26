<?php

class Gwilym_Event_Test extends Gwilym_Event
{
	/** for testing, a public accessor for _flushBindings */
	public function flushBindings ()
	{
		$this->_flushBindings();
	}
}

function test_gwilym_event_function_callback (Gwilym_Event $event)
{
	$event->data++;
}

class Tests_Gwilym_Event extends UnitTestCase
{
	protected $_event;

	public function setUp ()
	{
		$this->_event = new Gwilym_Event_Test();
	}

	protected function generateRandomEventId ()
	{
		// 'random'
		return 'test' . md5(uniqid('', true));
	}

	public static function staticMethodPreventsDefault (Gwilym_Event $event)
	{
		$event->preventDefault();
	}

	public static function staticMethodStopsPropagation (Gwilym_Event $event)
	{
		$event->stopPropagation();
	}

	public static function staticMethodCallback ($event)
	{
		$event->data++;
	}

	public static function staticMethodCallbackForInstanceEvent ($event)
	{
		$event->data++;
	}

	public static function staticMethodReturnsFalse ($event)
	{
		return false;
	}

	public function instanceMethodCallback ($event)
	{
		$event->data++;
	}


	public function testFactory ()
	{
		$this->assertIsA(Gwilym_Event::factory(), 'Gwilym_Event');
	}

	public function testType ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, 'test_gwilym_event_function_callback');
		$event = $this->_event->trigger($id);
		$this->assertEqual($id, $event->type());
	}

	public function testBindToFunctionAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, 'test_gwilym_event_function_callback');
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, 'test_gwilym_event_function_callback');
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testPersistentBindToFunctionAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, 'test_gwilym_event_function_callback', true);
		$this->_event->flushBindings();
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, 'test_gwilym_event_function_callback');
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testBindToStaticMethodAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testPersistentBindToStaticMethodAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'), true);
		$this->_event->flushBindings();
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testBindToInstanceMethodAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array($this, 'instanceMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, array($this, 'instanceMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testBindToClosureAndUnbind ()
	{
		$id = $this->generateRandomEventId();
		$closure = function($event){
			$event->data++;
		};
		$this->_event->bind($id, $closure);
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($id, $closure);
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testBindOnInstanceEventAndUnbind ()
	{
		// make three binds but only one specific to this instance and then trigger - the resulting data should show only one binding was fired
		$id = $this->generateRandomEventId();
		$this->_event->bind($this, $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		$this->_event->bind(new stdClass, $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		$event = $this->_event->trigger($this, $id, 1);
		$this->assertEqual(2, $event->data);
		$this->_event->unbind($this, $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		$event = $this->_event->trigger($this, $id, 1);
		$this->assertEqual(1, $event->data);
	}

	public function testPreventDefault ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodPreventsDefault'));
		$event = $this->_event->trigger($id);
		$this->assertTrue($event->isDefaultPrevented());
		$this->assertFalse($event->isPropagationStopped());
	}

	public function testStopPropagation ()
	{
		// bind twice, but the first should prevent the second, so default will never be prevented
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodStopsPropagation'));
		$this->_event->bind($id, array(__CLASS__, 'staticMethodPreventsDefault'));
		$event = $this->_event->trigger($id);
		$this->assertFalse($event->isDefaultPrevented());
		$this->assertTrue($event->isPropagationStopped());
	}

	public function testCannotPersistClosureBinding ()
	{
		$this->expectException('Gwilym_Event_Exception_CannotPersistClosureBinding');
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, function($event){
			$event->data++;
		}, true);
	}

	public function testCannotPersistInstanceBinding ()
	{
		$this->expectException('Gwilym_Event_Exception_CannotPersistInstanceBinding');
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array($this, 'instanceMethodCallback'), true);
	}

	public function testCannotPersistInstanceEvent ()
	{
		$this->expectException('Gwilym_Event_Exception_CannotPersistInstanceEvent');
		$id = $this->generateRandomEventId();
		$this->_event->bind($this, $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'), true);
	}

	public function testMultipleBindings ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'));
		$this->_event->bind($id, array(__CLASS__, 'staticMethodPreventsDefault'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(2, $event->data);
		$this->assertTrue($event->isDefaultPrevented());
	}

	public function testDuplicateBindings ()
	{
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'));
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(3, $event->data);
	}

	public function testBindingReturningFalsePreventsDefaultAndStopsPropagation ()
	{
		// if staticMethodCallback is fired, event->data will increase and this will fail
		$id = $this->generateRandomEventId();
		$this->_event->bind($id, array(__CLASS__, 'staticMethodReturnsFalse'));
		$this->_event->bind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = $this->_event->trigger($id, 1);
		$this->assertEqual(1, $event->data);
		$this->assertTrue($event->isDefaultPrevented());
		$this->assertTrue($event->isPropagationStopped());
	}
}
