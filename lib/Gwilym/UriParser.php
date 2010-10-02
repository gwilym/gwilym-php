<?php

abstract class Gwilym_UriParser
{
	/**
	* returns the base uri of the current request
	*/
	abstract public function getBase ();

	/**
	* returns the uri of the current request relative to the base uri
	*/
	abstract public function getUri ();

	/**
	* returns the document root of the web server / v-host config
	*/
	abstract public function getDocRoot ();
}
