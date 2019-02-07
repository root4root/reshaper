<?php
namespace Root4root\Reshaper;

use \Exception as Exception;

class Configurator
{
    public $alphabet;
    public $config = [];
    
    private $parseExpression = '/^\(([A-Z0-9\*\+\-\/\&\|]*)\)([a-z]*)?(?:\((.*)\))?/';
    
    /*
     * $config = ['fields'   => [['expression' => '',
     *                            'columns' => [],
     *                            'operations' => [],
     *                            'extra' => '',
     *                            'type' => ''
     *                          ]],
     *            'required' => [[...]] //same structure
     * ];
     */
    
    public function __construct(array $fields = [], array $required = [])
    {
        $this->alphabet = range('A','Z');
        
        if (! empty($fields)) {
            $this->createConfig($fields, $required);
        }
        
    }
    
    public function createConfig(array $fields, array $required = []) 
    {
        $this->config['fields'] = $this->parseFields($fields);
        
        if (empty($required)) {
            $this->config['required'] = $this->config['fields'];
        } else {
            $this->config['required'] = $this->parseFields($required);
        }
        
        return $this;
    }
      
    public function saveConfig()
    {
        if (empty($this->config)) {
            throw new Exception('Create config first.');
        }
        
        return json_encode($this->config);
    }
    
    public function getConfig()
    {
        if (empty($this->config)) {
            throw new Exception('Create config first.');
        }
        
        return $this->config;
    }
    
    public function loadConfigFromJSON($json)
    {
        $config = json_decode($json, true);
        if (empty($config)) {
            throw new Exception('JSON is not valid.');
        } else {
            $this->config = $config;
        }
        return $this;
    }
    
    public function parseFields($fields)
    {
        $parsed = [];
        
        foreach ($fields as $arrKey => $field) {
            $parsed[$arrKey] = $this->parseField($field);
        }
        
        return $parsed;
    }
    
    public function getConfigArray()
    {
        $config = ['fields' => [], 'required' => []];
        
        foreach ($this->config['fields'] as $rule) {
            $config['fields'][] = $rule['expression'];
        }
        
        foreach ($this->config['required'] as $rule) {
            $config['required'][] = $rule['expression'];
        }
        
        return $config;
    }
    
    private function parseField($field)
    {
        $parsedField = [];
        
        preg_match($this->parseExpression, $field, $result);
            
        if (empty($result[1])) {
            throw new Exception('Parse error. Wrong format.');
        }

        $parsedField['expression'] = $result[0];

        $columns = $this->normalizeColumns(preg_split('/(\W)/', $result[1]));

        if ($columns === false) {
            throw new Exception('Parse error. No such column.');
        }

        $parsedField['columns'] = $columns;

        preg_match_all('/\W/', $result[1], $operations);
        $parsedField['operations'] = $operations[0];
        
        $parsedField['extra'] = empty($result[3]) ? '' : $result[3];

        $parsedField['type'] = $this->checkType($result[2], $parsedField['extra']);
        //$parsedField['extra'] = $result[3];
        
        return $parsedField;
    }
    
    private function checkType($rawType, $extra = '')
    {
        $actualType = '';
        
        if (empty($rawType)) {
            $actualType = 's'; //default type
        } else {
            $actualType = $rawType;
        }

        if (
            $actualType == 'r' && 
            (empty($extra) || @preg_match($extra, null) === false)
        ) {
            throw new Exception('Wrong regular expression format.');
        } 
        
        return $actualType;
    }
    
    private function normalizeColumns($columns)
    {
        $fields = [];

        foreach ($columns as $column) {
            
            $col = (int)$column;
            
            if ($col == 0 && in_array($column, $this->alphabet)) {
                $fields[] = array_search($column, $this->alphabet);
                continue;
            }
            
            if ($col > 0) {
                $fields[] = $col - 1;
                continue;
            }
            return false;
        }
        return $fields;
    }
}
