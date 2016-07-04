<?php

namespace root4root\Reshaper;

use root4root\Reshaper\ReshaperData;
use root4root\Reshaper\ProcessorInterface;

class Processor_r implements ProcessorInterface
{
    public $data = null;

    public function setData(ReshaperData $data)
    {
        $this->data = $data;
    }
    
    public function requiredCol(array $rule)
    {
        $regexp = $rule['extra'];
        $result = $this->isEmpty($this->typecast($rule['columns'][0], $regexp));
        
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];

            if ($operation == '&' || $operation == '*') {
                $result = $result && $this->isEmpty($this->typecast($nextcol, $regexp));
            } else {
                $result = $result || $this->isEmpty($this->typecast($nextcol, $regexp));
            }
        }
        
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $regexp = $rule['extra'];
        $colkey = $rule['columns'][0];
        $result = $this->typecast($colkey, $regexp);
        
        //$this->data->setResult($result);
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];
            $result .= ' ' . $this->typecast($nextcol, $regexp);
        }
        
        $this->data->setResult($result);
        
        return true;
    }
    
    public function typecast($colkey, $regexp)
    {
        $result = '';
        
        $rawcol = $this->data->getCol($colkey);
                
        if (! preg_match($regexp, $rawcol, $matches)) {
            return $result;
        }
        
        $matchesCount = count($matches);
        
        if ($matchesCount > 1) {
            for ($i=1; $i<$mathesCount; $i++) {
                $result .= $matches[$i];
            }
        } else {
            $result = $matches[0];
        }
        
        return $result;
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
