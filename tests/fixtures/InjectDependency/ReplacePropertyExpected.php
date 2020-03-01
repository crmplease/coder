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
     * @param string $newParameter some new description
     */
    public function __construct(FooClass $existsParameter, string $newParameter)
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
