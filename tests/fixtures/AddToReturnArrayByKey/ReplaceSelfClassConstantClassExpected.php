<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddToReturnArrayByKey;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ReplaceSelfClassConstantClass
{
    public const SELF_CONSTANT = 'self constant value';

    public function getArray(): array
    {
        return [
            'path1' => [
                self::SELF_CONSTANT => 'self constant replaced',
                'key0' => 'value0',
                'closure' => function ($param) {
                    return $param ?: null;
                },
                'key1' => 'value1',
            ],
        ];
    }
}
