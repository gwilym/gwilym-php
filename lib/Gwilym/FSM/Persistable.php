<?php

/**
* FSM extension that implements persistence via Gwilym_KeyStore
*/
abstract class Gwilym_FSM_Persistable extends Gwilym_FSM_Pausable
{
	/** @var string random / unique id assigned to this fsm instance */
	protected $_id;

	/** @var array set of data which will be persisted when save() is called */
	public $data = array();

	public function __construct ()
	{
		parent::__construct();
		$this->_id = md5(uniqid('', true));
	}

	/** @return string random / unique id assigned to this fsm instance */
	public function id ()
	{
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
