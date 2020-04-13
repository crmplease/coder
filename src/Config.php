<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use function is_array;
use function is_bool;
use function is_callable;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class Config
{
    protected $autoImport;

    public function getAutoImport(): ?callable
    {
        if (!$this->autoImport) {
            return null;
        }
        if (is_bool($this->autoImport)) {
            $autoImport = $this->autoImport;
            $this->autoImport = static function () use ($autoImport): bool {
                return $autoImport;
            };
        } elseif (is_array($this->autoImport) && !is_callable($this->autoImport)) {
            $autoImportMap = $this->autoImport;
            $this->autoImport = static function(string $file) use ($autoImportMap): ?bool {
                return $autoImportMap[$file] ?? null;
            };
        }
        return $this->autoImport;
    }

    /**
     * @param callable|array|bool|null $autoImport if null, the use default value from rector.yaml;
     *        if true, the auto import will be always enabled;
     *        if false, the auto import will be always disabled;
     *        if array, they file name is used as key, value can be true/false/null;
     *        if callable then it will receive file name as first argument and should return true/false/null.
     *
     * @return $this
     */
    public function setAutoImport($autoImport): self
    {
        $this->autoImport = $autoImport;
        return $this;
    }

    protected $showProgressBar = true;

    public function doShowProgressBar(): bool
    {
        return $this->showProgressBar;
    }

    public function setShowProgressBar(bool $showProgressBar): self
    {
        $this->showProgressBar = $showProgressBar;

        return $this;
    }

    protected $rectorConfigPath = '';

    public function getRectorConfigPath(): string
    {
        return $this->rectorConfigPath;
    }

    public function setRectorConfigPath(string $rectorConfigPath): self
    {
        $this->rectorConfigPath = $rectorConfigPath;

        return $this;
    }
}
