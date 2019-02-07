<?php
namespace Root4root\Reshaper\Processors;

use Root4root\Reshaper\ReshaperData;

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
        
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];

            if ($operation == '&') {
                $result = $result && $this->isValid($this->typecast($nextCol));
            } else {
                $result = $result || $this->isValid($this->typecast($nextCol));
            }
        }
        return $result;
    }
    
    public function processCol(array $rule)
    {
        $result = $this->typecast($rule['columns'][0]);
        
        foreach ($rule['operations'] as $key => $operation) {
            $nextCol = $rule['columns'][$key + 1];
            
            switch ($operation) {
                case '+':
                    $result += $this->typecast($nextCol);
                    break;
                case '-':
                    $result -= $this->typecast($nextCol);
                    break;
                case '*':
                    $result *= $this->typecast($nextCol);
                    break;
                case '/':
                    $nextColVal = $this->typecast($nextCol);
                    if ($nextColVal != 0) {
                        $result = ($result / $nextColVal);
                    } else {
                        $result = 0;
                    }
                    break;
            }
        }
        
        if (! empty($rule['extra'])) {
            $result = $result + $result / 100 * $rule['extra'];
        }
        
        $this->data->setResultCol($result);
        
        return true;
    }
    
    public function typecast($key)
    {
        if (! empty($this->cache[$key])) {
            return $this->cache[$key];
        }
        
        $rawCol = $this->data->getCol($key);
        $rawCol = preg_replace(['/[^0-9.,]/', '/,/'], ['','.'], $rawCol);
        $realCol = (float)$rawCol;
        
        $this->cache[$key] = $realCol;

        return $realCol;
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
