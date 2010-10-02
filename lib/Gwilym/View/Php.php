<?php

class Gwilym_View_Php extends Gwilym_View
{
	protected $_template;

	public function __construct (Gwilym_Controller $controller, $template)
	{
		parent::__construct($controller);
		$this->_template = $template;
	}

	public function display ()
	{
		require GWILYM_APP_DIR . '/View/' . $this->_template;
	}

	public function render ()
	{
		ob_start();
		$this->display();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
