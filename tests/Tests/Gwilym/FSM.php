<?php

class Tests_Gwilym_FSM extends UnitTestCase
{
	public function testBasicFsm ()
	{
		$fsm = new Tests_Gwilym_FSM_BasicFsmTest;
		$fsm->setPayload('0')
			->start();
		$this->assertFalse($fsm->getStarted());
		$this->assertEqual('0123', $fsm->getPayload());
	}
	
	public function testStoppingFsm ()
	{
		$fsm = new Tests_Gwilym_FSM_StoppingFsmTest;
		$fsm->setPayload('0')
			->start();
		$this->assertFalse($fsm->getStarted());
		$this->assertEqual('01', $fsm->getPayload());
	}
	
	public function testLoopingFsm ()
	{
		$fsm = new Tests_Gwilym_FSM_LoopingFsmTest;
		$fsm->setFrom(10)
			->setTo(20)
			->start();
		$this->assertFalse($fsm->getStarted());
		$this->assertEqual(20, $fsm->getCounter());
		$this->assertEqual(10, $fsm->getIterations());
	}
	
	public function testPausableFsm ()
	{
		$fsm = new Tests_Gwilym_FSM_PausableTest;
		$fsm->resume();
		$this->assertTrue($fsm->getPaused());
		$this->assertTrue($fsm->getStarted());
		$fsm->resume();
		$this->assertTrue($fsm->getPaused());
		$this->assertTrue($fsm->getStarted());
		$fsm->resume();
		$this->assertFalse($fsm->getPaused());
		$this->assertFalse($fsm->getStarted());
	}
	
	public function testPausedFsm ()
	{
		$fsm = new Tests_Gwilym_FSM_PausedTest;
		$fsm->start();
		$this->assertTrue($fsm->getPaused());
		$this->assertFalse($fsm->ran);
	}
}
