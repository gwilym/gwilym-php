<?php

class Gwilym_Template
{
	/** @var Twig_Environment */
	public $twig;

	public $context = array();

	public function __construct (Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	public function set ($key, $value)
	{
		$this->context[$key] = $value;
	}

	public function delete ($key)
	{
		unset($this->context[$key]);
	}

	public function render ($template, $context = null)
	{
		if ($context === null)
		{
			$context = $this->context;
		}

		$template = $this->twig->loadTemplate($template);
		$template->render($this->context);
	}

	public function display ($template, $context = null)
	{
		if ($context === null)
		{
			$context = $this->context;
		}

		$template = $this->twig->loadTemplate($template);
		$template->display($context);
	}
}
