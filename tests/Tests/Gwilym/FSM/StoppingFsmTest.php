<?php

class Tests_Gwilym_FSM_StoppingFsmTest extends Tests_Gwilym_FSM_BasicFsmTest
{
	protected function _one ()
	{
		parent::_one();
		$this->stop();
	}
}
