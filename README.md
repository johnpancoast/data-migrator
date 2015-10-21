data-migrator
=============
data-migrator is a PHP lib which provides you with a structured way to migrate data from one data structure to another.

The need for this arose from realizing that I was writing some of the same things over and over whenever I handled writing data migrations. Moving data from one database to another, importing and validating CSVs, any stream of data that needed to be sanitized or validated at some point. These all share similar functionality. Not to mention, custom migration scripts tend to become maintenance nightmares which, in certain scenarios, matters if they are migrations which will be used a lot.

Installation
------------
data-migrator uses [composer](https://getcomposer.org/). 

To include it, either use [composer require](https://getcomposer.org/doc/03-cli.md#require)
 
```
composer require johnpancoast/data-migrator:~0.2.0
```

... or require it in your project's composer file

```php
{
    "require": {
        "johnpancoast/data-migrator": "~0.2.0",
    }
}
```

Concepts
--------
The migrator is the object you'll interact with. It accepts a "```model```" and an ```iterator```.

* ```model``` - Where you define the desired structures of data and how they will get created from the data being iterated. Most importantly, this defines the structure you're migrating *to* and how to validate this data.
* ```iterator``` - Any object implementing PHP's [Iterator](http://php.net/manual/en/class.iterator.php) interface. Each iteration is the data you're migrating *from*.

The migrator iterates the data in the iterator, passing this data to various methods of your model to create (and receive) a data structure you define. It validates this structure based on your defined validation constraints and provides you with the finished product for that iteration.

Example Usage
-------------
You must first create a ```model``` which extends [```AbstractModel```](https://github.com/johnpancoast/data-migrator/blob/master/src/AbstractModel.php) (or implements 
 [```ModelInterface```](https://github.com/johnpancoast/data-migrator/blob/master/src/ModelInterface.php)). 

Here's a working model that's doing absolutely nothing.

```php
use Pancoast\DataMigrator\AbstractModel;

class MyModel extends AbstractModel
{
    public function getFields()
    {
        return [];
    }
}
```

Let's create an example migrator that uses this model. We are mixing code in the same place  which we normally wouldn't do. This is just for examples.

```php
use Pancoast\DataMigrator\AbstractModel;
use Pancoast\DataMigrator\Migrator;

class MyModel extends AbstractModel
{
    public function getFields()
    {
        return [];
    }
}

$migrator = new Migrator(
    new MyModel(),
    new ArrayIterator([
        [ 'row1-col0', 'row1-col1' ],
        [ 'row2-col0', 'row2-col1' ],
        [ 'row3-col0', 'row3-col1' ]
    ])
);

$migrator->run();
```

So you can see that to use the migrator, we just need to provide it with a model object and an iterator to iterate. But if we ran the above example, nothing would happen. This is because the model has nothing defined. Let's first add some fields.

```php
use Pancoast\DataMigrator\AbstractModel;
use Pancoast\DataMigrator\Field\ArrayIndexField;
use Pancoast\DataMigrator\Field\CallableField;
use Pancoast\DataMigrator\Migrator;

class MyModel extends AbstractModel
{
    public function getFields()
    {
        return [
            new ArrayIndexField(
                'foo',
                [],
                0
            ),
            new ArrayIndexField(
                'bar',
                [],
                1
            ),
            new CallableField(
                'faz',
                [],
                ( function($iterationInput) {
                    return $iterationInput[0].' - '.strrev($iterationInput[1]);
                })
            ),
        ];
    }
}

$migrator = new Migrator(
    new MyModel(),
    new ArrayIterator([
        [ 'row1-col0', 'row1-col1' ],
        [ 'row2-col0', 'row2-col1' ],
        [ 'row3-col0', 'row3-col1' ]
    ])
);

$migrator->run();

```

Look at ```MyClass``` above. ```getFields()``` is now returning an array of fields which is the definition of the structure you're expecting from each iteration and how this data gets set from each iteration. The first 2 fields are ```ArrayIndexField``` which allow you to specify the array index, in an iteration of input, where a field can be retrieved from. The last field is a ```CallableField``` which allows you to pass a callable to specify how input for the field is determined. *There will be more fields available... it's only v0.2!*

You can create and use your own custom field classes. They just need to implement [```FieldInterface```](https://github.com/johnpancoast/data-migrator/blob/master/src/FieldInterface.php).

Now the model is defining fields but if we were to run the migrator, there would *still* be no output. This is because although we've created the fields that define the I/O structures, we still haven't done anything with the data.
  
Following is a complete example.

```php
use Pancoast\DataMigrator\AbstractModel;
use Pancoast\DataMigrator\Field\ArrayIndexField;
use Pancoast\DataMigrator\Field\CallableField;
use Pancoast\DataMigrator\Migrator;

class MyModel extends AbstractModel
{
    public function getFields()
    {
        return [
            new ArrayIndexField(
                'foo',
                [],
                0
            ),
            new ArrayIndexField(
                'bar',
                [],
                1
            ),
            new CallableField(
                'faz',
                [],
                ( function($iterationInput) {
                    return $iterationInput[0].' - '.strrev($iterationInput[1]);
                })
            ),
        ];
    }

    public function endIteration($iterationCount, $iterationOutput)
    {
    
        // perhaps we'd persist data here or add something to a transaction
                                        
        print_r($iterationOutput);
    }

    public function end()
    {
    
        // perhaps we'd flush data here or commit a transaction
        
        echo "goodbye, world.\n";
    }
}

$migrator = new Migrator(
    new MyModel(),
    new ArrayIterator([
        [ 'row1-col0', 'row1-col1' ],
        [ 'row2-col0', 'row2-col1' ],
        [ 'row3-col0', 'row3-col1' ]
    ])
);

$migrator->run();

```

We added the following 2 methods to ```MyClass```.

```
public function endIteration($iterationCount, $iterationOutput)
{

    // perhaps we'd persist data here or add something to a transaction
                                    
    print_r($iterationOutput);
}

public function end()
{

    // perhaps we'd flush data here or commit a transaction
    
    echo "goodbye, world.\n";
}
```

The first, ```endIteration()```, gets executed at the end of each iteration and receives ```$iterationOutput```. This is an iteration of data in an array that matches the names of fields you defined in ```getFields()```. This is after validation has occurred on these fields (discussed below). The second method ```end()``` gets executed at the end of all iterations.

The above example would output this:

```
Array
(
    [foo] => row1-col0
    [bar] => row1-col1
    [faz] => row1-col0 - 1loc-1wor
)
Array
(
    [foo] => row2-col0
    [bar] => row2-col1
    [faz] => row2-col0 - 1loc-2wor
)
Array
(
    [foo] => row3-col0
    [bar] => row3-col1
    [faz] => row3-col0 - 1loc-3wor
)
goodbye, world.
```

The following model methods are available to be extended from ```AbstractModel``` *although ```getFields()```, ```endIteration()```, and ```end()``` will likely be used the most*.

* ```getFields()``` - For defining and returning an array of objects implementing ```FieldInterface```.
* ```begin()``` - Executed before iterating.
* ```createIterationInput($iterationCount, $iterationData)``` - This method is provided data from one iteration and can construct the $iterationInput that will be passed to ```beginIteration()```. This is called after ```begin()``` and before ```beginIteration()```.
* ```beginIteration($iterationCount, &$iterationInput, &$iterationOutput)``` - Executed at the beginning of each iteration before validation. Useful for custom modification of input and output.
* ```endIteration($iterationCount, $iterationOutput)``` - Executed at the end of each iteration after validation.
* ```end()``` - Executed after all iterations.
* ```handleIterationConstraintViolations($iterationCount, $violationList)``` - Called when an iteration had fields which failed validation.


#### Validation
data-migrator uses Symfony's awesome [validator](http://symfony.com/doc/current/book/validation.html).

In ```MyClass``` in the above example, look at the fields we defined. The 2nd parameter of each field accepts an array of Symfony (or custom) [constraints](http://symfony.com/doc/current/book/validation.html#constraints). The fields in the examples are empty but if we wanted the first field to require that it be an integer, we could change it to the following.

```php
new ArrayIndexField(
    'foo',
    [
        new Constraint\Type(['type' => 'int'])
    ],
    0
),
```

The above would give us the following error. *In our examples it would be uncaught*.

```The following field errors occurred in iteration 1: [foo] - This value should be of type int.```

Upon constraint validations, the migrator will call on the following methods:

* The ```handleConstraintViolations()``` method of the field that failed (@see FieldInterface::handleConstraintViolations()).
* The ```handleIterationConstraintViolations()``` method of the model the migrator is running (@see ModelInterface::handleIterationConstraintViolations()).

@todo
----
* Passing $messages as reference through each iteration method for communication.
* Perhaps events.
* More fields and models.