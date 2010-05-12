<?php

class Gwilym_View_Twig extends Gwilym_View
{
	protected $_file;
	protected $_paths;
	protected $_loader;
	protected $_environment;
	protected $_template;

	public function __construct ($file, $paths = array())
	{
		if (!count($paths)) {
			$paths[] = GWILYM_APP_DIR . '/View/';
		}

		$this->_paths = $paths;
		$this->_file = $file;

		$this->_loader = new Twig_Loader_Filesystem($this->_paths);

		$this->_environment = new Twig_Environment($this->_loader, array(
			'trim_blocks' => true,
			'cache' => GWILYM_CACHE_DIR . '/twig/view/',
			'auto_reload' => true,
		));

		$this->_environment->addExtension(new Twig_Extension_Escaper);
	}

	protected function _preRender ()
	{
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
