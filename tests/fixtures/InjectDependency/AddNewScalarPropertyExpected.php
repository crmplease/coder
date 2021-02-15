<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\InjectDependency;

use Tests\Crmplease\Coder\fixtures\FooClass;

class AddNewScalarProperty
{
    protected $existsProperty;
    protected $newProperty;

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     * @param string|null $newParameter some description
     */
    public function __construct(FooClass $existsParameter, ?string $newParameter = null)
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
