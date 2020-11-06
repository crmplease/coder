<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\AddToReturnArrayByKey;

class SomeClass
{
    public function getArray(): array
    {
        return [
            'path1' => [
                'key0' => 'value0',
                'closure' => function ($param) {
                    return $param ?: null;
                },
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];
    }
}
