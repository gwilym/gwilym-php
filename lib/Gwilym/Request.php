<?php

class Gwilym_Request
{
	const MAX_NESTING_DEPTH = 20;

	/**
	* Calls to handle() may eventually make another call to handle() if transfer() is called. This value controls the maximum amount of times handle() can be called for one instance.
	*
	* @var int
	*/
	protected $_nestingDepth = self::MAX_NESTING_DEPTH;

	protected $_routers = array();

	protected $_uriParser;

	public function __construct ($uri = null)
	{
		if ($uri !== null)
		{
			$this->uriParser(new Gwilym_UriParser_Fixed('', $uri));
		}
	}

	public function uriParser (Gwilym_UriParser $uriParser = null)
	{
		if ($uriParser !== null)
		{
			$this->_uriParser = $uriParser;
		}
		else if ($this->_uriParser === null)
		{
			$this->_uriParser = new Gwilym_UriParser_Guess;
		}
		return $this->_uriParser;
	}

	public function uri ()
	{
		return $this->uriParser()->uri();
	}

	public function addRouter ($router)
	{
		$this->_routers[] = $router;
	}

	public function handle ()
	{
		$transfer = null;

		while (true)
		{
			if (!$this->_nestingDepth--)
			{
				throw new Gwilym_Request_Exception_TooManyTransfers;
			}

			try
			{
				if ($transfer)
				{
					return $transfer->follow();
				}
				else
				{
					foreach ($this->_routers as $index => /** @var Gwilym_Router */$router)
					{
						if (is_string($router))
						{
							$router = $this->_routers[$index] = new $router;
						}

						$result = $router->route($this);
						if ($result !== false) {
							return true;
						}
					}
					break;
				}
			}
			catch (Gwilym_Request_Exception_Transfer $exception)
			{
				$transfer = null;

				if (class_exists($exception->to) && Gwilym_Reflection::isClassInstanciable($exception->to))
				{
					$transfer = $this->route($exception->to, $exception->args);
				}
				else
				{
					$previousParser = $this->uriParser();
					$fixedParser = new Gwilym_UriParser_Fixed($previousParser->base(), $exception->to, $previousParser->docroot());
					$this->uriParser($fixedParser);
				}
			}
		}

		return false;
	}

	/**
	* Transfer current execution to another controller
	*
	* @param string $to Name of controller class to transfer to directly, or a URI to send through routing rules to discover new controller
	* @param array $args If $to is a class name, you can provide optional args, too
	*/
	public function transfer ($to, $args = array())
	{
		$exception = new Gwilym_Request_Exception_Transfer;
		$exception->to = $to;
		$exception->args = $args;
		throw $exception;
	}

	/**
	* @param string $controller
	* @param array $args
	* @return Gwilym_Route
	*/
	public function route ($controller, $args = array())
	{
		return new Gwilym_Route($this, $controller, $args);
	}

	public function routeToUri (Gwilym_Route $route)
	{
		foreach ($this->_routers as $index => /** @var Gwilym_Router */$router)
		{
			if (is_string($router))
			{
				$router = $this->_routers[$index] = new $router;
			}

			if ($uri = $router->routeToUri($route))
			{
				return $uri;
			}
		}
		return false;
	}
}
