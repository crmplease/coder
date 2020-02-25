<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\CrmPlease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByOrderTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            'getArray',
            ['path1'],
            'value1'
        );
        $this->assertFixture($fixture);
    }
}
