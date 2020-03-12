<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Rector\AddMethodToClassRector;
use Tests\Crmplease\Coder\fixtures\FooClass;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddMethodToClassTest extends FunctionalTestCase
{
    public function testNewMethod(): void
    {
        $fixture = 'NewMethod';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'newMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            false,
            false,
            '?string',
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testNewAbstractMethod(): void
    {
        $fixture = 'NewAbstractMethod';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'newMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            true,
            false,
            '?string',
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testUpdateMethod(): void
    {
        $fixture = 'UpdateMethod';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'existsMethod',
            AddMethodToClassRector::VISIBILITY_PROTECTED,
            true,
            false,
            true,
            '?string',
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testMarkMethodAbstract(): void
    {
        $fixture = 'MarkMethodAbstract';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'existsMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            true,
            false,
            'int',
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testExistsMethod(): void
    {
        $fixture = 'ExistsMethod';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'existsMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            false,
            false,
            'int',
            'exists return description',
            'Exists description'
        );
        $this->assertFixture($fixture);
    }

    public function testRemoveDescriptionAndReturnType(): void
    {
        $fixture = 'RemoveDescriptionAndReturnType';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            '__construct',
            AddMethodToClassRector::VISIBILITY_PUBLIC
        );
        $this->assertFixture($fixture);
    }

    public function testVoidType(): void
    {
        $fixture = 'VoidType';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'newMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            false,
            false,
            '',
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testReturnObject(): void
    {
        $fixture = 'ReturnObject';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'newMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            false,
            false,
            '\\' . FooClass::class,
            'new return description',
            'New description'
        );
        $this->assertFixture($fixture);
    }

    public function testAddPhpDoc(): void
    {
        $fixture = 'AddPhpDoc';
        $coder = $this->getCoder();
        $coder->addMethodToClass(
            $this->createFixtureFile($fixture),
            'existsMethod',
            AddMethodToClassRector::VISIBILITY_PUBLIC,
            false,
            false,
            false,
            'int',
            'exists return description',
            'Exists description'
        );
        $this->assertFixture($fixture);
    }
}
