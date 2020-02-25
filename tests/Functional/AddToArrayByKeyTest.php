<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use CrmPlease\Coder\Constant;
use Tests\CrmPlease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayByKeyTest extends FunctionalTestCase
{
    public function testInt(): void
    {
        $fixture = 'int';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            2,
            'int2'
        );
        $this->assertFixture($fixture);
    }

    public function testIntReplace(): void
    {
        $fixture = 'intReplace';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            0,
            'int0 replaced'
        );
        $this->assertFixture($fixture);
    }

    public function testIntDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            0,
            'int0'
        );
        $this->assertFixture($fixture);
    }

    public function testString(): void
    {
        $fixture = 'string';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            'key2',
            'value2'
        );
        $this->assertFixture($fixture);
    }

    public function testStringReplace(): void
    {
        $fixture = 'stringReplace';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            'key0',
            'value0 replaced'
        );
        $this->assertFixture($fixture);
    }

    public function testStringDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            'key0',
            'value0'
        );
        $this->assertFixture($fixture);
    }

    public function testClassConstant(): void
    {
        $fixture = 'classConstant';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\BarClass::class'),
            'bar class'
        );
        $this->assertFixture($fixture);
    }

    public function testClassConstantReplace(): void
    {
        $fixture = 'classConstantReplace';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\FooClass::class'),
            'foo class replaced'
        );
        $this->assertFixture($fixture);
    }

    public function testClassConstantDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\FooClass::class'),
            'foo class'
        );
        $this->assertFixture($fixture);
    }

    public function testConstant(): void
    {
        $fixture = 'constant';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\BarClass::TEST'),
            'bar constant'
        );
        $this->assertFixture($fixture);
    }

    public function testConstantReplace(): void
    {
        $fixture = 'constantReplace';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\FooClass::TEST'),
            'foo constant replaced'
        );
        $this->assertFixture($fixture);
    }

    public function testConstantDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\CrmPlease\Coder\fixtures\FooClass::TEST'),
            'foo constant'
        );
        $this->assertFixture($fixture);
    }
}
