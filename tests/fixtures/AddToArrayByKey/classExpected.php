<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */

use Tests\Crmplease\Coder\fixtures\FooClass;
use const Tests\Crmplease\Coder\fixtures\FOO_TEST;

require_once __DIR__ . '/../constants.php';

return [
    0 => 'int0',
    1 => 'int1',
    'key0' => 'value0',
    'key1' => 'value1',
    'closure' => function ($param) {
        return $param ?: null;
    },
    FOO_TEST => 'foo constant',
    FooClass::class => 'foo class',
    FooClass::TEST => 'foo class constant',
    \Tests\Crmplease\Coder\fixtures\BarClass::class => 'bar class',
];
