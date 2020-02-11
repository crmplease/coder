<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class CheckMethodHelper
{
    public function checkMethod(string $method, Node $node): bool
    {
        $methodNode = $node->getAttribute(AttributeKey::METHOD_NODE);
        if (!$methodNode instanceof ClassMethod) {
            return false;
        }
        if ($methodNode->name->name !== $method) {
            return false;
        }
        return  true;
    }
}
