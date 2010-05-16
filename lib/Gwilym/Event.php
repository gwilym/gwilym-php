<?php

abstract class Gwilym_Event
{
	protected static $_bindingsLoaded = array();

	protected static $_bindings = array();

	/**
	* Find all bindings for the specified event
	*
	* @param string $event
	*/
	public static function bindings ($event)
	{
		if (@$_bindingsLoaded[$event])
		{
			// bindings already loaded
			return $_bindings[$event];
		}

		// find stored bindings and add to any existing temporary bindings
		$prefix = 'Gwilym_Event,' . $event . ',binding,';
		$prefixLength = strlen($prefix);
		$bindings = Gwilym_KeyStore::multiGet($prefix . '*');

		if (!isset(self::$_bindings[$event]))
		{
			self::$_bindings[$event] = array();
		}

		foreach ($bindings as $md5 => $binding)
		{
			$md5 = substr($md5, $prefixLength);

			if (strpos($binding, '::') !== false)
			{
				// class::method() callback
				$binding = explode('::', $binding, 2);
				$binding = array($binding[0], $binding[1]);
			}

			self::$_bindings[$event][$md5] = $binding;
		}

		return self::$_bindings[$event];
	}

	public static function bind ($callback, $temporary = false)
	{
		$event = get_called_class();

		if (Gwilym_Reflection::isClosure($callback))
		{
			// php won't allow closures to be serialized so we can't store a permanent binding
			self::$_bindings[$event][] = $callback;
			return;
		}

		if (is_array($callback))
		{
			// class::method() callback
			$class = $callback[0];
			$method = $callback[1];
		}
		else
		{
			// function() callback
			$class = $callback[0];
			$method = false;
		}

		if (!is_string($class))
		{
			throw new Gwilym_Event_Exception_BindingMustBeStatic();
		}

		$value = $class;
		if ($method)
		{
			$value .= '::' . $method;
		}

		$md5 = md5($value);
		$key = 'Gwilym_Event,' . $event . ',binding,' . $md5;

		if (!$temporary)
		{
			Gwilym_KeyStore::set($key, $value);
		}

		self::$_bindings[$event][$md5] = $callback;
	}

	public static function unbind ($callback)
	{
		throw new Exception('Not yet implemented');
	}

	/** @var string */
	protected $_id;

	/** @var bool */
	protected $_preventDefault = false;

	/** @var bool */
	protected $_stopPropogation = false;

	/** @return string */
	public function id ()
	{
		if ($this->_id === null)
		{
			$this->_id = get_class($this);
		}

		return $this->_id;
	}

	/** @return Gwilym_Event */
	public function trigger ($data = null)
	{
		$bindings = self::bindings($this->id());
		foreach ($bindings as $binding)
		{
			if (Gwilym_Reflection::isClosure($binding))
			{
				$binding($this, $data);
			}
			else
			{
				if (!is_callable($binding))
				{
					throw new Gwilym_Event_Exception_BindingNotCallable();
				}
				call_user_func($binding, $data);
			}

			if ($this->_stopPropogation)
			{
				break;
			}
		}
		return $this;
	}
}
