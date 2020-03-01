<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayByOrderTest extends FunctionalTestCase
{
    public function testNull(): void
    {
        $fixture = 'null';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            null
        );
        $this->assertFixture($fixture);
    }

    public function testNullDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            null
        );
        $this->assertFixture($fixture);
    }

    public function testFalse(): void
    {
        $fixture = 'false';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            false
        );
        $this->assertFixture($fixture);
    }

    public function testFalseDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            false
        );
        $this->assertFixture($fixture);
    }

    public function testTrue(): void
    {
        $fixture = 'true';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            true
        );
        $this->assertFixture($fixture);
    }

    public function testTrueDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            true
        );
        $this->assertFixture($fixture);
    }

    public function testInt(): void
    {
        $fixture = 'int';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            2
        );
        $this->assertFixture($fixture);
    }

    public function testIntDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            1
        );
        $this->assertFixture($fixture);
    }

    public function testFloat(): void
    {
        $fixture = 'float';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            1.0
        );
        $this->assertFixture($fixture);
    }

    public function testFloatDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            0.0
        );
        $this->assertFixture($fixture);
    }

    public function testString(): void
    {
        $fixture = 'string';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            'string'
        );
        $this->assertFixture($fixture);
    }

    public function testStringDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            'test'
        );
        $this->assertFixture($fixture);
    }

    public function testClassConstant(): void
    {
        $fixture = 'classConstant';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::class')
        );
        $this->assertFixture($fixture);
    }

    public function testClassConstantDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::class')
        );
        $this->assertFixture($fixture);
    }

    public function testConstant(): void
    {
        $fixture = 'constant';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::TEST')
        );
        $this->assertFixture($fixture);
    }

    public function testConstantDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::TEST')
        );
        $this->assertFixture($fixture);
    }

    public function testCode(): void
    {
        $fixture = 'code';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Code('Rule::unique(\'countries\')->ignore($country->getKey())')
        );
        $this->assertFixture($fixture);
    }

    public function testComplexValue(): void
    {
        $fixture = 'complexValue';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            [
                2,
                'string',
                new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::class'),
                new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::TEST'),
                new Code('Rule::unique(\'countries\')->ignore($country->getKey())'),
            ]
        );
        $this->assertFixture($fixture);
    }
}
