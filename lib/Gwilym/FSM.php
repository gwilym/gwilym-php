<?php

abstract class Gwilym_FSM implements Gwilym_FSM_Interface
{
	protected $_started = false;
	protected $_pause = false;
	protected $_payload = array();
	protected $_states;
	protected $_state;
	protected $_nextstate;

	abstract protected function _getStates ();

	public function __construct ()
	{
		// reserved
	}

	public function start ()
	{
		if ($this->_started) {
			return;
		}

		$this->_started = true;

		$this->_states = $this->_getStates();
		reset($this->_states);
		$this->_nextstate = key($this->_states);

		while ($this->_nextstate && !$this->_pause) {
			$this->step();
		}

		return;
	}

	public function step ()
	{
		if (is_array($this->_nextstate)) {
			foreach ($this->_nextstate as $next) {
				if (is_string($next)) {
					$this->_state = $next;
					break;
				}

				if (!$this->{'_' . $next['on']}()) {
					continue;
				}

//				echo " > " . $next['on'] . "\n";
				$this->_state = $next['goto'];
				break;
			}
		} else if (is_string($this->_nextstate)) {
			$this->_state = $this->_nextstate;
		} else {
			$this->_state = null;
			$this->_nextstate = null;
			$this->stop();
			return;
		}

//		echo $this->_state . "\n";
		$method = '_' . $this->_state;
		if (method_exists($this, $method)) {
			$this->$method();
		}

		$this->_nextstate = $this->_states[$this->_state];
	}

	public function started ()
	{
		return $this->_started;
	}

	public function stop ()
	{
		if (!$this->_started) {
			return;
		}

		$this->_started = false;
	}

	public function pause ()
	{
		$this->_pause = true;
	}

	public function resume ()
	{
		$this->_pause = false;
	}
}
