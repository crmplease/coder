<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\RemoveTraitFromClass;

use Tests\Crmplease\Coder\fixtures\FooTrait;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class RemoveNotExistsTrait
{
    use FooTrait;
}
