<?php
namespace Root4root\Reshaper\Test;

use PHPUnit\Framework\TestCase;
use Root4root\Reshaper\ProcessorFactory;
use Root4root\Reshaper\ReshaperData;
use Root4root\Reshaper\Processors\Processor_s;
use \Exception;

class ProcessorFactoryTest extends TestCase
{
    public function testCheckClass() 
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Processor file not found.');
        
        ProcessorFactory::checkClass('WrongProcessorName_z');
    }
    
    public function testGetProcessor() 
    {
        $input = 's';
        $expected = Processor_s::class;

        ProcessorFactory::newData(new reshaperData());
        
        $this->assertInstanceOf($expected, ProcessorFactory::getProcessor($input));
    }
    
}
