### What this all about?
Imagine that you have an array and at the same time you are trying to create a new one with the custom field from it, including mixing. This might be very helpful if you are working with some data sources, and want to fit the data under a specific format. Let’s say, you have a price list and you need to update your database in accordance with it. To make this possible you need to reshape data to fit database schema. Hence, the name.

### Quick examle
```php
$fields = ['(A)i','(B)s', '(B+C)s'];
$required = ['(A)i', '(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)'];
$data = [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0];

$obj = new Configurator();
$reshaper = new Reshaper($obj->createConfig($fields, $required));

$result = $reshaper->parseRow($data);

if ($result !== false) {
    print_r($result->getResult());
}
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

##### Required array syntax ($required in example)
###### (column)type(extra) 

Columns separators: |,& (or +,* respectevly).

Types: f,i,r,s from package.

(A|B)i - A **or** B must be > 0

(A&B)s - A **and** B is not empty strings

(G|H)r(regexp) - A **or** B must satisfy regular expression.

**You should specify required array or its equivalents to field list. This can cause a problem with performance.**

### Your own types
You can define your own data types or override defaults from package. Create class Processor_type, which implements the Processor Interface.
