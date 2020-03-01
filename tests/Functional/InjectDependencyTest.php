<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Rector\AddPropertyToClassRector;
use Tests\Crmplease\Coder\fixtures\BarClass;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class InjectDependencyTest extends FunctionalTestCase
{
    public function testAddNewProperty(): void
    {
        $fixture = 'AddNewProperty';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED
        );
        $coder->addParameterToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            '?\\' . BarClass::class,
            true,
            null
        );
        $coder->addCodeToMethod(
            $fixtureFile,
            '__construct',
            '$this->newProperty = $newParameter;'
        );
        $coder->addPhpdocParamToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            '\\' . BarClass::class . '|null',
            'some description'
        );
        $this->assertFixture($fixture);
    }

    public function testPropertyExists(): void
    {
        $fixture = 'PropertyExists';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED
        );
        $coder->addParameterToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            '?\\' . BarClass::class,
            true,
            null
        );
        $coder->addCodeToMethod(
            $fixtureFile,
            '__construct',
            '$this->newProperty = $newParameter;'
        );
        $coder->addPhpdocParamToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            '\\' . BarClass::class . '|null',
            'some description'
        );
        $this->assertFixture($fixture);
    }

    public function testReplaceProperty(): void
    {
        $fixture = 'ReplaceProperty';
        $coder = $this->getCoder();
        $fixtureFile = $this->createFixtureFile($fixture);
        $coder->addPropertyToClass(
            $fixtureFile,
            'newProperty',
            false,
            AddPropertyToClassRector::VISIBILITY_PROTECTED
        );
        $coder->addParameterToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            'string',
            false
        );
        $coder->addCodeToMethod(
            $fixtureFile,
            '__construct',
            '$this->newProperty = $newParameter;'
        );
        $coder->addPhpdocParamToMethod(
            $fixtureFile,
            '__construct',
            'newParameter',
            'string',
            'some new description'
        );
        $this->assertFixture($fixture);
    }
}
