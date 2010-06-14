<?php

/**
* FSM extension that implements persistence via PHP session
*/
abstract class Gwilym_FSM_Persistable_Session extends Gwilym_FSM_Persistable
{
	/** @var Gwilym_request */
	protected $_request;
	public $name;

	public function __construct (Gwilym_Request $request)
	{
		parent::__construct();
		$this->name = get_class($this);
		$this->_request = $request;
		$this->_id = md5($this->name . $this->_request->sessionId() . $this->_request->server('HTTP_USER_AGENT'));
	}

	/**
	* Saves this FSM's state and data information to the keystore
	*
	* @return void
	*/
	public function save ()
	{
		$this->_request->session('gwilym,fsm,' . $this->name . ',' . $this->id(), array(
			'id' => $this->id(),
			'started' => $this->_started,
			'state' => $this->_state,
			'data' => $this->data,
		));
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
			$id = $this->_id;
		}

		$form = $this->_request->session('gwilym,fsm,' . $this->name . ',' . $id);
		if (!$form || $form['id'] !== $id) {
			return false;
		}

		$this->_id = $id;
		$this->_started = $form['started'];
		$this->_state = $form['state'];
		$this->data = $form['data'];
		$this->pause();
		return true;
	}
}
