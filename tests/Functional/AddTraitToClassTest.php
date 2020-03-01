<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Tests\Crmplease\Coder\fixtures\BarTrait;
use Tests\Crmplease\Coder\fixtures\FooTrait;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddTraitToClassTest extends FunctionalTestCase
{
    public function testAddTrait(): void
    {
        $fixture = 'AddTrait';
        $coder = $this->getCoder();
        $coder->addTraitToClass(
            $this->createFixtureFile($fixture),
            BarTrait::class
        );
        $this->assertFixture($fixture);
    }

    public function testDuplicateTrait(): void
    {
        $fixture = 'DuplicateTrait';
        $coder = $this->getCoder();
        $coder->addTraitToClass(
            $this->createFixtureFile($fixture),
            FooTrait::class
        );
        $this->assertFixture($fixture);
    }
}
