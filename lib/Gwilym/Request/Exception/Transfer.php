<?php

class Gwilym_Request_Exception_Transfer extends Gwilym_Request_Exception
{
	protected $_to;
	protected $_args = array();
	
	public function to ($value = null)
	{
		if (!func_num_args()) {
			return $this->_to;
		}
		
		$this->_to = (string)$value;
		return $this;
	}
	
	public function args ($value = null)
	{
		if (!func_num_args()) {
			return $this->_args;
		}
		
		$this->_args = $value;
		return $this;
	}
}
