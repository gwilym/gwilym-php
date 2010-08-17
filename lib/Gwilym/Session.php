<?php

/**
* Abstracts access to PHP's sessions functionality, allowing for lazy-starting of session.
*/
class Gwilym_Session extends Gwilym_ArrayObject_ByReference
{
	/** @var string */
	protected $_name;

	/** @var string */
	protected $_id;

	/** @var bool */
	protected $_started = false;

	/**
	* @param mixed $input ignored
	* @param mixed $flags see ArrayObject documentation
	* @param mixed $iterator_class see ArrayObject documentation
	* @return Gwilym_Session
	*/
	public function __construct ($input = null, $flags = null, $iterator_class = null)
	{
		$this->_name = session_name();

		if ($iterator_class === null) {
			parent::__construct(array(), $flags);
		} else {
			parent::__construct(array(), $flags, $iterator_class);
		}
	}

	/**
	* Override of FirstRead's _setAccessed method to implement lazy-starting of the session when the ArrayObject is first accessed
	*
	* @return bool true if flag was changed, otherwise false (ie. flag already set)
	*/
	protected function _setAccessed ()
	{
		$result = parent::_setAccessed();
		if ($result) {
			// first access of session data
			if ($this->_name !== session_name()) {
				session_name($this->_name);
			}

			if ($this->_id !== null) {
				session_id($this->_id);
			}

			session_start();
			$this->_started;
			$this->exchangeArray($_SESSION);
		}
		return $result;
	}

	/** @return bool true if session has been started for this PHP execution */
	public function started ()
	{
		return $this->_started;
	}

	/**
	* Get or set the current session name
	*
	* @param string $name
	* @return string|Gwilym_Session
	*/
	public function name ($name = null)
	{
		if ($name === null) {
			return $this->_name;
		}

		$this->_name = $name;
		return $this;
	}

	/**
	* Get or set the current session id
	*
	* @param string $id
	* @return string|null|Gwilym_Session
	*/
	public function id ($id = null)
	{
		if ($id === null) {
			return $this->_id;
		}

		$this->_id = $id;
		return $this;
	}
}
