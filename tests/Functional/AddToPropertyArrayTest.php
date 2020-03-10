<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToPropertyArray(
            $this->createFixtureFile($fixture),
            'array',
            ['path1'],
            [
                'value4',
                'key5' => 'value5',
            ]
        );
        $this->assertFixture($fixture);
    }
}
