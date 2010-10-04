<?php

/**
* FSM extension that implements pausing and resuming.
*
* Calling this pause() from within a state method will prevent any further states from being executed. The machine can then either be continued by calling step() manually or by calling resume().
*
* Alternatively, calling pause() before start() will set up the machine for running, but will not execute the first step until step() or resume() is called.
*/
abstract class Gwilym_FSM_Pausable extends Gwilym_FSM
{
	protected $_paused = false;

	public function getPaused ()
	{
		return $this->_paused;
	}
	
	public function pause ()
	{
		$this->_paused = true;
	}

	public function resume ()
	{
		if (!$this->_started) {
			$this->start();
			return;
		}

		$this->_paused = false;

		if ($this->_started) {
			while ($this->_step()) {
				// just loop until stopped
			}
		}

		$this->_stopping();
	}

	protected function _step ()
	{
		if ($this->_paused) {
			return;
		}

		return parent::_step();
	}

	public function stop ()
	{
		$this->_paused = false;
		return parent::stop();
	}
}
