<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\AddToReturnArrayByKey;

class SomeClass
{
    public const SELF_CONSTANT = 'self constant value';

    public function getArray(): array
    {
        return [
            'path1' => [
                self::SELF_CONSTANT => 'self constant',
                'key0' => 'value0',
                'closure' => function ($param) {
                    return $param ?: null;
                },
                'key1' => 'value1',
            ],
        ];
    }
}
