<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Rector\Caching\ChangedFilesDetector;
use Rector\Core\Bootstrap\ConfigShifter;
use Rector\Core\Bootstrap\RectorConfigsResolver;
use Rector\Core\Configuration\Configuration;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Rector\Core\Set\SetResolver;
use Rector\Set\SetProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\SetConfigResolver\ConfigResolver;
use Symplify\SetConfigResolver\SetAwareConfigResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use function array_merge;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * Based on https://github.com/rectorphp/rector/blob/v0.7.65/src/Bootstrap/RectorConfigsResolver.php
 * @see https://github.com/rectorphp/rector/blob/v0.7.65/src/Bootstrap/RectorConfigsResolver.php
 * @see RectorConfigsResolver
 */
class RectorContainerConfigurator
{
    private $config;
    private $setResolver;
    private $configResolver;
    private $setAwareConfigResolver;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setResolver = new SetResolver();
        $this->configResolver = new ConfigResolver();
        $setProvider = new SetProvider();
        $this->setAwareConfigResolver = new SetAwareConfigResolver($setProvider);
    }

    /**
     * @noRector
     */
    protected function getFirstResolvedConfig(): ?SmartFileInfo
    {
        return $this->configResolver->getFirstResolvedConfigFileInfo();
    }

    /**
     * @param SmartFileInfo[] $configFileInfos
     * @return SmartFileInfo[]
     */
    protected function resolveSetFileInfosFromConfigFileInfos(array $configFileInfos): array
    {
        return $this->setAwareConfigResolver->resolveFromParameterSetsFromConfigFiles($configFileInfos);
    }

    /**
     * @return SmartFileInfo[]
     * @noRector
     */
    protected function provide(): array
    {
        $configFileInfos = [];

        // Detect configuration from --set
        $argvInput = new ArgvInput();

        $set = $this->setResolver->resolveSetFromInput($argvInput);
        if ($set !== null) {
            $configFileInfos[] = $set->getSetFileInfo();
        }

        // And from --config or default one
        $inputOrFallbackConfigFileInfo = $this->configResolver->resolveFromInputWithFallback(
            $argvInput,
            ['rector.yaml'],
        );

        if ($inputOrFallbackConfigFileInfo !== null) {
            $configFileInfos[] = $inputOrFallbackConfigFileInfo;
        }

        $setFileInfos = $this->resolveSetFileInfosFromConfigFileInfos($configFileInfos);

        return array_merge($configFileInfos, $setFileInfos);
    }

    /**
     * Based on https://github.com/rectorphp/rector/blob/v0.7.65/bin/rector, between try...catch
     * @see https://github.com/rectorphp/rector/blob/v0.7.65/bin/rector
     *
     * @return ContainerInterface
     * @throws FileNotFoundException
     */
    public function configureContainer(): ContainerInterface
    {
        $configFileInfos = $this->provide();
        $configFileInfos[] = new SmartFileInfo(__DIR__ . '/../rector.yaml');

        // Build DI container
        $rectorContainerFactory = new RectorContainerFactory();

        // shift configs as last so parameters with main config have higher priority
        $configShifter = new ConfigShifter();
        $firstResolvedConfig = $this->getFirstResolvedConfig();
        if ($firstResolvedConfig !== null) {
            $configFileInfos = $configShifter->shiftInputConfigAsLast($configFileInfos, $firstResolvedConfig);
        }
        if ($this->config->getRectorConfigPath()) {
            $configFileInfos[] = new SmartFileInfo($this->config->getRectorConfigPath());
        }

        /** @var ContainerInterface $container */
        $container = $rectorContainerFactory->createFromConfigs($configFileInfos);

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        $configuration->setFirstResolverConfigFileInfo($this->getFirstResolvedConfig());

        if ($this->getFirstResolvedConfig()) {
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setFirstResolvedConfigFileInfo($this->getFirstResolvedConfig());
        }

        return $container;
    }
}
