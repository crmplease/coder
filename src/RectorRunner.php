<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Crmplease\Coder\Rector\RectorException;
use PHPStan\AnalysedCodeException;
use PHPStan\Analyser\NodeScopeResolver;
use Rector\Core\Application\AppliedRectorCollector;
use Rector\Core\Application\ErrorAndDiffCollector;
use Rector\Core\Application\FileProcessor;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\Core\Application\FileSystem\RemovedAndAddedFilesProcessor;
use Rector\Core\Configuration\Option;
use Rector\Core\Console\Output\ConsoleOutputFormatter;
use Rector\Core\Console\Output\OutputFormatterCollector;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Extension\FinishingExtensionRunner;
use Rector\FileSystemRector\FileSystemFileProcessor;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Testing\Application\EnabledRectorsProvider;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
use function get_class;
use function implode;
use function sprintf;
use const PHP_EOL;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * Based on \Rector\Core\Application\RectorApplication::runOnFileInfos
 * @see \Rector\Core\Application\RectorApplication::runOnFileInfos
 */
class RectorRunner
{
    private $config;
    private $parameterProvider;
    private $symfonyStyle;
    private $fileSystemFileProcessor;
    private $errorAndDiffCollector;
    private $fileProcessor;
    private $enabledRectorsProvider;
    private $removedAndAddedFilesCollector;
    private $removedAndAddedFilesProcessor;
    private $nodeScopeResolver;
    private $finishingExtensionRunner;
    private $outputFormatterCollector;
    private $appliedRectorCollector;
    private $privatesAccessor;

    public function __construct(
        Config $config,
        ParameterProvider $parameterProvider,
        SymfonyStyle $symfonyStyle,
        FileSystemFileProcessor $fileSystemFileProcessor,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileProcessor $fileProcessor,
        EnabledRectorsProvider $enabledRectorsProvider,
        RemovedAndAddedFilesCollector $removedAndAddedFilesCollector,
        RemovedAndAddedFilesProcessor $removedAndAddedFilesProcessor,
        NodeScopeResolver $nodeScopeResolver,
        FinishingExtensionRunner $finishingExtensionRunner,
        OutputFormatterCollector $outputFormatterCollector,
        AppliedRectorCollector $appliedRectorCollector
    )
    {
        $this->config = $config;
        $this->parameterProvider = $parameterProvider;
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemFileProcessor = $fileSystemFileProcessor;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessor = $fileProcessor;
        $this->enabledRectorsProvider = $enabledRectorsProvider;
        $this->removedAndAddedFilesCollector = $removedAndAddedFilesCollector;
        $this->removedAndAddedFilesProcessor = $removedAndAddedFilesProcessor;
        $this->nodeScopeResolver = $nodeScopeResolver;
        $this->finishingExtensionRunner = $finishingExtensionRunner;
        $this->outputFormatterCollector = $outputFormatterCollector;
        $this->appliedRectorCollector = $appliedRectorCollector;
        $this->privatesAccessor = new PrivatesAccessor();
    }

    /**
     * @param string $file
     * @param AbstractRector $rector
     *
     * @throws ShouldNotHappenException
     * @throws FileNotFoundException
     * @throws RectorException
     */
    public function run(string $file, AbstractRector $rector): void
    {
        if ($this->config->doShowProgressBar()) {
            $this->symfonyStyle->text('Run rector '.get_class($rector)." on file {$file}");
        }
        $smartFileInfo = new SmartFileInfo($file);
        if ($this->config->doShowProgressBar()) {
            // why 3? one for each cycle, so user sees some activity all the time
            $this->symfonyStyle->progressStart(3);
        }
        // PHPStan has to know about all files!
        $this->nodeScopeResolver->setAnalysedFiles([$smartFileInfo->getRealPath()]);
        // 1. parse files to nodes
        $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
            $this->fileProcessor->parseFileInfoToLocalCache($smartFileInfo);
        });

        $this->enabledRectorsProvider->reset();
        $this->enabledRectorsProvider->addEnabledRector(get_class($rector));

        // 2. change nodes with Rectors
        $previousAutoImport = $this->parameterProvider->provideParameter(Option::AUTO_IMPORT_NAMES);
        $shouldAutoImport = $this->shouldAutoImport($smartFileInfo);
        if ($shouldAutoImport !== null) {
            $this->parameterProvider->changeParameter(Option::AUTO_IMPORT_NAMES, $shouldAutoImport);
        }
        try {
            $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
                $this->fileProcessor->refactor($smartFileInfo);
            });
        } finally {
            if ($shouldAutoImport !== null) {
                $this->parameterProvider->changeParameter(Option::AUTO_IMPORT_NAMES, $previousAutoImport);
            }
        }

        // 3. print to file or string
        $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
            $this->processFileInfo($smartFileInfo);
        });

        if ($this->config->doShowProgressBar()) {
            $this->symfonyStyle->newLine(2);
        }

        // 4. remove and add files
        $this->removedAndAddedFilesProcessor->run();

        // 5. extensions on finish
        $this->finishingExtensionRunner->run();

        if ($this->config->doShowProgressBar()) {
            $outputFormatter = $this->outputFormatterCollector->getByName(ConsoleOutputFormatter::NAME);
            $outputFormatter->report($this->errorAndDiffCollector);
        }

        $errors = $this->errorAndDiffCollector->getErrors();

        // workaround clear errorAndDiffCollector
        $this->privatesAccessor->setPrivateProperty($this->errorAndDiffCollector, 'errors', []);
        $this->privatesAccessor->setPrivateProperty($this->errorAndDiffCollector, 'fileDiffs', []);
        $this->privatesAccessor->setPrivateProperty($this->appliedRectorCollector, 'rectorClassesByFile', []);

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

    protected function processFileInfo(SmartFileInfo $fileInfo): void
    {
        if ($this->removedAndAddedFilesCollector->isFileRemoved($fileInfo)) {
            // skip, because this file exists no more
            return;
        }

        $oldContent = $fileInfo->getContents();

        $newContent = $this->fileProcessor->printToFile($fileInfo);

        $this->errorAndDiffCollector->addFileDiff($fileInfo, $newContent, $oldContent);

        $this->fileSystemFileProcessor->processFileInfo($fileInfo);
    }

    private function tryCatchWrapper(SmartFileInfo $smartFileInfo, callable $callback): void
    {
        if ($this->config->doShowProgressBar()) {
            $this->symfonyStyle->progressAdvance();
        }

        try {
            $callback($smartFileInfo);
        } catch (AnalysedCodeException $analysedCodeException) {
            $this->errorAndDiffCollector->addAutoloadError($analysedCodeException, $smartFileInfo);
        } catch (Throwable $throwable) {
            $this->errorAndDiffCollector->addThrowableWithFileInfo($throwable, $smartFileInfo);
        }
    }
}
