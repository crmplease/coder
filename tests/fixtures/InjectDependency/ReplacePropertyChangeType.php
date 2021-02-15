<?php
/**
 * @author Mougrim <rinat@mougrim.ru>
 */
namespace Tests\Crmplease\Coder\fixtures\InjectDependency;

use Tests\Crmplease\Coder\fixtures\FooClass;

class ReplacePropertyChangeType
{
    protected $existsProperty;
    protected $newProperty = 'default value';

    /**
     * Description
     *
     * @param FooClass $existsParameter exists parameter description
     * @param string $newParameter some description
     */
    public function __construct(FooClass $existsParameter, string $newParameter = 'default value')
    {
        $this->existsProperty = $existsParameter;
        $this->newProperty = $newParameter;
    }
}
