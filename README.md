# SmartDto\Dto
Create and manipulate Data Transfer Objects

### Basic Use

First, create your Dto:

```php
<?php
namespace MyNamespace\Dto;

use \SmartDto\Dto as SDto;

class Get extends SDto
{
    # Type these attribures as required (depending on your PHP version)
    public $id = 0;
    public $description = '';
    public $title = '';
}

```

Reference your Dto in your model

```php
<?php
namespace MyNamespace;

use Dto\Get;

class MyClass
{
    # You can type your return so your IDE can easily
    # interpret what should be returned for quick reference
    public function get(): Get
    {
        $dataArray = [
            'id' => 123,
            'description' => 'Some kind of example description',
            'tile' => 'Tile Here!'
        ];
        # Return back your Dto
        return Get($dataArray);
    }
}
```
### Basic Result:

```php
<?php
$MyClass = new MyNamespace\MyClass();
# Any respectable IDE will be able to detect your params and make viewing available attributes easy
# If the data attribute "$dataArray" is missing the "id" key, for example, because of the Dto object
# mapping, this will not throw an undefined index error
# As is, this will write "123"
echo $MyClass->id;
```

### Advanced Example

Revised Dto to include some model-esque features:

```php
<?php
namespace MyNamespace\Dto;

use \SmartDto\Dto as SDto;

class Get extends SDto
{
    public $id = 0;
    public $description = '';
    public $title = '';
    
    # If you create a same-named method as the parameter,
    # the Dto will then call this method post-process
    # Because it's protected, your IDE should not show it as available
    protected function id()
    {
        $this->id += 1;
    }
}

```

### Advanced Result

This will produce a different result than the first example because it will run the "id()" method as well:

```php
<?php
$MyClass = new MyNamespace\MyClass();
# This will result in the value "124" instead of "123"
echo $MyClass->id;
```

## Parameter Conversion

You are able to convert the input array before assigning the keys to your Dto:

```php
<?php
namespace MyNamespace\Dto;

use \SmartDto\Dto as SDto;

class Get extends SDto
{
    # Notice the Camel Case keys
    public $id = 0;
    public $theDescription = '';
    public $myTitle = '';
}

```
## Convert Using A Constant

```php
<?php
namespace MyNamespace;

use Dto\Get;

class MyClass
{
    public function get(): Get
    {
        # Notice these are not formatted the same as the DTO
        $dataArray = [
            'id' => 123,
            'the_description' => 'Some kind of example description',
            'my title' => 'Tile Here!'
        ];
        # Return back your Dto
        return Get($dataArray, \SmartDto\Dto::CAMEL_CASE);
    }
}

```

## This will map to camel case.

This will then be mapped to your camel cased parameters:

```php
echo $MyClass->theDescription;
```

> If you have a string such as `'thedescription'`, it will not generate camel case because there is no way to know where to break.

# SmartDto\Mapper

This object will generate a string for copy and pasting to your DTO class by examining your input data:

```php
<?php
use \SmartDto\Mapper;

$dataArray = [
    'id' => 123,
    'description' => 'Some kind of example description',
    'price' => 1.23
];

$Mapper = new Mapper($dataArray);
echo $Mapper->getAttributes();
```

This will produce a string that you can copy and paste:

```
public $id = 0;
public $description = '';
public $price = 0.0;
```

Pass `true` to the `public function getAttributes(true);` to add types behind your parameters:

```
public int $id = 0;
public string $description = '';
public float $price = 0.0;
```
