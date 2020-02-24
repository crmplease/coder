<?php
declare(strict_types=1);

namespace CrmPlease\Coder;

use CrmPlease\Coder\Rector\RectorException;
use PHPStan\AnalysedCodeException;
use PHPStan\Analyser\NodeScopeResolver;
use Rector\Application\AppliedRectorCollector;
use Rector\Application\ErrorAndDiffCollector;
use Rector\Application\FileProcessor;
use Rector\Application\FileSystem\RemovedAndAddedFilesCollector;
use Rector\Application\FileSystem\RemovedAndAddedFilesProcessor;
use Rector\Console\Output\ConsoleOutputFormatter;
use Rector\Console\Output\OutputFormatterCollector;
use Rector\Exception\ShouldNotHappenException;
use Rector\Extension\FinishingExtensionRunner;
use Rector\FileSystemRector\FileSystemFileProcessor;
use Rector\Rector\AbstractRector;
use Rector\Testing\Application\EnabledRectorsProvider;
use Symfony\Component\Console\Style\SymfonyStyle;
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
 * Based on \Rector\Application\RectorApplication::runOnFileInfos
 * @see \Rector\Application\RectorApplication::runOnFileInfos
 */
class RectorRunner
{
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
        $this->symfonyStyle->text('Run rector ' . get_class($rector) . " on file {$file}");
        $smartFileInfo = new SmartFileInfo($file);
        // why 3? one for each cycle, so user sees some activity all the time
        $this->symfonyStyle->progressStart(3);
        // PHPStan has to know about all files!
        /** @noinspection PhpUndefinedMethodInspection */
        $this->nodeScopeResolver->setAnalysedFiles([$smartFileInfo->getRealPath()]);
        // 1. parse files to nodes
        $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
            $this->fileProcessor->parseFileInfoToLocalCache($smartFileInfo);
        });

        $this->enabledRectorsProvider->reset();
        $this->enabledRectorsProvider->addEnabledRector(get_class($rector));

        // 2. change nodes with Rectors
        $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
            $this->fileProcessor->refactor($smartFileInfo);
        });

        // 3. print to file or string
        $this->tryCatchWrapper($smartFileInfo, function (SmartFileInfo $smartFileInfo): void {
            $this->processFileInfo($smartFileInfo);
        });

        $this->symfonyStyle->newLine(2);

        // 4. remove and add files
        $this->removedAndAddedFilesProcessor->run();

        // 5. extensions on finish
        $this->finishingExtensionRunner->run();

        $outputFormatter = $this->outputFormatterCollector->getByName(ConsoleOutputFormatter::NAME);
        $outputFormatter->report($this->errorAndDiffCollector);

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
        $this->symfonyStyle->progressAdvance();

        try {
            $callback($smartFileInfo);
        } catch (AnalysedCodeException $analysedCodeException) {
            $this->errorAndDiffCollector->addAutoloadError($analysedCodeException, $smartFileInfo);
        } catch (Throwable $throwable) {
            $this->errorAndDiffCollector->addThrowableWithFileInfo($throwable, $smartFileInfo);
        }
    }
}
