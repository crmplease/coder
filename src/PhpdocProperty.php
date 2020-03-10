<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use function ltrim;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class PhpdocProperty
{
    private $name;
    private $type;
    private $description;

    public function __construct(string $name, string $type = '', string $description = '')
    {
        $this->name = ltrim($name, '$');
        $this->type = $type;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
