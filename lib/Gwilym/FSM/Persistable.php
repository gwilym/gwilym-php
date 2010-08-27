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

	/** @var Gwilym_FSM_Persister_Interface */
	protected $_persister;

	protected function _stopping ()
	{
		if ($this->autosave()) {
			$this->save();
		}
		return parent::_stopping();
	}
	
	public function getPersistData ()
	{
		// @todo dont like this fn name
		return array(
			'id' => $this->_id,
			'started' => $this->_started,
			'state' => $this->_state,
			'data' => $this->data,
		);
	}
	
	public function unpersist ($data)
	{
		// @todo dont like this fn name
		$this->_id = $data['id'];
		$this->_started = $data['started'];
		$this->_state = $data['state'];
		$this->data = $data['data'];
		$this->pause();
	}
	
	public function setPersister (Gwilym_FSM_Persister_Interface $value) {
		$this->_persister = $value;
		return $this;
	}
	
	public function getPersister ()
	{
		return $this->_persister;
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
		$this->_autosave = (bool)$autosave;
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
	public function save ()
	{
		return $this->getPersister()->save($this);
	}

	/**
	* Load the state and data information of an existing FSM into this instance
	*
	* @param string $id
	* @return void
	*/
	public function load ($id = null)
	{
		if ($id === null) {
			$id = $this->id();
		}
		return $this->getPersister()->load($this, $id);
	}
	
	/**
	* Deletes all state and data information for the current FSM.
	*
	* @return void
	*/
	public function delete ()
	{
		return $this->getPersister()->delete($this);
	}
}
