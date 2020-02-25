<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\CrmPlease\Coder\fixtures\InjectDependency;

use Tests\CrmPlease\Coder\fixtures\FooClass;

class AddNewProperty
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
