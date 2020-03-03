<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToFileReturnArrayTest extends FunctionalTestCase
{
    public function test(): void
    {
        $fixture = 'test';
        $coder = $this->getCoder();
        $coder->addToFileReturnArray(
            $this->createFixtureFile($fixture),
            ['path1'],
            [
                'value4',
                'key5' => 'value5',
            ]
        );
        $this->assertFixture($fixture);
    }
}
