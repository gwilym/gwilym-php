<?php

/**
* FSM extension that implements persistence via Gwilym_KeyStore
*/
abstract class Gwilym_FSM_Persistable_KeyStore extends Gwilym_FSM_Persistable
{
	/** @var Gwilym_KeyStore_Interface */
	protected $_keystore;

	/**
	* Gets or sets the keystore instance to use for data persistence
	*
	* @param Gwilym_KeyStore_Interface $keystore optional keystore instance to use
	* @return Gwilym_KeyStore_Interface when getting or $this when setting
	*/
	public function keystore (Gwilym_KeyStore_Interface $keystore = null)
	{
		if ($keystore !== null) {
			$this->_keystore = $keystore;
			return $this;
		}

		if ($this->_keystore === null) {
			$this->_keystore = Gwilym_KeyStore::factory();
		}

		return $this->_keystore;
	}

	/**
	* Saves this FSM's state and data information to the keystore
	*
	* @return void
	*/
	public function save ()
	{
		$this->keystore()->set('gwilym,fsm,' . $this->_id, $this->_id);

		$this->_keystore->multiDelete('gwilym,fsm,' . $this->_id . ',*');
		$this->_keystore->set('gwilym,fsm,' . $this->_id . ',started', serialize($this->_started));
		$this->_keystore->set('gwilym,fsm,' . $this->_id . ',state', $this->_state);
		foreach ($this->data as $key => $value) {
			$this->_keystore->set('gwilym,fsm,' . $this->_id . ',data,' . $key, serialize($value));
		}
	}

	/**
	* Load the state and data information of an existing FSM into this instance
	*
	* @param string $id
	* @return void
	*/
	public function load ($id)
	{
		$loaded = $this->keystore()->get('gwilym,fsm,' . $id);
		if ($loaded !== $id) {
			throw new Gwilym_FSM_Persistable_Exception_NotFound($id);
		}
		unset($loaded);

		$this->_id = $id;
		$this->_started = unserialize($this->_keystore->get('gwilym,fsm,' . $id . ',started'));
		$this->_state = $this->_keystore->get('gwilym,fsm,' . $id . ',state');

		$this->data = array();
		$prefix = 'gwilym,fsm,' . $id . ',data,';
		$prefixLength = strlen($prefix);
		$data = $this->_keystore->multiGet($prefix . '*');
		foreach ($data as $key => $value) {
			$this->data[substr($key, $prefixLength)] = $value;
		}
		$this->pause();
	}
}
