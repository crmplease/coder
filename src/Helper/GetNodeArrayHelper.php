<?php
declare(strict_types=1);

namespace CrmPlease\Coder\Helper;

use CrmPlease\Coder\Rector\RectorException;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use function get_class;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
class GetNodeArrayHelper
{
    /**
     * @param Return_ $node
     *
     * @return Array_
     * @throws RectorException
     */
    public function getFromReturnStatement(Return_ $node): Array_
    {
        $nodeArray = $node->expr;
        if (!$nodeArray) {
            throw new RectorException('Return statement is without value');
        }

        if (!$nodeArray instanceof Array_) {
            throw new RectorException("Return value isn't array, node class: " . get_class($nodeArray));
        }
        return $nodeArray;
    }

    /**
     * @param PropertyProperty $node
     *
     * @return Array_
     * @throws RectorException
     */
    public function getFromPropertyPropertyStatement(PropertyProperty $node): Array_
    {
        $defaultNode = $node->default;

        if (!$defaultNode) {
            throw new RectorException("Property doesn't have default value");
        }


        if (!$defaultNode instanceof Array_) {
            throw new RectorException("Property isn't array, node class: " . get_class($defaultNode));
        }
        return $defaultNode;
    }
}
