# Coder

How to run:
```bash
vendor/bin/rector process --autoload-file path/to/project/vendor/autoload.php\\
   --only "\CrmPlease\Coder\Rector\AddToReturnArrayByOrderRector"\\
   path/to/project/Path/To/Class.php
```

Config is in rector.yaml.

In all rectors constant can be global constant or class constant, examples:
- `SOME_GLOBAL_CONSTANT`
- `Path\To\Class::CLASS_CONSTANT`
- `Path\To\Class::class`

In all rectors value can be:
- int
- float
- string

In all rectors key can be:
- int
- string

Config for AddToReturnArrayByOrderRector:
- setMethod: method name, to which need to add value/constant to return array, method should have only one return statement, which return array
- setValue: value, which need to add to return array
- setConstant: constant, which need to add to return array.

Config for AddToPropertyArrayByOrderRector:
- setProperty: property name, to which need to add value/constant, property should be array
- setValue: value, which need to add to return array
- setConstant: constant, which need to add to return array.

Config for AddToReturnArrayByKeyRector:
- setMethod: method name, to which need to add value/constant to return array by key, method should have only one return statement, which return array
- setKey: key, by which need to add to return array
- setKeyConstant: key as constant, by which need to add to return array
- setValue: value, which need to add to return array by key
- setConstant: constant, which need to add to return array.

You should provide only key or only key as constant. You should provide only value or only constant.

Config for AddToPropertyArrayByKeyRector:
- setProperty: property name, to which need to add value/constant by key, property should be array
- setKey: key, by which need to add to property array
- setKeyConstant: key as constant, by which need to add to property array
- setValue: value, which need to add to property array by key
- setConstant: constant, which need to add to property array.

You should provide only key or only key as constant. You should provide only value or only constant.

Config for AddPropertyToClassRector:
- setProperty: property name which need to add
- setVisibility: property visibility 'public', 'protected' or 'private', default is 'private'
- setIsStatic: is property static or no, true or false, default false
- setValue: default property value, don't pass it if isn't needed

Config for AddParameterToMethodRector:
- setMethod: method name, to which need to add parameter
- setParameter: parameter name which need to add
- setParameterType: parameter type which need to add, can be class name started with '\\' or scalar type
- setHasValue: has default value or not, true or false, default true
- setValue: default parameter value

Config for AddCodeToMethodRector:
- setMethod: method name, to which need to add code
- setCode: code which need to add

Config for AddTraitToClassRector:
- setTrait: trait name, to which need to add to class

Config for AddPhpdocParamToMethodRector:
- setMethod: method name, to which phpdoc need to add parameter
- setParameter: parameter name which need to add to phpdoc
- setParameterType: parameter type which need to add to phpdoc, can be class name started with '\\' or scalar type, collections, union type
- setDescription: description for param in phpdoc

Config for ChangeClassParentRector:
- setParentClass: new parent class name
