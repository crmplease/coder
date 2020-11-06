<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayByOrderTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'test';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            ['path1'],
            'value2'
        );
        $this->assertFixture($fixture);
    }
}
