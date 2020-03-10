<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Code;
use Crmplease\Coder\Constant;
use Crmplease\Coder\PhpdocMethod;
use Crmplease\Coder\PhpdocMethodParameter;
use Tests\Crmplease\Coder\fixtures\BarClass;
use Tests\Crmplease\Coder\fixtures\FooClass;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddPhpdocMethodToClassTest extends FunctionalTestCase
{
    public function testNewProperty(): void
    {
        $fixture = 'NewMethod';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'newMethod',
                'string|null',
                false,
                [],
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testUpdateMethod(): void
    {
        $fixture = 'UpdateMethod';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'existsMethod',
                'string|null',
                true,
                [
                    new PhpdocMethodParameter('parameter1', 'int', true, 0),
                    new PhpdocMethodParameter('parameter2', 'string', true, ''),
                ],
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testExistsMethod(): void
    {
        $fixture = 'ExistsMethod';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'existsMethod',
                'int',
                false,
                []
            )
        );
        $this->assertFixture($fixture);
    }

    public function testObjectReturnValue(): void
    {
        $fixture = 'ObjectReturnValue';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'newMethod',
                '\\'. FooClass::class . '|null',
                false,
                [],
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testEmptyReturnType(): void
    {
        $fixture = 'EmptyReturnType';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'newMethod',
                '',
                false,
                [],
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testWithoutPhpdoc(): void
    {
        $fixture = 'WithoutPhpdoc';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodToClass(
            $this->createFixtureFile($fixture),
            new PhpdocMethod(
                'newMethod',
                'string|null',
                false,
                [],
                'description'
            )
        );
        $this->assertFixture($fixture);
    }

    public function testSeveralMethods(): void
    {
        $fixture = 'SeveralMethods';
        $coder = $this->getCoder();
        $coder->addPhpdocMethodsToClass(
            $this->createFixtureFile($fixture),
            [
                new PhpdocMethod(
                    'newMethod3',
                    '\\' . FooClass::class,
                    true,
                    [
                        new PhpdocMethodParameter('parameter1', 'int', true, 0),
                        new PhpdocMethodParameter('parameter2', 'string', true, ''),
                        new PhpdocMethodParameter('parameter3', '?\\' . BarClass::class, true, null),
                        new PhpdocMethodParameter('parameter4', 'string', true, new Constant('\\' . FooClass::class . '::TEST')),
                        new PhpdocMethodParameter('parameter5', 'float', true, new Code('3.14')),
                    ],
                    'description method 3'
                ),
                new PhpdocMethod(
                    'existsMethod1',
                    'int'
                ),
                new PhpdocMethod(
                    'existsMethod2',
                    'string|null',
                    false,
                    [],
                    'description method 2'
                ),
            ]
        );
        $this->assertFixture($fixture);
    }
}
