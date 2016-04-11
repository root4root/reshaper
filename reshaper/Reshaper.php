<?php

namespace root4root\Reshaper;

require_once 'ReshaperData.php';
require_once 'ProcessorFactory.php';
require_once 'processors/ProcessorInterface.php';

class Reshaper
{
    private $config;
    
    public function __construct(Configurator $configurator)
    {
        $this->config = $configurator->getConfig();
    }
    
    
    public function parseRow(array $row)
    {
        if (empty($row)) {
            return false;
        }
        
        $data = new ReshaperData();
        
        $data->setData($row);
        ProcessorFactory::newData($data);
        
        foreach ($this->config['required'] AS $rule) {
            $processor = ProcessorFactory::getProcessor($rule['type']);
            if ($processor->requiredCol($rule) === false) {
                return false;
            }
        }
   
        foreach ($this->config['fields'] AS $rule) {
            $processor = ProcessorFactory::getProcessor($rule['type']);
            $processor->processCol($rule);
            $data->nextCol();        
        }
        
        return $data;
    }
}