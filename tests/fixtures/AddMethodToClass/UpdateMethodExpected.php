<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddMethodToClass;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class UpdateMethod
{
    /**
     * New description
     *
     * @return string|null new return description
     */
    protected static final function existsMethod(): ?string
    {
        return 0;
    }
}
