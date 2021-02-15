<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Constant;
use Tests\Crmplease\Coder\FunctionalTestCase;

require_once __DIR__ . '/../fixtures/constants.php';

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

    public function testConstant(): void
    {
        $fixture = 'constant';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\BAR_TEST'),
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
            new Constant('\Tests\Crmplease\Coder\fixtures\FOO_TEST'),
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
            new Constant('\Tests\Crmplease\Coder\fixtures\FOO_TEST'),
            'foo constant'
        );
        $this->assertFixture($fixture);
    }

    public function testClass(): void
    {
        $fixture = 'class';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::class'),
            'bar class'
        );
        $this->assertFixture($fixture);
    }

    public function testClassReplace(): void
    {
        $fixture = 'classReplace';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::class'),
            'foo class replaced'
        );
        $this->assertFixture($fixture);
    }

    public function testClassDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::class'),
            'foo class'
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
            new Constant('\Tests\Crmplease\Coder\fixtures\BarClass::TEST'),
            'bar class constant'
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
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::TEST'),
            'foo class constant replaced'
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
            new Constant('\Tests\Crmplease\Coder\fixtures\FooClass::TEST'),
            'foo class constant'
        );
        $this->assertFixture($fixture);
    }
}
