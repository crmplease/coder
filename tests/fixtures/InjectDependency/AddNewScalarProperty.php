<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\InjectDependency;

use Tests\Crmplease\Coder\fixtures\FooClass;

class AddNewScalarProperty
{
    protected $existsProperty;

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     */
    public function __construct(FooClass $existsParameter)
    {
        $this->existsProperty = $existsParameter;
    }
}
