<?php
declare(strict_types=1);

namespace CrmPlease\Coder;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class Code
{
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
