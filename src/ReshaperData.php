<?php
namespace Root4root\Reshaper;

class ReshaperData
{
    public $currentData = [];
    public $result = [];
    public $currentCol = 0;
    
    public function setData(array $data)
    {
        if (! empty($data)) {
            $this->currentData = $data;
        } else {
            return false;
        }
        
        return true;
    }
    
    public function getData()
    {
        return $this->currentData;
    }
    
    public function getCol($key)
    {
        if (isset($this->currentData[$key])) {
            return $this->currentData[$key];
        } else {
            return '';
        }
    }
    
    public function setResultCol($result)
    {
        $this->result[$this->currentCol] = $result;
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function nextCol()
    {
        $this->currentCol++;
    }
}
