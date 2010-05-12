<?php

class Gwilym_View_Twig extends Gwilym_View
{
	protected $_file;

	protected $_loader;
	protected $_environment;
	protected $_template;

	public function __construct ($file)
	{
		$this->_file = $file;
	}

	protected function _preRender ()
	{
		$this->_loader = new Twig_Loader_Filesystem(array(
			GWILYM_APP_DIR . '/View/',
		));

		$this->_environment = new Twig_Environment($this->_loader, array(
			'trim_blocks' => true,
			'cache' => GWILYM_CACHE_DIR . '/twig/view/',
			'auto_reload' => true,
		));

		$this->_environment->addExtension(new Twig_Extension_Escaper);

		$this->_template = $this->_environment->loadTemplate($this->_file);
	}

	public function display ()
	{
		$this->_preRender();
		$this->_template->display($this->data);
	}

	public function render ()
	{
		$this->_preRender();
		$this->_template->render($this->data);
	}
}
