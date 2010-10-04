<?php

class Tests_Gwilym_FSM extends UnitTestCase
{
    public function testBasicFsm ()
    {
        $fsm = new Tests_Gwilym_FSM_BasicFsmTest;
        $fsm->setPayload('0')
            ->start();
        $this->assertEqual('0123', $fsm->getPayload());
    }
    
    public function testStoppingFsm ()
    {
        $fsm = new Tests_Gwilym_FSM_StoppingFsmTest;
        $fsm->setPayload('0')
            ->start();
        $this->assertEqual('01', $fsm->getPayload());
    }
    
    public function testLoopingFsm ()
    {
        $fsm = new Tests_Gwilym_FSM_LoopingFsmTest;
        $fsm->setFrom(10)
            ->setTo(20)
            ->start();
        $this->assertEqual(20, $fsm->getCounter());
        $this->assertEqual(10, $fsm->getIterations());
    }
}
