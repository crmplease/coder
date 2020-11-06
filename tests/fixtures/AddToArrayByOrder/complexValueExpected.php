<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */

use Tests\Crmplease\Coder\fixtures\FooClass;

$country = new stdClass();
return [
    null,
    false,
    true,
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
    [2, 'string', \Tests\Crmplease\Coder\fixtures\BarClass::class, \Tests\Crmplease\Coder\fixtures\BarClass::TEST, Rule::unique('countries')->ignore($country->getKey())],
];
