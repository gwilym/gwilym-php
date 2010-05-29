<?php

class Tests_Gwilym_KeyStore_Base extends UnitTestCase
{
	public $ks;

	public function testGetForNonExistantKey ()
	{
		$this->assertFalse($this->ks->exists('not_exist'));
		$this->assertFalse($this->ks->get('not_exist'));
	}

	public function testDeleteAndExists ()
	{
		$this->assertFalse($this->ks->exists('exists'));
		$this->assertTrue($this->ks->set('exists', 1));
		$this->assertTrue($this->ks->exists('exists'));
		$this->assertTrue($this->ks->delete('exists'));
		$this->assertFalse($this->ks->exists('exists'));
	}

	public function testMultiSetGet ()
	{
		$set = array(
			'test_ab' => 'ab',
			'test_bc' => 'bc',
			'test_cd' => 'cd',
			'test_de' => 'de',
		);

		$this->assertTrue($this->ks->multiDelete('test_??'));

		$this->assertTrue($this->ks->multiSet($set));

		$this->assertEqual('ab', $this->ks->get('test_ab'));
		$this->assertEqual('bc', $this->ks->get('test_bc'));
		$this->assertEqual('cd', $this->ks->get('test_cd'));
		$this->assertEqual('de', $this->ks->get('test_de'));

		$get = $this->ks->multiGet('test_??');

		$this->assertEqual($set, $get);

		$this->assertTrue($this->ks->multiDelete('test_??'));

		$get = $this->ks->multiGet('test_??');

		$this->assertEqual(array(), $get);
	}

	public function testIncrement ()
	{
		$this->assertTrue($this->ks->delete('increment_test'));
		$this->assertEqual(1, $this->ks->increment('increment_test'));
		$this->assertEqual(3, $this->ks->increment('increment_test', 2));
		$this->assertEqual(2, $this->ks->decrement('increment_test'));
		$this->assertEqual(-1, $this->ks->decrement('increment_test', 3));
		$this->assertEqual(-3, $this->ks->increment('increment_test', -2));
		$this->assertEqual(-1, $this->ks->decrement('increment_test', -2));
		$this->assertTrue($this->ks->delete('increment_test'));
	}

	public function testAppend ()
	{
		$this->assertTrue($this->ks->delete('append_test'));
		$this->assertEqual(4, $this->ks->append('append_test', 'abcd'));
		$this->assertEqual(8, $this->ks->append('append_test', '1234'));
		$this->assertTrue($this->ks->delete('append_test'));
	}
}
