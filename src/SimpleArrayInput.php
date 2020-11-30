<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use function in_array;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class SimpleArrayInput implements InputInterface
{
    private $arguments;
    private $options;
    private $additionalArguments;
    private $additionalOptions;
    private $interactive = true;

    public function __construct(
        array $arguments = [],
        array $options = [],
        array $additionalArguments = [],
        array $additionalOptions = []
    )
    {
        $this->arguments = $arguments;
        $this->options = $options;
        $this->additionalArguments = $additionalArguments;
        $this->additionalOptions = $additionalOptions;
    }
    /**
     * @inheritDoc
     */
    public function getFirstArgument()
    {
        return $this->arguments[0] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        $values = (array) $values;

        foreach ($this->arguments as $value) {
            if (in_array($value, $values, false)) {
                return true;
            }
        }

        foreach ($this->options as $key => $value) {
            if (in_array($key, $values, false)) {
                return true;
            }
        }

        if ($onlyParams) {
            return false;
        }

        foreach ($this->additionalArguments as $value) {
            if (in_array($value, $values, false)) {
                return true;
            }
        }

        foreach ($this->additionalOptions as $key => $value) {
            if (in_array($key, $values, false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getParameterOption($values, $default = false, bool $onlyParams = false)
    {
        $values = (array) $values;

        foreach ($this->arguments as $value) {
            if (in_array($value, $values, false)) {
                return true;
            }
        }

        foreach ($this->options as $key => $value) {
            if (in_array($key, $values, false)) {
                return $value;
            }
        }

        if ($onlyParams) {
            return $default;
        }

        foreach ($this->additionalArguments as $value) {
            if (in_array($value, $values, false)) {
                return true;
            }
        }

        foreach ($this->additionalOptions as $key => $value) {
            if (in_array($key, $values, false)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function bind(InputDefinition $definition)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function getArgument(string $name)
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setArgument(string $name, $value)
    {
        $this->arguments[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    public function hasArgument($name)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setOption(string $name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    public function hasOption(string $name)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isInteractive()
    {
        return $this->interactive;
    }

    /**
     * @inheritDoc
     */
    public function setInteractive(bool $interactive)
    {
        $this->interactive = $interactive;
    }
}
