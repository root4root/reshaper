<?php
namespace Root4root\Reshaper\Processors;

use Root4root\Reshaper\ReshaperData;

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
        
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];

            if ($operation == '&' || $operation == '*') {
                $result = $result && $this->isEmpty($this->typecast($nextCol, $regexp));
            } else {
                $result = $result || $this->isEmpty($this->typecast($nextCol, $regexp));
            }
        }
        
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $regexp = $rule['extra'];
        $colKey = $rule['columns'][0];
        $result = $this->typecast($colKey, $regexp);
        
        //$this->data->setResult($result);
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];
            $result .= ' ' . $this->typecast($nextCol, $regexp);
        }
        
        $this->data->setResultCol($result);
        
        return true;
    }
    
    public function typecast($colKey, $regexp)
    {
        $result = '';
        
        $rawCol = $this->data->getCol($colKey);
                
        if (! preg_match($regexp, $rawCol, $matches)) {
            return $result;
        }
        
        $matchesCount = count($matches);
        
        if ($matchesCount > 1) {
            for ($i = 1; $i < $matchesCount; $i++) {
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
