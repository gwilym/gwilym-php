<?php

/**
* Implements the base of a finite state machine / state executor in PHP. Extend on this base class to implement specific functionality.
*/
abstract class Gwilym_FSM implements Gwilym_FSM_Interface
{
	/** @var bool whether the fsm is started (running) or not */
	protected $_started = false;

	/** @var array set of state -> transition data */
	protected $_states;

	/** @var string name of current state */
	protected $_state;

	/** @return array function for extendees to return a set of data for storing in _states */
	abstract protected function _getStates ();

	public function __construct ()
	{
		$this->_states = $this->_getStates();
	}

	/** @var starts the finite state machine, if not already started */
	public function start ()
	{
		if ($this->_started) {
			return;
		}

		$this->_started = true;
		$this->_state = null;

		while ($this->step()) {
			// just loop until stopped
		}

		$this->_exiting();
	}

	/**
	* This internal method is called whenever the FSM is exiting a loop of iterations (such as after a call to start(), or Pausable's resume()).
	*
	* It mainly exists to allow Persistable to implement auto-saving when a loop finishes, but may have other uses, too.
	*/
	protected function _exiting ()
	{
		// reserved for extendees
	}

	/** @return string returns the next state based on the current state and current conditions, or null if there is no next suitable state */
	protected function _getNextState ()
	{
		if ($this->_state === null) {
			// first iteration, pick first state and return
			reset($this->_states);
			return key($this->_states);
		}

		$transitions = $this->_states[$this->_state];

		if ($transitions === null) {
			// end of transition list
			return;
		}

		if (is_string($transitions)) {
			// direct transition
			return $transitions;
		}

		if (is_array($transitions)) {
			// conditional transition
			foreach ($transitions as $next) {
				if (is_string($next)) {
					// default transition, usually found after one or more conditionals
					return $next;
				}

				if (!$this->{'_' . $next['on']}()) {
					// condition not met
					continue;
				}

				return $next['goto'];
			}
		}
	}

	/**
	* If started, steps through the machine's state once
	*
	* @return string the new current state name, that is, the state that was just executed by this call to step()
	*/
	public function step ()
	{
		if (!$this->_started) {
			return;
		}

		$state = $this->_getNextState();

		if (!$state) {
			$this->stop();
			return;
		}

		$method = '_' . $state;
		if (method_exists($this, $method)) {
			$this->$method();
		}

		$this->_state = $state;
		return true;
	}

	/** @return bool true if started otherwise false */
	public function started ()
	{
		return $this->_started;
	}

	/**
	* If started, stops the current state machine, forgetting current-state information
	*
	* @return void
	*/
	public function stop ()
	{
		if (!$this->_started) {
			return;
		}

		$this->_started = false;
		$this->_state = null;

		$this->_exiting();
	}
}
