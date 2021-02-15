<?php
declare(strict_types=1);

namespace Tests\Crmplease\Coder\fixtures\AddPhpdocMethodToClass;

/**
 * @author Mougrim <rinat@mougrim.ru>
 * @method int existsMethod1()
 * @method string|null existsMethod2() description method 2
 * @method static \Tests\Crmplease\Coder\fixtures\FooClass newMethod3(bool $parameter0 = true, bool $parameter1 = false, int $parameter2 = 0, string $parameter3 = '', ?\Tests\Crmplease\Coder\fixtures\BarClass $parameter4 = null, string $parameter5 = \Tests\Crmplease\Coder\fixtures\FooClass::TEST, string $parameter6 = \Tests\Crmplease\Coder\fixtures\FOO_TEST, float $parameter7 = 3.14, float $parameter8 = 3.14, array $parameter9 = [1, 3 => 2, 'key' => 'value']) description method 3
 */
class SeveralMethods
{
}
