# Coder

Coder is library for code-generation, e.g. add property to class, parameter to method, value to array and etc. Library is based on [Rector](https://github.com/rectorphp/rector).

## Installation

This library should be installed as a dependency using [Composer](https://getcomposer.org/):

```bash
composer.phar config repositories.crmplease/coder git git@gitlab.crmplease.me:mougrim/coder.git
composer.phar require crmplease/coder:dev-master --dev
```

## Get started

The facade class of this library is [`Coder`](src/Coder.php), you need to just create it and call methods, example:
```php
use Crmplease\Coder\Coder;

$coder = Coder::create();
$coder->addToFileReturnArrayByOrder(
    '/path/to/file.php',
    // path in array
    ['level1', 'level2.1'],
    'value'
);
```

## API

In all methods value can be:
- int
- float
- string
- instance of [`Constant`](src/Constant.php) class (see below)
- instance of [`Code`](src/Code.php) class (see below)
- array of arrays or types above with keys of types below

In all methods key can be:
- int
- string
- instance of [`Constant`](src/Constant.php) class (see below)

In all methods path is a path in array, if path not found, then it will be added. Path is array, so if we use path `['level1', 'level2']`, then empty array became:
```php
[
    'level1' => [
        'level2' => [
            // key => value or value will be added here
        ],
    ],
];
```

Use empty path if you want to change root level in array.

Parts of the path can be same as key:
- int
- string
- instance of [`Constant`](src/Constant.php) class (see below)

If you need to pass code, which will be added as is, then you can use [`Code`](src/Code.php) class:
```php
use Crmplease\Coder\Code;

new Code('$a = $b;');
```

If you need to pass some constant, the you can use [`Constant`](src/Constant.php) class :
```php
use Crmplease\Coder\Constant;

new Constant('\Some\ClassName::class');
```

### Add to file return array by order

```php
$coder->addToFileReturnArrayByOrder(
    '/path/to/file.php',
    // path in array
    ['level1.1', 'level2.1'],
    // value to add
    'newValue'
);
```

If value `'newValue'` already exists in array, then nothing changed.

Example:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        'level2.1' => [
            'existsValue',
        ],
    ],
    'level1.2' => [
        'value',
    ],
];
```
Became:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        'level2.1' => [
            'existsValue',
            'newValue',
        ],
    ],
    'level1.2' => [
        'value',
    ],
];
```

### Add to method return array by order

```php
$coder->addToReturnArrayByOrder(
    __DIR__ . '/path/to/ClassName.php',
    // method name
    'getArray',
    // path in array
    ['level1.1', 'level2.1'],
    // value to add
    'newValue'
);
```

If value `'newValue'` already exists in array, then nothing changed.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function getArray(): array
    {
        return [
            'level1.1' => [
                'level2.1' => [
                    'existsValue',
                ],
            ],
            'level1.2' => [
                'value',
            ],
        ];
    }
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function getArray(): array
    {
        return [
            'level1.1' => [
                'level2.1' => [
                    'existsValue',
                    'newValue',
                ],
            ],
            'level1.2' => [
                'value',
            ],
        ];
    }
}
```

### Add to property array by order

```php
$coder->addToPropertyArrayByOrder(
    __DIR__ . '/path/to/ClassName.php',
    // property name
    'array',
    // path in array
    ['level1.1', 'level2.1'],
    // value to add
    'newValue'
);
```

If value `'newValue'` already exists in array, then nothing changed.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $array = [
        'level1.1' => [
            'level2.1' => [
                'existsValue',
            ],
        ],
        'level1.2' => [
            'value',
        ],
    ];
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $array = [
        'level1.1' => [
            'level2.1' => [
                'existsValue',
                'newValue',
            ],
        ],
        'level1.2' => [
            'value',
        ],
    ];
}
```

### Add to file return array by key

```php
$coder->addToFileReturnArrayByKey(
    '/path/to/file.php',
    // path in array
    ['level1.1', 'level2.1'],
    // key to add
    'newKey',
    // value to add
    'newValue'
);
```

If key `'newKey'` already exists in array, then value for this key will be changed to `'newValue'`.

Example:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        'level2.1' => [
            'existsKey' => 'existsValue',
        ],
    ],
    'level1.2' => [
        'key' => 'value',
    ],
];
```
Became:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        'level2.1' => [
            'existsKey' => 'existsValue',
            'newKey' => 'newValue',
        ],
    ],
    'level1.2' => [
        'key' => 'value',
    ],
];
```

### Add to method return array by key

```php
$coder->addToReturnArrayByKey(
    __DIR__ . '/path/to/ClassName.php',
    // method name
    'getArray',
    // path in array
    ['level1.1', 'level2.1'],
    // key to add
    'newKey',
    // value to add
    'newValue'
);
```

If key `'newKey'` already exists in array, then value for this key will be changed to `'newValue'`.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function getArray(): array
    {
        return [
            'level1.1' => [
                'level2.1' => [
                    'existsKey' => 'existsValue',
                ],
            ],
            'level1.2' => [
                'key' => 'value',
            ],
        ];
    }
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function getArray(): array
    {
        return [
            'level1.1' => [
                'level2.1' => [
                    'existsKey' => 'existsValue',
                    'newKey' => 'newValue',
                ],
            ],
            'level1.2' => [
                'key' => 'value',
            ],
        ];
    }
}
```

### Add to property array by key

```php
$coder->addToPropertyArrayByKey(
    __DIR__ . '/path/to/ClassName.php',
    // property name
    'array',
    // path in array
    ['level1.1', 'level2.1'],
    // key to add
    'newKey',
    // value to add
    'newValue'
);
```

If key `'newKey'` already exists in array, then value for this key will be changed to `'newValue'`.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $array = [
        'level1.1' => [
            'level2.1' => [
                'existsKey' => 'existsValue',
            ],
        ],
        'level1.2' => [
            'key' => 'value',
        ],
    ];
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $array = [
        'level1.1' => [
            'level2.1' => [
                'existsKey' => 'existsValue',
                'newKey' => 'newValue',
            ],
        ],
        'level1.2' => [
            'key' => 'value',
        ],
    ];
}
```

### Add property to class

```php
use Crmplease\Coder\Rector\AddPropertyToClassRector;

$coder->addPropertyToClass(
    '/path/to/ClassName.php',
    // property name
    'newProperty',
    // pass true if property should be static
    false,
    // property visibility, can be VISIBILITY_PRIVATE, VISIBILITY_PROTECTED or VISIBILITY_PUBLIC
    AddPropertyToClassRector::VISIBILITY_PRIVATE,
    // default value for property, skip it or pass null if isn't needed
    'newValue'
);
```

If property exists, then visibility and static will be checked, if they're different, then [`RectorException`](src/Rector/RectorException.php) will be thrown. If they're equal, then value will changed to `'newValue'`.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $existsProperty;
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $existsProperty;
    private $newProperty = 'newValue';
}
```

### Add parameter to method:

```php
$coder->addParameterToMethod(
    '/path/to/ClassName.php',
    // method name
    '__construct',
    // parameter name
    'newParameter',
    // parameter type
    '?string',
    // if parameter has default value, then true
    true,
    // parameter default value
    'newValue'
);
```

If parameter exists, then type will be checked. If it's different, then [`RectorException`](src/Rector/RectorException.php) will be thrown. If it's equal, then default value will be changed.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function __construct(int $existsParameter = 123) {}
}
```

Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    public function __construct(int $existsParameter = 123, ?string $newParameter = 'newValue') {}
}
```

### Add code to the end of method

```php
$coder->addCodeToMethod(
    '/path/to/ClassName.php',
    // method name
    '__construct',
    // code as string
    '$this->newProperty = $newParameter;'
);
```

In simple cases code duplicates is checked (when you try to add one line code).

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $existsProperty;
    private $newProperty;

    public function __construct(int $existsParameter = 123, ?string $newParameter = 'newValue')
    {
        $this->existsProperty = $existsParameter;
    }
}
```

Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    protected $existsProperty;
    private $newProperty;

    public function __construct(int $existsParameter = 123, ?string $newParameter = 'newValue')
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
```

### Add phpdoc param to method

```php
$coder->addPhpdocParamToMethod(
    '/path/to/ClassName.php',
    // method name
    '__construct',
    // parameter, for which need to add phpdoc
    'newParameter',
    // parameter type
    'string|null',
    // description for parameter
    'some description'
);
```

If phpdoc for parameter already exists, then it will be removed and added again.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    /**
     * @param int $existsParameter
     */
    public function __construct(int $existsParameter = 123, ?string $newParameter = 'newValue') {}
}
```

Became
```php
// file /path/to/ClassName.php
class ClassName
{
    /**
     * @param int $existsParameter
     * @param string|null $newParameter some description
     */
    public function __construct(int $existsParameter = 123, ?string $newParameter = 'newValue') {}
}
```

### Add phpdoc property to class

```php
use \Crmplease\Coder\PhpdocProperty;
$coder->addPhpdocPropertyToClass(
    '/path/to/ClassName.php',
    new PhpdocProperty(
        // property name
        'newProperty',
        // property type, default is mixed
        'string|null',
        // description for property
        'description'
    )
);
```

If phpdoc for property already exists, then it will be updated.

Example:
```php
// file /path/to/ClassName.php
/**
 * @property int $existsProperty
 */
class ClassName {}
```

Became
```php
// file /path/to/ClassName.php
/**
 * @property int $existsProperty
 * @property string|null $newProperty some description
 */
class ClassName {}
```

You can add several properties:

```php
use \Crmplease\Coder\PhpdocProperty;
$coder->addPhpdocPropertiesToClass(
    '/path/to/ClassName.php',
    [
        new PhpdocProperty('newProperty1'),
        new PhpdocProperty('newProperty2'),
    ]
);
```

### Add trait to class

```php
$coder->addTraitToClass(
    '/path/to/ClassName.php',
    \Some\NewTraitName::class
);
```

If trait is already used, then nothing will be changed.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    use \Some\ExistsTraitName;
}
```

Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    use \Some\ExistsTraitName;
    use \Some\NewTraitName;
}
```

### Change class parent

```php
$coder->changeClassParent(
    __DIR__ . '/../src/Mougrim/TestClass.php',
    \Some\OtherClass::class
);
```

If parent doesn't exists, then it will be added.

Example:
```php
// file /path/to/ClassName.php
class ClassName extends \Some\ParentClass {}
```

Became:
```php
// file /path/to/ClassName.php
class ClassName extends \Some\OtherClass {}
```

### More complex example

More complex example with constants and code:
```php
use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;

$coder->addToFileReturnArrayByOrder(
    '/path/to/file.php',
    // path in array
    ['level1.1', new Constant('\Some\ClassName::class')],
    // value to add
    [
        'newValue',
        new Constant('\Some\ClassName::SOME_CONSTANT'),
        new Code('Rule::unique(\'countries\')->ignore($country->getKey())'),
    ]
);
```

If value `'newValue'` already exists in array, then nothing changed.

Example:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        \Some\OtherClass::class => ['value'],
        \Some\ClassName::class => [
            'existsValue',
        ],
    ],
    'level1.2' => [
        'value',
    ],
];
```
Became:
```php
// file /path/to/file.php
return [
    'level1.1' => [
        \Some\OtherClass::class => ['value'],
        \Some\ClassName::class => [
            'existsValue',
            [
                'newValue',
                \Some\ClassName::SOME_CONSTANT,
                Rule::unique('countries')->ignore($country->getKey()),
            ]
        ],
    ],
    'level1.2' => [
        'value',
    ],
];
```

## Internals

If you want to auto import classes, then change `parameters.auto_import_names` to `true` in [rector.yaml](rector.yaml).

You can run Rector using command line interface:
```bash
vendor/bin/rector process --config path/to/project/rector.yaml\\
   --autoload-file path/to/project/vendor/autoload.php\\
   --only "\Crmplease\Coder\Rector\AddToReturnArrayByOrderRector"\\
   path/to/project/Path/To/Class.php
```

But command line interface doesn't allow to pass parameters to rectors. You can pass parameters using setters in config file [rector.yaml](rector.yaml) config. For example:
```yaml
  Crmplease\Coder\Rector\AddToReturnArrayByOrderRector:
    calls:
      - method: setMethod
        arguments:
          - 'getArray'
      - method: setPath
        arguments:
          - ['level1', 'level2']
      - method: setValue
        arguments:
          - 'newValue'
```

### AddToFileReturnArrayByOrderRector

Config for AddToFileReturnArrayByOrderRector:
- setPath: path in array where need to add value
- setValue: value, which need to add to return array

### AddToReturnArrayByOrderRector

Config for AddToReturnArrayByOrderRector:
- setMethod: method name, to which need to add value/constant to return array, method should have only one return statement, which return array
- setPath: path in array where need to add value
- setValue: value, which need to add to return array

### AddToPropertyArrayByOrderRector

Config for AddToPropertyArrayByOrderRector:
- setProperty: property name, to which need to add value/constant, property should be array
- setPath: path in array where need to add value
- setValue: value, which need to add to return array

### AddToFileReturnArrayByKeyRector

Config for AddToFileReturnArrayByKeyRector:
- setPath: path in array where need to add key => value
- setKey: key, by which need to add to return array
- setValue: value, which need to add to return array by key

### AddToReturnArrayByKeyRector

Config for AddToReturnArrayByKeyRector:
- setMethod: method name, to which need to add value/constant to return array by key, method should have only one return statement, which return array
- setPath: path in array where need to add key => value
- setKey: key, by which need to add to return array
- setValue: value, which need to add to return array by key

### AddToPropertyArrayByKeyRector

Config for AddToPropertyArrayByKeyRector:
- setProperty: property name, to which need to add value/constant by key, property should be array
- setPath: path in array where need to add key => value
- setKey: key, by which need to add to property array
- setValue: value, which need to add to property array by key

### AddPropertyToClassRector

Config for AddPropertyToClassRector:
- setProperty: property name which need to add
- setVisibility: property visibility 'public', 'protected' or 'private', default is 'private'
- setIsStatic: is property static or no, true or false, default false
- setValue: default property value, don't pass it if isn't needed

### AddParameterToMethodRector

Config for AddParameterToMethodRector:
- setMethod: method name, to which need to add parameter
- setParameter: parameter name which need to add
- setParameterType: parameter type which need to add, can be class name started with '\\' or scalar type
- setHasValue: has default value or not, true or false, default true
- setValue: default parameter value

### AddCodeToMethodRector

Config for AddCodeToMethodRector:
- setMethod: method name, to which need to add code
- setCode: code which need to add

### AddTraitToClassRector

Config for AddTraitToClassRector:
- setTrait: trait name, to which need to add to class

### AddPhpdocParamToMethodRector

Config for AddPhpdocParamToMethodRector:
- setMethod: method name, to which phpdoc need to add parameter
- setParameter: parameter name which need to add to phpdoc
- setParameterType: parameter type which need to add to phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setDescription: description for param in phpdoc

### AddPhpdocPropertyToClassRector

Config for AddPhpdocParamToMethodRector:
- setProperty: property name which need to add to phpdoc
- setPropertyType: property type which need to add to phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setDescription: description for property in phpdoc

### ChangeClassParentRector

Config for ChangeClassParentRector:
- setParentClass: new parent class name
