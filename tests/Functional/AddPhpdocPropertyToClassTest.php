<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\PhpdocProperty;
use Tests\Crmplease\Coder\fixtures\FooClass;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPhpdocPropertyToClassTest extends FunctionalTestCase
{
    public function testNewProperty(): void
    {
        $fixture = 'NewProperty';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertyToClass(
            $this->createFixtureFile($fixture),
            new PhpdocProperty(
                'newProperty',
                'string|null',
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testUpdateProperty(): void
    {
        $fixture = 'UpdateProperty';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertyToClass(
            $this->createFixtureFile($fixture),
            new PhpdocProperty(
                'existsProperty',
                'string|null',
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testNewObjectProperty(): void
    {
        $fixture = 'NewObjectProperty';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertyToClass(
            $this->createFixtureFile($fixture),
            new PhpdocProperty(
                'newProperty',
                '\\' . FooClass::class . '|null'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testNewEmptyTypeProperty(): void
    {
        $fixture = 'NewEmptyTypeProperty';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertyToClass(
            $this->createFixtureFile($fixture),
            new PhpdocProperty('newProperty')
        );
        $this->assertFixture($fixture);
    }

    public function testWithoutPhpdoc(): void
    {
        $fixture = 'WithoutPhpdoc';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertyToClass(
            $this->createFixtureFile($fixture),
            new PhpdocProperty(
                'newProperty',
                'string|null',
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testSeveralProperties(): void
    {
        $fixture = 'SeveralProperties';
        $coder = $this->getCoder();
        $coder->addPhpdocPropertiesToClass(
            $this->createFixtureFile($fixture),
            [
                new PhpdocProperty(
                    'replaceProperty',
                    'string|null',
                    'replace description'
                ),
                new PhpdocProperty(
                    'newProperty',
                    '\\' . FooClass::class,
                    'new description'
                ),
            ]
        );
        $this->assertFixture($fixture);
    }
}
