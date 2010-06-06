<?php

class Gwilym_KeyStore_Mongodb_CursorIterator extends IteratorIterator
{
	public function key ()
	{
		$current = $this->getInnerIterator()->current();
		return $current['_id'];
	}

	public function current ()
	{
		$current = $this->getInnerIterator()->current();
		return $current['value'];
	}
}
