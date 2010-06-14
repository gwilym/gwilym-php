<?php

/**
* Minor extension of Gwilym_FSM_Pausable which always starts in the paused state
*/
abstract class Gwilym_FSM_Paused extends Gwilym_FSM_Pausable
{
	public function start ()
	{
		if (!$this->_started) {
			$this->pause();
		}

		parent::start();
	}
}
