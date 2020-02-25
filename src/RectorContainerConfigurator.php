<?php
declare(strict_types=1);

namespace CrmPlease\Coder;

use Rector\Core\Configuration\Configuration;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Rector\Core\Set\Set;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\SetConfigResolver\ConfigResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use function array_merge;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * Based on https://github.com/rectorphp/rector/blob/v0.6.14/bin/rector
 * @see https://github.com/rectorphp/rector/blob/v0.6.14/bin/rector
 * @see \RectorConfigsResolver
 */
class RectorContainerConfigurator
{
    /**
     * @var ConfigResolver
     */
    private $configResolver;

    public function __construct()
    {
        $this->configResolver = new ConfigResolver();
    }

    /**
     * @return string[]
     * @throws FileNotFoundException
     */
    protected function provide(): array
    {
        $configs = [];

        // Detect configuration from --set
        $input = new ArgvInput();

        $setConfig = $this->configResolver->resolveSetFromInputAndDirectory($input, Set::SET_DIRECTORY);
        if ($setConfig !== null) {
            $configs[] = $setConfig;
        }

        // And from --config or default one
        $inputOrFallbackConfig = $this->configResolver->resolveFromInputWithFallback(
            $input,
            ['rector.yaml']
        );
        if ($inputOrFallbackConfig !== null) {
            $configs[] = $inputOrFallbackConfig;
        }

        // resolve: parameters > sets
        $parameterSetsConfigs = $this->configResolver->resolveFromParameterSetsFromConfigFiles(
            $configs,
            Set::SET_DIRECTORY
        );
        if ($parameterSetsConfigs !== []) {
            $configs = array_merge($configs, $parameterSetsConfigs);
        }

        return $configs;
    }

    /**
     * @return string|null
     */
    protected function getFirstResolvedConfig(): ?string
    {
        return $this->configResolver->getFirstResolvedConfig();
    }

    /**
     * @return ContainerInterface
     * @throws FileNotFoundException
     */
    public function configureContainer(): ContainerInterface
    {
        $configs = $this->provide();
        $configs[] = __DIR__ . '/../rector.yaml';

        // Build DI container
        $rectorContainerFactory = new RectorContainerFactory();
        /** @var ContainerInterface $container */
        $container = $rectorContainerFactory->createFromConfigs($configs);

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        $configuration->setFirstResolverConfig($this->getFirstResolvedConfig());
        return $container;
    }
}
