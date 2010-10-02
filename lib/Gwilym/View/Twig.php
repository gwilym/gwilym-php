<?php

class Gwilym_View_Twig extends Gwilym_View
{
	protected $_file;
	protected $_paths;
	protected $_loader;
	protected $_environment;
	protected $_template;

	protected function _preRender ()
	{
		$this->_template = $this->_environment->loadTemplate($this->_file);
	}
	
	protected function _getContext ()
	{
		return $this->data();
	}

	public function __construct (Gwilym_Controller $controller, $file, $paths = array())
	{
		parent::__construct($controller);
		
		if (empty($paths)) {
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

		$this->_environment->addExtension(new Twig_Extension_Escaper(true));
	}

	public function display ()
	{
		$this->_preRender();
		$this->_template->display($this->_getContext());
	}

	public function render ()
	{
		$this->_preRender();
		return $this->_template->render($this->_getContext());
	}
}
