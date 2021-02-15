<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\InjectDependency;

use Tests\Crmplease\Coder\fixtures\FooClass;

class ReplacePropertyAddType
{
    protected $existsProperty;
    protected $newProperty;

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     * @param mixed $newParameter some description
     */
    public function __construct(FooClass $existsParameter, $newParameter)
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
