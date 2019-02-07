<?php
namespace Root4root\Reshaper;

use Root4root\Reshaper\ReshaperData;
use Root4root\Reshaper\ProcessorFactory;

class Reshaper
{
    private $config;
    
    public function __construct(Configurator $configurator)
    {
        $this->config = $configurator->getConfig();
    }
       
    public function parseRow(array $row)
    {
        $data = new ReshaperData();
        
        if (empty($row)) {
            return $data;
        }
        
        $data->setData($row);
        ProcessorFactory::newData($data);
        
        foreach ($this->config['required'] as $rule) {
            $processor = ProcessorFactory::getProcessor($rule['type']);
            if ($processor->requiredCol($rule) === false) {
                return $data;
            }
        }
   
        foreach ($this->config['fields'] as $rule) {
            $processor = ProcessorFactory::getProcessor($rule['type']);
            $processor->processCol($rule);
            $data->nextCol();        
        }
        
        return $data;
    }
}