<?php

class Gwilym_FSM_State
{
	/** @var Gwilym_FSM */
	protected $_fsm;

	/** @var string */
	protected $_name;

	/** @var array */
	protected $_transitions = array();

	/** @var Gwilym_FSM_Transition */
	protected $_defaultTransition;

	/** @var Gwilym_Event */
	protected $_event;

	/** @var bool */
	protected $_stop = false;

	public function __construct (Gwilym_FSM $fsm, $name)
	{
		$this->_event = Gwilym_Event::factory();
		$this->_fsm = $fsm;
		$this->_name = $name;
	}

	public function fsm ()
	{
		return $this->_fsm;
	}

	public function name ()
	{
		return $this->_name;
	}

	public function transition ($name, $to = null)
	{
		if (func_num_args() === 1)
		{
			if (!isset($this->_transitions[$name])) {
				return false;
			}
			return $this->_transitions[$name];
		}
		return $this->_transitions[$name] = new Gwilym_FSM_Transition($this, $this->fsm()->state($to), $name);
	}

	public function defaultTransition ($to = null)
	{
		if (func_num_args()) {
			$this->_defaultTransition = new Gwilym_FSM_Transition($this, $this->fsm()->state($to));
		}
		return $this->_defaultTransition;
	}

	protected $_enter = array();

	public function enter ($callback = null)
	{
		if (func_num_args()) {
			$this->_enter[] = $callback;
			return $this;
		}

		foreach ($this->_enter as $callback) {
			if (call_user_func($callback, $this) === false) {
				break;
			}
		}

		$this->_event->trigger($this->_fsm, 'enter', $this->_fsm);
		$this->_event->trigger($this, 'enter', $this);

		if ($this->_stop) {
			$this->_fsm->stop();
		}
	}

	protected $_leave = array();

	public function leave ($callback = null)
	{
		if (func_num_args()) {
			$this->_leave[] = $callback;
			return $this;
		}

		foreach ($this->_leave as $callback) {
			if (call_user_func($callback, $this) === false) {
				break;
			}
		}

		$this->_event->trigger($this, 'leave', $this);
		$this->_event->trigger($this->_fsm, 'leave', $this->_fsm);
	}

	/**
	* mark this state as the end, entering the state will stop the FSM
	*
	* @return
	*/
	public function stop ($stop = null)
	{
		if (func_num_args())
		{
			$this->_stop = $stop;
			return $this;
		}
		return $this->_stop;
	}
}
