<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */

use Tests\Crmplease\Coder\fixtures\FooClass;

return [
    null,
    false,
    0,
    1,
    0.0,
    0.5,
    '',
    'null',
    'false',
    'true',
    '0',
    '1',
    '2',
    'test',
    function ($param) {
        return $param ?: null;
    },
    FooClass::class,
    FooClass::TEST,
    true,
];
