<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Rector\Caching\Detector\ChangedFilesDetector;
use Rector\Core\Bootstrap\ConfigShifter;
use Rector\Core\Bootstrap\RectorConfigsResolver;
use Rector\Core\Configuration\Configuration;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Rector\Set\RectorSetProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\SetConfigResolver\ConfigResolver;
use Symplify\SetConfigResolver\SetAwareConfigResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use function array_merge;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * Based on https://github.com/rectorphp/rector/blob/0.9.28/src/Bootstrap/RectorConfigsResolver.php
 * @see https://github.com/rectorphp/rector/blob/0.9.28/src/Bootstrap/RectorConfigsResolver.php
 * @see RectorConfigsResolver
 */
class RectorContainerConfigurator
{
    private $config;
    private $configResolver;
    private $setAwareConfigResolver;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->configResolver = new ConfigResolver();
        $rectorSetProvider = new RectorSetProvider();
        $this->setAwareConfigResolver = new SetAwareConfigResolver($rectorSetProvider);
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
     */
    protected function provide(): array
    {
        $configFileInfos = [];

        // Detect configuration from --set
        $argvInput = new ArgvInput([]);

        // And from --config or default one
        $inputOrFallbackConfigFileInfo = $this->configResolver->resolveFromInputWithFallback(
            $argvInput,
            ['config/rector.php'],
        );

        if ($inputOrFallbackConfigFileInfo !== null) {
            $configFileInfos[] = $inputOrFallbackConfigFileInfo;
        }

        $setFileInfos = $this->resolveSetFileInfosFromConfigFileInfos($configFileInfos);

        return array_merge($configFileInfos, $setFileInfos);
    }

    /**
     * Based on https://github.com/rectorphp/rector/blob/0.9.28/bin/rector.php, between try...catch
     * @see https://github.com/rectorphp/rector/blob/0.9.28/bin/rector.php
     *
     * @return ContainerInterface
     * @throws FileNotFoundException
     */
    public function configureContainer(): ContainerInterface
    {
        $configFileInfos = $this->provide();
        $configFileInfos[] = new SmartFileInfo(__DIR__ . '/../config/rector.php');

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


        $firstResolvedConfig = $this->getFirstResolvedConfig();
        if ($firstResolvedConfig) {
            /** @var Configuration $configuration */
            $configuration = $container->get(Configuration::class);
            $configuration->setFirstResolverConfigFileInfo($firstResolvedConfig);

            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setFirstResolvedConfigFileInfo($firstResolvedConfig);
        }

        return $container;
    }
}
