<?php

namespace root4root\Reshaper;

class Processor_f implements ProcessorInterface
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
        $result = $this->isValid($this->typecast($rule['columns'][0]));
        
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];

            if ($operation == '&') {
                $result = $result && $this->isValid($this->typecast($nextcol));
            } else {
                $result = $result || $this->isValid($this->typecast($nextcol));
            }
        }
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $result = $this->typecast($rule['columns'][0]);
        
        foreach ($rule['operations'] AS $key=>$operation) {
            $nextcol = $rule['columns'][$key+1];
            
            switch ($operation) {
                case '+':
                    $result += $this->typecast($nextcol);
                    break;
                case '-':
                    $result -= $this->typecast($nextcol);
                    break;
                case '*':
                    $result *= $this->typecast($nextcol);
                    break;
                case '/':
                    $nexctolVal = $this->typecast($nextcol);
                    if ($nexctolVal != 0) {
                        $result = ($result/$nexctolVal);
                    } else {
                        $result = 0;
                    }
                    break;
            }
        }
        
        if (! empty($rule['extra'])) {
            $result = $result + $result/100*$rule['extra'];
        }
        
        $this->data->setResult($result);
        
        return true;
    }
    
    public function typecast($key)
    {
        if (! empty($this->cache[$key])) {
            return $this->cache[$key];
        }
        
        $rawcol = $this->data->getCol($key);
        $rawcol = preg_replace(array('/[^0-9.,]/', '/,/'), array('','.'), $rawcol);
        $realcol = (float)$rawcol;
        
        $this->cache[$key] = $realcol;

        return $realcol;
    }
    
    public function isValid($col = 0)
    {
        if ($col == 0) {
            return false;
        } else {
            return true;
        }
    }
}
