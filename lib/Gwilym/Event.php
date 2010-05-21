<?php

class Gwilym_Event
{
	/**
	* storage for bindings for an event, in the format of event_id => callback
	*
	* @var array<callback>
	*/
	protected static $_bindings = array();

	/**
	* storage for whether an event has had persistent bindings loaded yet, in the format of event_id => bool
	*
	* @var mixed
	*/
	protected static $_loaded = array();

	/**
	* load any persisted bindings for a specific event
	*
	* @param string $event
	* @return void
	*/
	protected static function _load ($event)
	{
		if (isset(self::$_loaded[$event]) && self::$_loaded[$event]) {
			return;
		}

		$bindings = Gwilym_KeyStore::multiGet('event,' . $event . ',bind,*');

		foreach ($bindings as $binding)
		{
			if (strpos($bindings, '::') === false)
			{
				// function callback
				self::$_bindings[$event][] = $binding;
			}
			else
			{
				// class::static callback
				$binding = explode('::', $binding);
				self::$_bindings[$event][] = array($binding[0], $binding[1]);
			}
		}
	}

	/**
	* bind a callback to an event
	*
	* @param object $object optional, the event can be object specific - binding to a specific object implies $persist = false as internal object ids are not unique between page loads
	* @param string $event event name
	* @param callback $callback callback, which can be a closure, an array(class, static method) or a function name - binding a closure implies $persist = false as closures cannot be serialized
	* @param bool $persist optional, if true the binding will persist beyond this script execution (default false)
	*/
	public static function bind ($object, $event, $callback = null, $persist = null)
	{
		$args = func_get_args();

		if (!is_object($object)) {
			$object = null;
			$event = array_shift($args);
			$callback = array_shift($args);
			$persist = (bool)array_shift($args);
		}

		if ($object) {
			// binding to an object may only be temporary
			self::$_bindings[$event . '#' . spl_object_hash($object)][] = $callback;
			return true;
		}

		self::$_bindings[$event][] = $callback;

		if (!$persist || Gwilym_Reflection::isClosure($callback)) {
			// binding a closure may only be temporary
			return true;
		}

		if (is_array($callback)) {
			// store array callbacks as strings to undo later when loading bindings
			$callback = $callback[0] . '::' . $callback[1];
		}

		Gwilym_KeyStore::set('Gwilym_Event,' . $event . ',binding,' . md5($callback), $callback);
	}

	/**
	* trigger an event, propagating the trigger to all bound callbacks
	*
	* @param object $object optional
	* @param string $event
	* @param mixed $data optional
	* @return Gwilym_Event
	*/
	public static function trigger ($object, $event = null, $data = null)
	{
		$args = func_get_args();

		if (is_object($object))
		{
			// triggering instance-specific event
			$key = $event . '#' . spl_object_hash($object);
		}
		else
		{
			// triggering global event
			$data = $event;
			$event = $object;
			$key = $event;
			$object = null;
			self::_load($event);
		}

		$instance = new self($event);
		$instance->data = $data;

		if (!isset(self::$_bindings[$key]))
		{
			return $instance;
		}

		foreach (self::$_bindings[$key] as $binding)
		{
			$result = call_user_func($binding, $instance);

			if ($result === false)
			{
				$instance->stopPropagation();
				$instance->preventDefault();
				break;
			}

			if ($instance->isPropagationStopped())
			{
				break;
			}
		}

		return $instance;
	}

	// ====================

	protected $_defaultPrevented = false;

	public function isDefaultPrevented ()
	{
		return $this->_defaultPrevented;
	}

	public function preventDefault ()
	{
		$this->_defaultPrevented = true;
	}

	protected $_propagationStopped = false;

	public function isPropagationStopped ()
	{
		return $this->_propagationStopped;
	}

	public function stopPropagation ()
	{
		$this->_propagationStopped = true;
	}

	protected $_type;

	public function type ()
	{
		return $this->_type;
	}

	public function __construct ($type)
	{
		$this->_type = $type;
	}

	/** @var mixed data payload provided by trigger() */
	public $data = null;
}
