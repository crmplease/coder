<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddMethodToClass;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
abstract class MarkMethodAbstract
{
    /**
     * New description
     *
     * @return int new return description
     */
    public abstract function existsMethod(): int;
}
