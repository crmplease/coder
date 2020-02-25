<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\CrmPlease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayByKeyTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'test';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['path1'],
            'key1',
            'value1'
        );
        $this->assertFixture($fixture);
    }
}
