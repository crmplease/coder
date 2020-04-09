<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\Functional;

use Crmplease\Coder\Coder;
use Crmplease\Coder\Config;
use Crmplease\Coder\Constant;
use Tests\Crmplease\Coder\fixtures\BazClass;
use Tests\Crmplease\Coder\FunctionalTestCase;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ConfigTest extends FunctionalTestCase
{
    public function testAutoImportDisabled(): void
    {
        $fixture = 'autoImportDisabled';
        $config = (new Config())
            ->setAutoImport(false);
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportEnabled(): void
    {
        $fixture = 'autoImportEnabled';
        $config = (new Config())
            ->setAutoImport(true);
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportDisabledByArray(): void
    {
        $fixture = 'autoImportDisabled';
        $config = (new Config())
            ->setAutoImport(
                [
                    $this->getResultFixturePath($fixture) => false,
                ]
            );
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportEnabledByArray(): void
    {
        $fixture = 'autoImportEnabled';
        $config = (new Config())
            ->setAutoImport(
                [
                    $this->getResultFixturePath($fixture) => true,
                ]
            );
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportDisabledByCallback(): void
    {
        $fixture = 'autoImportDisabled';
        $config = (new Config())
            ->setAutoImport(
                function (string $file) use ($fixture): bool {
                    return $file !== $this->getResultFixturePath($fixture);
                }
            );
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportEnabledByCallback(): void
    {
        $fixture = 'autoImportEnabled';
        $config = (new Config())
            ->setAutoImport(
                function (string $file) use ($fixture): bool {
                    return $file === $this->getResultFixturePath($fixture);
                }
            );
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function testAutoImportDisabledByCallableArray(): void
    {
        $fixture = 'autoImportDisabled';
        $config = (new Config())
            ->setAutoImport([$this, 'autoImportDisabled']);
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function autoImportDisabled(): bool
    {
        return false;
    }

    public function testAutoImportEnabledByCallableArray(): void
    {
        $fixture = 'autoImportEnabled';
        $config = (new Config())
            ->setAutoImport([$this, 'autoImportEnabled']);
        $coder = Coder::create($config)
            ->setShowProgressBar(false);
        $coder->addToFileReturnArrayByOrder(
            $this->createFixtureFile($fixture),
            [],
            new Constant('\\' . BazClass::class . '::class'),
        );
        $this->assertFixture($fixture);
    }

    public function autoImportEnabled(): bool
    {
        return true;
    }
}
