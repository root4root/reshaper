<?php

namespace root4root\Reshaper;

use root4root\Reshaper\ReshaperData;
use root4root\Reshaper\ProcessorInterface;

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
        
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];

            if ($operation == '&' || $operation == '*') {
                $result = $result && $this->isEmpty($this->typecast($nextcol));
            } else {
                $result = $result || $this->isEmpty($this->typecast($nextcol));
            }
        }
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $result = $this->typecast($rule['columns'][0]);
        
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];
            $result .= ' ' . $this->typecast($nextcol);
        }
        $this->data->setResult($result);
        
        return true;
    }
    
    public function typecast($key)
    {
        if (! empty($this->cache[$key])) {
            return $this->cache[$key];
        }
        $realcol = trim($this->data->getCol($key));
        $this->cache[$key] = $realcol;
        
        return $realcol;
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
