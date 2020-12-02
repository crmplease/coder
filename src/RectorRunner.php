<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Crmplease\Coder\Rector\RectorException;
use Rector\ChangesReporting\Application\ErrorAndDiffCollector;
use Rector\ChangesReporting\Collector\AffectedFilesCollector;
use Rector\ChangesReporting\Output\ConsoleOutputFormatter;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\Core\Application\RectorApplication;
use Rector\Core\Application\TokensByFilePathStorage;
use Rector\Core\Configuration\Configuration;
use Rector\Core\Configuration\Option;
use Rector\Core\Console\Output\OutputFormatterCollector;
use Rector\Core\PhpParser\Parser\Parser;
use Rector\Core\Rector\AbstractRector;
use Rector\Testing\Application\EnabledRectorsProvider;
use Rector\NodeTypeResolver\FileSystem\CurrentFileInfoProvider;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use function get_class;
use function implode;
use function sprintf;
use const PHP_EOL;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * Based on \Rector\Core\Console\Command\ProcessCommand::execute
 * @see \Rector\Core\Console\Command\ProcessCommand::execute
 */
class RectorRunner
{
    private $configuration;
    private $application;
    private $config;
    private $parameterProvider;
    private $errorAndDiffCollector;
    private $enabledRectorsProvider;
    private $affectedFilesCollector;
    private $removedAndAddedFilesCollector;
    private $currentFileInfoProvider;
    private $tokensByFilePathStorage;
    private $outputFormatterCollector;
    private $parser;
    private $privatesAccessor;

    public function __construct(
        Configuration $configuration,
        RectorApplication $application,
        Config $config,
        ParameterProvider $parameterProvider,
        ErrorAndDiffCollector $errorAndDiffCollector,
        EnabledRectorsProvider $enabledRectorsProvider,
        AffectedFilesCollector $affectedFilesCollector,
        RemovedAndAddedFilesCollector $removedAndAddedFilesCollector,
        CurrentFileInfoProvider $currentFileInfoProvider,
        TokensByFilePathStorage $tokensByFilePathStorage,
        OutputFormatterCollector $outputFormatterCollector,
        Parser $parser
    )
    {
        $this->configuration = $configuration;
        $this->application = $application;
        $this->config = $config;
        $this->parameterProvider = $parameterProvider;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->enabledRectorsProvider = $enabledRectorsProvider;
        $this->affectedFilesCollector = $affectedFilesCollector;
        $this->removedAndAddedFilesCollector = $removedAndAddedFilesCollector;
        $this->currentFileInfoProvider = $currentFileInfoProvider;
        $this->tokensByFilePathStorage = $tokensByFilePathStorage;
        $this->outputFormatterCollector = $outputFormatterCollector;
        $this->parser = $parser;
        $this->privatesAccessor = new PrivatesAccessor();
    }

    /**
     * @param string $file
     * @param AbstractRector $rector
     *
     * @throws FileNotFoundException
     * @throws RectorException
     */
    public function run(string $file, AbstractRector $rector): void
    {
        $smartFileInfo = new SmartFileInfo($file);

        $this->configuration
            ->resolveFromInput(new SimpleArrayInput(
                [],
                [
                    Option::OPTION_NO_PROGRESS_BAR => !$this->config->doShowProgressBar(),
                    Option::OPTION_ONLY => get_class($rector),
                ],
            ));

        $phpFileInfos = [$smartFileInfo];

        $this->enabledRectorsProvider->reset();
        $previousAutoImport = $this->parameterProvider->provideParameter(Option::AUTO_IMPORT_NAMES);
        $shouldAutoImport = $this->shouldAutoImport($smartFileInfo);
        if ($shouldAutoImport !== null) {
            $this->parameterProvider->changeParameter(Option::AUTO_IMPORT_NAMES, $shouldAutoImport);
        }
        try {
            $this->configuration->setFileInfos($phpFileInfos);
            $this->application->runOnFileInfos($phpFileInfos);
        } finally {
            if ($shouldAutoImport !== null) {
                $this->parameterProvider->changeParameter(Option::AUTO_IMPORT_NAMES, $previousAutoImport);
            }
        }

        if ($this->config->doShowProgressBar()) {
            $outputFormatter = $this->outputFormatterCollector->getByName(ConsoleOutputFormatter::NAME);
            $outputFormatter->report($this->errorAndDiffCollector);
        }

        $errors = $this->errorAndDiffCollector->getErrors();

        // workaround clear errorAndDiffCollector
        $this->privatesAccessor->setPrivateProperty($this->errorAndDiffCollector, 'errors', []);
        $this->privatesAccessor->setPrivateProperty($this->errorAndDiffCollector, 'fileDiffs', []);
        // workaround clear for fix formatting issues when refactor file second time
        $this->privatesAccessor->setPrivateProperty($this->tokensByFilePathStorage, 'tokensByFilePath', []);
        $this->privatesAccessor->setPrivateProperty($this->parser, 'nodesByFile', []);

        if ($errors) {
            $messages = [];
            foreach ($errors as $error) {
                $message = sprintf(
                    'Could not process "%s" file%s, due to: %s"%s".',
                    $error->getFileInfo()->getPathname(),
                    $error->getRectorClass() ? ' by "' . $error->getRectorClass() . '"' : '',
                    PHP_EOL,
                    $error->getMessage()
                );

                if ($error->getLine()) {
                    $message .= ' On line: ' . $error->getLine();
                }

                $messages[] = $message;
            }
            throw new RectorException("There are errors on run rector:\n" . implode("\n", $messages));
        }
    }

    protected function shouldAutoImport(SmartFileInfo $smartFileInfo): ?bool
    {
        $autoImport = $this->config->getAutoImport();
        if (!$autoImport) {
            return null;
        }
        $realFilePath = $smartFileInfo->getRealPath();
        if (!$realFilePath) {
            return null;
        }
        return $autoImport($realFilePath);
    }
}
