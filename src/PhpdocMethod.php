<?php
declare(strict_types=1);

namespace Crmplease\Coder;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class PhpdocMethod
{
    private $name;
    private $returnType;
    private $isStatic;
    private $parameters;
    private $description;

    /**
     * @param string $name
     * @param string $returnType
     * @param bool $isStatic
     * @param PhpdocMethodParameter[] $parameters
     * @param string $description
     */
    public function __construct(
        string $name,
        string $returnType = '',
        bool $isStatic = false,
        array $parameters = [],
        string $description = ''
    )
    {

        $this->name = $name;
        $this->returnType = $returnType;
        $this->isStatic = $isStatic;
        $this->parameters = $parameters;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    /**
     * @return PhpdocMethodParameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
