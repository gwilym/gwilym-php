<?php

class Tests_Gwilym_FSM_LoopingFsmTest extends Gwilym_FSM
{
    protected $_from;
    protected $_to;
    protected $_counter;
    protected $_iterations;
    
    protected function _getStates ()
    {
        return array(
            'loop' => array(
                array(
                    'on' => 'finished',
                    'goto' => null,
                ),
                'loop',
            ),
        );
    }
    
    protected function _loop ()
    {
        $this->_counter++;
        $this->_iterations++;
    }
    
    protected function _finished ()
    {
        return $this->_counter >= $this->_to;
    }
    
    public function setFrom ($value)
    {
        $this->_from = $value;
        return $this;
    }
    
    public function setTo ($value)
    {
        $this->_to = $value;
        return $this;
    }
    
    public function getCounter ()
    {
        return $this->_counter;
    }
    
    public function getIterations ()
    {
        return $this->_iterations;
    }
    
    public function start ()
    {
        $this->_counter = $this->_from;
        $this->_iterations = 0;
        return parent::start();
    }
}
