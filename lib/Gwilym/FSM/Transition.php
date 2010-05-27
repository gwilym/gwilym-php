<?php

class Gwilym_FSM_Transition
{
	protected $_name;
	protected $_from;
	protected $_to;

	public function __construct (Gwilym_FSM_State $from, Gwilym_FSM_State $to, $name = null)
	{
		$this->_from = $from;
		$this->_to = $to;
		$this->_name = $name;
	}

	public function name ()
	{
		return $this->_name;
	}

	public function from ()
	{
		return $this->_from;
	}

	public function to ()
	{
		return $this->_to;
	}

	public function transition ($name, $to = null)
	{
		return $this->_from->transition($name, $to);
	}

	public function state ($name = null)
	{
		return $this->_from->fsm()->state($name);
	}

	protected $_execute = array();

	public function execute ($callback = null)
	{
		if (func_num_args()) {
			$this->_execute[] = $callback;
			return $this;
		}

		foreach ($this->_execute as $callback) {
			if (call_user_func($callback, $this) === false) {
				return false;
			}
		}

		return true;
	}
}
