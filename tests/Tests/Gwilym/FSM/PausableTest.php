<?php

class Tests_Gwilym_FSM_PausableTest extends Gwilym_FSM_Pausable
{
	protected function _getStates ()
	{
		return array(
			'one' => 'two',
			'two' => 'three',
			'three' => null,
		);
	}

	protected function _one ()
	{
		$this->pause();
	}

	protected function _two ()
	{
		$this->pause();
	}
}
