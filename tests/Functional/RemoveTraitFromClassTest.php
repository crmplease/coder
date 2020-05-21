<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\fixtures\BarTrait;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class RemoveTraitFromClassTest extends FunctionalTestCase
{
    public function testRemoveTrait(): void
    {
        $fixture = 'RemoveTrait';
        $coder = $this->getCoder();
        $coder->removeTraitFromClass(
            $this->createFixtureFile($fixture),
            BarTrait::class
        );
        $this->assertFixture($fixture);
    }

    public function testRemoveNotExistsTrait(): void
    {
        $fixture = 'RemoveNotExistsTrait';
        $coder = $this->getCoder();
        $coder->removeTraitFromClass(
            $this->createFixtureFile($fixture),
            BarTrait::class
        );
        $this->assertFixture($fixture);
    }
}
