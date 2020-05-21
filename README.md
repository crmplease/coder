# Coder

Coder is library for code-generation, e.g. add property to class, parameter to method, value to array and etc. Library is based on [Rector](https://github.com/rectorphp/rector).

## Installation

This library should be installed as a dependency using [Composer](https://getcomposer.org/):

```bash
composer.phar require crmplease/coder --dev
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
    'newValue',
    // property type in Phpdoc
    'string',
    // property description in Phpdoc
    'description'
);
```

If property exists, then property will be updated.

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
    /**
     * @var string description
     */
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

### Add method to class

```php
use Crmplease\Coder\Rector\AddMethodToClassRector;

$coder->addMethodToClass(
    '/path/to/ClassName.php',
    // method name
    'newMethod',
    // method visibility, can be VISIBILITY_PRIVATE, VISIBILITY_PROTECTED or VISIBILITY_PUBLIC
    AddMethodToClassRector::VISIBILITY_PUBLIC,
    // pass true if method should be static
    false,
    // pass true if method should be abstract
    false,
    // pass true if method should be final
    false,
    // method return type
    'int',
    // return description
    'return description',
    // method description
    'method description'
);
```

If method exists, then signature and Phpdoc will be updated.

Example:
```php
// file /path/to/ClassName.php
class ClassName
{
    /**
     * Exists description
     *
     * @return int exists return description
     */
    public function existsMethod(): int
    {
        return 0;
    }
}
```
Became:
```php
// file /path/to/ClassName.php
class ClassName
{
    /**
     * Exists description
     *
     * @return int exists return description
     */
    public function existsMethod(): int
    {
        return 0;
    }
    /**
     * method description
     * @return int return description
     */
    public function newMethod(): int
    {
        return 0;
    }
}
```

### Add Phpdoc param to method

```php
$coder->addPhpdocParamToMethod(
    '/path/to/ClassName.php',
    // method name
    '__construct',
    // parameter, for which need to add Phpdoc
    'newParameter',
    // parameter type
    'string|null',
    // description for parameter
    'some description'
);
```

If Phpdoc for parameter already exists, then it will be removed and added again.

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

### Add Phpdoc property to class

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
        'some description'
    )
);
```

If Phpdoc for property already exists, then it will be updated.

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

### Add Phpdoc method to class

```php
use \Crmplease\Coder\PhpdocMethod;
use \Crmplease\Coder\PhpdocMethodParameter;
$coder->addPhpdocMethodToClass(
    '/path/to/ClassName.php',
    new PhpdocMethod(
        // method name
        'newMethod',
        // return type, default is mixed
        'string|null',
        // true if should be static
        false,
        // array of parameters
        [
            new PhpdocMethodParameter(
                // parameter name
                'parameter1',
                // parameter type
                 'int',
                // has default value if true
                true,
                // default value
                0
            ),
        ],
        // description for method
        'some description'
    )
);
```

If Phpdoc for method already exists, then it will be updated.

Example:
```php
// file /path/to/ClassName.php
/**
 * @method int existsMethod()
 */
class ClassName {}
```

Became
```php
// file /path/to/ClassName.php
/**
 * @method int existsMethod()
 * @method string|null newMethod(int $parameter1 = 0) some description
 */
class ClassName {}
```

You can add several methods:

```php
use \Crmplease\Coder\PhpdocMethod;
$coder->addPhpdocMethodsToClass(
    '/path/to/ClassName.php',
    [
        new PhpdocMethod('newMethod1'),
        new PhpdocMethod('newMethod2'),
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

### Remove trait from class

```php
$coder->removeTraitFromClass(
    '/path/to/ClassName.php',
    \Some\ExistsTraitName::class
);
```

If trait isn't used, then nothing will be changed.

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

## Config

You can provide config object when create coder:
```php
use Crmplease\Coder\Coder;
use Crmplease\Coder\Config;

$config = new Config();
$coder = Coder::create($config);
```

### Disable progress bar

By default rector shows progress bar when change files. You can disable it:
```php
use Crmplease\Coder\Config;

$config = (new Config())
    ->setShowProgressBar(false);
```

### Auto import classes

By default auto import classes is disabled by [rector.yaml](rector.yaml). You can change default value or use mapping/callback for enable/disable auto import classes:

```php
use Crmplease\Coder\Config;

$config = (new Config())
    // use default value
    ->setAutoImport(null)
    // always auto import
    ->setAutoImport(true)
    // newer auto import
    ->setAutoImport(false)
    ->setAutoImport(
        [
            // auto import this file
            '/path/to/file/with/enabled/auto/import/classes.php' => true,
            // doesn't auto import this file
            '/path/to/file/with/disabled/auto/import/classes.php' => false,
            // use default value
            '/path/to/file/with/defaul/auto/import/classes.php' => null,
        ]
    )
    ->setAutoImport(
        static function (string $file): ?bool {
            // some logic
            // if null is returned, then default value will be used
            return $result;
        }
    )
    ->setAutoImport([SomeClass::class, 'method']);
```

### Path to rector config path

Rector config file for coder is [`rector.yaml`](rector.yaml). You can provide path to your own config file with redeclare values from [`rector.yaml`](rector.yaml) or add new one:

```php
use Crmplease\Coder\Config;

$config = (new Config())
    ->setRectorConfigPath('/path/to/rector.yaml');
```

For more information about rector configuration see rector [documentation](https://github.com/rectorphp/rector).

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
- setType: property type in Phpdoc, can be class name started with '\\' or scalar type, can by autodetected by default value
- setDescription: property description in Phpdoc

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

### AddMethodToClassRector

Config for AddMethodToClassRector:
- setMethod: method name which need to add
- setVisibility: method visibility 'public', 'protected' or 'private', default is 'private'
- setIsStatic: is method static or no, true or false, default false
- setIsAbstract: is method abstract or no, true or false, default false
- setIsFinal: is method final or no, true or false, default false
- setReturnType: method return type, can be class name started with '\\' or scalar type or void
- setReturnDescription: return description in Phpdoc
- setDescription: method description in Phpdoc

### AddTraitToClassRector

Config for AddTraitToClassRector:
- setTrait: trait name, to which need to add to class

### RemoveTraitFromClassRector

Config for RemoveTraitFromClassRector:
- setTrait: trait name, to which need to remove from class

### AddPhpdocParamToMethodRector

Config for AddPhpdocParamToMethodRector:
- setMethod: method name, to which Phpdoc need to add parameter
- setParameter: parameter name which need to add to Phpdoc
- setParameterType: parameter type which need to add to Phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setDescription: description for param in Phpdoc

### AddPhpdocPropertyToClassRector

Config for AddPhpdocPropertyToClassRector:
- setProperty: property name which need to add to Phpdoc
- setPropertyType: property type which need to add to Phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setDescription: description for property in Phpdoc

### AddPhpdocMethodToClassRector

Config for AddPhpdocMethodToClassRector:
- setMethod: method name which need to add to Phpdoc
- setReturnType: method return type which need to add to Phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setIsStatic: is method static or no, true or false, default false
- setParameters: array of [`PhpdocMethodParameter`](src/PhpdocMethodParameter.php) objects
- setDescription: description for method in Phpdoc

### ChangeClassParentRector

Config for ChangeClassParentRector:
- setParentClass: new parent class name
