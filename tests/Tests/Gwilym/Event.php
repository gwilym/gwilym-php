<?php

function test_gwilym_event_function_callback (Gwilym_Event $event)
{
	$event->data++;
}

class Tests_Gwilym_Event extends UnitTestCase
{
	protected function generateRandomEventId ()
	{
		// 'random'
		return md5(uniqid('', true));
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


	public function testBindToFunction ()
	{
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, 'test_gwilym_event_function_callback');
		$event = Gwilym_Event::trigger($id, 1);
		$this->assertEqual(2, $event->data);
	}

	public function testPersistentBindToFunction ()
	{
		$this->skip();
	}

	public function testBindToStaticMethod ()
	{
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = Gwilym_Event::trigger($id, 1);
		$this->assertEqual(2, $event->data);
	}

	public function testPersistentBindToStaticMethod ()
	{
		$this->skip();
	}

	public function testBindToInstanceMethod ()
	{
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, array($this, 'instanceMethodCallback'));
		$event = Gwilym_Event::trigger($id, 1);
		$this->assertEqual(2, $event->data);
	}

	public function testBindToClosure ()
	{
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, function($event){
			$event->data++;
		});
		$event = Gwilym_Event::trigger($id, 0);
		$this->assertEqual(1, $event->data);
	}

	public function testBindOnInstanceEvent ()
	{
		// make three binds but only one specific to this instance and then trigger - the resulting data should show only one binding was fired
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($this, $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		Gwilym_Event::bind(new stdClass(), $id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodCallbackForInstanceEvent'));
		$event = Gwilym_Event::trigger($this, $id, 1);
		$this->assertEqual(2, $event->data);
	}

	public function testPreventDefault ()
	{
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodPreventsDefault'));
		$event = Gwilym_Event::trigger($id);
		$this->assertTrue($event->isDefaultPrevented());
		$this->assertFalse($event->isPropagationStopped());
	}

	public function testStopPropagation ()
	{
		// bind twice, but the first should prevent the second, so default will never be prevented
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodStopsPropagation'));
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodPreventsDefault'));
		$event = Gwilym_Event::trigger($id);
		$this->assertFalse($event->isDefaultPrevented());
		$this->assertTrue($event->isPropagationStopped());
	}

	public function testCannotPersistClosureBinding ()
	{
		$this->skip();
	}

	public function testCannotPersistInstanceBinding ()
	{
		$this->skip();
	}

	public function testCannotPersistInstanceEvent ()
	{
		$this->skip();
	}

	public function testMultipleBindings ()
	{
		$this->skip();
	}

	public function testDuplicateBindings ()
	{
		$this->skip();
	}

	public function testBindingReturningFalsePreventsDefaultAndStopsPropagation ()
	{
		// if staticMethodCallback is fired, event->data will increase and this will fail
		$id = $this->generateRandomEventId();
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodReturnsFalse'));
		Gwilym_Event::bind($id, array(__CLASS__, 'staticMethodCallback'));
		$event = Gwilym_Event::trigger($id, 1);
		$this->assertEqual(1, $event->data);
		$this->assertTrue($event->isDefaultPrevented());
		$this->assertTrue($event->isPropagationStopped());
	}
}
