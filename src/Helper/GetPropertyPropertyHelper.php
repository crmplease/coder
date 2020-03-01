<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class GetPropertyPropertyHelper
{
    public function getPropertyProperty(Class_ $node, string $name): ?PropertyProperty
    {
        foreach ($node->getProperties() as $property) {
            foreach ($property->props as $propertyPropertyNode) {
                if ($propertyPropertyNode->name->name === $name) {
                    return $propertyPropertyNode;
                }
            }
        }

        return null;
    }
}
