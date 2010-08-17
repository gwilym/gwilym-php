<?php

/**
* FSM extension that implements persistence via Gwilym_KeyStore
*/
abstract class Gwilym_FSM_Persistable extends Gwilym_FSM_Pausable
{
	/** @var string random / unique id assigned to this fsm instance */
	protected $_id;

	/** @var bool true if the FSM's state should be saved after a loop of iterations end */
	protected $_autosave = true;

	/** @var array set of data which will be persisted when save() is called */
	public $data = array();

	protected function _exiting ()
	{
		if ($this->autosave()) {
			$this->save();
		}
		return parent::_exiting();
	}

	/**
	* Gets or set's this pausable finite state machine's setting for auto saving.
	*
	* With this set to true, a machine's state will be automatically saved after a call to start() or resume() ends (either at the end of the machine's set of states or after being paused), or after a call to stop().
	*
	* @param bool $autosave
	* @return bool
	*/
	public function autosave ($autosave = null)
	{
		if ($autosave === null) {
			return $this->_autosave;
		}
		$this->_autosave = $autosave;
		return $this;
	}

	/** @return string random / unique id assigned to this fsm instance */
	public function id ()
	{
		if ($this->_id === null) {
			$this->_id = md5(uniqid('', true));
		}
		return $this->_id;
	}

	/**
	* Saves this FSM's state and data information to the keystore
	*
	* @return void
	*/
	abstract public function save ();

	/**
	* Load the state and data information of an existing FSM into this instance
	*
	* @param string $id
	* @return void
	*/
	abstract public function load ($id);
}
