<?php
declare(strict_types=1);

namespace Crmplease\Coder;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class PhpdocMethodParameter
{
    private $name;
    private $type;
    private $hasDefault;
    private $default;

    /**
     * @param string $name
     * @param string $type
     * @param bool $hasDefault
     * @param string|float|int|array|Constant|Code|null $default
     */
    public function __construct(string $name, string $type = '', bool $hasDefault = false, $default = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->hasDefault = $hasDefault;
        $this->default = $default;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * @return array|Code|Constant|float|int|string|null
     */
    public function getDefault()
    {
        return $this->default;
    }
}
