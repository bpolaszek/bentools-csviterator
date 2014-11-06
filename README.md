CSVIterator
-----------

An easy way to iterate over a CSV file.
Consider the example "example.csv"

> "FirstName";"LastName";"City"
"Bill";"Gates";"Seattle"
"Steve";"Jobs";"Palo Alto"

Example usage :

```php 
$csv    =    new \BenTools\CSVIterator\CSVIterator('example.csv', ';');
foreach ($csv as $row)
    var_dump($row);
```    

Outputs :

> array (size=3)
  0 => string 'FirstName' (length=9)
  1 => string 'LastName' (length=8)
  2 => string 'City' (length=4)
array (size=3)
  0 => string 'Bill' (length=4)
  1 => string 'Gates' (length=5)
  2 => string 'Seattle' (length=7)
array (size=3)
  0 => string 'Steve' (length=5)
  1 => string 'Jobs' (length=4)
  2 => string 'Palo Alto' (length=9)

CSVIteratorExtended
-------------------
An extension to CSV Iterator taking the first row as keys.

```php 
$csv    =    new BenTools\CSVIterator\CSVIteratorExtended(new \BenTools\CSVIterator\CSVIterator('example.csv', ';'));
foreach ($csv as $row)
    var_dump($row);
```
	    
Outputs : 

> array (size=3)
  'FirstName' => string 'Bill' (length=4)
  'LastName' => string 'Gates' (length=5)
  'City' => string 'Seattle' (length=7)
array (size=3)
  'FirstName' => string 'Steve' (length=5)
  'LastName' => string 'Jobs' (length=4)
  'City' => string 'Palo Alto' (length=9)

You can optionnally pass a callable as a 2nd argument to ensure you have php-friendly keys :

```php 
$csv    =    new BenTools\CSVIterator\CSVIteratorExtended(new \BenTools\CSVIterator\CSVIterator('example.csv', ';'), 'strtolower');
foreach ($csv as $row)
    var_dump($row);
```	    

Outputs :

> array (size=3)
  'firstname' => string 'Bill' (length=4)
  'lastname' => string 'Gates' (length=5)
  'city' => string 'Seattle' (length=7)
array (size=3)
  'firstname' => string 'Steve' (length=5)
  'lastname' => string 'Jobs' (length=4)
  'city' => string 'Palo Alto' (length=9)

Installation
------------
Add the following line into your composer.json :

    {
      "require": {
          "bentools/csviterator": "dev-master"
      }
    }  
Enjoy.