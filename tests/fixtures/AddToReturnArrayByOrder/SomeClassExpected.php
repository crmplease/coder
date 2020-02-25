<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\CrmPlease\Coder\fixtures\AddToReturnArrayByOrder;

class SomeClass
{
    public function getArray(): array
    {
        return [
            'path1' => [
                'value0', 'value1',
            ],
        ];
    }
}
