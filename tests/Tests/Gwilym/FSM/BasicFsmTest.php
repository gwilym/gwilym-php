<?php

class Tests_Gwilym_FSM_BasicFsmTest extends Gwilym_FSM
{
	protected $_payload;
	
	protected function _getStates ()
	{
		return array(
			'one' => 'two',
			'two' => 'three',
			'three' => null,
		);
	}
	
	public function setPayload ($value)
	{
		$this->_payload = $value;
		return $this;
	}
	
	public function getPayload ()
	{
		return $this->_payload;
	}
	
	protected function _one ()
	{
		$this->_payload .= '1';
	}
	
	protected function _two ()
	{
		$this->_payload .= '2';
	}
	
	protected function _three ()
	{
		$this->_payload .= '3';
	}
}
