<?php
declare(strict_types=1);

namespace Crmplease\Coder\Helper;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\PhpParser\NodeTraverser\CallableNodeTraverser;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class ReturnStatementHelper
{
    private $callableNodeTraverser;

    public function __construct(CallableNodeTraverser $callableNodeTraverser)
    {
        $this->callableNodeTraverser = $callableNodeTraverser;
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
        $this->callableNodeTraverser->traverseNodesWithCallable($classMethod, static function (Node $node) use(&$returnNode, &$i): void {
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
