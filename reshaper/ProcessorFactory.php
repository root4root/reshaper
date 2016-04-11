<?php
namespace root4root\Reshaper;

use \Exception as Exception;

class ProcessorFactory
{
    private static $instances = [];
    private static $path = 'processors/Processor_';
    private static $data = null;
        
    public static function checkClass($processor)
    {
        $filename = dirname(__FILE__) . '/'. self::$path . $processor . '.php';
        
        if (file_exists($filename) === false) {
            throw new Exception('Processor file not found');
        } else {
            require_once($filename);
        }
        
        if (class_exists(__NAMESPACE__ . '\Processor_' . $processor, false) === false) {
            throw new Exception('Processor class not found');
        }

        return true;
    }
    
    public static function getProcessor($processor)
    {
        if (empty(self::$instances[$processor])) {
            
            $classname = 'Processor_' . $processor;
            $absClassname = __NAMESPACE__ . '\\' . $classname;
            
            if (class_exists($absClassname, false)) {
                $processorObj = new $absClassname();
            } elseif (class_exists($classname, false)) {
                $processorObj = new $classname();
            } else {
                self::checkClass($processor);
                $processorObj = new $absClassname;
            }
            
            $processorObj->setData(self::$data);
            self::$instances[$processor] = $processorObj;
            
            return $processorObj;
            
        } else {
            return self::$instances[$processor];
        }
        
        return false;
    }
    
    
    public static function newData(ReshaperData $data)
    {
        self::$data = $data;
        
        foreach (self::$instances AS $instance) {
            $instance->setData($data);
        }
    }
    
}
