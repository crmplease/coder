<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\CrmPlease\Coder\fixtures\InjectDependency;

use Tests\CrmPlease\Coder\fixtures\FooClass;

class AddNewProperty
{
    protected $existsProperty;
    protected $newProperty;

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     * @param \Tests\CrmPlease\Coder\fixtures\BarClass|null $newParameter some description
     */
    public function __construct(FooClass $existsParameter, ?\Tests\CrmPlease\Coder\fixtures\BarClass $newParameter = null)
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
