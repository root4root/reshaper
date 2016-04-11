<?php

namespace root4root\Reshaper;

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

            if ($operation == '&') {
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
        $this->data->setResult($result);

        return true;
    }
    
    public function typecast($colkey, $regexp)
    {
        $rawcol = $this->data->getCol($colkey);
        preg_match($regexp, $rawcol, $result);
        
        if (empty($result) || $result[0] == '') {
            return '';
        } else {
            return $result[0];
        }
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
