<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByKeyTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToReturnArrayByKey(
            $this->createFixtureFile($fixture),
            'getArray',
            ['path1'],
            'key2',
            'value2'
        );
        $this->assertFixture($fixture);
    }
}
