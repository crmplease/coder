<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ReturnStatementHelper
{
    private $simpleCallableNodeTraverser;

    public function __construct(SimpleCallableNodeTraverser $simpleCallableNodeTraverser)
    {
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
    }

    public function isReturnNodeForClosure(Return_ $node): bool
    {
        $parent = $node;
        while (($parent = $parent->getAttribute(AttributeKey::PARENT_NODE))) {
            if ($parent instanceof Closure) {
                return true;
            }
        }
        return false;
    }

    public function getLastReturnForClassMethod(ClassMethod $classMethod): ?Return_
    {
        $returnNode = null;
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($classMethod, static function (Node $node) use(&$returnNode, &$i): void {
            if (!$node instanceof Return_) {
                return;
            }
            $parent = $node;
            while (($parent = $parent->getAttribute(AttributeKey::PARENT_NODE))) {
                if ($parent instanceof Closure) {
                    return;
                }
                if ($parent instanceof FunctionLike) {
                    $returnNode = $node;
                    return;
                }
            }
        });

        return $returnNode;
    }
}
