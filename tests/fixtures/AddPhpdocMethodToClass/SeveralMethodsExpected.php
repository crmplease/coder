<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddPhpdocMethodToClass;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * @method int existsMethod1()
 * @method string|null existsMethod2() description method 2
 * @method static \Tests\Crmplease\Coder\fixtures\FooClass newMethod3(int $parameter1 = 0, string $parameter2 = '', ?\Tests\Crmplease\Coder\fixtures\BarClass $parameter3 = null, string $parameter4 = \Tests\Crmplease\Coder\fixtures\FooClass::TEST, float $parameter5 = 3.14) description method 3
 */
class SeveralMethods
{
}
