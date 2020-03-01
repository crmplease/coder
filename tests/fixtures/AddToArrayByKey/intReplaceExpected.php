<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */

use Tests\Crmplease\Coder\fixtures\FooClass;

return [
    0 => 'int0 replaced',
    1 => 'int1',
    'key0' => 'value0',
    'key1' => 'value1',
    FooClass::class => 'foo class',
    FooClass::TEST => 'foo constant',
];
