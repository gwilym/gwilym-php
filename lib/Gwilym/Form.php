<?php

/**
* This class represents a single-step form with validation of input fields.
*
* It may be possible to extend this to support multi-step forms in the same structure, but I haven't looked at it yet
*/
abstract class Gwilym_Form extends Gwilym_FSM_Persistable
{
	protected $_request;
	
	protected $_id;

	protected $_fields = array();
	
	protected $_tokenInput = '_form_token';
	
	public function __construct (Gwilym_Request $request)
	{
		parent::__construct();
		$this->setRequest($request);
		$token = $this->_request->post($this->_tokenInput);
		if ($token) {
			$this->load($token);
		}
	}
	
	public function setRequest (Gwilym_Request $request)
	{
		$this->_request = $request;
		return $this;
	}
	
	public function getRequest ()
	{
		return $this->_request;
	}
	
	public function getResponse ()
	{
		return $this->_request->response();
	}

	public function setId ($value)
	{
		$this->_id = (string)$value;
		return $this;
	}

	public function getId ()
	{
		if ($this->_id === null) {
			$this->_id = md5(uniqid('', true));
		}
		return $this->_id;
	}
	
	public function getTokenInput ()
	{
		return $this->_tokenInput;
	}
	
	public function getNextUrl ()
	{
		return $this->getRequest()->getCurrentRoute()->uri();
	}
	
	public function getToken ()
	{
		return $this->getId();
	}

	public function addField (Gwilym_Form_Field $field, $group = '')
	{
		if ($this->getField($field->getId())) {
			throw new Gwilym_Form_Exception_DuplicateFieldId;
		}

		$field->setForm($this);
		$this->_fields[] = $field;
	}

	public function getField ($id)
	{
		$id = (string)$id;

		foreach ($this->_fields as $field) {
			if ($field->getId() === $id) {
				return $field;
			}
		}

		return false;
	}
	
	/**
	* Defines generalised states of a form processor:
	* display => process => display => process => ...
	* display => process => end.
	*
	* @return array
	*/
	protected function _getStates ()
	{
		return array(
			'initState' => 'displayState',
			'displayState' => array(
				array(
					'on' => 'isFormSubmit',
					'goto' => 'processState',
				),
				'displayState',
			),
			'processState' => array(
				array(
					'on' => 'isSubmitSuccess',
					'goto' => 'successState',
				),
				'displayState',
			),
			'successState' => null,
		);
	}
	
	protected function _isFormSubmit ()
	{
		if ($this->_request->method() != 'POST') {
			return false;
		}
		$id = $this->_request->post($this->_tokenInput);
		return (bool)$id;
	}
	
	protected function _initState ()
	{
		$this->data['message'] = 'initial state message';
	}
	
	protected function _displayState ()
	{
		$this->pause();
	}
	
	protected function _processState ()
	{
		$this->_submitSuccess = true;
		
		foreach ($this->_fields as $field) {
			$id = $field->getUniqueId();
			$value = $this->_request->post($id);
			if ($value) {
				$field->setValue($value);
			}
		}
		
		$formResults = array();
		
		foreach ($this->_fields as $field) {
			$formResults[] = $result = $field->validate();
			$failures = $result->getFailures();
			if (empty($failures)) {
				continue;
			}
			
			$this->_submitSuccess = false;
		}
		
		if (!$this->_submitSuccess) {
			$this->_onValidateFailure($formResults);
		}
	}
	
	protected $_submitSuccess = false;
	
	protected function _isSubmitSuccess ()
	{
		return $this->_submitSuccess;
	}
	
	protected function _successState ()
	{
		$this->delete();
		$this->autosave(false);
		$this->_onValidateSuccess();
	}
	
	protected function _onValidateFailure ($formResults)
	{
		// available for override by child classes
	}
	
	protected function _onValidateSuccess ()
	{
		// available for override by child classes
	}
}
