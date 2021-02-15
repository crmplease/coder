<?php
declare(strict_types=1);

namespace Crmplease\Coder;

use function constant;
use function explode;
use function ltrim;
use function strpos;
use function strtolower;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class Constant
{
    private $constant;
    
    public function __construct(string $constant)
    {
        $this->constant = $constant;
    }

    public function getConstant(): string
    {
        return $this->constant;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (strpos($this->constant, '::') !== false) {
            [$class, $constant] = explode('::', $this->constant);
            if (strtolower($constant) === 'class') {
                return ltrim($class, '\\');
            }
        }
        return constant($this->constant);
    }
}
