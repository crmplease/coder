<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\CrmPlease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ArrayPathTest extends FunctionalTestCase
{
    public function testEmptyPath(): void
    {
        $fixture = 'emptyPath';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            [],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }

    public function testNewPath(): void
    {
        $fixture = 'newPath';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['newPath'],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }

    public function testExistsPath(): void
    {
        $fixture = 'existsPath';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['existsPath'],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }

    public function test2newPaths(): void
    {
        $fixture = '2newPaths';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['newPath', 'newSubPath'],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }

    public function test1exists1newSubPath(): void
    {
        $fixture = '1exists1newSubPath';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['existsPath', 'newSubPath'],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }

    public function test2existsPaths(): void
    {
        $fixture = '2existsPaths';
        $coder = $this->getCoder();
        $coder->addToFileReturnArrayByKey(
            $this->createFixtureFile($fixture),
            ['existsPath', 'existsSubPath'],
            'newKey',
            'newValue'
        );
        $this->assertFixture($fixture);
    }
}
