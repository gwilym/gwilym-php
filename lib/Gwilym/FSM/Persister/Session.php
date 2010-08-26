<?php

class Gwilym_FSM_Persister_Session implements Gwilym_FSM_Persister_Interface
{
	protected $_request;
	
	protected $_key;
	
	public function setRequest (Gwilym_Request $value)
	{
		$this->_request = $value;
		return $this;
	}
	
	public function getRequest ()
	{
		return $this->_request;
	}
	
	public function save (Gwilym_FSM $fsm)
	{
		$this->_request->session(
			$this->_getKey($fsm),
			serialize($fsm->getPersistData())
		);
	}
	
	public function load (Gwilym_FSM $fsm, $id)
	{
		$fsm->unpersist(unserialize($this->_request->session($this->_getKey($fsm, $id))));
	}
	
	public function delete (Gwilym_FSM $fsm)
	{
		$this->_request->session('');
		unset($_SESSION[$this->_getKey($fsm)]);
	}
	
	protected function _getKey (Gwilym_FSM $fsm, $id = null)
	{
		if ($id === null) {
			$id = $fsm->id();
		}
		return 'gwilym,fsm,' . get_class($fsm) . ',' . md5($id);
	}
}
