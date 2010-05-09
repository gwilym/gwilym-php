<?php

class Gwilym_View_Twig extends Gwilym_View
{
	protected $_template;
	protected $_twigLoader;
	protected $_twigEnvironment;
	protected $_twigTemplate;

	public function __construct ($template)
	{
		$this->_template = $template;
	}

	protected function _preRender ()
	{
		$this->_twigLoader = new Twig_Loader_Filesystem(array(
			GWILYM_APP_DIR . '/View/',
		));

		$this->_twigEnvironment = new Twig_Environment($this->_twigLoader, array(
			'trim_blocks' => true,
			'cache' => GWILYM_CACHE_DIR . '/twig/view/',
			'auto_reload' => true,
		));

		$this->_twigTemplate = $this->_twigEnvironment->loadTemplate($this->_template);
	}

	public function display ()
	{
		$this->_preRender();
		$this->_twigTemplate->display($this->data);
	}

	public function render ()
	{
		$this->_preRender();
		$this->_twigTemplate->render($this->data);
	}
}
