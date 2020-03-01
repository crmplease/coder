<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayByKeyTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToPropertyArrayByKey(
            $this->createFixtureFile($fixture),
            'array',
            ['path1'],
            'key1',
            'value1'
        );
        $this->assertFixture($fixture);
    }
}
