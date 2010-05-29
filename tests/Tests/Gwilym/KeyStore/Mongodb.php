<?php

class Tests_Gwilym_KeyStore_Mongodb extends Tests_Gwilym_KeyStore_Base
{
	public function setUp ()
	{
		$this->ks = new Gwilym_KeyStore_Mongodb();
	}
}
