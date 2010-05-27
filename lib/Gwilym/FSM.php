<?php

/**
* e.g.
*
*	$fsm = new Gwilym_FSM();
*
*	$fsm
*		->state('loop')
*			->defaultTransition('loop')
*			->transition('99', 'end')
*		->state('end')
*			->stop(true);
*
*	$fsm->start();
*
*	$i = 0;
*	while (++$i && $fsm->running()) {
*		$fsm->input($i);
*	}
*
* 	$this->assertEqual(99, $i);
*
*/
class Gwilym_FSM
{
	/** @var Gwilym_FSM_State */
	protected $_state;

	/** @var Gwilym_FSM_State */
	protected $_initial;

	/** @var Array<Gwilym_FSM_State> */
	protected $_states;

	/** @var bool */
	protected $_running = false;

	protected $_payload;

	/** @var Gwilym_Event */
	protected $_event;

	public function __construct ()
	{
		$this->_event = Gwilym_Event::factory();
	}

	public function start ($payload = null)
	{
		if ($this->_running) {
			return;
		}

		$this->_payload = $payload;
		$this->_running = true;
		$this->_state = $this->_initial;

		$this->_event->trigger($this, 'start', $this);

		$this->_state->enter();
	}

	public function running ()
	{
		return $this->_running;
	}

	public function stop ()
	{
		$this->_running = false;
		$this->_state->leave();
		$this->_event->trigger($this, 'stop', $this);
	}

	public function input ($symbol)
	{
		if (!$this->_running) {
			return false;
		}

		$transition = $this->_state->transition($symbol);
		if (!$transition) {
			$transition = $this->_state->defaultTransition();
		}

		if (!$transition) {
			return false;
		}

		if ($transition->execute() === false) {
			return false;
		}

		$this->_state->leave();
		$this->_state = $transition->to();
		$this->_state->enter();
	}

	public function state ($name = null)
	{
		if (func_num_args() === 0) {
			if (!$this->_running) {
				return false;
			}
			return $this->_state;
		}

		if (!isset($this->_states[$name])) {
			$this->_states[$name] = new Gwilym_FSM_State($this, $name);
			if (!$this->_initial) {
				$this->_initial = $this->_states[$name];
			}
		}

		return $this->_states[$name];
	}
}
