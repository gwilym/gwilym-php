<?php

class Tests_Gwilym_FSM_PausedTest extends Gwilym_FSM_Paused
{
	public $ran = false;
	
	protected function _getStates ()
	{
		return array(
			'go' => null,
		);
	}
	
	protected function _go ()
	{
		$this->ran = true;
	}
}
