<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\CrmPlease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToPropertyArrayByOrderTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToPropertyArrayByOrder(
            $this->createFixtureFile($fixture),
            'array',
            ['path1'],
            'value1'
        );
        $this->assertFixture($fixture);
    }
}
