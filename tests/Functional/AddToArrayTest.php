<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder\Functional;

use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class AddToArrayTest extends FunctionalTestCase
{
    public function testAddNewValues(): void
    {
        $fixture = 'addNewValues';
        $coder = $this->getCoder();
        $coder->addToFileReturnArray(
            $this->createFixtureFile($fixture),
            [],
            [
                'value4',
                'key5' => 'value5',
                'value6',
            ]
        );
        $this->assertFixture($fixture);
    }

    public function testPartOfValuesNew(): void
    {
        $fixture = 'partOfValuesNew';
        $coder = $this->getCoder();
        $coder->addToFileReturnArray(
            $this->createFixtureFile($fixture),
            [],
            [
                'value1',
                'key2' => 'value2',
                'value4',
                'key5' => 'value5',
            ]
        );
        $this->assertFixture($fixture);
    }

    public function testDuplicates(): void
    {
        $fixture = 'duplicates';
        $coder = $this->getCoder();
        $coder->addToFileReturnArray(
            $this->createFixtureFile($fixture),
            [],
            [
                'value1',
                'value3',
                'key2' => 'value2',
            ]
        );
        $this->assertFixture($fixture);
    }
}
