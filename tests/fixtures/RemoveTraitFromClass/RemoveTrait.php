<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\RemoveTraitFromClass;

use Tests\Crmplease\Coder\fixtures\BarTrait;
use Tests\Crmplease\Coder\fixtures\FooTrait;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class RemoveTrait
{
    use FooTrait;
    use BarTrait;
}
