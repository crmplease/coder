<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Rector\AddPropertyToClassRector;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPropertyToClassTest extends FunctionalTestCase
{
    public function testNewProperty(): void
    {
        $fixture = 'NewProperty';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            'new property default value',
            'string',
            'new property description'
        );
        $this->assertFixture($fixture);
    }

    public function testUpdateProperty(): void
    {
        $fixture = 'UpdateProperty';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'existsProperty',
            true,
            AddPropertyToClassRector::VISIBILITY_PUBLIC,
            'new default value',
            'string',
            'new description'
        );
        $this->assertFixture($fixture);
    }

    public function testAddPhpdoc(): void
    {
        $fixture = 'AddPhpdoc';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'existsProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            0,
            'int',
            'description'
        );
        $this->assertFixture($fixture);
    }

    public function testExistsProperty(): void
    {
        $fixture = 'ExistsProperty';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'existsProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            0,
            'int',
            'description'
        );
        $this->assertFixture($fixture);
    }

    public function testRemovePhpdoc(): void
    {
        $fixture = 'RemovePhpdoc';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'existsProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED
        );
        $this->assertFixture($fixture);
    }

    public function testAutoTypeBool(): void
    {
        $fixture = 'AutoTypeBool';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            false
        );
        $this->assertFixture($fixture);
    }

    public function testAutoTypeInt(): void
    {
        $fixture = 'AutoTypeInt';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            0
        );
        $this->assertFixture($fixture);
    }

    public function testAutoTypeFloat(): void
    {
        $fixture = 'AutoTypeFloat';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            0.0
        );
        $this->assertFixture($fixture);
    }

    public function testAutoTypeString(): void
    {
        $fixture = 'AutoTypeString';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            ''
        );
        $this->assertFixture($fixture);
    }

    public function testAutoTypeArray(): void
    {
        $fixture = 'AutoTypeArray';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            []
        );
        $this->assertFixture($fixture);
    }

    public function testDescriptionWithEmptyType(): void
    {
        $fixture = 'DescriptionWithEmptyType';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED,
            null,
            '',
            'new description'
        );
        $this->assertFixture($fixture);
    }
}
