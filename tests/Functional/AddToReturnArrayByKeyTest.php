<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Constant;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToReturnArrayByKeyTest extends FunctionalTestCase
{
    public function testAddKey(): void
    {
        $fixture = 'SomeClass';
        $coder = $this->getCoder();
        $coder->addToReturnArrayByKey(
            $this->createFixtureFile($fixture),
            'getArray',
            ['path1'],
            'key2',
            'value2'
        );
        $this->assertFixture($fixture);
    }

    public function testAddSelfClassConstant(): void
    {
        $fixture = 'ReplaceSelfClassConstantClass';
        $coder = $this->getCoder();
        $coder->addToReturnArrayByKey(
            $this->createFixtureFile($fixture),
            'getArray',
            ['path1'],
            new Constant('\Tests\Crmplease\Coder\fixtures\AddToReturnArrayByKey\ReplaceSelfClassConstantClass::SELF_CONSTANT'),
            'self constant replaced',
        );
        $this->assertFixture($fixture);
    }
}
