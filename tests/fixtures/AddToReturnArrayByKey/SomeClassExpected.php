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
                'key0' => 'value0', 'key1' => 'value1',
            ],
        ];
    }
}
