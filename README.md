# Reshaper 

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) 
[![Build Status](https://travis-ci.org/root4root/reshaper.svg?branch=master)](https://travis-ci.org/root4root/reshaper)

Implements primitive language which helps transform arrays (rows) i.e. exclude some values (members) or mix them. As a result, you could get array that bigger or smaller than original one. 

Library was developed for convinient way to filtering data came from xlsx parser, or any. Language simplicity allows to involve end customers, so they can write filters without help from programmers. That's why A,B,C etc as columns names. Digits are also acceptible, but indexes begins from 1. First element, second and so on.


### Quick examle
```php
require_once ('vendor/autoload.php');

use Root4root\Reshaper\Configurator;
use Root4root\Reshaper\Reshaper;

$fields = ['(A)i','(B)s', '(B+C)s'];
$requiredCols = ['(A)i', '(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)'];

$dataRow = [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0];

$config = new Configurator($fields, $requiredCols);
$reshaper = new Reshaper($config);

$output = $reshaper->parseRow($dataRow);

print_r($output->getResult());

/*
 * Array
 * (
 *     [0] => 1
 *     [1] => PARTNUMBER
 *     [2] => PARTNUMBER Part.333
 * )
 */

```
### Explanation
There are two arrays for configuration. First one is answering the question «What fields are expected to be created with the output array?» Second formulates rules for validation.

##### Fields array syntax ($fields  in example)
###### (column)type(extra)
Columns can be separated by a special chars: +, -, *, /.

Types from package:
* f - float
* i - integer
* r - regular expression
* s - string

Extra: some additional configuration, required for ‘r’ processor. Can be used with ‘i’ and ‘f’ by optional.

Each type handles with certain processor, with its own validation and filed rules. For example, processor 's' will concatenate fields, regardless of separator (B+C) or (B*C). Processor 'i' (integer) and 'f' will calculate the result depending on the math sign.

(A)i(30) - get value from column, convert it to integer and increase by 30%. Same operation with the float type.

You can specify columns by number, starting from 1 (first). Example: '(2+3)s' == '(B+C)s'

##### Required array syntax ($requiredCols in example)
###### (column)type(extra) 

Columns separators: |,& (or +,* respectevly).

Types: f,i,r,s from package.

(A|B)i - A **or** B must be > 0

(A&B)s - A **and** B is not empty strings

(G|H)r(regexp) - A **or** B must satisfy regular expression.

**You should specify required array or its equivalents to field list. This can cause a problem with performance.**

### Your own types
You can define your own data types or override defaults from package. Create class Processor_type, which implements the Processor Interface.