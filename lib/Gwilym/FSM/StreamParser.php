<?php

abstract class Gwilym_FSM_StreamParser extends Gwilym_FSM
{
	protected $_originalStream;
	protected $_stream;
	protected $_opener;

	public function __construct ($stream)
	{
		parent::__construct();
		$this->_originalStream = $stream;
	}

	public function start ()
	{
		if (!$this->_started) {
			if (is_resource($this->_originalStream)) {
				$this->_stream = $this->_originalStream;
				$this->_opener = false;
			} else {
				$this->_stream = fopen($this->_originalStream, 'r');
				$this->_opener = true;
			}

			fseek($this->_stream, 0);
		}

		parent::start();
	}

	public function stop ()
	{
		if ($this->_started) {
			if ($this->_opener) {
				fclose($this->_stream);
			}
			$this->_stream = null;
		}

		parent::stop();
	}
}
