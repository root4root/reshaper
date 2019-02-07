<?php
namespace Root4root\Reshaper\Test;

use PHPUnit\Framework\TestCase;
use Root4root\Reshaper\Configurator;
use \Exception as Exception;

class ConfiguratorTest extends TestCase
{
    
    public function setUp()
    {
        $this->configurator = new Configurator();
    }
    
    public function tearDown() 
    {
        unset($this->configurator);
    }
    
    public function testCreateConfigWrongFormat() 
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Parse error. Wrong format.');
        
        $this->configurator->createConfig(['@#$'], ['(A)s']);
    }
    
    public function testCreateConfigNoSuchColumn() 
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Parse error. No such column.');
        
        $this->configurator->createConfig(['(A1)']);
    }
    
    public function testCreateConfigWrongRegexp() 
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wrong regular expression format.');
        
        $this->configurator->createConfig(['(A)s'], ['(A)r(#:^(#)']);
    }
    
    public function testLoadConfigFromJSON()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('JSON is not valid.');
        
        $this->configurator->loadConfigFromJSON('anynotvalidJSON');
    }
    
    public function testGetConfigCreateConfigFirst()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Create config first.');
        
        $this->configurator->getConfig();
    }
    
    public function testCheckTypeRegexpWrongFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wrong regular expression format.');
        
        $method = new \ReflectionMethod(Configurator::class, 'checkType');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configurator, ['r', '#:^($']);
    }
    
    public function testCheckTypeDefaultValue()
    {
        $method = new \ReflectionMethod(Configurator::class, 'checkType');
        $method->setAccessible(true);
        
        $input = '';
        $expected = 's';
        
        $this->assertEquals($expected, $method->invoke($this->configurator, $input));
    }
    
    /**
     * @dataProvider provideFileds
     */
    public function testFieldParser($input, $expected)
    {
        $method = new \ReflectionMethod(Configurator::class, 'parseField');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->configurator, $input));
    }
    
    public function provideFileds()
    {
        return [
            ['(A)i(10)', [
                'expression' => '(A)i(10)',
                'columns' => [0],
                'operations' => [],
                'extra' => 10,
                'type' => 'i'
            ]],
            ['(A|B|C)f(10)', [
                'expression' => '(A|B|C)f(10)',
                'columns' => [0,1,2],
                'operations' => ['|','|'],
                'extra' => 10,
                'type' => 'f'
                
            ]],
            ['(1)', [
                'expression' => '(1)',
                'columns' => [0],
                'operations' => [],
                'extra' => '',
                'type' => 's'
            ]],
            ['(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)', [
                'expression' => '(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)',
                'columns' => [6,7,8],
                'operations' => ['|','|'],
                'extra' => '/^(?:\s*)[1-9,\+]+(?:\s*)$/',
                'type' => 'r'
            ]],
        ];
    }
}
