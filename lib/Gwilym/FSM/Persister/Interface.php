<?php

interface Gwilym_FSM_Persister_Interface
{
	public function load (Gwilym_FSM $fsm, $id);
	public function save (Gwilym_FSM $fsm);
	public function delete (Gwilym_FSM $fsm);
}
