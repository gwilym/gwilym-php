<?php

class Gwilym_Html
{
	public static function encode ($input)
	{
		// @todo double check intended usage of this
		return htmlentities($input);
	}
	
	public static function decode ($input)
	{
		// @todo double check intended usage of this
		return html_entity_decode($input);
	}
}
