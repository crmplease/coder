<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddMethodToClass;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ExistsMethod
{
    /**
     * Exists description
     *
     * @return int exists return description
     */
    public function existsMethod(): int
    {
        return 0;
    }
}
