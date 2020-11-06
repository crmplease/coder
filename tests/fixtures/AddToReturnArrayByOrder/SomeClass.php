<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\AddToReturnArrayByOrder;

class SomeClass
{
    public function getArray(): array
    {
        return [
            'path1' => [
                'value0',
                function ($param) {
                    return $param ?: null;
                },
                'value1',
            ],
        ];
    }
}
