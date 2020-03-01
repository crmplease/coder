<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\InjectDependency;

use Tests\Crmplease\Coder\fixtures\FooClass;

class PropertyExists
{
    protected $existsProperty;
    protected $newProperty;

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     * @param \Tests\Crmplease\Coder\fixtures\BarClass|null $newParameter some description
     */
    public function __construct(FooClass $existsParameter, ?\Tests\Crmplease\Coder\fixtures\BarClass $newParameter = null)
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
