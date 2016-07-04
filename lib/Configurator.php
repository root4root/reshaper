<?php
namespace root4root\Reshaper;

use \Exception as Exception;

class Configurator
{
    public $alphabet;
    public $config = [];
    
    /*
    *$config = ['fields'   => [['expression' => '',
    *                                  'columns' => [],
    *                                  'operations' => [],
    *                                  'extra' => '',
    *                                  'type' => ''
    *                         ]],
    *           'required' => []
    *          ];
    */
    
    public function __construct()
    {
        $this->alphabet = range('A','Z');
        
    }
    
    public function createConfig(array $fields, array $required = array()) 
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
            throw new Exception('JSON is not valid');
        } else {
            $this->config = $config;
        }
        return $this;
    }
    
    public function parseFields($fields)
    {
        $arrkey = 0;
        $parsed = [];
        
        $expression = '/^\(?([A-Z0-9\*\+\-\/\&\|]*)\)?([a-z]*)?(?:\((.*)\))?/';
        
        
        foreach ($fields AS $field) {
            preg_match($expression, $field, $result);
            
            if (empty($result[1])) {
                throw new Exception('Parse error. Wrong format.');
            }
            
            $parsed[$arrkey]['expression'] = $result[0];
            
            $columns = $this->normalizeColumns(preg_split('/(\W)/', $result[1]));
            
            if ($columns === false) {
                throw new Exception('Parse error. No such column.');
            }
            
            $parsed[$arrkey]['columns'] = $columns;
            preg_match_all('/\W/', $result[1], $operations);
            $parsed[$arrkey]['operations'] = $operations[0];
            
            if (empty($result[2])) {
                $parsed[$arrkey]['type'] = 's';
            } else {
                $parsed[$arrkey]['type'] = $result[2];
            }
            
            if ($parsed[$arrkey]['type'] == 'r' && (empty($result[3]) || @preg_match($result[3], null) === false)) {
                throw new Exception('Wrong regular expression format');
            } 
            
            if (! empty($result[3])) {
                $parsed[$arrkey]['extra'] = $result[3];
            }
            
            $arrkey++;
        }
        return $parsed;
    }
    
    public function normalizeColumns($columns)
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
