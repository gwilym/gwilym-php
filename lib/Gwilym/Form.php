<?php

/**
* This class represents a single-step form with validation of input fields.
*
* It may be possible to extend this to support multi-step forms in the same structure, but I haven't looked at it yet
*/
abstract class Gwilym_Form extends Gwilym_FSM_Persistable
{
	/** @var Gwilym_Request a reference to the current request object to be passed in on construct */
	protected $_request;
	
	/** @var array<Gwilym_Form_Field> a set of form field instances for validation purposes */
	protected $_fields = array();
	
	/** @var string the POST field to look for which will contain this form's id */
	protected $_idInput = '_form_id';
	
	/** @var string the POST field to look for which will contain this form's security token */
	protected $_tokenInput = '_form_token';
	
	protected $_token;
	
	public function __construct (Gwilym_Request $request, $id = null, $token = null)
	{
		parent::__construct($id);
		
		$this->_token = $token ? $token : md5(uniqid('', true));
		$this->setRequest($request);
		
		if ($id !== null) {
			$this->load($id, $token);
		}
		
		$this->resume();
	}
	
	public function getPersistData ()
	{
		$return = parent::getPersistData();
		$return['token'] = $this->_token;
		return $return;
	}
	
	public function unpersist ($data)
	{
		$this->_token = $data['token'];
		parent::unpersist($data);
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
	
	public function getIdInput ()
	{
		return $this->_idInput;
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
		return $this->_token;
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
	
	public function load ($id, $token = null)
	{
		try {
			parent::load($id);
		} catch (Gwilym_FSM_Persister_Exception_FsmNotFound $exception) {
			throw new Gwilym_Form_Exception_FormNotFound;
		}
		if ($token !== null && strcmp($this->getToken(), (string)$token) !== 0) {
			throw new Gwilym_Form_Exception_InvalidToken;
		}
		return true;
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
		$this->data['form']['message'] = 'initial state message';
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
