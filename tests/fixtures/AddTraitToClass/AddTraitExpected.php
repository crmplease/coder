<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\AddTraitToClass;

use Tests\Crmplease\Coder\fixtures\FooTrait;

class AddTrait
{
    use FooTrait;
    use \Tests\Crmplease\Coder\fixtures\BarTrait;
}
