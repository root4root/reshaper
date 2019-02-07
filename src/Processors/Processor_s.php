<?php
namespace Root4root\Reshaper\Processors;

use Root4root\Reshaper\ReshaperData;

class Processor_s implements ProcessorInterface
{
    public $data = null;
    private $cache = [];
    

    public function setData(ReshaperData $data)
    {
        $this->data = $data;
        $this->cache = [];
    }
    
    public function requiredCol(array $rule)
    {
        $result = $this->isEmpty($this->typecast($rule['columns'][0]));
        
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];

            if ($operation == '&' || $operation == '*') {
                $result = $result && $this->isEmpty($this->typecast($nextCol));
            } else {
                $result = $result || $this->isEmpty($this->typecast($nextCol));
            }
        }
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $result = $this->typecast($rule['columns'][0]);
        
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];
            $result .= ' ' . $this->typecast($nextCol);
        }
        $this->data->setResultCol($result);
        
        return true;
    }
    
    public function typecast($key)
    {
        if (! empty($this->cache[$key])) {
            return $this->cache[$key];
        }
        
        $realCol = trim($this->data->getCol($key));
        $this->cache[$key] = $realCol;
        
        return $realCol;
    }
    
    public function isEmpty($col = '')
    {
        if ($col == '') {
            return false;
        } else {
            return true;
        }
    }
}
